<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_smart_date;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;
use Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface;
use Drupal\smart_date_recur\Entity\SmartDateOverride;
use Drupal\smart_date_recur\Entity\SmartDateRule;

/**
 * Schema.org Smart Date JSON-LD manager service.
 */
class SchemaDotOrgSmartDateJsonLdManager implements SchemaDotOrgSmartDateJsonLdManagerInterface {

  /**
   * Constructs a SchemadotorgSmartDateJsonLdManager object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\schemadotorg\SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager
   *   The Schema.org schema type manager.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected SchemaDotOrgSchemaTypeManagerInterface $schemaTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function alterProperties(array &$data, FieldItemListInterface $items, BubbleableMetadata $bubbleable_metadata): void {
    $field_definition = $items->getFieldDefinition();
    if ($field_definition->getType() !== 'smartdate') {
      return;
    }

    // Get Schema.org property from the Schema.org mapping.
    $mapping = SchemaDotOrgMapping::loadByEntity($items->getEntity());
    $schema_property = $mapping->getSchemaPropertyMapping($field_definition->getName());

    // Alter the JSON-LD data based on Schema.org property.
    switch ($schema_property) {
      case 'startDate':
        $this->alterStartDateProperties($data, $items);
        return;

      case 'eventSchedule';
        $this->alterEventScheduleProperties($data, $items);
        return;
    }
  }

  /**
   * Alter the Schema.org JSON-LD eventSchedule.
   *
   * @param array &$data
   *   The JSON-LD date.
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   The Smart Date field items.
   */
  protected function alterEventScheduleProperties(array &$data, FieldItemListInterface $items): void {
    // Get recurring rules and add simple smart dates to the event schedule.
    $rules = [];
    $event_schedules = [];
    foreach ($items as $item) {
      /** @var \Drupal\smart_date_recur\Entity\SmartDateRule|null $rule */
      $rule = (!empty($item->rrule)) ? $this->entityTypeManager
        ->getStorage('smart_date_rule')
        ->load($item->rrule) : NULL;
      if ($rule) {
        $rules[$rule->id()] = $rule;
      }
      else {
        $event_schedules[] = $this->getScheduleFromFieldItem($item);
      }
    }

    // If there are no rules, set the eventSchedule and exit.
    if (!$rules) {
      $data['eventSchedule'] = (count($event_schedules) === 1)
        ? reset($event_schedules)
        : $event_schedules;
      return;
    }

    // Loop through the rules collect recurring schedule with except dates
    // and rescheduled dates & times.
    foreach ($rules as $rule) {
      $override_ids = $this->entityTypeManager
        ->getStorage('smart_date_override')
        ->getQuery()
        ->accessCheck(FALSE)
        ->condition('rrule', $rule->id())
        ->execute();

      // If there are no overrides set the eventSchedule.
      if (!$override_ids) {
        $event_schedules[] = $this->getScheduleFromRule($rule);
        continue;
      }

      // Get all the recurring instances.
      $instance_dates = [];
      /** @var \Recurr\Recurrence[] $instances */
      $instances = $rule->makeRuleInstances()->toArray();
      foreach ($instances as $instance) {
        $instance_dates[$instance->getIndex()] = $instance->getStart()->format('c');
      }

      $except_dates = [];
      $rescheduled_schedules = [];

      /** @var \Drupal\smart_date_recur\Entity\SmartDateOverride[] $overrides */
      $overrides = SmartDateOverride::loadMultiple($override_ids);
      foreach ($overrides as $override) {
        // Add the overridden date to except dates.
        $except_dates[] = $instance_dates[$override->rrule_index->value];

        // Add the rescheduled date to the event schedule.
        if ($override->value->value) {
          $rescheduled_schedules[] = $this->getScheduleFromOverride($override);
        }
      }

      // Get Schedule from the recurring rule.
      $schedule = $this->getScheduleFromRule($rule);

      // Add except dates to the Schedule.
      if ($except_dates) {
        $schedule['exceptDate'] = $except_dates;
      }

      // Add Schedule to the event schedule.
      $event_schedules[] = $schedule;

      // Append reschedule schedules to the event schedules.
      if ($rescheduled_schedules) {
        $event_schedules = array_merge($event_schedules, $rescheduled_schedules);
      }
    }

    // Set the event schedule.
    $data['eventSchedule'] = (count($event_schedules) === 1)
      ? reset($event_schedules)
      : $event_schedules;
  }

  /**
   * Alter the Schema.org JSON-LD startDate.
   *
   * @param array &$data
   *   The JSON-LD date.
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   The Smart Date field items.
   */
  protected function alterStartDateProperties(array &$data, FieldItemListInterface $items): void {
    $field_definition = $items->getFieldDefinition();

    // Only alter the Schema.org type data that have a single daterange field.
    $cardinality = $field_definition->getFieldStorageDefinition()->getCardinality();
    if ($cardinality !== 1) {
      return;
    }

    // Get Schema.org mapping.
    $mapping = SchemaDotOrgMapping::loadByEntity($items->getEntity());

    // Check that the field is mapped to startDate.
    $schema_property = $mapping->getSchemaPropertyMapping($field_definition->getName());
    if ($schema_property !== 'startDate') {
      return;
    }

    // Get the Schema.org type.
    $schema_type = $mapping->getSchemaType();

    // Set the startDate property.
    $data['startDate'] = date('c', $items->value);

    // Set the endDate property.
    if ($this->schemaTypeManager->hasProperty($schema_type, 'endDate')
      && empty($data['endDate'])
      && !empty($items->end_value)) {
      $data['endDate'] = date('c', $items->end_value);
    }

    // Set the duration property.
    if ($this->schemaTypeManager->hasProperty($schema_type, 'duration')
      && empty($data['duration'])
      && !empty($items->end_value)) {
      $data['duration'] = $this->formatDuration((int) $items->duration);
    }
  }

  /**
   * Get Schema.org Schedule from Smart date field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The field item.
   *
   * @return array
   *   The Schema.org Schedule type.
   */
  protected function getScheduleFromFieldItem(FieldItemInterface $item): array {
    $value = [
      '@type' => 'Schedule',
      'startDate' => date('Y-m-d', (int) $item->value),
      'startTime' => date('H:i:00', (int) $item->value),
      'endTime' => date('H:i:00', (int) $item->end_value),
      'duration' => $this->formatDuration((int) $item->duration),
    ];

    // Add scheduleTimezone if timezone set.
    if ($item->timezone) {
      $value['scheduleTimezone'] = $item->timezone;
    }

    return $value;
  }

  /**
   * Get Schema.org Schedule from Smart date field item.
   *
   * @param \Drupal\smart_date_recur\Entity\SmartDateOverride $override
   *   The smart date override.
   *
   * @return array
   *   The Schema.org Schedule type.
   */
  protected function getScheduleFromOverride(SmartDateOverride $override): array {
    return [
      '@type' => 'Schedule',
      'startDate' => date('Y-m-d', (int) $override->value->value),
      'startTime' => date('H:i:00', (int) $override->value->value),
      'endTime' => date('H:i:00', (int) $override->end_value->value),
      'duration' => $this->formatDuration((int) $override->duration->value),
    ];
  }

  /* ************************************************************************ */
  // Schedule methods.
  /* ************************************************************************ */

  /**
   * Get Schema.org Schedule from Smart date rule.
   *
   * @param \Drupal\smart_date_recur\Entity\SmartDateRule|null $rule
   *   The Smart date rule.
   *
   * @return array
   *   The Schema.org Schedule type.
   */
  protected function getScheduleFromRule(?SmartDateRule $rule = NULL): array {
    if (!$rule) {
      return [];
    }

    // Get the rule's parent entity first field item.
    $field_name = $rule->field_name->value;
    $entity = $rule->getParentEntity();
    $item = $entity->{$field_name}->first();

    // Use the first field item to build the Schedule object.
    $value = $this->getScheduleFromFieldItem($item);

    // repeatFrequency using Duration format as linked above e.g. P1W or PT90M.
    // Realized after implementing that the duration key is meant to go on the parent.
    // Left the code in case Smart Date can be used to populate parent duration values.
    $value['repeatFrequency'] = $this->formatInterval($rule);

    // byDay using schema.org links if needed.
    $by_day = $this->formatByDay($rule);
    if ($by_day) {
      $value['byDay'] = $by_day;
    }

    // If present, process a limit string.
    $limit_string = $rule->limit->value;
    if ($limit_string) {
      $limit_parts = explode('=', $limit_string);
      if ($limit_parts[0] === 'COUNT') {
        $value['repeatCount'] = $limit_parts[1];
      }
      elseif ($limit_parts[0] === 'UNTIL') {
        $value['endDate'] = $limit_parts[1];
      }
    }

    return $value;
  }

  /* ************************************************************************ */
  // Smart date format methods.
  /* ************************************************************************ */

  /**
   * Format duration.
   *
   * @param int $duration
   *   Duration, in minutes.
   *
   * @return string
   *   The duration as a string.
   */
  protected function formatDuration(int $duration): string {
    if (empty($duration)) {
      // @todo Additional validation to assume zero is the correct value?
      return 'P0D';
    }

    // Start duration string.
    $duration_string = 'P';

    // Set days.
    $days = floor($duration / 1440);
    if ($days > 0) {
      $duration_string .= $days . 'D';
    }

    // Set time range within days.
    $time_within_day = $duration % 1440;
    if ($time_within_day > 0) {
      $duration_string .= 'T';

      $hours = floor($time_within_day / 60);
      if ($hours > 0) {
        $duration_string .= $hours . 'H';
      }

      $minutes = $time_within_day % 60;
      if ($minutes > 0) {
        $duration_string .= $minutes . 'H';
      }
    }

    return $duration_string;
  }

  /**
   * Format interval for recurring events.
   *
   * @param \Drupal\smart_date_recur\Entity\SmartDateRule $rule
   *   The Smart date rule entity.
   *
   * @return string
   *   The interval string for recurring events.
   *
   * @see https://en.wikipedia.org/wiki/ISO_8601#Repeating_intervals
   */
  protected function formatInterval(SmartDateRule $rule): string {
    $freq = $rule->freq->value ?? '';
    if (empty($freq)) {
      return '';
    }

    // Start interval string.
    $interval_string = 'P';

    // Set interval type.
    if (in_array($freq, ['MINUTELY', 'HOURLY'])) {
      $interval_string .= 'T';
    }

    // Set interval frequency.
    $parameters = $rule->getParametersArray();
    $interval_string .= $parameters['interval'] ?: 1;

    // Set interval type.
    $intervalAbbreviations = [
      'MINUTELY' => 'M',
      'HOURLY' => 'H',
      'DAILY' => 'D',
      'WEEKLY' => 'W',
      'MONTHLY' => 'M',
      'YEARLY' => 'Y',
    ];
    $interval_string .= $intervalAbbreviations[$freq] ?? '';

    return $interval_string;
  }

  /**
   * Format days for recurring events.
   *
   * @param \Drupal\smart_date_recur\Entity\SmartDateRule $rule
   *   The Smart date rule entity.
   *
   * @return array|null
   *   An array of Schema.org days for recurring events.
   */
  protected function formatByDay(SmartDateRule $rule): ?array {
    $params = $rule->getParametersArray();
    $byday = $params['byday'] ?? [];
    if (empty($byday)) {
      return NULL;
    }

    $byday = array_combine($byday, $byday);

    $schema_days = [
      'SU' => 'https://schema.org/Sunday',
      'MO' => 'https://schema.org/Monday',
      'TU' => 'https://schema.org/Tuesday',
      'WE' => 'https://schema.org/Wednesday',
      'TH' => 'https://schema.org/Thursday',
      'FR' => 'https://schema.org/Friday',
      'SA' => 'https://schema.org/Saturday',
    ];

    // Get https://schema.org/DayOfWeek as an indexed array of links
    // to Schema.org days.
    return array_values(array_intersect_key($schema_days, $byday));
  }

}

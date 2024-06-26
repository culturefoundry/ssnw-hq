<?php

namespace Drupal\mermaid_diagram_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Provides a field type of MermaidDiagram.
 *
 * @FieldType(
 *   id = "mermaid_diagram",
 *   label = @Translation("Mermaid diagram"),
 *   default_formatter = "mermaid_diagram_formatter",
 *   default_widget = "mermaid_diagram_widget",
 * )
 */
class MermaidDiagramItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        // List the values that the field will save.
        'title' => [
          'type' => 'varchar',
          'length' => '255',
          'not null' => FALSE,
        ],
        'diagram' => [
          'type' => 'text',
          'size' => 'medium',
          'not null' => FALSE,
        ],
        'caption' => [
          'type' => 'text',
          'size' => 'medium',
          'not null' => FALSE,
        ],
        'key' => [
          'type' => 'text',
          'size' => 'small',
          'not null' => FALSE,
        ],
        // Seems wrong to have to use int in place of boolean.
        'show_code' => [
          'type' => 'int',
          'size' => 'tiny',
          'not null' => FALSE,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];

    $properties['title'] = DataDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE);
    $properties['caption'] = DataDefinition::create('string')
      ->setLabel(t('Caption'))
      ->setRequired(TRUE);
    $properties['diagram'] = DataDefinition::create('string')
      ->setLabel(t('Mermaid code'))
      ->setRequired(TRUE);
    $properties['show_code'] = DataDefinition::create('string')
      ->setLabel(t('Expose the code'))
      ->setRequired(FALSE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $title = $this->get('title')->getValue();
    $caption = $this->get('caption')->getValue();
    $diagram = $this->get('diagram')->getValue();
    // Whether is has show_code or not should not determine emptiness.
    return (empty($title)) && (empty($caption)) && (empty($diagram));
  }

}

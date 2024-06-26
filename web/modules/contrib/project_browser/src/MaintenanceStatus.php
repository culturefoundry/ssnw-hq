<?php

declare(strict_types=1);

namespace Drupal\project_browser;

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * The maintenance statuses available to the project browser.
 */
enum MaintenanceStatus: string {

  case Maintained = 'maintained';
  case All = 'all';

  /**
   * Represents this enum as a set of options.
   *
   * @return array<string, \Drupal\Core\StringTranslation\TranslatableMarkup>
   *   The cases in this enum. The keys are the backing values, and the values
   *   are the translatable labels.
   */
  public static function asOptions(): array {
    $options = [];
    foreach (self::cases() as $case) {
      $options[$case->value] = $case->label();
    }
    return $options;
  }

  /**
   * Returns a translatable label for the current case.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   A translatable label.
   */
  public function label(): TranslatableMarkup {
    return match ($this) {
      self::Maintained => t('Maintained'),
      self::All => t('Show all'),
    };
  }

}

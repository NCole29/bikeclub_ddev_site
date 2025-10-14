<?php

declare(strict_types=1);

namespace Drupal\bikeclub\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hook implementations for themes.
 */
class BikeclubStyles {
  /**
   * Implements hook_page_attachments().
   */
  #[Hook('page_attachments')]
  public function attachStyles(array &$attachments) {
    $attachments['#attached']['library'][] = 'bikeclub/bikeclub_styles';
  }
}
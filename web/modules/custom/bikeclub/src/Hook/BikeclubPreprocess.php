<?php

declare(strict_types=1);

namespace Drupal\bikeclub\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Preprocess Hook implementations.
 */
class BikeclubPreprocess {
  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_field')]
  public function PreprocessFields(&$vars) {
    // Hide country name everywhere address is displayed.
    if ($vars['field_name'] == 'field_address') {
      $vars['items'][0]['content']['country']['#value'] = '';
    }    
  }
}
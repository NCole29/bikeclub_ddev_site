<?php

declare(strict_types=1);

namespace Drupal\bikeclub\Hook;

use Drupal\Core\Hook\Attribute\Hook;

class BikeclubTemplates {

  /** 
   * Implements hook_theme to register templates.
   */
  #[Hook('theme')]
  public function addTemplates() {
    return [
      'calendar_view_pager__calendar' => [
        'template' => 'calendar-view-pager--custom',
        'base hook' => 'calendar_view_pager__calendar',
      ],
      'field__node__field_registration_form' => [
        'template' => 'field--node--registration-button',
        'base hook' => 'field',
      ],
      'field__node__field_registration_free_form' => [
        'template' => 'field--node--registration-button',
        'base hook' => 'field',
      ],
      'field__node__webform__webform' => [
        'template' => 'field--node--registration-button',
        'base hook' => 'field',
      ],
      'node__location' => [
        'template' => 'node--location--custom',
        'base hook' => 'node',
      ],
      'node__card_row' => [
        'template' => 'node--card-row',
        'base hook' => 'node',
      ],
      'node__card_column' => [
        'template' => 'node--card-column',
        'base hook' => 'node',
      ],
      // Adding 'uri' to variables so its available in paragraph templates.
      'fontawesomeicons' => [
        'variables' => [
          'icons' => NULL,
          'layers' => FALSE,
        ],
      ],
      'fontawesomeicon' => [
        'variables' => [
          'tag' => 'i',
          'iconset' => '',
          'name' => NULL,
          'style' => NULL,
          'settings' => NULL,
          'transforms' => NULL,
          'mask' => NULL,
          'css' => NULL,
          'uri' => NULL,
        ],
      ],
    ];      
  }
}
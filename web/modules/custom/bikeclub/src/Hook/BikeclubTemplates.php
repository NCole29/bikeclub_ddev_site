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
      'node__card_row' => [
        'template' => 'node--card-row',
        'base hook' => 'node',
      ],
      'node__location' => [
        'template' => 'node--location',
        'base hook' => 'node',
      ],
      'node__summary' => [
        'template' => 'node--summary',
        'base hook' => 'node',
      ],
      'node__summary_2' => [
        'template' => 'node--summary-2',
        'base hook' => 'node',
      ],
     'paragraph__hero_banner' => [
        'template' => 'paragraph--hero-banner',
        'base hook' => 'paragraph',
      ],
      'field__address' => [
        'template' => 'field--address',
        'base hook' => 'field',
      ],  
      'address_plain__custom' => [
        'template' => 'address-plain',
        'base hook' => 'field',
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
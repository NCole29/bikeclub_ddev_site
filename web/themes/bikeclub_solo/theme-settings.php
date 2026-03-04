<?php

/**
 * @file
 * Add items to Appearance > Settings > Bikeclub Solo.
 *
 */
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\File;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function bikeclub_solo_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {

  require_once __DIR__ . '/includes/bikeclub_settings.inc';
}

/**
 * Modified from Solo helper function _generate_form_element() to shorten "settings" names.
 */
function _bikeclub_form_element($prefix, $label, $attribute_key, $attribute_label) {
  return [
    '#type' => 'textfield',
    '#maxlength' => 7,
    '#size' => 10,
    '#title' => t("(@attribute_label)", ['@attribute_label' => $attribute_label]),
    '#default_value' => theme_get_setting("{$prefix}_{$attribute_key}"),
    '#attributes' => [
      'pattern' => '^#[a-fA-F0-9]{6}',
    ],
    '#wrapper_attributes' => [
      'data-drupal-selector' => 'solo-color-picker',
    ],
  ];
}

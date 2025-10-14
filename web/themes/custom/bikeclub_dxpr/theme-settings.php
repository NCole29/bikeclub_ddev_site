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
function bikeclub_dxpr_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {

  require_once __DIR__ . '/includes/club_accounts.inc';
  require_once __DIR__ . '/includes/cancel_background.inc';

}

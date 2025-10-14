<?php

/**
 * @file
 * club_accounts.inc
 * Add Club accounts to the Appearance > theme > settings form
 * These data are available to the page.html.twig template
 */

/**
 * The theme settings.
 */

$form['bikeclub_dxpr_settings']['club_accounts'] = [
  '#type' => 'details',
  '#title' => t("<strong class='purple'>RWGPS and Strava links</strong> - Bikeclub DXPR Theme"),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
];
$form['bikeclub_dxpr_settings']['club_accounts']['rwgps_url'] = [
  '#type' => 'textfield',
  '#title' => t('RWGPS Club Account'),
  '#default_value' => theme_get_setting('rwgps_url'),
];
$form['bikeclub_dxpr_settings']['club_accounts']['strava_url'] = [
  '#type' => 'textfield',
  '#title' => t('Strava Club Account'),
  '#default_value' => theme_get_setting('strava_url'),
];

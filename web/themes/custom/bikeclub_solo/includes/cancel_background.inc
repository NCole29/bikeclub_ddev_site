<?php

/**
 * @file
 * cancel_background.inc
 * Add Cancel block background color option to the Appearance > theme > settings form
 * These data are available to the page.html.twig template
 */

/**
 * The theme settings.
 */

 $options = [
  '#ffb5ba'=> 'Pink',
  '#be0032'=> 'Red',
  '#f38400'=> 'Orange',
  '#f3c300'=> 'Yellow',
  '#008856'=> 'Green',
  '#00a1c2'=> 'Blue',
  '#9a4eae'=> 'Purple',
  '#ffb7a5'=> 'Yellowish-Pink',
  '#e25822'=> 'Reddish-Orange',
  '#f6a600'=> 'Orange-Yellow',
  '#dcd300'=> 'Greenish-Yellow',
  '#8db600'=> 'Yellow-Green',
  '#27a64c'=> 'Yellowish-Green',
  '#008882'=> 'Bluish-Green',
  '#0085a1'=> 'Greenish-Blue',
  '#30267a'=> 'Purplish-Blue',
  '#870074'=> 'Reddish-Purple',
  '#ce4676'=> 'Purplish-Red',
];
$form['bikeclub_solo_settings']['cancel_background'] = [
  '#type' => 'details',
  '#title' => t("<strong class='info'>Cancellation Block</strong> - Bikeclub Solo Theme"),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
];

$form['bikeclub_solo_settings']['cancel_background']['cancel_bgcolor'] = [
  '#type' => 'select',
  '#title' => t('Select a background color for the ride cancellation notices on the home page.<br><small>Color turns blue on hover.</small>'),
  '#description' => t(''),
  '#options' => $options,
  '#default_value' => theme_get_setting('cancel_bgcolor'),
];


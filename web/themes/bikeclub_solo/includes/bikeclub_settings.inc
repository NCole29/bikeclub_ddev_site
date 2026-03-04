<?php

/**
 * @file
 * bikeclub_solo.theme
 * Add Club accounts and subtheme colors to the Appearance > theme > settings form.
 * The _bikeclub_form_element method is in the Solo theme.
 */

/**
 * The theme settings.
 */

  $prefix = "bikeclub";
  $label = "Bikeclub"; 

  $form["bikeclub_solo_settings"]["{$prefix}"] = [
    "#type" => "details",
    "#title" => t("<strong class='info'>@label</strong> Settings - Bikeclub Solo SubTheme", ["@label" => $label]),
    "#collapsible" => TRUE,
    "#collapsed" => TRUE,
    "#description" => t("Add RWGPS/Strava links. Configure colors for Banner overlays and paragraph design elements (callouts, cards). "),
  ];

  // Create vertical tabs container.
  $form["bikeclub_solo_settings"]["{$prefix}"]["bikeclub_tabs"] = [
    "#type" => "vertical_tabs",
    "#default_tab" => "edit-club-accounts",
  ];

  // Club accounts Tab.
  $form['bikeclub_solo_settings']["{$prefix}"]['club_accounts'] = [
    '#type' => 'details',
    '#title' => t("RWGPS and Strava links"),
    "#group" => "bikeclub_tabs",
    "#weight" => -10,
    "#id" => "edit-club-accounts",
  ];
  $form['bikeclub_solo_settings']["{$prefix}"]['club_accounts']["{$prefix}_rwgps_url"] = [
    '#type' => 'textfield',
    '#title' => t('RWGPS Club Account'),
    '#default_value' => theme_get_setting("{$prefix}_rwgps_url"),
  ];
  $form['bikeclub_solo_settings']["{$prefix}"]['club_accounts']["{$prefix}_strava_url"] = [
    '#type' => 'textfield',
    '#title' => t('Strava Club Account'),
    '#default_value' => theme_get_setting("{$prefix}_strava_url"),
  ];

  // Banner Colors Tab.
  $form["bikeclub_solo_settings"]["{$prefix}"]["banner_colors_tab"] = [
    "#type" => "details",
    "#title" => t("Banner Overlay Colors"),
    "#group" => "bikeclub_tabs",
    "#weight" => 0,
    '#attributes' => [
      'class' => ['solo-color-panel'],
      'data-solo-panel' => 'colors',
    ],
    "#description" => t('An optional overlay sits on top of the banner image and may contain text and a button. Regardless of the specified textbox background color, the background is made transparent on mobile devices.'),
  ];
  $attributes =  [
    'banner_bg'      => t('Textbox background'),
    'banner_tx_bt'   => t('Button text'),
    'banner_tx_bt_h' => t('Button text (hover)'),
    'banner_bg_bt'   => t('Button background'),
    'banner_bg_bt_h' => t('Button background (hover)'),
  ];
  foreach ($attributes as $attribute_key => $attribute_label) {
    $settings_region[] = "{$prefix}_{$attribute_key}";
  }
  foreach ($attributes as $attribute_key => $attribute_label) {
    $form["bikeclub_solo_settings"]["{$prefix}"]["banner_colors_tab"]["{$prefix}_{$attribute_key}"] = _bikeclub_form_element($prefix, $label, $attribute_key, $attribute_label);
  }

  // Cancellation color Tab.
  $form["bikeclub_solo_settings"]["{$prefix}"]["cancel_color_tab"] = [
    "#type" => "details",
    "#title" => t("Cancellation Color"),
    "#group" => "bikeclub_tabs",
    "#weight" => 0,
    '#attributes' => [
      'class' => ['solo-color-panel'],
      'data-solo-panel' => 'colors',
    ],
    "#description" => t('Ride cancellation alerts are displayed in the <strong>Highlighted region</strong>. That region appears above under Page Wrapper and a background color should be set there.'), 
  ];

  // Paragraphs (callout, card) colors Tab.
  $form["bikeclub_solo_settings"]["{$prefix}"]["color_settings_tab"] = [
    "#type" => "details",
    "#title" => t("Design Components"),
    "#group" => "bikeclub_tabs",
    "#weight" => 0,
    '#attributes' => [
      'class' => ['solo-color-panel'],
      'data-solo-panel' => 'colors',
    ],
    "#description" => t('Design components (Drupal paragraphs types) may be added when selecting Type of page ="Page with section layouts and design components.
    <p>A callout is a textbox with a left border that sets the information apart from the main text.</p> 
    <p>A card contains a title, image, and text and is generally used to display a collection of items (e.g., board members, events, etc).</p>
    Callouts and cards default to a white background unless users select a shaded background which employs the colors specified here'), 
  ];
  $attributes =  [
    'callout_br' => t('Callout border'),
    'callout_bt_bg' => t('Callout button background'),
    'callout_bt_tx' => t('Callout button text'),
    'callout_bg' => t('Callout background, shaded'),
    'card_bg'    => t('Card background, shaded'),
  ];
  foreach ($attributes as $attribute_key => $attribute_label) {
    $settings_region[] = "{$prefix}_{$attribute_key}";
  }

  foreach ($attributes as $attribute_key => $attribute_label) {
    $form["bikeclub_solo_settings"]["{$prefix}"]["color_settings_tab"]["{$prefix}_{$attribute_key}"] = _bikeclub_form_element($prefix, $label, $attribute_key, $attribute_label);
  }


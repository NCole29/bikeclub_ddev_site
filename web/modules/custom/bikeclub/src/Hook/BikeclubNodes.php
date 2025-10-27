<?php

declare(strict_types=1);

namespace Drupal\bikeclub\Hook;

use Drupal\bikeclub\Utility\RenameImages;
use Drupal\bikeclub\Utility\UpdateRecurDates;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateHelper;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Messenger\MessengerInterface;

class BikeclubNodes {

/**
 * Constructor for BikeclubNode Hooks.
 */
 public function __construct(
    protected ConfigFactoryInterface $config,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected MessengerInterface $messenger,
    protected RenameImages $renameImages
  ) {
  }
  
  /**
   * Implements hook_NODE_presave().
   * 
   * Presave operations for media, events, and locations. 
   */
  #[Hook('node_presave')]
  public function nodePresave(EntityInterface $node) {
  
    // Replace image name with Alt text.
    if ($node->hasField('field_text') & isset($node->field_text)) {
      $this->renameImages->fixMedia($node);
    }
    if ($node->hasField('field_components') & isset($node->field_components->target_id)) {
      $this->renameImages->fixPmedia($node);
    }
    if ($node->hasField('banner_image')  & isset($node->banner_image)) {
      $this->renameImages->fixBanners($node);
    }

    // CODE PER CONTENT TYPE.
    // Conditional_fields module does not "zero out" entity reference fields so it's done here.
    $node_type = $node->bundle();
  
    switch ($node_type) {
      case 'event':
        $regType = $node->get('field_registration_type')->value;

        // Clear registration values when updated.
        //  (Conditional fields module doesn't do this for entity reference fields.)
        if ($regType <> 1) { // 1 = webform
          unset($node->field_registration_closing);
          unset($node->field_registration_form);
          unset($node->field_registration_limit);
          unset($node->field_registration_visible);
        } elseif ($regType <> 2) { // 2 = link
          unset($node->field_registration_link);
        }

        // Put webform closing date (default timezone) in node field (UTC) for display. 
        if ($regType == 1 and !empty($node->field_registration_form->close)) { 
          $node->field_registration_closing = $this->convert2UTC($node->field_registration_form->close,);
        }
        break;

      case 'location':
        // Display message if Geocoder Provider is not configured.
        $googleAPI = $this->config->get('geocoder.geocoder_provider.googlemaps')->get('configuration.apiKey');
        
        if (!$googleAPI) {
          $link = \Drupal\Core\Url::fromRoute('entity.geocoder_provider.collection')->toString();
          $content = 'Please configure a <a href="@link">geocoder provider</a> to geocode addresses for map display on the website.
          (The link to Google Maps works even if not geocoded.)';

          $this->messenger->addWarning(t($content, [ '@link' => $link ]));
        }
        break;

      case 'ride':
        // Fill day-of-week for displaying weekend vs weekday rides.
        if ($node->field_date->value) {
          $node->field_dayofweek = DateHelper::dayOfWeek($node->field_date->value);
        }
        break;

      case 'recurring_ride':
        // Get numeric day-of-week from timestamp. 
        // Typecast field_datetime because its a 'bigint' type that Drupal returns as string. 
        $date_time = (int) $node->field_datetime->value;
        $node->field_dayofweek = date('w',$date_time);

        // Edited content.
        if ($node->id()) {
          UpdateRecurDates::deleteDates($node->id()); // Delete future dates.
          UpdateRecurDates::addDates($node,0); // 0 = don't add all, just future dates.
        } 
       break;
    } 


    if ($node_type == 'ride' or $node_type == 'recurring_ride') {
       if ($node->field_ride_picture->target_id) {
          $this->renameImages->fixRideImage($node->field_ride_picture->target_id);
        }

        // Clear the registration fields upon update.
        if ($node->field_registration_required->value == 0) {
          $node->field_registration_form = NULL;
          $node->field_registration_closing = NULL;
          $node->field_rider_limit = NULL;
        } 
        elseif (!empty($node->field_registration_form->close)) { 
          // Put webform closing date (default timezone) in node field (UTC) for display. 
          $node->field_registration_closing = $this->convert2UTC($node->field_registration_form->close,);
        }
    }
  }

  /**
   * Implements hook_ENTITY_TYPE_insert() for node.
   */
  #[Hook('node_insert')]
  function nodeInsert(EntityInterface $node) {
    if ($node->bundle() == 'recurring_ride') {
      UpdateRecurDates::addDates($node, 1);
    }
  }

  /**
   * Implements hook_ENTITY_TYPE_delete() for node.
   */
  #[Hook('node_delete')]
  function nodeDelete(EntityInterface $node) {

    // Delete recurring dates when recurring ride is deleted.
    if ($node->bundle() == 'recurring_ride') {
    
      // Select recurring_dates with field_recurid = recurring_ride node ID. 
      $query = $this->entityTypeManager->getStorage('node')->getQuery();
      $ids = $query
        ->condition('field_recurid', $node->id())
        ->accessCheck(FALSE)
        ->execute();

      $node_storage = $this->entityTypeManager->getStorage('node');
      $entities = $node_storage->loadMultiple($ids);

      foreach ($entities as $recur_date) {
        $recur_date->delete();
      }
    }
  }

  /**
   * Convert webform date stored in site's default timezone to Drupal date (UTC). 
   */ 
  public function convert2UTC($webformDate) {
    // Convert webform date (site default timezone) to UTC timezone.
    $timezone = $this->config->get('system.date')->get('timezone.default');
    $datetime = new DrupalDateTime($webformDate, $timezone);
    $datetime->setTimezone(new \DateTimeZone('UTC'));

    // Return UTC date in Drupal date string format.
    return $datetime->format('Y-m-d\TH:i:s');
  }
 
}

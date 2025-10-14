<?php

namespace Drupal\bikeclub;

use Drupal\Core\Datetime\DrupalDateTime;

class RegCloseDate {
  /**
   * REGISTRATION CLOSING DATE.
   * Webform open and closing dates are not exposed for display on node pages
   *  so save the webform closing date to a node field.
   */ 
  function fillCloseDate($closeString) {

    $closeDate = new DateTime($closeString);
    $timezone = new DateTimeZone('UTC');
    $closeDate->setTimezone($timezone);

    // Return in same format as original closeDateString (escape the 'T').
    return $closeDate->format('Y-m-d\TH:i:s');
  }
}
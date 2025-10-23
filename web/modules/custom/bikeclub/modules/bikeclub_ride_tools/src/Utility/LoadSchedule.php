<?php

namespace Drupal\bikeclub_ride_tools\Utility;

use Drupal\Core\Datetime\DrupalDateTime;

class LoadSchedule {
  /**
   * Add records to the club_schedule table for each day, for 5 years.
   * $load = 0 (initial load), 1 (subsequent loads) 
   */
  public static function loadSchedule($load) {
    $now = time();
    $storage = \Drupal::entityTypeManager()->getStorage('club_schedule');

    if ($load == 0) {
      // Initial load during install starts with current year.
      $startYr = date("Y");
    } else {
      // Start with year after max year in data table.
      $maxdate = \Drupal::entityQueryAggregate('club_schedule')
        ->accessCheck(FALSE)
        ->aggregate('schedule_date', 'MAX', NULL)
        ->execute();
      $startYr = substr($maxdate[0]['schedule_date_max'],0,4) + 1;
    }

    for ($year = $startYr; $year < ($startYr + 3); $year++) {         
      $jan1 = strtotime("First day Of January $year");

      for($x = 0; $x < 365; $x++){
        $timestamp = strtotime("+$x day", $jan1);
        $weekday = date('l', $timestamp);

        $date = DrupalDateTime::createFromTimestamp($timestamp, 'UTC')->format('Y-m-d'); 		

        $newDate = $storage->create([
          'weekday' => $weekday,
          'schedule_date' => $date,
          'created' => $now,
          'changed' => $now,
          'langcode' => "en",
        ]);
        $newDate->save();
      }
    }
  }
}
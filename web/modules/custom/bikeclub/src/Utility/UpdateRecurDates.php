<?php

namespace Drupal\bikeclub\Utility;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Core\Datetime\DateHelper;

/**
 * Calendar is very slow with Smart Dates, faster with Drupal dates.
 * So insert one record per recurring ride date into Recurring Dates content type 
 *   title          = ride name
 *   field_recurid  = entity reference for recurring ride
 *   field_location = starting location ID 
 *   field_date     = Drupal date (instead of Smart date)
 *   field_cancel   = cancellation indicator is updated by UpdateCancellation.php
 *
 * In club_ride.module:
 *  hook_insert calls addDates() for new recurring dates
 *  hook_presave calls deleteDates() and addDates() for existing rides with updated Smart date
 * 
 * Recurring ride registrations are "attached" to ride_date nodes, so we can't delete past dates
 */

final class UpdateRecurDates {

	public static function deleteDates($nid) {
		// Recuring date node IDs for the recurring ride.
		// Delete future dates prior to replacing them.
		$recurids = \Drupal::entityQuery("node")
		  ->accessCheck(FALSE)
  		->condition('field_recurid', $nid)
  		->execute();

		$storage_handler = \Drupal::entityTypeManager()->getStorage("node");

		if (!empty($recurids)) {
			$recur_dates = $storage_handler->loadMultiple($recurids);
	 	  $now = \Drupal::time()->getRequestTime();
			$rdates = [];
			
			foreach ($recur_dates as $rdate) {
				$date = $rdate->get('field_date')->value;
				//echo '<br/>Date: ' . $date;

				if (strtotime($date) > $now) {
					//echo '<br/>Future date: ' . $date;
					$rdates[] = $rdate;
				} 
			}
			if (!is_null($rdates)) {
			 $storage_handler->delete($rdates); // must be array
			}
		}
	}

	public static function addDates($node, $all) {

		// CREATE one 'recurring_date' node for each recurring_ride instance
		// with Drupal date instead of Smart Date timestamp. 
		
		$dates  = $node->field_datetime;

		foreach($dates as $date) {
			$now = \Drupal::time()->getRequestTime();

			// Add all dates (all=1) or replace future dates (all=0).
			$mindate = ($all == 1) ? 0 : $now;

			if ($date->value > $mindate) {
			
				// If no ttimezone conversion, 4 hrs are subtracted assuming timestamp is UTC.
				// Specify 'default' timezone and Drupal converts that to UTC upon save.

				// Get the default timezone value.
				$datetime = DateTimePlus::createFromTimestamp($date->value,'UTC');
				$instance = $datetime->format('Y-m-d\TH:i:s');
				$dayofweek = DateHelper::dayOfWeek($instance);

			//	$drupal_date_time = DrupalDateTime::createFromTimestamp($date->value, 'America/New_York');
				$drupal_date_time = new DrupalDateTime('@' . $date->value, 'America/New_York'); // wrong day


				$new_node = Node::create([
					'type' => 'recurring_dates',
					'status' => '1',
					'langcode' => 'en',
					'title' => $node->title->value,
					'field_recurid' => $node->id(),
					'field_location' => $node->field_location->target_id,
					'field_date' => $drupal_date_time->format('Y-m-d\TH:i:s'),
					'field_dayofweek' => $dayofweek,
				]);
				$new_node->save();
			}
		}
	}
}


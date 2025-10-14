<?php

namespace Drupal\bikeclub_report;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Add member-year records to club_memberyear.
 * Called by OldFeesForm.php and AnnualMembership.php.
 */
class AddmemberYear {

  public static function addYears($contact_ids) {
    $db = \Drupal::database();
    $entity = \Drupal::entityTypeManager()->getStorage('club_memberyear');
    $today = \Drupal::time()->getRequestTime();

    // Loop over contacts.  
    foreach ($contact_ids as $contact_id) {
      $contact = $contact_id->contact_id;

	/* FOR TESTING
      if ($contact > 26) {
        break;
      }
	*/
      // Get contact's last contribution and max Year in member-year table.
      $memberYear = $db->query("SELECT MAX(contribution_id) as maxId, MAX(year) as maxYr FROM {club_memberyear}
        WHERE contact_id = :cid", [':cid' => $contact, ])
      ->fetch();

      $maxId = is_null($memberYear->maxId) ? 0 : $memberYear->maxId;
      $maxYr = is_null($memberYear->maxYr) ? 0 : $memberYear->maxYr;

      // Get new contributions for contact (id > maxID).
      $query = $db->select('civicrm_contribution', 'c');
      $query->leftjoin('civicrm_contribution__field_membership_term', 'mt', 'c.id = mt.entity_id');
      $query
        ->fields('c', ['id','receive_date','total_amount'])
        ->fields('mt', ['field_membership_term_value'])
        ->condition('c.contact_id', $contact, '=')
        ->condition('c.id', $maxId, '>')
        ->condition('c.financial_type_id', 2, '=')
        ->condition('c.contribution_status_id', 1, '=')
        ->condition('mt.field_membership_term_value', NULL, 'IS NOT NULL');
      $contributions = $query->execute()->fetchAll(); 

      // Loop over contact's contributions to fill member-year records.
      foreach($contributions as $contribution) {
        $paymentYr = substr($contribution->receive_date,0,4);
        $term = $contribution->field_membership_term_value;

	    // Convert CiviCRM date to Drupal date string in UTC timezone.
        $receive_date = $contribution->receive_date;

        // Use site default timezone to convert to Drupal date.
        $drupal_date = DrupalDateTime::createFromFormat('Y-m-d H:i:s', $receive_date, new \DateTimeZone(date_default_timezone_get()));
        
        // Save Drupal date string in storage timezone (UTC).
        $date_to_save = $drupal_date
          ->setTimezone(new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE))
          ->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
																  
        // Fill member-year table starting with paymentYR or maxYr+1.
        if ($paymentYr > $maxYr) {
          $startYr = $paymentYr;
        } else {
          $startYr = $maxYr + 1;
        }
        
        for($year = $startYr; $year < ($startYr + $term); $year++){

          $newMemberYr = $entity->create([
            'contact_id' => $contact,
            'contribution_id' => $contribution->id,
            'total_amount' => $contribution->total_amount,
            'receive_date' => $date_to_save,
            'year' => $year,
            'created' => $today,
          ]);
          $newMemberYr->save();

          $maxYr = $year;
        }
      }
    }
  }
}
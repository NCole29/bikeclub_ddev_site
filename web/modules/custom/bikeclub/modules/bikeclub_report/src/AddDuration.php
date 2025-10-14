<?php

namespace Drupal\bikeclub_report;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Fill Drupal field_membership_term on membership contribution records.
 * Called by AnnualMembership.php.
 */
class AddDuration {

  public static function getOldFees() {
    $configname = 'club.memberfees';
    $fees = \Drupal::service('config.factory')->get($configname)->get('fees');

    // Array of historic fees, duration, dates entered at /admin/config/club/member_fees.
    if (!is_null($fees)) {
      foreach ($fees as $fee) {
        if ($fee['fee_amount'] > 0){
          $amount = $fee['fee_amount'];
          $oldfees[(int)$amount]["term"] = $fee['fee_term'];
          $oldfees[(int)$amount]["date"] = $fee['fee_date'];
        }
      }
    return $oldfees;					  
    }

  }

  public function getFees($db) {
    // Array of CURRENT membership fees and duration. 
    $query = $db->select('civicrm_membership_type', 'm');
    $query
      ->fields( 'm', ['minimum_fee', 'duration_unit', 'duration_interval' ])
      ->condition('m.financial_type_id', 2)
      ->condition('m.is_active', 1);
    $mfees = $query->execute()->fetchAll(); 
  
    foreach($mfees as $mfee) {
      if ($mfee->duration_unit == 'year') {
        $fees[(int)$mfee->minimum_fee] = $mfee->duration_interval + 0;
      }
    }
    return $fees;
  }

  public function addTerm($contributions) {
    $db = \Drupal::database();

    $oldfees = $this->getOldFees();
    $fees = $this->getFees($db);
    
    if ($oldfees) {				   
      $dates = array_column($oldfees,'date');
      $maxOldDate = max($dates);

    // Fill field_membership_term on contribution record based on total_amount paid.
    foreach($contributions as $contribution) {
      $pay_date   = substr($contribution->receive_date,0,10); // Get datepart from CiviCRM datetime string
      $pay_amount = $contribution->total_amount;

      // Pay date after the expiration of the last OLD fee.
      if ($pay_date > $maxOldDate and array_key_exists((int)$pay_amount, $fees)) {
        $term = $fees[(int)$pay_amount];  // Use current fees.
      } 
      else {
        // Pay date before the expiration of the matching old fee.
        if (array_key_exists((int)$pay_amount, $oldfees) and $pay_date <= $oldfees[(int)$pay_amount]['date']) {
          $term = $oldfees[(int)$pay_amount]['term']; // Use historic (old) fee.
        }
      }

      // INSERT term, or fill array to display records with missing term.
      if(isset($term) and !is_null($term)) {
        $fields = array(
          'bundle' => 'civicrm_contribution',
          'deleted' => 0,
          'entity_id' => $contribution->id,
          'revision_id' => $contribution->id,
          'langcode' => 'en',
          'delta' => 0,
          'field_membership_term_value' => $term,
        );

        $db->insert('civicrm_contribution__field_membership_term')
          ->fields($fields)
          ->execute();
      }  
        else {
        $missing_terms[$contribution->id]['id'] = $contribution->id;
        $missing_terms[$contribution->id]['receive_date'] = $pay_date;
        $missing_terms[$contribution->id]['total_amount'] = $pay_amount;
      }  
    }

    // Print message for Administrator only.  
    $roles = \Drupal::currentUser()->getRoles();
    if ( in_array('administrator', $roles) ) { 
      $message ='';
      foreach ($missing_terms as $missing) {
        $message .= '<br>Contribution Id: ' .  $missing['id'] . ', Date: ' . $missing['receive_date'] . ', Amount: ' . $missing['total_amount'];
      }
      
      \Drupal::messenger()->addStatus('Membership duration could not determined ' . $message);
    }
  }
}
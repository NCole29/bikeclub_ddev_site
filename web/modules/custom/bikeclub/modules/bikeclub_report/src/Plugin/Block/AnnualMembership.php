<?php

namespace Drupal\bikeclub_report\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\bikeclub_report\AddDuration;
use Drupal\bikeclub_report\AddMemberYear;

#[Block(
  id: "annual_membership",
  admin_label: new TranslatableMarkup("Annual membership"),
  category: new TranslatableMarkup("Club")
)]
class AnnualMembership extends BlockBase {
  
  public function addDuration($db) {
    // Retrive membership contribution records that are missing membership_term.
    $query = $db->select('civicrm_contribution', 'c');
    $query->leftjoin('civicrm_contribution__field_membership_term', 'mt', 'c.id = mt.entity_id');
    $query
      ->fields('c', ['id','total_amount','receive_date'])
      ->fields('mt', ['field_membership_term_value'])
      ->condition('c.financial_type_id', 2)
      ->condition('mt.field_membership_term_value', NULL, 'IS NULL');
    $contributions = $query->execute()->fetchAll(); 
    $count = count($contributions); 

    \Drupal::messenger()->addMessage("Membership duration will be added to $count contribution records."); 

    // Add membership duration (field_member_term) to new contributions.
    if ($count > 0) {
      \Drupal::service('add_duration')->addTerm($contributions);
    }
  }

  public function addMemberYears($db) {
    // List of contact_ids with membership payments prior to this year that have not been processed.
    $thisYear = date("Y");
    $maxYear = $db->query("SELECT MAX(year) as maxYear FROM {club_memberyear}")->fetch();
    $maxYear = is_null($maxYear->maxYear) ? 0 : $maxYear->maxYear;

    if ($maxYear < ($thisYear - 1)) {
      $contact_ids = $db->query("SELECT DISTINCT(contact_id) FROM {civicrm_contribution} 
        WHERE financial_type_id = :finId and contribution_status_id = :cstatus and is_test = :test and
        year(receive_date) > $maxYear and year(receive_date) < ($thisYear - 1)
        ORDER BY contact_id", 
        [':finId' => 2, 
        ':cstatus' => 1, 
        ':test' => 0,
        ])
        ->fetchALL();

      // Process MEMBERSHIP contributions into memberyear records.
      if (count($contact_ids) >0) {
        $count = count($contact_ids); 
        \Drupal::messenger()->addMessage("MemberYear records will be added for $count members.");
        AddMemberYear::AddYears($contact_ids);  
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $db = \Drupal::database();

    /*-------------------------------------------------------------------------------------
      Trigger these functions before report loads:
       (1) add membership duration to membership contribution records
       (2) once-per-year, process prior-year contributions to club_memberyear table
      Did not use rules because CiviCRM processing of memberships and contributions is complex.
      -------------------------------------------------------------------------------------- */
    $this->addDuration($db);    // Add membership duration to new MEMBERSHIP contributions.
    $this->addMemberYears($db); // Add member-year records to enable annual membership counts.

    // BUILD TABLE WITH NUMBER OF MEMBERSHIPS EACH YEAR.
    $counts = $db->query("SELECT year, COUNT(year) as numMembers FROM {club_memberyear}
      GROUP BY year
      ORDER BY year DESC")
      ->fetchAll();

    if (empty($counts)) {
      $rows[] = null;
    } else {
      foreach($counts as $count) {
        if ($count->year < date("Y")) {
          $rows[$count->year]['year'] = $count->year;
          $rows[$count->year]['members'] = $count->numMembers; 
        }
      }
    }

    // Build table
    $title = "<h3 class='w3-block-title'>Membership by Year</h3>";
    $header = ['Year', 'Members'];
    $footer = "<small><small>Membership as of year-end.</small></small>";

    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => 'No content has been found.',
      '#attributes' => array (
        'class' => ['report-table'],
      ),
      '#cache' => array (
        'max-age' => 0,
      ),
    ];
    $tableHTML = \Drupal::service('renderer')->renderInIsolation($build);
    return [
      '#type' => '#markup',
      '#markup' => $title . $tableHTML . $footer,
      '#attached' => [
        'library' => [
          'bikeclub/bikeclub_styles',
        ],
    ];
  }
}
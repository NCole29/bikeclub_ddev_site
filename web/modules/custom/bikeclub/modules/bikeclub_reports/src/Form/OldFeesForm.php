<?php

namespace Drupal\bikeclub_report\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bikeclub_report\AddMemberYear;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form to enter inactive, historic membership fees.
 * When form data are entered for the first time or updated,
 *  1) field_membership_term is filled on civicrm_contribution records (in Drupal field)
 *  2) club_memberyear entities are (re)created for contacts with contributions updated in (1)
 */
class OldFeesForm extends ConfigFormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
   protected $entityTypeManager;

   public function __construct(EntityTypeManagerInterface $entity_type_manager) {
     $this->entityTypeManager = $entity_type_manager;
   }
 
   public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }
 
  protected function getEditableConfigNames() {
    return [
      'club.memberfees',
    ];
  }

  public function getFormId() {
    return 'member_fees_form';
  }

  public function getFees() {
    return $this->config('club.memberfees')->get('fees');
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['instructions'] = [
      '#type' => 'markup',
      '#markup' => "<p>Enter membership fees and associated information for <em>old</em> fees that are in membership contribution records.<br>
      <strong>Do not</strong> enter fees that are currently enabled in <a href='/civicrm/admin/member/membershipType'>CiviCRM Membership Types</a>.</p>
      <p>After clicking submit, historic contribution records are proccessed to add membership duration and enable membership counts by year.
       PLEASE be patient." 
    ];
   
    // Define table.
    $form['fees'] = [
      '#type' => 'table',
      '#header' => [
          $this->t('Fee amount'),
          $this->t('Membership Duration<br>(#years)'),
          $this->t('Last date in effect'),
      ],
      '#rows' => [],
      '#empty' => $this->t('No entries available.'),
      '#attributes' => [
        'class' => ['w3-table-all','w3-medium',],
      ],
    ];

    // Fill form with previously saved values or null.
    $fees = $this->getFees();
    $numRows = 10;

    // Add rows to table.
    for ($i = 1; $i <= $numRows; $i++) {
      // Set the first column.
      $form["fees"][$i]["fee_amount"] = [
        '#type' => 'number',
         '#default_value' => isset($fees[$i]['fee_amount']) ? $fees[$i]['fee_amount'] : null,
       // '#default_value' => $values["fees"][$i]["fee_amount"],
      ];
      // Set the second column.
      $form["fees"][$i]["fee_term"] = [
        '#type' => 'number',
        '#default_value' => isset($fees[$i]['fee_term']) ? $fees[$i]['fee_term'] : null,
        //'#default_value' => $values["fees"][$i]["fee_term"],
      ];
      // Set the third column.
      $form["fees"][$i]["fee_date"] = [
        '#type' => 'date',
        '#default_value' => isset($fees[$i]['fee_date']) ? $fees[$i]['fee_date'] : null,
        //'#default_value' => $values["fees"][$i]["fee_date"],        
      ];
    }
    $form = parent::buildForm($form, $form_state);
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Check if historic fees were updated, and process data.
    $this->compareData($form_state);

    // Save fees.
    $this->config('club.memberfees')
      ->set("fees", $form_state->getValue("fees"))
      ->save();
  }

  /**
   * Any change in historic fees? 
   */
  public function compareData($form_state) {
    $olddata = $this->getFees();
    $newdata = $form_state->getValue("fees");

    if (!is_null($olddata)) {
      echo 'Old data is not null';
      // Compare old and new values in each column.
      $diff1 = $this->compareCols($olddata, $newdata, 'fee_amount');
      $diff2 = $this->compareCols($olddata, $newdata, 'fee_term');
      $diff3 = $this->compareCols($olddata, $newdata, 'fee_date');
    }

    // If fees were added/revised then update membership_term on contributions.
    if (is_null($olddata) or !empty($diff1) or !empty($diff2) or !empty($diff3)) {
      $maxdate = max(array_column($newdata, 'fee_date'));

      // Construct array for fee/date lookup.
      foreach ($newdata as $new) {
        if ($new['fee_amount'] > 0){
          $amount = $new['fee_amount'];
          $newfees[(int)$amount]["term"] = $new['fee_term'];
          $newfees[(int)$amount]["date"] = $new['fee_date'];
        }
      }
      $this->updateContributions($maxdate, $newfees);
    }
  }

  /**
   * Compare data in each column of fees arrays.
   */
  public function compareCols($data1, $data2, $column) {
    $col1 = array_column($data1, $column);
    $col2 = array_column($data2, $column);
    // array_diff returns values in 1st array that are not in 2nd array
    $diff = array_merge(array_diff($col1, $col2), array_diff($col2, $col1));
    return $diff;
  }

  /**
   * Update membership_term on contribution records.
   */
  public function updateContributions($maxdate, $newfees) {
    $entity = 'civicrm_contribution';
	$query = $this->entityTypeManager->getStorage($entity)->getQuery();
    $cids = $query
      ->condition('financial_type_id', 2)
      ->condition('receive_date', $maxdate, '<=')
      ->range(0, 9999999999)
      ->accessCheck(FALSE)
      ->execute();

    $storage = $this->entityTypeManager->getStorage($entity);

    if (count($cids) > 0 ) {
      foreach($cids as $cid) {

        if ($cid > 26) {
          break;
        }

        $contrib = $storage->load($cid);

        $pay_amount = $contrib->get('total_amount')->value;
        $pay_date   = substr($contrib->get('receive_date')->value,0,10);
        $term       = $contrib->get('field_membership_term')->value;

        // Pay date before the expiration of the matching old fee.
        if (array_key_exists((int)$pay_amount, $newfees) and $pay_date <= $newfees[(int)$pay_amount]['date']) {
          $newterm = $newfees[(int)$pay_amount]['term']; 
        }

        // Save new term and keep list of contacts with updated records.
        if ($newterm <> $term) {
          $contrib->set('field_membership_term', $newterm);
          $contrib->save();

          $updatedContacts[] = $contrib->get('contact_id')->target_id;
        }
        $updatedContacts = array_unique($updatedContacts);
        $this->fixMemberYears($updatedContacts);
      }
    }
  }

  /**
   * Create club_memberyear records (which are tabulated in AnnualMembership.php).
   */
  public function fixMemberYears($contact_ids) {
    // Is club_memberyear table already populated?
    $storage = $this->entityTypeManager->getStorage("club_memberyear");
    $memberYrs = $storage->getQuery()
      ->accessCheck(FALSE)
      ->count()->execute();

    // If memberyear is populated, delete records for contact_ids with updated data.
    if ($memberYrs > 0) {
      foreach ($contact_ids as $contact_id) {
		$query = $this->entityTypeManager->getStorage('club_memberyear')->getQuery();
        $memYrIds = $query
          ->accessCheck(FALSE)
          ->condition('contact_id', $contact_id)
          ->execute(); // array of record IDs.

        if (!empty($memYrIds)) {
          $entities = $storage->loadMultiple($memYrIds);
          $storage->delete($entities);    
        }
      } 
    }

    // Process MEMBERSHIP contributions into memberyear records.
    AddMemberYear::AddYears($contact_ids);  
  }
}  
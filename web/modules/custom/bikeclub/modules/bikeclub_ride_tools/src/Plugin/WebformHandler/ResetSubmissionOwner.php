<?php

namespace Drupal\bikeclub_ride_tools\Plugin\WebformHandler;

use Drupal\civicrm_entity\Entity\CivicrmEntityTag;
use Drupal\civicrm_entity\Entity\CivicrmTag;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\user\Entity\User;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform\Entity\WebformSubmission;

/**
 * @WebformHandler(
 *   id = "ResetSubmissionOwner",
 *   label = @Translation("Reset Submission Owner"),
 *   category = @Translation("Custom"),
 *   description = @Translation("Change webform submission owner; tag nonmember riders."),
 * )
 */
class ResetSubmissionOwner extends WebformHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function postSave(WebformSubmissionInterface $webform_submission, $update = true) {

    $sid = $webform_submission->get('sid')->value;  // Submission ID
    $submittedBy = $webform_submission->get('uid')->target_id; 
        
    $data = $webform_submission->getData();
    $contact_id = $data['civicrm_1_contact_1_contact_existing']; // CiviCRM contact ID
    $user_id = $data['civicrm_1_contact_1_contact_user_id'];     // Drupal user ID from CiviCRM
   
    if ( $user_id > 0 and $submittedBy <> $user_id) {
      $this->change_owner($sid, $user_id);
    } 
    // Anonymous users - tag as nonmember rider if not already tagged.
    elseif ($user_id < 1) { 
      $tag_id = $this->getTagId('Nonmember rider');
      $this->tag_nonmember($contact_id, $tag_id);
    }
  }
  
 /**
   * Change the owner of a Webform submission.
   *
   * @param int $sid
   *   The ID of the Webform submission.
   * @param int $new_owner_uid
   *   The user ID of the new owner.
   */
  function change_owner(int $sid, int $new_owner_uid): void {
    /** @var \Drupal\webform\WebformSubmissionInterface $webform_submission */
    $webform_submission = WebformSubmission::load($sid);

    if (!$webform_submission) {
      return;
    }
    try {
      $webform_submission->setOwnerId($new_owner_uid);
      $webform_submission->save();
    } catch (\Exception $e) {
      return;
    }
  }

  /**
   * Get CiviCRM tag id.
   */
  function getTagId($tag_name) {
    $query = \Drupal::entityQuery('civicrm_tag')
      ->condition('name', $tag_name);
    $tag_id = $query->execute();
    $tag_id = reset($tag_id);
    return $tag_id;
  }

  /**
   * Tag nonmembers who register for rides.
   *
   * @param int $contact_id
   *   The CiviCRM Contact ID for the name entered in the registration form.
   * @param int $tag_id
   *   The CiviCRM Tag ID for nonmember rider.
   */
  function tag_nonmember($contact_id, $tag_id) {

    if ($tag_id < 1) {
      return;
    } else {
      // Does contact already have the tag?
      $query = \Drupal::entityQuery('civicrm_entity_tag')
        ->condition('tag_id', $tag_id)
        ->condition('entity_id', $contact_id);
      $hasTag = $query->execute();   
      $hasTag = reset($hasTag);

      if ($hasTag) {
        return;
      } else {
        // Create civicrm_entity_tag to assign the tag to the contact.
        $entity_tag_storage = \Drupal::entityTypeManager()->getStorage('civicrm_entity_tag');

        $entity_tag = $entity_tag_storage->create([
          'entity_id' => $contact_id, //$contact->id(),
          'entity_table' => 'civicrm_contact', // Specify the entity table
          'tag_id' => $tag_id,
        ]);
        $entity_tag->save();
      }
    }
  }
}
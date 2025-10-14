<?php
declare(strict_types=1);

namespace Drupal\bikeclub\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormInterface; 
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\views\ViewExecutableFactory;

/**
 * Hook implementations for forms.
 */
class BikeclubForms {

 /**
  * Constructor for BikeclubNode Hooks.
  */
  public function __construct(
    protected ConfigFactoryInterface $config,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected MessengerInterface $messenger,
    protected AccountProxyInterface $currentUser,
    protected RouteMatchInterface $routeMatch,
    protected ViewExecutableFactory $view_executable
  ) {
  }

  /**
   * Implements hook_form_alter().
   */
  #[Hook('form_alter')]
  public function formAlter(&$form, $form_state, $form_id) {
    // \Drupal::messenger()->addStatus('Form ID: ' . $form_id);

    switch ($form_id) {
      case 'node_ride_form':
      case 'node_ride_edit_form':
      case 'node_ride_quick_node_clone_form':
      case 'node_recurring_ride_form':
      case 'node_recurring_ride_edit_form':
      case 'node_recurring_ride_quick_node_clone_form':
        // Change button text.
        $form['field_ride_leader']['widget']['add_more']['#value'] = "Add another leader";
    
        // Remove instructions "begin typing" ...
        $form['field_registration_link']['widget'][0]['uri']['#description'] = NULL;

        // Disable Route field if RWGPS API key is empty.
        $rwgps = $this->config->get('club.adminsettings')->get('rwgps_api');
        
        if (!$rwgps) {
          $form['field_rwgps_routes']['#disabled'] = TRUE;
          $form['field_rwgps_routes']['widget']['#title'] = 
            "RWGPS Routes - Please enter a <a href='/admin/config/club/rwgps'>RWGPS API key</a> to enable this field.";
        }

        // Recurring ride - remove "Add another" date.
        if (in_array($form_id, ['node_recurring_ride_form','node_recurring_ride_edit_form',
                                'node_recurring_ride_quick_node_clone_form'])) {
          unset($form['field_datetime']['widget']['add_more']);
        }
        break;

      case 'user_form':
        // If editing Own account, hide name (it's set in CiviCRM), status, and roles.
        $form_user = $this->routeMatch->getParameter('user')->id();
        $currentUser = $this->currentUser->id();

        if ($form_user == $currentUser) {
          $form['account']['name']['#access'] = FALSE;
          $form['account']['status']['#access'] = FALSE;
          $form['account']['roles']['#access'] = FALSE;
        }
      break;

      case 'user_login_form':
        // Password description text (see core/modules/user/src/form/UserLoginForm.php)
        $form['pass']['#description'] = t('Forgot your <a href="/user/password"><strong>password?</strong></a>');
      break;

      case 'user_pass':
        // Password reset form
        $form['custom_field'] = [
          '#markup' => t('Be sure to check your spam or junk folder. If you do not find an email, contact the
            <a href="/contact/membership"><strong>Membership Coordinator</strong></a>.<br>
            Return to <a href="/user/login"><strong>Log in</strong></a> page.')];
      break;

      case 'webform_submission_free_event_delete_form':
        // Shorten the default "delete confirmation" message to make if "friendlier" for users.
        // Default: "Warning message: Are you sure you want to delete the [event name]: Submission #[xxx]? This action cannot be undone.
        //  This action will ... Remove records from the database, Delete any uploaded files, Cancel all pending actions. 

        // Get event title and remove the part after the colon which is "Submission #XXX".
        $title = $form['#title']->getArguments('%label');
        $event_name = explode(':',reset($title));
        $form['#title'] = 'Cancel registration for ' . $event_name[0];

        $form['warning']['#message_type'] = NULL;  // Remove type to remove the "Warning message" header.
        $form['warning']['#message_message'] = 'Are you sure you want to delete registration for <strong>' . $event_name[0] . '</strong>?'; 
        $form['description'] = NULL;
      break;

      case 'webform_submission_renew_membership_add_form':
        $user = $form['elements']['contact_pagebreak']['civicrm_1_contact_1_contact_existing']['#value'];

        if ($this->currentUser->id() != $user and $this->getCurrentAdmin() == 1) {
          $this->messenger->addError('Form contains YOUR information. Use CiviCRM to manually renew membership for another user.');
        }
      break;
    }

    // Ride registration form_id contains node info, so can't condition on form_id.
    if ($form_state->getFormObject() instanceof WebformSubmissionForm) {

      $webform = $form_state->getFormObject()->getWebform();

      if ($webform->id() == 'ride_registration') {
        $source_entity = $form_state->getFormObject()->getEntity()->getSourceEntity();

        if ($source_entity && $source_entity->getEntityTypeId() === 'node') {
          $nid = $source_entity->id();
          $node = $this->entityTypeManager->getStorage('node')->load($nid);
    
          if ($node->bundle() == 'ride') {
            $date_string = $node->get('field_date')->value; 
          }
          elseif ($node->bundle() == 'recurring_ride') {
            // View display returns the next ride date for $nid.
            $view = $this->view_executable->get('ride_dates');
            $view->setDisplay('register_next_recurring');
            $view->execute();
            $view->setArguments([$nid,]);
            $view->preExecute();
            $view->execute();
            
            $date_string = $view->result[0]->_entity->get('field_date')->value;
          }
          // Drupal ride_date is stored in UTC, webform needs date in default timezone.
          $form['elements']['ride_date']['#default_value'] = $this->convertTimeZone($date_string);
        }
      }
    }
  }

  /**
   * Functions called by hook_form_alter().
   */
  public function getCurrentAdmin() {
    $roles = $this->currentUser->getRoles();

    $currentAdmin = 0;
    if(in_array('administrator',$roles) | in_array('site_admin',$roles) | in_array('membership_coordinator',$roles)) {
      $currentAdmin = 1;
    }
    return $currentAdmin;
  }

  public function convertTimeZone($date_string) {
    // Transform UTC date_string (UTC) to default timezone.
    $timezone = new \DateTimeZone('UTC');
    $date_time = new DrupalDateTime($date_string, $timezone);
    $timestamp = $date_time->getTimestamp();

    $date = \Drupal::service('date.formatter')->format($timestamp, 'custom', 'Y-m-d');
    return $date;
  }
}
<?php
declare(strict_types=1);

namespace Drupal\bikeclub\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormInterface; 
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Hook implementations for forms.
 */
class BikeclubForms {

 /**
  * Constructor for BikeclubNode Hooks.
  */
  public function __construct(
    protected ConfigFactoryInterface $config,
    protected AccountProxyInterface $currentUser,
    protected RouteMatchInterface $routeMatch
  ) {
  }

  /**
   * Implements hook_form_alter().
   */
  #[Hook('form_alter')]
  public function formAlter(&$form, $form_state, $form_id) {
    //\Drupal::messenger()->addStatus('Form ID: ' . $form_id);

    switch ($form_id) {
      case 'node_ride_form':
      case 'node_ride_edit_form':
      case 'node_ride_quick_node_clone_form':
      case 'node_recurring_ride_form':
      case 'node_recurring_ride_edit_form':
      case 'node_recurring_ride_quick_node_clone_form':
        // Change button text.
        $form['field_ride_leader']['widget']['add_more']['#value'] = "Add another leader";
    
        // Disable Route field if RWGPS API key is empty.
        $rwgps = $this->config->get('bikeclub.adminsettings')->get('rwgps_api');
        if (!$rwgps) {
          $form['field_rwgps_routes']['#disabled'] = TRUE;
          $form['field_rwgps_routes']['widget']['#title'] = 
            "RWGPS Routes - Please enter a <a href='/admin/config/bikeclub/rwgps-api'>RWGPS API key</a> to enable this field. The RideTools module is required.";
        }
       
        if (in_array($form_id, ['node_recurring_ride_form','node_recurring_ride_edit_form',
                                'node_recurring_ride_quick_node_clone_form'])) {
          // Remove "Add another" date.
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
    }
  }
}
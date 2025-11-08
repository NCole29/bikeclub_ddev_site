<?php

namespace Drupal\bikeclub_ride_tools\Hook;

use Drupal\Core\Url;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hook implementations for bikeclub_ride_tools.
 */
class RideHelp {

  /**
   * Implements hook_help().
   */
  #[Hook('help')]
  public function rideHelp($route_name, RouteMatchInterface $route_match) {
    switch ($route_name) {
      case 'help.page.bikeclub_ride_tools':
        $output = '';
        $output .= '<h2>' . t('About') . '</h2>';
        $output .= '<p>' . t('The Bikeclub ride tools module creates content types for ride pages integrated with RWGPS; calendar and list displays of rides; tools for administering rides; ');
        $output .= t('user roles for <em>Rides coordinator</em> and <em>Ride leader</em> (integrated with CiviCRM mailing lists); and optional ride registration forms.') . '</p>';

        $output .= '<h2>' . t('Uses') . '</h2>';
        $output .= '<dl>';

        $output .= '<dt>' . t('Use Ride Tools') . '</dt>';

        $output .= '<dd>' . t('Three ride tools are provided on the Administration menu:<ol>');
        $output .= t('<li><strong>Cancel ride</strong> adds a cancellation notice to the ride page and calendar displays.</li> ');
        $output .= t('<li><strong>Nonmember waiver</strong> provides a form for entering nonmember names and emails if a paper signup form is used at ride starts. This form adds ');
        $output .= t('nonmember names as CiviCRM contacts and triggers an automated followup email that can be edited at ');
        $output .= t(' <a href=":reminder">CiviCRM > Administer > Communications > Schedule reminders</a>.', 
           [':reminder' => Url::fromRoute('civicrm.civicrm_admin_scheduleReminders')->toString()]) . '</li>';
        $output .= '<li>' . t('<strong>View ride schedule</strong> provides a listing of all calendar dates and scheduled rides, making it easy to identify dates with no rides. ') ;
        $output .= t('This tool can be used to assign ride leaders to dates in advance of creating a ride page') . '</li>';
        $output .= '</ol></dd>';

        $output .= '<h2>' . t('Administration') . '</h2';

        $output .= '<dt>'. t('Custom Entities</dt>');

        $output .= '<dd>' . t('This module creates two custom entities which mostly operate "behind the scenes."') . '<ul>';
        $output .= '<li>' . t('<strong>RWGPS route</strong>. When a RWGPS route number is entered on a ride page, route information is obtained via the RWGPS API and saved to the club_route table');
        $output .= t(' for display on ride pages. This requires a <a href="https://ridewithgps.com/api">RWGPS API key</a> which can be entered into site configuration using the <em>RWGPS API key</em> link below.') . '</li>';
        $output .= '<li>' . t('<strong>Schedule date</strong>. The club_schedule table is populated with calendar dates and joined with ride page data for the <em>View ride schedule</em> tool. ');
        $output .= t('At installation, three years of calendar dates are populated. Over time, add more dates with the <em>Add schedule dates</em> link below.') . '</li>';
        $output .= '</dd>';

        $output .= '<dt>' . t('User roles and Permissions') . '</dt>';

        $output .= '<dd>' . t('Two Drupal roles are added, <em>Rides coordinator</em> and <em>Ride leader</em>, along with a CiviCRM mailing list for <em>Ride leaders</em>. ');
        $output .= '<ul><li>' . t('Rides coordinators have permissions to create, publish, edit any, and delete any Ride and Recurring ride.') . '</li>';
        $output .= '<li>' . t('Ride leaders have permissions to create Ride and Recurring ride, but may only save as draft. They may <em>edit own</em> and <em>delete own</em> rides.') . '</li></ul>';

        $output .= t('The <em>CiviCRM Group Role Sync</em> module is installed to sychronize the CiviCRM Ride leader mailing list with the Drupal Ride leader role. This synchronization ');
        $output .= t('works in only one direction, from CiviCRM to Drupal. '); 
        $output .= t('Add/remove CiviCRM contacts in the <a href=":mailing">CiviCRM Ride leader group</a>',[':mailing' => Url::fromURI('internal:/civicrm/group?reset=1')->toString()]); 
        $output .= t(' and go to Drupal <a href=":sync">People > CiviGroup Roles Sync</a> to add/remove the Ride Leader role in Drupal',                   
                    [':sync'    => Url::fromRoute('civicrm_group_roles.admin_config_civicrm.civicrm_group_roles_manual_sync')->toString()]) . '</li><ul>';

        $output .= '</dd></dl>';
        return $output;
    }
  }

}

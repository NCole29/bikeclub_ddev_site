<?php

namespace Drupal\bikeclub_report\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;


class ReportHelp.php {
	
/**
 * Implements hook_help().
 */
  #[Hook('help')]
  public function reportHelp($route_name, RouteMatchInterface $route_match) {
  switch ($route_name) {
    case 'help.page.bikeclub_annualmember':
      $output = '';
      $output .= '<h3>' . t('About') . '</h3>';
      $output .= '<p>' . t("This module provides Annual membership counts calculated from CiviCRM <u>contribution</u> data.<br>CiviCRM membership data are not used because members have a single membership record that is updated upon renewal and membership may not be continuous. 
      <p><strong>Annual membership is necessarily a point-in-time measure. It is calculated as of year-end.</strong></p>

      This module:
      <ol>
      <li>Adds membership duration, stored in a <a href='/admin/structure/civicrm-entity/civicrm-contribution/fields'>Drupal field</a>, to membership contribution records. Duration is obtained from <a href='/civicrm/admin/member/membershipType'>Membership Type</a> based on fee paid.</li>
      <li>Determines years of active membership based on the full sequence of a member's contribution records (because renewals may occur before a membership expiration date). 
      The custom bikeclub_annualmemberyear table stores one record per member and year.</li>
      <li>Provides an Annual Membership Report with counts as of year-end; excluding the current year which is shown in the 'Current Membership' block.</li>
      </ol></p> 

      #1 is triggered to act on new contributions every time the Annual Membership report is loaded (in lieu of adding a cron job).<br>
      #2 is triggered the first time the Annual Membership report is loaded after the first of the year, thus allowing for reconciliation of cancellations and refunds during the year."
      ) . '</p>';
      $output .= '<h3>' . t('Uses') . '</h3>';
      $output .= '<dl>';
      $output .= '<dt>' . t('View the Annual Membership Report') . '</dt>';
      $output .= '<dd>' . t('This report is displayed in a block. Upon installation, this block is placed on a Membership Statistics node along with blocks displaying current membership statistics from <a href="/admin/structure/views">View > CiviCRM Contacts</a>.') . '</dd>';
      $output .= '<dt>' . t('Process Historic Membership Contributions') . '</dt>';
      $output .= '<dd>' . t('Enter <em>old</em> membership fees and associated duration in the <a href=":config-page">old member fees form</a> if your site has historic contribution data and membership fees have changed over time.
      Submitting this form triggers a rebuild of the bikeclub_annualmemberyear table.', 
      [':config-page' => Url::fromRoute('bikeclub_annualmember.admin_settings')->toString()]) . '</dd>';
      $output .= '</dl>';

      $output .= "<h3>Related Processes</h3>";
      $output .= "<table>";
      $output .= "<tr> <th>Task</th><th>Instructions</th></tr>";
      $output .= "<tr>";
      $output .= "<td>Enter current membership fees</td>";
      $output .= "<td>Go to <a href='/civicrm/admin/member/membershipType'>CiviCRM > Administer > CiviMember > Membership types</a>.</td></tr>";
      $output .= "<tr>";
      $output .= "  <td>Revise current membership fees</td>";
      $output .= "  <td>Put the site in maintenance mode, load the Annual Membership report to process existing records with the current membership fees, then revise the fees at <a href='/civicrm/admin/member/membershipType'>CiviCRM > Administer > CiviMember > Membership types</a>.</td></tr>";
      $output .= "<tr>";
      $output .= "  <td>Enter historic membership fees</td>";
      $output .= '  <td>' . t('Go to <a href=":oldfees">old member fees form</a>.', [':oldfees' => Url::fromRoute('bikeclub_annualmember.admin_settings')->toString()]) . " Submitting this form will rebuild the bikeclub_annualmemberyear table.</td>";
      $output .= "</tr>";
      $output .= "<tr>";
      $output .= "  <td>View membership duration on CiviCRM contribution records</td>";
      $output .= "  <td>Go to <a href='/admin/structure/civicrm-entity/civicrm-contribution/memberships'>Structure > CiviCRM Entity > CiviCRM Contribution</a> and click the Membership Contributions tab.</a>";
      $output .= "<br>Individual records may be edited from this page to add missing membership duration. However, it is better to add the historic fees and thereby trigger the rebuilding of the bikeclub_annualmemberyear table.</td>";
      $output .= "</tr></table>";

      return $output;
    }
  }

}
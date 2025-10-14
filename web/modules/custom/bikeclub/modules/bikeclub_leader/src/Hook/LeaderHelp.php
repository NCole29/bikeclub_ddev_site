<?php

namespace Drupal\bikeclub_leader\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Url;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Hook implementations for bikeclub_leader.
 */
class LeaderHelp {

  /**
   * Implements hook_help().
   */
  #[Hook('help')]
  public function leaderHelp($route_name, RouteMatchInterface $route_match) {
    switch ($route_name) {
      case 'help.page.club_leader':
        $output = '';
        $output .= '<h2>' . t('About') . '</h2>';
        $output .= '<p>' . t('The Bikeclub leader module allows you to maintain a list of current and past club officers, directors, and coordinators ') ;
        $output .=  t('and automatically assign Drupal roles for the duration of their term. Current and past leaders are displayed on a public webpage.') . '</p>';

        $output .= '<h2>' . t('Uses') . '</h2>';
        $output .= '<dl>';

        $output .= '<dt>' . t('<strong>Manage</strong> the <a href=":positions">Club positions</a> taxonomy.', [
          ':positions' => Url::fromRoute('entity.taxonomy_vocabulary.overview_form', ['taxonomy_vocabulary' => 'positions',])->toString()]) . '</dt>';

        $output .= '<dd>' . t("Use this taxonomy to maintain the list of club positions. When entering new leaders, you'll select a position from this list. ") ;
        $output .=  t('Use the category field to organize positions - e.g., Directors, Officers, Coordinators. The category is used to group positions for display. ');
        $output .=  t('The order of positions in the taxonomy determines the order for display, within categories. Drag positions to reorder them in the taxonomy.') ;
        $output .=  t('<p>Use the <em>Disabled</em> checkbox to prevent assignment of positions that are no longer in use. ');
        $output .=  t('<strong>DO NOT delete positions</strong> that were used in the past because this will remove those past positions from the Past leader display.');
        $output .= '</dd>';

       $output .= '<dt>' . t('<strong>Manage</strong> <a href=":leaders">Club leaders</a>.',
         [':leaders' => Url::fromRoute('view.club_leaders.edit')->toString()]) . '</dt>';

        $output .= '<dd>' . t('Add club leaders by navigating to <a href=":people">People</a>, clicking the <a href=":leaders">Club leaders</a> tab, and clicking the Add leader button. ', [
          ':people' => Url::fromRoute('entity.user.collection')->toString(),
          ':leaders' => Url::fromRoute('view.club_leaders.edit')->toString()
        ]);
        $output .= '<dd>' . t('About the Add leader form:') . '<ul>';
        $output .= '<li>' . t('There are two name fields.') . '<ol>';
        $output .= '<li>' . t('<em>Name</em> is an autocomplete field. It will return persons with a Drupal User account (only Drupal users can be assigned roles). ');
        $output .= t('This field may be restricted to <em>Members</em> by changing the filter on the <a href=":eligible">Club leaders eligible</a> View.',
          [':eligible' => Url::fromURI('internal:/admin/structure/views/view/club_leaders_eligible')->toString(),
        ]) . '</li>';
        $output .= '<li>' . t('<em>Edited name</em> is a text field. It may be used to standardize names loaded via the autocomplete field - for example, when members use a nickname, or to impose capitalization. ');
        $output .= t('It is also used to load past leaders who are no longer members - i.e., during a data import using the Feeds module. ');
        $output .= t('For current leaders, always fill the autocomplete field so that a Drupal role is assigned. If the <em>Edited name</em> field is also filled, it will be used for display.') . '</li></ul>';
        $output .= '<li>' . t('<em>Start and end dates</em>: (a) define the term of service, (b) identify current vs. past leaders for display, and (c) are used to enable/disable the associated Drupal role for the term of service.') . '</li>';
        $output .= '</ul>';
        
        $output .= '<p>' . t('For leaders who serve multiple consecutive terms, be sure to add a new record to the database for each term to provide a complete list of current and past leaders.');
        $output .= ' Do not simply change the <em>End date</em> for leaders who are re-elected.</p>';
        $output .= '</dd>';


        $output .= '<dt>' . t('<strong>View</strong>  current and past <a href=":viewcl">Club leaders</a>. ', [
          ':viewcl' => Url::fromRoute('view.club_leaders.current')->toString()]) . '</dt>';
        $output .= '<dd>' . t('The Club Leaders View provides public pages. Add <em>Club leaders</em> to the main menu if it does not already exist, with link "/club-leaders".') . '</dd>';
        $output .= '</dl>';


        $output .= '<h2>' . t('Notes') . '</h2>';
        $output .= '<dl><dt>This module:';
        $output .= '<dd><ul>';
        $output .= '<li>Creates a custom entity (ClubLeader), with code to assign the Drupal Role to a club leader when the leader record is saved</li>';
        $output .= '<li>Includes a form hook to customize the <em>Club positions</em> taxonomy overview page</li>';
        $output .= '<li>Includes <em>cleanup</em> code to remove assigned Drupal roles from users when their leader term concludes. 
        This code runs when a leader record is saved and checks the term dates and assigned roles of all leaders, taking account of the possibility of re-election.</li>';
        $output .= '</ul></dd></dt></dl>';
       
        return $output;
    }
  }

}

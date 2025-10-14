<?php

namespace Drupal\bikeclub_report;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Class implementing EntityViewsDataInterface exposes custom entity to views.
 * Reference this class in AnnualMembership.php annotation handlers.
 */

class MemberYearViews extends EntityViewsData implements EntityViewsDataInterface {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['club_memberyear']['contact_id']['relationship'] = [
      'title' => $this->t('Contact record'),
      'help' => $this->t('CiviCRM contact record.'),
      // Table that we join with.
      'base' => 'civicrm_contact',
      'base field' => 'id',
      // ID of relationship handler plugin to use.
      'id' => 'standard',
      // Default label for relationship in the UI.
      'label' => $this->t('CiviCRM contact'),
    ];

    return $data;
  }
}

<?php

namespace Drupal\bikeclub_ride_tools\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\Language;


/**
 * Form controller for the club_schedule entity edit forms.
 *
 * @ingroup club_schedule
 */
class ClubScheduleForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $entity = $this->entity;
    $schedule_date = strtotime($entity->schedule_date->value);
    $print_date = date('l, M d, Y', $schedule_date);

    $form['schedule_form']['#markup'] = 'Add ride leader for ' . $print_date;

    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $form_state->setRedirect('entity.club_schedule.collection');
    $entity = $this->getEntity();
    $entity->save();
  }

}

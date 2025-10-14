<?php

namespace Drupal\bikeclub_ride_tools\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\club_newschedule\LoadSchedule;

/**
 * @file
 * Contains /admin/config/bikeclub/add-schedule-dates
 */
class AddScheduleDates extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'add_schedule_dates';
  }

  public function getDates() {
    $minmax = \Drupal::entityQueryAggregate('club_newschedule')
    ->accessCheck(FALSE)
    ->aggregate('schedule_date', 'MIN', NULL)
    ->aggregate('schedule_date', 'MAX', NULL)
    ->execute();
    
    return $minmax;
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get range of dates in the club_newschedule table.
    $minmax = $this->getDates();

    // Get the date formatter service
    $date_formatter = \Drupal::service('date.formatter');

    $min = $minmax[0]["schedule_date_min"];
    $max = $minmax[0]["schedule_date_max"];

    // Create DrupalDateTime objects.
    $min_datetime = new DrupalDateTime($min, 'UTC'); 
    $max_datetime = new DrupalDateTime($max, 'UTC'); 

    // Format the date
    $min_date = $date_formatter->format($min_datetime->getTimestamp(), 'custom', 'm-d-Y'); 
    $max_date = $date_formatter->format($max_datetime->getTimestamp(), 'custom', 'm-d-Y'); 

    $form['instructions'] = [
      '#type' => 'markup',
      '#markup' => "<p>The database contains Schedule dates from <strong>$min_date</strong> to <strong>$max_date</strong>.<br>Click the button below to add 
      an additional 3 years of dates to the database.</p>" 
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add dates'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    LoadSchedule::loadSchedule("1"); // 1 indicates NOT initial load.
    \Drupal::messenger()->addMessage(t("Three years of dates have been added."));
    $form_state->setRedirect('<front>');
  }
}
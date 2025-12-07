<?php

namespace Drupal\bikeclub_ride_tools\Form;

use Drupal\bikeclub_ride_tools\Utility\LoadSchedule;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @file
 * Contains /admin/config/bikeclub/add-schedule-dates
 */
class AddScheduleDates extends FormBase {

  /**
   * The schedule load type: initial(0) or not(1).
   *
   * @var integer
   */
  protected $loadType;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(DateFormatterInterface $date_formatter. EntityTypeManagerInterface $entity_type_manager) {
    $this->dateFormatter = $date_formatter;
    $this->entityTypeManager = $entity_type_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'add_schedule_dates';
  }

  public function getDates() {
    //$minmax = \Drupal::entityQueryAggregate('club_schedule')
    $query = $this->entityTypeManager->getStorage('club_schedule')->getAggregateQuery();
    $query->accessCheck(FALSE)
    $query->aggregate('schedule_date', 'MIN', NULL)
    $query->aggregate('schedule_date', 'MAX', NULL)
    $minmax = $query->execute();
    
    return $minmax;
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get range of dates in the club_schedule table.
    $minmax = $this->getDates();

    if (!is_null($minmax[0]["schedule_date_min"])) {

      $this->loadType = 1; // Not an initial load.

      $min = $minmax[0]["schedule_date_min"];
      $max = $minmax[0]["schedule_date_max"];

      // Create DrupalDateTime objects.
      $min_datetime = new DrupalDateTime($min); 
      $max_datetime = new DrupalDateTime($max); 

      // Format the date
      $min_date = $this->dateFormatter->format($min_datetime->getTimestamp(), 'custom', 'm-d-Y'); 
      $max_date = $this->dateFormatter->format($max_datetime->getTimestamp(), 'custom', 'm-d-Y'); 

      $form['instructions'] = [
        '#type' => 'markup',
        '#markup' => "<p>The database contains Schedule dates from <strong>$min_date</strong> to <strong>$max_date</strong>.<br>Click the button below to add 
        an additional 3 years of dates to the database.</p>" 
      ];
    } else {
      $this->loadType = 0; // Initial load.
      $form['instructions'] = [
        '#type' => 'markup',
        '#markup' => "<p>Click the button below to add 3 years of dates to the database.</p>" 
      ];
    }

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add dates'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
   
    LoadSchedule::loadSchedule($this->loadType); 
    $this->messenger->addMessage(t("Three years of dates have been added."));
  }
}
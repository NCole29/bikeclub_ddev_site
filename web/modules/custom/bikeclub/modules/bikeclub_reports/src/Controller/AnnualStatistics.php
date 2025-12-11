<?php

namespace Drupal\bikeclub_reports\Controller;

use Drupal\Core\Database\Connection; 
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response; 


class AnnualStatistics implements ContainerInjectionInterface {

  protected $database;
  protected $entityTypeManager;
  protected $loggerFactory;

  public function __construct(
    Connection $database, 
    EntityTypeManagerInterface $entity_type_manager,
    LoggerChannelFactoryInterface $logger_factory)
    {
    $this->database = $database;
    $this->entityTypeManager = $entity_type_manager;
    $this->loggerFactory = $logger_factory;
  }

   public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('entity_type.manager'),
      $container->get('logger.factory'),
    );
   }

 
  public function tabulate() {
  /**
   * Count rides scheduled and cancelled during the year and save to Statistics.
   */
    $year = date('Y');
    $jan1 = $year . '-01-01'; 
    $dec31 = $year . '-12-31';

    $node_storage = $this->entityTypeManager->getStorage('node');

    $bundles = ['ride', 'recurring_dates'];

    foreach ($bundles as $bundle) {
      $count[$bundle]['total'] = $node_storage->getQuery()
        ->condition('type', $bundle, '=')
        ->condition('field_date', $jan1, '>=')
        ->condition('field_date', $dec31, '<=')
        ->accessCheck(FALSE)
        ->count()->execute();

      $count[$bundle]['canceled']  = $node_storage->getQuery()
        ->condition('type', $bundle, '=')
        ->condition('field_date', $jan1, '>=')
        ->condition('field_date', $dec31, '<=')
        ->condition('field_cancel', 1, '=')
        ->accessCheck(FALSE)
        ->count()->execute();  
    }

    $stat['rides'] = $count['ride']['total'] + $count['recurring_dates']['total'];
    $stat['canceled_rides'] = $count['ride']['canceled'] + $count['recurring_dates']['canceled'];

    // Check if node exists and update, else create.
    $categories = ['rides', 'canceled_rides'];

    foreach ($categories as $category) {
      // TODO: check if $nodes has multiple values. Do we need "foreach($nodes)" on line 82?
      $nodes = $node_storage->loadByProperties([
          'type' =>'statistic',
          'field_year' => $year,
          'field_category' => $category,
        ]);

      if (!empty($nodes)) {
        foreach ($nodes as $node) {
          $node->set('field_number', $stat[$category]);
          $node->save();
        }
      } 
      else {
        $node = $node_storage->create([
          'type' => 'statistic',
          'title' => $year . ' ' . $category,
          'field_year' => $year,
          'field_category' => $category,
          'field_number' => $stat[$category],
          'status' => 1,
          'created' => time(),
          'changed' => time(),
        ]);
        $node->save();  
      }
    }
    $logger = $this->loggerFactory->get('bikeclub_stats');
    $logger->info('End-of-year ride counts were added to Statistics: @rides scheduled rides and @cancel cancelled rides.',
     ['@rides'  => print_r($stat['rides'], TRUE), 
      '@cancel' => print_r($stat['canceled_rides'], TRUE) ]);
      

    /**
     * If CiviCRM is installed, retrieve count of current and new memberships and save to Statistics.
     */
    if (\Drupal::moduleHandler()->moduleExists('civicrm')) {
  
      $query = $this->database->select('civicrm_membership', 'm');
      $query->leftjoin('civicrm_contact', 'c', 'm.contact_id = c.id');
      $query
        ->fields('m', ['status_id'])
        ->condition('m.status_id', [1,2], 'IN')
        ->condition('m.is_test', 0, '=')
        ->condition('c.is_deleted', 0, '=');
      $count = $query->countQuery()->execute()->fetchField();

      // Save count.
      $node_storage = $this->entityTypeManager->getStorage('node');

      $year = date('Y');
      $category = 'membership';

      // Check if node exists and update, else create.
      $nodes = $node_storage->loadByProperties([
          'type' =>'statistic',
          'field_year' => $year,
          'field_category' => $category,
        ]);

      if (!empty($nodes)) {
        foreach ($nodes as $node) {
          $node->set('field_number', $count);
          $node->save();
        }
      } 
      else {
        $node = $node_storage->create([
          'type' => 'statistic',
          'title' => $year . ' ' . $category,
          'field_year' => $year,
          'field_category' => $category,
          'field_number' => $count,
          'status' => 1,
          'created' => time(),
          'changed' => time(),
        ]);
        $node->save();  
      }
      $logger = $this->loggerFactory->get('bikeclub_stats');
      $logger->info('End-of-year membership count was added to Statistics. Club has @count current and new members.',
      ['@count' => print_r($count, TRUE)] );
    }

    return new Response('', Response::HTTP_NO_CONTENT); 
 }

}

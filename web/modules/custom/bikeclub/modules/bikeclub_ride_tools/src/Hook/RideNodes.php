<?php

declare(strict_types=1);

namespace Drupal\bikeclub_ride_tools\Hook;

use Drupal\bikeclub_ride_tools\Utility\GetRwgpsClient;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;


/**
 * Hook implementations for the node module.
 */
class RideNodes {

/**
 * Constructor for BikeclubNode Hooks.
 */
 public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected GetRwgpsClient $rwgpsClient
  ) {
  }
  
  /**
   * Implements hook_NODE_presave().
   * 
   * Presave operations realted to Ride Tools (i.e., custom entities).
   */
  #[Hook('node_presave')]
  function nodePresave(EntityInterface $node) {
    $node_type = $node->bundle();

    switch ($node_type) {
      case 'ride':
		// Fill schedule date (entity reference field).
        if ($node->field_date->value) {
          $date_formatter = \Drupal::service('date.formatter');
          $date = $date_formatter->format($node->get('field_date')->date->getTimestamp(), 'custom', 'Y-m-d');

          $query = $this->entityTypeManager->getStorage('club_schedule')->getQuery();
          $id = $query
            ->accessCheck(FALSE)
            ->condition('field_schedule_date', $date, '=')
            ->execute();
          $id = reset($id);

          if ($id) {
            $node->field_schedule_date->target_id = $id;
          }       
        }
	  // No break. Next section applies to ride and recurring_ride.
      case 'recurring_ride':
        // Process RWGPS routes.
        if ($node->field_rwgps_routes) {

          // When Ride is published, call RWGPS API and store data in club_route table.
          // Ride leaders can save draft Ride pages but cannot publish, so they can't put junk in route table.
          if ($node->status->value == 1) {
            $ride_start = $node->field_location->target_id;

            foreach ($node->field_rwgps_routes as $routeId) {
              // Trim to remove whitespace then call RouteInfo to save or update route entity.
              $routeId->target_id = trim($routeId->target_id);
              $this->rwgpsClient->getRouteInfo($routeId->target_id, $ride_start);
            }
          }
        } 
		break;
    } 
  } 

}

<?php

namespace Drupal\bikeclub_ride_tools\Utility;

class GetRwgpsClient {

  protected $client;

  /**
   * Constructs a new GetRwgpsClient object.
   *
   * @param $http_client_factory \Drupal\Core\Http\ClientFactory
   */
  public function __construct($http_client_factory) {
    $config = $this->configFactory->get('club.adminsettings');

    $this->client = $http_client_factory->fromOptions([
      'base_uri' => 'https://ridewithgps.com/routes/',
      'headers' => [
      'apikey' => $config->get('rwgps_api')
        ]
      ]);
    $this->logger = $this->loggerFactory->get('type');
  }

  /**
   * Get route information.
   *
   * @param int $routeId
   *
   * @return array
   */
  public function getRouteInfo($routeId, $ride_start) {

    $entity = $this->entityTypeManager->getStorage('club_route');

    if (is_numeric($routeId)) {

      $url = $routeId . '.json';

      try {
        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() == 200) {
          $routeInfo = json_decode($response->getBody(),true);

          // Set geofield values: starting location point; WKT version of the point; geofield point (polygon is NULL).
          $location_lon_lat = [$routeInfo['first_lng'], $routeInfo['first_lat']];
          $location_wkt = \Drupal::service('geofield.wkt_generator')->wktBuildPoint($location_lon_lat);
          $geofield_point = [
          'value' => $location_wkt,
          ];

          // Determine if route exists in Drupal: if yes then UPDATE, else CREATE.
          $oldRoute = $entity
            ->load($routeId);

          if ($oldRoute) {
            $oldRoute->name->value = $routeInfo['name'];
            $oldRoute->distance->value = round($routeInfo['distance']/1609);  // convert meters to miles
            $oldRoute->elevation_gain->value = round($routeInfo['elevation_gain']*3.281); // convert meters to feet
            $oldRoute->locality->value = $routeInfo['locality'];
            $oldRoute->state->value = $routeInfo['administrative_area'];
            $oldRoute->geofield->setValue([$geofield_point, NULL]);
            $oldRoute->field_ride_start->target_id = $ride_start;
            $oldRoute->created_at->value = $routeInfo['created_at'];
            $oldRoute->updated_at->value = $routeInfo['updated_at'];
            $oldRoute->save();
          } else {
            // Create new route entity.
            $newRoute = $entity->create([
              'rwgps_id' => $routeInfo['id'],
              'name' => $routeInfo['name'],
              'distance' => round($routeInfo['distance']/1609),  // convert meters to miles
              'elevation_gain' => round($routeInfo['elevation_gain']*3.281), // convert meters to feet
              'locality' => $routeInfo['locality'],
              'state' => $routeInfo['administrative_area'],
              'geofield' => [$geofield_point, NULL],
              'created_at' => $routeInfo['created_at'],
              'updated_at' => $routeInfo['updated_at'],
            ]);
            $newRoute->save();
          }
        } // end if = 200
      } catch(\Exception $e) {
        $this->logger->error($e->getMessage());
        $this->messenger->addStatus('Route # not found at RideWithGPS: ' . $routeId);
      } // end catch
    } // end is_numeric
    else {
      $this->messenger->addStatus('Route number is not numeric: ' . $routeId);
    }
  } // end function
} // end class

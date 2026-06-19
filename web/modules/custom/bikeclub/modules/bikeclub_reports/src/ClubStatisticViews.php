<?php

namespace Drupal\bikeclub_reports;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Class implementing EntityViewsDataInterface exposes custom entity to views.
 * This class is referenced in ClubStatistic.php annotation under handlers: 
 *   "views_data" = "Drupal\bikeclub_reports\ClubStatisticViews",
 */

class ClubStatisticViews extends EntityViewsData implements EntityViewsDataInterface {
}

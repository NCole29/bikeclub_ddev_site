<?php

namespace Drupal\bikeclub_reports;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a ClubStatistic entity.
 *
 * We have this interface so we can join the other interfaces it extends.
 *
 * @ingroup club
 */
interface ClubStatisticInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface, EntityPublishedInterface {

}

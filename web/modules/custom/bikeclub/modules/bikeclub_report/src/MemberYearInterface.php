<?php

namespace Drupal\bikeclub_report;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface defining the club_memberyear entity.
 *
 * We have this interface so that we can join the other interfaces it extends.
 *
 * @ingroup club
 */
interface MemberYearInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface, EntityPublishedInterface {

}

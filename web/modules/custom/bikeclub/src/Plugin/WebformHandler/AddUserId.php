<?php

namespace Drupal\bikeclub\Plugin\WebformHandler;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform\Entity\WebformSubmission;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @WebformHandler(
 *   id = "AddUserId",
 *   label = @Translation("Add User Id"),
 *   category = @Translation("Custom"),
 *   description = @Translation("Use email to lookup user id for anonymous submissions."),
 * )
 */
class AddUserId extends WebformHandlerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(WebformSubmissionInterface $webform_submission, $update = true) {
    $data = $webform_submission->getData();
    $email = $data['email'];

    // Return if user is filled or don't have email.
    if ($webform_submission->getOwner()->id() > 0 | empty($email)) {
      return;
    }

    // For anonymous submission, get user_id and setOwner on submission.
    $users = $this->entityTypeManager->getStorage('user')->loadByProperties(['mail' => $email]);
    $user = $users ? reset($users) : FALSE; // Users is array. Take first entry since its unique.

    if ($user) {
      $user_account = $this->entityTypeManager->getStorage('user')->load($user->id());
      $webform_submission->setOwner($user_account);
    }
  }
}

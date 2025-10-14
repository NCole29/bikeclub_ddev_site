<?php

namespace Drupal\bikeclub_report\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\EntityOwnerTrait;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\bikeclub_report\MemberYearInterface;

/**
 * Defines the 'club_memberyear' entity type.
 *
 * @ContentEntityType(
 *   id = "club_memberyear",
 *   label = @Translation("Member-Year record"),
 *   base_table = "club_memberyear",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "owner" = "author",
 *     "published" = "published",
 *   },
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\bikeclub_report\MemberYearListBuilder",
 *     "views_data" = "Drupal\bikeclub_report\MemberYearViewsData",
 *     "form" = {
 *       "default" = "Drupal\bikeclub_report\Form\MemberYearForm",
 *     },
 *   },
 *   admin_permission = "administer Club MemberYear data",
 *   links = {
 *     "canonical" = "/admin/structure/club_memberyear/{club_memberyear}",
 *     "edit-form" = "/admin/structure/club_memberyear/{club_memberyear}/edit",
 *     "collection" = "/admin/structure/club_memberyear/list"
 *   },
 *   field_ui_base_route = "bikeclub_report.memberyear_settings",
 * )
 *
 */
class MemberYear extends ContentEntityBase implements MemberYearInterface {

  use EntityChangedTrait, EntityOwnerTrait, EntityPublishedTrait;
  
  /**
   * {@inheritdoc}
   *
   * Define the field properties here.
   *
   * Field name, type and size determine the table structure.
   *
   * In addition, we can define how the field and its content can be manipulated
   * in the GUI. The behaviour of the widgets used can be determined here.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Unique record ID, assigned as primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('Unique record ID'))
      ->setReadOnly(TRUE);

    // Standard uuid field.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setReadOnly(TRUE);

    $fields['contact_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Contact ID'))
      ->setDescription(t('CiviCRM Contact ID.'))
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Fields extracted from civicrm_contributions. 
    $fields['contribution_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Contribution ID'))
      ->setDescription(t('ID from civicrm_contribution table.'))
      ->setDisplayOptions('view', array(
          'label' => 'inline',
          'type' => 'integer',
          'weight' => 1,
        ))
      ->setDisplayConfigurable('view', TRUE);

    $fields['total_amount'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Total amount'))
      ->setDescription(t('Total amount from from civicrm_contribution table.'))
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['receive_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Receive date'))
      ->setDescription(t('Receive date from civicrm_contribution table.'))
      ->setSettings([
        'datetime_type' => 'datetime',
      ])
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'datetime_default',
        'settings' => [
          'format_type' => 'long',
        ]
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['year'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Membership year'))
      ->setDescription(t('Membership is current in this year based on receive date and total amount.'))
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'integer',
		'weight' => 7,
      ])
      ->setDisplayConfigurable('view', TRUE);          

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

     $fields += static::publishedBaseFieldDefinitions($entity_type);

    return $fields;
  }
}

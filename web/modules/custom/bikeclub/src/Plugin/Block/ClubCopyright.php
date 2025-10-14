<?php

declare(strict_types=1);

namespace Drupal\bikeclub\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an editable 'Copyright' Block.
 *
 */
#[Block(
  id: "bikeclub_copyright",
  admin_label: new TranslatableMarkup("Club copyright"),
  category: new TranslatableMarkup("Club")
)]
class ClubCopyright extends BlockBase  implements ContainerFactoryPluginInterface {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new ClubCopyright instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {

    $year = date("Y");
    $site_name = $this->configFactory->getEditable('system.site')->get('name');

    $text = $this->configuration['text'] ?? 'All Rights Reserved.';
    $copyright = t("©@year @site. $text", [
      '@year' => $year,
      '@site' => $site_name,
    ]);

    $build['content'] = [
      '#markup' => '<p>' . $copyright . '</p>',
    ];
    return $build;
  }

    /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Editable Text'),
      '#default_value' => $config['text'] ?? 'All Rights Reserved.',
      '#description' => $this->t('Enter the text you want to display. Year and Site name are added automatically.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['text'] = $form_state->getValue('text');
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'text' => 'All Rights Reserved.',
    ];
  }

}

<?php

namespace Drupal\language\Plugin\ConfigAction;

use Drupal\Core\Config\Action\Attribute\ConfigAction;
use Drupal\Core\Config\Action\ConfigActionPluginInterface;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\language\Entity\ContentLanguageSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[ConfigAction(
  id: 'makeTranslatable',
  admin_label: new TranslatableMarkup('Enable translations'),
)]
final class MakeBundleTranslatable implements ConfigActionPluginInterface, ContainerFactoryPluginInterface {

  public function __construct(
    private readonly ConfigManagerInterface $configManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get(ConfigManagerInterface::class),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function apply(string $configName, mixed $value): void {
    /** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $bundle */
    $bundle = $this->configManager->loadConfigEntityByName($configName);

    ContentLanguageSettings::loadByEntityTypeBundle(
      $bundle->getEntityType()->getBundleOf(),
      $bundle->id(),
    )->setLanguageAlterable(TRUE)->save();
  }

}

<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_starterkit\Drush\Commands;

use Consolidation\AnnotatedCommand\CommandData;
use Drupal\Component\DependencyInjection\ContainerInterface;
use Drupal\schemadotorg_starterkit\SchemaDotOrgStarterkitConverterInterface;
use Drupal\schemadotorg_starterkit\SchemaDotOrgStarterkitManagerInterface;
use Drush\Commands\DrushCommands;
use Drush\Exceptions\UserAbortException;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Schema.org starter kit Drush commands.
 */
class SchemaDotOrgStarterkitCommands extends DrushCommands {

  /**
   * Constructs a SchemaDotOrgStarterkitCommands object.
   *
   * @param \Drupal\schemadotorg_starterkit\SchemaDotOrgStarterkitManagerInterface $starterkitManager
   *   The Schema.org starter kit manager.
   * @param \Drupal\schemadotorg_starterkit\SchemaDotOrgStarterkitConverterInterface $starterkitConverter
   *   The Schema.org starter kit converter.
   */
  public function __construct(
    protected SchemaDotOrgStarterkitManagerInterface $starterkitManager,
    protected SchemaDotOrgStarterkitConverterInterface $starterkitConverter,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get('schemadotorg_starterkit.manager'),
      $container->get('schemadotorg_starterkit.converter')
    );
  }

  /* ************************************************************************ */
  // Info.
  /* ************************************************************************ */

  /**
   * Allow users to choose the starter kit to be outputted.
   *
   * @hook interact schemadotorg:starterkit-info
   */
  public function infoInteract(InputInterface $input): void {
    $this->interactChooseStarterkit($input, dt('info'));
  }

  /**
   * Validates the Schema.org starter kit info.
   *
   * @hook validate schemadotorg:starterkit-info
   */
  public function infoValidate(CommandData $commandData): void {
    $this->validateStarterkit($commandData);
  }

  /**
   * Outputs a Schema.org starter kits information in Markdown.
   *
   * @param string $name
   *   The name of starter kit.
   *
   * @command schemadotorg:starterkit-info
   *
   * @usage drush schemadotorg:starterkit-info schemadotorg_starterkit_events
   */
  public function info(string $name): void {
    $settings = $this->starterkitManager->getStarterkitSettings($name);
    $this->output()->writeln('Types');
    $this->output()->writeln('');
    foreach ($settings['types'] as $type => $mapping_defaults) {
      [, $schema_type] = explode(':', $type);
      $uri = 'https://schema.org/' . $schema_type;

      $this->output()->writeln('- **' . $mapping_defaults['entity']['label'] . '** (' . $type . ')  ');
      if ($mapping_defaults['entity']['description']) {
        $this->output()->writeln('  ' . $mapping_defaults['entity']['description'] . '  ');
      }
      $this->output()->writeln('  <' . $uri . '>');
      $this->output()->writeln('');
    }
  }

  /* ************************************************************************ */
  // Install.
  /* ************************************************************************ */

  /**
   * Allow users to choose the starter kit to be installed.
   *
   * @hook interact schemadotorg:starterkit-install
   */
  public function installInteract(InputInterface $input): void {
    $this->interactChooseStarterkit($input, dt('install'));
  }

  /**
   * Validates the Schema.org starter kit install.
   *
   * @hook validate schemadotorg:starterkit-install
   */
  public function installValidate(CommandData $commandData): void {
    $this->validateStarterkit($commandData);
  }

  /**
   * Setup the Schema.org starter kit.
   *
   * @param string $name
   *   The name of starter kit.
   *
   * @command schemadotorg:starterkit-install
   *
   * @usage drush schemadotorg:starterkit-install schemadotorg_starterkit_events
   *
   * @aliases soski
   */
  public function install(string $name): void {
    $this->confirmStarterkit($name, dt('install'), TRUE);
    $this->starterkitManager->install($name);
  }

  /* ************************************************************************ */
  // Update.
  /* ************************************************************************ */

  /**
   * Allow users to choose the starter kit to be update.
   *
   * @hook interact schemadotorg:starterkit-update
   */
  public function updateInteract(InputInterface $input): void {
    $this->interactChooseStarterkit($input, dt('update'));
  }

  /**
   * Validates the Schema.org starter kit update.
   *
   * @hook validate schemadotorg:starterkit-update
   */
  public function updateValidate(CommandData $commandData): void {
    $this->validateStarterkit($commandData);
  }

  /**
   * Setup the Schema.org starter kit.
   *
   * @param string $name
   *   The name of starter kit.
   *
   * @command schemadotorg:starterkit-update
   *
   * @usage drush schemadotorg:starterkit-update schemadotorg_starterkit_events
   *
   * @aliases sosku
   */
  public function update(string $name): void {
    $this->confirmStarterkit($name, dt('update'), TRUE);
    $this->starterkitManager->update($name);
  }

  /* ************************************************************************ */
  // Generate.
  /* ************************************************************************ */

  /**
   * Allow users to choose the starter kit to generate.
   *
   * @hook interact schemadotorg:starterkit-generate
   */
  public function generateInteract(InputInterface $input): void {
    $this->interactChooseStarterkit($input, dt('generate'));
  }

  /**
   * Validates the Schema.org starter kit generate.
   *
   * @hook validate schemadotorg:starterkit-generate
   */
  public function generateValidate(CommandData $commandData): void {
    $this->validateStarterkit($commandData);
  }

  /**
   * Generate the Schema.org starter kit.
   *
   * @param string $name
   *   The name of starter kit.
   *
   * @command schemadotorg:starterkit-generate
   *
   * @usage drush schemadotorg:starterkit-generate schemadotorg_starterkit_events
   *
   * @aliases soskg
   */
  public function generate(string $name): void {
    $this->confirmStarterkit($name, dt('generate'));
    $this->starterkitManager->generate($name);
  }

  /* ************************************************************************ */
  // Kill.
  /* ************************************************************************ */

  /**
   * Allow users to choose the starter kit to kill.
   *
   * @hook interact schemadotorg:starterkit-kill
   */
  public function killInteract(InputInterface $input): void {
    $this->interactChooseStarterkit($input, dt('kill'));
  }

  /**
   * Validates the Schema.org starter kit kill.
   *
   * @hook validate schemadotorg:starterkit-kill
   */
  public function killValidate(CommandData $commandData): void {
    $this->validateStarterkit($commandData);
  }

  /**
   * Kill the Schema.org starter kit.
   *
   * @param string $name
   *   The name of starter kit.
   *
   * @command schemadotorg:starterkit-kill
   *
   * @usage drush schemadotorg:starterkit-kill schemadotorg_starterkit_events
   *
   * @aliases soskk
   */
  public function kill(string $name): void {
    $this->confirmStarterkit($name, dt('kill'));
    $this->starterkitManager->kill($name);
  }

  /* ************************************************************************ */
  // Convert.
  /* ************************************************************************ */

  /**
   * Allow users to choose the starter kit to convert.
   *
   * @hook interact schemadotorg:starterkit-convert
   */
  public function convertInteract(InputInterface $input): void {
    $this->interactChooseStarterkit($input, dt('convert'));
  }

  /**
   * Validates the Schema.org starter kit convert.
   *
   * @hook validate schemadotorg:starterkit-convert
   */
  public function convertValidate(CommandData $commandData): void {
    $this->validateStarterkit($commandData);
  }

  /**
   * Convert a Schema.org starter kit to a recipe.
   *
   * @param string $name
   *   The name of starter kit.
   *
   * @command schemadotorg:starterkit-convert
   *
   * @usage drush schemadotorg:starterkit-convert schemadotorg_starterkit_events
   */
  public function convert(string $name): void {
    $this->confirmStarterkit($name, dt('convert'));
    $this->starterkitConverter->convert($name);
    $this->output()->writeln("Created $name/recipe.yml");
  }

  /* ************************************************************************ */
  // Command helper methods.
  /* ************************************************************************ */

  /**
   * Allow users to choose the starter kit.
   *
   * @param \Symfony\Component\Console\Input\InputInterface $input
   *   The user input.
   * @param string $action
   *   The action.
   */
  protected function interactChooseStarterkit(InputInterface $input, string $action): void {
    $name = $input->getArgument('name');
    if ($name) {
      return;
    }

    switch ($action) {
      case 'install':
        $starterkits = array_diff_key(
          $this->starterkitManager->getStarterkits(),
          $this->starterkitManager->getStarterkits(TRUE)
        );
        break;

      case 'convert':
        $starterkits = $this->starterkitManager->getStarterkits();
        break;

      default:
        $starterkits = $this->starterkitManager->getStarterkits(TRUE);
        break;
    }

    if (empty($starterkits)) {
      throw new \Exception(dt('There are no Schema.org starter kits to @action', ['@action' => $action]));
    }

    $starterkits = array_keys($starterkits);
    $choices = array_combine($starterkits, $starterkits);
    $choice = $this->io()->choice(dt('Choose a Schema.org starter kit to @action', ['@action' => $action]), $choices);
    $input->setArgument('name', $choice);
  }

  /**
   * Validates the Schema.org starter kit name.
   */
  protected function validateStarterkit(CommandData $commandData): void {
    $arguments = $commandData->getArgsWithoutAppName();
    $name = $arguments['name'] ?? '';
    $starterkit = $this->starterkitManager->getStarterkit($name);
    if (!$starterkit) {
      throw new \Exception(dt("Schema.org starter kit '@name' not found.", ['@name' => $name]));
    }
  }

  /**
   * Convert Schema.org starter kit command action.
   *
   * @param string $name
   *   The starter kit name.
   * @param string $action
   *   The starter kit action.
   * @param bool $required
   *   Include required types.
   *
   * @throws \Drush\Exceptions\UserAbortException
   */
  protected function confirmStarterkit(string $name, string $action, bool $required = FALSE): void {
    $t_args = [
      '@action' => $action,
      '@name' => $name,
    ];
    if (!$this->io()->confirm(dt("Are you sure you want to @action the '@name' starter kit?", $t_args))) {
      throw new UserAbortException();
    }
  }

}

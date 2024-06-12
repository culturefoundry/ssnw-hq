<?php

declare(strict_types=1);

namespace Drupal\schemadotorg_starterkit\Drush\Commands;

use Consolidation\AnnotatedCommand\CommandData;
use Drupal\Component\DependencyInjection\ContainerInterface;
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
   * @param \Drupal\schemadotorg_starterkit\SchemaDotOrgStarterkitManagerInterface $starterKitManager
   *   The Schema.org starter kit manager.
   */
  public function __construct(
    protected SchemaDotOrgStarterkitManagerInterface $starterKitManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get('schemadotorg_starterkit.manager')
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
    $settings = $this->starterKitManager->getStarterkitSettings($name);
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
   * Allow users to choose the starter kit to be install.
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
    $this->starterKitManager->install($name);
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
    $this->starterKitManager->update($name);
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
    $this->starterKitManager->generate($name);
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
    $this->starterKitManager->kill($name);
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

    if ($action === 'install') {
      $starterkits = array_diff_key(
        $this->starterKitManager->getStarterkits(),
        $this->starterKitManager->getStarterkits(TRUE)
      );
    }
    else {
      $starterkits = $this->starterKitManager->getStarterkits(TRUE);
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
    $starterkit = $this->starterKitManager->getStarterkit($name);
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

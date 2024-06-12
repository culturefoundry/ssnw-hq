<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_jsonld\Kernel;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdBuilderInterface;
use Drupal\schemadotorg_jsonld\SchemaDotOrgJsonLdManagerInterface;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;
use Drupal\Tests\schemadotorg_jsonld\Traits\SchemaDotOrgJsonLdTestTrait;

/**
 * Base class to testing Schema.org JSON-LD.
 *
 * @group schemadotorg
 */
abstract class SchemaDotOrgJsonLdKernelTestBase extends SchemaDotOrgEntityKernelTestBase {
  use SchemaDotOrgJsonLdTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'filter',
    'schemadotorg_jsonld',
  ];

  /**
   * The date formatter service.
   */
  protected DateFormatterInterface $dateFormatter;

  /**
   * Schema.org JSON-LD manager.
   */
  protected SchemaDotOrgJsonLdManagerInterface $manager;

  /**
   * Schema.org JSON-LD builder.
   */
  protected SchemaDotOrgJsonLdBuilderInterface $builder;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['schemadotorg_jsonld']);
    $this->dateFormatter = $this->container->get('date.formatter');
    $this->manager = $this->container->get('schemadotorg_jsonld.manager');
    $this->builder = $this->container->get('schemadotorg_jsonld.builder');
  }

}

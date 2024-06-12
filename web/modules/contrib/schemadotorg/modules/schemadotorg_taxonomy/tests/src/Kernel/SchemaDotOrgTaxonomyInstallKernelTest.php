<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg_taxonomy\Kernel;

use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Tests\schemadotorg\Kernel\SchemaDotOrgEntityKernelTestBase;

/**
 * Tests the functionality of the Schema.org taxonomy installation.
 *
 * @covers \schemadotorg_taxonomy_install()
 * @group schemadotorg
 */
class SchemaDotOrgTaxonomyInstallKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'taxonomy',
    'schemadotorg_taxonomy',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('taxonomy_vocabulary');
    $this->installEntitySchema('taxonomy_term');
    $this->installConfig(['schemadotorg_taxonomy']);

    Vocabulary::create(['name' => 'Tags', 'vid' => 'tags'])->save();
  }

  /**
   * Test Schema.org taxonomy installation.
   */
  public function testInstall(): void {
    \Drupal::moduleHandler()->loadInclude('schemadotorg_taxonomy', 'install');
    schemadotorg_taxonomy_install(FALSE);

    /** @var \Drupal\schemadotorg\SchemaDotOrgMappingInterface|null $mapping */
    $mapping = SchemaDotOrgMapping::load('taxonomy_term.tags');

    // Confirm that Add tags to all content types.
    $this->assertEquals(
      [
        'id' => 'tags',
        'label' => 'Tags',
        'description' => 'Use tags to group articles on similar topics into categories.',
        'auto_create' => TRUE,
      ],
      $this->config('schemadotorg_taxonomy.settings')->get('default_vocabularies.tags')
    );

    // Confirm taxonomy term mapping is created and mapped to DefinedTerm.
    $this->assertEquals('DefinedTerm', $mapping->getSchemaType());
  }

}

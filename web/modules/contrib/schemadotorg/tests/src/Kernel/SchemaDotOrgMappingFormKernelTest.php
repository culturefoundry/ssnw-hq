<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\Core\Form\FormState;
use Drupal\schemadotorg\Entity\SchemaDotOrgMapping;
use Drupal\schemadotorg\Form\SchemaDotOrgMappingForm;

/**
 * Tests the Schema.org mapping form.
 *
 * @coversClass \Drupal\schemadotorg\Form\SchemaDotOrgMappingForm
 * @group schemadotorg
 */
class SchemaDotOrgMappingFormKernelTest extends SchemaDotOrgEntityKernelTestBase {

  /**
   * Test Schema.org mapping form.
   */
  public function testSchemaDotOrgMappingForm(): void {
    $entity_form = SchemaDotOrgMappingForm::create($this->container)
      ->setModuleHandler($this->container->get('module_handler'));

    // Display node:Thing (without bundle) mapping form.
    $this->createSchemaEntity('node', 'Thing');
    $node_mapping = SchemaDotOrgMapping::load('node.thing');
    $entity_form->setEntity($node_mapping);
    $form = $entity_form->buildForm([], new FormState());
    $this->assertEquals('Content type', $form['entity_type']['#title']);
    $this->assertEquals('Thing', $form['entity_type']['link']['#title']);
    $this->assertEquals(' (thing)', $form['entity_type']['link']['#suffix']);
    $this->assertEquals('Thing', $form['schema_type']['label']['#title']);
    $this->assertArrayNotHasKey('schema_properties', $form);
    $this->assertArrayNotHasKey('actions', $form);

    // Display user:Person (with bundle or properties) mapping form.
    $this->createSchemaEntity('user', 'Person');
    $user_mapping = SchemaDotOrgMapping::load('user.user');
    $entity_form->setEntity($user_mapping);
    $form = $entity_form->buildForm([], new FormState());
    $this->assertEquals('Entity type', $form['entity_type']['#title']);
    $this->assertEquals('User', $form['entity_type']['#markup']);
    $this->assertEquals('Person', $form['schema_type']['label']['#title']);
    $this->assertArrayHasKey('schema_properties', $form);
    $this->assertArrayNotHasKey('actions', $form);
  }

}

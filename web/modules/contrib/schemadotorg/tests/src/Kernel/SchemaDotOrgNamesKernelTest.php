<?php

declare(strict_types=1);

namespace Drupal\Tests\schemadotorg\Kernel;

use Drupal\schemadotorg\SchemaDotOrgNamesInterface;

/**
 * Tests the Schema.org names service.
 *
 * @coversDefaultClass \Drupal\schemadotorg\SchemaDotOrgNames
 * @group schemadotorg
 */
class SchemaDotOrgNamesKernelTest extends SchemaDotOrgKernelTestBase {

  /**
   * The Schema.org names service.
   */
  protected SchemaDotOrgNamesInterface $names;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['schemadotorg']);

    $this->names = $this->container->get('schemadotorg.names');
  }

  /**
   * Tests SchemaDotOrgReportBreadcrumbBuilder::snakeCaseToCamelCase().
   *
   * @covers ::snakeCasetoCamelCase
   */
  public function testSnakeCaseToCamelCase(): void {
    $tests = [
      ['one', 'one'],
      ['one_two', 'oneTwo'],
      ['one_two_three', 'oneTwoThree'],
    ];
    foreach ($tests as $test) {
      $this->assertEquals($test[1], $this->names->snakeCaseToCamelCase($test[0]));
    }
  }

  /**
   * Tests SchemaDotOrgReportBreadcrumbBuilder::snakeCaseToUpperCamelCase().
   *
   * @covers ::snakeCasetoCamelCase
   */
  public function testSnakeCaseToUpperCamelCase(): void {
    $tests = [
      ['one', 'One'],
      ['one', 'One'],
      ['one_two', 'OneTwo'],
      ['one_two_three', 'OneTwoThree'],
    ];
    foreach ($tests as $test) {
      $this->assertEquals($test[1], $this->names->snakeCaseToUpperCamelCase($test[0]));
    }
  }

  /**
   * Tests SchemaDotOrgReportBreadcrumbBuilder::camelCaseToSnakeCase().
   *
   * @covers ::camelCaseToSnakeCase
   */
  public function testCamelCaseToSnakeCase(): void {
    $tests = [
      ['one', 'one'],
      ['One', 'one'],
      ['oneTwo', 'one_two'],
      ['OneTwo', 'one_two'],
      ['OneTwoTHREE', 'one_two_three'],
      ['oneTWOThree', 'one_two_three'],
      ['OneTwo_Three', 'one_two__three'],
      ['OneTwo__Three', 'one_two___three'],
    ];
    foreach ($tests as $test) {
      $this->assertEquals($test[1], $this->names->camelCaseToSnakeCase($test[0]));
    }
  }

  /**
   * Tests SchemaDotOrgReportBreadcrumbBuilder::camelCaseToTitleCase().
   *
   * @covers ::camelCaseToTitleCase
   */
  public function testCamelCaseToTitleCase(): void {
    $tests = [
      // Basic.
      ['one', 'One'],
      ['One', 'One'],
      ['oneTwo', 'One Two'],
      ['OneTwo', 'One Two'],
      ['OneTwoTHREE', 'One Two THREE'],
      ['oneTWOThree', 'One TWO Three'],
      ['OneTwo_Three', 'One Two_ Three'],
      // Custom.
      ['Nonprofit501', 'Nonprofit 501'],
      ['gtin', 'GTIN'],
      ['rxcui', 'RxCUI'],
      // Acronyms.
      ['ShaHash', 'SHA Hash'],
      ['TheShaHash', 'The SHA Hash'],
      ['TheSha', 'The SHA'],
      // Minor words.
      ['ThisIsASentence', 'This Is a Sentence'],
      ['WhatIf', 'What if'],
      ['IfThatIsNotTrue', 'If That Is not True'],
    ];
    foreach ($tests as $test) {
      $this->assertEquals($test[1], $this->names->camelCaseToTitleCase($test[0]));
    }
  }

  /**
   * Tests SchemaDotOrgReportBreadcrumbBuilder::camelCaseToSentenceCase().
   *
   * @covers ::camelCaseToSentenceCase
   */
  public function testCamelCaseToSentenceCase(): void {
    $tests = [
      // Basic.
      ['one', 'One'],
      ['One', 'One'],
      ['oneTwo', 'One two'],
      ['OneTwo', 'One two'],
      ['OneTwoTHREE', 'One two THREE'],
      ['oneTWOThree', 'One TWO three'],
      ['OneTwo_Three', 'One two_ three'],
      // Custom.
      ['Nonprofit501', 'Nonprofit 501'],
      ['gtin', 'GTIN'],
      ['rxcui', 'RxCUI'],
      // Acronyms.
      ['ShaHash', 'SHA hash'],
      ['TheShaHash', 'The SHA hash'],
      ['TheSha', 'The SHA'],
      // Minor words.
      ['ThisIsASentence', 'This is a sentence'],
      ['WhatIf', 'What if'],
      ['IfThatIsNotTrue', 'If that is not true'],
    ];
    foreach ($tests as $test) {
      $this->assertEquals($test[1], $this->names->camelCaseToSentenceCase($test[0]));
    }
  }

  /**
   * Tests SchemaDotOrgReportBreadcrumbBuilder::camelCaseToDrupalName().
   *
   * @covers ::camelCaseToDrupalName
   */
  public function testCamelCaseToDrupalName(): void {
    $this->assertEquals('one_two', $this->names->camelCaseToDrupalName('OneTwo'));
    $this->assertEquals('action_test', $this->names->camelCaseToDrupalName('actionableTest'));
    $this->assertEquals('action_test', $this->names->camelCaseToDrupalName('actionableTest', ['maxlength' => 5]));
    $this->assertEquals('action', $this->names->camelCaseToDrupalName('actionableTest', ['maxlength' => 6, 'truncate' => TRUE]));
    $this->assertEquals('action', $this->names->camelCaseToDrupalName('actionableTest', ['maxlength' => 7, 'truncate' => TRUE]));
  }

  /**
   * Tests SchemaDotOrgReportBreadcrumbBuilder::schemeIdToDrupalLabel().
   *
   * @covers ::schemaIdToDrupalLabel
   */
  public function testSchemaLabelToDrupalLabel(): void {
    // Check that types use title case.
    $this->assertEquals('One Two', $this->names->schemaIdToDrupalLabel('types', 'OneTwo'));

    // Check that properties use sentence case.
    $this->assertEquals('One two', $this->names->schemaIdToDrupalLabel('properties', 'OneTwo'));
  }

  /**
   * Tests SchemaDotOrgReportBreadcrumbBuilder::schemaIdToDrupalName().
   *
   * @covers ::schemaIdToDrupalName
   */
  public function testSchemaIdToDrupalName(): void {
    $tests = [
      // Schema.org types.
      ['types', 'ActionAccessSpecification', 'action_access_specification'],
      ['types', 'DigitalDocumentPermissionType', 'digit_doc_permission_type'],
      ['types', 'EUEnergyEfficiencyCategoryA1Plus', 'eu_energy_eff_category_a1_plus'],
      ['types', 'MedicalCode', 'medical_code'],
      ['types', 'WearableMeasurementChestOrBust', 'wearable_measurement_chest_or_bust'],
      // Schema.org properties.
      ['properties', 'cvdNumBeds', 'cvd_beds'],
      ['properties', 'disambiguatingDescription', 'disambiguating_desc'],
      ['properties', 'educationalCredentialAwarded', 'edu_credential_awarded'],
      ['properties', 'itemDefectReturnShippingFeesAmount', 'itm_def_ret_ship_fees_amt'],
      ['properties', 'specialOpeningHoursSpecification', 'special_opening_hrs_spec'],
      ['properties', 'verificationFactCheckingPolicy', 'ver_fact_checking_policy'],
    ];
    foreach ($tests as $test) {
      $this->assertEquals($test[2], $this->names->schemaIdToDrupalName($test[0], $test[1]));
    }
  }

}

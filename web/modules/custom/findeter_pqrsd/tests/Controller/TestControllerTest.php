<?php

namespace Drupal\findeter_pqrsd\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the findeter_pqrsd module.
 */
class TestControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "findeter_pqrsd TestController's controller functionality",
      'description' => 'Test Unit for module findeter_pqrsd and controller TestController.',
      'group' => 'Other',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests findeter_pqrsd functionality.
   */
  public function testTestController() {
    // Check that the basic functions of module findeter_pqrsd.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}

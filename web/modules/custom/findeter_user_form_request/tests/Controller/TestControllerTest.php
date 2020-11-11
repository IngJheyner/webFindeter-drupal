<?php

namespace Drupal\findeter_user_form_request\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the findeter_user_form_request module.
 */
class TestControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "findeter_user_form_request TestController's controller functionality",
      'description' => 'Test Unit for module findeter_user_form_request and controller TestController.',
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
   * Tests findeter_user_form_request functionality.
   */
  public function testTestController() {
    // Check that the basic functions of module findeter_user_form_request.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}

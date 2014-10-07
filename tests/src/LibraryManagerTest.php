<?php
/**
 * @file
 * Contains \Drupal\summoner\Tests\LibraryManagerTest.
 */

namespace Drupal\summoner\Tests;

use Drupal\Core\Cache\NullBackend;
use Drupal\simpletest\KernelTestBase;
use Drupal\summoner\Exception\LibraryNotFoundException;
use Drupal\summoner\LibraryManager;

/**
 * @group summoner
 */
class LibraryManagerTest extends KernelTestBase {

  /* @var \Drupal\summoner\LibraryManager */
  protected $manager;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->manager = new LibraryManager(new NullBackend('summoner.tests'), array(
      drupal_get_path('module', 'summoner') . '/tests/libraries',
      '[profile]/libraries',
      '[site]/libraries',
    ));
  }

  public function testConstructor() {
    $this->assertNotNull($this->manager, 'LibraryManager initialized.');

    $module = drupal_get_path('module', 'summoner');
    $profile = drupal_get_path('profile', drupal_get_profile());
    $site = \Drupal::service('kernel')->getSitePath();

    $this->assertEqual(array(
      $module . '/tests/libraries',
      $profile . '/libraries',
      $site . '/libraries',
    ), $this->manager->getSearchPaths(), 'Search paths correct.');
  }

  public function testDiscovery() {

    // Test if library 'bower_a' exists.
    $this->assertTrue(array_key_exists('bower_a', $this->manager->getLibraries()));

    // Fetch the library and check if its the correct one.
    $library = $this->manager->getLibrary('bower_a');
    $this->assertEqual('bower_a', $library->getName(), 'Library "bower_a" found.');

    $exception = null;
    try {
      $this->assertNull($this->manager->getLibrary('bower_y'), 'Unknown library returns null.');
    }
    catch (LibraryNotFoundException $exc) {
      $exception = $exc;
    }
    $this->assertNotNull($exception, 'Library not found exception fired.');
  }

  public function testPathProcessing() {
    $module = drupal_get_path('module', 'summoner');

    // Check a simple base bath to a library.
    $base_path = '/libraries/bower_a';
    $this->assertTrue($this->manager->processPath($base_path), 'Base path process returns TRUE.');
    $this->assertEqual($base_path, $module . '/tests/libraries/bower_a', 'Processed base path is correct.');

    // Check an asset path.
    $asset_path = '/libraries/bower_a/test.js';
    $this->assertTrue($this->manager->processPath($asset_path), 'Asset path process returns TRUE.');
    $this->assertEqual($base_path, $module . '/tests/libraries/bower_a/test.js', 'Processed asset path is correct.');

    // Test behavior for a nonexistent path.
    $unknown_path = '/libraries/bower_y';
    $this->assertFalse($this->manager->processPath($unknown_path), 'Non-existent path returns false.');
    $this->assertEqual($base_path, '/libraries/bower_y', 'Unknown path remains untouched.');
  }

}
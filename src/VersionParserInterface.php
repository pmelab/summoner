<?php
/**
 * @file
 * Contains \Drupal\summoner\VersionParserInterface.
 */

namespace Drupal\summoner;

/**
 * Interface VersionParserInterface
 * @package Drupal\summoner
 */
interface VersionParserInterface {
  /**
   * @param Library $library
   * @return \Drupal\summoner\Version
   */
  public function applies(Library $library);

  /**
   * @param Library $library
   * @return \Drupal\summoner\Version
   */
  public function getVersion(Library $library);
}
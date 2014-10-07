<?php

/**
 * @file
 * Contains \Drupal\summoner\BowerVersionParser.
 */

namespace Drupal\summoner;

use Drupal\Component\Serialization\Json;

class BowerVersionParser implements VersionParserInterface {
  /**
   * {@inheritdoc}
   */
  public function applies(Library $library) {
    return file_exists($library->getPath() . '/bower.json');
  }

  /**
   * {@inheritdoc}
   */
  public function getVersion(Library $library) {
    $data = Json::decode($library->getPath() . '/bower.json');
    return new Version(1, 0, 0);
  }

}
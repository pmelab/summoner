<?php
/**
 * Created by PhpStorm.
 * User: philipp
 * Date: 06.10.14
 * Time: 16:07
 */

namespace Drupal\summoner;

class Version {
  protected $major;
  protected $minor;
  protected $patch;

  function __construct($major, $minor, $patch) {
    $this->major = $major;
    $this->minor = $minor;
    $this->patch = $patch;
  }

  /**
   * @param string $version
   * @return bool
   */
  public function matches($version) {
    return TRUE;
  }
}
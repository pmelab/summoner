<?php

/**
 * @file
 * Contains \Drupal\summoner\Library.
 */

namespace Drupal\summoner;

class Library {
  protected $name;
  protected $path;

  function __construct($name, $path) {
    $this->name = $name;
    $this->path = $path;
  }

  /**
   * @return mixed
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return mixed
   */
  public function getPath() {
    return '/' . $this->path . '/';
  }
}
<?php

/**
 * @file
 * Contains Drupal\summoner\LibraryList.
 */

namespace Drupal\summoner;

/**
 * Simple container for a set of libraries to be able to use typehinting in the
 * controller class.
 */
class LibraryList extends \ArrayIterator {
  public function __construct($value) {
    $libraries = explode(',', $value);
    array_walk($libraries, function (&$lib) {
      $lib = str_replace('::', '/', $lib);
    });
    parent::__construct($libraries, 0);
  }
}
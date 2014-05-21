<?php

/**
 * @file
 * Contains Drupal\summoner\LibraryList.
 */

namespace Drupal\summoner;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
    $libraryDiscovery = \Drupal::service('library.discovery');
    foreach ($libraries as $lib) {
      list($extension, $name) = explode('/', $lib);
      $result = $libraryDiscovery->getLibraryByName($extension, $name);
      if (!$result) {
        // TODO: Exception type and not that hacky.
        throw new \Exception('Unknown library ' . $extension . '/' . $name);
      }
    }
    parent::__construct($libraries, 0);
  }
}
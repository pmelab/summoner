<?php

/**
 * @file
 * Contains \Drupal\summoner\LibraryDetection.
 */

namespace Drupal\summoner;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\summoner\Exception\LibraryNotFoundException;
use SebastianBergmann\Exporter\Exception;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LibraryManager {
  protected $cacheBackend;
  protected $libraries;
  protected $search_paths;

  /**
   * Create a new instance of this service.
   *
   * @param CacheBackendInterface $cache_backend
   * @param array $search_paths
   */
  function __construct(CacheBackendInterface $cache_backend, array $search_paths) {
    $this->cacheBackend = $cache_backend;
    $this->search_paths = str_replace(array(
      '[profile]',
      '[site]'), array(
      drupal_get_path('profile', drupal_get_profile()),
      \Drupal::service('kernel')->getSitePath()
    ), $search_paths);
  }

  /**
   * Return the configured search paths.
   * @return mixed
   */
  public function getSearchPaths() {
    return $this->search_paths;
  }

  /**
   * Transform a libraries path.
   *
   * @param $path
   * @return bool|string
   * @throws LibraryNotFoundException
   */
  public function processPath(&$path) {
    if (preg_match_all('/^\/libraries\/([^\/]*)/', $path, $matches)) {
      try {
        $path = '/' . str_replace($matches[0][0], $this->getLibrary($matches[1][0])->getPath() . '/', $path);
        return TRUE;
      }
      catch (Exception $exc) {
        return FALSE;
      }
    }
    return FALSE;
  }

  /**
   * @param $name
   * @return \Drupal\summoner\Library
   * @throws LibraryNotFoundException
   */
  public function getLibrary($name) {
    if (!isset($this->libraries)) {
      $this->getLibraries();
    }
    if (!array_key_exists($name, $this->libraries)) {
      throw new LibraryNotFoundException(array($name));
    }
    return $this->libraries[$name];
  }

  public function getLibraries() {
    if (isset($this->libraries)) {
      return $this->libraries;
    }

    $this->libraries = array();
    $nomask = array('CVS');

    foreach ($this->search_paths as $dir) {
      if (is_dir($dir) && $handle = opendir($dir)) {
        while (FALSE !== ($file = readdir($handle))) {
          if (!in_array($file, $nomask) && $file[0] != '.') {
            if (is_dir("$dir/$file")) {
              $this->libraries[$file] = new Library($file, "$dir/$file");
            }
          }
        }
        closedir($handle);
      }
    }

    return $this->libraries;
  }
}

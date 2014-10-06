<?php

/**
 * @file
 * Contains \Drupal\summoner\LibraryDetection.
 */

namespace Drupal\summoner;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\summoner\Exception\LibraryNotFoundException;

class LibraryManager {
  protected $cacheBackend;

  protected $libraries;

  /**
   * Create a new instance of this service.
   *
   * @param CacheBackendInterface $cache_backend
   */
  function __construct(CacheBackendInterface $cache_backend) {
    $this->cacheBackend = $cache_backend;
  }

  public function processLibraries($libraries) {
    foreach ($libraries as $id => $library) {
      if (array_key_exists('js', $library)) {
        foreach ($library['js'] as $key => $data) {
          if ($new_path = $this->processPath($key)) {
            unset($libraries[$id]['js'][$key]);
            $libraries[$id]['js'][$new_path] = $data;
          }
        }
      }

      if (array_key_exists('css', $library)) {
        foreach ($library['css'] as $group => $files) {
          foreach ($files as $key => $data) {
            if ($new_path = $this->processPath($key)) {
              unset($libraries[$id]['css'][$group][$key]);
              $libraries[$id]['css'][$group][$new_path] = $data;
            }
          }
        }
      }
    }
    return $libraries;
  }

  protected function processPath($path) {
    if (preg_match_all('/^\/libraries\/(.*?)\//', $path, $matches)) {
      return str_replace($matches[0][0], $this->getLibrary($matches[1][0])->getPath(), $path);
    }
  }

  /**
   * @param $name
   * @return Library
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

  protected function getLibraries() {
    if (isset($this->libraries)) {
      return $this->libraries;
    }
    $searchdir = array();
    $profile = drupal_get_path('profile', drupal_get_profile());
    $config = conf_path();

    // @todo core/libraries

    // Similar to 'modules' and 'themes' directories inside an installation
    // profile, installation profiles may want to place libraries into a
    // 'libraries' directory.
    $searchdir[] = "$profile/libraries";

    // Search sites/all/libraries for backwards-compatibility.
    $searchdir[] = 'sites/all/libraries';

    // Always search the root 'libraries' directory.
    $searchdir[] = 'libraries';

    // Also search sites/<domain>/*.
    $searchdir[] = "$config/libraries";

    // Retrieve list of directories.
    $this->libraries = array();
    $nomask = array('CVS');
    foreach ($searchdir as $dir) {
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

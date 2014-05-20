<?php

/**
 * @file
 * Contains Drupal\summoner\ParamConverter\LibraryConverter.
 */

namespace Drupal\summoner\ParamConverter;
use Drupal\Core\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * Parameter converter for checking if libraries exist and turn the
 * arguments into #attach-able arrays.
 */
class LibraryConverter implements ParamConverterInterface {
  /**
   * {@inheritdoc}
   */
  public function convert($value, $definition, $name, array $defaults, Request $request) {
    $libraries = explode(',', $value);
    foreach ($libraries as $index => $lib) {
      $libraries[$index] = str_replace('::', '/', $lib);
    }
    return $libraries;
  }

  /**
   * {@inheritdoc}
   */
  public function applies($definition, $name, Route $route) {
    return !empty($definition['type']) && $definition['type'] == 'summoner.libraries';
  }
}
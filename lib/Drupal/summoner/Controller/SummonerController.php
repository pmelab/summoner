<?php
/**
 * @file
 * Contains \Drupal\summoner\Controller\SummonerController.
 */

namespace Drupal\summoner\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Page callback controller for fetching asset uri's.
 */
class SummonerController {
  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function load(Request $request) {
    $id = $request->attributes->get('id');
    $params = explode(',', $request->get('libraries'));
    $libraries = array();
    foreach ($params as $param) {
      $libraries[] = explode(':', $param);
    }
    $attached['#attached'] = array('library' => array());
    foreach ($libraries as $library) {
      $attached['#attached']['library'][] = $library[0] . '/' . $library[1];
    }
    $attached['#attached']['js'][] = array(
      'type' => 'setting',
      'data' => array('summoner-id' => $id),
    );
    drupal_render($attached);
    $response = new AjaxResponse();
    return $response;
  }
}

<?php
/**
 * @file
 * Contains \Drupal\summoner\Controller\SummonerController.
 */

namespace Drupal\summoner\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Symfony\Component\HttpFoundation\Request;

/**
 * Page callback controller for fetching asset uri's.
 */
class SummonerController {
  /**
   * @param Request $request
   * @return AjaxResponse
   */
  public function load(Request $request) {
    $id = $request->attributes->get('id');
    $libraries = explode(',', $request->get('libraries'));
    $attached['#attached'] = array('library' => array());
    foreach ($libraries as $library) {
      $attached['#attached']['library'][] = $library;
    }
    $attached['#attached']['js'][] = array(
      'type' => 'setting',
      'data' => array('summoner-id' => $id),
    );
    drupal_render($attached);
    $response = new AjaxResponse();
    $response->addCommand(new InvokeCommand('body', 'summonerLoaded', array($id)));
    return $response;
  }
}

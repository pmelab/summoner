<?php
/**
 * @file
 * Contains \Drupal\summoner\Controller\SummonerController.
 */

namespace Drupal\summoner\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;

/**
 * Page callback controller for fetching asset uri's.
 */
class SummonerController {
  /**
   * @param integer $id
   * @param array $libraries
   * @return AjaxResponse
   */
  public function load($id, $libraries) {
    $attached['#attached'] = array('library' => $libraries);
    drupal_render($attached);
    $response = new AjaxResponse();
    $response->addCommand(new InvokeCommand('body', 'summonerLoaded', array($id)));
    return $response;
  }
}

<?php
/**
 * @file
 * Contains \Drupal\summoner\Controller\SummonerController.
 */

namespace Drupal\summoner\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\summoner\LibraryList;

/**
 * Page callback controller for fetching asset uri's.
 */
class SummonerController {
  /**
   * @param integer $id
   * @param LibraryList $libraries
   * @return AjaxResponse
   */
  public function load($id, LibraryList $libraries) {
    $state = array();
    foreach ($libraries as $lib) {
      $state[$lib] = TRUE;
    }

    $attached['#attached'] = array(
      'library' => $libraries,
      'js' => array(
        array(
          'data' => array('summonerState' => $state),
          'type' => 'setting',
        ),
      ),
    );

    drupal_render($attached);
    $response = new AjaxResponse();
    $response->addCommand(new InvokeCommand('body', 'summonerLoaded', array($id)));
    return $response;
  }
}

/**
 * @file
 * Load javascript, css and library assets on demand.
 */
(function($, Drupal, drupalSettings){
  var summonerRequest = 0;
  var $summonerAnchor = false;

  var summonerCallbacks = {};

  Drupal.summon = function (libraries, callback, type) {
    libraries = $.isArray(libraries) ? libraries : [libraries];
    var id = "summoner-link-" + (++summonerRequest);
    var url = Drupal.url('summoner/load/' + summonerRequest + '?libraries=' + libraries.join(','));
    var $link = $('<a id="' + id + '" href="' + url + '" class="use-ajax"/>');
    $link.appendTo($summonerAnchor);
    Drupal.behaviors.AJAX.attach($summonerAnchor, drupalSettings);
    summonerCallbacks[summonerRequest] = callback;
    $link.click();
  };

  Drupal.behaviors.summoner = {
    attach: function (context, settings) {
      if (!$summonerAnchor) {
        $summonerAnchor = $('<div style="display: none" id="summoner-anchor"/>');
        $summonerAnchor.appendTo('body');
      }
    }
  };

  $.fn.summonerLoaded = function(id) {
    $('#summoner-link-' + id).remove();
    $('#summoner-loaded-' + id).remove();
    if (summonerCallbacks[id]) {
      summonerCallbacks[id]();
    }
  };

}(jQuery, Drupal, drupalSettings));
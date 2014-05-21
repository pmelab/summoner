/**
 * @file
 * Load javascript, css and library assets on demand.
 */
(function($, Drupal, drupalSettings){
  var summonerRequest = 0;
  var $summonerAnchor = false;

  var summonerCallbacks = {};
  drupalSettings.summonerState = drupalSettings.summonerState || {};

  Drupal.summon = function (libraries, callback) {
    libraries = $.isArray(libraries) ? libraries : [libraries];
    var toLoad = [];
    $.each(libraries, function (index, lib){
      if (!drupalSettings.summonerState[lib]) {
        var library = lib.replace('/', '::');
        toLoad.push(library);
      }
    });
    if (toLoad.length > 0) {
      var id = "summoner-link-" + (++summonerRequest);
      var url = Drupal.url('summoner/load/' + summonerRequest + '/' + toLoad.join(','));
      var $link = $('<a id="' + id + '" href="' + url + '" class="use-ajax"/>');
      $link.appendTo($summonerAnchor);
      Drupal.behaviors.AJAX.attach($summonerAnchor, drupalSettings);
      summonerCallbacks[summonerRequest] = callback;
      $link.click();
    }
    else {
      callback();
    }
  };

  Drupal.behaviors.summoner = {
    attach: function () {
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
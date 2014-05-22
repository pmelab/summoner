/**
 * @file
 * Load javascript, css and library assets on demand.
 */
(function($, Drupal){
  var $summonerAnchor = false;

  var summonerCallbacks = {};
  Drupal.settings.summonerState = Drupal.settings.summonerState || {};

  Drupal.summon = function (libraries, callback) {
    libraries = $.isArray(libraries) ? libraries : [libraries];
    var toLoad = [];
    $.each(libraries, function (index, lib){
      if (!Drupal.settings.summonerState[lib]) {
        toLoad.push(lib);
      }
    });
    if (toLoad.length > 0) {
      toLoad.sort();
      var id = toLoad.join(',');
      if (!summonerCallbacks[id]) {
        summonerCallbacks[id] = [];
        var url = Drupal.settings.basePath + 'summoner/load/' + id.replace('/', '::');
        var $link = $('<a data-id="summoner-link-' + id + '" href="' + url + '" class="use-ajax"/>');
        $link.appendTo($summonerAnchor);
        Drupal.behaviors.AJAX.attach($summonerAnchor, Drupal.settings);
        $link.click();
      }
      summonerCallbacks[id].push(callback);
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

  $.fn.summonerLoaded = function(id, hash) {
    $('[data-id="summoner-link-' + id +'"]"').remove();
    if (summonerCallbacks[id]) {
      $.each(summonerCallbacks[id], function(index, callback) {
        callback();
      });
    }
    Drupal.behaviors['summonerLoaded' + hash] = null;
  };

}(jQuery, Drupal));
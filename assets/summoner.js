/**
 * @file
 * Load javascript, css and library assets on demand.
 */
(function($, Drupal){

  var $summonerAnchor = false;
  var summonerCallbacks = {};

  Drupal.settings.summonerState = Drupal.settings.summonerState || {};
  Drupal.summoner = {};


  Drupal.behaviors.summoner = {
    attach: function () {
      if (!$summonerAnchor) {
        $summonerAnchor = $('<div style="display: none" id="summoner-anchor"/>');
        $summonerAnchor.appendTo('body');
      }
    }
  };

  Drupal.summoner.attachBehavior = function (libraries) {
    Drupal.behaviors['summonerLoad-' + libraries] = {
      attach: function () {
        $('a[data-libraries="' + libraries +'"]"').remove();
        if (summonerCallbacks[libraries]) {
          $.each(summonerCallbacks[libraries], function(index, callback) {
            callback();
          });
        }
        Drupal.behaviors['summonerLoad-' + libraries] = null;
      }
    };
  };

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
        var $link = $('<a data-libraries="' + id + '" href="' + url + '" class="use-ajax"/>');
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

}(jQuery, Drupal));
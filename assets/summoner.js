/**
 * @file
 * Load javascript, css and library assets on demand.
 */
(function($, Drupal){
  var summonerCallbacks = {};

  Drupal.settings.summonerState = Drupal.settings.summonerState || {};

  Drupal.summonerAttachBehavior = function (libraries) {
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
      var libs = toLoad.join(',');
      if (!summonerCallbacks[libs]) {
        summonerCallbacks[libs] = [];
        var url = Drupal.settings.basePath + 'summoner/load/' + libs.replace('/', '::');
        var element = $('body');
        var ajax = new Drupal.ajax(url, element, { url: url, event: 'mousedown' });
        ajax.beforeSerialize(ajax.element, ajax.options);
        $.ajax(ajax.options);
      }
      summonerCallbacks[libs].push(callback);
    }
    else {
      callback();
    }
  };

}(jQuery, Drupal));
/**
 * @file
 * Load javascript, css and library assets on demand.
 */
(function($, Drupal){
  // Common asset url patterns.
  var isAbsoluteUrl = /^([a-z]+:)?\/\/?/;
  var isAsset = /\.(js|css)$/;
  var isJS = /\.js$/;
  var isCSS = /\.css$/;

  var summonRecursive = function (assets, callback) {
    assets = $.isArray(assets) ? assets : [assets];
    if (assets.length == 0) {
      callback(assets, callback);
    }
    var asset = assets.shift();
    var cb = assets.length ? Drupal.summon : callback;

    // Make asset objects of string assets.
    if (typeof asset !== 'object') {
      asset = { data: asset, uri: asset };
      if (isCSS.test(asset.data)) {
        asset.type = 'css';
      }
      else if (isJS.test(asset.data)) {
        asset.type = 'js';
      }
      else {
        asset.type = 'library';
      }
    }

    // Make relative urls absolute.
    if (isAsset.test(asset.data) && !isAbsoluteUrl.test(asset.data)) {
      asset.data = Drupal.settings.basePath + asset.data;
    }

    if (asset.uri == 'inline') {
      if (asset.type === 'css') {
        $('head').append($('<style/>').text(asset.data));
      }
      else if (asset.type === 'js') {
        $.globalEval(asset.data);
      }
      cb(assets, callback);
      return;
    }

    // Load javascript assets with jquery $.getScript()
    if (asset.type === 'js') {
      if (Drupal.settings.ajaxPageState.js[asset.uri]) {
        cb(assets, callback);
        return;
      }
      $.getScript(asset.data, function(){
        var uri = $.isArray(asset.uri) ? asset.uri : [asset.uri];
        $.each(uri, function(index, uri){
          Drupal.settings.ajaxPageState.js[uri] = 1;
        });
        cb(assets, callback);
      });
    }

    // Load css by adding a link tag to the header.
    else if (asset.type === 'css') {
      if (!Drupal.settings.ajaxPageState.css[asset.uri]) {
        $('head').append( $('<link rel="stylesheet" type="text/css" />').attr('href', asset.data));
        var uri = $.isArray(asset.uri) ? asset.uri : [asset.uri];
        $.each(uri, function(index, uri){
          Drupal.settings.ajaxPageState.js[uri] = 1;
        });
      }
      cb(assets, callback);
    }

    // Everything else is probably a library. Get information from the system,
    // fill up assets and step into recursion.
    else if (asset.type === 'library') {
      var path = Drupal.settings.basePath + 'summoner/fetch/' + asset.data;
      $.ajax({
        url: path,
        dataType: 'json',
        success: function(data, status, request) {
          data.assets.reverse();
          $.each(data.assets, function(index, asset){
            assets.unshift(asset);
          });

          $.extend(true, Drupal.settings, data.settings);
          Drupal.summon(assets, callback);
        }
      });
    }
  };

  Drupal.summon = function (assets, callback) {
    summonRecursive(assets, function() {
      callback(Drupal.settings);
    });
  };

}(jQuery, Drupal));
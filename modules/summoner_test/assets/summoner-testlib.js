(function($, Drupal) {
  window.alert('test');
  Drupal.behaviors.summoner_testlib = {
    attach: function (context, settings) {
      window.alert('attach!');
    }
  };
}(jQuery, Drupal));
(function ($, Drupal) {
  Drupal.behaviors.summoner_test = {
    attach: function (context, settings) {
      var $link = $('#summoner-test-link', context);
      var $button = $('#summoner-test-button', context);
      $link.click(function(event) {
        event.preventDefault();
        Drupal.summon('summoner_test:summoner.testlib', function() {
          window.alert('loaded!');
        });
      });
    }
  };
}(jQuery, Drupal));
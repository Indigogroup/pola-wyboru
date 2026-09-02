/**
 * Pola Wyboru - Public JavaScript
 */

(function($) {
  'use strict';

  $(document).ready(function() {
    var $form = $('#pola_wyboru_form');
    var $heelSelector = $('.pola-wyboru-heel-height');
    var $customColorimetryToggle = $('.pola-wyboru-custom-colorimetry-toggle');
    var $customSizeToggle = $('.pola-wyboru-custom-size-toggle');

    // Handle heel height change
    $heelSelector.on('change', function() {
      var heelHeight = $(this).val();
      var productId = $('[name="pola_wyboru_product_id"]').val();
      var configuration = getFormConfiguration();

      if (!heelHeight) {
        return;
      }

      // Save configuration and redirect
      var data = {
        action: 'pola_wyboru_change_heel_height',
        nonce: pola_wyboru_ajax.nonce,
        product_id: productId,
        heel_height: heelHeight,
        configuration: configuration
      };

      $.post(pola_wyboru_ajax.ajax_url, data, function(response) {
        if (response.success) {
          // Redirect to new product page
          window.location.href = response.data.product_url;
        } else {
          alert(response.data);
        }
      });
    });

    // Handle custom colorimetry toggle
    $customColorimetryToggle.on('change', function() {
      var $field = $(this).closest('.pola-wyboru-custom-colorimetry').find('.pola-wyboru-custom-colorimetry-field');
      if ($(this).is(':checked')) {
        $field.slideDown();
      } else {
        $field.slideUp();
      }
    });

    // Handle custom size toggle
    $customSizeToggle.on('change', function() {
      var $field = $(this).closest('.pola-wyboru-custom-size').find('.pola-wyboru-custom-size-field');
      if ($(this).is(':checked')) {
        $field.slideDown();
      } else {
        $field.slideUp();
      }
    });

    // Get current form configuration
    function getFormConfiguration() {
      var config = {};
      var $inputs = $form.find('input, select, textarea');

      $inputs.each(function() {
        var $input = $(this);
        var name = $input.attr('name');

        if (!name || name === 'pola_wyboru_nonce' || name === 'pola_wyboru_product_id') {
          return;
        }

        // Extract key from pola_wyboru_config[key]
        var match = name.match(/pola_wyboru_config\[(.+)\]/);
        if (match) {
          var key = match[1];

          if ($input.is(':checkbox')) {
            config[key] = $input.is(':checked') ? 1 : 0;
          } else if ($input.is(':radio')) {
            if ($input.is(':checked')) {
              config[key] = $input.val();
            }
          } else {
            config[key] = $input.val();
          }
        }
      });

      return config;
    }

    // Restore form state on page load (from session)
    function restoreFormState() {
      // This could be enhanced with data attributes or hidden inputs
      // containing the saved configuration from the session
    }

    restoreFormState();
  });
})(jQuery);

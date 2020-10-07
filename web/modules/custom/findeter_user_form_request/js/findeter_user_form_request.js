(function ($, Drupal) {
  //'use strict';
  Drupal.behaviors.findeterUserFormRequest = {
    attach: function (context, settings) {
      
      if($('#edit-field-request-description').length){
        $("#edit-field-request-description").keyup(function() {
          var sizeText = $('#edit-field-request-description').val().length;
          $('.counter-char').html(sizeText);
        });
      }

      if($('#field-contact-answer-channel-anonimous').length){
        $('#no-anonimous').hide();

        $('.form-item-field-contact-answer-channel-anonimous label').click(function(){
          $(this).parent().find('input').prop("checked", true);
          if($(this).parent().find('input').attr('value')=='0'){
            $('#no-anonimous').show();
          }else{
            $('#no-anonimous').hide();
          }
        });

        $('input[type=radio][name=field_contact_answer_channel_anonimous]').change(function() {
          if (this.value == '1') {
            $('#no-anonimous').hide();
          }
          else if (this.value == '0') {
            $('#no-anonimous').show();
          }
        });

      }

      $.fn.afterAsignCallback = function(argument) {
        console.log('afterAsignCallback is called.');
        location.reload();
      };

      //var abc = $('#edit-field-age-range').selectToUISlider().next();

      /*$(".htc-ajax-form").on("click",function(){

        var ajaxSettings = {
          url: Drupal.url("htc-ajax-response"),
          dialogType: 'ajax',
        };

        var myAjaxObject = Drupal.ajax(ajaxSettings);

        // Declare a new Ajax command specifically for this Ajax object.
        myAjaxObject.commands.insert = function (ajax, response, status) {

          console.info('response', response);
          $("#htcp-wrapper").html(response.data);
          Drupal.attachBehaviors(document, Drupal.settings);

        };

        // Programmatically trigger the Ajax request.
        myAjaxObject.execute();

      });*/


    }
  };
})(jQuery, Drupal);

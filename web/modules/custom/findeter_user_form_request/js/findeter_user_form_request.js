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

        if($('input[type=radio][name=field_contact_answer_channel_anonimous]:checked').val() == '0'){
          $('#no-anonimous').show();
        }

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

    }
  };
})(jQuery, Drupal);

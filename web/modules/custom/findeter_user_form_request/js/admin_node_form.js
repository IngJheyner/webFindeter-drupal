(function ($, Drupal) {
  //'use strict';
  Drupal.behaviors.findeterUserFormRequest = {
    attach: function (context, settings) {

      // click inside modal link name, close the modal and fill the text input
      $('.user-asign-link').click(function(){
        $('#edit-field-asign').val($(this).attr('rel'));
        $('.ui-dialog-titlebar-close').click();
        return false;
      });

      // hide all elemnts at the begining of the form
      $('#edit-info-person').hide();
      $('.form-item-field-type-request').hide();
      $('.form-item-field-legal-nit').hide();
      $('.form-item-field-legal-business-name').hide();
      $('.form-item-field-legal-type-business').hide();

      // adding class required fields
      $('.form-item-field-type-request label').addClass('js-form-required form-required');
      $('.form-item-field-person-number-id label').addClass('js-form-required form-required');
      $('.form-item-field-person-type-id label').addClass('js-form-required form-required');
      $('.form-item-field-person-first-name label').addClass('js-form-required form-required');
      $('.form-item-field-person-first-lastname label').addClass('js-form-required form-required');
      $('.form-item-field-person-address label').addClass('js-form-required form-required');
      $('.form-item-field-person-deparment label').addClass('js-form-required form-required');
      $('.form-item-field-person-municipality label').addClass('js-form-required form-required');
      $('.form-item-field-person-phone-contact label').addClass('js-form-required form-required');
      $('.form-item-field-person-email label').addClass('js-form-required form-required');
      $('.form-item-field-legal-nit label').addClass('js-form-required form-required');
      $('.form-item-field-legal-business-name label').addClass('js-form-required form-required');
      $('.form-item-field-legal-type-business label').addClass('js-form-required form-required');
      
      // show fields when some value is setted
      var typeForm = $( "#edit-field-type-form option:selected" ).text();
      if(typeForm == 'Peticiones'){
        $('.form-item-field-type-request').show();
      }

      var requester = $( "#edit-field-type-requester option:selected" ).text();
      if(requester !== 'anonimo' && requester !== '-Seleccione una opción-'){
        $('#edit-info-person').show();
      }

    
      $('#edit-field-type-form').on('change', function() {
        if(this.value == 'Peticiones'){
          $('.form-item-field-type-request').fadeIn();
        }else{
          $('.form-item-field-type-request').fadeOut();
        }
      });

      

      $('#edit-field-type-requester').on('change', function() {
        if(this.value == 'anonimo'){
          $('#edit-info-person').fadeOut();
        }else{
          $('#edit-info-person').fadeIn();
        }

        if(this.value == 'juridica'){
          $('.form-item-field-legal-nit').fadeIn();
          $('.form-item-field-legal-business-name').fadeIn();
          $('.form-item-field-legal-type-business').fadeIn();
        }else{
          $('.form-item-field-legal-nit').fadeOut();
          $('.form-item-field-legal-business-name').fadeOut();
          $('.form-item-field-legal-type-business').fadeOut();
        }
      });

      // chars counter for description field
      if($('#edit-field-request-description').length){
        $("#edit-field-request-description").keyup(function() {
          var sizeText = $('#edit-field-request-description').val().length;
          $('.counter-char').html(sizeText);
        });
      }

    }
  };
})(jQuery, Drupal);

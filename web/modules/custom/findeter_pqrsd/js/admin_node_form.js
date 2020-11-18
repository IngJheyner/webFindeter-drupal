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
      $('.form-item-field-pqrsd-tipo-peticion').hide();
      $('.form-item-field-pqrsd-nit').hide();
      $('.form-item-field-pqrsd-razon-social').hide();
      $('.form-item-field-pqrsd-tipo-empresa').hide();

      // adding class required fields
      $('.form-item-field-pqrsd-tipo-peticion label').addClass('js-form-required form-required');
      $('.form-item-field-pqrsd-numero-id label').addClass('js-form-required form-required');
      $('.form-item-field-pqrsd-tipo-documento label').addClass('js-form-required form-required');
      $('.form-item-field-pqrsd-primer-nombre label').addClass('js-form-required form-required');
      $('.form-item-field-pqrsd-primer-apellido label').addClass('js-form-required form-required');
      $('.form-item-field-pqrsd-direccion label').addClass('js-form-required form-required');
      $('.form-item-field-pqrsd-departamento label').addClass('js-form-required form-required');
      $('.form-item-field-pqrsd-municipalidadlabel').addClass('js-form-required form-required');
      $('.form-item-field-pqrsd-telefono label').addClass('js-form-required form-required');
      $('.form-item-field-pqrsd-email label').addClass('js-form-required form-required');
      $('.form-item-field-pqrsd-nit label').addClass('js-form-required form-required');
      $('.form-item-field-pqrsd-razon-social label').addClass('js-form-required form-required');
      $('.form-item-field-pqrsd-tipo-empresa label').addClass('js-form-required form-required');
      
      // show fields when some value is setted
                               
      var typeForm = $( "#edit-field-pqrsd-tipo-radicado option:selected" ).text();
      if(typeForm == 'Peticiones'){
        $('.form-item-field-pqrsd-tipo-peticion').show();
      }
                           
      var requester = $( "#edit-field-pqrsd-tipo-solicitante option:selected" ).text();
      if(requester !== 'anonimo' && requester !== '-Seleccione una opción-'){
        $('#edit-info-person').show();
      }

    
      $('#edit-field-pqrsd-tipo-radicado').on('change', function() {
        if(this.value == 'Peticiones'){
          $('.form-item-field-pqrsd-tipo-peticion').fadeIn();
        }else{
          $('.form-item-field-pqrsd-tipo-peticion').fadeOut();
        }
      });

      

      $('#edit-field-pqrsd-tipo-solicitante').on('change', function() {
        if(this.value == 'anonimo'){
          $('#edit-info-person').fadeOut();
        }else{
          $('#edit-info-person').fadeIn();
        }

        if(this.value == 'juridica'){
          $('.form-item-field-pqrsd-nit').fadeIn();
          $('.form-item-field-pqrsd-razon-social').fadeIn();
          $('.form-item-field-pqrsd-tipo-empresa').fadeIn();
        }else{
          $('.form-item-field-pqrsd-nit').fadeOut();
          $('.form-item-field-pqrsd-razon-social').fadeOut();
          $('.form-item-field-pqrsd-tipo-empresa').fadeOut();
        }
      });

      // chars counter for description field
      if($('#edit-field-pqrsd-descripcion').length){
        $("#edit-field-pqrsd-descripcion").keyup(function() {
          var sizeText = $('#edit-field-pqrsd-descripcion').val().length;
          $('.counter-char').html(sizeText);
        });
      }

    }
  };
})(jQuery, Drupal);

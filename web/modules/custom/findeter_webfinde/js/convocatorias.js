/**
 * @file
 * Global utilities.
 *
 */
(function($, Drupal) {

    'use strict';

    Drupal.behaviors.convocatorias = {
        attach: function(context, settings) {

            $(document, context).once('convocatorias').each(function() {

                let codigoProceso = '';

                $("form#node-convocatorias-form input[id='edit-field-convcatoria-archivos-entity-browser-entity-browser-open-modal'], form#node-convocatorias-edit-form input[id='edit-field-convcatoria-archivos-entity-browser-entity-browser-open-modal'] ").on('click', function() {

                    codigoProceso = $("form#node-convocatorias-form input[id='edit-title-0-value'],form#node-convocatorias-edit-form input[id='edit-title-0-value']").val();

                    $(document, context).ajaxStop(function() {

                        let iframe = '';

                        document.querySelector('iframe#entity_browser_iframe_entity_browser_archivos_convocatorias').onload = function(e) {

                            iframe = document.querySelector('iframe#entity_browser_iframe_entity_browser_archivos_convocatorias').contentWindow.document;

                            let files = iframe.getElementById('ief-dropzone-upload');
                            $(files).wrap("<details class='required-fields field-group-details js-form-wrapper form-wrapper seven-details' data-drupal-selector='edit-group-file-conv' id='edit-group-file-conv'><div class='seven-details__wrapper details-wrapper'></div></details>");
                            $(files).parent().before("<summary role='button' aria-controls='edit-group-prueba' aria-expanded='false' aria-pressed='false' class='seven-details__summary form-required'><span>Expandir formularios para los archivos cargados.</span></summary>");

                            iframe = document.querySelector('iframe#entity_browser_iframe_entity_browser_archivos_convocatorias').contentWindow.document;
                            let uploadFile = iframe.getElementById('edit-upload');
                            $(uploadFile).children().children('p').remove();

                            $(uploadFile).on('click', function() {
                                iframe.getElementById('edit-group-file-conv').removeAttribute('open');
                            });

                            iframe.getElementById('edit-group-file-conv').addEventListener('click', function() {
                                let newFiles = iframe.getElementById('ief-dropzone-upload');
                                $(newFiles).children('div.form-wrapper').each(function(idx, el) {
                                    $(el).children('fieldset').children('div.fieldset-wrapper').children('div.field--name-field-convcatoria-ruta-archivo').children().children('input').val(codigoProceso);
                                    $(el).children('fieldset').children('div.fieldset-wrapper').children('div.field--name-field-convcatoria-ruta-archivo').css('display', 'none');
                                });
                            });

                        }
                    })
                });

                let rolAbogado = $("form#node-convocatorias-edit-form").attr('rol');

                if (rolAbogado !== undefined) {
                    $("#edit-field-convcatoria-archivos-current div.form-wrapper input[type='submit']").remove();
                }

            });
        }
    };

})(jQuery, Drupal);
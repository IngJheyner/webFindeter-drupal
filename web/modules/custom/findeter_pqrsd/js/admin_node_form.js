(function($, Drupal) {
  //'use strict';
  Drupal.behaviors.findeterUserFormRequest = {
    attach: function(context, settings) {

      $(document, context).once('findeterUserFormRequest ').each(function() {

          /*===========================================
          Pasar valores
          Dependiendo del tipo de radicado se caragan valores
          SMFC(Queja o Reclamo)
          =============================================*/
          const loadOptionsField = (petition, element) => {

            let item = [];
            let itemSmfc = [];
            $.map(element, function(valueField, nameField) {

                field = document.querySelector(nameField);
                length = field.options.length;
                while(length--){
                  field.remove(length);
                }
                field.appendChild(new Option('-Seleccione una opción-', ''));
                field.removeAttribute('disabled');
                field.classList.remove('form-select');

                $.map(valueField, function(element, index) {
                  if(isNaN(index))
                    item.push({index: index, value: element});
                  else
                    itemSmfc.push({index: index, value: element});
                });
            });

            if(petition){
              $.map(itemSmfc, function(element, index) {
                field.appendChild(new Option(element.value, element.index));
              });

            }else{
              $.map(item, function(element, index) {
                field.appendChild(new Option(element.value, element.index));
              });
            }
          };

          $('#edit-field-pqrsd-tipo-radicado').on('change', function() {

            if (this.value == 'Peticiones') {
              $('.form-item-field-pqrsd-tipo-peticion').fadeIn();
            } else {
              $('.form-item-field-pqrsd-tipo-peticion').fadeOut();
            }

            /* ===== ===== Preguntamos que tipo de peticion, carga de algunos valores para SMFC===== ===== */
            let TypPetition = (this.value == 'Quejas' || this.value == 'Reclamos') ? true : false;

            $.map(settings.optionsFields, function(element, index) {
                loadOptionsField(TypPetition, element);
            });

            if(!TypPetition){
              document.querySelector('#edit-field-pqrsd-nit').removeAttribute('required');
            }else{
              document.querySelector('#edit-field-pqrsd-nit').setAttribute('required', 'required');
            }
          });

          $('#field-pqrsd-municipio-select').on('change', function(){
              console.log($(this).val())
          });

          $.fn.afterLocation = function(argument) {

            if ($('#register-pqrsd-admin').length) {
              //console.log('inside location');

              var typeForm = $('#edit-field-pqrsd-tipo-solicitante option:selected').val();

              //console.log(typeForm, 'option selected');

              if (typeForm == 'juridica' || typeForm == '2') {
                $('.form-item-field-pqrsd-nit').fadeIn();
                $('.form-item-field-pqrsd-razon-social').fadeIn();
                $('.form-item-field-pqrsd-tipo-empresa').fadeIn();
              } else {
                $('.form-item-field-pqrsd-nit').fadeOut();
                $('.form-item-field-pqrsd-razon-social').fadeOut();
                $('.form-item-field-pqrsd-tipo-empresa').fadeOut();
              }
            }
          };

          /*===========================================
          REPORTES
          -Custom a campos para el formato 379
          =============================================*/
          $('#edit-trimestre-inicio').before('<label class="custom-label">Del:</label>');
          $('#edit-trimestre-fin').before('<label class="custom-label">Al:</label>');

          //replace url params to generate pdf file
          if ($('.export-link').length) {
            var queryString = window.location.search;
            queryString = queryString.substring(1);
            if (queryString != '') {
              var link = $('.export-link').attr('href');
              var newLink = link.replace("filter", queryString);
              $('.export-link').attr('href', newLink);
            }
          }

          /*===========================================
          ENVIAR DATOS A LA API SMFC DE LA TABLA DE REPORTES
          =============================================*/
          let sendApiSmfc = document.querySelector('#send-data-smfc');

          if (sendApiSmfc !== null) {

            let fnSendApiSmfc = async (sendApiSmfc) => {

              try {

                const divBarProgress = document.createElement('div');
                divBarProgress.setAttribute('class', 'ajax-progress ajax-progress-throbber');
                divBarProgress.style.display = "none";

                const divProgressThrobber = document.createElement('div');
                divProgressThrobber.setAttribute('class', 'throbber');
                divProgressThrobber.innerHTML = '&nbsp';

                const divProgressMessage = document.createElement('div');
                divProgressMessage.setAttribute('class', 'message');
                divProgressMessage.textContent = 'Espere, por favor...';

                sendApiSmfc.textContent = "";
                divBarProgress.append(divProgressThrobber, divProgressMessage);
                sendApiSmfc.append(divBarProgress);

                divBarProgress.style.display = "block";

                await fetch('/admin/send-data-smfc', {
                  method: "GET",
                }).then(response => {
                  window.location.reload();
                });

              } catch (error) {

                console.log(error);

              }

            };

            sendApiSmfc.addEventListener('click', function(e) {
              e.preventDefault();
              fnSendApiSmfc(sendApiSmfc);
            });
          }

          /*===========================================
          TABLA COMPARATIVA POR ANIO
          - Reporte anio
          =============================================*/
          let tableReportsMonths = document.querySelector('#table-reports-months');

          if (tableReportsMonths !== null) {

            /* ===== ===== SE CREA UN ELEMENTO O NOD DE TIPO SELECT ===== ===== */
            const containerFieldMonths = document.querySelector('#container-text-reports-months');

            // Creamos un formulario para realizar la peticion via get.
            let formReportsAnio = document.createElement("form");
            formReportsAnio.setAttribute('method',"get");

            // Creamos un campo de tipo selector para los anios.
            const selectAnio = document.createElement('select');
            selectAnio.id = 'field-text-reports-months';

            const divBarProgress = document.createElement('div');
            divBarProgress.setAttribute('class', 'ajax-progress ajax-progress-throbber');
            divBarProgress.style.display = "none";

            const divProgressThrobber = document.createElement('div');
            divProgressThrobber.setAttribute('class', 'throbber');
            divProgressThrobber.innerHTML = '&nbsp';

            const divProgressMessage = document.createElement('div');
            divProgressMessage.setAttribute('class', 'message');
            divProgressMessage.textContent = 'Espere, por favor...';

            // Agregamos cada nodo para los elementos creados.
            formReportsAnio.appendChild(selectAnio);

            divBarProgress.append(divProgressThrobber, divProgressMessage);

            formReportsAnio.appendChild(divBarProgress);
            containerFieldMonths.appendChild(formReportsAnio);

            const date = new Date();
            const year = date.getFullYear();

            for(let i = year; i >= 2020; i--) {
              selectAnio.options.add(new Option(i, i));
            }

            let tbodyTableReportsMonths = tableReportsMonths.querySelector('tbody');

            selectAnio.addEventListener('change', function() {
              divBarProgress.style.display = 'block';
              fetch('/admin/reporte-resultados-data-anio/'+selectAnio.value, {
                method: "GET",
              })
              .then(response => {

                if (response.ok){
                  divBarProgress.style.display = 'none';
                  return response.json();
                }
              })
              .then((data) => {
              // console.log(data.data)

                let tbodyHTML = "";

                let datas = data.data;

                for (let clave in datas) {
                  tbodyHTML += '<tr><td>'+datas[clave].name+'</td><td>'+datas[clave].requests+'</td><td>'+datas[clave].attended+'</td><td>'+datas[clave].process+'</td><td>'+datas[clave].canceled+'</td><td>'+datas[clave].days_answer+'</td></tr>';
                }

                tbodyTableReportsMonths.innerHTML = tbodyHTML;

              })
              .catch(function (error) {
                console.error("ERROR SECTORS: ", error.message)
              })
            });

          }


          // // Source of data, see module hook_views_pre_render function
          // if ($('canvas#tipo-radicado-chart').length) {
          //     colorArray = colorize(settings.pqrsdReports.tipoRadicado);
          //     new Chart($('#time-chart'), {
          //         type: 'bar',
          //         data: {
          //             labels: $.map(settings.pqrsdReports.monthly, function(element, index) { return index }),
          //             datasets: [{
          //                 label: '',
          //                 data: $.map(settings.pqrsdReports.monthly, function(element, index) { return element }),
          //                 backgroundColor: colorArray,
          //                 borderColor: colorArray
          //             }]
          //         },
          //         options: {
          //             title: {
          //                 display: true,
          //                 text: 'Registro mensual de radicados'
          //             }
          //         }
          //     });

          //     colorArray = colorize(settings.pqrsdReports.tipoRadicado);
          //     new Chart($('#tipo-radicado-chart'), {
          //         type: 'pie',
          //         data: {
          //             labels: $.map(settings.pqrsdReports.tipoRadicado, function(element, index) { return index }),
          //             datasets: [{
          //                 data: $.map(settings.pqrsdReports.tipoRadicado, function(element, index) { return element }),
          //                 backgroundColor: colorArray,
          //                 borderColor: colorArray
          //             }]
          //         },
          //         options: {
          //             title: {
          //                 display: true,
          //                 text: 'Tipo de radicado'
          //             }
          //         }
          //     });

          //     colorArray = colorize(settings.pqrsdReports.tipoSolicitante);
          //     new Chart($('#tipo-solicitante-chart'), {
          //         type: 'pie',
          //         data: {
          //             labels: $.map(settings.pqrsdReports.tipoSolicitante, function(element, index) { return index }),
          //             datasets: [{
          //                 data: $.map(settings.pqrsdReports.tipoSolicitante, function(element, index) { return element }),
          //                 backgroundColor: colorArray,
          //                 borderColor: colorArray
          //             }]
          //         },
          //         options: {
          //             title: {
          //                 display: true,
          //                 text: 'Tipo solicitante'
          //             }
          //         }
          //     });

          //     colorArray = colorize(settings.pqrsdReports.tipoProducto);
          //     new Chart($('#tipo-producto-chart'), {
          //         type: 'pie',
          //         data: {
          //             labels: $.map(settings.pqrsdReports.tipoProducto, function(element, index) { return index }),
          //             datasets: [{
          //                 data: $.map(settings.pqrsdReports.tipoProducto, function(element, index) { return element }),
          //                 backgroundColor: colorArray,
          //                 borderColor: colorArray
          //             }]
          //         },
          //         options: {
          //             title: {
          //                 display: true,
          //                 text: 'Tipo producto'
          //             }
          //         }
          //     });

          //     colorArray = colorize(settings.pqrsdReports.canalRecepcion);
          //     new Chart($('#canal-recepcion-chart'), {
          //         type: 'pie',
          //         data: {
          //             labels: $.map(settings.pqrsdReports.canalRecepcion, function(element, index) { return index }),
          //             datasets: [{
          //                 data: $.map(settings.pqrsdReports.canalRecepcion, function(element, index) { return element }),
          //                 backgroundColor: colorArray,
          //                 borderColor: colorArray
          //             }]
          //         },
          //         options: {
          //             title: {
          //                 display: true,
          //                 text: 'Canal de recepción'
          //             }
          //         }
          //     });

          //     colorArray = colorize(settings.pqrsdReports.canalRecepcion);
          //     new Chart($('#forma-recepcion-chart'), {
          //         type: 'pie',
          //         data: {
          //             labels: $.map(settings.pqrsdReports.formaRecepcion, function(element, index) { return index }),
          //             datasets: [{
          //                 data: $.map(settings.pqrsdReports.formaRecepcion, function(element, index) { return element }),
          //                 backgroundColor: colorArray,
          //                 borderColor: colorArray
          //             }]
          //         },
          //         options: {
          //             title: {
          //                 display: true,
          //                 text: 'Forma de recepción'
          //             }
          //         }
          //     });

          // }
      });

      /*===========================================
      Se crea un campo pilot para obtener valores de
      municipio y este psarlo al campo hidden para ser guardado
      =============================================*/
      $('#field-pqrsd-municipio-select').on('change', function(){
          $('#field-pqrsd-municipio').val(($(this).val()));
      });

      // click inside modal link name, close the modal and fill the text input
      $('.user-asign-link').click(function() {
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
      $('.form-item-field-pqrsd-municipalidad label').addClass('js-form-required form-required');
      $('.form-item-field-pqrsd-telefono label').addClass('js-form-required form-required');
      $('.form-item-field-pqrsd-email label').addClass('js-form-required form-required');
      $('.form-item-field-pqrsd-nit label').addClass('js-form-required form-required');
      $('.form-item-field-pqrsd-razon-social label').addClass('js-form-required form-required');
      $('.form-item-field-pqrsd-tipo-empresa label').addClass('js-form-required form-required');

      // show fields when some value is setted
      var typeForm = $("#edit-field-pqrsd-tipo-radicado option:selected").text();
      if (typeForm == 'Peticiones') {
        $('.form-item-field-pqrsd-tipo-peticion').show();
      }

      var requester = $("#edit-field-pqrsd-tipo-solicitante option:selected").text();
      if (requester !== 'Anónimo' && requester !== '-Seleccione una opción-') {
        $('#edit-info-person').show();
      } else {
        $('#edit-info-person').hide();
      }

      $("#edit-field-pqrsd-autorizacion").prop("checked", true);

      $('#edit-field-pqrsd-tipo-solicitante').on('change', function() {
        if (this.value == 'anonimo') {
          $('#edit-info-person').fadeOut();
        } else {
          $('#edit-info-person').fadeIn();
        }

        if (this.value == 'juridica' || this.value == '2') {
          $('.form-item-field-pqrsd-nit').fadeIn();
          $('.form-item-field-pqrsd-razon-social').fadeIn();
          $('.form-item-field-pqrsd-tipo-empresa').fadeIn();
          document.querySelector('#edit-field-pqrsd-nit').setAttribute('required', 'required');
        } else {
          $('.form-item-field-pqrsd-nit').fadeOut();
          $('.form-item-field-pqrsd-razon-social').fadeOut();
          $('.form-item-field-pqrsd-tipo-empresa').fadeOut();
          document.querySelector('#edit-field-pqrsd-nit').removeAttribute('required');
        }
      });

      // chars counter for description field
      if ($('#edit-field-pqrsd-descripcion').length) {
        $("#edit-field-pqrsd-descripcion").keyup(function() {
          var sizeText = $('#edit-field-pqrsd-descripcion').val().length;
          $('.counter-char').html(sizeText);
        });
      }

      // function colorize(data) {
      //     var opacity = '0.8';
      //     var result = [];
      //     let colorList = [];
      //     colorList[0] = 'rgba(249, 65, 68, ' + opacity + ')',
      //         colorList[1] = 'rgba(243, 114, 44, ' + opacity + ')',
      //         colorList[2] = 'rgba(248, 150, 30, ' + opacity + ')',
      //         colorList[3] = 'rgba(249, 132, 74, ' + opacity + ')',
      //         colorList[4] = 'rgba(249, 199, 79s, ' + opacity + ')',
      //         colorList[5] = 'rgba(144, 190, 109, ' + opacity + ')',
      //         colorList[6] = 'rgba(67, 170, 139, ' + opacity + ')',
      //         colorList[7] = 'rgba(77, 144, 142, ' + opacity + ')',
      //         colorList[8] = 'rgba(87, 117, 144, ' + opacity + ')',
      //         colorList[9] = 'rgba(39, 125, 161, ' + opacity + ')',
      //         colorList[10] = 'rgba(153, 5, 7, ' + opacity + ')',
      //         colorList[11] = 'rgba(135, 52, 8, ' + opacity + ')',
      //         colorList[12] = 'rgba(136, 77, 4, ' + opacity + ')',
      //         colorList[13] = 'rgba(156, 55, 5, ' + opacity + ')',
      //         colorList[14] = 'rgba(157, 112, 6, ' + opacity + ')',
      //         colorList[15] = 'rgba(71, 104, 47, ' + opacity + ')',
      //         colorList[16] = 'rgba(33, 84, 69, ' + opacity + ')',
      //         colorList[17] = 'rgba(38, 71, 70, ' + opacity + ')',
      //         colorList[18] = 'rgba(43, 58, 72, ' + opacity + ')',
      //         colorList[19] = 'rgba(19, 62, 80, ' + opacity + ')',
      //         colorList[20] = 'rgba(252, 163, 164, ' + opacity + ')',
      //         colorList[21] = 'rgba(249, 185, 151, ' + opacity + ')',
      //         colorList[22] = 'rgba(252, 204, 146, ' + opacity + ')',
      //         colorList[23] = 'rgba(252, 194, 165, ' + opacity + ')',
      //         colorList[24] = 'rgba(252, 227, 168, ' + opacity + ')',
      //         colorList[25] = 'rgba(201, 223, 184, ' + opacity + ')',
      //         colorList[26] = 'rgba(159, 217, 199, ' + opacity + ')',
      //         colorList[27] = 'rgba(162, 205, 204, ' + opacity + ')',
      //         colorList[28] = 'rgba(169, 187, 203, ' + opacity + ')',
      //         colorList[29] = 'rgba(132, 197, 225, ' + opacity + ')'

      //     $.map(data, function(element, index) {
      //         var index = Math.floor(Math.random() * colorList.length);
      //         result.push(colorList[index]);
      //         delete colorList[index];
      //     })
      //     return result;
      // }

    }
  };
})(jQuery, Drupal);
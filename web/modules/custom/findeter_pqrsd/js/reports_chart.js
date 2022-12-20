(function($, Drupal) {
  //'use strict';
  Drupal.behaviors.reports_chart = {
    attach: function(context, settings) {

      // Funcion que da color a cada uno de los items de la graficas
      function colorize(data) {

        var opacity = '0.8';
        var result = [];
        let colorList = [];
        colorList[0] = 'rgba(249, 65, 68, ' + opacity + ')',
          colorList[1] = 'rgba(243, 114, 44, ' + opacity + ')',
          colorList[2] = 'rgba(248, 150, 30, ' + opacity + ')',
          colorList[3] = 'rgba(249, 132, 74, ' + opacity + ')',
          colorList[4] = 'rgba(249, 199, 79s, ' + opacity + ')',
          colorList[5] = 'rgba(144, 190, 109, ' + opacity + ')',
          colorList[6] = 'rgba(67, 170, 139, ' + opacity + ')',
          colorList[7] = 'rgba(77, 144, 142, ' + opacity + ')',
          colorList[8] = 'rgba(87, 117, 144, ' + opacity + ')',
          colorList[9] = 'rgba(39, 125, 161, ' + opacity + ')',
          colorList[10] = 'rgba(153, 5, 7, ' + opacity + ')',
          colorList[11] = 'rgba(135, 52, 8, ' + opacity + ')',
          colorList[12] = 'rgba(136, 77, 4, ' + opacity + ')',
          colorList[13] = 'rgba(156, 55, 5, ' + opacity + ')',
          colorList[14] = 'rgba(157, 112, 6, ' + opacity + ')',
          colorList[15] = 'rgba(71, 104, 47, ' + opacity + ')',
          colorList[16] = 'rgba(33, 84, 69, ' + opacity + ')',
          colorList[17] = 'rgba(38, 71, 70, ' + opacity + ')',
          colorList[18] = 'rgba(43, 58, 72, ' + opacity + ')',
          colorList[19] = 'rgba(19, 62, 80, ' + opacity + ')',
          colorList[20] = 'rgba(252, 163, 164, ' + opacity + ')',
          colorList[21] = 'rgba(249, 185, 151, ' + opacity + ')',
          colorList[22] = 'rgba(252, 204, 146, ' + opacity + ')',
          colorList[23] = 'rgba(252, 194, 165, ' + opacity + ')',
          colorList[24] = 'rgba(252, 227, 168, ' + opacity + ')',
          colorList[25] = 'rgba(201, 223, 184, ' + opacity + ')',
          colorList[26] = 'rgba(159, 217, 199, ' + opacity + ')',
          colorList[27] = 'rgba(162, 205, 204, ' + opacity + ')',
          colorList[28] = 'rgba(169, 187, 203, ' + opacity + ')',
          colorList[29] = 'rgba(132, 197, 225, ' + opacity + ')'

        $.map(data, function(element, index) {
            var index = Math.floor(Math.random() * colorList.length);
            result.push(colorList[index]);
            delete colorList[index];
        })
        return result;
      }

      // Funcion que crea las graficas
      const chartsGraphics = () => {

        if ($('canvas#tipo-radicado-chart').length) {

          // HTML para el tbody de cada una de las tablas
          let tableTbodyHtml = "";

          /* ===== ===== Registro mensual de radicado ===== ===== */
          const tableTimeChart = document.querySelector("table#time-chart");
          colorArray = colorize(settings.pqrsdReports.monthly);
          $.map(settings.pqrsdReports.monthly, function(element, index) {
            tableTbodyHtml += "<tr><td>"+index+"</td><td>"+element+"</td></tr>";
          });
          new Chart($('canvas#time-chart'), {
            type: 'bar',
            data: {
              labels: $.map(settings.pqrsdReports.monthly, function(element, index) { return index }),
              datasets: [{
                label: '',
                data: $.map(settings.pqrsdReports.monthly, function(element, index) { return element }),
                backgroundColor: colorArray,
                borderColor: colorArray
              }]
            },
            options: {
              title: {
                display: true,
                text: 'Registro mensual de radicados'
              }
            }
          });
          tableTimeChart.querySelector("tbody").innerHTML = tableTbodyHtml;
          tableTbodyHtml = "";

          /* ===== ===== Tipo de radicado ===== ===== */
          const tableTypeRad = document.querySelector("table#tipo-radicado-chart");
          $.map(settings.pqrsdReports.tipoRadicado, function(element, index) {
            tableTbodyHtml += "<tr><td>"+index+"</td><td>"+element+"</td></tr>";
          });
          colorArray = colorize(settings.pqrsdReports.tipoRadicado);
          new Chart($('canvas#tipo-radicado-chart'), {
              type: 'pie',
              data: {
                  labels: $.map(settings.pqrsdReports.tipoRadicado, function(element, index) { return index }),
                  datasets: [{
                      data: $.map(settings.pqrsdReports.tipoRadicado, function(element, index) { return element }),
                      backgroundColor: colorArray,
                      borderColor: colorArray
                  }]
              },
              options: {
                  title: {
                      display: true,
                      text: 'Tipo de radicado'
                  }
              }
          });
          tableTypeRad.querySelector("tbody").innerHTML = tableTbodyHtml;
          tableTbodyHtml = "";

          /* ===== ===== Tipo de solicitante ===== ===== */
          const tableTypeSol = document.querySelector("table#tipo-solicitante-chart");
          $.map(settings.pqrsdReports.tipoSolicitante, function(element, index) {
            tableTbodyHtml += "<tr><td>"+index+"</td><td>"+element+"</td></tr>";
          });
          colorArray = colorize(settings.pqrsdReports.tipoSolicitante);
          new Chart($('#tipo-solicitante-chart'), {
              type: 'pie',
              data: {
                  labels: $.map(settings.pqrsdReports.tipoSolicitante, function(element, index) { return index }),
                  datasets: [{
                      data: $.map(settings.pqrsdReports.tipoSolicitante, function(element, index) { return element }),
                      backgroundColor: colorArray,
                      borderColor: colorArray
                  }]
              },
              options: {
                  title: {
                      display: true,
                      text: 'Tipo solicitante'
                  }
              }
          });
          tableTypeSol.querySelector("tbody").innerHTML = tableTbodyHtml;
          tableTbodyHtml = "";

          /* ===== ===== Tipo de producto ===== ===== */
          const tableTypeProd = document.querySelector("table#tipo-producto-chart");
          $.map(settings.pqrsdReports.tipoProducto, function(element, index) {
            tableTbodyHtml += "<tr><td>"+index+"</td><td>"+element+"</td></tr>";
          });
          colorArray = colorize(settings.pqrsdReports.tipoProducto);
          new Chart($('canvas#tipo-producto-chart'), {
              type: 'pie',
              data: {
                  labels: $.map(settings.pqrsdReports.tipoProducto, function(element, index) { return index }),
                  datasets: [{
                      data: $.map(settings.pqrsdReports.tipoProducto, function(element, index) { return element }),
                      backgroundColor: colorArray,
                      borderColor: colorArray
                  }]
              },
              options: {
                  title: {
                      display: true,
                      text: 'Tipo producto'
                  }
              }
          });
          tableTypeProd.querySelector("tbody").innerHTML = tableTbodyHtml;
          tableTbodyHtml = "";

          /* ===== ===== Canal de recepcion ===== ===== */
          const CanalRecep = document.querySelector("table#canal-recepcion-chart");
          $.map(settings.pqrsdReports.canalRecepcion, function(element, index) {
            tableTbodyHtml += "<tr><td>"+index+"</td><td>"+element+"</td></tr>";
          });
          colorArray = colorize(settings.pqrsdReports.canalRecepcion);
          new Chart($('#canal-recepcion-chart'), {
              type: 'pie',
              data: {
                  labels: $.map(settings.pqrsdReports.canalRecepcion, function(element, index) { return index }),
                  datasets: [{
                      data: $.map(settings.pqrsdReports.canalRecepcion, function(element, index) { return element }),
                      backgroundColor: colorArray,
                      borderColor: colorArray
                  }]
              },
              options: {
                  title: {
                      display: true,
                      text: 'Canal de recepción'
                  }
              }
          });
          CanalRecep.querySelector("tbody").innerHTML = tableTbodyHtml;
          tableTbodyHtml = "";

          /* ===== ===== Forma de recepcion ===== ===== */
          const FormRecep = document.querySelector("table#forma-recepcion-chart");
          $.map(settings.pqrsdReports.formaRecepcion, function(element, index) {
            tableTbodyHtml += "<tr><td>"+index+"</td><td>"+element+"</td></tr>";
          });
          colorArray = colorize(settings.pqrsdReports.formaRecepcion);
          new Chart($('#forma-recepcion-chart'), {
              type: 'pie',
              data: {
                  labels: $.map(settings.pqrsdReports.formaRecepcion, function(element, index) { return index }),
                  datasets: [{
                      data: $.map(settings.pqrsdReports.formaRecepcion, function(element, index) { return element }),
                      backgroundColor: colorArray,
                      borderColor: colorArray
                  }]
              },
              options: {
                  title: {
                      display: true,
                      text: 'Forma de recepción'
                  }
              }
          });
          FormRecep.querySelector("tbody").innerHTML = tableTbodyHtml;
          tableTbodyHtml = "";
        }
      }

      // Funcion para construir cada contenedor de tipo de producto
      let chartContainer = [];
      const createdDivChartContainer = (tableHtml, iterar) => {

        const tipProduct = document.querySelector("div#tipo-producto-chart");
        let table = "";

        let chart_container = document.createElement("div");
        chart_container.className = "chart-container chart-container-product-"+ iterar;
        chartContainer.push('chart-container-product-'+ iterar);

        table = "<table id='tipo-solicitante-chart'><thead><tr><th>Tipo de Producto</th><th>Total</th></tr></thead><tbody>";
        table += tableHtml;
        table += "</tbody></table>";
        chart_container.innerHTML = table;
        tipProduct.insertAdjacentElement("afterend", chart_container);
      }

      // Funcion de generacion de PDF estadisticas
      const generatePdf = async () => {

        // Se inicia la nueva variable de jspdf y html2canvas
        const { jsPDF } = window.jspdf;

        // Seleccion de la lista de charts que representas las graficas
        const listChart = document.querySelector('div.list-charts');

        // Modificamos el objeto de tipo producto
        let tipProductsArray = settings.pqrsdReports.tipoProducto;
        tipProductsArray = Object.entries(tipProductsArray);

        if (tipProductsArray.length > 12 ) {

          let iterar = 0;
          let tableHtml = "";
          const tipProductTable = document.querySelector("div#tipo-producto-chart table");

          // Iteramos para construir las diferentes paginas de un maximo de 12 item por pagina para generar el pdf.
          // Este item es unico ya que existen mas de 20 tipos de producto en el sistema
          $.map(tipProductsArray.reverse(), function(element) {

            tableHtml += "<tr><td>"+element[0]+"</td><td>"+element[1]+"</td></tr>";

            if ( iterar % 12 == 0) {

              if (iterar === 12) {

                tipProductTable.querySelector('tbody tr').remove();
                tipProductTable.querySelector('tbody').innerHTML = tableHtml;
                tableHtml = "";

              } else {

                createdDivChartContainer(tableHtml, iterar);
                tableHtml = "";

              }

            }

            iterar++;

          });

        }

        const listChartContainer = document.querySelectorAll('div.list-charts div.chart-container');

        let doc = new jsPDF({
          orientation: 'p',
          unit: 'px',
          format: 'letter',
          hotfixes: ['px_scaling'],
        });

        // Se crea una imagen de los graficos y se agrega al PDF
        for(let i = 0; i < listChartContainer.length; i++) {

          await html2canvas(listChartContainer[i], {
            width: doc.internal.pageSize.getWidth(),
            height: doc.internal.pageSize.getHeight(),
            scale: 1,
            //x: 100,
          })
          .then((canvas) => {
            const img = canvas.toDataURL("image/jpeg");
            // doc.addImage(img, "PNG", 140, 10, doc.internal.pageSize.getWidth(), doc.internal.pageSize.getHeight());
            doc.addImage(img, "JPEG", 30, 93, doc.internal.pageSize.getWidth(), doc.internal.pageSize.getHeight());
            // console.log(doc.internal.pageSize.getWidth());
            //doc.save("p&lstatement.pdf");
            if ((listChartContainer.length - 1) != i) {
              doc.addPage();
            }
          });

        }

        // Eliminamos chartContainer de productos si se ha crea el nodo.
        if (tipProductsArray.length > 12 ) {
          chartContainer.forEach(element => {
            document.querySelector('div.'+element).remove();
          });
        }

        // Se configura para dar un paginador dinamico al pdf
        let pageCount = doc.internal.getNumberOfPages();
        const timePeriod = document.querySelector('time#time-period');

        for (let i = 0; i < pageCount; i++) {

          doc.setPage(i);
          const pageSize = doc.internal.pageSize;
          const pageWidth = pageSize.width ? pageSize.width : pageSize.getWidth();
          const pageHeight = pageSize.height ? pageSize.height : pageSize.getHeight();
          const header = 'Gráfica de estadisticas PQRSD';
          const footer = `Página ${i} de ${pageCount}`;

          doc.setFontSize(12);

          // Header
          let logo = new Image();
          logo.src = '/themes/custom/webfinde/images/iconos/findeter.png';
          doc.addImage(logo, 'PNG', 600, 12, 150, 58, { baseline: 'top' });

          doc.text(header, 40, 23, { baseline: 'top' });
          doc.text(timePeriod.innerHTML, 38, 47, { baseline: 'top'} );

          // Footer
          doc.text(footer, pageWidth / 2 - (doc.getTextWidth(footer) / 2), pageHeight - 15, { baseline: 'bottom' });
        }

        return doc.save("reporteGraficasestadisticas-pqrsd.pdf");

      }

      $(document, context).once('#findeter-pqrsd-filter-statistics').each(function() {

        chartsGraphics();
        const exportLinkPdf = document.querySelector('a#send-pdf');

        // Evento que genera para el PDF
        exportLinkPdf.addEventListener('click', (e) => {

          e.preventDefault();

          // Esperamos que resuelva la promesa para descargar el PDF.
          document.querySelector('div.ajax-progress-throbber').style.display = "block";

          generatePdf().then(value => {
            document.querySelector('div.ajax-progress-throbber').style.display = "none";
            chartsGraphics(); // Actualizamos las graficas
          });

        });

      });
    }
  };
})(jQuery, Drupal);
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>service contratacion</title>
    <link rel="stylesheet" href="../../bootstrap/bootstrap-4/css/bootstrap.min.css">
    <style>
        .link{
            cursor: pointer;
            color:blue;
            font-weight:500;
        }
    </style>
</head>
<body>
    <div class="container" style="margin-top: 25px;">
        <div class="row" id="convocatorias">
            <div id="respuesta" class="row">
                <div class="table_responsive">
                    <table id="tblProcesos" class="table table-striped table-condensed table-hover tablaGenPagineAjax  dataTable no-footer" role="grid" aria-describedby="tblProcesos_info" style="width: 1140px;">
                        <thead>
                            <tr role="row">
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 25px;">No.</th>
                                <th class="sorting_disabled colModalidad" rowspan="1" colspan="1" style="width: 91px;">Numero de Proceso:</th>
                                <th style="padding: 0px 15px; width: 65px;" class="sorting_disabled" rowspan="1" colspan="1">Estado:</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 59px;">Actividad:</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 690px;">Objeto:</th>
                                <!--th class="sorting_disabled" rowspan="1" colspan="1" style="width: 100px;">Departamento y Municipio de Ejecución:</th-->
                                <th style="" class="" rowspan="1" colspan="1">Cuantía:</th>
                                <th style="" class="" rowspan="1" colspan="1">Fecha de apertura</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyrespuesta">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
		<div class="row" id="detalleconvocatorias" hidden>
            <div class="col-md-12" id="detalleconvo">
                <div class="row col-md-10">
                    <legend style="border-bottom:1px solid black">Información general del proceso</legend>

                    <div class="col-md-12">
                        <label class="form-group col-md-3">Programa : </label>
                        <span class="col-md-9" id="txtprograma"></span>
                    </div>

                    <div class="col-md-12">
                        <label class="form-group col-md-3">Número del proceso : </label>
                        <span class="col-md-9" id="txtnumproc"></span>
                    </div>

                    <div class="col-md-12">
                        <label class="form-group col-md-3">Tipo de proceso : </label>
                        <span class="col-md-9" id="txttipproc"></span>
                    </div>

                    <div class="col-md-12">
                        <label class="form-group col-md-3">Estados del proceso : </label>
                        <span class="col-md-9" id="txtestado"></span>
                    </div>

                    <div class="col-md-12">
                        <label class="form-group col-md-3">Actividad : </label>
                        <span class="col-md-9" id="txtactividad"></span>
                    </div>

                    <div class="col-md-12">
                        <label class="form-group col-md-3">Tipo de contratación : </label>
                        <span class="col-md-9" id="txttipcontr"></span>
                    </div>

                    <div class="col-md-12">
                        <label class="form-group col-md-3">Objeto a contratar : </label>
                        <span class="col-md-9" id="txtobjcontrata"></span>
                    </div>

                    <div class="col-md-12">
                        <label class="form-group col-md-3">Cuantía a contratar : </label>
                        <span class="col-md-9" id="txtcuantia"></span>
                    </div>

                    <div class="col-md-12">
                        <label class="form-group col-md-3">Modalidad : </label>
                        <span class="col-md-9" id="txtmodalidad"></span>
                    </div>
                </div>

                <!--div clas="row col-md-10">
                    <legend>Ubicación geográfica del proceso</legend>
                    <div class="col-md-12">
                        <label class="form-group col-md-3">Departamento(s) : </label>
                        <div class="form-control-md-6" id="txtubicacion"><br></div>
                    </div>
                </div-->

                <div class="row col-md-10">
                    <legend style="border-bottom:1px solid">Datos de contacto del proceso</legend>
                    <div class="col-md-12">
                        <label class="form-group col-md-3">Correo electrónico : </label>
                        <span class="col-md-9" id="txtcorreo"></span>
                    </div>
                </div>


            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="tablaGen table table-striped table-condensed table-hover dataTable no-footer" id="tblDocumentos" role="grid" aria-describedby="tblDocumentos_info">
                        <thead>
                            <tr role="row"><th class="sorting_asc" tabindex="0" aria-controls="tblDocumentos" rowspan="1" colspan="1" aria-sort="ascending" aria-label="No.: Activar para ordenar la columna de manera descendente" style="width: 25px;">No.</th><th class="sorting" tabindex="0" aria-controls="tblDocumentos" rowspan="1" colspan="1" aria-label="Nombre: Activar para ordenar la columna de manera ascendente" style="width: 276px;">Nombre</th><th class="sorting" tabindex="0" aria-controls="tblDocumentos" rowspan="1" colspan="1" aria-label="Descripción: Activar para ordenar la columna de manera ascendente" style="width: 277px;">Descripción</th><th class="sorting" tabindex="0" aria-controls="tblDocumentos" rowspan="1" colspan="1" aria-label="Fecha de Publicación del documento  (aaaa-mm-dd): Activar para ordenar la columna de manera ascendente" style="width: 83px;">Fecha de Publicación del documento <br> (aaaa-mm-dd)</th></tr>
                        </thead>
                        <tbody id="tbodydocume">
                            <!--tr role="row" class="odd">
                                <td class="sorting_1">1</td>
                                <td>
                                    <a href="https://www.findeter.gov.co/loader.php?lServicio=Convocatoria&amp;lTipo=userWS&amp;lFuncion=descargarDoc&amp;NombreDocumento=PAF-ATF-I-065-2020_EP INTERVENTORÍA BIOGAS VF.pdf&amp;numProceso=PAF-ATF-I-065-2020&amp;lista=6" title="Descargar">PAF-ATF-I-065-2020_EP INTERVENTORIÌA BIOGAS VF.pdf</a>
                                </td>
                                <td>PAF-ATF-I-065-2020_EP INTERVENTORIÌA BIOGAS VF.pdf</td>
                                <td class="centro">2020-12-03</td>
                            </tr-->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../../js/jquery-3.5.1.min.js"></script>
    <script>
        var formatter = new Intl.NumberFormat("es");
        $( document ).ready(function() {
            $.ajax({
                url: '../../controller/controller2.php',
                type: "POST",
                data : {
                },
                dataType: 'json',
                success: function(data){
                    tbodyrespuesta = '';
                    numero = 1;
                    datos = data.GetListadoConvocatoriasResult['EntidadesConvocatorias.ListaConvocatorias'];
                    console.log(datos);
                    datos.forEach(element => {
                        tbodyrespuesta += '<tr>'
                        + '<td>'+numero+'</td>'
                        +'<td class="rowfilt link" value="'+element.NumProceso+'">'+element.NumProceso+'</td>'
                        +'<td>'+element.EstadoConvocatoria+'</td>'
                        +'<td>'+element.Actividad+'</td>'
                        +'<td>'+element.ObjetoContratar+'</td>'
                        /*+'<td>'+element.Depto+' - '+element.Municipio+'</td>'*/
                        +'<td> $'+formatter.format(element.Cuantia)+'</td>'
                        +'<td>'+element.FechaConvocada.split('T')[0]+'</td>'

                    numero++;
                    });
                    $('#tbodyrespuesta').html(tbodyrespuesta);
                },
                error: function(data){
                    console.log(data);
                }
            });

            $("#tbodyrespuesta").on('click', '.rowfilt', function () {
                numproces = $(this)[0].getAttribute('value');
                $.ajax({
                    url: '../../controller/controller2.php',
                    type: "POST",
                    data : {
                        'numero_proceso'    :   numproces
                    },
                    dataType: 'json',
                    success: function(data){
                        tbodyrep='';
                        console.log(data['GetDetalleConvocatoriaResult']['EntidadesConvocatorias.ListaConvocatorias']);
                        registro = data['GetDetalleConvocatoriaResult']['EntidadesConvocatorias.ListaConvocatorias'];
                        $('#txtprograma').html(registro.Programa);
                        $('#txtnumproc').html(registro.NumProceso);
                        $('#txttipproc').html(registro.TipoProceso);
                        $('#txtestado').html(registro.EstadoConvocatoria);
                        $('#txtactividad').html(registro.Actividad);
                        $('#txttipcontr').html(registro.TipoContratacion);
                        $('#txtobjcontrata').html(registro.ObjetoContratar);
                        $('#txtcuantia').html('$'+formatter.format(registro.Cuantia));
                        $('#txtmodalidad').html(registro.ModContratacion);
                        $('#txtcorreo').html(registro.Contacto);
                        $('#convocatorias').attr('hidden', true);
                        $('#detalleconvocatorias').attr('hidden', false);
                    },
                    error: function(data){
                        console.log(data);
                    }
                });
                $.ajax({
                    url: '../../controller/controller2.php',
                    type: "POST",
                    data : {
                        'numero_proceso_documento'    :   numproces
                    },
                    dataType: 'json',
                    success: function(data){
                        tbodyrespDocu='';
                        console.log(data);
                        documentos = data['GetDocumentosConvocatoriaResult']['EntidadesConvocatorias.Documentos'];
                        console.log(documentos);
                        if(documentos.length > 1)
                        {
                            num = 1
                            documentos.forEach(element => {
                                tbodyrespDocu += '<tr class="rowfiltdoc">'
                                    +'<td value="'+numproces+'">'+num+'</td>'
                                    +'<td class="link" value="'+element.NombreDoc+'"> <!--a href="../../controller/controller2.php?nombdoc='+element.NombreDoc+'&amp;numpro='+numproces+'"-->'+element.NombreDoc +'</td>'
                                    +'<td>'+element.DescripcionDoc +'</td>'
                                    +'<td>'+element.FechaDoc.split("T")[0] +'</td>'

                                +'</tr>';

                                num++;
                            });
                        }else{
                            tbodyrespDocu += '<tr class="rowfiltdoc">'
                                    +'<td value="'+numproces+'">1</td>'
                                    +'<td class="link" value="'+element.NombreDoc+'">'+documentos.NombreDoc +'</td>'
                                    +'<td>'+documentos.DescripcionDoc +'</td>'
                                    +'<td>'+documentos.FechaDoc.split("T")[0] +'</td>'
                                +'</tr>';
                        }

                        $('#tbodydocume').html(tbodyrespDocu);
                        /*$('#vistadocumentos').attr('hidden', false);
                        $('#vistatabla').attr('hidden', true);*/

                    },
                    error: function(data){
                        console.log(data);
                    }

                });
            });

            function base64ToArrayBuffer(base64) {
                var binaryString = window.atob(base64);
                var binaryLen = binaryString.length;
                var bytes = new Uint8Array(binaryLen);
                for (var i = 0; i < binaryLen; i++) {
                    var ascii = binaryString.charCodeAt(i);
                    bytes[i] = ascii;
                }
                console.log(bytes);
                return bytes;
            }

            function saveByteArray(reportName, byte) {
                var blob = new Blob([byte]);
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                var fileName = reportName;
                link.download = fileName;
                link.click();
            };

            $("#tbodydocume").on('click', '.rowfiltdoc', function () {
                //console.log($(this)[0]);
                numproces = $(this)[0].childNodes[0].getAttribute('value');
                NombreDoc = $(this)[0].childNodes[1].getAttribute('value');
                $.ajax({
                    url: '../../controller/controller2.php',
                    type: "POST",
                    data : {
                        'numero_proceso_documento_desc'    :   {
                            'numproceso'    :   numproces,
                            'nombredocu'    :   NombreDoc
                        }
                    },
                    responseType: 'blob',
                    success: function(res){
                        //console.log(res);
                        //var data = new Uint8Array(res);
                        /*var blob = new Blob([res], {type: 'application/pdf'});
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        var fileName = NombreDoc;
                        link.download = fileName;
                        link.click();*/
                        //return data;
                        var sampleArr = base64ToArrayBuffer(res);
                        saveByteArray(NombreDoc, sampleArr);
                    },
                    error: function(data){
                        console.log(data + ' Errorr');
                    }
                });
            });
        });

    </script>
</body>
</html>
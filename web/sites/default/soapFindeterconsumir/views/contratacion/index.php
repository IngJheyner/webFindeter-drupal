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
    <div class="container">
        <div class="col-md-12" id="vistatabla">
            <div class=" row" style="margin-top: 25px;">
                <div class="col-md-12">
                    <form method="POST" id="form-service">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label for="">Modalidad:</label>
                                <select name="modalidad" id="modalidad" class="form-control" >
                                    <option value=""> Cargando... </option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">Estado:</label>
                                <select name="estado" id="estado" class="form-control">
                                    <option value=""> Cargando... </option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">Procesos:</label>
                                <select name="procesos" id="procesos" class="form-control" >
                                    <option value=""> Cargando... </option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">Objeto:</label>
                                <input id="objeto" class="form-control" placeholder="Escriba el objeto">
                            </div>
                            <div class="col-md-3 form-group">
                                <input type="submit" value="Filtrar" class="btn btn-info">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div id="respuesta" class="col-md-12">
                    <div class="table_responsive">
                        <table id="tblProcesos" class="table table-striped table-condensed table-hover tablaGenPagineAjax  dataTable no-footer" role="grid" aria-describedby="tblProcesos_info" style="">
                            <thead>
                                <tr role="row">
                                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 25px;">No.</th>
                                    <th class="sorting_disabled colModalidad" rowspan="1" colspan="1" style="width: 91px;">Modalidad:</th>
                                    <th style="padding: 0px 15px; width: 65px;" class="sorting_disabled" rowspan="1" colspan="1">Fecha de creación</th>
                                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 59px;">Estado:</th>
                                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 690px;">Objeto:</th>
                                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 100px;">Presupuesto</th>
                                    <th style="display: none; width: 0px;" class="sorting_disabled hide info-detalle" rowspan="1" colspan="1"></th>
                                </tr>
                            </thead>
                            <tbody id="tbodyrespuesta">
                                
                            </tbody>
                        </table>
                    </div>
    
                </div>      
            </div>
        </div>
        <div class="col-md-12" id="vistadocumentos" hidden>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                    <div class="col-md-9"><h2 id="numberproceso"></h2> </div> <div class="col-md-3"><button class="btn btn-success" onclick="atras()"> Atrás</button></div>
                    </div>
                    <legend style="border-bottom: 0.5px solid #e5e5e5;">Información del proceso</legend>
                    <div class="form-group"><label class="col-md-3">Fecha de Inicio:</label>
                        <span class="col-md-9" id="fechainicio"></span>
                    </div>
                    <div class="form-group"><label class="col-md-3">Fecha de cierre:</label>
                        <span class="col-md-9" id="fechacierre"></span>
                    </div>
                    <div class="form-group"><label class="col-md-3">Estado:</label>
                        <span class="col-md-9" id="estadodoc"></span>
                    </div>
                    <div class="">
                        <div class="col-md-3">
                            <label>Objeto:</label>
                        </div>
                        <div class="col-md-9">
                            <span id="objetoproceso"> </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table id="tblDocumentosProceso" class="table table-striped table-condensed table-hover tablaGenPagineAjax  dataTable no-footer" style="margin-top:20px;" role="grid" aria-describedby="tblDocumentosProceso_info">
                        <thead><tr role="row">
                            <th class="sorting" tabindex="0" aria-controls="tblDocumentosProceso" rowspan="1" colspan="1" aria-label="No: activate to sort column ascending" style="width: 0px;">No</th>
                            <th class="sorting" tabindex="0" aria-controls="tblDocumentosProceso" rowspan="1" colspan="1" aria-label="Documento: activate to sort column ascending" style="width: 0px;">Documento</th>
                            <th class="sorting_desc" tabindex="0" aria-controls="tblDocumentosProceso" rowspan="1" colspan="1" aria-sort="descending" aria-label="Fecha de publicación: activate to sort column ascending" style="width: 0px;">Fecha de publicación</th>
                            <th class="sorting" tabindex="0" aria-controls="tblDocumentosProceso" rowspan="1" colspan="1" aria-label="Nombre: activate to sort column ascending" style="width: 0px;">Nombre</th><th class="sorting" tabindex="0" aria-controls="tblDocumentosProceso" rowspan="1" colspan="1" aria-label="Descargar: activate to sort column ascending" style="width: 0px;">Descargar</th>
                        </tr></thead>
                        <tbody id="tbodyrespDocu">
                            <!--<tr role="row" class="odd">
                                <td>34</td>
                                <td>CONTRATO</td>
                                <td class="sorting_1">2018-03-21 14:31:30</td>
                                <td>0042-2018_CON_CONTRATO.pdf</td>
                                <td><a href="https://www.findeter.gov.co/loader.php?lServicio=WS&amp;lTipo=erp&amp;lFuncion=descarga&amp;nombre=0042-2018_CON_CONTRATO.pdf"> Descargar </a></td>
                            </tr>-->
                        </tbody>
                    </table>
                            
                    <!--script type="text/javascript">$('#tblDocumentosProceso').dataTable({ "order": [[ 2, "desc" ]], "bFilter": false, "bLengthChange": true, "sPaginationType": "full_numbers" });</script-->
                </div>
            </div>
        </div>
    </div>
    
    

    <script src="../../js/jquery-3.5.1.min.js"></script>
    <script>
        $( document ).ready(function() {
            var formatter = new Intl.NumberFormat("es");
            $.ajax({
                url: '../../controller/controller.php',
                type: "POST",
                data : {
                    'procedimientos' : 73,
                    'argumentos' : ''
                },
                dataType: 'json',
                success: function(data){
                    selectmodalidresp = '';
                    selectmodalidresp += '<option value=""> --Seleccione-- </option>'
                    data.Registro.forEach(element => {
                        selectmodalidresp += '<option value="'+ element.tcnn_codigo+'"> '+element.tcnc_nombre +'</option>'
                    });
                    $('#modalidad').html(selectmodalidresp);
                },
                error: function(data){
                    console.log(data);
                }
            });
            $.ajax({
                url: '../../controller/controller.php',
                type: "POST",
                data : {
                    'procedimientos' : 74,
                    'argumentos' : ''
                },
                dataType: 'json',
                success: function(data){
                    selectestadoresp = '';
                    selectestadoresp += '<option value=""> --Seleccione-- </option>'
                    data.Registro.forEach(element => {
                        selectestadoresp += '<option value="'+element.pctc_estado+'"> '+element.pctc_estado +'</option>'
                    });
                    $('#estado').html(selectestadoresp);
                },
                error: function(data){
                    console.log(data);
                }
            });
            $.ajax({
                url: '../../controller/controller.php',
                type: "POST",
                data : {
                    'procedimientos' : 75,
                    'argumentos' : ''
                },
                dataType: 'json',
                success: function(data){
                    selectprocesosresp = '';
                    selectprocesosresp += '<option value=""> --Seleccione-- </option>'
                    data.Registro.forEach(element => {
                        selectprocesosresp += '<option value="'+ element.interno+'"> '+element.interno +'</option>'
                    });
                    $('#procesos').html(selectprocesosresp);
                },
                error: function(data){
                    console.log(data);
                }
            });
            $.ajax({
                    url: '../../controller/controller.php',
                    type: "POST",
                    data : {
                        'procedimientos'    :   76,
                        'estado'            :   'VIGENTE'
                    },
                    dataType: 'json',
                    success: function(data){
                        tbodyrep='';
                        if(data.Registro.Error){
                            tbodyrep += '<tr>'
                                +'<td colspan="6">'+ data.Registro.Error +'</td>'
                            +'</tr>';
                        }else{
                            if(data.Registro.length > 1)
                            {
                                num = 1
                                data.Registro.forEach(element => {

                                    tbodyrep += '<tr class="rowfilt">'
                                        +'<td name="interno" value="'+element.interno+'">'+num+'</td>'
                                        +'<td class="link" name="modalidad" value="'+ element.modalidadCodigo +'">'+ element.modalidad +'</td>'
                                        +'<td align="right">'+element.INITDATE.split("T")[0] +'</td>'
                                        +'<td align="right">'+ element.estado+' </td>'
                                        +'<td align="right">'+ element.objeto+' </td>'
                                        +'<td align="right">'+ formatter.format(element.presupuestado)+' </td>'
                                    +'</tr>';
                                    num ++;
                                });
                            }else{
                                tbodyrep += '<tr class="rowfilt">'
                                    +'<td name="interno" value="'+data.Registro.interno+'">1</td>'
                                    +'<td class="link" name="modalidad" value="'+ data.Registro.modalidadCodigo +'">'+ data.Registro.modalidad +'</td>'
                                    +'<td align="right">'+data.Registro.INITDATE.split("T")[0] +'</td>'
                                    +'<td align="right">'+ data.Registro.estado+' </td>'
                                    +'<td align="right">'+ data.Registro.objeto+' </td>'
                                    +'<td align="right">'+ formatter.format(data.Registro.presupuestado)+' </td>'
                                +'</tr>';
                            }
                        }
                        $('#tbodyrespuesta').html(tbodyrep);
                    },
                    error: function(data){
                        console.log(data);
                    }

                });
            $("#tbodyrespuesta").on('click', '.rowfilt', function () {
                internomod      = $(this)[0].childNodes[0].getAttribute('value');
                modalidaddoc    = $(this)[0].childNodes[1].getAttribute('value');               
                $.ajax({
                    url: '../../controller/controller.php',
                    type: "POST",
                    data : {
                        'procedimientos'    :   78,
                        'argumentos'        :   '@tipo_contrato='+modalidaddoc+', @interno="'+internomod+'"'
                    },
                    dataType: 'json',
                    success: function(data){
                        tbodyrep='';
                        console.log(data.Registro);
                        registro = data.Registro;
                        $('#numberproceso').html('Proceso Número: '+ registro.interno);
                        $('#fechainicio').html(registro.pctf_apertura.split("T")[0]);
                        if(registro.pctf_cierre.split("T")[0] != '1900-01-01'){
                            $('#fechacierre').html(registro.pctf_cierre.split("T")[0]);
                        }
                        $('#estadodoc').html(registro.papc_nombre);
                        $('#objetoproceso').html(registro.objeto);
                    },
                    error: function(data){
                        console.log(data);
                    }

                });

                $.ajax({
                    url: '../../controller/controller.php',
                    type: "POST",
                    data : {
                        'procedimientos'    :   79,
                        'argumentos'        :   '@tipo_contrato='+modalidaddoc+', @interno="'+internomod+'"'
                    },
                    dataType: 'json',
                    success: function(data){
                        tbodyrespDocu='';
                        if(data.Registro.Error){
                            tbodyrespDocu += '<tr>'
                                +'<td colspan="6">'+ data.Registro.Error +'</td>'
                            +'</tr>';
                        }else{
                            if(data.Registro.length > 1)
                            {
                                num = 1
                                data.Registro.forEach(element => {
                                    tbodyrespDocu += '<tr class="rowfilt">'
                                        +'<td>'+num+'</td>'
                                        +'<td>'+ element.documento +'</td>'
                                        +'<td>'+element.fecha.split("T")[0] +'</td>'
                                        +'<td>'+element.nom_archivo +'</td>'
                                        +'<td> <a href="../../controller/controller.php?funcion=descarga&amp;nombre='+element.nom_archivo+'"> Descargar </a></td>'

                                    +'</tr>';

                                    num++;
                                });
                            }else{
                                tbodyrespDocu += '<tr class="rowfilt">'
                                        +'<td>1</td>'
                                        +'<td>'+ data.Registro.documento +'</td>'
                                        +'<td>'+data.Registro.fecha.split("T")[0] +'</td>'
                                        +'<td>'+data.Registro.nom_archivo +'</td>'
                                        +'<td> <a href="../../controller/controller.php?funcion=descarga&amp;nombre='+data.Registro.nom_archivo+'"> Descargar </a></td>'
                                    +'</tr>';
                            }
                        }

                        $('#tbodyrespDocu').html(tbodyrespDocu);
                        $('#vistadocumentos').attr('hidden', false);
                        $('#vistatabla').attr('hidden', true);

                    },
                    error: function(data){
                        console.log(data);
                    }

                });
            }); 
            $('#form-service').submit(function(event){
                event.preventDefault();
                var modalidad = $("#modalidad").val();
                var estado = $('#estado').val()
                var procesos = $("#procesos").val();
                var objeto = $('#objeto').val();

                $.ajax({
                    url: '../../controller/controller.php',
                    type: "POST",
                    data : {
                        'procedimientos'    :   76,
                        'modalidad'         :   modalidad,
                        'estado'            :   estado,
                        'procesos'          :   procesos,
                        'objeto'            :   objeto
                    },
                    dataType: 'json',
                    success: function(data){
                        tbodyrep='';
                        if(data.Registro.Error){
                            tbodyrep += '<tr>'
                                +'<td colspan="6">'+ data.Registro.Error +'</td>'
                            +'</tr>';
                        }else{
                            if(data.Registro.length > 1)
                            {
                                num = 1
                                data.Registro.forEach(element => {

                                    tbodyrep += '<tr class="rowfilt">'
                                        +'<td name="interno" value="'+element.interno+'">'+num+'</td>'
                                        +'<td name="modalidad" value="'+ element.modalidadCodigo +'">'+ element.modalidad +'</td>'
                                        +'<td align="right">'+element.INITDATE.split("T")[0] +'</td>'
                                        +'<td align="right">'+ element.estado+' </td>'
                                        +'<td align="right">'+ element.objeto+' </td>'
                                        +'<td align="right">'+ formatter.format(element.presupuestado)+' </td>'
                                    +'</tr>';
                                    num ++;
                                });
                            }else{
                                tbodyrep += '<tr class="rowfilt">'
                                    +'<td name="interno" value="'+data.Registro.interno+'">1</td>'
                                    +'<td name="modalidad" value="'+ data.Registro.modalidadCodigo +'">'+ data.Registro.modalidad +'</td>'
                                    +'<td align="right">'+data.Registro.INITDATE.split("T")[0] +'</td>'
                                    +'<td align="right">'+ data.Registro.estado+' </td>'
                                    +'<td align="right">'+ data.Registro.objeto+' </td>'
                                    +'<td align="right">'+ formatter.format(data.Registro.presupuestado)+' </td>'
                                +'</tr>';
                            }
                        }
                        $('#tbodyrespuesta').html(tbodyrep);
                    },
                    error: function(data){
                        console.log(data);
                    }

                });

            });

        });

        var atras = function(){
                $('#vistadocumentos').attr('hidden', true);
                $('#vistatabla').attr('hidden', false);
            }
    </script>
</body>
</html>
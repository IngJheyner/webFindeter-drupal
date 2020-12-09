<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>service contratacion</title>
    <link rel="stylesheet" href="../../bootstrap/bootstrap-4/css/bootstrap.min.css">
</head>
<body>
    <div class="row" style="margin-top: 25px;">
        <div class="container">
            <form method="POST" id="form-service">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label for="">Modalidad:</label>
                        <select name="modalidad" id="modalidad" class="form-control" required>
                            <option value=""> Cargando... </option>
                        </select>
                    </div>
                    <!--div class="col-md-3 form-group" hidden>
                        <label for="">Estado:</label>
                        <select name="estado" id="estado" class="form-control">
                            <option value=""> Cargando... </option>
                        </select>
                    </div-->
                    <div class="col-md-3 form-group">
                        <label for="">Procesos:</label>
                        <select name="procesos" id="procesos" class="form-control" required>
                            <option value=""> Cargando... </option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <input type="submit" value="Filtrar" class="btn btn-info">
                    </div>
                </div>
            </form>
            <div id="respuesta">
                <div class="table_responsive">
                    <table id="tblProcesos" class="tablaGen table table-striped table-condensed table-hover tablaGenPagineAjax  dataTable no-footer" role="grid" aria-describedby="tblProcesos_info" style="width: 1140px;">
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

    <script src="../../js/jquery-3.5.1.min.js"></script>
    <script>
        $( document ).ready(function() {
            $.ajax({
                url: '../../controller/controller.php',
                type: "POST",
                data : {
                    'procedimientos' : 73,
                    //'procedimientos' : 77,
                    //'argumentos' : '@CANTIDAD=10, @PAGINA=1'
                    'argumentos' : ''
                },
                dataType: 'json',
                success: function(data){
                    //tbodyrep  = '';
                    selectmodalidresp = '';
                    //console.log(data.Registro);
                    selectmodalidresp += '<option value=""> --Seleccione-- </option>'
                    data.Registro.forEach(element => {
                        //console.log(element);
                        /*tbodyrep += '<tr>'
                            +'<td>N°</td>'
                            +'<td>'+ element.modalidad +'</td>'
                            +'<td align="right">'+element.INITDATE +'</td>'
                            +'<td align="right">'+ element.estado+' </td>'
                            +'<td align="right">'+ element.objeto+' </td>'
                            +'<td align="right">'+ element.presupuestado+' </td>'
                        +'</tr>';*/
                        selectmodalidresp += '<option value="'+ element.tcnn_codigo+'"> '+element.tcnc_nombre +'</option>'
                        

                    });
                    $('#modalidad').html(selectmodalidresp);
                    //$("#tbodyrespuesta").html(tbodyrep);
                    //console.log(data.Registro);
                    //var obj = jQuery.parseJSON(data);
                    //console.log(obj);
                },
                error: function(data){
                    console.log(data);
                }
            });
            /*$.ajax({
                url: '../../controller/controller.php',
                type: "POST",
                data : {
                    'procedimientos' : 74,
                    'argumentos' : ''
                },
                dataType: 'json',
                success: function(data){
                    selectestadoresp = '';
                    //console.log(data.Registro);
                    selectestadoresp += '<option val=""> --Seleccione-- </option>'
                    data.Registro.forEach(element => {
                        selectestadoresp += '<option val="'+ element.STATE+'"> '+element.pctc_estado +'</option>'
                        

                    });
                    $('#estado').html(selectestadoresp);
                },
                error: function(data){
                    console.log(data);
                }
            });*/
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
                    //console.log(data.Registro);
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

            $('#form-service').submit(function(event){
                var modalidad = $("#modalidad").val();
                var procesos = $("#procesos").val();
                event.preventDefault();
                $.ajax({
                    url: '../../controller/controller.php',
                    type: "POST",
                    data : {
                        'procedimientos' : 78,
                        'argumentos' : '@tipo_contrato="'+modalidad+'", @interno="'+procesos+'"'
                    },
                    dataType: 'json',
                    success: function(data){
                        tbodyrep='';
                        console.log(data.Registro);
                        /*data.Registro.forEach(element => {
                            
                        });*/
                        
                        tbodyrep += '<tr>'
                            +'<td>1</td>'
                            +'<td>'+ $("#modalidad option:selected").text() +'</td>'
                            +'<td align="right">'+data.Registro.pctf_apertura +'</td>'
                            +'<td align="right">'+ data.Registro.papc_nombre+' </td>'
                            +'<td align="right">'+ data.Registro.objeto+' </td>'
                            +'<td align="right">'+ data.Registro.pctv_presupuestado+' </td>'
                        +'</tr>';
                        $('#tbodyrespuesta').html(tbodyrep);
                    },
                    error: function(data){
                        console.log(data);
                    }
                });
            })
        });
    </script>
</body>
</html>
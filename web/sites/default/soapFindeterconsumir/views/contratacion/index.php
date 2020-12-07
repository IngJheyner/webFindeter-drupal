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
                            <!--<tr data-alt="0181-2020" data-alt2="5637145334" class="abreProceso odd" role="row">
                                <td>1</td><td class=" colModalidad">INVITACION A OFERTAR</td>
                                <td>2020-12-02</td>
                                <td>VIGENTE</td>
                                <td>REALIZAR LOS ESTUDIOS DE SATISFACCIÓN DE CLIENTE INTERNO Y EXTERNO BUSCANDO CONOCER LOS NIVELES DE SATISFACCIÓN OFRECIDOS AL INTERIOR DE L...</td>
                                <td>123.800.000,00</td>
                                <td class="hide info-detalle">0181-2020*5637145334</td>
                            </tr>
                            <tr data-alt="0179-2020" data-alt2="5637145334" class="abreProceso even" role="row">
                                <td>2</td>
                                <td class=" colModalidad">INVITACION A OFERTAR</td>
                                <td>2020-12-01</td>
                                <td>VIGENTE</td>
                                <td>RENOVACIÓN DEL LICENCIAMIENTO ANTIVIRUS MCAFEE COMPLETE ENDPOINT FOR BUSINESS (CEB), CON EL SOPORTE Y MANTENIMIENTO PARA LA PLATAFORMA DE PROTECCIÓN END-POINT.</td>
                                <td>121.506.140,00</td>
                                <td class=" hide info-detalle">0179-2020*5637145334</td>
                            </tr>
                            <tr data-alt="0175-2020" data-alt2="5637145334" class="abreProceso odd" role="row">
                                <td>3</td>
                                <td class=" colModalidad">INVITACION A OFERTAR</td>
                                <td>2020-11-27</td>
                                <td>VIGENTE</td>
                                <td>CONTRATAR EL SUMINISTRO, INSTALACIÓN Y FUNCIONALIDAD DE 4 PARQUES INFANTILES RECREATIVOS EN DIFERENTES MUNICIPIOS PRIORIZADOS A NIVEL NACIO...</td>
                                <td>200.444.673,00</td>
                                <td class=" hide info-detalle">0175-2020*5637145334</td>
                            </tr>
                            <tr data-alt="0068-2019" data-alt2="5637152083" class="abreProceso even" role="row">
                                <td>4</td>
                                <td class=" colModalidad">CONVOCATORIA ABIERTA</td>
                                <td>1900-01-01</td>
                                <td>VIGENTE</td>
                                <td>SUMINISTRO DE ELEMENTOS DE ASEO Y CAFETERÍA, EN LAS CIUDADES DE MEDELLÍN, CALI, BUCARAMANGA, NEIVA, BARRANQUILLA, PEREIRA, MONTERÍA, BOGO...</td>
                                <td>737.964.969,00</td>
                                <td class=" hide info-detalle">0068-2019*5637152083</td>
                            </tr>-->
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
                    'procedimientos' : 77,
                    'argumentos' : '@CANTIDAD=10, @PAGINA=1'
                },
                dataType: 'json',
                success: function(data){
                    tbodyrep  = '';
                    //console.log(data);
                    data.Registro.forEach(element => {
                        //console.log(element);
                        tbodyrep += '<tr>'
                            +'<td>N°</td>'
                            +'<td>'+ element.modalidad +'</td>'
                            +'<td align="right">'+element.INITDATE +'</td>'
                            +'<td align="right">'+ element.estado+' </td>'
                            +'<td align="right">'+ element.objeto+' </td>'
                            +'<td align="right">'+ element.presupuestado+' </td>'
                        +'</tr>';

                    });
                    $("#tbodyrespuesta").html(tbodyrep);
                    //console.log(data.Registro);
                    //var obj = jQuery.parseJSON(data);
                    //console.log(obj);
                },
                error: function(data){
                    console.log(data);
                }
            });
        });
    </script>
</body>
</html>
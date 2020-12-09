<html>
<head>
    <link rel="stylesheet" href="bootstrap/bootstrap-4/css/bootstrap.min.css">
	<!--link rel="stylesheet" href="../../../themes/contrib/bootstrap4/dist/bootstrap/4.5.3/dist/css/bootstrap.min.css"-->
</head>
<body style="width: 98%;">
   
    <div class="container" style="margin:0px;">
        <div class="row">
            <div class="col-md-6">
                <label>Vigencia</label>
                <select class="form-control" id="vig">
                    <option value="0">--Seleccione--</option>
                    <option value="2020">2020</option>
                    <option value="2019">2019</option>
                    <option value="2018">2018</option>
                </select>
            </div>
            <div class="col-md-6" id="divtipo" hidden>
                <label for="">Tipo</label> 
                <select name="tipo" id="tipo" class="form-control">
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6" id="divcapitulo" hidden>
                <label for="">Capítulo</label> 
                <select name="capitulo" id="capitulo" class="form-control">
                </select>
            </div>
            <div class="col-md-6" id="divsubcapitulo" hidden>
                <label for="">SubCapítulo</label>
                <select name="subCapitulo" id="subCapitulo" class="form-control">
                </select>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 25px;">
        <div class="container">
            <div id="respuesta">
                <div class="table_responsive">
                    <table class="tablaGen table table-striped table-condensed table-hover tablaGenPagine" style>
                        <thead>
                            <tr>
                                <th style="text-align: center;">Tipo</th>
                                <th style="text-align: center;" class="titulo" data-toggle="tooltip" data-placement="left" title="Presupuesto aprobado por la Junta Directiva para cada vigencia." alt="Presupuesto aprobado por la Junta Directiva para cada vigencia.">Inicial</th>
                                <th style="text-align: center;" class="titulo" data-toggle="tooltip" data-placement="left" title="Presupuesto inicial mas modificaciones que se requieran." alt="Presupuesto inicial mas modificaciones que se requieran.">Neto</th>
                                <th style="text-align: center;" class="titulo" data-toggle="tooltip" data-placement="left" title="Son los actos realizados en desarrollo de la capacidad de contratar y de comprometer el presupuesto" alt="Son los actos realizados en desarrollo de la capacidad de contratar y de comprometer el presupuesto">Ejecución</th>
                                <th style="text-align: center;" class="titulo" data-toggle="tooltip" data-placement="left" title="Corresponde al NETO - EJECUCION" alt="Corresponde al NETO - EJECUCION">Saldo</th>
                            </tr>     
                        </thead>	
                        <tbody id="tbodyrespuesta" style="font-size:14px;">	

                        </tbody>	
                    </table>        
                </div>  
            </div>
        </div>
    </div>
    <script src="js/jquery-3.5.1.min.js"></script>
    <script>
        var formatter = new Intl.NumberFormat("es", {
            style: "currency",
            currency: "COP"
        });

        var tipoelemen = '<option value="0">--Seleccione--</option> <option value="1">fuentes</option><option value="2">aplicaciones</option>'

        $('#vig').on('change', function(){
            var vig = $("#vig").val();
            //alert($("#vig").val());
            switch (vig) {
                case "0":
                    $("#divtipo").attr('hidden',true);
                    $("#tipo").html("");
                    $("#divcapitulo").attr('hidden',true);
                    $("#capitulo").html("");
                    $("#divsubcapitulo").attr('hidden',true);
                    $("#subCapitulo").html("");
                    break;
                case "2020":
                    $("#divcapitulo").attr('hidden',true);
                    $("#capitulo").html("");
                    $("#divsubcapitulo").attr('hidden',true);
                    $("#subCapitulo").html("");
                    $("#divtipo").attr('hidden',false);
                    $("#tipo").html(tipoelemen);
                    break;
                case "2019":
                    $("#divcapitulo").attr('hidden',true);
                    $("#capitulo").html("");
                    $("#divsubcapitulo").attr('hidden',true);
                    $("#subCapitulo").html("");
                    $("#divtipo").attr('hidden',false);
                    $("#tipo").html(tipoelemen);
                    break;
                case "2018":
                    $("#divcapitulo").attr('hidden',true);
                    $("#capitulo").html("");
                    $("#divsubcapitulo").attr('hidden',true);
                    $("#subCapitulo").html("");
                    $("#divtipo").attr('hidden',false);
                    $("#tipo").html(tipoelemen);
                    break;
            }
            $.ajax({
                url: 'controller/controller.php',
                type: "POST",
                data : {
                    'procedimientos' : '82',
                    'argumentos' : '@vig='+vig
                },
                dataType: 'json',
                success: function(data){
                    
                    tbodyrep  = '';
                    data.Registro.forEach(element => {
                        //console.log(element);
                        tbodyrep += '<tr>'
                            +'<td>'
                                +'<a href="#" value="1" class="enlaceTipo" alt="">'+ element.Nombre+'</a>'
                            +'</td>'
                            +'<td align="middle">'+ '$ '+ formatter.format(element.INICIAL) +'</td>'
                            +'<td align="middle">'+ '$ '+ formatter.format(element.NETO) +'</td>'
                            +'<td align="middle">'+ '$ '+ formatter.format(element.EJECUCION) +'</td>'
                            +'<td align="middle">'+ '$ '+ formatter.format(element.SALDO) +'</td>'
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

        $('#tipo').on('change', function(){

            var vig = $("#vig").val();
            var tipo = $("#tipo").val();
            //alert($("#vig").val());
            switch (tipo) {
                case "0":
                    $("#divcapitulo").attr('hidden',true);
                    $("#capitulo").html("");
                    $("#divsubcapitulo").attr('hidden',true);
                    $("#subCapitulo").html("");
                    break;
                case "1":
                    $("#divsubcapitulo").attr('hidden',true);
                    $("#subCapitulo").html("");
                    $("#divcapitulo").attr('hidden',false);
                    $("#capitulo").html('<label for="">Capítulo</label> <select name="capitulo" id="capitulo" class="form-control"><option value="0">--Seleccione--</option><option value="01">ingresos financieros</option><option value="02">ingresos convenios y/o programas</option><option value="03">otros ingresos operacionales</option><option value="04">otros ingresos no operacionales</option></select>');
                    break;
                case "2":
                    $("#divsubcapitulo").attr('hidden',true);
                    $("#subCapitulo").html("");
                    $("#divcapitulo").attr('hidden',false);
                    $("#capitulo").html('<option value="0">--Seleccione--</option><option value="01">gastos financieros</option><option value="03">gastos de funcionamiento y admon general</option><option value="04">adquisicion de activos</option>');
                    break;
            }
            $.ajax({
                url: 'controller/controller.php',
                type: "POST",
                data : {
                    'procedimientos' : '82',
                    'argumentos' : '@vig='+vig+', @tip_cta='+tipo
                },
                dataType: 'json',
                success: function(data){
                    
                    tbodyrep  = '';
                    data.Registro.forEach(element => {
                        //console.log(element);
                        tbodyrep += '<tr>'
                            +'<td>'
                                +'<a href="#" value="1" class="enlaceTipo" alt="">'+ element.Nombre+'</a>'
                            +'</td>'
                            +'<td align="middle">'+ '$ '+ formatter.format(element.INICIAL) +'</td>'
                            +'<td align="middle">'+ '$ '+ formatter.format(element.NETO) +'</td>'
                            +'<td align="middle">'+ '$ '+ formatter.format(element.EJECUCION) +'</td>'
                            +'<td align="middle">'+ '$ '+ formatter.format(element.SALDO) +'</td>'
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

        $('#capitulo').on('change', function(){

            var vig = $("#vig").val();
            var tipo = $("#tipo").val();
            var capitulo = $("#capitulo").val();
            //alert($("#vig").val());
            switch (tipo) {
                case "0":
                    $("#divsubcapitulo").attr('hidden',true);
                    $("#subCapitulo").html("");
                    break;
                case "1":
                    switch (capitulo) {
                        case "0":
                            $("#divsubcapitulo").attr('hidden',true);
                            $("#subCapitulo").html("");
                            break;
                        case "01":
                            $("#divsubcapitulo").attr('hidden',false);
                            $("#subCapitulo").html('<option value="0">--Seleccione--</option><option value="01">recaudo intereses cartera redescuento</option>'
                                +'<option value="02">recaudo intereses cartera empleados y exempleados</option>'
                                +'<option value="04">rendimientos financieros</option>'
                                +'<option value="05">ingresos - derivados</option>'
                                +'<option value="06">ingresos - inversiones</option>'
                                +'<option value="07">compensacion tasa ministerios</option>');
                            break;
                        case "02":
                            $("#divsubcapitulo").attr('hidden',false);
                            $("#subCapitulo").html('<option value="0">--Seleccione--</option>'
                                +'<option value="01">convenios y/o programas</option>'
                                +'<option value="02">otros convenio y/o programas</option>');
                            break;
                        case "03":
                            $("#divsubcapitulo").attr('hidden',false);
                            $("#subCapitulo").html('<option value="0">--Seleccione--</option>'
                                +'<option value="01">comisiones fiduciarias</option>'
                                +'<option value="02">otras comisiones</option>'
                                +'<option value="04">banca de inversion</option>');
                            break;
                        case "04":
                            $("#divsubcapitulo").attr('hidden',false);
                            $("#subCapitulo").html('<option value="0">--Seleccione--</option>'
                                +'<option value="01">otros</option>');
                            break;
                    }
                    break;
                case "2":
                    switch (capitulo) {
                        case "0":
                            $("#divsubcapitulo").attr('hidden',true);
                            $("#subCapitulo").html("");
                            break;
                        case "01":
                            $("#divsubcapitulo").attr('hidden',false);
                            $("#subCapitulo").html('<option value="0">--Seleccione--</option>'
                                +'<option value="02">intereses y comisiones de obligaciones</option>'
                                +'<option value="03">aportes financieros reservas asamblea</option>'
                                +'<option value="04">egresos - derivados</option>'
                                +'<option value="06">comisiones y otros gastos financieros</option>');
                            break;
                        case "03":
                            $("#divsubcapitulo").attr('hidden',false);
                            $("#subCapitulo").html('<option value="0">--Seleccione--</option>'
                                +'<option value="01">gastos de personal</option>'
                                +'<option value="02">gastos de viaje</option>'
                                +'<option value="03">honorarios</option>'
                                +'<option value="04">impuestos y contribuciones</option>'
                                +'<option value="05">gastos administrativos</option>'
                                +'<option value="07">banca de inversion</option>');
                            break;
                        case "04":
                            $("#divsubcapitulo").attr('hidden',false);
                            $("#subCapitulo").html('<option value="0">--Seleccione--</option>'
                                +'<option value="01">adquisicion de activos</option>');
                            break;
                    }
                    break;
            }
            $.ajax({
                url: 'controller/controller.php',
                type: "POST",
                data : {
                    'procedimientos' : '82',
                    'argumentos' : '@vig='+vig+', @tip_cta='+tipo+', @cap="'+capitulo+'"'
                },
                dataType: 'json',
                success: function(data){
                    
                    tbodyrep  = '';
                    data.Registro.forEach(element => {
                        //console.log(element);
                        tbodyrep += '<tr>'
                            +'<td>'
                                +'<a href="#" value="1" class="enlaceTipo" alt="">'+ element.Nombre+'</a>'
                            +'</td>'
                            +'<td align="middle">'+ '$ '+ formatter.format(element.INICIAL) +'</td>'
                            +'<td align="middle">'+ '$ '+ formatter.format(element.NETO) +'</td>'
                            +'<td align="middle">'+ '$ '+ formatter.format(element.EJECUCION) +'</td>'
                            +'<td align="middle">'+ '$ '+ formatter.format(element.SALDO) +'</td>'
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

        $('#subCapitulo').on('change', function(){
            var vig = $("#vig").val();
            var tipo = $("#tipo").val();
            var capitulo = $("#capitulo").val();
            var subcapi = $("#subCapitulo").val();
            console.log(subcapi);
            $.ajax({
                url: 'controller/controller.php',
                type: "POST",
                data : {
                    'procedimientos' : '82',
                    'argumentos' : '@vig="'+vig+'", @tip_cta='+tipo+', @cap="'+capitulo+'", @scap="'+subcapi+'"'
                },
                dataType: 'json',
                success: function(data){
                    
                    tbodyrep  = '';
                    console.log(data.Registro)
                    if(data.Registro.length > 1){
                        data.Registro.forEach(element => {
                            //console.log(element);
                            tbodyrep += '<tr>'
                                +'<td>'
                                    +'<a href="#" value="1" class="enlaceTipo" alt="">'+ element.Nombre+'</a>'
                                +'</td>'
                                +'<td align="middle">'+ '$ '+ formatter.format(element.INICIAL) +'</td>'
                                +'<td align="middle">'+ '$ '+ formatter.format(element.NETO) +'</td>'
                                +'<td align="middle">'+ '$ '+ formatter.format(element.EJECUCION) +'</td>'
                                +'<td align="middle">'+ '$ '+ formatter.format(element.SALDO) +'</td>'
                            +'</tr>';
                        });
                    }else{
                        tbodyrep += '<tr>'
                                +'<td>'
                                    +'<a href="#" value="1" class="enlaceTipo" alt="">'+ data.Registro.Nombre+'</a>'
                                +'</td>'
                                +'<td align="middle">'+ '$ ' + formatter.format(data.Registro.INICIAL) +'</td>'
                                +'<td align="middle">'+ '$ ' + formatter.format(data.Registro.NETO) +'</td>'
                                +'<td align="middle">'+ '$ ' + formatter.format(data.Registro.EJECUCION) +'</td>'
                                +'<td align="middle">'+ '$ ' + formatter.format(data.Registro.SALDO) +'</td>'
                            +'</tr>';
                    }
                    
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

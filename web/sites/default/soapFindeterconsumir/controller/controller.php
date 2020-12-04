<?php

function consult(){
    $procedimiento =  $_POST['procedimientos'];
    $argumentos = $_POST['argumentos'];
    $url = "http://w2sdg022:8084/sp_ws.asmx?WSDL";
    $client = new SoapClient($url);
    $result = $client->resultado_xml([ "procedimiento" => $procedimiento, "argumentos" => $argumentos]);
    //$registro = $result->Registro;
    //print_r($registro);
    $arr = $result->resultado_xmlResult->any;
    //var_dump(json_encode($arr));
    /*$arr = array(
        'stack'=>'overflow',
        'key'=>'value'
    );*/
    $xml = simplexml_load_string($arr);
    $json = json_encode($xml);
    $array = json_decode($json,TRUE);
   echo json_encode($array);
    //return json_encode($arr);
}

consult();

/*try {
    $client = new SoapClient($url, [ "procedimiento" => "82", "argumentos" => ""]);
    $result = $client->resultado_xml([ "procedimiento" => "82", "argumentos" => ""]);
    //print_r($client->__getFunction());
    //$client->resultado_xml([ "procedimiento" => 82, 'argumentos' => '']
    /*$arrayvalues = json_encode($result->resultado_xmlResult->any);
    $arrayvalues = strval($arrayvalues);
    //print($arrayvalues);
    $arrayvalues = explode('<\/Vig><\/Registro>', $arrayvalues);
    print_r(strval(xmlns($arrayvalues[0])));
    $arrayvalue1 = explode('""xmlns=\"\""', strval($arrayvalues[0]));
    $arrayvalues = implode($arrayvalue1);
    print_r($arrayvalue1);
    $arrayvalues = str_split($arrayvalues, 4);
    //array_pop($arrayvalues);
    print_r($arrayvalues);
    echo('<div class="container" style="margin-top: 10px;">');
    echo('<div class="row">');
    echo('<label>Vigencia</label>');
    echo('<select class="form-control" id="vig">');
    echo('<option value="0">--Seleccione--</option>');
    foreach($arrayvalues as $key => $value){
        echo("<option value='".$value."'>".$value."</option>");
    }
    echo('</select>');
    echo('</div>');
    echo('</div>');
} catch ( SoapFault $e ) {
echo $e->getMessage();
}*/

echo PHP_EOL;
?>
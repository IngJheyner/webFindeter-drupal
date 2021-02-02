<?php

function consult(){
    try {
        $url = "http://10.10.3.22:6525/SIAAF_ServiciosWCF/Servicios/SvcCredenciales.svc?WSDL";
        $client = new SoapClient($url);
        //$result = $client->resultado_xml([ "IDUnico" => "651551E9-814E-40BE-9590-775C3F59A0F0"]);
        $result = $client->ConsultarCredencial([ "idCredencial" => "f7f81cd6-5384-4da6-bba0-0d4ca4a321b3"]);
        $result = $result->ConsultarCredencialResult;
        $result = base64_decode($result->Clave);

        echo json_encode($result);
        //echo json_encode($client->__getFunctions());
    } catch ( SoapFault $e ) {
        echo $e->getMessage();
    }
}

consult();

echo PHP_EOL;
?>
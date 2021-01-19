<?php

function consult(){
    try {
        $url = "http://10.10.3.22:6525/SIAAF_ServiciosWCF/Servicios/SvcCredenciales.svc?WSDL";
        $client = new SoapClient($url);
        //$result = $client->resultado_xml([ "IDUnico" => "651551E9-814E-40BE-9590-775C3F59A0F0"]);
        $result = $client->ConsultarCredencial([ "idCredencial" => "651551E9-814E-40BE-9590-775C3F59A0F0"]);
        $result = $result->ConsultarCredencialResult;
        echo base64_encode($result->Clave);
        //echo json_encode($client->__getFunctions());
    } catch ( SoapFault $e ) {
        echo $e->getMessage();
    }
}

consult();

echo PHP_EOL;
?>
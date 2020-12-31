<?php

function consult(){
    if(!empty($_GET['nombre'])){
        $fileName = $_GET['nombre'];
        //echo($_GET['nombre']);
        //$filePath = 'E:/ecosistema/private/contratacion/ws/0001-2016_AAD_ACTA DE SELECCION C-FDT-01-2016.pdf';
        $filePath = 'E:/ecosistema/private/contratacion/ws/'.$fileName;
        if(!empty($fileName) && file_exists($filePath)){
            // Define headers
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$fileName");
            header("Content-Type: application/zip");
            header("Content-Transfer-Encoding: binary");
            
            // Read the file
            readfile($filePath);
            exit;
        }else{
            echo 'The file does not exist.';
        }
        /*header("Content-disposition: attachment; filename=Hipercubo.pdf");
        header("Content-type: application/pdf");
        //readfile("E:/ecosistema/private/contratacion/ws/0068-2019_AAP_ACTA%20DE%20APERTURA%20C-FDT-03-2019.pdf");
        readfile("/0068-2019_AAP_ACTA DE APERTURA C-FDT-03-2019.pdf");*/
    }else{
        $procedimiento =  $_POST['procedimientos'];

        if($procedimiento == 76 ){
            //$ingEstdo = intval($_POST['estado']);
            if($_POST['modalidad'] != '') $queryModalidad = ' modalidadCodigo = '.$_POST['modalidad']. ' ';
            if($_POST['estado'] != '') $queryEstado = ' estado = \''.$_POST['estado'].'\' ';
            if($_POST['procesos'] != '') $queryInterno = ' interno = \''.$_POST['procesos'].'\' ';
            if($_POST['objeto'] != '') $queryObjeto = ' objeto like \'%'.$_POST['objeto'].'%\' ';
            //var_dump($queryEstado);
            if($_POST['modalidad'] != '')
            {
                if($_POST['estado'] != '' || $_POST['procesos'] != '' || $_POST['objeto'] != '')
                {
                    $queryModalidad = $queryModalidad . 'and';
                }
            } 

            if($_POST['estado'] != '')
            {
                if($_POST['procesos'] != '' || $_POST['objeto'] != '')
                {
                    $queryEstado = $queryEstado . 'and';
                }

            }
            if($_POST['procesos'] != '')
            {
                if($_POST['objeto'] != '')
                {
                    $queryInterno = $queryInterno . 'and';
                }
            }
            
            $parametros = "$queryModalidad $queryEstado $queryInterno $queryObjeto";
            
            $_POST['argumentos'] = '@aym_filtro="'.$parametros.'"';
            
        }
        $argumentos = $_POST['argumentos'];
        $url = "http://w2sdg022:8084/sp_ws.asmx?WSDL";
        $client = new SoapClient($url);
        $result = $client->resultado_xml([ "procedimiento" => $procedimiento, "argumentos" => $argumentos]);
        $arr = $result->resultado_xmlResult->any;
        $xml = simplexml_load_string($arr);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        echo json_encode($array);
    }
    /*$procedimiento =  $_POST['procedimientos'];

    if($procedimiento == 76 ){
        //$ingEstdo = intval($_POST['estado']);
        if($_POST['modalidad'] != '') $queryModalidad = ' modalidadCodigo = '.$_POST['modalidad']. ' ';
        if($_POST['estado'] != '') $queryEstado = ' estado = \''.$_POST['estado'].'\' ';
        if($_POST['procesos'] != '') $queryInterno = ' interno = \''.$_POST['procesos'].'\' ';
        if($_POST['objeto'] != '') $queryObjeto = ' objeto like \'%'.$_POST['objeto'].'%\' ';
        //var_dump($queryEstado);
        if($_POST['modalidad'] != '')
        {
            if($_POST['estado'] != '' || $_POST['procesos'] != '' || $_POST['objeto'] != '')
            {
                $queryModalidad = $queryModalidad . 'and';
            }
        } 

        if($_POST['estado'] != '')
        {
            if($_POST['procesos'] != '' || $_POST['objeto'] != '')
            {
                $queryEstado = $queryEstado . 'and';
            }

        }
        if($_POST['procesos'] != '')
        {
            if($_POST['objeto'] != '')
            {
                $queryInterno = $queryInterno . 'and';
            }
        }
        
        $parametros = "$queryModalidad $queryEstado $queryInterno $queryObjeto";
        
        $_POST['argumentos'] = '@aym_filtro="'.$parametros.'"';
        
    }
    $argumentos = $_POST['argumentos'];
    $url = "http://w2sdg022:8084/sp_ws.asmx?WSDL";
    $client = new SoapClient($url);
    $result = $client->resultado_xml([ "procedimiento" => $procedimiento, "argumentos" => $argumentos]);
    $arr = $result->resultado_xmlResult->any;
    $xml = simplexml_load_string($arr);
    $json = json_encode($xml);
    $array = json_decode($json,TRUE);
    echo json_encode($array);*/
}

consult();

echo PHP_EOL;
?>
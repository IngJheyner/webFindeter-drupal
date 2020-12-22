<?php

function consult(){
    try {
        $url = "http://w2sdg022:8083/Servicio/SvcConvocatorias.svc?wsdl";
        
        $client = new SoapClient($url);
        /*$elarray = array(
            'InPrograma'            =>    null,
            'InActividad'           =>    null,
            'InTipoContratacion'    =>    null,
            'InModContratacion'     =>    null,
            'InDepto'               =>    null,
            'InMunicipio'           =>    null,
            'InCuantiaInicial'      =>    null,
            'InCuantiaFinal'        =>    null,
            'InNumProceso'          =>    null,
            'InEstado'              =>    'Adjudicada',
            'InFechaDesde'          =>    null,
            'InFechaHasta'          =>    null

        );*/
        /*$elarray = array(
            'InEstado'              =>    "Adjudicada"
            //'InEstado'              =>    0
        );*/
        //echo json_encode($client->__getFunctions());

 
        // Listas para descargas archivos PDF de cada convocatoria

        define('CONVOCATORIA_DOCUMENTOS_LISTAS', [
            '0' => 'Adjuntos cronogramas por convocatorias', 
            '1' => 'Informes definitivo de requisitos habilitantes', 
            '2' => 'Informes verificación de requisitos habilitantes ', 
            '3' => 'Informes de requerimientos por subsanar', 
            '4' => 'Informes de verificación económica', 
            '5' => 'Informe de calificación y orden de elegibilidad', 
            '6' => 'Adjuntos por convocatorias']);


        /*return $client->GetDocumentoSeleccionLista ([
            'nombreDocumento'   => "PAF-ATF-I-065-2020_EP INTERVENTORÍA BIOGAS VF.pdf",
            'numProc'           => "PAF-ATF-I-065-2020",
            'nombreLista'       => CONVOCATORIA_DOCUMENTOS_LISTAS[6]
        ]);*/
        /*echo json_encode($client->GetDetalleConvocatoria([
            'NumProceso'  => "PAF-ATF-I-065-2020"
        ]));*/
        if($_POST['numero_proceso']){
            echo json_encode($client->GetDetalleConvocatoria([
                'NumProceso'      =>     $_POST['numero_proceso']
            ]));
        }else if($_POST['numero_proceso_documento']){
            echo json_encode($client->GetDocumentosConvocatoria([
                'NumProceso'      =>     $_POST['numero_proceso_documento']
            ]));
        }else if($_POST['numero_proceso_documento_desc']){
            //if($_GET['nombdoc']){
            $respuesta = $client->getDocumento([
                'NombreDocumento'   =>  $_POST['numero_proceso_documento_desc']['nombredocu'],
                'NumProc'           =>  $_POST['numero_proceso_documento_desc']['numproceso'],
                //'NombreDocumento'   =>  $_GET['nombdoc'],
                //'NumProc'           =>  $_GET['numpro'],
            ]);
            //var_dump($respuesta->GetDocumentoResult);
            //echo $file;
            header('Content-type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.$_POST['numero_proceso_documento_desc']['nombredocu'].'"');
            header('Content-Transfer-Enconding: binary');
            //return file_put_contents($_POST['numero_proceso_documento_desc']['nombredocu'], $file);
            //return file_put_contents(base64_decode($data), $_GET['nombdoc']);
            //echo $file;
            echo base64_encode($respuesta->GetDocumentoResult);
        }else{
            echo json_encode($client->GetListadoConvocatorias([
                'InEstado'              =>    "Convocada"
            ]));
        }

    } catch ( SoapFault $e ) {
        echo $e->getMessage();
    }
}

consult();

echo PHP_EOL;
?>
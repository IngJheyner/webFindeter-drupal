<?php

    $ruta = 'F:/copia-media/';
	set_time_limit(6000);

    function cantidad_archivos($path, $extension_archivo)
    {
        $match = glob($path . "*." . $extension_archivo);
        $num_arc = 0;

        if($match)
        {
            $num_arc = count($match);
        }
        return $match;
    }


    function move_to($origen,$destino, $nom_file)
    {
        //print_r($origen);
        //print_r('moviendo.. '. $origen . '  a  '. $destino . '/' . $nom_file);
        if (!file_exists($destino)) 
        {
            mkdir($destino, 0777, true);
            
        }
        try {
            rename($origen,$destino. '/' . $nom_file);

        } catch (Exception $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
        }
        
        //unlink($origen);
    }

    function traslation_files($files, $path)
    {
        foreach($files as $file)
        {
            $convocatoria = explode("@", end(explode("/", $file)))[0];
            //var_dump($path);
            $name_file = explode("@", $file)[1]; 
            $origen = $file;
            $destino = $path.$convocatoria;

            move_to($origen, $destino, $name_file);
        } 

    }    

    $archivos_pdf = cantidad_archivos($ruta, 'xlsx');

    echo traslation_files($archivos_pdf, $ruta);

    /*$archivos_xsl = cantidad_archivos($ruta, 'xsl');

    $archivos_doc = cantidad_archivos($ruta, 'docs');*/








?>
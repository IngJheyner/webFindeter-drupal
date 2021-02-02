<?php



$serverName = "10.10.3.73\\SQL2016, 52644";

$connectionInfo = array("Database" => "pre_webfinde21", "UID" => "CS-ECO_DIG-WM", "PWD" => "9F&c[*0q_=hd/C");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if($conn){
	echo "conexion establecida";
	
}else{
	echo "conexion fallida";
	die(print_r(sqlsrv_errors(), true));

}

sqlsrv_close($conn);

?>
<?php

    include("configuracion.php");
	
	$desde	=	$_GET['iniTime'];
	$hasta	=	$_GET['endTime'];
	$tabla	=	$_SESSION['execution'];
	
	$response = array('code' => "", 'message' => "");
	
	$enlace = mysqli_connect($db_host, $db_user, $db_password, $db_database);

	/* verificar la conexión */
	if (mysqli_connect_errno()) {
		$message = "Fallo la conexion: ".mysqli_connect_error();
		
		$response['code'] = "001"; // code 001 error de conexión
        $response['message'] = $message;       
        die(json_encode($response));
		
		exit();
	}
 
	/* Escapeo las variables */ 
	$tabla = mysqli_real_escape_string($enlace, $tabla);
	$desde = mysqli_real_escape_string($enlace, $desde);
	$hasta = mysqli_real_escape_string($enlace, $hasta);
 
	$query = "SELECT label, count(*) as Samples, AVG(`elapsed`) as AVG, MAX(`elapsed`) as MAX, MIN(`elapsed`) as MIN, STD(`elapsed`) as StdDev FROM $tabla WHERE (FROM_UNIXTIME(jtimestamp div 1000) BETWEEN \"$desde\" AND \"$hasta\") AND responseCode=\"200\" GROUP BY label ORDER BY label";
 
    $result = mysqli_query($enlace, $query);
	
//	echo $query;
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] = "002"; // code 002 error de query
        $response['message'] = $message;        
        die(json_encode($response));
		
		exit();
        
    }else{
		
		$data = array();
        $mensaje = array();
		while($row = $result->fetch_object()){
			
			$data["label"]      = $row->label; 
            $data["Samples"]    = $row->Samples; 
            $data["AVG"]      	= round($row->AVG,2); 
            $data["MAX"]      	= $row->MAX; 			
            $data["MIN"]      	= $row->MIN; 			
            $data["StdDev"]     = round($row->StdDev,2); 
			
            $mensaje[]=$data;
			
        }
		
		$response['code'] = "000";
        $response['message'] = $mensaje;
        echo json_encode($response);
		
		mysqli_close($enlace);

    }



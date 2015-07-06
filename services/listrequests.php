<?php

    include("configuracion.php");
	
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
 
	$query = "SELECT DISTINCT label FROM $tabla ORDER BY label";
 
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
            			
            $mensaje[]=$data;
			
        }
		
		$response['code'] = "000";
        $response['message'] = $mensaje;
        echo json_encode($response);
		
		mysqli_close($enlace);

    }



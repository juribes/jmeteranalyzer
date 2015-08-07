<?php

    include("configuracion.php");
	
    $response = array('code' => "", 'message' => "");

    if (isset($_SESSION['execution'])){
        $tabla	=	$_SESSION['execution'];
    }else{
        $response['message'] = "You need to select a test/execution";
        $response['code'] = "003";
        die(json_encode($response));
    }

    ob_start();
    $enlace = mysqli_connect($db_host, $db_user, $db_password, $db_database);
    ob_end_clean();

    /* Verify the connection */
    if (mysqli_connect_errno()) {
        $message = utf8_encode("Error in the connection: ".mysqli_connect_error());	
        $response['code'] = "001"; // code 001 error de conexión
        $response['message'] = $message; 
        die(json_encode($response));
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



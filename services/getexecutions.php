<?php

    include("configuracion.php");
	
	$response = array('code' => "", 'message' => "");
	
	ob_start();
	$enlace = mysqli_connect($db_host, $db_user, $db_password, $db_database);
	ob_end_clean();
	
	/* Verify the connection */
	if (mysqli_connect_errno()){
            $message = utf8_encode("Error in the connection: ".mysqli_connect_error());	
            $response['code'] = "001"; // code 001 error de conexión
            $response['message'] = $message; 
            die(json_encode($response));
	}

	/* Escapeo las variables */ 
	$db_database = mysqli_real_escape_string($enlace, $db_database);
 
	$query = "SHOW tables FROM $db_database";
 
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = utf8_encode('Invalid query: ' . mysql_error() . "\nComplete query: ".$query);
		$response['code'] = "002"; // code 002 error de query
        $response['message'] = $message;  
        die(json_encode($response));
    }else{
		
        $data = array();
        $tables = array();
        while($row = $result->fetch_object()){
            $data["name"] = $row->Tables_in_k12_results; 			
            $tables[]=$data;
        }
		
        if (isset($_SESSION['execution'])){
                $mensaje['selected'] = $_SESSION['execution'];
        }else{
                $mensaje['selected'] = "";
        }
		
        $response['code'] 	= "000";
        $mensaje['tables']	= $tables;
        $response['message']	= $mensaje;
        echo json_encode($response);
		
	mysqli_close($enlace);

    }
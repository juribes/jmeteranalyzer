<?php

    include("configuracion.php");

	$response = array('code' => "", 'message' => "");
	
	if (isset($_SESSION['execution'])){
            $testname =	$_SESSION['execution'];
            $testid	  =	$_SESSION['executionID'];
	}else{
            $response['message']    = "You need to select a test/execution";
            $response['code']       = "003";
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
	$testid = mysqli_real_escape_string($enlace, $testid);
 
	$query = "SELECT starttime, finishtime, duration, transaccount, minRT, maxRT, avgRT, avgTPS, maxTPS FROM tbl_tests WHERE id_test = $testid";
 
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Invalid GI query 1: ' . mysql_error() . "\n";
        $message .= 'Full query: ' . $query;
            
		$response['code'] = "002"; // code 002 error de query
        $response['message'] = $message;
		mysqli_close($enlace);
        die(json_encode($response));
        
    }else{
        
		$row = $result->fetch_object();
		$mensaje["finishtime"]  = $row->finishtime; 
		$mensaje["starttime"]   = $row->starttime; 
		$mensaje["duration"]    = $row->duration;
		$mensaje["transcount"] 	= $row->transaccount;
		$mensaje["maxrt"]      	= $row->maxRT; 			
		$mensaje["minrt"]     	= $row->minRT; 	
		$mensaje["avgrt"]		= $row->avgRT; 	
        $mensaje["maxtps"]		= $row->maxTPS; 
        $mensaje["avgtps"]		= $row->avgTPS;				
    }
	
	/* Response codes */
    $query = "SELECT respcodecode, numberofresponses FROM tbl_responsecodes WHERE tbl_tests_id_test = $testid AND label= 'all_test'";
 
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Invalid response codes query: ' . mysql_error() . "\n";
        $message .= 'Full query: ' . $query;
            
		$response['code'] = "002"; // code 002 error de query
        $response['message'] = $message;        
        die(json_encode($response));
        
    }else{
        
		$data = array();
        $mensajeerror = array();
	
        while($row = $result->fetch_object()){
            $data["responseCode"]   = $row->respcodecode; 
            $data["count"]          = $row->numberofresponses; 
            $mensajeerror[]=$data;		
        }		
    }
	
	$mensaje["error"] = $mensajeerror;
		
	$response['code'] = "000";
	$response['message'] = $mensaje;
	
	mysqli_close($enlace);
	echo json_encode($response);
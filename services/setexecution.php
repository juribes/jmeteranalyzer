<?php

    include("configuracion.php");
	
	$testid	= $_GET['execution'];
 
	$response = array('code' => "", 'message' => "");

        if($testid=="undefined"){
            $response['message']    = "You need to select a test/execution";
            $response['code']       = "003";
            die(json_encode($response));
        }
        
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
 
	$query = "SELECT name, starttime, finishtime FROM tbl_tests WHERE id_test=".$testid;

    $result = mysqli_query($enlace, $query);
	
	if (!$result){
		$response['code'] = "002";
		$response['message'] = "Error in the query Get ID: ".mysqli_error($enlace)."<br>".$query;
		mysqli_close($enlace);
		die(json_encode($response));
	}else{
		$row = $result->fetch_object();
		$testname = $row->name; 
		$mensaje["starttime"]   = $row->starttime; 
		$mensaje["finishtime"]  = $row->finishtime; 
	}
		
	$_SESSION['execution']		= $testname;
	$_SESSION['executionID']	= $testid;
	
	$response['code'] = "000";
	$mensaje['testname'] = $testname;

	$response['message'] = $mensaje;
	
	mysqli_close($enlace);
	echo json_encode($response);
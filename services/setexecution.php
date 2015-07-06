<?php

    include("configuracion.php");
	
	$tabla	=	$_GET['execution'];

	$response = array('code' => "", 'message' => "");
	
	$_SESSION['execution']	= $tabla;
 
	$response['code'] = "000";
	$mensaje['message'] = $tabla;

	$response['message'] = $mensaje;
	echo json_encode($response);
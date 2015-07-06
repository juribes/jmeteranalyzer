<?php

    include("configuracion.php");
	
	$tabla	=	$_GET['execution'];

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
 
	$query = "CREATE TABLE IF NOT EXISTS $tabla (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jtimestamp` bigint(13) NOT NULL,
  `elapsed` int(11) NOT NULL,
  `label` varchar(345) NOT NULL,
  `responsecode` varchar(50) NOT NULL,
  `threadname` varchar(20) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `bytes` int(11) NOT NULL,
  `grpthreads` int(11) NOT NULL,
  `allthreads` int(11) NOT NULL,
  `latency` int(11) NOT NULL,
  `samplecount` int(11) NOT NULL,
  `errorcount` int(11) NOT NULL,
  `hostname` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB;";
 
    $result = mysqli_query($enlace, $query);
	
	if (mysqli_query($enlace, $query)) {
		$response['code'] = "000";
		$mensaje['message'] = $tabla;
		$_SESSION['execution']	= $tabla;
	} else {
		$response['code'] = "002";
		$mensaje['message'] = "Error creating table: " . mysqli_error($conn);
	}

	$response['message'] = $mensaje;
	echo json_encode($response);
	
	mysqli_close($enlace);

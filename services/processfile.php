<?php

    include("configuracion.php");
	
	$filename	=	$_GET['filename'];
	$servername	=	$_GET['servername'];
	$tabla	=	$_SESSION['execution'];
	
	$response = array('code' => "", 'message' => "");
	
	$MyLog = fopen(".\\Uploads\\".$filename, "r");
	
	$enlace = mysqli_connect($db_host, $db_user, $db_password, $db_database);
	
	/* verificar la conexión */
	if (mysqli_connect_errno()) {
		$message = "Fallo la conexion: ".mysqli_connect_error();
		
		$response['code'] = "001"; // code 001 error de conexión
        $response['message'] = $message;       
        die(json_encode($response));
		
		exit();
	}
	
	fgetcsv($MyLog);
	while (!feof($MyLog)) {
		$registro = fgetcsv($MyLog);
		if ($registro!=null){
			if($registro[3]==""){
				$registro[3]="No response code";
			}
			if ($registro!=NULL){
				$sql[] = '('.mysqli_real_escape_string($enlace, $registro[0]).', '.mysqli_real_escape_string($enlace, $registro[1]).', "'.mysqli_real_escape_string($enlace, $registro[2]).'", "'.mysqli_real_escape_string($enlace, $registro[3]).'", "'.mysqli_real_escape_string($enlace, $registro[4]).'", '.mysqli_real_escape_string($enlace, $registro[5]).', '.mysqli_real_escape_string($enlace, $registro[6]).', '.mysqli_real_escape_string($enlace, $registro[7]).', '.mysqli_real_escape_string($enlace, $registro[8]).', '.mysqli_real_escape_string($enlace, $registro[9]).', '.mysqli_real_escape_string($enlace, $registro[10]).', '.mysqli_real_escape_string($enlace, $registro[11]).', "'.mysqli_real_escape_string($enlace, $servername).'")';
			}
		}
	}
	//echo 'INSERT INTO '.$tabla.' (                         jtimestamp,                                           elapsed,                                               label,                                                 responseCode,                                           threadName,                                             success,                                             bytes,                                                grpThreads,                                           allThreads,                                           Latency,                                              SampleCount,                                           ErrorCount,                                              Hostname) ;

	$query = 'INSERT INTO '.$tabla.' (jtimestamp, elapsed, label, responseCode, threadName, success, bytes, grpThreads, allThreads, Latency, SampleCount, ErrorCount, Hostname)  VALUES '.implode(',', $sql);
	
	$result = mysqli_query($enlace, $query);
	
	if ($result){
		$response['code'] = "000";
		$response['message'] = mysqli_affected_rows($enlace)." registros insertados para el servidor: ".$servername." desde el archivo: ".$filename." en la tabla: ".$tabla;
	
		
	}else{
		$response['code'] = "002";
		$response['message'] = "Error en la Query: ".mysqli_error($enlace)."<br>".$query;//$query;
	}
	
	mysqli_close($enlace);
	fclose($MyLog);
	
	echo json_encode($response);
	

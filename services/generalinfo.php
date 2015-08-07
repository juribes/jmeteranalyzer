<?php

    include("configuracion.php");

	$response = array('code' => "", 'message' => "");
	
	if (isset($_SESSION['execution'])){
		$tabla	=	$_SESSION['execution'];
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
	$tabla = mysqli_real_escape_string($enlace, $tabla);
 
	$query = "SELECT FROM_UNIXTIME(MAX(`jtimestamp`) div 1000) as MAXjtimestamp, 
	FROM_UNIXTIME(MIN(`jtimestamp`) div 1000) as MINjtimestamp, 
	CONVERT_TZ(FROM_UNIXTIME(TIMESTAMPDIFF(SECOND, FROM_UNIXTIME(MIN(`jtimestamp`) div 1000), FROM_UNIXTIME(MAX(`jtimestamp`) div 1000))),'-5:00','+00:00') as duration, 
	count(*) as totaltrans, 
	MAX(`elapsed`) as maxRT, 
	MIN(`elapsed`) as minRT, 
	AVG(`elapsed`) avgRT
FROM 
	$tabla";
 
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
        
		$row = $result->fetch_object();
		
		$duracion = explode(" ",$row->duration);
		$mensaje["finishtime"]  = $row->MAXjtimestamp; 
		$mensaje["starttime"]   = $row->MINjtimestamp; 
		$mensaje["duration"]    = $duracion[1];
		$mensaje["transcount"] 	= $row->totaltrans;
		$mensaje["maxrt"]      	= $row->maxRT; 			
		$mensaje["minrt"]     	= $row->minRT; 	
		$mensaje["avgrt"]		= round($row->avgRT,2); 	
			
    }
/* Otros MAX TPS, AVG TPS */
	$query = "SELECT MAX(temptable.TPS) as maxTPS, AVG(temptable.TPS) as avgTPS 
	FROM (SELECT COUNT(*) as TPS FROM $tabla GROUP BY SECOND(FROM_UNIXTIME(jtimestamp div 1000))) as temptable";
 
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

        $row = $result->fetch_object();
        $mensaje["maxtps"]	= $row->maxTPS; 
        $mensaje["avgtps"]	= $row->avgTPS;	
		
    }
/*  */
    $query = "SELECT responseCode, COUNT(*) as count FROM $tabla GROUP BY responseCode";
 
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
        $mensajeerror = array();
	
        while($row = $result->fetch_object()){
			
            $data["responseCode"]   = $row->responseCode; 
            $data["count"]          = $row->count; 
           			
            $mensajeerror[]=$data;		
        }		
		
    }
	
	$mensaje["error"]=$mensajeerror;
		
	$response['code'] = "000";
	$response['message'] = $mensaje;
	echo json_encode($response);
	
	mysqli_close($enlace);





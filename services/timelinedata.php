<?php

    include("configuracion.php");
	
	$desde	=	$_GET['iniTime'];
	$hasta	=	$_GET['endTime'];
	$request=	$_GET['request'];
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
	$tabla 		= mysqli_real_escape_string($enlace, $tabla);
	$desde 		= mysqli_real_escape_string($enlace, $desde);
	$hasta 		= mysqli_real_escape_string($enlace, $hasta);
	$request 	= mysqli_real_escape_string($enlace, $request);
 
//	$query = "SELECT label, FROM_UNIXTIME(jtimestamp div 1000) as Date, AVG(elapsed) as AVG, count(*) as TPS FROM $tabla WHERE (FROM_UNIXTIME(jtimestamp div 1000) BETWEEN \"$desde\" AND \"$hasta\") and label =\"$request\" AND responseCode=\"200\" GROUP BY label, HOUR(Date), MINUTE(Date) ORDER BY Date";
 	$query = "SELECT label, FROM_UNIXTIME(jtimestamp div 1000) as Date, AVG(elapsed) as AVG, count(*) as TPS FROM $tabla WHERE (FROM_UNIXTIME(jtimestamp div 1000) BETWEEN \"$desde\" AND \"$hasta\") and label =\"$request\" AND responseCode=\"200\" GROUP BY label, HOUR(Date), MINUTE(Date), SECOND(Date) ORDER BY Date";

    $result = mysqli_query($enlace, $query);


// SELECT label, FROM_UNIXTIME(jtimestamp div 1000) as Date, AVG(elapsed) as AVG, count(*) as TPS FROM $tabla WHERE FROM_UNIXTIME(`jtimestamp` div 1000) <= "2015-06-24 10:24:53" and label ="1 /Shibboleth.sso/Login" GROUP BY label, HOUR(Date), MINUTE(Date) ORDER BY Date 	
//echo $query;
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;        
        die(json_encode($response));
		
		exit();
        
    }else{
		
		$data = array();
		$mensaje = array();
		while($row = $result->fetch_object()){
			$data["label"] 	= $row->label;
			$data["time"]	= $row->Date;
			$data["AVG"]	= round($row->AVG,2);
			$data["TPS"] 	= intval($row->TPS);
			
            $mensaje[]=$data;
        }
	
		$response['code'] = "000";
        $response['message'] = $mensaje;
        echo json_encode($response);

    }
	mysqli_close($enlace);


<?php

    include("configuracion.php");
	
	$desde	 =	$_GET['iniTime'];
	$hasta	 =	$_GET['endTime'];
	$request =	$_GET['request'];
	
	$response = array('code' => "", 'message' => "");
	
	if (isset($_SESSION['execution'])){
		$testname =	$_SESSION['execution'];
		$testid	  =	$_SESSION['executionID'];
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
	$testlogtable 	= mysqli_real_escape_string($enlace, "testlog".$testid);
	$desde 			= mysqli_real_escape_string($enlace, $desde);
	$hasta 			= mysqli_real_escape_string($enlace, $hasta);
	$request 		= mysqli_real_escape_string($enlace, $request);
 
//	$query = "SELECT label, FROM_UNIXTIME(jtimestamp div 1000) as Date, AVG(elapsed) as AVG, count(*) as TPS FROM $testlogtable WHERE (FROM_UNIXTIME(jtimestamp div 1000) BETWEEN \"$desde\" AND \"$hasta\") and label =\"$request\" AND responseCode=\"200\" GROUP BY label, HOUR(Date), MINUTE(Date) ORDER BY Date";
 	$query = "SELECT label, FROM_UNIXTIME(jtimestamp div 1000) as Date, AVG(elapsed) as AVG, count(*) as TPS FROM $testlogtable WHERE (FROM_UNIXTIME(jtimestamp div 1000) BETWEEN \"$desde\" AND \"$hasta\") and label =\"$request\" AND responseCode=\"200\" GROUP BY label, HOUR(Date), MINUTE(Date), SECOND(Date) ORDER BY Date";

    $result = mysqli_query($enlace, $query);


// SELECT label, FROM_UNIXTIME(jtimestamp div 1000) as Date, AVG(elapsed) as AVG, count(*) as TPS FROM $testlogtable WHERE FROM_UNIXTIME(`jtimestamp` div 1000) <= "2015-06-24 10:24:53" and label ="1 /Shibboleth.sso/Login" GROUP BY label, HOUR(Date), MINUTE(Date) ORDER BY Date 	
//echo $query;
	
    if (!$result) {
        $message  = 'Invalid query Timeline Data: ' . mysql_error() . "\n";
        $message .= 'Full query: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;  
		mysqli_close($enlace);		
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


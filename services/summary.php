<?php

    include("configuracion.php");
	
	$desde	=	$_GET['iniTime'];
	$hasta	=	$_GET['endTime'];

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
	$testlogtable = mysqli_real_escape_string($enlace, "testlog".$testid);
	$desde = mysqli_real_escape_string($enlace, $desde);
	$hasta = mysqli_real_escape_string($enlace, $hasta);
 
// HTTP 200 and 302
//	$query = "SELECT label, count(*) as Samples, AVG(`elapsed`) as AVG, MAX(`elapsed`) as MAX, MIN(`elapsed`) as MIN, STD(`elapsed`) as StdDev FROM $testlogtable WHERE (FROM_UNIXTIME(jtimestamp div 1000) BETWEEN \"$desde\" AND \"$hasta\") AND (responseCode=\"200\" or  responseCode=\"302\")GROUP BY label ORDER BY label";

// HTTP 200 only
// 	$query = "SELECT label, count(*) as Samples, AVG(`elapsed`) as AVG, MAX(`elapsed`) as MAX, MIN(`elapsed`) as MIN, STD(`elapsed`) as StdDev FROM $testlogtable WHERE (FROM_UNIXTIME(jtimestamp div 1000) BETWEEN \"$desde\" AND \"$hasta\") AND responseCode=\"200\" GROUP BY label ORDER BY label";

// HTTP all
	$query = "SELECT label, count(*) as Samples, AVG(`elapsed`) as AVG, MAX(`elapsed`) as MAX, MIN(`elapsed`) as MIN, STD(`elapsed`) as StdDev, SUM(case when success = 0 then 1 else 0 end ) as numerror  FROM $testlogtable WHERE (FROM_UNIXTIME(jtimestamp div 1000) BETWEEN \"$desde\" AND \"$hasta\") GROUP BY label ORDER BY label";
 
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Invalid query SUMMARY: ' . mysql_error() . "\n";
        $message .= 'Full query: ' . $query;
            
		$response['code'] = "002"; // code 002 error de query
        $response['message'] = $message;
		mysqli_close($enlace);			
        die(json_encode($response));
		
    }else{
		
		$data = array();
        $mensaje = array();
		while($row = $result->fetch_object()){
			
			$data["label"]      = $row->label; 
            $data["Samples"]    = $row->Samples; 
            $data["AVG"]      	= round($row->AVG,2); 
            $data["MAX"]      	= $row->MAX; 			
            $data["MIN"]      	= $row->MIN; 			
            $data["StdDev"]     = round($row->StdDev,2); 
			$data["numerror"]   = $row->numerror; 
			$data["perror"]     = round(100*$row->numerror/$row->Samples,2); 			
			
            $mensaje[]=$data;
        }
		
		$response['code'] = "000";
        $response['message'] = $mensaje;
        echo json_encode($response);
		
		mysqli_close($enlace);

    }
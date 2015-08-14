<?php

    include("configuracion.php");
	
	$response = array('code' => "", 'message' => "");
	
	if (isset($_SESSION['execution'])){
		$testname =	$_SESSION['execution'];
		$testid	  =	$_SESSION['executionID'];
	}else{
		$response['message'] = "You need to select a test/execution";
		$response['code'] = "003";
		die(json_encode($response));
	}
	
	$dataPointsAll 		= array();
	$dataPoints200		= array();
	$dataPointsAllvec 	= array();
	$dataPoints200vec	= array();

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
		
	/*Percentil 100 all*/
	$query = "SELECT percentil, responsetime, resptype FROM tbl_percentiles WHERE label = 'all_test' AND tbl_tests_id_test = ".$testid;
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Invalid query PERCENTIL: ' . mysql_error() . "\n";
        $message .= 'Full query: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;        
        die(json_encode($response));
        
    }else{
		while($row = $result->fetch_object()){
			if ($row->resptype == "all"){
				$dataPointsAll['y'] 		= intval($row->responsetime);
				$dataPointsAll['label']		= $row->percentil;
				$dataPointsAllvec[]			= $dataPointsAll;
			}else{
				$dataPoints200['y']			= intval($row->responsetime);
				$dataPoints200['label']		= $row->percentil;
				$dataPoints200vec[]			= $dataPoints200;
			}
        }		
    }
	
	$gTitle['text'] 	= "RT Percentiles";
	$gTitle['fontSize']     = 30;
	$gAxisX['title']	= "Percentil";
	$gAxisX['titleFontSize']= 20;
	$gAxisX['labelFontSize']= 15;
	$gAxisY['title']	= "Responde time (ms)";	
	$gAxisY['titleFontSize']= 20;
	$gAxisY['labelFontSize']= 15;
	
	$dataSet1['type'] 		= "bar";
	$dataSet1['legendText'] 	= "All requests"; //Legend name
	$dataSet1['name'] 		= "All requests"; //tool tip name
	$dataSet1['showInLegend'] 	= true;
	$dataSet1['dataPoints'] 	= $dataPointsAllvec;
	
	$dataSet2['type'] 		= "bar";
	$dataSet2['legendText'] 	= "Successful requests"; //Legend name
	$dataSet2['name'] 		= "Successful requests"; //tool tip name
	$dataSet2['showInLegend'] 	= true;
	
	$dataSet2['dataPoints']	= $dataPoints200vec;
	
	$gdata[0]	=	$dataSet1;
	$gdata[1]	=	$dataSet2;
			
	$message['title'] 	= $gTitle;
    $message['zoomEnabled'] = true;
    $message['exportFileName'] = "Percentiles";
    $message['exportEnabled'] = true;
	$message['axisX'] 	= $gAxisX;
	$message['axisY'] 	= $gAxisY;
	$message['data'] 	= $gdata;	
		
	$response['code'] = "000";
	$response['message'] = $message;
	echo json_encode($response);
	
	mysqli_close($enlace);
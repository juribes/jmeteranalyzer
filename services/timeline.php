<?php

    include("configuracion.php");
	
	$desde	=	$_GET['iniTime'];
	$hasta	=	$_GET['endTime'];
	$request=	$_GET['request'];
	$tabla	=	$_SESSION['execution'];
	
	$response = array('code' => "", 'message' => "");
	
	ob_start();
	$enlace = mysqli_connect($db_host, $db_user, $db_password, $db_database);
	ob_end_clean();

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
 
	$query = "SELECT label, FROM_UNIXTIME(jtimestamp div 1000) as Date, AVG(elapsed) as AVG, count(*) as TPS FROM $tabla WHERE (FROM_UNIXTIME(jtimestamp div 1000) BETWEEN \"$desde\" AND \"$hasta\") and label =\"$request\" AND responseCode=\"200\" GROUP BY label, HOUR(Date), MINUTE(Date) ORDER BY Date";
 
    $result = mysqli_query($enlace, $query);
	
//echo $query;
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;        
        die(json_encode($response));
		
		exit();
        
    }else{
		
		$gTitle['text'] 		= $request;
		$gTitle['fontSize'] 	= 20;
        $gToolTip['shared']		= true;
		$gToolTip['enabled']	= true;
		$gAxisX['title']		= "Time";
		$gAxisX['titleFontSize']= 20;
		$gAxisX['labelFontSize']= 15;
		$gAxisY['includeZero']	= false;
		$gAxisY['title']		= "Responde time (ms)";
		$gAxisY['titleFontSize']= 20;
		$gAxisY['labelFontSize']= 15;
		$gAxisY2['includeZero']	= false;
		$gAxisY2['title']		= "TPS (Req/min)";
		$gAxisY2['titleFontSize']= 20;
		$gAxisY2['labelFontSize']= 15;
			
		$dataPointsElapse 	= array();
		$dataPointsTPS 		= array();
		
		$textElapse = "";
        $textTPS 	= "";
		
		while($row = $result->fetch_object()){
			$time=explode(" ",$row->Date);
			$dataElapse["x"]	= $time[0]."T". $time[1];
			$dataElapse["y"]	= round($row->AVG,2);
			$dataTPS["x"] 		= $time[0]."T". $time[1];
			$dataTPS["y"] 		= intval($row->TPS);
			
            $textElapse[] 		= $dataElapse;
			$textTPS[] 			= $dataTPS;
        }
	
		$dataLine1['type'] 			= "line";
		$dataLine1['legendText'] 	= $request." RT AVG"; //Legend name
		$dataLine1['name'] 			= "RT AVG"; //tool tip name
		$dataLine1['showInLegend'] 	= true;
		$dataLine1['dataPoints'] 	= $textElapse;
		
		$dataLine2['type'] 			= "line";
		$dataLine2['legendText'] 	= $request." TPS"; //Legend name
		$dataLine2['name'] 			= "TPS"; //tool tip name
		$dataLine2['showInLegend'] 	= true;
		$dataLine2['axisYType'] 	= "secondary";
		$dataLine2['dataPoints']	= $textTPS;

		$gdata[0]	=	$dataLine1;
		$gdata[1]	=	$dataLine2;
			
		$message['title'] 	= $gTitle;
                $message['zoomEnabled'] = true;                
		$message['toolTip']     = $gToolTip;
		$message['axisX'] 	= $gAxisX;
		$message['axisY'] 	= $gAxisY;
		$message['axisY2'] 	= $gAxisY2;
		$message['data'] 	= $gdata;
		
		$response['code'] = "000";
        $response['message'] = $message;
        echo json_encode($response);
		


    }
	mysqli_close($enlace);


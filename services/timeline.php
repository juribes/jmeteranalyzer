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
	$testlogtable	= mysqli_real_escape_string($enlace, "testlog".$testid);
	$desde 		= mysqli_real_escape_string($enlace, $desde);
	$hasta 		= mysqli_real_escape_string($enlace, $hasta);
	$request 	= mysqli_real_escape_string($enlace, $request);
 
        
        if ($_SESSION['multifile']){
            $query = "SELECT label, FROM_UNIXTIME(jtimestamp div 1000) as Date, AVG(elapsed) as AVG, count(*) as TPS FROM $testlogtable WHERE (FROM_UNIXTIME(jtimestamp div 1000) BETWEEN \"$desde\" AND \"$hasta\") and label =\"$request\" AND responseCode=\"200\" GROUP BY label, HOUR(Date), MINUTE(Date) ORDER BY Date";
        }else{
            $query = "SELECT label, FROM_UNIXTIME(jtimestamp div 1000) as Date, AVG(elapsed) as AVG, count(*) as TPS, AVG(allthreads) as VU FROM $testlogtable WHERE (FROM_UNIXTIME(jtimestamp div 1000) BETWEEN \"$desde\" AND \"$hasta\") and label =\"$request\" AND responseCode=\"200\" GROUP BY label, HOUR(Date), MINUTE(Date) ORDER BY Date";
        }
    
        $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Invalid query Timeline: ' . mysql_error() . "\n";
        $message .= 'Full query: ' . $query;
        $response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;  
        mysqli_close($enlace);		
        die(json_encode($response));
		
    }else{
		
        $gTitle['text'] 	= $request;
        $gTitle['fontSize'] 	= 20;
        $gLegend['fontFamily']  = "Helvetica";
        $gLegend['cursor']      = "pointer";
        $gLegend['itemclick']   = '';
        $gToolTip['shared']	= true;
        $gToolTip['enabled']	= true;
        $gAxisX['title']	= "Time";
        $gAxisX['titleFontSize']= 20;
        $gAxisX['labelFontSize']= 15;
        $gAxisY['includeZero']	= false;
        $gAxisY['title']	= "Responde time (ms)";
        $gAxisY['titleFontSize']= 20;
        $gAxisY['labelFontSize']= 15;
        $gAxisY2['includeZero']	= false;
        $gAxisY2['title']	= "TPM (Req/min) & VU";
        $gAxisY2['titleFontSize']= 20;
        $gAxisY2['labelFontSize']= 15;

        if ($_SESSION['multifile']){
            $dataPointsElapse 	= array();
            $dataPointsTPS 	= array();

            $textElapse = "";
            $textTPS    = "";

            while($row = $result->fetch_object()){
                $time=explode(" ",$row->Date);
                $dataElapse["x"]	= $time[0]."T". $time[1];
                $dataElapse["y"]	= round($row->AVG,2);
                $dataTPS["x"] 	        = $time[0]."T". $time[1];
                $dataTPS["y"] 	        = intval($row->TPS);	
                $textElapse[] 	        = $dataElapse;
                $textTPS[] 		= $dataTPS;
            }
        }else{
            $dataPointsElapse 	= array();
            $dataPointsTPS 	= array();
            $dataPointsVU       = array();

            $textElapse = "";
            $textTPS    = "";
            $textVU     = "";

            while($row = $result->fetch_object()){
                $time=explode(" ",$row->Date);
                $dataElapse["x"]   = $time[0]."T". $time[1];
                $dataElapse["y"]   = round($row->AVG,2);
                $dataTPS["x"] 	   = $time[0]."T". $time[1];
                $dataTPS["y"] 	   = intval($row->TPS);
                $dataPointsVU["x"] = $time[0]."T". $time[1];
                $dataPointsVU["y"] = round($row->VU,0);
                $textElapse[] 	   = $dataElapse;
                $textTPS[] 	   = $dataTPS;
                $textVU[]          = $dataPointsVU;
            }
        }
        
        $dataLine1['type'] 		= "line";
        $dataLine1['color']             = "#369EAD";
        $dataLine1['legendText'] 	= $request.": AVG RT"; //Legend name
        $dataLine1['name'] 		= "AVG RT"; //tool tip name
        $dataLine1['showInLegend'] 	= true;
        $dataLine1['dataPoints'] 	= $textElapse;

        $dataLine2['type'] 		= "line";
        $dataLine2['color']             = "#C24642";
        $dataLine2['legendText'] 	= $request." TPM"; //Legend name
        $dataLine2['name'] 		= "TPM"; //tool tip name
        $dataLine2['showInLegend'] 	= true;
        $dataLine2['axisYType'] 	= "secondary";
        $dataLine2['dataPoints']	= $textTPS;

        if (!$_SESSION['multifile']){
            $dataLine3['type'] 		= "area";
            $dataLine3['color']         = "#7F6084";
            $dataLine3['legendText'] 	= "VU"; //Legend name
            $dataLine3['name'] 		= "VU"; //tool tip name
            $dataLine3['showInLegend'] 	= true;
            $dataLine3['axisYType'] 	= "secondary";
            $dataLine3['dataPoints']	= $textVU;    
            $gdata[0]                   = $dataLine3;
            $gdata[1]	                = $dataLine1;
            $gdata[2]	                = $dataLine2;
        }else{
            $gdata[0]	=	$dataLine1;
            $gdata[1]	=	$dataLine2;
        }
        
        $message['title']          = $gTitle;
        $message['legend']         = $gLegend;
        $message['zoomEnabled']    = true;
        $message['exportFileName'] = "Response time over time";
        $message['exportEnabled']  = true;
        $message['toolTip']        = $gToolTip;
        $message['axisX']          = $gAxisX;
        $message['axisY']          = $gAxisY;
        $message['axisY2']         = $gAxisY2;
        $message['data']           = $gdata;

        $response['code'] = "000";
        $response['message'] = $message;
        echo json_encode($response);
		
    }
    mysqli_close($enlace);


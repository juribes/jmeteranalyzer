<?php

    include("configuracion.php");

    $response = array('code' => "", 'message' => "");

    if (isset($_SESSION['execution'])){
        $testname =	$_SESSION['execution'];
        $testid	  =	$_SESSION['executionID'];
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
    $testid = mysqli_real_escape_string($enlace, $testid);
    $testlogtable = mysqli_real_escape_string($enlace, "testlog".$testid);

    $query = "SELECT starttime, finishtime, duration, transaccount, minRT, maxRT, avgRT, avgTPS, maxTPS FROM tbl_tests WHERE id_test = $testid";
 
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Invalid GI query 1: ' . mysql_error() . "\n";
        $message .= 'Full query: ' . $query;
            
        $response['code'] = "002"; // code 002 error de query
        $response['message'] = $message;
        mysqli_close($enlace);
        die(json_encode($response));
        
    }else{
        
        $row = $result->fetch_object();
        $mensaje["finishtime"]  = $row->finishtime; 
        $mensaje["starttime"]   = $row->starttime; 
        $mensaje["duration"]    = $row->duration;
        $mensaje["transcount"] 	= $row->transaccount;
        $mensaje["maxrt"]      	= $row->maxRT; 			
        $mensaje["minrt"]     	= $row->minRT; 	
        $mensaje["avgrt"]	= $row->avgRT; 	
        $mensaje["maxtps"]	= $row->maxTPS; 
        $mensaje["avgtps"]	= $row->avgTPS;				
    }
	
    /* Response codes */
    $query = "SELECT respcodecode, numberofresponses FROM tbl_responsecodes WHERE tbl_tests_id_test = $testid AND label= 'all_test'";
 
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Invalid response codes query: ' . mysql_error() . "\n";
        $message .= 'Full query: ' . $query;
            
        $response['code'] = "002"; // code 002 error de query
        $response['message'] = $message;        
        die(json_encode($response));
    }else{
        
        $data = array();
        $mensajeerror = array();
	
        while($row = $result->fetch_object()){
            $data["label"]      = $row->respcodecode;
            $data["legendText"] = $row->respcodecode;
            $data["y"]          = round(intval($row->numberofresponses)*100/$mensaje["transcount"],2);
            $data["count"]      = $row->numberofresponses;
            $mensajeerror[]     = $data;
        }		
    }
    
    $mensaje["error"]   = $mensajeerror;

    $gTitle['text'] 	= "Response codes";
    $gTitle['fontSize']	= 15;

    $rcgraph['title'] 	= $gTitle;

    $rcgraph['animationEnabled']    = true;

    $gLegend['verticalAlign']       = "center";
    $gLegend['horizontalAlign']     =  "left";
    $gLegend['fontSize']            = 15;
    $gLegend['fontFamily']          = "Helvetica";

    $rcgraph['legend']	= $gLegend;

    $rcgraph['theme'] 	= "theme2";

    $dataPie['type'] 			= "pie";
    $dataPie['indexLabelFontFamily']    = "Helvetica";
    $dataPie['indexLabelFontSize'] 	= 10;
    $dataPie['indexLabel'] 		= "{label}: {y}%";
    $dataPie['startAngle'] 		= -20;
    //$dataPie['showInLegend']		= true;
    $dataPie['toolTipContent']		= "{legendText}: {y}%";
    $dataPie['dataPoints']		= $mensajeerror;

    $rcgraph['data']    = array();
    
    $rcgraph['data'][]    = $dataPie;
    
    $mensaje["rcgraph"] = $rcgraph;
    
    /* General Timeline Graph */
    if ($_SESSION['multifile']){
        $query = "SELECT FROM_UNIXTIME(jtimestamp div 1000) as Date, AVG(elapsed) as AVG, count(*) as TPS FROM $testlogtable GROUP BY HOUR(Date), MINUTE(Date) ORDER BY Date";
    }else{
        $query = "SELECT FROM_UNIXTIME(jtimestamp div 1000) as Date, AVG(elapsed) as AVG, count(*) as TPS, AVG(allthreads) as VU FROM $testlogtable GROUP BY HOUR(Date), MINUTE(Date) ORDER BY Date";
    }
    
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Invalid General Timeline Graph query: ' . mysql_error() . "\n";
        $message .= 'Full query: ' . $query;
            
        $response['code'] = "002"; // code 002 error de query
        $response['message'] = $message;        
        die(json_encode($response));
    }else{
        
        if ($_SESSION['multifile']){
            $data = array();
            $mensajeerror = array();

            $textElapse = "";
            $textTPS    = "";

            while($row = $result->fetch_object()){
                $time=explode(" ",$row->Date);
                $dataElapse["x"]	= $time[0]."T". $time[1];
                $dataElapse["y"]	= round($row->AVG,2);
                $dataTPS["x"] 	= $time[0]."T". $time[1];
                $dataTPS["y"] 	= intval($row->TPS);	
                $textElapse[] 	= $dataElapse;
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
        $gTitle['text'] 	  = "Globat Timeline";
        $gTitle['fontSize'] 	  = 20;
        $gLegendRT['fontFamily']    = "Helvetica";
        $gLegendRT['cursor']        = "pointer";
        $gLegendRT['itemclick']   = '';
        $gToolTip['shared']	  = true;
        $gToolTip['enabled']	  = true;
        $gAxisX['title']	  = "Time";
        $gAxisX['titleFontSize']  = 15;
        $gAxisX['labelFontSize']  = 15;
        $gAxisY['includeZero']	  = false;
        $gAxisY['title']	  = "Responde time (ms)";
        $gAxisY['titleFontSize']  = 15;
        $gAxisY['labelFontSize']  = 15;
        $gAxisY2['includeZero']	  = false;
        $gAxisY2['title']	  = "TPM (Req/min)";
        $gAxisY2['titleFontSize'] = 15;
        $gAxisY2['labelFontSize'] = 15;

        $dataLine1['type'] 		= "line";
        $dataLine1['color']             = "#369EAD";
        $dataLine1['legendText'] 	= "RT AVG"; //Legend name
        $dataLine1['name'] 		= "RT AVG"; //tool tip name
        $dataLine1['showInLegend'] 	= true;
        $dataLine1['dataPoints'] 	= $textElapse;

        $dataLine2['type'] 		= "line";
        $dataLine2['color']             = "#C24642";
        $dataLine2['legendText'] 	= "TPM"; //Legend name
        $dataLine2['name'] 		= "TPM"; //tool tip name
        $dataLine2['showInLegend'] 	= true;
        $dataLine2['axisYType'] 	= "secondary";
        $dataLine2['dataPoints']	= $textTPS;

        $gdata[0]	=	$dataLine1;
        $gdata[1]	=	$dataLine2;

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
        
        $gtlgraph['title'] 	= $gTitle;
        $gtlgraph['legend'] 	= $gLegendRT;
        $gtlgraph['zoomEnabled'] = true;
        $gtlgraph['exportFileName'] = "Response time over time";
        $gtlgraph['exportEnabled'] = true;
        $gtlgraph['toolTip']     = $gToolTip;
        $gtlgraph['axisX'] 	= $gAxisX;
        $gtlgraph['axisY'] 	= $gAxisY;
        $gtlgraph['axisY2'] 	= $gAxisY2;
        $gtlgraph['data'] 	= $gdata;
    }
    
    $mensaje["gtlgraph"] = $gtlgraph;
       
    $response['code'] = "000";
    $response['message'] = $mensaje;

    mysqli_close($enlace);
    echo json_encode($response);
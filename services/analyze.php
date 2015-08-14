<?php

    include("configuracion.php");

	$response = array('code' => "", 'message' => "");
	$geninfo = array();
	
	if (isset($_SESSION['execution'])){
		$testname	=	$_SESSION['execution'];
		$testid		=	$_SESSION['executionID'];
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
            $response['code'] = "001"; // connexion error = code 001 
            $response['message'] = $message; 
            die(json_encode($response));
	}
 
	/*Variables escaping*/ 
	$testlogtable = mysqli_real_escape_string($enlace, "testlog".$testid);
 
	$query = "SELECT FROM_UNIXTIME(MAX(`jtimestamp`) div 1000) as MAXjtimestamp, FROM_UNIXTIME(MIN(`jtimestamp`) div 1000) as MINjtimestamp, CONVERT_TZ(FROM_UNIXTIME(TIMESTAMPDIFF(SECOND, FROM_UNIXTIME(MIN(`jtimestamp`) div 1000), FROM_UNIXTIME(MAX(`jtimestamp`) div 1000))),'-5:00','+00:00') as duration, 
	count(*) as totaltrans, MAX(`elapsed`) as maxRT, MIN(`elapsed`) as minRT, AVG(`elapsed`) avgRT FROM $testlogtable";
 
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
        $geninfo['transcount'] = $row->totaltrans;
        
        if($geninfo['transcount'] == 0){
            $response['code'] = "006";
            $response['message'] = 'You should upload and process at least 1 file before analize';
            mysqli_close($enlace);		
            die(json_encode($response));
        }else{
            $duracion = explode(" ",$row->duration);
            $geninfo['finishtime'] = $row->MAXjtimestamp; 
            $geninfo['starttime'] = $row->MINjtimestamp; 
            $geninfo['duration'] = $duracion[1];
            $geninfo['maxrt'] = $row->maxRT; 			
            $geninfo['minrt'] = $row->minRT; 	
            $geninfo['avgrt'] = round($row->avgRT,2);
        }
    }
	/* Other data: MAX TPS, AVG TPS */
	$query = "SELECT MAX(temptable.TPS) as maxTPS, AVG(temptable.TPS) as avgTPS 
	FROM (SELECT COUNT(*) as TPS FROM $testlogtable GROUP BY SECOND(FROM_UNIXTIME(jtimestamp div 1000))) as temptable";
 
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Invalid GI query 2: ' . mysql_error() . "\n";
        $message .= 'Full query: ' . $query;
            
		$response['code'] = "002"; // code 002 error de query
        $response['message'] = $message;
		mysqli_close($enlace);		
        die(json_encode($response));
       
    }else{

        $row = $result->fetch_object();
        $geninfo['maxtps'] = $row->maxTPS; 
        $geninfo['avgtps'] = $row->avgTPS;	
		
    }
	
	/* - Update General Information - */
	$query = "UPDATE tbl_tests SET starttime = '".$geninfo['starttime']."', finishtime = '".$geninfo['finishtime']."', duration = '".$geninfo['duration']."', transaccount = '".$geninfo['transcount']."', minRT = '".$geninfo['minrt']."', maxRT = '".$geninfo['maxrt']."', avgRT = '".$geninfo['avgrt']."', avgTPS = '".$geninfo['avgtps']."', maxTPS = '".$geninfo['maxtps']."' WHERE id_test = $testid";
	
	$result = mysqli_query($enlace, $query);
	
	if (!$result){
        $message  = 'Invalid query UPDATE GI: ' . mysql_error() . "\n";
        $message .= 'Full query: ' . $query;
            
		$response['code'] = "002"; // code 002 error de query
        $response['message'] = $message;
		mysqli_close($enlace);		
        die(json_encode($response));
	}
	
	/* Response codes */
	/*Clean the response codes if it has already been analyzed*/
	
	$query = "DELETE FROM tbl_responsecodes WHERE tbl_tests_id_test = $testid";
	
	$result = mysqli_query($enlace, $query);

    if (!$result) {
        $message  = 'Invalid query DELETE response codes: ' . mysql_error() . "\n";
        $message .= 'Full query: ' . $query;
            
		$response['code'] = "002"; // code 002 error de query
        $response['message'] = $message;
		mysqli_close($enlace);		
        die(json_encode($response));
    }	
	
	/* Count the response codes */
	
    $query = "SELECT responseCode, COUNT(*) as count FROM $testlogtable GROUP BY responseCode";

    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Invalid query response codes: ' . mysql_error() . "\n";
        $message .= 'Full query: ' . $query;
            
		$response['code'] = "002"; // code 002 error de query
        $response['message'] = $message;
		mysqli_close($enlace);		
        die(json_encode($response));
		
    }else{
	
        while($row = $result->fetch_object()){
			$sql[] = '("'.$testid.'", "'.mysqli_real_escape_string($enlace, $row->responseCode).'","'.mysqli_real_escape_string($enlace, $row->count).'", "all_test")';		
        }		
		$query = 'INSERT INTO tbl_responsecodes	(tbl_tests_id_test, respcodecode, numberofresponses, label)  VALUES '.implode(',', $sql);
		
		$result = mysqli_query($enlace, $query);
		
		if (!$result) {
			$message  = 'Invalid query response codes: ' . mysql_error() . "\n";
			$message .= 'Full query: ' . $query;
				
			$response['code'] = "002"; // code 002 error de query
			$response['message'] = $message;
			mysqli_close($enlace);		
			die(json_encode($response));
		}
    }

	/* Labels */
	$query = "DELETE FROM tbl_labels WHERE tbl_tests_id_test = $testid";
	
	$result = mysqli_query($enlace, $query);
	
	if (!$result) {
            $message  = 'Invalid query DELETE labels: ' . mysql_error() . "\n";
            $message .= 'Full query: ' . $query;

            $response['code'] = "002"; // code 002 error de query
            $response['message'] = $message;
		mysqli_close($enlace);		
        die(json_encode($response));
    }
	
	$query = "SELECT DISTINCT label FROM $testlogtable ORDER BY label";
 
    $result = mysqli_query($enlace, $query);
	
	if (!$result) {
        $message  = 'Invalid query LIST REQUEST: ' . mysql_error() . "\n";
        $message .= 'Full query: ' . $query;
            
		$response['code'] = "002"; // code 002 error de query
        $response['message'] = $message;
		mysqli_close($enlace);		
        die(json_encode($response));
		
    }else{
		
		while($row = $result->fetch_object()){
			$labels[] = '("'.$testid.'", "'.mysqli_real_escape_string($enlace, $row->label).'")';		
        }
		
		$query = 'INSERT INTO tbl_labels (tbl_tests_id_test, label) VALUES '.implode(',', $labels);

		$result = mysqli_query($enlace, $query);
		
		if (!$result) {
			$message  = 'Invalid query INSERT labels: ' . mysql_error() . "\n";
			$message .= 'Full query: ' . $query;
				
			$response['code'] = "002"; // code 002 error de query
			$response['message'] = $message;
			mysqli_close($enlace);		
			die(json_encode($response));
		}
		
	}
	
    /* Percentiles */

    $dataPointsAll 		= array();
    $dataPoints200		= array();
    $dataPointsAllvec 	= array();
    $dataPoints200vec	= array();
    $percentiles		= array();
	
    /*Percentil 100 all*/
    $query = "SELECT MAX(elapsed) as p100allelapse, COUNT(*) as p100allcount FROM $testlogtable";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
        $response['code'] 	= "002"; // code 002 error de query
        $response['message'] 	= $message;
        mysqli_close($enlace);			
        die(json_encode($response));
    }else{
        $row = $result->fetch_object();
        $dataPointsAll['y']	= intval($row->p100allelapse);
        $dataPointsAll['label']	= "100";
        $dataPointsAllvec[0]	= $dataPointsAll;
        $p100allcount		= $row->p100allcount;
    }
	
	
    /*Percentil 100 200*/
    $query = "SELECT MAX(elapsed) as p100200elapse, COUNT(*) as p100200count FROM $testlogtable WHERE responseCode=\"200\"";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
        $response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;
        mysqli_close($enlace);	
        die(json_encode($response));
    }else{
        $row = $result->fetch_object();
        $dataPoints200['y'] 	= intval($row->p100200elapse);
        $dataPoints200['label']	= "100";
        $dataPoints200vec[0]	= $dataPoints200;
        $p100200count		= $row->p100200count;
    }
	
	
	/*Percentil 95 all*/
	$percentAll = round($p100allcount * 0.95, 0, PHP_ROUND_HALF_UP);
	$percent200 = round($p100200count * 0.95, 0, PHP_ROUND_HALF_UP);
	
	$query = "SELECT elapsed as p95allelapse FROM $testlogtable ORDER BY elapsed ASC limit $percentAll,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;
		mysqli_close($enlace);
        die(json_encode($response));
    }else{
		$row = $result->fetch_object();
		$dataPointsAll['y'] 	= intval($row->p95allelapse);
		$dataPointsAll['label']	= "95";
		$dataPointsAllvec[1]	= $dataPointsAll;
    }
	
	/*Percentil 95 200*/
	$query = "SELECT elapsed as p95200elapse FROM $testlogtable WHERE responseCode=\"200\" ORDER BY elapsed ASC limit $percent200,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;
		mysqli_close($enlace);	
        die(json_encode($response));
    }else{
		$row = $result->fetch_object();
		$dataPoints200['y'] 	= intval($row->p95200elapse);
		$dataPoints200['label']	= "95";
		$dataPoints200vec[1]	= $dataPoints200;
    }
	
	/*Percentil 90 all*/
	$percentAll = round($p100allcount * 0.9, 0, PHP_ROUND_HALF_UP);
	$percent200 = round($p100200count * 0.9, 0, PHP_ROUND_HALF_UP);
	
	$query = "SELECT elapsed as p90allelapse FROM $testlogtable ORDER BY elapsed ASC limit $percentAll,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;
		mysqli_close($enlace);
        die(json_encode($response));
    }else{
		$row = $result->fetch_object();
		$dataPointsAll['y']		= intval($row->p90allelapse);
		$dataPointsAll['label']	= "90";
		$dataPointsAllvec[2]	= $dataPointsAll;
	}
	
	/*Percentil 90 200*/
	$query = "SELECT elapsed as p90200elapse FROM $testlogtable WHERE responseCode=\"200\" ORDER BY elapsed ASC limit $percent200,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;
		mysqli_close($enlace);
        die(json_encode($response));
    }else{
		$row = $result->fetch_object();
		$dataPoints200['y']		= intval($row->p90200elapse);
		$dataPoints200['label']	= "90";
		$dataPoints200vec[2]	= $dataPoints200;
    }		
	
	/*Percentil 80 all*/
	$percentAll = round($p100allcount * 0.8, 0, PHP_ROUND_HALF_UP);
	$percent200 = round($p100200count * 0.8, 0, PHP_ROUND_HALF_UP);
	
	$query = "SELECT elapsed as p80allelapse FROM $testlogtable ORDER BY elapsed ASC limit $percentAll,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;
		mysqli_close($enlace);
        die(json_encode($response));
    }else{
		$row = $result->fetch_object();
		$dataPointsAll['y']		= intval($row->p80allelapse);
		$dataPointsAll['label']	= "80";
		$dataPointsAllvec[3]	= $dataPointsAll;
    }
	
	/*Percentil 80 200*/
	$query = "SELECT elapsed as p80200elapse FROM $testlogtable WHERE responseCode=\"200\" ORDER BY elapsed ASC limit $percent200,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;  
		mysqli_close($enlace);
        die(json_encode($response));
    }else{
		$row = $result->fetch_object();
		$dataPoints200['y']		= intval($row->p80200elapse);
		$dataPoints200['label']	= "80";
		$dataPoints200vec[3]	= $dataPoints200;
	}			
	
	/*Percentil 75 all*/
	$percentAll = round($p100allcount * 0.75, 0, PHP_ROUND_HALF_UP);
	$percent200 = round($p100200count * 0.75, 0, PHP_ROUND_HALF_UP);
	
	$query = "SELECT elapsed as p75allelapse FROM $testlogtable ORDER BY elapsed ASC limit $percentAll,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;
		mysqli_close($enlace);
        die(json_encode($response));
    }else{
		$row = $result->fetch_object();
		$dataPointsAll['y']		= intval($row->p75allelapse);
		$dataPointsAll['label']	= "75";
		$dataPointsAllvec[4]	= $dataPointsAll;
    }
	
	/*Percentil 75 200*/
	$query = "SELECT elapsed as p75200elapse FROM $testlogtable WHERE responseCode=\"200\" ORDER BY elapsed ASC limit $percent200,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;
		mysqli_close($enlace);
        die(json_encode($response));
    }else{
		$row = $result->fetch_object();
		$dataPoints200['y']		= intval($row->p75200elapse);
		$dataPoints200['label']	= "75";
		$dataPoints200vec[4]	= $dataPoints200;
    }		
	
	/*Percentil 50 all*/
	$percentAll = round($p100allcount * 0.50, 0, PHP_ROUND_HALF_UP);
	$percent200 = round($p100200count * 0.50, 0, PHP_ROUND_HALF_UP);
	
	$query = "SELECT elapsed as p50allelapse FROM $testlogtable ORDER BY elapsed ASC limit $percentAll,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;
		mysqli_close($enlace);	
        die(json_encode($response));
    }else{
		$row = $result->fetch_object();
		$dataPointsAll['y']		= intval($row->p50allelapse);
		$dataPointsAll['label']	= "50";
		$dataPointsAllvec[5]	= $dataPointsAll;
    }
	
	/*Percentil 50 200*/
	$query = "SELECT elapsed as p50allelapse FROM $testlogtable WHERE responseCode=\"200\" ORDER BY elapsed ASC limit $percent200,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;
		mysqli_close($enlace);	
        die(json_encode($response));
    }else{
		$row = $result->fetch_object();
		$dataPoints200['y']		= intval($row->p50allelapse);
		$dataPoints200['label']	= "50";
		$dataPoints200vec[5]	= $dataPoints200;
    }		
	
	/*Clean the percentiles if it has already been analyzed*/
	
	$query = "DELETE FROM tbl_percentiles WHERE tbl_tests_id_test = $testid";
	
	$result = mysqli_query($enlace, $query);

    if (!$result) {
        $message  = 'Invalid query DELETE response codes: ' . mysql_error() . "\n";
        $message .= 'Full query: ' . $query;
            
		$response['code'] = "002"; // code 002 error de query
        $response['message'] = $message;
		mysqli_close($enlace);		
        die(json_encode($response));
    }	
	
	for ($i = 0; $i <= 5; $i++) {
		$percentiles[] = '("'.$testid.'", "all_test", "'.mysqli_real_escape_string($enlace, $dataPointsAllvec[$i]['label']).'", "'.mysqli_real_escape_string($enlace, $dataPointsAllvec[$i]['y']).'", "all")';
		
		$percentiles[] = '("'.$testid.'", "all_test", "'.mysqli_real_escape_string($enlace, $dataPoints200vec[$i]['label']).'", "'.mysqli_real_escape_string($enlace, $dataPoints200vec[$i]['y']).'", "200")';
	}
	
	$query = 'INSERT INTO tbl_percentiles (tbl_tests_id_test, label, percentil, responsetime, resptype) VALUES '.implode(',', $percentiles);

	$result = mysqli_query($enlace, $query);
	
	if (!$result) {
		$message  = 'Invalid query response codes: ' . mysql_error() . "\n";
		$message .= 'Full query: ' . $query;
			
		$response['code'] = "002"; // code 002 error de query
		$response['message'] = $message;
		mysqli_close($enlace);		
		die(json_encode($response));
	}
	
	$mensaje['message'] = "Test analyzed successfully";
	$mensaje['starttime'] = $geninfo['starttime'];
	$mensaje['finishtime'] = $geninfo['finishtime'];
	
	$response['code'] = "000";
	$response['message'] = $mensaje;
	mysqli_close($enlace);
	echo json_encode($response);
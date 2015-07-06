<?php

    include("configuracion.php");
	
	$tabla	=	$_SESSION['execution'];
	
	$response = array('code' => "", 'message' => "");
	$dataPointsAll 		= array();
	$dataPoints200		= array();
	$dataPointsAllvec 	= array();
	$dataPoints200vec	= array();

	
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
	
	/*Percentil 100 all*/
	$query = "SELECT MAX(elapsed) as p100allelapse, COUNT(*) as p100allcount FROM $tabla";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;        
        die(json_encode($response));
		
		exit();
        
    }else{
		$row = $result->fetch_object();
		$dataPointsAll['y']		= intval($row->p100allelapse);
		$dataPointsAll['label']	= "100";
		$dataPointsAllvec[0]	= $dataPointsAll;
		$p100allcount			= $row->p100allcount;
    }
	
	
	/*Percentil 100 200*/
	$query = "SELECT MAX(elapsed) as p100200elapse, COUNT(*) as p100200count FROM $tabla WHERE responseCode=\"200\"";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;        
        die(json_encode($response));
		
		exit();
        
    }else{
		$row = $result->fetch_object();
		$dataPoints200['y'] 	= intval($row->p100200elapse);
		$dataPoints200['label']	= "100";
		$dataPoints200vec[0]	= $dataPoints200;
		$p100200count			= $row->p100200count;
    }
	
	
	/*Percentil 95 all*/
	$percentAll = round($p100allcount * 0.95, 0, PHP_ROUND_HALF_UP);
	$percent200 = round($p100200count * 0.95, 0, PHP_ROUND_HALF_UP);
	
	$query = "SELECT elapsed as p95allelapse FROM $tabla ORDER BY elapsed ASC limit $percentAll,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;        
        die(json_encode($response));
		
		exit();
        
    }else{
		$row = $result->fetch_object();
		$dataPointsAll['y'] 	= intval($row->p95allelapse);
		$dataPointsAll['label']	= "95";
		$dataPointsAllvec[1]	= $dataPointsAll;
    }
	
	/*Percentil 95 200*/
	$query = "SELECT elapsed as p95200elapse FROM $tabla WHERE responseCode=\"200\" ORDER BY elapsed ASC limit $percent200,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;        
        die(json_encode($response));
		
		exit();
        
    }else{
		$row = $result->fetch_object();
		$dataPoints200['y'] 	= intval($row->p95200elapse);
		$dataPoints200['label']	= "95";
		$dataPoints200vec[1]	= $dataPoints200;
    }
	
	/*Percentil 90 all*/
	$percentAll = round($p100allcount * 0.9, 0, PHP_ROUND_HALF_UP);
	$percent200 = round($p100200count * 0.9, 0, PHP_ROUND_HALF_UP);
	
	$query = "SELECT elapsed as p90allelapse FROM $tabla ORDER BY elapsed ASC limit $percentAll,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;        
        die(json_encode($response));
		
		exit();
        
    }else{
		$row = $result->fetch_object();
		$dataPointsAll['y']		= intval($row->p90allelapse);
		$dataPointsAll['label']	= "90";
		$dataPointsAllvec[2]	= $dataPointsAll;
	}
	
	/*Percentil 90 200*/
	$query = "SELECT elapsed as p90200elapse FROM $tabla WHERE responseCode=\"200\" ORDER BY elapsed ASC limit $percent200,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;        
        die(json_encode($response));
		
		exit();
        
    }else{
		$row = $result->fetch_object();
		$dataPoints200['y']		= intval($row->p90200elapse);
		$dataPoints200['label']	= "90";
		$dataPoints200vec[2]	= $dataPoints200;
    }		
	
	/*Percentil 80 all*/
	$percentAll = round($p100allcount * 0.8, 0, PHP_ROUND_HALF_UP);
	$percent200 = round($p100200count * 0.8, 0, PHP_ROUND_HALF_UP);
	
	$query = "SELECT elapsed as p80allelapse FROM $tabla ORDER BY elapsed ASC limit $percentAll,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;        
        die(json_encode($response));
		
		exit();
        
    }else{
		$row = $result->fetch_object();
		$dataPointsAll['y']		= intval($row->p80allelapse);
		$dataPointsAll['label']	= "80";
		$dataPointsAllvec[3]	= $dataPointsAll;
    }
	
	/*Percentil 80 200*/
	$query = "SELECT elapsed as p80200elapse FROM $tabla WHERE responseCode=\"200\" ORDER BY elapsed ASC limit $percent200,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;        
        die(json_encode($response));
		
		exit();
        
    }else{
		$row = $result->fetch_object();
		$dataPoints200['y']		= intval($row->p80200elapse);
		$dataPoints200['label']	= "80";
		$dataPoints200vec[3]	= $dataPoints200;
	}			
	
	/*Percentil 75 all*/
	$percentAll = round($p100allcount * 0.75, 0, PHP_ROUND_HALF_UP);
	$percent200 = round($p100200count * 0.75, 0, PHP_ROUND_HALF_UP);
	
	$query = "SELECT elapsed as p75allelapse FROM $tabla ORDER BY elapsed ASC limit $percentAll,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;        
        die(json_encode($response));
		
		exit();
        
    }else{
		$row = $result->fetch_object();
		$dataPointsAll['y']		= intval($row->p75allelapse);
		$dataPointsAll['label']	= "75";
		$dataPointsAllvec[4]	= $dataPointsAll;
    }
	
	/*Percentil 75 200*/
	$query = "SELECT elapsed as p75200elapse FROM $tabla WHERE responseCode=\"200\" ORDER BY elapsed ASC limit $percent200,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;        
        die(json_encode($response));
		
		exit();
        
    }else{
		$row = $result->fetch_object();
		$dataPoints200['y']		= intval($row->p75200elapse);
		$dataPoints200['label']	= "75";
		$dataPoints200vec[4]	= $dataPoints200;
    }		
	
	/*Percentil 50 all*/
	$percentAll = round($p100allcount * 0.50, 0, PHP_ROUND_HALF_UP);
	$percent200 = round($p100200count * 0.50, 0, PHP_ROUND_HALF_UP);
	
	$query = "SELECT elapsed as p50allelapse FROM $tabla ORDER BY elapsed ASC limit $percentAll,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;        
        die(json_encode($response));
		
		exit();
        
    }else{
		$row = $result->fetch_object();
		$dataPointsAll['y']		= intval($row->p50allelapse);
		$dataPointsAll['label']	= "50";
		$dataPointsAllvec[5]	= $dataPointsAll;
    }
	
	/*Percentil 50 200*/
	$query = "SELECT elapsed as p50allelapse FROM $tabla WHERE responseCode=\"200\" ORDER BY elapsed ASC limit $percent200,1";
    $result = mysqli_query($enlace, $query);
	
    if (!$result) {
        $message  = 'Query invalido: ' . mysql_error() . "\n";
        $message .= 'Query completa: ' . $query;
            
		$response['code'] 		= "002"; // code 002 error de query
        $response['message'] 	= $message;        
        die(json_encode($response));
		
		exit();
        
    }else{
		$row = $result->fetch_object();
		$dataPoints200['y']		= floatval($row->p50allelapse);
		$dataPoints200['label']	= "50";
		$dataPoints200vec[5]	= $dataPoints200;
    }		
	
	$gTitle['text'] 	= "RT Percentiles";
	$gTitle['fontSize'] = 30;
	$gAxisX['title']		= "Percentil";
	$gAxisX['titleFontSize']= 20;
	$gAxisX['labelFontSize']= 15;
	$gAxisY['title']	= "Responde time (ms)";	
	$gAxisY['titleFontSize']= 20;
	$gAxisY['labelFontSize']= 15;
	
	$dataSet1['type'] 			= "bar";
	$dataSet1['legendText'] 	= "All requests"; //Legend name
	$dataSet1['name'] 			= "All requests"; //tool tip name
	$dataSet1['showInLegend'] 	= true;
	$dataSet1['dataPoints'] 	= $dataPointsAllvec;
	
	$dataSet2['type'] 			= "bar";
	$dataSet2['legendText'] 	= "Successful requests"; //Legend name
	$dataSet2['name'] 			= "Successful requests"; //tool tip name
	$dataSet2['showInLegend'] 	= true;
	
	$dataSet2['dataPoints']	= $dataPoints200vec;
	
	$gdata[0]	=	$dataSet1;
	$gdata[1]	=	$dataSet2;
			
	$message['title'] 	= $gTitle;
	$message['axisX'] 	= $gAxisX;
	$message['axisY'] 	= $gAxisY;
	$message['data'] 	= $gdata;	
		
	$response['code'] = "000";
	$response['message'] = $message;
	echo json_encode($response);
	
	
	mysqli_close($enlace);


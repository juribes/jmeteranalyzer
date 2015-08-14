<?php

    include("configuracion.php");
	
	$filename	=	$_GET['filename'];
	$servername	=	$_GET['servername'];
	$multifile	=	$_GET['multifile'];

	$response = array('code' => "", 'message' => "");        
        
	if (isset($_SESSION['execution'])){
		$testname	=	$_SESSION['execution'];
		$testid		=	$_SESSION['executionID'];
	}else{
		$response['message'] = "You need to select a test/execution";
		$response['code'] = "003";
		die(json_encode($response));
	}
	
	$MyLog = fopen(".\\Uploads\\".$filename, "r");
	
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

	/*Verify number of files*/
	
	$query = 'SELECT numberoffiles FROM tbl_tests WHERE id_test='.$testid;

	$result = mysqli_query($enlace, $query);
	
	if (!$result){
		$response['code'] = "002";
		$response['message'] = "Error in the query select number of files, error: ".mysqli_error($enlace)."<br>".$query;
		
		mysqli_close($enlace);
		fclose($MyLog);
		die(json_encode($response));
	}else{
		$row = $result->fetch_object();
		$numberoffiles = $row->numberoffiles; 
		$numberoffiles++;
	}

	if ($numberoffiles > 1 and $multifile == "false"){
		$response['code'] = "005";
		$response['message'] = "The test has file(s) already proccessed, if you process more than 1 file the test is going to be multifile and JMeteranalyzer will not be able to show the VU (Virtual User) stats. Do you want to process the file and make it multifile test?";
		
		mysqli_close($enlace);
		fclose($MyLog);
		die(json_encode($response));
	}
	
	fgetcsv($MyLog);
	while (!feof($MyLog)) {
		$registro = fgetcsv($MyLog);
		if ($registro!=null){
			if($registro[3]==""){
				$registro[3]="No response code";
			}
			if ($registro!=NULL){
				$sql[] = '('.mysqli_real_escape_string($enlace, $registro[0]).', '.mysqli_real_escape_string($enlace, $registro[1]).', "'.mysqli_real_escape_string($enlace, $registro[2]).'", "'.mysqli_real_escape_string($enlace, $registro[3]).'", "'.mysqli_real_escape_string($enlace, $registro[4]).'", '.mysqli_real_escape_string($enlace, $registro[5]).', '.mysqli_real_escape_string($enlace, $registro[6]).', '.mysqli_real_escape_string($enlace, $registro[7]).', '.mysqli_real_escape_string($enlace, $registro[8]).', '.mysqli_real_escape_string($enlace, $registro[9]).', '.mysqli_real_escape_string($enlace, $registro[10]).', '.mysqli_real_escape_string($enlace, $registro[11]).', "'.mysqli_real_escape_string($enlace, $servername).'")';
			}
		}
	}
	
	/* INSERT in log table */
	$query = 'INSERT INTO testlog'.$testid.' (jtimestamp, elapsed, label, responseCode, threadName, success, bytes, grpThreads, allThreads, Latency, SampleCount, ErrorCount, Hostname)  VALUES '.implode(',', $sql);
	
	$result = mysqli_query($enlace, $query);
	
	if ($result){
		$response['code'] = "000";
		$response['message'] = mysqli_affected_rows($enlace)." records inserted for the server: ".$servername." from the file: ".$filename." in the test: ".$testname;
	}else{
		$response['code'] = "002";
		$response['message'] = "Error in the query INSERT log: ".mysqli_error($enlace)."<br>".$query;
		
		mysqli_close($enlace);
		fclose($MyLog);
		die(json_encode($response));
	}

	
	/*Variables escaping*/
	$filename 	= mysqli_real_escape_string($enlace, $filename);
	$servername = mysqli_real_escape_string($enlace, $servername);
	
	/* INSERT in files table */
	$query = "INSERT INTO tbl_files (tbl_tests_id_test, name, server)  VALUES ('".$testid."', '".$filename."', '".$servername."')";
	
	$result = mysqli_query($enlace, $query);
	
	if (!$result){
		$response['code'] = "002";
		$response['message'] = "Error in the query INSERT file in table, error: ".mysqli_error($enlace)."<br>".$query;
		
		mysqli_close($enlace);
		fclose($MyLog);
		die(json_encode($response));
	}		

	
	/* Update tests table */
	if ($numberoffiles == 1){
		$query = 'UPDATE tbl_tests SET numberoffiles = '.$numberoffiles.' WHERE id_test ='.$testid;
	}else{
		$query = "UPDATE tbl_tests SET numberoffiles = ".$numberoffiles.", multifile = '1' WHERE id_test =".$testid;
	}
		
	$result = mysqli_query($enlace, $query);
	
	if (!$result){
		$response['code'] = "002";
		$response['message'] = "Error en la Query: ".mysqli_error($enlace)."<br>".$query;
		
		mysqli_close($enlace);
		fclose($MyLog);
		die(json_encode($response));
	}	

	mysqli_close($enlace);
	fclose($MyLog);
	echo json_encode($response);
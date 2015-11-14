<?php

    include("configuracion.php");
	
    $testname	=	$_GET['execution'];

    $response = array('code' => "", 'message' => "");

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

    /*Variables escaping*/ 
    $testname = mysqli_real_escape_string($enlace, $testname);

    /*Insert new test*/
    $query = "INSERT INTO tbl_tests (`name`, `starttime`, `finishtime`, `duration`, `transaccount`, `minRT`, `maxRT`, `avgRT`, `avgTPS`, `maxTPS`, `numberoffiles`, `multifile`) VALUES ('$testname', '1000-01-01 00:00:00', '1000-01-01 00:00:00', '00:00:00', '0', '0', '0', '0', '0', '0', '0', '0')";

    $result = mysqli_query($enlace, $query);

    if ($result){
        $response['code'] = "000";
        $response['message'] = "Test created successfully.";//"The test: ".$testname." has been created successfully, the next step is to upload the test logs";
    }else{
        if (mysqli_error($enlace)=="Duplicate entry '".$testname."' for key 'tbl_testscol_UNIQUE'"){
            $response['code'] = "004";
            $response['message'] = "The test: ".$testname." already exists, please select a new name for the test";
            die(json_encode($response));
        }else{
            $response['code'] = "002";
            $response['message'] = "Error in the query INSERT: ".mysqli_error($enlace)."<br>".$query;
            die(json_encode($response));
        }
    }
	
	/*Get test ID*/
	$query = "SELECT id_test FROM tbl_tests WHERE name = '$testname'";
	
	$result = mysqli_query($enlace, $query);
	
	if ($result){
		$row = $result->fetch_object();
		$testid = $row->id_test; 
	}else{
		$response['code'] = "002";
		$response['message'] = "Error in the query Get ID: ".mysqli_error($enlace)."<br>".$query;
		mysqli_close($enlace);
		die(json_encode($response));
	}
	
	/*Create new logtable*/
	
    $query = "CREATE TABLE IF NOT EXISTS testlog".$testid." (
  id INT(11) NOT NULL AUTO_INCREMENT COMMENT '',
  jtimestamp BIGINT(13) NOT NULL COMMENT '',
  elapsed INT(11) NOT NULL COMMENT '',
  label VARCHAR(345) NOT NULL COMMENT '',
  responsecode VARCHAR(50) NOT NULL COMMENT '',
  threadname VARCHAR(20) NOT NULL COMMENT '',
  success TINYINT(1) NOT NULL COMMENT '',
  bytes INT(11) NOT NULL COMMENT '',
  grpthreads INT(11) NOT NULL COMMENT '',
  allthreads INT(11) NOT NULL COMMENT '',
  latency INT(11) NOT NULL COMMENT '',
  samplecount INT(11) NOT NULL COMMENT '',
  errorcount INT(11) NOT NULL COMMENT '',
  hostname VARCHAR(15) NOT NULL COMMENT '',
  PRIMARY KEY (id)  COMMENT '',
  INDEX jtimestamp (jtimestamp ASC)  COMMENT '',
  INDEX label (label ASC)  COMMENT '',
  INDEX responsecode (responsecode ASC)  COMMENT '')
ENGINE = InnoDB;";
 
    $result = mysqli_query($enlace, $query);

    if (mysqli_query($enlace, $query)) {
        $response['code'] 			= "000";
        $response['message'] 		= "Test created successfully.";
        $_SESSION['execution']		= $testname;
        $_SESSION['executionID']	= $testid;
    } else {
        $response['code'] = "002";
        $response['message'] = "Error in the query CREATE: ".mysqli_error($enlace)."<br>".$query;
        mysqli_close($enlace);
        die(json_encode($response));
    }

    echo json_encode($response);

    mysqli_close($enlace);

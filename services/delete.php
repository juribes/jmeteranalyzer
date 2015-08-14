<?php

    include("configuracion.php");
	
    $testid = $_GET['execution'];

    $response = array('code' => "", 'message' => "");

    if($testid=="undefined"){
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

    /*Variables escaping*/ 
    $testid = mysqli_real_escape_string($enlace, $testid);

	/*GET test name*/
	$query = "SELECT name FROM tbl_tests WHERE id_test=".$testid;

        $result = mysqli_query($enlace, $query);
        
	if (!$result){
		$response['code'] = "002";
		$response['message'] = "Error in the query Get ID: ".mysqli_error($enlace)."<br>".$query;
		mysqli_close($enlace);
		die(json_encode($response));
	}else{
		$row = $result->fetch_object();
		$testname = $row->name; 
	}
	
	/*Delete test*/
	$query = "DELETE FROM tbl_files WHERE tbl_tests_id_test = $testid; DELETE FROM tbl_labels WHERE tbl_tests_id_test= $testid; DELETE FROM tbl_percentiles WHERE tbl_tests_id_test = $testid; DELETE FROM tbl_responsecodes WHERE tbl_tests_id_test = $testid;DELETE FROM tbl_tests WHERE id_test = $testid; DROP TABLE testlog".$testid.";";
	
	$result = mysqli_multi_query($enlace, $query);
        
	if ($result){
            $response['code'] = "000";
            
            unset($_SESSION['execution']);
            unset($_SESSION['executionID']);
            $response['message'] = "Test '".$testname."' deleted succefully";
	}else{
            $response['code'] = "002";
            $response['message'] = "Error in the query DELETE: ".mysqli_error($enlace)."<br>".$query;
            mysqli_close($enlace);
            die(json_encode($response));
        }
	
    echo json_encode($response);
    mysqli_close($enlace);

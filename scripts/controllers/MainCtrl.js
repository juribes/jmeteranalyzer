'use strict';

/**
 * @ngdoc function
 * @name JMeteranalyzer.controller:mainCtrl
 * @description
 * # mainCtrl
 * Controller of the JMeteranalyzer
 */
angular.module('JMeteranalyzer')
  .controller('mainCtrl', [ '$scope', '$location', '$log', 'services', 'interData', 'navlocation', 'testid', function ($scope, $location, $log, services, interData, navlocation, testid){

    $scope.init = function(){
        $scope.startview();
    }

	$scope.test = testid;
	$scope.location = navlocation;
	$scope.formcontent = interData;
	$scope.location.locURL = $location.path();
	$scope.showAnalize	=	false;

	$scope.modalmanager = function(mtitle, mmessage){
		$scope.modaltitle = mtitle;
		$scope.modalmessage = mmessage;
		$('#myModal').modal('show');
	}
	
	$scope.startview = function(){ 
        services.getexecutions()
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
                    $scope.executions = res.message.tests;
                    $scope.test.testname = res.message.selected;
                    var i;
                    for (i in $scope.executions) {
                        if ($scope.executions[i].name == $scope.test.testname){
                            $scope.testname = $scope.executions[i].id_test;
                        }
                    }
					
                    $log.log("Successful: get executions");
                    break;
                case "001":
                    //Error de conexión a la base de datos
                    $log.log("DB connection error. " + res.message);
                    $scope.modalmanager("Error", "DB connection error");
                    break;
                case "002":
                    //Error en el query
                    $log.log("Query error. " + res.message);
                    $scope.modalmanager("Error", "Query error.");
                    break;
                case "003":
                    //Error no test selected
                    $log.log("No test selected. " + res.message);
                    $scope.modalmanager("Error", "There is no test selected, please go to Home and select one.");
                    break;
                default:
                    $log.log("Unknown error. Message:" + res.message);
                    $scope.modalmanager("Error", "Unknown error, check the log to see more information");
            }     
        }, function(err){
            // error
            $log.log("Error in the promise");
            $scope.modalmanager("Error", "Error in the promise");
        })
    }

    $scope.setexecution = function(){ 
        if(typeof $scope.testname == 'undefined' || $scope.testname == ""){
            $log.log("Error no test selected");
            $scope.modalmanager("Error", "Please select a test");
        }else{
            services.setexecution($scope.testname)
            .then(function(res){
                // success
                switch(res.code) {
                        case "000":
                                $scope.test.testname = res.message.testname;
                                $scope.formcontent.InitialTime = res.message.starttime;
                                $scope.formcontent.FinalTime = res.message.finishtime;
                                $scope.formcontent.multifile = res.message.multifile;
                                $log.log("Successful: set execution");
                                $scope.showAnalize = true;
                                break;
                        case "001":
                                //Error de conexión a la base de datos
                                $log.log("DB connection error. " + res.message);
                                $scope.modalmanager("Error", "DB connection error");
                                break;
                        case "002":
                                //Error en el query
                                $log.log("Query error. " + res.message);
                                $scope.modalmanager("Error", "Query error.");
                                break;
                        case "003":
                                //Error no test selected
                                $log.log("No test selected. " + res.message);
                                $scope.modalmanager("Error", "There is no test selected, please go to Home and select one.");
                                break;
                        default:
                                $log.log("Unknown error. Message:" + res.message);
                                $scope.modalmanager("Error", "Unknown error, check the log to see more information");
                }
            }, function(err){
                // error
                $log.log("Error in the promise");
                $scope.modalmanager("Error", "Error in the promise");
            })
        }
    }
	
    $scope.createexecution = function(){ 
        if($scope.newtestForm.$error.required){
                $log.log("Error no name for the test");
                $scope.modalmanager("Error", "Please select a name for the test");
        }else{
            services.createexecution($scope.newtest)
            .then(function(res){
                    // success
                    switch(res.code) {
                        case "000":
                                $scope.test.testname = res.message.message;
                                $scope.startview();
                                $log.log("Successful: create execution");
                                $scope.modalmanager("Info", "Test created succefully, the next step is to upload log files");
                                break;
                        case "001":
                                //Error de conexión a la base de datos
                                $log.log("DB connection error. " + res.message);
                                $scope.modalmanager("Error", "DB connection error");
                                break;
                        case "002":
                                //Error en el query
                                $log.log("Query error. " + res.message);
                                $scope.modalmanager("Error", "Query error.");
                                break;
                        case "003":
                                //Error no test selected
                                $log.log("No test selected. " + res.message);
                                $scope.modalmanager("Error", "There is no test selected, please go to Home and select one.");
                                break;
                        case "004":
                                //Error test name duplicated
                                $log.log(res.message);
                                $scope.modalmanager("Error", res.message);
                                break;
                        default:
                                $log.log("Unknown error. Message:" + res.message);
                                $scope.modalmanager("Error", "Unknown error, check the log to see more information");
                    }     
            }, function(err){
                // error
                $log.log("Error in the promise");
                $scope.modalmanager("Error", "Error in the promise");
            })
        }
    }
	
    $scope.analyze = function(){
        $scope.modalverb = "Analyzing...";
        $('#ModalLoading').modal('show');
        services.analyze()
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
                    $scope.respuesta = res.message.message;
                    $scope.formcontent.InitialTime = res.message.starttime;
                    $scope.formcontent.FinalTime = res.message.finishtime;
                    $scope.formcontent.multifile = res.message.multifile;
                    $log.log("Test analized successfully");
                    $scope.modalmanager("Info", $scope.respuesta);
                    $scope.showAnalize	=	false;
                    break;
                case "001":
                    //Error de conexión a la base de datos
                    $log.log("DB connection error. " + res.message);
                    $scope.modalmanager("Error", "DB connection error");
                    break;
                case "002":
                    //Error en el query
                    $log.log("Query error. " + res.message);
                    $scope.modalmanager("Error", "Query error.");
                    break;
                case "003":
                    //Error no test selected
                    $log.log("No test selected. " + res.message);
                    $scope.modalmanager("Error", "There is no test selected, please go to Home and select one.");
                    break;
                case "006":
                    //Error no files processed
                    $log.log(res.message);
                    $scope.modalmanager("Error", res.message);
                    break;
                default:
                    $log.log("Unknown error. Message:" + res.message);
                    $scope.modalmanager("Error", "Unknown error, check the log to see more information");
            }   
            $('#ModalLoading').modal('hide');
        }, function(err){
            // error
            $('#ModalLoading').modal('hide');
            $log.log("Error in the promise");
            $scope.modalmanager("Error", "Error in the promise");
        })
    }

    $scope.deletetestconfirm = function(){
        if(typeof $scope.testname == 'undefined' || $scope.testname == ""){
            $log.log("Error no test selected");
            $scope.modalmanager("Error", "Please select a test");
        }else{
            var i;
            for (i in $scope.executions) {
                if ($scope.executions[i].id_test == $scope.testname){
                    $scope.test.testname = $scope.executions[i].name;
                }
            }
            $scope.modaltitle = "Confirm Delete";
            $scope.modalmessage = "Are you sure that you want to delete the test: '" + $scope.test.testname + "'?";
            $('#ModalConfirm').modal('show');
        }
    }

    $scope.deletetest = function(){
        $('#ModalConfirm').modal('hide');
        if(typeof $scope.testname == 'undefined' || $scope.testname == ""){
            $log.log("Error no test selected");
            $scope.modalmanager("Error", "Please select a test");
        }else{
            services.deletetest($scope.testname)
            .then(function(res){
                // success
                switch(res.code) {
                        case "000":
                                $scope.test.testname = res.message;
                                $log.log("Successful: delete test");
                                $scope.startview();         
                                $scope.modalmanager("Info", res.message);
                                break;
                        case "001":
                                //Error de conexión a la base de datos
                                $log.log("DB connection error. " + res.message);
                                $scope.modalmanager("Error", "DB connection error");
                                break;
                        case "002":
                                //Error en el query
                                $log.log("Query error. " + res.message);
                                $scope.modalmanager("Error", "Query error.");
                                break;
                        case "003":
                                //Error no test selected
                                $log.log("No test selected. " + res.message);
                                $scope.modalmanager("Error", "There is no test selected, please go to Home and select one.");
                                break;
                        default:
                                $log.log("Unknown error. Message:" + res.message);
                                $scope.modalmanager("Error", "Unknown error, check the log to see more information");
                }
            }, function(err){
                // error
                $log.log("Error in the promise");
                $scope.modalmanager("Error", "Error in the promise");
            })
        }
    }


    $scope.init();	
	
}]);

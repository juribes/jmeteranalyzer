'use strict';

/**
 * @ngdoc function
 * @name jMeterlyser.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the jMeterlyser
 */
angular.module('jMeterlyser')
  .controller('MainCtrl', [ '$scope', '$location', '$log', 'services', 'navlocation', 'testid', function ($scope, $location, $log, services, navlocation, testid){

    $scope.init = function(){
        $scope.startview();
    }

	$scope.test = testid;
	$scope.location = navlocation;
	$scope.location.locURL = $location.path();

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
					$scope.executions = res.message.tables;
					$scope.test.testname = res.message.selected;
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
        services.setexecution($scope.testname)
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
					$scope.test.testname = res.message.message;
                    $log.log("Successful: set execution");
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
	
	$scope.createexecution = function(){ 
        services.createexecution($scope.newtest)
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
					$scope.test.testname = res.message.message;
                    $log.log("Successful: create execution");
					$scope.modalmanager("Info", "Test created succefully");
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
	
	$scope.init();	
	
	}]);

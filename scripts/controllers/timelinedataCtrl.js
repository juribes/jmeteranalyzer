'use strict';

/**
 * @ngdoc function
 * @name jMeterlyser.controller:timelineDataCtrl
 * @description
 * # timelineDataCtrl
 * Controller of the jMeterlyser
 */
angular.module('jMeterlyser')
  .controller('timelineDataCtrl', [ '$scope', '$location', '$log', 'services', 'interData', 'navlocation', function ($scope, $location, $log, services, interData, navlocation) {

    $scope.init = function(){
        $scope.startview();
		$scope.location.locURL = $location.path();
    }
	
	$scope.location = navlocation;
	
	$scope.formcontent = interData;
	
    $scope.timelinetable = function(){ 
        services.timelinedata($scope.formcontent.InitialTime, $scope.formcontent.FinalTime, $scope.formcontent.req)
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
                    //ok location
                    $scope.dataset = res.message;
                    $log.log("Successful timeline data query");
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

	$scope.iconT        = "glyphicon glyphicon-sort-by-attributes";
    $scope.iconM        = "glyphicon glyphicon-sort";

	$scope.startview = function(){ 
        services.listreq()
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
					$scope.requests = res.message;
                    $log.log("Successful list requests query");
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

'use strict';

/**
 * @ngdoc function
 * @name JMeteranalyzer.controller:timelineDataCtrl
 * @description
 * # timelineDataCtrl
 * Controller of the JMeteranalyzer
 */
angular.module('JMeteranalyzer')
  .controller('timelineDataCtrl', [ '$scope', '$location', '$log', 'services', 'interData', 'navlocation', function ($scope, $location, $log, services, interData, navlocation) {

    $scope.init = function(){
        $scope.startview();
        $scope.location.locURL = $location.path();
    }
	
	$scope.location = navlocation;
	
	$scope.formcontent = interData;
	
	$scope.modalmanager = function(mtitle, mmessage){
		$scope.modaltitle = mtitle;
		$scope.modalmessage = mmessage;
		$('#myModal').modal('show');
	}
	
    $scope.timelinetable = function(){
		$('#ModalLoading').modal('show');
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
                case "003":
                    //Error no test selected
                    $log.log("No test selected. " + res.message);
                    $scope.modalmanager("Error", "There is no test selected, please go to Home and select one.");
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

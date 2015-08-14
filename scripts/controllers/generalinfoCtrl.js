'use strict';

/**
 * @ngdoc function
 * @name JMeteranalyzer.controller:generalinfoCtrl
 * @description
 * # generalinfoCtrl
 * Controller of the JMeteranalyzer
 */
angular.module('JMeteranalyzer')
  .controller('generalinfoCtrl', [ '$scope', '$location', '$log', 'services', 'navlocation', function ($scope, $location, $log, services, navlocation) {

    $scope.init = function(){
        $scope.startview();
		$scope.location.locURL = $location.path();
    }
	
	$scope.location = navlocation;
	
	$scope.modalmanager = function(mtitle, mmessage){
		$scope.modaltitle = mtitle;
		$scope.modalmessage = mmessage;
		$('#myModal').modal('show');
	}
	
    $scope.startview = function(){ 
        services.generalinfo()
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
                    $scope.info=res.message;
                    $log.log("Successful general info query");
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

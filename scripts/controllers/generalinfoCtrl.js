'use strict';

/**
 * @ngdoc function
 * @name jMeterlyser.controller:generalinfoCtrl
 * @description
 * # generalinfoCtrl
 * Controller of the jMeterlyser
 */
angular.module('jMeterlyser')
  .controller('generalinfoCtrl', [ '$scope', '$location', '$log', 'services', 'navlocation', function ($scope, $location, $log, services, navlocation) {

    $scope.init = function(){
        $scope.startview();
		$scope.location.locURL = $location.path();
    }
	
	$scope.location = navlocation;
	
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

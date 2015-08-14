'use strict';

/**
 * @ngdoc function
 * @name JMeteranalyzer.controller:percentilesCtrl
 * @description
 * # percentilesCtrl
 * Controller of the JMeteranalyzer
 */
angular.module('JMeteranalyzer')
  .controller('percentilesCtrl', [ '$scope', '$location', '$log', 'services', 'navlocation', function ($scope, $location, $log, services, navlocation) {
  
    $scope.init = function(){
        $scope.startview();
		$scope.location.locURL = $location.path();
    }
	
	$scope.location = navlocation;
	$scope.dataGraph = {};
	
	$scope.modalmanager = function(mtitle, mmessage){
		$scope.modaltitle = mtitle;
		$scope.modalmessage = mmessage;
		$('#myModal').modal('show');
	}
	
    $scope.startview = function(){ 
        services.percentiles()
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
                    $scope.dataGraph = res.message;
                    var chart = new CanvasJS.Chart("chartContainer", $scope.dataGraph);
                    chart.render();
                    $log.log("Successful get percentiles");
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
    },
	
    $scope.init();
  }]);

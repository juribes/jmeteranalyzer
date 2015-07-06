'use strict';

/**
 * @ngdoc function
 * @name jMeterlyser.controller:percentilesCtrl
 * @description
 * # percentilesCtrl
 * Controller of the jMeterlyser
 */
angular.module('jMeterlyser')
  .controller('percentilesCtrl', [ '$scope', '$location', '$log', 'services', 'navlocation', function ($scope, $location, $log, services, navlocation) {
  
    $scope.init = function(){
        $scope.startview();
		$scope.location.locURL = $location.path();
    }
	
	$scope.location = navlocation;
	$scope.dataGraph = {};
	
    $scope.startview = function(){ 
        services.percentiles()
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
					$scope.dataGraph = res.message;
					var chart = new CanvasJS.Chart("chartContainer", $scope.dataGraph);
					chart.render();
                    $log.log("Successful timeline query");
                    break;
                case "001":
                    //Error de coneción a la base de datos
                    $log.log("DB connection error");
                    alert($scope.res.message); //MEJORAR AQUI: no alerts
                    break;
                case "002":
                    //Error en el query
                    $log.log("Query error");
                    alert($scope.res.message); //MEJORAR AQUI: no alerts
                    break;
                default:
                    alert("Unknown error???"); //MEJORAR AQUI: no alerts
                    $log.log("Unknown error");
            }   
            
        }, function(err){
            // error
            alert("Error in the promise"); //MEJORAR AQUI: no alerts
            $log.log("Error in the promise");
        })
    },
	
	$scope.init();
  }]);

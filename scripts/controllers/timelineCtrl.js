'use strict';

/**
 * @ngdoc function
 * @name jMeterlyser.controller:timelineCtrl
 * @description
 * # timelineCtrl
 * Controller of the jMeterlyser
 */
angular.module('jMeterlyser')
  .controller('timelineCtrl', [ '$scope', '$location', '$log', 'services', 'interData', 'navlocation', function ($scope, $location, $log, services, interData, navlocation) {
  
    $scope.init = function(){
        $scope.startview();
		$scope.location.locURL = $location.path();
    }
	
	$scope.location = navlocation;
	
	$scope.formcontent = interData;
	
    $scope.graphTL = function(){ 
        services.timeline($scope.formcontent.InitialTime, $scope.formcontent.FinalTime, $scope.formcontent.req)
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
					var dataGraph = res.message;
					var i ,j, d;
					for (i in dataGraph.data) {
						for (j in dataGraph.data[i].dataPoints) {
							d=new Date(dataGraph.data[i].dataPoints[j].x);
							dataGraph.data[i].dataPoints[j].x=d;
						}
					}
				
					var chart = new CanvasJS.Chart("chartContainer",dataGraph);
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
	
	$scope.startview = function(){ 
        services.listreq()
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
					$scope.requests = res.message;
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
    }
	
	
	$scope.init();
  }]);

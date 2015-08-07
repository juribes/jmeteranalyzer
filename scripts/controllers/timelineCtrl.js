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
	
	$scope.modalmanager = function(mtitle, mmessage){
		$scope.modaltitle = mtitle;
		$scope.modalmessage = mmessage;
		$('#myModal').modal('show');
	}
	
	$scope.timezone = function(){
		var tzdate = new Date();
		var tz = tzdate.getTimezoneOffset();
		var timeg = Math.abs((-1)*(tz/60)*100);
		if (timeg <= 0){
			var timet = "+";
		}else{
			var timet = "-";
		}
		timeg = Math.abs(timeg);
		tz  = "0000" + timeg.toString();
		return (timet + tz.substr(tz.length-4));
	}
	
    $scope.graphTL = function(){ 
        services.timeline($scope.formcontent.InitialTime, $scope.formcontent.FinalTime, $scope.formcontent.req)
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
                    var dataGraph = res.message;
                    var i ,j, d, tz;
                    tz = $scope.timezone();
                    for (i in dataGraph.data) {
                            for (j in dataGraph.data[i].dataPoints) {
                                    d=new Date(dataGraph.data[i].dataPoints[j].x + tz);
                                    dataGraph.data[i].dataPoints[j].x=d;
                            }
                    }

                    var chart = new CanvasJS.Chart("chartContainer",dataGraph);
                    chart.render();
                    $log.log("Successful timeline query");
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

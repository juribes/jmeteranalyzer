'use strict';

/**
 * @ngdoc function
 * @name JMeteranalyzer.controller:summaryCtrl
 * @description
 * # summaryCtrl
 * Controller of the JMeteranalyzer
 */
angular.module('JMeteranalyzer')
  .controller('summaryCtrl', [ '$scope', '$location', '$log', 'services', 'interData', 'navlocation', function ($scope, $location, $log, services, interData, navlocation) {

    $scope.init = function(){
        $scope.order = "label";
        $scope.location.locURL = $location.path();
        $scope.sortGUI("label","asc");
    }
	
	$scope.location = navlocation;
        
	$scope.iconx = {'label': "glyphicon glyphicon-sort", 'Samples': "glyphicon glyphicon-sort", 'AVG': "glyphicon glyphicon-sort", 'MAX': "glyphicon glyphicon-sort", 'MIN': "glyphicon glyphicon-sort", 'StdDev': "glyphicon glyphicon-sort"};
        
	$scope.formcontent = interData;
        
	$scope.modalmanager = function(mtitle, mmessage){
		$scope.modaltitle = mtitle;
		$scope.modalmessage = mmessage;
		$('#myModal').modal('show');
	}
	
    $scope.summarytable = function(){ 
        services.summary($scope.formcontent.InitialTime, $scope.formcontent.FinalTime)
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
                    //ok location
                    var i;
                    $scope.dataset=res.message;
                    $log.log("Successful summay query");
                    for (i in $scope.dataset) {
                        $scope.dataset[i].Samples= parseFloat($scope.dataset[i].Samples);
                        $scope.dataset[i].AVG= parseFloat($scope.dataset[i].AVG);
                        $scope.dataset[i].MAX= parseFloat($scope.dataset[i].MAX);
                        $scope.dataset[i].MIN= parseFloat($scope.dataset[i].MIN);
                        $scope.dataset[i].StdDev= parseFloat($scope.dataset[i].StdDev);
                    }
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

    $scope.sort = function(tipo){
        switch (tipo){
            case "label":
                if ($scope.order=="label"){
                    $scope.order="-label";
                    $scope.sortGUI("label","des");
                }else{
                    $scope.order="label";
                    $scope.sortGUI("label","asc");
                }
                break;
            case "Samples":
                if ($scope.order=="Samples"){
                    $scope.order="-Samples";
                    $scope.sortGUI("Samples","des");
                }else{
                    $scope.order="Samples";
                    $scope.sortGUI("Samples","asc");
                }
                break;
            case "AVG":
                if ($scope.order=="AVG"){
                    $scope.order="-AVG";
                    $scope.sortGUI("AVG","des");
                }else{
                    $scope.order="AVG";
                    $scope.sortGUI("AVG","asc");
                }
                break;
            case "MAX":
                if ($scope.order=="MAX"){
                    $scope.order="-MAX";
                    $scope.sortGUI("MAX","des");
                }else{
                    $scope.order="MAX";
                    $scope.sortGUI("MAX","asc");
                }
                break;
            case "MIN":
                if ($scope.order=="MIN"){
                    $scope.order="-MIN";
                    $scope.sortGUI("MIN","des");
                }else{
                    $scope.order="MIN";
                    $scope.sortGUI("MIN","asc");
                }
                break;
            case "StdDev":
                if ($scope.order=="StdDev"){
                    $scope.order="-StdDev";
                    $scope.sortGUI("StdDev","des");
                }else{
                    $scope.order="StdDev";
                    $scope.sortGUI("StdDev","asc");
                }
                break;
            default:;
        }
    }
    $scope.sortGUI = function(tipo, direction){
        var i;
        for (i in $scope.iconx){
            $scope.iconx[i] = "glyphicon glyphicon-sort";
        }
        if (direction=="asc"){
            $scope.iconx[tipo] = "glyphicon glyphicon-sort-by-attributes";
        }else{
            $scope.iconx[tipo] = "glyphicon glyphicon-sort-by-attributes-alt";
        }
    }
        
    $scope.init();
  }]);

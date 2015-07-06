'use strict';

/**
 * @ngdoc function
 * @name jMeterlyser.controller:summaryCtrl
 * @description
 * # summaryCtrl
 * Controller of the jMeterlyser
 */
angular.module('jMeterlyser')
  .controller('summaryCtrl', [ '$scope', '$location', '$log', 'services', 'interData', 'navlocation', function ($scope, $location, $log, services, interData, navlocation) {

    $scope.init = function(){
        $scope.orden = "";
		$scope.location.locURL = $location.path();
    }
	
	$scope.location = navlocation;
	
	$scope.formcontent = interData;
	
    $scope.summarytable = function(){ 
        services.summary($scope.formcontent.InitialTime, $scope.formcontent.FinalTime)
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
                    //ok location
                    $scope.dataset=res.message;
                    $log.log("Successful summay query");
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

	$scope.iconT        = "glyphicon glyphicon-sort-by-attributes";
    $scope.iconM        = "glyphicon glyphicon-sort";

/*	
    $scope.sort = function(tipo){
        switch ($scope.orden){
            case "":
                switch(tipo){
                    case "titulo":
                        $scope.orden = "titulo";
                        $scope.sortUI("TituloA");
                        break;
                    case "modificacion":
                        $scope.orden = "modificacion";
                        $scope.sortUI("ModificacionA");
                        break;
                    default:;
                }
                break;
            case "Request":
                switch(tipo){
                    case "Request":
                        $scope.orden = "-Request";
                        $scope.sortUI("RequestD");
                        break;
                    case "modificacion":
                        $scope.orden = "modificacion";
                        $scope.sortUI("ModificacionA");
                        break;
                    default:;
                }
                break; 
            case "-Request":
                switch(tipo){
                    case "titulo":
                        $scope.orden = "titulo";
                        $scope.sortUI("TituloA");
                        break;
                    case "modificacion":
                        $scope.orden = "modificacion";
                        $scope.sortUI("ModificacionA");
                        break;
                    default:;
                }
                break; 
            case "modificacion":
                switch(tipo){
                    case "titulo":
                        $scope.orden = "titulo";
                        $scope.sortUI("TituloA");
                        break;
                    case "modificacion":
                        $scope.orden = "-modificacion";
                        $scope.sortUI("ModificacionD");
                        break;
                    default:;
                }
                break;
            case "-modificacion":
                switch(tipo){
                    case "titulo":
                        $scope.orden = "titulo";
                        $scope.sortUI("TituloA");
                        break;
                    case "modificacion":
                        $scope.orden = "modificacion";
                        $scope.sortUI("ModificacionA");
                        break;
                    default:;
                }
                break;
            default:;
        }
    }	
*/
	
	
	
	
	
	
	$scope.init();
  }]);

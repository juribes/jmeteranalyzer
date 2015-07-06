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
                    //ok location
                    $scope.info=res.message;
					$scope.holaa=services.hola;//Test
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

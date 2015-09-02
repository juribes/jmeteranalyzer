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

    $scope.startview = function(){ 
		//$('#ModalLoading').modal('show');
        services.generalinfo()
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
                    $scope.info = res.message;
                    $scope.dataGTLG = res.message.gtlgraph;
                    $scope.dataRCG = res.message.rcgraph;
                    var i ,j, d, tz;
                    tz = $scope.timezone();
                    for (i in  $scope.dataGTLG.data) {
                            for (j in  $scope.dataGTLG.data[i].dataPoints) {
                                    d=new Date( $scope.dataGTLG.data[i].dataPoints[j].x + tz);
                                     $scope.dataGTLG.data[i].dataPoints[j].x=d;
                            }
                    }
                    $scope.dataGTLG.legend.itemclick = function (e) {if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {e.dataSeries.visible = false;} else {e.dataSeries.visible = true;}e.chart.render();};
                    var chartGTLG = new CanvasJS.Chart("chartContainerGTLG",  $scope.dataGTLG);
                    chartGTLG.render();
                    var chartRC = new CanvasJS.Chart("chartContainerRC",  $scope.dataRCG);
                    chartRC.render();
                    
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
			//$('#ModalLoading').modal('hide');
        }, function(err){
            // error
			//$('#ModalLoading').modal('hide');
            $log.log("Error in the promise");
            $scope.modalmanager("Error", "Error in the promise");
        })
    }

	$scope.init();
  }]);

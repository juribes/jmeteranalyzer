'use strict';

/**
 * @ngdoc function
 * @name JMeteranalyzer.controller:uploadCtrl
 * @description
 * # uploadCtrl
 * Controller of the JMeteranalyzer
 */
angular.module('JMeteranalyzer')
  .controller('uploadCtrl', [ '$scope', '$location', 'navlocation', 'FileUploader', 'services', 'interData', '$log', function ($scope, $location, navlocation, FileUploader, services, interData, $log) {

		$scope.location = navlocation;
		$scope.formcontent = interData;
		$scope.location.locURL = $location.path();
		$scope.showAnalize	=	false;
		$scope.multifile	=	false;
		$scope.fileid		=	"";
		$scope.servname		=	"";
		
	    var uploader = $scope.uploader = new FileUploader({
            url: 'services/upload.php'
        });

        // FILTERS

        uploader.filters.push({
            name: 'customFilter',
            fn: function(item /*{File|FileLikeObject}*/, options) {
                return this.queue.length < 10;
            }
        });

        // CALLBACKS

        uploader.onWhenAddingFileFailed = function(item /*{File|FileLikeObject}*/, filter, options) {
            console.info('onWhenAddingFileFailed', item, filter, options);
        };
        uploader.onAfterAddingFile = function(fileItem) {
            console.info('onAfterAddingFile', fileItem);
        };
        uploader.onAfterAddingAll = function(addedFileItems) {
            console.info('onAfterAddingAll', addedFileItems);
        };
        uploader.onBeforeUploadItem = function(item) {
            console.info('onBeforeUploadItem', item);
        };
        uploader.onProgressItem = function(fileItem, progress) {
            console.info('onProgressItem', fileItem, progress);
        };
        uploader.onProgressAll = function(progress) {
            console.info('onProgressAll', progress);
        };
        uploader.onSuccessItem = function(fileItem, response, status, headers) {
            console.info('onSuccessItem', fileItem, response, status, headers);
        };
        uploader.onErrorItem = function(fileItem, response, status, headers) {
            console.info('onErrorItem', fileItem, response, status, headers);
        };
        uploader.onCancelItem = function(fileItem, response, status, headers) {
            console.info('onCancelItem', fileItem, response, status, headers);
        };
        uploader.onCompleteItem = function(fileItem, response, status, headers) {
            console.info('onCompleteItem', fileItem, response, status, headers);
        };
        uploader.onCompleteAll = function() {
            console.info('onCompleteAll');
        };

        console.info('uploader', uploader);
	
		// JCUS
		
	$scope.modalmanager = function(mtitle, mmessage){
		$scope.modaltitle = mtitle;
		$scope.modalmessage = mmessage;
		$('#myModal').modal('show');
	}
		
	$scope.process = function(filename, servername, multifile){ 
		$('#ModalConfirm').modal('hide');
		$scope.modalverb = "Processing...";
		$('#ModalLoading').modal('show');
		$scope.fileid = filename;
		$scope.servname = servername; 
        services.process(filename, servername, multifile)
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
                    $scope.respuesta = res.message;
                    $log.log("Successful process file");
                    $scope.modalmanager("Info", $scope.respuesta);
                    $scope.showAnalize = true;
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
                case "005":
                    //Multifile Confirmation
                    $log.log("Multifile conformation. " + res.message);
                    $scope.modaltitle = "Confirm Multifile";
                    $scope.modalmessage = res.message;
                    $('#ModalConfirm').modal('show');	
                    break;
                default:
                    $log.log("Unknown error. Message:" + res.message);
                    $scope.modalmanager("Error", "Unknown error, check the log to see more information");
            }   
            $('#ModalLoading').modal('hide');
        }, function(err){
            // error
            $('#ModalLoading').modal('hide');
            $log.log("Error in the promise");
            $scope.modalmanager("Error", "Error in the promise");
        })
    }

	$scope.analyze = function(){
		$scope.modalverb = "Analyzing...";
		$('#ModalLoading').modal('show');
        services.analyze()
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
                    $scope.respuesta = res.message.message;
                    $scope.formcontent.InitialTime = res.message.starttime;
                    $scope.formcontent.FinalTime = res.message.finishtime;
                    $scope.formcontent.multifile = res.message.multifile;
                    $log.log("Test analized successfully");
                    $scope.modalmanager("Info", $scope.respuesta);
                    $scope.showAnalize	=	false;
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
                case "006":
                    //Error no files processed
                    $log.log(res.message);
                    $scope.modalmanager("Error", res.message);
                    break;
                default:
                    $log.log("Unknown error. Message:" + res.message);
                    $scope.modalmanager("Error", "Unknown error, check the log to see more information");
            }   
            $('#ModalLoading').modal('hide');
        }, function(err){
            // error
			$('#ModalLoading').modal('hide');
            $log.log("Error in the promise");
            $scope.modalmanager("Error", "Error in the promise");
        })
    }
	
  }]);

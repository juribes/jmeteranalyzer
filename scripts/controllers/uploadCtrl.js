'use strict';

/**
 * @ngdoc function
 * @name jMeterlyser.controller:uploadCtrl
 * @description
 * # uploadCtrl
 * Controller of the jMeterlyser
 */
angular.module('jMeterlyser')
  .controller('uploadCtrl', [ '$scope', '$location', 'navlocation', 'FileUploader', 'services', '$log', function ($scope, $location, navlocation, FileUploader, services, $log) {

		$scope.location = navlocation;
		$scope.location.locURL = $location.path();
		
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
		$scope.process = function(filename, servername){ 
        services.process(filename, servername)
        .then(function(res){
            // success
            switch(res.code) {
                case "000":
					$scope.respuesta = res.message;
					alert($scope.respuesta);
                    $log.log("Successful process file");
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
	
  }]);

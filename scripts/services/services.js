'use strict';

/**
 * @ngdoc service
 * @name JMeteranalyzer.services
 * @description
 * # services
 * Factory in the JMeteranalyzer.
 */
angular.module('JMeteranalyzer')
  .service('services',['$http', '$log', '$q', function($http, $log, $q) {
    
    var servicio = this;
    
    servicio.summary = function(InitialTime, FinalTime){
        var defer = $q.defer();
        $http.get('services/summary.php?iniTime='+InitialTime+'&endTime='+FinalTime)
        .success(function(res){
            defer.resolve(res);
        })
        .error(function(err){
            defer.reject(err);
        });
        return defer.promise;
    };
 
    servicio.timeline = function(InitialTime,FinalTime,req){
        var defer = $q.defer();
        $http.get('services/timeline.php?iniTime='+InitialTime+'&endTime='+FinalTime+'&request='+req)
        .success(function(res){
            defer.resolve(res);
        })
        .error(function(err){
            defer.reject(err);
        });
        return defer.promise;
    };
 
    servicio.timelinedata = function(InitialTime,FinalTime,req){
        var defer = $q.defer();
        $http.get('services/timelinedata.php?iniTime='+InitialTime+'&endTime='+FinalTime+'&request='+req)
        .success(function(res){
            defer.resolve(res);
        })
        .error(function(err){
            defer.reject(err);
        });
        return defer.promise;
    };
 
    servicio.generalinfo = function(){
        var defer = $q.defer();
        $http.get('services/generalinfo.php')
        .success(function(res){
            defer.resolve(res);
        })
        .error(function(err){
            defer.reject(err);
        });
        return defer.promise;
    };
 
    servicio.listreq = function(){
        var defer = $q.defer();
        $http.get('services/listrequests.php')
        .success(function(res){
            defer.resolve(res);
        })
        .error(function(err){
            defer.reject(err);
        });
        return defer.promise;
    };
 
     servicio.percentiles = function(){
        var defer = $q.defer();
        $http.get('services/percentiles.php')
        .success(function(res){
            defer.resolve(res);
        })
        .error(function(err){
            defer.reject(err);
        });
        return defer.promise;
    };
	
	servicio.process = function(filename, servername, multifile){
        var defer = $q.defer();
        $http.get('services/processfile.php?filename='+filename+'&servername='+servername+'&multifile='+multifile)
        .success(function(res){
            defer.resolve(res);
        })
        .error(function(err){
            defer.reject(err);
        });
        return defer.promise;
    };

    servicio.setexecution = function(execution){
        var defer = $q.defer();
        $http.get('services/setexecution.php?execution='+execution)
        .success(function(res){
            defer.resolve(res);
        })
        .error(function(err){
            defer.reject(err);
        });
        return defer.promise;
    };

    servicio.createexecution = function(execution){
        var defer = $q.defer();
        $http.get('services/createexecution.php?execution='+execution)
        .success(function(res){
            defer.resolve(res);
        })
        .error(function(err){
            defer.reject(err);
        });
        return defer.promise;
    };

    servicio.getexecutions = function(){
        var defer = $q.defer();
        $http.get('services/getexecutions.php')
        .success(function(res){
            defer.resolve(res);
        })
        .error(function(err){
            defer.reject(err);
        });
        return defer.promise;
    };	
	
    servicio.analyze = function(){
        var defer = $q.defer();
        $http.get('services/analyze.php')
        .success(function(res){
            defer.resolve(res);
        })
        .error(function(err){
            defer.reject(err);
        });
        return defer.promise;
    };		

    servicio.deletetest = function(execution){
        var defer = $q.defer();
        $http.get('services/delete.php?execution='+execution)
        .success(function(res){
            defer.resolve(res);
        })
        .error(function(err){
            defer.reject(err);
        });
        return defer.promise;
    };
          
  }]);

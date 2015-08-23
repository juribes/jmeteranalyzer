'use strict';

/**
 * @ngdoc overview
 * @name JMeteranalyzer
 * @description
 * # JMeteranalyzer
 *
 * Main module of the application.
 */
angular
  .module('JMeteranalyzer', [
    'ngAnimate',
    'ngCookies',
    'ngResource',
    'ngRoute',
    'ngSanitize',
    'ngTouch',
	'angularFileUpload'
  ])
  .config(function ($routeProvider) {
    $routeProvider
	    .when('/', {
          templateUrl: 'views/main.html',
          controller: 'mainCtrl'
        })
		.when('/upload', {
          templateUrl: 'views/upload.html',
          controller: 'uploadCtrl'
        })
        .when('/summary', {
          templateUrl: 'views/summary.html',
          controller: 'summaryCtrl'
        })
        .when('/timelineData', {
          templateUrl: 'views/timelinedata.html',
          controller: 'timelineDataCtrl'
        })
        .when('/timeline', {
          templateUrl: 'views/timeline.html',
          controller: 'timelineCtrl'
        })
        .when('/general_info', {
          templateUrl: 'views/generalinfo.html',
          controller: 'generalinfoCtrl'
        })
		.when('/percentiles', {
          templateUrl: 'views/percentiles.html',
          controller: 'percentilesCtrl'
        })
        .otherwise({
          redirectTo: '/'
        });
  });

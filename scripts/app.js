'use strict';

/**
 * @ngdoc overview
 * @name jMeterlyser
 * @description
 * # jMeterlyser
 *
 * Main module of the application.
 */
angular
  .module('jMeterlyser', [
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
          controller: 'MainCtrl'
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
          templateUrl: 'views/timelineData.html',
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

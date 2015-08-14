'use strict';

/**
 * @ngdoc function
 * @name JMeteranalyzer.controller:navbarCtrl
 * @description
 * # navbarCtrl
 * Controller of the JMeteranalyzer
 */
angular.module('JMeteranalyzer')
  .controller('navbarCtrl', [ '$scope', '$location', 'navlocation', 'testid', function ($scope, $location, navlocation, testid) {
		$scope.location = navlocation;
		$scope.test = testid;
  }]);

'use strict';

/**
 * @ngdoc function
 * @name jMeterlyser.controller:navbarCtrl
 * @description
 * # navbarCtrl
 * Controller of the jMeterlyser
 */
angular.module('jMeterlyser')
  .controller('navbarCtrl', [ '$scope', '$location', 'navlocation', 'testid', function ($scope, $location, navlocation, testid) {
		$scope.location = navlocation;
		$scope.test = testid;
  }]);

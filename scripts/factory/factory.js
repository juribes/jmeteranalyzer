'use strict';

/**
 * @ngdoc factory
 * @name jMeterlyser.factory
 * @description
 * # factory
 * Factory in the jMeterlyser.
 */
angular.module('jMeterlyser')
  .factory('interData', function() {	  
	    return {
			InitialTime : '2015-06-24 09:24:53',
			FinalTime : '2015-06-24 10:24:53',
			req : ''
		};
  });
  
angular.module('jMeterlyser')
  .factory('navlocation', ['$location', function($location) {	  
	    return {locURL: "/"};
  }]);
  
angular.module('jMeterlyser')
  .factory('testid', ['$location', function($location) {	  
	    return {testname: ""};
  }]);
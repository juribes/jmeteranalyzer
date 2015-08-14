'use strict';

/**
 * @ngdoc factory
 * @name JMeteranalyzer.factory
 * @description
 * # factory
 * Factory in the JMeteranalyzer.
 */
angular.module('JMeteranalyzer')
  .factory('interData', function() {	  
	    return {
			InitialTime : '',
			FinalTime : '',
			req : ''
		};
  });
  
angular.module('JMeteranalyzer')
  .factory('navlocation', ['$location', function($location) {	  
	    return {locURL: "/"};
  }]);
  
angular.module('JMeteranalyzer')
  .factory('testid', ['$location', function($location) {	  
	    return {testname: ""};
  }]);
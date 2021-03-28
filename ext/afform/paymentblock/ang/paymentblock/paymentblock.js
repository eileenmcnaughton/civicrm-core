(function(angular, $, _) {
  // "paymentblock" is a basic skeletal directive.
  // Example usage: <div paymentblock="{foo: 1, bar: 2}"></div>
  angular.module('paymentblock').directive('paymentblock', function() {
    return {
      restrict: 'AE',
      templateUrl: '~/paymentblock/paymentblock.html',
      scope: {
        paymentblock: '='
      },
      link: function($scope, $el, $attr) {
        var ts = $scope.ts = CRM.ts('paymentblock');
        $scope.$watch('paymentblock', function(newValue){
          $scope.myOptions = newValue;
        });
      }
    };
  });
})(angular, CRM.$, CRM._);

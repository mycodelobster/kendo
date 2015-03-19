<script type="text/javascript">
	app.controller('Ctrl',function($scope, $http,MyFactory){

		var baseUrl = MyFactory.rootUrl+'propinsi';
		var ds = {
			transport:{read:{url: baseUrl+'/get_all', dataType: 'json', tipe: 'POST'}}
		};

		$scope.options = {
			dataSource: ds,
			toolbar: [{template:"<div kendo-button ng-click='edit(false)' sprite-css-class=\"'k-icon k-i-plus'\">Add New</div>"}],
			columns:[
			{field:'NAMA_PROPINSI', title:'NAMA PROPINSI'},
			{command:[{template:"<div kendo-button ng-click='edit(dataItem)' sprite-css-class=\"'k-icon k-i-pencil'\" >Edit</div>"},
			{template:"<div kendo-button ng-click='delete(dataItem)' sprite-css-class=\"'k-icon k-i-close'\" >Delete</div>"}]}
			],
			sortable:true,
			filterable:{mode:'row'},
		};

		$scope.edit = function(data){

			$scope.modal = (data==false) ? '' : angular.copy(data);
			$scope.window.center().open();

			$scope.validate = function(event){
				event.preventDefault();
				if ($scope.validator.validate()){

					$http.post(baseUrl+'/update', $scope.modal).success(function(res){
						console.log(res);
						if(res.error){$scope.notifModal.show(res.error, "warning") }
						else{
							$scope.grid.dataSource.read();
							$scope.window.close();
						}
					});
				}
			};
		};

		$scope.delete = function(dataItem){
			$scope.confirm.center().open();
			$scope.yes = function(){
				$http.post(baseUrl+'/delete', dataItem).success(function(res){
					console.log(res);
					if(res.error){$scope.notifConfirm.show(res.error, "warning")}
					else{
						$scope.grid.dataSource.read();
						$scope.confirm.close();
					}
				});
			};
		};

	});
</script>

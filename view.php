<div class="container" ng-app="App" ng-controller="Ctrl">
	<div id="dialog" kendo-window="dialog" k-width="500" k-modal="true" k-visible="false"></div>
	<div id="grid" kendo-grid="grid" k-options="options"></div>
	<div id="window" kendo-window="window" k-visible="false" k-width="300">
	{{dataItem}}
	<input kendo-text-box k-data-source="options.dataSource" ng-model="dataItem.description" >
	</div>
	<?php $this->load->view('script/app')?>
	<script type="text/javascript">
		app.controller('Ctrl', function($scope, $http, MyFactory){

			var fields = {
				stock_id: { editable: false, nullable: true, visible:true },
				description: { validation:{required:true} },
			};
			var columns = [
			{field:'stock_id', title:'Stock'},
			{ field: "description", title: "Category", width: "180px", editor: $scope.categoryDropDownEditor },
			// {command:[{template:"<div kendo-button ng-click='ediThis(dataItem)'>Edit</div>"}]}
			{command:['edit','destroy']}
			];
			$scope.options = MyFactory.gridSetting.extend({
				baseUrl:'app',
				dataSource:{
					schema:{
						model: {
							id: "stock_id",
							fields:fields
						}
					},
					error: error,
				},
				options:{
					toolbar:['create'],
					columns:columns,
					editable:'popup',
				}
			});

			function error(args) {
				if (args.errors) {
					this.cancelChanges();
					$scope.dialog.content(args.errors).center().open();
					$scope.grid.one("dataBinding", function (e) {  
						e.preventDefault();   
					});
				}
			}   
		})
</script>

<div class="container" ng-app="App" ng-controller="Ctrl">
	<div kendo-grid k-data-source="dataSource" k-options="gridOption" />
	<div kendo-window="kModal" k-visible="false" k-width="500" k-title="title" k-modal="true">
		<input kendo-text-box ng-model="modalData.description">
		<hr>
		{{dataItem}}
		<div ng-click="submit()" class="k-primary" kendo-button>Submit</div>
	</div>

	<script type="text/x-kendo-template" id="template">
		<label>Unit Price <input kendo-text-box ng-model="dataItem.description" /></label>
	</script>
</div>
<script type="text/javascript">
	var app = angular.module("App",["kendo.directives"]);
	app.controller("Ctrl", function($scope,$http){

		$scope.dataSource = new kendo.data.DataSource({
			transport:{
				read : {
					url : "http://localhost:8000/erpnow/item/data",
					dataType: "json"
				},
				update : {
					url : "http://localhost:8000/erpnow/item/update",
					dataType: "json",
					type:"POST"
				}
			},
			schema:{
				data:'Data',
				total:'Total',
				error:'error',
				model:{
					id:'stock_id',
					fields:{
						stock_id: { editable: false, nullable: true },
						description: { validation: { required: true } },
					}
				}
			},
			requestEnd:function(e){
				e.preventDefault();
				var type = e.type;
				var response = e.response;

				if(type=='update'){
					var error = response.error;
					if(error){
						alert('xxx');
					}
				}
			},
			error: function (args) {
				alert('x');
				e.preventDefault();
				this.cancelChanges()
			},
			pageSize:10,
			serverPaging:true
		});

		var Grid = {
			pageable:{refresh:true},
			toolbar: ['create',{'template':"<div kendo-button ng-click='modal(dataItem)'>Edit Modal</div>"}],
			editable: {
				mode: "popup",
				// template: kendo.template($("#template").html())
			},
			edit: function (e) {
				var popupWindow = e.container.getKendoWindow();
				e.container.find(".k-edit-form-container").width("auto");
				popupWindow.setOptions({
					width: 640,
					title:'xxxx'
				});
				popupWindow.center();
			},
			error: function(e) {
				this.cancelChanges()
				alert(e.errors);
			}
		}
		Grid.columns = [
		{ field: "stock_id", title: "Product Name" },
		{ field: "description", title: "Product Namesssssssss sssssssssss ssssssssssssss" },
		{command:['edit',{'template':"<div kendo-button ng-click='modal(dataItem)'>Edit Modal</div>"}]}
		];

		$scope.gridOption = Grid;

		$scope.modal = function(dataItem){
			console.log(dataItem);
			$scope.modalData = angular.copy(dataItem);
			$scope.kModal.center().open();

			$scope.submit = function(){
				$http.post("http://localhost:8000/erpnow/item/update", $scope.modalData)
				.success(function(res){
					dataItem = angular.copy($scope.modalData);
					console.log(res);
				})
				$scope.kModal.close();
				// $scope.dataSource.read();
			}
		}

	})
</script>

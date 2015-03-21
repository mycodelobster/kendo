<div kendo-grid="grid"  k-options="GridOptions"></div>
<script type="text/x-kendo-template" id="template" styles="padding:20px">
	<input   ng-model="dataItem.br_name" class="k-textbox"  required>
	<select ng-model="dataItem.debtor_no" kendo-dropdownlist k-options="customers" required></select>
</script>

<span kendo-notification="formError"></span>
<script type="text/javascript">
	app.controller('Ctrl', function($scope, $http){

		var baseUrl = '<?=base_url('home')?>';
		$scope.GridOptions = {
			dataSource: new kendo.data.DataSource({
				// autoSync: true,
				transport:{
					create:{url: baseUrl + '/create', dataType: 'json', type: 'POST'},
					read:{url: baseUrl + '/read', dataType: 'json', type: 'POST'},
					update:{url: baseUrl + '/update', dataType: 'json', type: 'POST'},
					destroy:{url: baseUrl + '/delete', dataType: 'json', type: 'POST'}
				},
				pageSize: 10,
				serverSort: true,
				serverPaging: true,
				serverFiltering: true,
				error: function error(arg) { 
					if (arg.status=="customerror") {

						$scope.formError.getNotifications().parent().remove();
						$scope.formError.show(arg.errors, "error");

						$scope.grid.one("dataBinding", function (e) {   
							e.preventDefault();   
						});

					}
					else if(arg.status=="error"){
						alert(arg.errorThrown);
					}
				},
				requestEnd: function(e){
					console.log(e.response);
				},	
				schema: { 
					data: 'data', total: 'total', errors: 'error',
					model:{
						id:'branch_code',
						fields:{
							branch_code:{ validation:{required:true}},
							debtor_no:{validation:{required:false}},
							name:{validation:{required:false}}
						}
					}
				}
			}),

			columns:[
			{field:'br_name', title:'BRANCH NAME'},
			{field:'name', title:'customers'},
			// {field:'debtor_no', title:'debtor_no'},
			{command:['edit',{ className: "k-primary", name: "destroy", text: "Remove" }]}],

			toolbar:['create'],
			sortable:{allowUnsort: true},
			pageable:{refresh:true},
			filterable:{extra:false, mode:'row'},
			editable:{
				mode:'popup',
				createAt: "top",
				update: true,
				confirmation: true,
				template: $('#template').html(),
				window:{
					width:600,
					title:'',
				}

			},
			edit: function(e){
				$scope.selected = e.model;
				e.container.find(".k-edit-form-container").width("auto");
				e.container.find(".k-edit-label").width('20%');
				e.container.find(".k-edit-field").width('75%');
			},

			
		};

		$scope.customers = {
			dataSource:{
				transport:{
					read:{
						url: 'http://localhost:8000/masterkendo/home/customer',
						dataType: 'json',
						type: 'POST'
					}
				}
			},
			dataTextField:'name',
			dataValueField:'debtor_no',
			optionLabel:'Selects',
			change:function(e){
				$scope.selected.name = this.text();
				$scope.$apply();
			}
		};
		

	
	})
</script>

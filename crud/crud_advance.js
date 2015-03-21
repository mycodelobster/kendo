<div kendo-grid="grid"  k-options="GridOptions"></div>
<span kendo-notification="formError"  k-animation='false'></span>

<script id="popup_editor" type="text/x-kendo-template">
	<label for="br_name" class="k-edit-label">BRANCH NAME</label>
	<input class="k-input k-textbox" name="br_name" data-bind="value:br_name">
</script>

<script id="template" type="text/x-kendo-template">
	<div kendo-dropdownlist k-options="Dropdownxxx"></div>
</script>

<script type="text/javascript">
	app.controller('Ctrl', function($scope, $http){

	
		
		var baseUrl = '<?=base_url('home')?>';
		$scope.GridOptions = {
			dataSource: new kendo.data.DataSource({
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
							br_name:{type:'string', validation:{required:true}}
						}
					}
				}
			}),

			columns:[
			{field:'br_name', title:'BRANCH NAME',
			filterable: {
				cell: {
					template: function (args) {
						args.element.kendoDropDownList({
							dataSource: args.dataSource,
							dataTextField: "br_name",
							dataValueField: "br_name",
							valuePrimitive: true,
							filter:'contains',
							optionLabel: 'Select br_name'
						});
					},
					// showOperators: false
				}
			}},
			{field:'debtor_no', title:'debtor_no', template: "<strong>#: debtor_no # - #: debtor_name # </strong>", 
			filterable: {
				cell: {
					template: function (e) {
						e.element.kendoDropDownList({
							dataSource: e.dataSource,
							dataTextField: "debtor_name",
							dataValueField: "debtor_no",
							valuePrimitive: true,
							filter:'contains',
							optionLabel: 'Select customer'
						});
					},
					showOperators: false
				}
			}},
			{command:['edit','destroy']}],

			toolbar:['create',{template: kendo.template($("#template").html())}],
			sortable:{allowUnsort: true},
			pageable:{refresh:true},
			filterable:{extra:false,mode:'row'},
			editable:{
				mode:'popup',
				createAt: "top",
				update: true,
				confirmation: true,
				template: kendo.template($("#popup_editor").html()),
				window:{
					width:600,
					title:'',
					pinned: true,
					position: { top: 100 }
				}
			},

			edit: function(e){
				e.container.find(".k-edit-form-container").width("auto");
				e.container.find(".k-edit-label").width('20%');
				e.container.find(".k-edit-field").width('75%');
			},

		};

		$scope.Dropdownxxx = {
			dataSource:{
				transport:{
					read:{url: baseUrl + '/customer', dataType: 'json', type: 'POST'},
				}
			},
			dataTextField: 'name',
			dataValueField: 'debtor_no',
			optionLabel: 'Select Option',
			filter: 'contains',
			change: function(e){
				var filter = {field:'debtor_no', operator:'eq',value: this.value()};
				$scope.grid.dataSource.filter(filter);

			}
		}

		$scope.xxx = {
			dataSource:{
				transport:{
					read:{url: baseUrl + '/customer', dataType: 'json', type: 'POST'},
				}
			},
			dataTextField: 'name',
			dataValueField: 'debtor_no',
			optionLabel: 'Select Option',
			filter: 'contains',
			change: function(e){
				$scope.grid.dataSource.filter({ field: "debtor_no", operator: "eq", value: this.value() });
			}
		}

	});
</script>

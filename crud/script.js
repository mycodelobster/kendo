<div kendo-grid="grid"  k-options="GridOptions"></div>
<span kendo-notification="formError"  k-animation='false'></span>
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
					// console.log(e.type);
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
			{field:'br_name', title:'BRANCH NAME'},
			// {field:'br_name', title:'BRANCH NAME', editor:function (container, options) {$('<textarea class="k-textbox" rows="3" data-bind="value: ' + options.field + '"></textarea>').appendTo(container)}},
			{command:['edit','destroy']}],

			toolbar:['create'],
			sortable:{allowUnsort: true},
			pageable:{refresh:true},
			filterable:{extra:false, mode:'row'},
			editable:{
				mode:'popup',
				createAt: "top",
				update: true,
				confirmation: true,
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
				// e.container.find(".k-edit-label:first").remove();
			},
			
		};



	})
</script>

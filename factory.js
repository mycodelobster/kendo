<script type="text/javascript">
	var app = angular.module('App',['kendo.directives']);
	app.factory('MyFactory', function(){
		var config = {};
		config.gridSetting = {

			sourceSetting: {
				transport:{
					read:{url:'get', dataType:'json', type:'POST'},
					create:{url:'create', dataType:'json', type:'POST'},
					update:{url:'update', dataType:'json', type:'POST'},
					destroy:{url:'delete', dataType:'json', type:'POST'},
				},
				schema:{
					data:'data',
					total:'total',
					errors: function(response) {
						return response.error; 
					}
				},
				pageSize:10,
				requestEnd:function(e){
					console.log(e);
				},
				error: function (e) {
					this.cancelChanges();
				},
			},

			gridSetting:{
				resizable: true,
				pageable: {refresh:true},
				sortable: { mode: "multiple", allowUnsort: true },
				filterable: {extra:false, mode:'row'},
				editable: {
					mode: "popup"
				},
				edit: function (e) {
					e.container.find(".k-edit-form-container").width("auto");
					var popupWindow = e.container.getKendoWindow();
					popupWindow.setOptions({
						title: '',
						width: 600,
					});
					popupWindow.center();
				},
				error: function(e) {
					this.cancelChanges()
				},
			},

			extend: function (settings) {

				/* Setup Base URL */
				var transport = this.sourceSetting.transport;
				transport.read.url = settings.baseUrl+'/get';
				transport.create.url = settings.baseUrl+'/create';
				transport.update.url = settings.baseUrl+'/update';
				transport.destroy.url = settings.baseUrl+'/delete';

				/* Extends Source Setting */
				if(typeof settings.dataSource !== 'undefined'){
					this.sourceSetting = $.extend(true, {}, this.sourceSetting, settings.dataSource);
				}
				
				/* Extends Grid Setting */
				if(typeof settings.options !== 'undefined'){
					this.gridSetting = $.extend(true, {}, this.gridSetting, settings.options);
				}
				this.gridSetting.dataSource = new kendo.data.DataSource(this.sourceSetting);
				return this.gridSetting;
			}
		};

		return config;
	});

</script>

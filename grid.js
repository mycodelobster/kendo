<script type="text/javascript">
var app = angular.module("App", [ "kendo.directives" ]);
app.controller('Ctrl', function($scope, $http){
    url = "<?=base_url('customer')?>";

    var productId = 0;
    $scope.gridData = new kendo.data.DataSource({
        transport: {
            read:{
                url:url+'/get_data',
                dataType:'json',
                type:'post'
            },
            update:{
                url:url+'/update',
                dataType:'json',
                type:'post'
            },
            destroy:{
                url:url+'/delete',
                dataType:'json',
                type:'post'
            },
            create:{
                url:url+'/create',
                dataType:'json',
                type:'get'
            },
        },
        requestEnd: function(e) {
            var response = e.response;
            var type = e.type;
            if(type=='update'){
                $scope.notf1.show('Info Updated');
            }
            console.log(type); 
            console.log(response); 
        },
        schema: {
            data: "Data",
            total: "Total",
            model: {
                id: "stock_id",
                fields:
                {
                    stock_id: { editable: false, nullable: true, visible:false },
                    pname: { validation:{required:true} },
                }
            }
        },
        pageSize: 10,
        serverPaging:true,
        serverSorting:true,
        serverFiltering:true,
    });

    $scope.gridOption = {
        dataSource : $scope.gridData,
        columns:[{
            field:'stock_id', 
            title:'Stock ID', 
            width:'300px',
            filterable:true
        },{
            field:'pname', 
            title:'pname', 
            width:'300px',
            filterable:false
        },{ 
            field:'cat_name', 
            title:'cat_name', 
            width:'300px',
            filterable:false
        },{
            command:['edit','destroy'],
            width:'200px'
        },{
            command:{'template':"<div kendo-button ng-click='modal(dataItem)'>Edit Modal</div>"},
            width:'200px'
        }],
        toolbar:['create'],
        toolbar: kendo.template($("#template").html()),
        filterable:true,
        sortable:true,
        pageable:{refresh:true},
        editable:'popup',
    }
  </script>

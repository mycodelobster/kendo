<script type="text/javascript">
var app = angular.module("App", [ "kendo.directives" ]);
app.controller('Ctrl', function($scope, $http){

    $scope.gridData = new kendo.data.DataSource({
        transport: {
            read:{
                url:'/get_data',
                dataType:'json',
                type:'post'
            },
            update:{
                url:'/update',
                dataType:'json',
                type:'post'
            },
            destroy:{
                url:'/delete',
                dataType:'json',
                type:'post'
            },
            create:{
                url:'/create',
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

    $scope.modal = function(dataItem){
        $scope.modal_data = dataItem;
        $scope.win1.center().open();

        $scope.submit = function(){

            $http.post('/test',dataItem).success(function(res){
                console.log(res);
                $scope.win1.close();
                $scope.notf1.show('Info Updated');
            })
        }
    }

    $scope.catList = {
        dataSource: new kendo.data.DataSource({
            transport: {
                read: "api/get_cat",
                data: function () {
                    return result = {
                        category_id: this.value(),
                        description: this.text()
                    };
                }
            },
            schema: {
                data: "data"
            }
        }),
        index:0,
        dataTextField:'description',
        dataValueField:'category_id',
        filter:'contains',
        optionLabel: {
          description: "Select Category",
          category_id: ""
        },
        change: function(e) {
            category_id = e.sender.value();
            if(category_id!=''){
                $scope.gridData.filter({field:"stock_master.category_id", operator: "eq", value:category_id});
            }
        }
    };


    $scope.stockList = {
        dataSource: new kendo.data.DataSource({
            serverFiltering: true,
            transport: {
                read: "api/get_stock",
                dataType:'json',
                parameterMap: function(options, operation) {
                    return {
                        category_id: options.filter.filters[0].value
                    }
                }
            },
            schema: {
                data: "data"
            }
        }),
        cascadeFrom:"category",
        optionLabel: {
          description: "Select Product",
          stock_id: ""
        },
        dataTextField:'description',
        dataValueField:'stock_id',
        filter:'contains',
        index:0,
        autobind:false,
        change: function(e) {
            var stock_id = e.sender.value();
            if(stock_id!=''){
                $scope.gridData.filter({field:"stock_master.stock_id", operator: "eq", value:stock_id});
            }
        },
    };
})
</script>

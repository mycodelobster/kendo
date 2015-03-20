	$scope.demo = function(e){
			curr = this.grid.dataSource.filter;

			$filter = new Array();
			$filter.push(curr);
			$filter.push({field: "branch_code", operator: "contains", value: this.searchQuery });
			$filter.push({field: "br_name", operator: "contains", value: this.searchQuery });
			this.grid.dataSource.filter($filter);
		}

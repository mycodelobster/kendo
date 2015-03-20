
				serverFiltering: true,
				error: function error(e) {        
					if (e.errors) {
						$scope.grid.one("dataBinding", function (e) {   
							e.preventDefault();   
						});
					}
				},

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

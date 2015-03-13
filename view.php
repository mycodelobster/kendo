<div class="container" ng-app="App" ng-controller="Ctrl">

	

	<script type="text/x-kendo-template" id="template">
		<kendo-button icon="'plus'"> ADD NEW</kendo-button>
		<div class="pull-right">
			<kendo-dropdownlist id="category" k-options="catList" style="width: 300px;margin-right: 10px;"> </kendo-dropdownlist>
			<kendo-dropdownlist k-options="stockList" style="width: 300px;margin-right: 10px;"> </kendo-dropdownlist>
		</div>
	</script>
	<div kendo-grid k-options="gridOption"></div>
	<?php $this->load->view('customer/modal')?>
	<?php $this->load->view('customer/script')?>
</div>

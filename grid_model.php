<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Grid_model extends CI_Model {

	private $_table_name;
	public function __construct()
	{
		parent::__construct();
		// $columns[] = "stock_master.stock_id";
		// $where[] = "stock_id='1730d301100'";
		// $join[] = array("stock_category", "stock_category.category_id = stock_master.category_id", "left");
	}

	public function get_data($table_name, $column='' , $where='', $join='')
	{
		$this->_table_name = $table_name;

		$take = $this->input->get_post('take');
		$page = $this->input->get_post('page');
		$skip = $this->input->get_post('skip');
		$pageSize = $this->input->get_post('pageSize');

		$sort = $this->input->get_post('sort');
		$sort_dir = $sort[0]['dir'];
		$sort_field = $sort[0]['field'];

		$filter = $this->input->get_post('filter');
		if( $filter != '' OR $where!='')
		{
			$filterdata = array();
			$filterdata['field'] = $filter['filters'][0]['field'];
			$filterdata['operator'] = $filter['filters'][0]['operator'];
			$filterdata['value'] = $filter['filters'][0]['value'];
			$filterdata['logic'] = $filter['logic'];  


			$this->additional_filter($where, $join);
			$data = $this->get_all_kendo( $column, $take, $skip, $sort_dir, $sort_field, $filterdata);

			$this->additional_filter($where, $join);
			$total_data = $this->count_all_where($filterdata);
		}
		else
		{
			$filterdata = 0;
			$this->additional_filter($where, $join);
			$data = $this->get_all_kendo( $column, $take, $skip, $sort_dir, $sort_field, $filterdata);
			$total_data = $this->count_all();
		}

		$result['Data'] = $data;
		$result['Total'] = $total_data;
		$result['Response'] = 'success';

		header("Content-type: application/json");
		echo json_encode($result);
	}

	function get_all_kendo( $columns, $take, $skip, $sort_dir, $sort_field, $filterdata ) {

		if($columns!='' AND count($columns) > 0 AND is_array($columns))
		{
			foreach ($columns as $value) {
				$this->db->select($value);
			}
		}

		if( isset( $sort_dir ) ){

			if( $filterdata != 0 ){
				$this->db->order_by($sort_field, $sort_dir);
				$this->db->limit($take,$skip);

				if( isset($filterdata['operator']) ) 
				{

					if( $filterdata['operator'] == 'eq' ) {
						$this->db->where($filterdata['field'], $filterdata['value']);
					}
					elseif( $filterdata['operator'] == 'neq' ) {
						$field = $filterdata['field'] . ' != ';
						$this->db->where($field, $filterdata['value']);
					}
					elseif( $filterdata['operator'] == 'startswith' ) {
						$this->db->like($filterdata['field'], $filterdata['value'], 'after');
					}
					elseif( $filterdata['operator'] == 'contains' ) {
						$this->db->like($filterdata['field'], $filterdata['value'], 'both');
					}
					elseif( $filterdata['operator'] == 'doesnotcontain' ) {
						$this->db->not_like($filterdata['field'], $filterdata['value']);
					}
					elseif( $filterdata['operator'] == 'endswith' ) {
						$this->db->like($filterdata['field'], $filterdata['value'],'before');
					}
				}
				$data = $this->db->get($this->_table_name);
			}else{
				$this->db->order_by($sort_field, $sort_dir);
				$this->db->limit($take,$skip);
				$data = $this->db->get($this->_table_name);
			}

		}
		else
		{

			if( $filterdata != 0 )
			{

				if( isset($filterdata['operator']) ) 
				{
					if( $filterdata['operator'] == 'eq' ) {
						$this->db->where($filterdata['field'], $filterdata['value']);
					}
					elseif( $filterdata['operator'] == 'neq' ) {
						$field = $filterdata['field'] . ' != ';
						$this->db->where($field, $filterdata['value']);
					}
					elseif( $filterdata['operator'] == 'startswith' ) {
						$this->db->like($filterdata['field'], $filterdata['value'], 'after');
					}
					elseif( $filterdata['operator'] == 'contains' ) {
						$this->db->like($filterdata['field'], $filterdata['value'], 'both');
					}
					elseif( $filterdata['operator'] == 'doesnotcontain' ) {
						$this->db->not_like($filterdata['field'], $filterdata['value']);
					}
					elseif( $filterdata['operator'] == 'endswith' ) {
						$this->db->like($filterdata['field'], $filterdata['value'],'before');
					}
				}

				$this->db->limit($take,$skip);
				$data = $this->db->get($this->_table_name);
			}else
			{
				$this->db->limit($take,$skip);
				$data = $this->db->get($this->_table_name);
			}

		}
		return $data->result();
	}

	function count_all() {
		$count = $this->db->count_all($this->_table_name);
		return $count;
	}

	function count_all_where($filterdata) 
	{
		if( isset($filterdata['operator']) ) {
			if( $filterdata['operator'] == 'eq' ) {
				$this->db->where($filterdata['field'], $filterdata['value']);
			}
			elseif( $filterdata['operator'] == 'neq' ) {
				$field = $filterdata['field'] . ' !=';
				$this->db->where($field, $filterdata['value']);
			}
			elseif( $filterdata['operator'] == 'startswith' ) {
				$this->db->like($filterdata['field'], $filterdata['value'], 'after');
			}
			elseif( $filterdata['operator'] == 'contains' ) {
				$this->db->like($filterdata['field'], $filterdata['value'], 'both');
			}
			elseif( $filterdata['operator'] == 'doesnotcontain' ) {
				$this->db->not_like($filterdata['field'], $filterdata['value']);
			}
			elseif( $filterdata['operator'] == 'endswith' ) {
				$this->db->like($filterdata['field'], $filterdata['value'],'before');
			}
		}


		$this->db->from($this->_table_name);
		$count = $this->db->count_all_results();
		return $count;
	}

	public function additional_filter($where,$join)
	{

		if($where!='' OR $join!='')
		{
			if(count($where) > 0 && is_array($where))
			{
				foreach ($where as $value) {
					$this->db->where($value);
				}
			}

			if(count($join) > 0 && is_array($join))
			{
				foreach ($join as $value) {
					$this->db->join($value[0],$value[1],$value[2]);
				}
			}

			return $this;
		}
	}

}

/* End of file grid_model.php */
/* Location: ./application/models/grid_model.php */

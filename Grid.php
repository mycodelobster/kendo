<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Grid {

	public $table_name;
	public $where = false;
	public $join = false;
	public $debug = false;
	public $column = false;
	public $CI;

	public function __construct($config)
	{
		$this->CI =& get_instance();
		$this->table_name = $config['table_name'];

		if(isset($config['debug']) && $config['debug'] === true)
		{
			$this->debug = true;
		} 

		if(isset($config['where']) && is_array($config['where']))
		{
			$this->where = $config['where'];
		}

		if(isset($config['join']) && is_array($config['join']))
		{
			$this->join = $config['join'];
		}

		if(isset($config['column']) && is_array($config['column']))
		{
			$this->column = $config['column'];
		}
	}

	public function data()
	{
		/* Get Params from Post Action */
		$take = $this->CI->input->get_post('take');
		$page = $this->CI->input->get_post('page');
		$skip = $this->CI->input->get_post('skip');
		$pageSize = $this->CI->input->get_post('pageSize');
		$sort = $this->CI->input->get_post('sort');
		$filter = $this->CI->input->get_post('filter');


		/* Build Sort */
		if(is_array($sort) && count($sort) > 0)
		{
			foreach ($sort as $key => $value) 
			{
				$field = $value['field'];
				$dir = $value['dir'];
				$this->CI->db->order_by($field, $dir);
			}
		}

		/* Main Query Filter */
		$this->build_filter($filter);
		$this->CI->db->from($this->table_name);
		$this->CI->db->limit($pageSize);
		$this->CI->db->offset($skip);
		$data = $this->CI->db->get()->result();


		/* Total Result Query */
		$this->build_filter($filter);
		$this->CI->db->from($this->table_name);
		$total_data = $this->CI->db->get()->num_rows();

		/* Returning Data */
		if($this->debug)
		{
			$result['Filter'] = $filter;
			$result['LastQuery'] = $this->CI->db->last_query();
			$result['Sort'] = $sort;
		}

		$result['Data'] = $data;
		$result['Total'] = $total_data;
		$result['Response'] = 'success';


		return $result;
	}

	public function build_filter($filter)
	{
		/* Set Columns */
		if(is_array($this->column) && count($this->column) > 0)
		{
			foreach ($this->column as $value) 
			{
				$this->CI->db->select($value);
			}
		}

		/* if any where declare in config params */
		if($this->where)
		{
			foreach ($this->where as $item) 
			{

				if(isset($item['field']) && isset($item['value']))
				{
					$field = $item['field'];
					$value = $item['value'];
					$this->CI->db->where($field, $value);	
				}
			}
		}


		/* if any JOIN declare in config params */
		if($this->join)
		{
			foreach ($this->join as $item) 
			{

				if(isset($item['table']) && isset($item['join_to']) && isset($item['field']) && isset($item['field_to']))
				{

					$table = $item['table'];
					$field = $item['field'];
					$join_to = $item['join_to'];
					$field_to = $item['field_to'];
					$type = 'left';
					if(isset($item['type']))
					{
						$type = $item['type'];
					}
					$relation = "$join_to.$field_to = $table.$field";
					$this->CI->db->join($join_to, $relation , $type);
				}
			}
		}

		if(is_array($filter) && isset($filter['logic']))
		{
			/* Logic not use for no extra filter */
			$logic = $filter['logic'];
			$filters = $filter['filters'];

			foreach ($filters as $params) 
			{
				$this->filter_query($params);
			}
		}
	}

	public function filter_query($params)
	{
		$field = $params['field'];
		$operator = $params['operator'];
		$value = $params['value'];

		if($operator == 'eq') 
		{
			$this->CI->db->where($field, $value);
		}
		elseif( $operator == 'neq' ) 
		{
			$field = $field . ' != ';
			$this->CI->db->where($field, $value);
		}
		elseif( $operator == 'startswith' ) 
		{
			$this->CI->db->like($field, $value, 'after');
		}
		elseif( $operator == 'contains' ) 
		{
			$this->CI->db->like($field, $value, 'both');
		}
		elseif( $operator == 'doesnotcontain' ) 
		{
			$this->CI->db->not_like($field, $value);
		}
		elseif( $operator == 'endswith' ) 
		{
			$this->CI->db->like($field, $value,'before');
		}
		return $this;
	}


	public function output($data)
	{
		header("Content-type: application/json");
		echo json_encode($data);
	}


	public function sample_setting()
	{
		$config['debug'] = true;
		$config['column'] = array(
			'stock_master.stock_id',
			'stock_master.category_id',
			'stock_category.description',
			);

		$config['where'] = array(
			array(
				"field" => 'stock_id',
				'value' => 'D-3MM'
				)
			);
		$config['join'] = array(
			array(
				"table" => 'stock_master',
				"field" => 'category_id',
				'join_to' => 'stock_category',
				'field_to' => 'category_id',
				'type' => 'left'
				)
			);
		$config['table_name'] = 'stock_master';
		$this->load->library('grid', $config);
		$data = $this->grid->data();
		$this->grid->output($data);		
	}
}

/* End of file Grid.php */
/* Location: ./application/libraries/Grid.php */

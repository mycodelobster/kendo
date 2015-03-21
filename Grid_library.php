<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Grid {

	public $table = false;
	public $where = false;
	public $join = false;
	public $debug = false;
	public $column = false;
	public $order = false;
	public $CI;

	public function __construct($config=array())
	{
		$this->CI =& get_instance();

		$is_ajax = $this->CI->input->is_ajax_request();
		if($is_ajax==false)
		{
			$this->CI->json->output(false,500);
		}

		if(isset($config['table']))
		{
			$this->table = $config['table'];
		} 

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

		if(isset($config['order']) && is_array($config['order']))
		{
			$this->order = $config['order'];
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
		$this->CI->db->from($this->table);
		$this->CI->db->limit($pageSize);
		$this->CI->db->offset($skip);
		$data = $this->CI->db->get()->result();
		$last_query = $this->CI->db->last_query();


		/* Total Result Query */
		$this->build_filter($filter);
		$this->CI->db->from($this->table);
		$total_data = $this->CI->db->get()->num_rows();

		/* Returning Data */
		if($this->debug)
		{
			$result['query'] = $last_query;
			$result['xxx'] = $this->CI->input->is_ajax_request();
			$result['filter'] = $filter;
			$result['sort'] = $sort;
		}

		$result['data'] = $data;
		$result['total'] = $total_data;
		$result['status'] = 'success';

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


		/* if any sort declare in config params */
		if($this->order)
		{
			foreach ($this->order as $item) 
			{
				if(isset($item['field']) && isset($item['type']))
				{
					$field = $item['field'];
					$type = $item['type'];
					$this->CI->db->order_by($field, $type);	
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


		/* Find AS */
		foreach ($this->column as $string) 
		{
			$string = preg_replace('#'.preg_quote(' ', '#').'{2,}#', ' ', $string);
			$string = trim($string);
			$array = explode(" as ",$string);

			if(count($array)==2)
			{
				if($array[1]==$field)
				{
					$field = $array[0];
				}
			}
			
		}

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
		elseif( $operator == 'gte' ) 
		{
			$field = $field . ' >= ';
			$this->CI->db->where($field, $value);
		}
		elseif( $operator == 'gt' ) 
		{
			$field = $field . ' > ';
			$this->CI->db->where($field, $value);
		}
		elseif( $operator == 'lte' ) 
		{
			$field = $field . ' <= ';
			$this->CI->db->where($field, $value);
		}
		elseif( $operator == 'lt' ) 
		{
			$field = $field . ' > ';
			$this->CI->db->where($field, $value);
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

		$config['order'] = array(
			array(
				"field" => 'stock_id',
				'type' => 'desc',
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
		$config['table'] = 'stock_master';
		$this->load->library('grid', $config);
		$data = $this->grid->data();
		$this->grid->output($data);		
	}
}

/* End of file Grid.php */
/* Location: ./application/libraries/Grid.php */

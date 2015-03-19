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

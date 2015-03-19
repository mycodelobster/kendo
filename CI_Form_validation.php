	$_POST = json_decode(file_get_contents('php://input'),true);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('NAMA', 'NAMA', 'trim|required');
		if($this->form_validation->run()==false) { 
			$response['error'] = validation_errors();
			$this->json->output($response);
		}
		$params['NAMA'] = $this->input->post('NAMA');

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	public function index()
	{
		$this->load->view('template');
	}

	public function read()
	{
		$config['debug'] = true;
		$config['column'] = array('br_name','branch_code','debtor_no');
		$config['table'] = 'cust_branch';
		$this->load->library('grid', $config);
		$data = $this->grid->data();
		$this->json->output($data);		
	}

	public function create()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('br_name', 'br_name', 'trim|required');
		if($this->form_validation->run()==false) 
		{ 
			$response['error'] = validation_errors('<span>','</span>');
			$this->json->output($response);
		}
		else
		{
			$params = array(
				'br_name' => $this->input->post('br_name')
				);
			$data = $this->db->insert('cust_branch', $params);
			$this->json->output($data);
		}

	}

	public function update()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('br_name', 'br_name', 'trim|required');
		if($this->form_validation->run()==false) 
		{ 
			$response['error'] = validation_errors('<span>','</span>');
			$this->json->output($response);
		}
		else
		{
			$branch_code = $this->input->post('branch_code');
			$params = array(
				'branch_code' => $this->input->post('branch_code')
				);
			$this->db->where('branch_code', $branch_code);
			$data = $this->db->update('cust_branch', $params);
			$this->json->output($data);
		}

	}

	public function delete()
	{
		$branch_code = $this->input->post('branch_code');
		$this->db->where('branch_code', $branch_code);
		$query = $this->db->delete('cust_branch');
		$this->json->output($query);
	}

}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */

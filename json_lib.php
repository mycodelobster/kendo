<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Json {

	public function __construct()
	{
		$this->CI =& get_instance();
		
	}

	public function output($data,$status=200)
	{
		
		if($data==false)
		{
			$status = 500;
			header("Content-type: application/json");
			$this->CI->output->set_status_header($status);
		}
		else
		{
			header("Content-type: application/json");
			$this->CI->output->set_status_header($status);
			echo json_encode($data);
		}

		exit;
	}

}

/* End of file Json.php */
/* Location: ./application/libraries/Json.php */

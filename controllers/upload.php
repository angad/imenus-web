<?php

class Upload extends CI_Controller{
	
	public function index()
	{
		
	}
	
	function logo()
	{
		//Loads the logo upload form
		$this->load->helper(array('form', 'url'));
		$this->load->view('logo_upload', array('error' => ' ' ));
	}
	
	function logo_upload()
	{
		$this->load->model('organization');
		
		//Generating a random file name of ~11 characters
		$n = rand(10e16, 10e20);
		$file_name =  base_convert($n, 10, 36);
		
		$config['file_name'] = $file_name;
		$config['upload_path'] = './uploads/logos/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '1024';	//Max 1MB
		$config['max_width']  = '1024';
		$config['max_height']  = '768';
		
		$this->load->library('upload', $config);

		if (!$this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			$this->load->view('logo_upload', $error);
		}
		else
		{
			$path = $config['upload_path'] . $file_name;
			$this->organization->logo_upload($path);
			$this->load->view('login', $data);
		}
	}
	
}
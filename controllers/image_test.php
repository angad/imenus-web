<?php
if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author angad
 */

class Image_test extends CI_Controller{
	
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->load->helper('form');
		$this->load->view('image_test_view', array('error'=>''));
	}
	
	public function upload()
	{
		$n = rand(10e16, 10e20);
		$file_name =  base_convert($n, 10, 36);
		
		//upload configuration
		$config['file_name'] = $file_name;
		$config['upload_path'] = './uploads/raw/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '1024';	//Max 1MB
		$config['max_width']  = '1024';
		$config['max_height']  = '768';
		
		$this->load->library('upload', $config);

		if (!$this->upload->do_upload("raw"))
		{
			$error = array('error' => $this->upload->display_errors());
			$this->load->view('image_test_view', $error);
		}

		$file_data = $this->upload->data();
		$full_path = $file_data['full_path'];
		$file_path = $file_data['file_path'];
		$raw_name = $file_data['raw_name'];
		$file_ext = $file_data['file_ext'];
		
		$this->load->model('image');
		
		$small = $this->image->small($file_path, $raw_name, $file_ext);
		
		echo $small; //the full path to the image
	}
}

?>
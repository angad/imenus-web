<?php

if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author angad
 */


class Image extends CI_Model{
	//Image generator class

	public function __construct()
	{
		parent::__construct();
		$this->load->library('image_lib');
		
	}
	
	public function small($file_path, $raw_name, $file_ext)
	{
		$config['image_library'] = 'gd2';
		$config['source_image']	= $file_path . $raw_name . $file_ext;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = 200;
		$config['height'] = 150;
		$config['new_image'] = $file_path . $raw_name . '_small' . $file_ext;

		$this->image_lib->clear();
		$this->image_lib->initialize($config);

		$this->image_lib->resize();
		return $config['new_image'];
	}
	
	public function medium($file_path, $raw_name, $file_ext)
	{
		$config['image_library'] = 'gd2';
		$config['source_image']	= $file_path . $raw_name . $file_ext;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = 800;
		$config['height'] = 600;
		$config['new_image'] = $file_path . $raw_name . '_medium' . $file_ext;
		
		$this->image_lib->clear();
		$this->image_lib->initialize($config);
		
		$this->image_lib->resize();
		return $config['new_image'];
	}
	
	public function large($file_path, $raw_name, $file_ext)
	{
		$config['image_library'] = 'gd2';
		$config['source_image']	= $file_path . $raw_name . $file_ext;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = 1024;
		$config['height'] = 768;
		$config['new_image'] = $file_path . $raw_name . '_large' . $file_ext;

		$this->image_lib->clear();
		$this->image_lib->initialize($config);
		
		$this->image_lib->resize();
		return $config['new_image'];
	}
}

?>
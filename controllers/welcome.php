<?php

class Welcome extends CI_Controller{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		redirect('/user', 'location', '301');
	}

}

?>

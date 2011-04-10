<?php
if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author angad
 */

class User_model extends CI_Model{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
	}
	
	function getMenuId()
	{
		if($this->session->userdata('logged_in'))
		{
			return $this->session->userdata('menu_id');
		}
		return False;
	}

	function isLoggedIn()
	{		
		if($this->session->userdata('logged_in'))
		{
			return $this->session->userdata('username');
		}
		return False;
	}

	
}

?>
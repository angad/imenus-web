<?php

class Features_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function newFeature($data)
	{
		$this->db->insert('Feature', $data);
	}
}
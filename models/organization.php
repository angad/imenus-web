<?php

class Organization extends CI_Model{
	
	
	function Organization()
	{
			$this->load_db();
	}
	
	function load_db()
	{
		$this->load->database();
	}
	
	function get_all()
	{
		$query = $this->db->query('SELECT * from organization');
		return $query->result_array();
	}
	
	function newOrganization($data)
	{
		$this->db->insert('organization', $data);
	}
}
?>

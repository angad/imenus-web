<?php

class Organization extends Model{
	
	
	function Organization()
	{
		parent::Model();
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
		$query = "INSERT INTO organization (name, username, password, owner_name, contact_number, address, email) VALUES (\'".$this->db->escape($data['name']) . "\', \'" $this->db->escape($data['username']) . "\', \'" . $this->db->escape($data['password']) . "\', \'" . $this->db->escape($data['owner_name']) . "\', \'" . $this->db->escape($data['contact_number']) . "\', \'" . 
$this->db->escape($data['address']) . "\', \'" . 
$this->db->escape($data['email']) . "\', \'" . 
	}
	$this->db->query($sql);
}
?>
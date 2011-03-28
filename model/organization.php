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
	
	function logo_upload($username, $data)
	{
		if($this->session->userdata('username')==$username && )
	}
	
	function checkPassword($username, $password)
	{
		$query = $this->db->query("SELECT password from organization WHERE username=\'" . $username . "\'");
		$pass = $query->result_array();
		if($pass[0] == $password) return 1;
		else return 0;
	}
	
	function usernameExists($username)
	{
		$query = $this->db->query("SELECT username from organization WHERE username=\'" . $username . "\'");
		$user = $query->result_array();
		if($user[0] == $username) return 1;
		else return 0;
	}
	
}
?>

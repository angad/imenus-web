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
		$query = $this->db->query('SELECT * from Organization');
		return $query->result_array();
	}
	
	function newOrganization($data)
	{
		$this->db->insert('Organization', $data);
	}
	
	function checkPassword($username, $password)
	{
		$query = $this->db->query("SELECT Password from Organization WHERE Username=\"" . $username . "\"");
		$result = $query->result_array();
		
		foreach($result as $row)
		{
			if($row['Password'] == $password) return True;
		}
		return False;
		
	}
	
	function username_exists($username)
	{
		$query = $this->db->query('SELECT Username from Organization WHERE Username=\'' . $username . '\'');
		$user = $query->result_array();
		foreach($user as $row)
		{
			echo $row;
			if($row['Username'] == $username) return True;
		}
		return False;
	}
	
}
?>

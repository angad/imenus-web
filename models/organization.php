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
	
	function getMenuId($username)
	{
		$query = $this->db->query('SELECT MenuId from Organization WHERE Username=\'' . $username . '\'');
		$result = $query->result_array();
		
		foreach($result as $row)
		{
			return $row['MenuId'];
		}
		return False;
	}
	
	function menuid()
	{
		if($this->session->userdata('logged_in'))
		{
			return $this->session->userdata('menu_id');
		}
		return False;
	}
	
	function checkPassword($username, $password)
	{
		$query = $this->db->query('SELECT Password from Organization WHERE Username=\'' . $username . '\'');
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
	
	function getMaxMenuId()
	{
		$query = $this->db->query('SELECT MAX(MenuId) AS a from Organization');
		$row = $query->row_array();
		return $row['a'];
	}
	
	function checkInviteKey($invite_key)
	{
		$query = $this->db->query('SELECT MenuId, created FROM InviteKey WHERE InviteKey=?', array($invite_key));
		$row = $query->row_array();
		if($row['MenuId']) 
		{
			echo $row['created'];
			echo time();
			
			return $row['MenuId'];
		}
		else return False;
	}
	
	function setInviteKey($data)
	{
		$this->db->insert('InviteKey', $data);
	}
}
?>

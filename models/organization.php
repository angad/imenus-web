<?php
if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author angad
 */

class Organization extends CI_Model{
	
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	function getOrganization()
	{
		$this->load->library('session');
		if($username = $this->session->userdata('username'))
		{
			$query = $this->db->query('SELECT Id from Organization where username=?', array($username));
			$row = $query->row_array();
			return $row['Id'];
		}
		else return False;
	}
    
    function getOrganizationData($orgID) {
        return $this->db->query('SELECT ID, MenuID, Name, Username, OwnerName, ContactNumber, Address, Email, GSTrate, ServiceCharge FROM Organization WHERE ID = ?', array($orgID))->row_array();
    }
    
    function getOrganizationDataFromUsername($username) {
        return $this->db->query('SELECT ID, MenuID, Name, Username, OwnerName, ContactNumber, Address, Email, GSTrate, ServiceCharge FROM Organization WHERE Username = ?', array($username))->row_array();
    }
	
	function get_all()
	{
		//Get a database dump
		$query = $this->db->query('SELECT * from Organization');
		return $query->result_array();
	}
	
	function newOrganization($data)
	{
		//add a new organization
		$this->db->insert('Organization', $data);
	}
	
	function getMenuId($username)
	{
		//Gets the MenuId associated with the username
		$query = $this->db->query('SELECT MenuId from Organization WHERE Username=?', array($username));
		$result = $query->result_array();
		
		foreach($result as $row)
		{
			return $row['MenuId'];
		}
		return False;
	}
	
	function menuid()
	{
		//checks session for menu_id
		if($this->session->userdata('logged_in'))
		{
			return $this->session->userdata('menu_id');
		}
		return False;
	}
	
	function checkPassword($username, $password)
	{
		//checks password for login
		$query = $this->db->query('SELECT Password from Organization WHERE Username=?', array($username));
		$result = $query->result_array();
				
		foreach($result as $row)
		{
			if($row['Password'] == $password) return True;
		}
		return False;
	}
	
	function username_exists($username)
	{
		//if username exists
		$query = $this->db->query('SELECT Username from Organization WHERE Username=?', array($username));
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
		//get max menu id
		$query = $this->db->query('SELECT MAX(MenuId) AS a from Organization');
		$row = $query->row_array();
		return $row['a'];
	}
	
	function checkInviteKey($invite_key)
	{
		$query = $this->db->query('SELECT MenuId, created FROM InviteKey WHERE InviteKey=?', array($invite_key));
		$row = $query->row_array();
		if(count($row)==0)
			return False;
		
		if($row['MenuId'])
		{
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

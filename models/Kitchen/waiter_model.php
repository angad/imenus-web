<?php

class Waiter_model extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function getWaiter($organization)
	{
		$query = $this->db->query('SELECT * FROM CallWaiter WHERE OrganizationId=?', array($organization));
		return $query->result_array();
	}
	
	public function WaiterAnswered($call_id)
	{
		$this->db->query('UPDATE CallWaiter SET Status = \'0\' WHERE Id=?', array($call_id));
	}
}

?>
<?php 
/**
 * @author angad
 */

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
	
	public function removeCall($call_id)
	{
		$this->db->query('DELETE FROM CallWaiter WHERE Id=?', array($call_id));
	}
}

?>
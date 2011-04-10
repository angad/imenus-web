<?php

class Orders_model extends CI_Model{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	function getOrders($organization)
	{
		$query = $this->db->query('SELECT * FROM `Order` WHERE OrganizationId=?', array($organization));
		return $query->result_array();
	}
	
	function getOrderItem($orderId)
	{
		$query = $this->db->query('SELECT * FROM OrderItem WHERE OrderId=?', array($orderId));
		$order = $query->row_array();
		return $order;
	}
}

?>
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
    
    function getOrderDetails($orderID) {
        return $this->db->query('SELECT ID, OrganizationID, Remarks, TableNumber FROM `Order` WHERE ID = ?', array($orderID))->row_array();
    }
	
	function getOrderItem($orderId)
	{
		$query = $this->db->query('SELECT * FROM OrderItem WHERE OrderId=? AND Started = ?', array($orderId, 0));
		$order = $query->row_array();
		return $order;
	}
	
	function orderStarted($order_item_id)
	{
		$this->db->query('UPDATE OrderItem SET Started = CURRENT_TIMESTAMP WHERE Id=?', array($order_item_id));
	}
	
}

?>
<?php

class Features_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('globals');
		
	}
	
	public function newFeature($data)
	{
		$this->db->insert('Feature', $data);
	}
    
    public function getFeaturesFromMenu($menuID) {
        return $this->db->query('SELECT '.FEATURE_FIELDS.' FROM '.FEATURES_TABLE.' WHERE MenuID = ?', array($menuID))->result_array();
    }

	public function getOrderItemFeatures($order_item_id)
	{
		$result = $this->db->query('SELECT * FROM OrderItemFeatures WHERE OrderItemId = ?', array($order_item_id));
		if($result) return $result->result_array();
		else return False;
	}
	
	public function getFeatureById($feature_id)
	{
		$result = $this->db->query('SELECT * FROM Feature WHERE Id=?', array($feature_id));
		if($result) return $result->row_array();
		else return False;
	}
}
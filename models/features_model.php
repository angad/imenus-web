<?php

class Features_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function newFeature($data)
	{
		$this->db->insert('Feature', $data);
	}
    
    public function getFeaturesFromMenu($menuID) {
        return $this->db->query('SELECT '.FEATURE_FIELDS.' FROM '.FEATURES_TABLE.' WHERE MenuID = ?', array($menuID))->result_array();
    }
}
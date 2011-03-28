<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

define('CATEGORIES_TABLE', 'Category');

class Categories_model extends CI_Model {

	function __construct() {
		parent::__construct();
        $this->load->database();
	}

    function getAll($menuID) {
        return $this->db->query('Select ID, Name FROM Category WHERE menuID = ?', array($menuID))->result_array();
    }
    
    function rename($menuID, $id, $name) {
        $this->db->query('UPDATE Category SET Name = ? WHERE ID = ? AND menuID = ?', array($name, $id, $menuID));
    }
    
    function add($menuID, $name) {
        $this->db->query('UPDATE Category SET Name = ? WHERE ID = ? AND menuID = ?', array($this->db->escape($name), $id, $menuID));
    }
}
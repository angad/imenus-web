<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

define('ITEMS_TABLE', 'Item');
<<<<<<< HEAD
define('ITEMS_TYPE_ITEM', 0);
define('ITEMS_TYPE_MEAL', 1);
=======
>>>>>>> 3906273a185c52301204fa2ae33367037d468a3a

class Items_model extends CI_Model {

	function __construct() {
		parent::__construct();
        $this->load->database();
	}
    
    function getAll($catID, $typeFilter = NULL, $start = NULL, $count = NULL) {
<<<<<<< HEAD
        $this->db->select('ID', 'Name', 'ShortDesc', 'Description', 'Price', 'Type', 'ImageSmall', 'ImageMedium', 'ImageLarge');
        $this->db->where('catID', $catID);                
        if (!is_null($typeFilter))
            $this->db->where('type', $typeFilter);
        return $this->db->get(ITEMS_TABLE, $count, $start);                
    }
    
    function getItems($catID, $start = NULL, $count = NULL) {
        return $this->getAll($catID, ITEMS_TYPE_ITEM, $start, $count);
    }
    
    function addItem($catID, $name, $description, $shortDesc = '', $price = 0, $type = ITEMS_TYPE_ITEM, $imageSmall = '', $imageMed = '', $imageLarge = '') {
        $this->db->query('INSERT INTO '.ITEMS_TABLE.'(catID, Name, Description, ShortDesc, Price, Type, ImageSmall, ImageMed, ImageLarge) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
=======
//        $this->db->select('ID, ParentID, CategoryID, Name, ShortDescription, LongDescription, Price, Type, ImageSmall, ImageMedium, ImageLarge');
//        $this->db->where('CategoryID', $catID);                
//        if (!is_null($typeFilter))
//            $this->db->where('type', $typeFilter);
//        $this->db->get(ITEMS_TABLE, $count, $start);
//        return $this->db->get(ITEMS_TABLE, $count, $start)->result_array();
        $limit_str = '';
        if (is_numeric($start)) {
            $limit_str = sprintf('LIMIT %d, %d', $start, is_numeric($count) ? $count : PHP_INT_MAX);
        } else if (is_numeric($count)) {
            $limit_str = sprintf('LIMIT %d', $count);
        }
        return $this->db->query('SELECT ID, ParentID, CategoryID, Name, ShortDescription, LongDescription, Price, Type, ImageSmall, ImageMedium, ImageLarge FROM '.ITEMS_TABLE.' WHERE CategoryID = ?'.(isset($typeFilter) ? ' AND Type = ?' : '').$limit_str, array($catID, $typeFilter))->result_array();
    }
    
    function getItem($itemID) {
        return $this->db->query('SELECT ID, ParentID, CategoryID, Name, ShortDescription, LongDescription, Price, Type, ImageSmall, ImageMedium, ImageLarge FROM '.ITEMS_TABLE.' WHERE ID = ?', array($itemID))->row_array();
    }
    
    function addItem($catID, $name, $description, $shortDesc = '', $price = 0, $type = ITEMS_TYPE_ITEM, $imageSmall = '', $imageMed = '', $imageLarge = '') {
        $this->db->query('INSERT INTO '.ITEMS_TABLE.'(CategoryID, Name, LongDescription, ShortDescription, Price, Type, ImageSmall, ImageMedium, ImageLarge) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
>>>>>>> 3906273a185c52301204fa2ae33367037d468a3a
                        array($catID, $name, $description, $shortDesc, $price, $type, $imageSmall, $imageMed, $imageLarge));
        return $this->db->insert_id();
    }
    
    function removeItem($itemID) {
        $this->db->query('DELETE FROM '.ITEMS_TABLE.' WHERE ID = ?', array($itemID));
    }

    function updateItem($itemID, $catID = NULL, $name = NULL, $description = NULL, $shortDesc = NULL, $price = NULL, $type = NULL, $imageSmall = NULL, $imageMed = NULL, $imageLarge = NULL) {
<<<<<<< HEAD
        $fields = array('catID', 'name', 'description', 'shortDesc', 'price', 'type', 'imageSmall', 'imageMed', 'imageLarge');
        $args = func_get_args();
        $update = array();
        for ($i = 0; $i < count($fields); ++$i)
            if (!is_null($args[i + 1]))
                $update[$fields[i]] = $args[i + 1];
        $this->db->update(ITEMS_TABLE, $update, array('itemID' => $itemID));
=======
        $fields = array('CategoryID', 'Name', 'LongDescription', 'ShortDescription', 'Price', 'Type', 'ImageSmall', 'ImageMedium', 'ImageLarge');
        $args = func_get_args();
        $update = array();
        for ($i = 0; $i < count($fields); ++$i)
            if (isset($args[$i + 1]))
                $update[$fields[$i]] = $args[$i + 1];
        $this->db->update(ITEMS_TABLE, $update, array('ID' => $itemID));
>>>>>>> 3906273a185c52301204fa2ae33367037d468a3a
    }
}
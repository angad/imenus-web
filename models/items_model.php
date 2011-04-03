<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

class Items_model extends CI_Model {

	function __construct() {
		parent::__construct();
        $this->load->database();
        $this->load->helper('constants');
	}
    
    static function tableName() {
        return ITEMS_TABLE;
    }
    
    function getAll($catID, $typeFilter = NULL, $start = NULL, $count = NULL) {
        $limit_str = '';
        if (is_numeric($start)) {
            $limit_str = sprintf(' LIMIT %d, %d', $start, is_numeric($count) ? $count : PHP_INT_MAX);
        } else if (is_numeric($count)) {
            $limit_str = sprintf(' LIMIT %d', $count);
        }
        $order = ' ORDER BY SortOrder ASC';
        return $this->db->query('SELECT '.ITEM_FIELDS.' FROM '.ITEMS_TABLE.' WHERE CategoryID = ?'.(isset($typeFilter) ? ' AND Type = ?' : '').$order.$limit_str, array($catID, $typeFilter))->result_array();
    }
    
    function getItem($itemID) {
        return $this->db->query('SELECT '.ITEM_FIELDS.' FROM '.ITEMS_TABLE.' WHERE ID = ?', array($itemID))->row_array();
    }
    
    function addItem($catID, $name, $description, $shortDesc = '', $price = 0, $type = ITEMS_TYPE_ITEM, $imageSmall = '', $imageMed = '', $imageLarge = '', $mealItems = NULL) {
        $this->db->query('INSERT INTO '.ITEMS_TABLE.'(CategoryID, Name, LongDescription, ShortDescription, Price, Type, ImageSmall, ImageMedium, ImageLarge, SortOrder) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                        array($catID, $name, $description, $shortDesc, $price, $type, $imageSmall, $imageMed, $imageLarge,
                        ($order = current($this->db->query('SELECT MAX(SortOrder) + 1 FROM '.ITEMS_TABLE.' WHERE CategoryID = ?', array($catID))->row_array())) ? $order : 0));
        $id = $this->db->insert_id();
        $this->setMealItems($id, $mealItems);
        return $id;
    }
    
    function removeItem($itemID) {
        $this->db->query('DELETE I, P1, P2 FROM ('.ITEMS_TABLE.' I LEFT JOIN '.PARENTS_TABLE.' P1 ON I.ID = P1.ParentID) LEFT JOIN '.PARENTS_TABLE.' P2 ON I.ID = P2.ItemID WHERE I.ID = ?', array($itemID));
    }

    function updateItem($itemID, $catID = NULL, $name = NULL, $description = NULL, $shortDesc = NULL, $price = NULL, $type = NULL, $imageSmall = NULL, $imageMed = NULL, $imageLarge = NULL, $mealItems = NULL) {
        $fields = array('CategoryID', 'Name', 'LongDescription', 'ShortDescription', 'Price', 'Type', 'ImageSmall', 'ImageMedium', 'ImageLarge');
        $args = func_get_args();
        $update = array();
        for ($i = 0; $i < count($fields); ++$i)
            if (isset($args[$i + 1]))
                $update[$fields[$i]] = $args[$i + 1];
        $this->db->update(ITEMS_TABLE, $update, array('ID' => $itemID));
        
        $this->setMealItems($itemID, $mealItems);
    }
    
    function getMealItems($itemID, $exclude_details = FALSE) {
        if ($exclude_details)
            $sql = 'SELECT ItemID FROM '.PARENTS_TABLE.' WHERE ParentID = ?';
        else
            $sql = 'SELECT I.'.str_replace(', ', ', I.', ITEM_FIELDS).' FROM '.ITEMS_TABLE.' I INNER JOIN '.PARENTS_TABLE.' P ON P.ItemID = I.ID AND P.ParentID = ?';
        $arr = $this->db->query($sql, array($itemID))->result_array();
        if ($exclude_details && count($arr) > 0)
            return current(call_user_func_array('array_merge_recursive', $arr));
        else
            return $arr;
    }
    
    function setMealItems($itemID, $newItemIDArray) {
        if (!is_array($newItemIDArray))
            return;
        
        $oldarr = $this->getMealItems($itemID, TRUE);
        $to_del = array_diff($oldarr, $newItemIDArray);
        $to_ins = array_diff($newItemIDArray, $oldarr);
        
        if (count($to_del) > 0) {
            array_unshift($to_del, 'DELETE FROM '.PARENTS_TABLE.' WHERE ParentID = %d AND ItemID IN (%d'.str_repeat(', %d', count($to_del) - 1).')');
            $this->db->query(call_user_func_array('sprintf', $to_del));
        }
        if (count($to_ins) > 0) {
            $ins_args = array('INSERT INTO '.PARENTS_TABLE.'(ParentID, ItemID) VALUES (%d, %d)'.str_repeat(', (%d, %d)', count($to_ins) - 1));
        	foreach($to_ins as $item) {
        		$ins_args[] = $itemID;
        		$ins_args[] = $item;
        	}
            $this->db->query(call_user_func_array('sprintf', $ins_args));
        }
    }
}
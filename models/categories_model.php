<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

define('CATEGORIES_TABLE', 'Category');

class Categories_model extends CI_Model {

	function __construct() {
		parent::__construct();
        $this->load->database();
	}

    function getAll($menuID) {
<<<<<<< HEAD
        return $this->db->query('Select ID, Name FROM Category WHERE menuID = ?', array($menuID))->result_array();
    }
    
    function rename($menuID, $id, $name) {
        $this->db->query('UPDATE Category SET Name = ? WHERE ID = ? AND menuID = ?', array($name, $id, $menuID));
    }
    
    function add($menuID, $name) {
        $this->db->query('UPDATE Category SET Name = ? WHERE ID = ? AND menuID = ?', array($this->db->escape($name), $id, $menuID));
=======
        return $this->db->query('SELECT menuID, parentID, ID, Name FROM Category WHERE menuID = ?', array($menuID))->result_array();
    }
    
    function getCat($catID) {
        return $this->db->query('SELECT menuID, parentID, ID, Name FROM Category WHERE catID = ?', array($catID))->row_array();
    }
    
    function getCategoriesInSameMenu($catID) {
        return $this->db->query('SELECT C1.menuID, C1.parentID, C1.ID, C1.Name FROM Category C1 INNER JOIN Category C2 ON C2.menuID = C1.menuID AND C2.ID = ?', array($catID))->result_array();
    }
    
    // RETURNS: Array in which the keys are catIDs, and the values are either the name of the category / item, or an array with 'Name' and 'Data' keys, the 'Data' value being a sub-tree
    // If $include_items = TRUE, all leaves are items; Categories with no items will have an empty array for 'Data'.
    // If $include_items = FALSE, all leaves are categories with no sub-categories.
    function getTreeFromCurrentMenu($catID, $include_items = FALSE) {
        $cats = $this->getCategoriesInSameMenu($catID);
        $nodes = array('Root');
        
        $parentNode = $catNode = NULL;
        
        foreach ($cats as $cat) {
            $catID = $cat['ID'];
            $catName = $cat['Name'];
            $parentID = $cat['parentID'];
            
            if (isset($nodes[$catID]))
                $nodes[$catID]['Name'] = $catName;
            else
                $nodes[$catID] = $catName;
            
            
            $catNode = &$nodes[$catID];
                
            if (!isset($nodes[$parentID]))
                $nodes[$parentID] = array('Name' => '', 'Data' => array());
            
            $parentNode = &$nodes[$parentID];
            if (is_array($parentNode)) 
                $parentNode['Data'][] = &$catNode;
            else
                $parentNode = array('Name' => $parentNode, 'Data' => array(&$catNode));
        }
        
        if (!$include_items)
            return $nodes[0];
            
        $this->load->model('Items_model');
        
        foreach ($nodes as $key => &$node) {
            if (is_array($node))
                continue;
            $menuitems = $this->Items_model->getAll($key);
            foreach ($menuitems as $mi)
                $subtree[$mi['ID']] = $mi;
            $node = array('Name' => $node, 'Data' => $subtree);
        }
        
        return $nodes[0];
    }
    
    function rename($id, $name) {
        $this->db->query('UPDATE Category SET Name = ? WHERE ID = ?', array($name, $id));
    }
    
    function add($menuID, $name) {
        $this->db->query('INSERT INTO Category(Name, menuID) VALUES (?, ?)', array($name, $menuID));
        return $this->db->insert_id();
>>>>>>> 3906273a185c52301204fa2ae33367037d468a3a
    }
}
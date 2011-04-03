<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');
/**
 * iMenus Categories Model
 *
 * @package		iMenus
 * @subpackage	Models
 * @category	Models
 * @author		Patrick
 */
class Categories_model extends CI_Model {

	function __construct() {
		parent::__construct();
        $this->load->database();
        $this->load->helper('constants');
	}
    
    static function tableName() {
        return CATEGORIES_TABLE;
    }

    function getAll($menuID, $include_counts = FALSE, $only_children_of = NULL) {
        $filter = isset($only_children_of) ? ' AND ParentID = ?' : '';
        $order = ' ORDER BY SortOrder ASC';
        if ($include_counts)
            $sql = 'SELECT C.menuID, C.parentID, C.ID, C.Name, (SELECT COUNT(*) FROM '.ITEMS_TABLE.' X WHERE X.CategoryID = C.ID AND X.Type = '.ITEMS_TYPE_ITEM.') AS ItemCount, (SELECT COUNT(*) FROM '.ITEMS_TABLE.' X WHERE X.CategoryID = C.ID AND X.Type = '.ITEMS_TYPE_MEAL.') AS MealCount, (SELECT COUNT(*) FROM '.CATEGORIES_TABLE.' X WHERE X.ParentID = C.ID) AS SubcatCount FROM '.CATEGORIES_TABLE.' C WHERE C.menuID = ?'.$filter.$order;
        else                
            $sql = 'SELECT menuID, parentID, ID, Name FROM '.CATEGORIES_TABLE.' WHERE menuID = ?'.$filter.$order;
        return $this->db->query($sql, array($menuID, $only_children_of))->result_array();
    }
    
    function getAllIDs($menuID) {
        $arr = $this->db->query('SELECT ID FROM '.CATEGORIES_TABLE.' WHERE menuID = ? ORDER BY SortOrder ASC', array($menuID))->result_array();
        if (($size = count($arr)) > 1)
            return current(call_user_func_array('array_merge_recursive', $arr));
        else if ($size == 1)
            return array($arr[0]['ID']);
        else
            return $arr;
    }
    
    function getCat($catID) {
        return $this->db->query('SELECT menuID, parentID, ID, Name FROM '.CATEGORIES_TABLE.' WHERE ID = ?', array($catID))->row_array();
    }
    
    function getCategoriesInSameMenu($catID) {
        return $this->db->query('SELECT C1.menuID, C1.parentID, C1.ID, C1.Name FROM '.CATEGORIES_TABLE.' C1 INNER JOIN '.CATEGORIES_TABLE.' C2 ON C2.menuID = C1.menuID AND C2.ID = ? ORDER BY C1.ParentID ASC, C1.SortOrder ASC', array($catID))->result_array();
    }
    
    // RETURNS: Array in which the keys are catIDs, and the values are either the name of the category / item, or an array with 'Name' and 'Data' keys, the 'Data' value being a sub-tree
    // If $include_items = TRUE, all leaves are items; Categories with no items will have an empty array for 'Data'.
    // If $include_items = FALSE, all leaves are categories with no sub-categories.
    function getTreeFromCurrentMenu($catID, $include_items = FALSE, $only_descendants = FALSE) {
        $cats = $this->getCategoriesInSameMenu($catID);
        $nodes = array('Root');
        
        $parentNode = $catNode = NULL;
        
        $root = $only_descendants ? $catID : 0;
        
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
                $parentNode['Data'][$catID] = &$catNode;
            else
                $parentNode = array('Name' => $parentNode, 'Data' => array($catID => &$catNode));
        }
        
        if (!$include_items)
            return $nodes[$root];
            
        $this->load->model('Items_model');
        
        foreach ($nodes as $key => &$node) {
            if (is_array($node))
                continue;                
            $menuitems = $this->Items_model->getAll($key);
            $subtree = array();
            foreach ($menuitems as $mi)
                $subtree[$mi['ID']] = $mi;
            $node = array('Name' => $node, 'Data' => $subtree);
        }
                                        
        return $nodes[$root];
    }
    
    function update($catID, $name = NULL, $parentID = NULL) {
        $fields = array('Name', 'ParentID');
        $args = func_get_args();
        $update = array();
        for ($i = 0; $i < count($fields); ++$i)
            if (isset($args[$i + 1]))
                $update[$fields[$i]] = $args[$i + 1];
        $this->db->update(CATEGORIES_TABLE, $update, array('ID' => $catID));
    }
    
    function add($menuID, $name, $parentID) {
        $this->db->query('INSERT INTO '.CATEGORIES_TABLE.'(Name, menuID, parentID, SortOrder) VALUES (?, ?, ?, ?)', array($name, $menuID, $parentID,
                            current($this->db->query('SELECT MAX(SortOrder) + 1 FROM '.CATEGORIES_TABLE.' WHERE parentID = ?', array($parentID))->row_array())));
        return $this->db->insert_id();
    }
    
    function remove($catID) {
        $subcatIDs = array_merge(array((int)$catID), $this->_flatten($this->getTreeFromCurrentMenu($catID, FALSE, TRUE)));
        
        $this->db->query('DELETE C, I, P1 FROM '.CATEGORIES_TABLE.' C LEFT JOIN '.ITEMS_TABLE.' I ON I.CategoryID = C.ID LEFT JOIN '.PARENTS_TABLE.' P ON P.ParentID = I.ID WHERE C.ID IN (?'.str_repeat(',?', count($subcatIDs) - 1).')', $subcatIDs);
    }
    
    private static function _flatten($tree) {
        if (!is_array($tree))
            return array();
        
        return array_merge(array_keys($tree['Data']), call_user_func_array('array_merge', array_map('Categories_model::_flatten', $tree['Data'])));
    }
}
<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');
/**
 * iMenus Categories Model
 *
 * @package		iMenus
 * @category	Models
 * @author		Patrick
 */

class Categories_model extends CI_Model {

	function __construct() {
		parent::__construct();
        $this->load->database();
        $this->load->helper('globals');
	}
    
    static function tableName() {
        return CATEGORIES_TABLE;
    }


    /**
     * Get All Categories
     *
     * Retrieves all Categories for a given Menu. Item, Set Meal and Subcategory
     * Counts can be included, and the retrieved Categories can be restricted only
     * to the direct children of a specified Category.
     *
     * @access	public
     * @param	int      menuID
     * @param   boolean  include_counts
     * @param   boolean  Parent Category (specify if only its children are required)
     * @return	array    
     */
    function getAll($menuID, $include_counts = FALSE, $only_children_of = NULL) {
        $filter = isset($only_children_of) ? ' AND ParentID = ?' : '';
        $order = ' ORDER BY SortOrder ASC';
        if ($include_counts)
            $sql = 'SELECT C.menuID, C.parentID, C.ID, C.Name, C.Image, C.TSV1, (SELECT COUNT(*) FROM '.ITEMS_TABLE.' X WHERE X.CategoryID = C.ID AND X.Type = '.ITEMS_TYPE_ITEM.') AS ItemCount, (SELECT COUNT(*) FROM '.ITEMS_TABLE.' X WHERE X.CategoryID = C.ID AND X.Type = '.ITEMS_TYPE_MEAL.') AS MealCount, (SELECT COUNT(*) FROM '.CATEGORIES_TABLE.' X WHERE X.ParentID = C.ID) AS SubcatCount FROM '.CATEGORIES_TABLE.' C WHERE C.menuID = ?'.$filter.$order;
        else                
            $sql = 'SELECT menuID, parentID, ID, Name, Image, TSV1 FROM '.CATEGORIES_TABLE.' WHERE menuID = ?'.$filter.$order;
        return $this->db->query($sql, array($menuID, $only_children_of))->result_array();
    }
    
    /**
     * Get All CategoryIDs
     *
     * Retrieves all CategoryIDs of a Menu's Categories 
     *
     * @access	public
     * @param	int   menuID
     * @return	array
     */
    function getAllIDs($menuID) {
        $arr = $this->db->query('SELECT ID FROM '.CATEGORIES_TABLE.' WHERE menuID = ? ORDER BY SortOrder ASC', array($menuID))->result_array();
        if (($size = count($arr)) > 1)
            return current(call_user_func_array('array_merge_recursive', $arr));
        else if ($size == 1)
            return array($arr[0]['ID']);
        else
            return $arr;
    }
    
    /**
     * Get Category Details
     *
     * Retrieves details of a single Category 
     *
     * @access	public
     * @param	int      catID
     * @param   boolean  include_counts
     * @return	array
     */
    function getCat($catID, $include_counts = FALSE) {
        if ($include_counts)
            $sql = 'SELECT C.menuID, C.parentID, C.ID, C.Name, C.Image, C.TSV1, (SELECT COUNT(*) FROM '.ITEMS_TABLE.' X WHERE X.CategoryID = C.ID AND X.Type = '.ITEMS_TYPE_ITEM.') AS ItemCount, (SELECT COUNT(*) FROM '.ITEMS_TABLE.' X WHERE X.CategoryID = C.ID AND X.Type = '.ITEMS_TYPE_MEAL.') AS MealCount, (SELECT COUNT(*) FROM '.CATEGORIES_TABLE.' X WHERE X.ParentID = C.ID) AS SubcatCount FROM '.CATEGORIES_TABLE.' C WHERE C.ID = ?';
        else                
            $sql = 'SELECT menuID, parentID, ID, Name, Image, TSV1 FROM '.CATEGORIES_TABLE.' WHERE ID = ?';
        return $this->db->query($sql, array($catID))->row_array();
    }
    
    /**
     * Get CategoryIDs from the same Menu 
     *
     * Retrieves all CategoryIDs in the same menu as the given ID 
     *
     * @access	public
     * @param	int
     * @param   int
     * @param   boolean
     * @return	array
     */
    function getCategoriesInSameMenu($catID, $excludeCat = NULL, $excludeCatsWithItems = FALSE) {
        $filter = is_numeric($excludeCat) ? ' AND C1.ID <> '.$excludeCat : '';
        if ($excludeCatsWithItems)
            $sql = 'SELECT C1.menuID, C1.parentID, C1.ID, C1.Name, C1.Image, C1.TSV1 FROM ('.CATEGORIES_TABLE.' C1 INNER JOIN '.CATEGORIES_TABLE.' C2 ON C2.menuID = C1.menuID AND C2.ID = ?) LEFT JOIN '.ITEMS_TABLE.' I ON C1.ID = I.CategoryID WHERE ISNULL(I.ID)'.$filter.' ORDER BY C1.ParentID ASC, C1.SortOrder ASC';
        else
            $sql = 'SELECT C1.menuID, C1.parentID, C1.ID, C1.Name, C1.Image, C1.TSV1 FROM '.CATEGORIES_TABLE.' C1 INNER JOIN '.CATEGORIES_TABLE.' C2 ON C2.menuID = C1.menuID AND C2.ID = ?'.$filter.' ORDER BY C1.ParentID ASC, C1.SortOrder ASC';
        return $this->db->query($sql, array($catID))->result_array();
    }
    
    /**
     * Get CategoryIDs from specified Menu
     *
     * Retrieves all CategoryIDs in the specified menu 
     *
     * @access	public
     * @param	int
     * @param   int
     * @param   boolean
     * @return	array
     */
    function getCategoriesInMenu($menuID, $excludeCat = NULL, $excludeCatsWithItems = FALSE) {
        $filter = is_numeric($excludeCat) ? ' AND C.ID <> '.$excludeCat : '';
        if ($excludeCatsWithItems)
            $sql = 'SELECT C.menuID, C.parentID, C.ID, C.Name, C.Image, C.TSV1 FROM '.CATEGORIES_TABLE.' C LEFT JOIN '.ITEMS_TABLE.' I ON C.ID = I.CategoryID WHERE ISNULL(I.ID) AND C.menuID = ?'.$filter.' ORDER BY ParentID ASC, C.SortOrder ASC';
        else
            $sql = 'SELECT menuID, parentID, ID, Name, Image, TSV1 FROM '.CATEGORIES_TABLE.' C WHERE menuID = ?'.$filter.' ORDER BY ParentID ASC, SortOrder ASC';
        return $this->db->query($sql, array($menuID))->result_array();
    }
    
    /**
     * Get Tree from the specified Menu
     *
     * RETURNS: Array in which the keys are catIDs, and the values are either the name of the category / item,
     * or an array with 'Name' and 'Data' keys, the 'Data' value being a sub-tree.
     * If $include_items = TRUE, all leaves are items; Categories with no items will have an empty array for 'Data'.
     * If $include_items = FALSE, all leaves are categories with no sub-categories.
     *
     * @access	public
     * @param	int       menuID
     * @param   boolean   include_items
     * @param   int       excludeCat
     * @param   boolean   excludeCatsWithItems
     * @return	array
     */
    function getTreeFromMenu($menuID, $include_items = FALSE, $excludeCat = NULL, $excludeCatsWithItems = FALSE) {
        return $this->_getTree($this->getCategoriesInMenu($menuID, $excludeCat, $excludeCatsWithItems), $include_items);
    }
    
    /**
     * Get Tree from the specified Category's Menu
     *
     * RETURNS: Array in which the keys are catIDs, and the values are either the name of the category / item,
     * or an array with 'Name' and 'Data' keys, the 'Data' value being a sub-tree.
     * If $include_items = TRUE, all leaves are items; Categories with no items will have an empty array for 'Data'.
     * If $include_items = FALSE, all leaves are categories with no sub-categories.
     *
     * @access	public
     * @param	int       catID
     * @param   boolean   include_items
     * @param   boolean   TRUE if only specified Category's subtree is required
     * @param   int       excludeCat
     * @param   boolean   excludeCatsWithItems
     * @return	array
     */
    function getTreeFromCurrentMenu($catID, $include_items = FALSE, $only_descendants = FALSE, $excludeCat = NULL, $excludeCatsWithItems = FALSE) {
        return $this->_getTree($this->getCategoriesInSameMenu($catID, $excludeCat, $excludeCatsWithItems), $include_items, $only_descendants ? $catID : 0);
    }
    
    /**
     * Get Tree from provided Categories
     *
     * RETURNS: Array in which the keys are catIDs, and the values are either the name of the category / item,
     * or an array with 'Name' and 'Data' keys, the 'Data' value being a sub-tree.
     * If $include_items = TRUE, all leaves are items; Categories with no items will have an empty array for 'Data'.
     * If $include_items = FALSE, all leaves are categories with no sub-categories.
     *
     * @access	private
     * @param	array     Categories (as returned by getCategoriesInSameMenu)
     * @param   boolean   include_items
     * @param   int       The root of the required subtree. Specify 0 for the whole tree
     * @return	array
     */
    private function _getTree($cats, $include_items = FALSE, $root = 0) {
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
    
    /**
     * Update Category
     *
     * Updates the Database, updating fields if they are specified 
     *
     * @access	public
     * @param	int
     * @param   string
     * @param   string
     */
    function update($catID, $name = NULL, $parentID = NULL, $TSV1 = NULL) {
        $fields = array('Name', 'ParentID', 'TSV1');
        $args = func_get_args();
        $update = array();
        for ($i = 0; $i < count($fields); ++$i)
            if (isset($args[$i + 1]))
                $update[$fields[$i]] = $args[$i + 1];
        $this->db->update(CATEGORIES_TABLE, $update, array('ID' => $catID));
    }
    
    /**
     * Adds a Category
     *
     * Updates the Database, adding a Category 
     *
     * @access	public
     * @param	int   menuID
     * @param   string
     * @param   int   parentID; Specify 0 for root
     * @return	int
     */
    function add($menuID, $name, $parentID, $TSV1 = NULL) {
        if (!isset($TSV1)) {
            $this->load->model('TSV_model');
            $TVDetails = $this->TSV_model->getThemeValueDetails($menuID, TSV_TYPE_CATEGORY);
            if ($TVDetails)
                $TSV1 = $TVDetails['Default'];
            else
                $TSV1 = 0;
        }
        $this->db->query('INSERT INTO '.CATEGORIES_TABLE.'(Name, menuID, parentID, TSV1, SortOrder) VALUES (?, ?, ?, ?, ?)', array($name, $menuID, $parentID, $TSV1,
                            ($order = current($this->db->query('SELECT MAX(SortOrder) + 1 FROM '.CATEGORIES_TABLE.' WHERE parentID = ?', array($parentID))->row_array())) ? $order : 0));
        return $this->db->insert_id();
    }
    
    /**
     * Removes a Category
     *
     * Removes a Category, its Sub-Categories, its Items and Set Meals 
     *
     * @access	public
     * @param	int
     */
    function remove($catID) {
        $subcatIDs = array_merge(array((int)$catID), $this->_flatten($this->getTreeFromCurrentMenu($catID, FALSE, TRUE)));
        
        $this->db->query('DELETE C, I, P FROM '.CATEGORIES_TABLE.' C LEFT JOIN '.ITEMS_TABLE.' I ON I.CategoryID = C.ID LEFT JOIN '.PARENTS_TABLE.' P ON P.ParentID = I.ID WHERE C.ID IN (?'.str_repeat(',?', count($subcatIDs) - 1).')', $subcatIDs);
    }
    
    /**
     * Updates Image Path
     *
     * Updates Database, updates an Category's Image Path and Image Last Modified 
     *
     * @access	public
     * @param	int     Category ID
     * @param   string  Path
     */
    function updateCategoryImage($catID, $path) {
        $root = substr(BASEPATH, 0, strrpos(rtrim(BASEPATH, '/'), '/'));
        if (substr($path, 0, strlen($root)) != $root)
            return;
        if (($current = current($this->db->query('SELECT Image FROM '.CATEGORIES_TABLE .' WHERE ID = ?', array($catID))->row_array())) && $current != '')
            @unlink ($root.'/'.$current);
        
        $this->db->query('UPDATE '.CATEGORIES_TABLE.' SET Image = ? WHERE ID = ?', array(ltrim(str_replace($root, '', $path, $count = 1), '/'), $catID));
    }
    
    /**
     * Flattens a Category Tree
     *
     * Takes in a tree from getTreeFromCurrentMenu, and returns an array of CategoryIDs
     *
     * @access	private
     * @static
     * @param	array
     * @return	array
     */
    private static function _flatten($tree) {
        if (!is_array($tree))
            return array();
        
        return array_merge(array_keys($tree['Data']), call_user_func_array('array_merge', array_map('Categories_model::_flatten', $tree['Data'])));
    }
}
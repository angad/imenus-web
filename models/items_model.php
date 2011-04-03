<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');
/**
 * iMenus Items Model
 *
 * @package		iMenus
 * @category	Models
 * @author		Patrick
 */

class Items_model extends CI_Model {

	function __construct() {
		parent::__construct();
        $this->load->database();
        $this->load->helper('constants');
	}
    
    static function tableName() {
        return ITEMS_TABLE;
    }
    
    /**
     * Get All Items
     *
     * Retrieves all Items for a given Category. Items can be filtered by a given Type,
     * and limited via Start and Count
     *
     * @access	public
     * @param	int
     * @param   int
     * @param   int
     * @param   int
     * @return	array    
     */
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
    
    /**
     * Get Item Details
     *
     * @access	public
     * @param	int
     * @return	array
     */
    function getItem($itemID) {
        return $this->db->query('SELECT '.ITEM_FIELDS.' FROM '.ITEMS_TABLE.' WHERE ID = ?', array($itemID))->row_array();
    }
    
    /**
     * Adds a Category
     *
     * Updates the Database, adding an Item 
     *
     * @access	public
     * @param	int
     * @param   string  Name
     * @param   string  Long Description
     * @param   string  Short Description
     * @param   double  Price
     * @param   int     Type (ITEMS_TYPE_ITEM or ITEMS_TYPE_MEAL)
     * @param   string  Small Image Path
     * @param   string  Medium Image Path
     * @param   string  Large Image Path
     * @param   array   Meal Items
     * @return	int
     */
    function addItem($catID, $name, $description, $shortDesc = '', $price = 0, $type = ITEMS_TYPE_ITEM, $imageSmall = '', $imageMed = '', $imageLarge = '', $mealItems = NULL) {
        $this->db->query('INSERT INTO '.ITEMS_TABLE.'(CategoryID, Name, LongDescription, ShortDescription, Price, Type, ImageSmall, ImageMedium, ImageLarge, SortOrder) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                        array($catID, $name, $description, $shortDesc, $price, $type, $imageSmall, $imageMed, $imageLarge,
                        ($order = current($this->db->query('SELECT MAX(SortOrder) + 1 FROM '.ITEMS_TABLE.' WHERE CategoryID = ?', array($catID))->row_array())) ? $order : 0));
        $id = $this->db->insert_id();
        $this->setMealItems($id, $mealItems);
        return $id;
    }
    
    /**
     * Removes an Item
     *
     * Removes an Item and its Meal Items (for Set Meals) 
     *
     * @access	public
     * @param	int
     */
    function removeItem($itemID) {
        $this->db->query('DELETE I, P1, P2 FROM ('.ITEMS_TABLE.' I LEFT JOIN '.PARENTS_TABLE.' P1 ON I.ID = P1.ParentID) LEFT JOIN '.PARENTS_TABLE.' P2 ON I.ID = P2.ItemID WHERE I.ID = ?', array($itemID));
    }

    /**
     * Update Category
     *
     * Updates the Database, updating fields if they are specified 
     *
     * @access	public
     * @param	int     Item ID
     * @param   int     Category ID
     * @param   string  Name
     * @param   string  Long Description
     * @param   string  Short Description
     * @param   double  Price
     * @param   int     Type (ITEMS_TYPE_ITEM or ITEMS_TYPE_MEAL)
     * @param   string  Small Image Path
     * @param   string  Medium Image Path
     * @param   string  Large Image Path
     * @param   array   Meal Items
     */
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
    
    /**
     * Get All Meal Items
     *
     * Retrieves all Meal Items of a Set Meal. If exclude_details is TRUE, only IDs are returned. 
     *
     * @access	public
     * @param	int
     * @param   boolean
     * @return	array
     */
    function getMealItems($itemID, $exclude_details = FALSE) {
        if ($exclude_details)
            $sql = 'SELECT ItemID FROM '.PARENTS_TABLE.' WHERE ParentID = ?';
        else
            $sql = 'SELECT I.'.str_replace(', ', ', I.', ITEM_FIELDS).' FROM '.ITEMS_TABLE.' I INNER JOIN '.PARENTS_TABLE.' P ON P.ItemID = I.ID AND P.ParentID = ?';
        $arr = $this->db->query($sql, array($itemID))->result_array();
        if ($exclude_details && ($size = count($arr)) > 1)
            return current(call_user_func_array('array_merge_recursive', $arr));
        else if ($size == 1)
            return array($arr[0]['ItemID']);
        else
            return $arr;
    }
    
    /**
     * Set Meal Items
     *
     * Updates Database, updates a Set Meal's Meal Items 
     *
     * @access	public
     * @param	int
     * @param   array
     */
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
    
    /**
     * Updates Image Path
     *
     * Updates Database, updates an Item's Image Path and Image Last Modified 
     *
     * @access	public
     * @param	int     Item ID
     * @param   string  Image Type, one of ITEM_IMAGE_SMALL, ITEM_IMAGE_MEDIUM or ITEM_IMAGE_LARGE
     * @param   string  Path
     */
    function updateItemImage($itemID, $imageType, $path) {
        if (!in_array($imageType, array(ITEM_IMAGE_SMALL, ITEM_IMAGE_MEDIUM, ITEM_IMAGE_LARGE)))
            return;
        $root = substr(BASEPATH, 0, strrpos(rtrim(BASEPATH, '/'), '/'));
        if (substr($path, 0, strlen($root)) != $root)
            return;
        if (($current = current($this->db->query('SELECT '.$imageType.' FROM '.ITEMS_TABLE .' WHERE ID = ?', array($itemID))->row_array())) && $current != '')
            @unlink ($root.'/'.$current);
        
        $this->db->query('UPDATE '.ITEMS_TABLE.' SET '.$imageType.' = ?, ImageLastModified = CURRENT_TIMESTAMP WHERE ID = ?', array(ltrim(str_replace($root, '', $path, $count = 1), '/'), $itemID));
    }
}
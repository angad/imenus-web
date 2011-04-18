<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');
/**
 * iMenus Theme-Specific Variable Model
 *
 * @package		iMenus
 * @category	Models
 * @author		Patrick
 */

class TSV_model extends CI_Model {

	function __construct() {
		parent::__construct();
        $this->load->database();
        $this->load->helper('globals');
        $this->load->model('User_model');
	}
    
    /**
     * Get Theme Value Options
     *
     * Retrieves all Theme Value Options for a given Menu and Value Type.
     * Returns an associative array, or FALSE if no Options defined
     *
     * @access	public
     * @param	int   Menu ID
     * @param   int   Value Type. Either TSV_TYPE_CATEGORY or TSV_TYPE_ITEM
     * @return	mixed
     */
    function getThemeValueOptions($menuID, $TSVtype) {
        $res = array();
        $menu = $this->db->query('SELECT Theme FROM '.MENU_TABLE.' WHERE ID = ?', array($menuID))->row_array();
        if (count($menu) == 0)
            return FALSE;
        $theme = $menu['Theme'];
        $opts = $this->db->query('SELECT OptionValue, OptionLabel FROM '.THEMEVALUEOPTIONS_TABLE.' WHERE Theme = ? AND Type = ?', array($theme, $TSVtype))->result_array();
        if (count($opts)) {
            foreach ($opts as $opt)
                $res[$opt['OptionValue']] = $opt['OptionLabel'];
        } else {
            $det = $this->db->query('SELECT `Min`, `Max` FROM '.THEMEVALUES_TABLE.' WHERE Theme = ? AND Type = ?', array($theme, $TSVtype))->row_array();
            if (count($det)) {
                $range = range($det['Min'], $det['Max']);
                return array_combine($range, $range);
            } else
                return FALSE;
        }
    }
    
    /**
     * Get Theme Value Details
     *
     * Retrieves details of the Theme Value for a given Menu and Value Type.
     * Returns an associative array, or FALSE if no Theme Value defined
     *
     * @access	public
     * @param	int   Menu ID
     * @param   int   Value Type. Either TSV_TYPE_CATEGORY or TSV_TYPE_ITEM
     * @return	mixed
     */
    function getThemeValueDetails($menuID, $TSVtype) {
        $res = array();
        $menu = $this->db->query('SELECT Theme FROM '.MENU_TABLE.' WHERE ID = ?', array($menuID))->row_array();
        if (count($menu) == 0)
            return FALSE;
        $theme = $menu['Theme'];
        $det = $this->db->query('SELECT `Default`, Label FROM '.THEMEVALUES_TABLE.' WHERE Theme = ? AND Type = ?', array($theme, $TSVtype))->row_array();
        if (count($det))
            return $det;
        else
            return FALSE;
    }
}
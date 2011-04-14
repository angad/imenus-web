<?php

if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author angad
 */

class Menu_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();	
	}
	
	function getTheme($menuid)
	{
		//Gets the theme associated with the Menu_id
		$query = $this->db->query('SELECT Theme FROM Menu WHERE Id=?', array($menuid));
		$row = $query->row_array();
		return $row['Theme'];		
	}
	
	function setTheme($menuid, $theme)
	{
		//Sets the theme for the menu
		$this->db->query('UPDATE Menu SET Theme=? WHERE Id= ?', array($theme, $menuid));
        $TSVdefaults = $this->db->query('SELECT Type, `Default` FROM ThemeValues WHERE Theme = ?', array($theme))->result_array();
        foreach ($TSVdefaults as $def)
            $this->db->query('UPDATE '.($def['Type'] ? 'Item' : 'Category').' SET TSV1 = ?', $def['Default']);
	}
	
	function newMenu($data)
	{
		$this->db->insert('Menu', $data);
	}
}

?>
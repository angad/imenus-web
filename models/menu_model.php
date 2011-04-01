<?php

class Menu_model extends CI_Model{
	
	public function Menu_model(){
		
		$this->load->database();	
	}
	
	function getTheme($menuid)
	{
		$query = $this->db->query('SELECT Theme FROM Menu WHERE Id=?', array($menuid));
		$row = $query->row_array();
		return $row['Theme'];
	}
	
	function setTheme($menuid, $theme)
	{
		$this->db->query('UPDATE Menu SET Theme=? WHERE Id= ?', array($theme, $menuid));
	}
}
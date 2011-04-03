<?php
if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author angad
 */

class Designer extends CI_Controller
{

	public function __construct()
	{
		//Initializes menu and user model
		//Loads form and uri helper
		parent::__construct();
		$this->load->model('menu_model');
		$this->load->model('user_model');
		$this->load->helper('form');
		$this->load->helper('url');
	}

	public function index()
	{
		//Checks if user is logged in
		//Loads the menu_view with the sidebar
		
		//get the menu id from the session
		$menu_id = $this->user_model->getMenuId();
		
		//if session does not exist, load the login form
		if(!$menu_id) 
		{
			$this->load->view('login_form');
			return;
		}
		
		$this->load->view('sidebar', array('title'=>'Menu'));
		$this->load->view('menu_view');
		$this->load->view('footer');
	}
	
	function theme($select)
	{
		//http://imenus.tk/index.php/designer/theme
		$menu_id = $this->user_model->getMenuId();

		//if session does not exist, load the login form
		if(!$menu_id)
		{
			$this->load->view('login_form');
			return;
		}
		$this->menu_model->setTheme($menu_id, $select);
		redirect('designer/selectTheme', 'refresh');
	}
	
	function selectTheme()
	{
		//http://imenus.tk/index.php/designer/selectTheme
		
		$menu_id = $this->user_model->getMenuId();
		
		//if session does not exist, load the login form
		if(!$menu_id) 
		{
			$this->load->view('login_form');
			return;
		}

		$this->load->view('sidebar', array('title'=>'Select Theme'));

		//get the current theme for the menu_id
		$current_theme = $this->menu_model->getTheme($menu_id);
		$data = array('current'=>$current_theme);
		$this->load->view('themes_view', $data);
		$this->load->view('footer');
	}
}	
?>
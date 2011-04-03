<?php

class Designer extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('menu_model');
		$this->load->model('user_model');
		$this->load->helper('form');
		$this->load->helper('url');
	}

	public function index()
	{
		$username = $this->user_model->isLoggedIn();
		
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
	}
	
	function theme($select)
	{
		$menu_id = $this->user_model->getMenuId();

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
	}
}	
?>
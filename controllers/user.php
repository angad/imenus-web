<?php

if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author angad
 */

class User extends CI_Controller{
	
	
	public function __construct()
	{	
		//Initializes menu and user model
		//Loads form and uri helper
		parent::__construct();
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('user_model');
		$this->load->library('session');
	}
	
	public function index()
	{
		//Checks if logged in
		//Loads the designer
		$username=$this->user_model->isLoggedIn();
		if($username)
		{
			redirect('/designer/', 'refresh');
		}
		else $this->load->view('login_form');
	}
	
	function login()
	{	
		//http://imenus.tk/index.php/user/login
		//Login post URL
		
		$password = $this->input->post('password');
		$username = $this->input->post('username');
		$this->load->model('organization');
		if($this->organization->checkPassword($username, md5($password)))
		{
			$menu_id = $this->organization->getMenuId($username);
						
			$newdata = array(
		                    'username'  => $username,
							'logged_in' => TRUE,
							'menu_id'=> $menu_id
							);
		
			$this->session->set_userdata($newdata);
			redirect('/designer/', 'refresh');
		}
		else 
		{
			echo "Invalid Username/Password";
			$this->load->view('login_form');
		}
	}
	
	function logout()
	{
		//http://imenus.tk/index.php/user/logout
		//Destroys the session and loads the login form
		$this->session->sess_destroy();
		$this->load->view('login_form');
	}
	
	function inviteKey()
	{		
		//http://imenus.tk/index.php/user/inviteKey
		//Generates an invite key for the menu_id
		
		$menu_id = $this->user_model->getMenuId();
		
		if(!$menu_id)
		{
			$this->load->view('login_form');
		}
		else{
			//Generating a random key of ~11 characters
			$n = rand(10e16, 10e20);
			$key =  base_convert($n, 10, 36);

			$this->load->model('organization');

			$data = array(
				'InviteKey'=>$key,
				'MenuId'=>$menu_id
			);

			$this->organization->setInviteKey($data);

			$this->load->view('sidebar.php', array('title'=>"InviteKey"));
			$this->load->view('invite_key.php', array('key'=>$key));
			$this->load->view('footer');	
		}
	}
}

?>
<?php

class User extends CI_Controller{
	
	public function index()
	{
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('user_model');
		$username=$this->user_model->isLoggedIn();
		if($username)
		{
			redirect('/designer/', 'refresh');
		}
		else $this->load->view('login_form');
	}
	
	function login()
	{	
		$this->load->library('session');
		$this->load->helper('url');
		
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

			//put redirect call from here to the designer
			//redirect('designer', 'refresh');
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
		$this->session->sess_destroy();
		$this->load->view('login_form');
	}

}
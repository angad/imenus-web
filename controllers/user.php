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
	
	function inviteKey()
	{
		$this->load->model('user_model');
		
		$menu_id = $this->user_model->getMenuId();
			
		//Generating a random key of ~11 characters
		$n = rand(10e16, 10e20);
		$key =  base_convert($n, 10, 36);
		
		$this->load->model('organization');
		
		$data = array(
			'key'=>$key,
			'MenuId'=>$menu_id
		);
		
		$this->organization->setInviteKey($data);
		
		$this->load->view('invite_key.php', array('key'=>$key));
	}

}
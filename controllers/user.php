<?php

class User extends CI_Controller{
	
	public function index()
	{
		$this->load->helper('form');
		$this->load->view('login_form');
	}
	
	function login()
	{	
		$this->load->library('session');
		$password = $this->input->post('password');
		$username = $this->input->post('username');
		$this->load->model('organization');
		if($this->organization->checkPassword($username, md5($password)))
		{
			$newdata = array(
		                    'username'  => $username,
							'logged_in' => TRUE
							);
		
			$this->session->set_userdata($array);

			//put redirect call from here to the designer
			//redirect('designer');
			redirect('designer');
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
	
	function isLoggedIn()
	{
		if($this->session->userdata('logged_in'))
		{
			return $this->session->userdata('username');
		}
		return False;
	}
}
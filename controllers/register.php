<?php

if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author angad
 */

class Register extends CI_Controller{

		public function __construct()
		{
			parent::__construct();
			$this->load->helper('form');
			$this->load->helper('url');
		}

        public function index()
        {
			$data['error']='';
            $this->load->view('register_form', $data);
        }

        function newOrganization()
        {
	        $this->load->model('organization');
    
			$this->load->library('form_validation');
			
			//Input validation rules
            $this->form_validation->set_rules('username', 'Username', 'required|min_length[5]|alpha_numeric');
			$this->form_validation->set_rules('password', 'Password', 'required|matches[repeat]|min_length[6]|md5');
			$this->form_validation->set_rules('repeat', 'Password Confirmation', 'required');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('owner_name', 'Owner Name', 'required');
			$this->form_validation->set_rules('address', 'Address', 'required');

			//Generating a random file name of ~11 characters
			$n = rand(10e16, 10e20);
			$file_name =  base_convert($n, 10, 36);

			//Logo upload configuration
			$config['file_name'] = $file_name;
			$config['upload_path'] = './uploads/logos/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']	= '1024';	//Max 1MB
			$config['max_width']  = '300';
			$config['max_height']  = '300';

			$this->load->library('upload', $config);
		
			
			//Getting Input post data
			$data['Name'] = $this->input->post('name');
            $data['Username'] = $this->input->post('username');
			$data['OwnerName'] = $this->input->post('owner_name');
			$data['ContactNumber'] = $this->input->post('contact_number');
			$data['Address'] = $this->input->post('address');
			$data['Email'] = $this->input->post('email');
			$data['Password'] = md5($this->input->post('password'));
			
			
			//Validate the invite key
			if(!$this->input->post('invite_key') == '')
			{
				$invite_key = $this->input->post('invite_key');
				if($menu_id = $this->organization->checkInviteKey($invite_key))
				{
					//if the invite key is correct, use an existing menu
					$data['MenuId'] = $menu_id;
				}
				else
				{
					//bad invite key
					$error = array('error' => 'Invite key bad or already expired.');
					$this->load->view('register_form', $error);
					return;
				}
			}
			else
			{
				//if its empty, create a new menu
				$data['MenuId'] = $this->organization->getMaxMenuId() + 1;
				$menudata['Theme'] = 1;
				$this->load->model('menu_model');
				$this->menu_model->newMenu($menudata);
			}
			
			//check if Username already exists
			if ($this->organization->username_exists($data['Username']))
			{
				$error = array('error' => 'Username Already exists. Please pick another one.');
				$this->load->view('register_form', $error);
				return;
			}

			if ($this->form_validation->run() == FALSE || !$this->upload->do_upload("logo"))
			{
				//Reload the form if there is any error with the inputs
				$error = array('error' => $this->upload->display_errors());
				$this->load->view('register_form', $error);
			}
			else
			{	
				$file_data = $this->upload->data();
				$full_path = $file_data['full_path'];
		   		$file_path = $file_data['file_path'];
		   		$orig_name = $file_data['orig_name'];
				$path = substr($file_path . $orig_name, 9);
				$data['Logo'] = $path;
							
				//Call the model
				$this->organization->newOrganization($data);
				//Load the login screen
				redirect('user', 'refresh');	
			}
        }
}
?>
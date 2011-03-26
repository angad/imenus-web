<?php
class Register extends CI_Controller{

        public function index()
        {
                $this->load->helper('form');
                $this->load->view('register_form');
                $this->load->model('Organization');
        }

        function newOrganization()
        {
			$this->load->library('form_validation');
			
			
			//Input validation rules
            $this->form_validation->set_rules('username', 'Username', 'callback_username_check');
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('repeat', 'Password Confirmation', 'required');
			$this->form_validation->set_rules('email', 'Email', 'required');

			if ($this->form_validation->run() == FALSE)
			{
				//Reload the form if there is any error with the inputs
				$this->load->view('register_form');
			}
			else
			{
				$data['name'] = $this->input->post('name');
                $data['username'] = $this->input->post('username');
				$data['owner_name'] = $this->input->post('owner_name');
				$data['contact_number'] = $this->input->post('name');
				$data['address'] = $this->input->post('name');
				$data['email'] = $this->input->post('name');
				
				//need to md5 it
				$data['password'] = $this->input->post('name');
				
				//Call the model
				$this->organizAtion->newOrganization($data);
				
				//Load the login screen
				$this->load->view('login');
			}
        }
}
?>
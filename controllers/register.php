<?php
class Register extends CI_Controller{

        public function index()
        {
                $this->load->helper('form');
                $this->load->view('register_form');
<<<<<<< HEAD
=======
                $this->load->model('Organization');
>>>>>>> d30674f8be14f4965c304859a6787e1e4371fa7e
        }

        function newOrganization()
        {
	        $this->load->model('organization');
    
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
				$data['Name'] = $this->input->post('name');
                $data['Username'] = $this->input->post('username');
				$data['OwnerName'] = $this->input->post('owner_name');
				$data['ContactNumber'] = $this->input->post('contact_number');
				$data['Address'] = $this->input->post('address');
				$data['Email'] = $this->input->post('email');
				
				//need to md5 it
				$data['Password'] = $this->input->post('name');
				
				//Call the model
				$this->organization->newOrganization($data);
				
				//Load the login screen
				$this->load->view('login_form');
			}
        }
}
?>
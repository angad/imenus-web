<?php

class Features extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form');
		$this->load->helper('url');
	}
	
	
	public function index()
	{
		$this->load->view('sidebar');
		$this->load->view('features_edit', array('error'=>''));
		$this->load->view('footer');
	}
	
	public function newFeature()
	{
		$this->load->model('user_model');
		$menu_id = $this->user_model->getMenuId();

		//if session does not exist, load the login form
		if(!$menu_id)
		{
			$this->load->view('login_form');
			return;
		}
		
		$data['MenuId'] = $menu_id;
		
	 	$this->load->model('features_model');
    
		$this->load->library('form_validation');
		
		//Input validation rules
        $this->form_validation->set_rules('name', 'Name', 'required|min_length[5]');
		$this->form_validation->set_rules('minvalue', 'Minimum Value', '');
		$this->form_validation->set_rules('maxvalue', 'Maximum Value', '');

		$data['Name'] = $this->input->post('name');
		$data['MinValue'] = $this->input->post('minvalue');
		$data['MaxValue'] = $this->input->post('maxvalue');
		$count = $this->input->post('count');
		$data['StringValues'] = "";
		$data['Type'] = $this->input->post('t');
		
		echo $data['Type'];
		
		for($i=1; $i<=$count; $i++)
		{
			$data['StringValues'] = $data['StringValues']  . $this->input->post('option' . $i) . ';';
		}
		
		
		if ($this->form_validation->run() == FALSE)
		{
			//Reload the form if there is any error with the inputs
			$error = array('error' => $this->upload->display_errors());			
			$this->load->view('sidebar');
			$this->load->view('features_edit', $error);
			$this->load->view('footer');
		}
		else
		{				
			//Call the model
			$this->features_model->newFeature($data);
			
			echo "Load the features list ehre now";
		}
	}
}
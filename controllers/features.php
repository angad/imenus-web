<?php

class Features extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('features_model');
		$this->load->model('user_model');
	}
	
	function checkAccess()
	{
		$menu_id = $this->user_model->getMenuId();
		if(!$menu_id)
		{
			$this->load->view('login_form');
			return False;
		}
		return $menu_id;
	}
	
	public function index()
	{
		$menu_id = $this->checkAccess();
		
		$features = $this->features_model->getFeaturesFromMenu($menu_id);
        
        $this->load->library('table');
        $this->load->helper('html');
        $this->table->set_heading('Name', 'Type', 'Values');
		
		foreach($features as $feature)
		{
		    $items = '';
            if ($feature['Type'])
                $items = ul(explode(';', $feature['StringValues']));
            else
                $items = '<ul><li>Icon: '.img($feature['Icon']).'</li><li>Maximum: '.$feature['MaxValue'];
          
            $this->table->add_row(anchor('features/editFeature/'.$feature['ID'], $feature['Name']), $feature['Type'] ? 'Strings' : 'Icons', $items);
//			$data['features'][$i]['Name'] = $feature['Name'];
//			$data['features'][$i]['Type'] = $feature['Type'];
//			$data['features'][$i]['MaxValue'] = $feature['MaxValue'];
//			$data['features'][$i]['Icon'] = $feature['Icon'];
//			$data['features'][$i]['Value'] = $feature['StringValues'];
//			$i++;
		}
		
		$this->load->view('sidebar', array('title' => "Item Features"));
		$this->load->view('features_view', array('features' => $this->table->generate()));
		$this->load->view('footer');
	}
	
	public function addFeature()
	{
		$menu_id = $this->checkAccess();
		$this->load->view('sidebar', array('title' => "Add Item Feature"));
		$this->load->view('features_edit', array('error'=>''));
		$this->load->view('footer');
	}
	
	public function editFeature($id)
	{
		$menu_id = $this->checkAccess();
		
		$feature = $this->features_model->getFeatureById($id);
		
		if(!$feature)
		{
			echo "Invalid Feature edit.";
			return;
		}
		$this->load->view('sidebar', array('title'=>"Edit Item Feature"));
		$this->load->view('features_edit', array('error'=>'', 'feature'=>$feature));
		$this->load->view('footer');
	}

	public function newFeature()
	{
		$menu_id = $this->checkAccess();
		
		$data['MenuId'] = $menu_id;
		
	 	$this->load->model('features_model');
    
		$this->load->library('form_validation');
		
		//Input validation rules
        $this->form_validation->set_rules('name', 'Name', 'required|min_length[5]');
		$this->form_validation->set_rules('maxvalue', 'Maximum Value', '');

		$data['Name'] = $this->input->post('name');
		$data['MaxValue'] = $this->input->post('maxvalue');
		$count = $this->input->post('count');
		$data['StringValues'] = "";
		$data['Type'] = $this->input->post('rad');
		$data['Icon'] = 'images/' . $this->input->post('icon') . '.gif';
		$data['Fixed'] = $this->input->post('fixed');
	
		for($i=1; $i<=$count; $i++)
		{
			if(strstr($this->input->post('option'. $i), ';'))
			{
				$error = "Options cannot contain anything apart from alphabets or numbers.";
				$this->load->view('sidebar', array('title' => "Add Item Feature"));
				$this->load->view('features_edit', array('error'=>$error));
				$this->load->view('footer');
				return;
			}
			$data['StringValues'] = $data['StringValues']  . $this->input->post('option' . $i) . ';';
		}
		
		if($count!=0)
		{
			$data['MaxValue'] = $count;
		}
	
		if ($this->form_validation->run() == FALSE)
		{
			$error = '';
			//Reload the form if there is any error with the inputs
			$this->load->view('sidebar', array('title' => "Add Item Feature"));
			$this->load->view('features_edit', array('error'=>$error));
			$this->load->view('footer');
		}
		else
		{
			//Call the model
			$this->features_model->newFeature($data);
			$data = NULL;
			echo "Load the features list here now";
		}
	}
}
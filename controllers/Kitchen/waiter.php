<?php

class Waiter extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('organization');
		$this->load->model('Kitchen/waiter_model');
	}
	
	function getData($call)
	{
		if(!$call){
			echo "Nobody calls the damn waiter!";
			return;				
		}

		$data['time'] = $call['Time'];
		$data['table_number'] = $call['TableNumber'];
		if($call['Status']==1)
			$data['status'] = "Pending";
		if($call['Status']==2) 
			$data['status'] = "Bill request";
		$data['id'] = $call['Id'];
		return $data;
	}
	
	public function index()
	{		
		$organization_id = $this->organization->getOrganization();
		
		if(!$organization_id)
		{
			$this->load->view('login_form');
			return;
		}
	
		$waiter_calls = $this->waiter_model->getWaiter($organization_id);

		$this->load->view('Kitchen/waiter_header');

		foreach($waiter_calls as $call)
		{
			$data = $this->getData($call);
			$this->load->view('Kitchen/waiter_call', $data);
		}
		
		$this->load->view('Kitchen/waiter_footer');
	}
    
    /**
     * Retrieves and Outputs Waiter Call count for active Organization
     * Called from sidebar.php
     */
    public function getRequestCount() {
        $this->load->helper('globals');
        if (!isAJAX() || ($organization_id = $this->organization->getOrganization()) === FALSE)
            return;
        echo count($this->waiter_model->getWaiter($organization_id));
    }
	
	public function getRequests()
	{
		$organization_id = $this->organization->getOrganization();
		$waiter_calls = $this->waiter_model->getWaiter($organization_id);

		foreach($waiter_calls as $call)
		{
			$data = $this->getData($call);
			echo '<div id = "waiter">';
			echo '<div class = "time">' . $data['time'] . '</div>';
			echo '<div class = "table_number">' .  $data['table_number'] . '</div>';
			echo '<div class = "status">' . $data['status'] . '</div>';
			echo '<div id = "button"><button type = "button" onclick= \'removeCall('. $data['id'] .  ')\'>Clear</button></div>';
			echo '</div>';
			echo '<br style = "clear:both"/>';
		}
	}
	
	public function removeCall()
	{
		$id = $this->input->post('id');
		$this->waiter_model->removeCall($id);
		$this->getRequests();
	}
	
}
<?php

class Waiter extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->load->model('organization');
		$this->load->model('Kitchen/waiter_model');
		
		$organization_id = $this->organization->getOrganization();
		$waiter_calls = $this->waiter_model->getWaiter($organization_id);

		$this->load->view('Kitchen/waiter_header');

		foreach($waiter_calls as $call)
		{
			if(!$call){
				echo "Nobody calls the damn waiter!";
				return;				
			}

			$data['time'] = $call['Time'];
			$data['table_number'] = $call['TableNumber'];
			if($call['Status'])
				$data['status'] = "Pending";
			else $data['status'] = "Attended";

			$this->load->view('Kitchen/waiter_call', $data);
		}
	}
}
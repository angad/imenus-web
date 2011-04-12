<?php

class Orders extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->load->model('Kitchen/orders_model');
		$this->load->model('organization');
		$this->load->model('items_model');
		
		$organization_id = $this->organization->getOrganization();
		
		$orders = $this->orders_model->getOrders($organization_id);

		$this->load->view('Kitchen/kitchen_header.php');		
		
		foreach($orders as $order)
		{
			$order_item = $this->orders_model->getOrderItem($order['Id']);
			
			if(!$order_item) {
				echo "No Orders"; return;
			}
			
			$item = $this->items_model->getItem($order_item['ItemId']);
						
			$data['item_name'] = $item['Name'];
			$data['table_number'] = $order['TableNumber'];
			$data['quantity'] = $order_item['Quantity'];
			$data['remarks'] = $order_item['Remarks'];
			$data['time'] = $order_item['Timestamp'];
			
			$this->load->view('Kitchen/kitchen_order.php', $data);
		}

		$this->load->view('Kitchen/kitchen_footer.php');
	}
}

?>
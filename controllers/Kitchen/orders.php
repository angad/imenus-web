<?php

class Orders extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
		
			$this->load->model('Kitchen/orders_model');
			$this->load->model('organization');
			$this->load->model('items_model');

	}
	
	public function index()
	{		
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
						
			$data['item_name'] = $this->checknil($item['Name']);
			$data['table_number'] = $this->checknil($order['TableNumber']);
			$data['quantity'] = $this->checknil($order_item['Quantity']);
			$data['remarks'] = $this->checknil($order_item['Remarks']);
			$data['time'] = $this->checknil($order_item['Timestamp']);
			
			$this->load->view('Kitchen/kitchen_order.php', $data);
		}

		$this->load->view('Kitchen/kitchen_footer.php');
	}
	
	function checknil($check)
	{
		if($check)
		{
			return $check;
		}
		else return "&nbsp;";
	}
	
	public function getorders()
	{
		$organization_id = $this->organization->getOrganization();
		
		$orders = $this->orders_model->getOrders($organization_id);
		
		foreach($orders as $order)
		{
			$order_item = $this->orders_model->getOrderItem($order['Id']);
			
			if(!$order_item) {
				echo "No Orders"; return;
			}
			
			$item = $this->items_model->getItem($order_item['ItemId']);
						
			$data['item_name'] = $this->checknil($item['Name']);
			$data['table_number'] = $this->checknil($order['TableNumber']);
			$data['quantity'] = $this->checknil($order_item['Quantity']);
			$data['remarks'] = $this->checknil($order_item['Remarks']);
			$data['time'] = $this->checknil($order_item['Timestamp']);

		echo '<div class = "item_name">' . $data["item_name"] . '</div>';
		echo '<div class = "quantity">' . $data["quantity"] .'</div>';
		echo '<div class = "remarks">' . $data["remarks"] . '</div>';
		echo '<div class = "time">' . $data["time"] . '</div>';
		echo '<div class = "table_number">' . $data["table_number"] . '</div>';
		echo '<br/>'

		}
	}
	
}

?>
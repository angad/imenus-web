<?php

class Orders extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('Kitchen/orders_model');
		$this->load->model('organization');
		$this->load->model('items_model');
		$this->load->model('features_model');
	}
	
	function checknil($check)
	{
		if($check)
		{
			return $check;
		}
		else return "&nbsp;";
	}
	
	
	public function getOrder($order)
	{
		//get the order items
		$order_item = $this->orders_model->getOrderItem($order['Id']);
				
		if(!$order_item) {
			echo "No Orders"; return;
		}
	
		//get the order item features
		$orderItemFeatures = $this->features_model->getOrderItemFeatures($order_item['Id']);
			//get the feature names and values
		$i=0;
		foreach($orderItemFeatures as $orderItemFeature)
		{
			$feature = $this->features_model->getFeatureById($orderItemFeature['FeatureId']);
						
			$data['feature_names'][$i] = $feature['Name'];
			$data['feature_values'][$i] = $orderItemFeature['Value'];
			$i++;
		}
		
		if($i==0)
		{
			$data['feature_names'] = NULL;
			$data['feature_value'] = NULL;
		}
		
		//get the order item
		$item = $this->items_model->getItem($order_item['ItemId']);
		
		$data['item_name'] = $this->checknil($item['Name']);
		$data['table_number'] = $this->checknil($order['TableNumber']);
		$data['quantity'] = $this->checknil($order_item['Quantity']);
		$data['remarks'] = $this->checknil($order_item['Remarks']);
		$data['time'] = $this->checknil($order_item['Timestamp']);
		
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
		
		$orders = $this->orders_model->getOrders($organization_id);
		
		$this->load->view('Kitchen/kitchen_header.php');
			
		foreach($orders as $order)
		{
			$data = $this->getOrder($order);
				
			$this->load->view('Kitchen/kitchen_order.php', $data);
		}		
		$this->load->view('Kitchen/kitchen_footer.php');
	}

	public function getorders()
	{
		$organization_id = $this->organization->getOrganization();
		
		$orders = $this->orders_model->getOrders($organization_id);
		
		foreach($orders as $order)
		{
			$data = $this->getOrder($order);
			
			echo '<div id = "order">';
			echo '<div class = "item_name">' . $data["item_name"] . '</div>';
			echo '<div class = "quantity">' . $data["quantity"] .'</div>';
			echo '<div class = "remarks">' . $data["remarks"] . '</div>';
			echo '<div class = "time">' . $data["time"] . '</div>';
			echo '<div class = "table_number">' . $data["table_number"] . '</div>';
			echo '<br/>';
			if($data['feature_names'])
			{
				echo 'Features : ';		
			foreach($data['feature_names'] as $feature_name)
			{
				echo $feature_name;
			}
			foreach($data['feature_values'] as $feature_value)
			{
				echo $feature_value;
			}
			}
			echo '</div>';
		}
	}
	
}

?>
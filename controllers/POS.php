<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

class POS extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('POS_model');
        $this->load->helper('url');
        date_default_timezone_set('Asia/Singapore');
    }

	public function index() {
        if ($this->User_model->getOrgID() === FALSE)
            redirect('/user');
        
		$data['table'] = $this->table();
        $data['AJAXUpdate'] = 'POS/table';
        $this->load->view('sidebar', array('title' => 'Bill Overview'));
		$this->load->view('POS_main_view', $data);
        $this->load->view('footer');
    }

    public function table() {
        if (($orgID = $this->User_model->getOrgID()) === FALSE)
            exit('');
        
        $updated = date('Y-m-d h:i:s A T');
        
        $active = $this->POS_model->getActiveOrders($orgID);
        $this->load->library('table');
        
        $this->table->set_heading('Table', 'Items Ordered', 'Total Payable', 'Orders', 'Bill');
        
        foreach ($active as $order) {
            $this->table->add_row($order['TableNumber'], $order['ItemsOrdered'], sprintf('$%01.2f', $order['TotalBill']), anchor('POS/view/'.$order['ID'], 'View Orders'), anchor('POS/bill/'.$order['ID'], 'Bill'));
        }
        $output = '<h4 class="updated">Updated: '.$updated."</h4>\n".$this->table->generate();
        
        if (isAJAX())
            echo $output;
        else
            return $output;
    }
    
    public function view($orderID = NULL) {
        if (!is_numeric($orderID))
            show_404();
        
        $this->load->model('Categories_model');
        $this->load->model('Kitchen/Orders_model');
        $det = $this->Orders_model->getOrderDetails($orderID);
        if (empty($det))
            show_404();
        else if ($det['OrganizationID'] != $this->User_model->getOrgID())
            show_error(ACCESS_DENIED_MSG, 403, ACCESS_DENIED);
        
        $data['table'] = $this->orders($orderID);
        $data['back'] = 'POS';
        $data['AJAXUpdate'] = 'POS/orders/'.$orderID;
        $this->load->view('sidebar', array('title' => 'Active Orders'));
		$this->load->view('POS_main_view', $data);
        $this->load->view('footer');
    }
    
    public function orders($orderID = NULL) {
        if (!is_numeric($orderID))
            show_404();
        
        if(isAJAX()) {
            $this->load->model('Kitchen/Orders_model');
            $det = $this->Orders_model->getOrderDetails($orderID);
            if (empty($det))
                show_404();
            else if ($det['OrganizationID'] != $this->User_model->getOrgID())
                show_error(ACCESS_DENIED_MSG, 403, ACCESS_DENIED);
        }
        
        $updated = date('Y-m-d h:i:s A T');
        
        $orders = $this->POS_model->getOrderItemETAs($orderID);
        $this->load->library('table');
        
        $this->table->set_heading('Item', 'Quantity', 'Ordered', 'Started', 'ETA');
        
        foreach ($orders as $order) {
            $ETA = $order['ETA'];
            $ETAstr = sprintf('%ds', $ETA % 60);
            
            if ($ETAm = ((int) ($ETA / 60)) % 60) {
                $ETAstr = sprintf('%dm:', $ETAm).$ETAstr;
                if ($ETAh = ((int) ($ETA / 3600)))
                    $ETAstr = sprintf('%dh:', $ETAh).$ETAstr;
            }
            
            $this->table->add_row($order['Name'], $order['Quantity'], date('Y-m-d h:i:s A T', $order['Timestamp']), $order['Started'] ? date('Y-m-d h:i:s A T', $order['Started']) : '-', $ETAstr);
        }
        $output = '<h4 class="updated">Updated: '.$updated."</h4>\n".$this->table->generate();
        
        if (isAJAX())
            echo $output;
        else
            return $output;
    }
    
    public function bill($orderID = NULL) {
        if (!is_numeric($orderID))
            show_404();
        
        $this->load->model('Categories_model');
        $this->load->model('Kitchen/Orders_model');
        $this->load->model('Items_model');
        
        $det = $this->Orders_model->getOrderDetails($orderID);
        if ($det['OrganizationID'] != ($orgID = $this->User_model->getOrgID()))
            show_error(ACCESS_DENIED_MSG, 403, ACCESS_DENIED);
        
        $items = $this->POS_model->getOrderItemDetails($orderID);
        
        $this->load->library('table');
        $this->table->set_heading('Qty', 'Item', 'Price', 'Amt (S$)');
        
        foreach ($items as $item) {
            $itemDisp = htmlspecialchars($item['Name']);
            if ($item['Type'] == ITEMS_TYPE_MEAL) {
                $mealItems = $this->Items_model->getMealItems($item['ItemID']);
                $itemDisp .= "\n<ul>";
                foreach ($mealItems as $mealItem)
                    $itemDisp .= "\n<li>".htmlspecialchars($mealItem['ItemQuantity'].' x '.$mealItem['Name']).'</li>';
                $itemDisp .= "\n</ul>";
            }
            $this->table->add_row($item['Quantity'], $itemDisp, sprintf('$%01.2f', $item['Price']), sprintf('$%01.2f', $item['Quantity'] * $item['Price']));
        }
        
        $this->load->model('Organization');
        $orgData = $this->Organization->getOrganizationData($orgID);
        
        $data['OrderID'] = $orderID;
        $data['Time'] = date('d M Y, h:i:s A T');
        $data['Name'] = $orgData['Name'];
        $data['Address'] = $orgData['Address'];
        $data['Contact'] = $orgData['ContactNumber'];
        $data['Table'] = $det['TableNumber'];
        $data['Remarks'] = $det['Remarks'];
        $data['GSTrate'] = $orgData['GSTrate'];
        $data['ServiceCharge'] = $orgData['ServiceCharge'];
        $data['table'] = $this->table->generate();
        
        $this->load->view('sidebar', array('title' => 'Bill Payment'));
		$this->load->view('POS_payment_view', $data);
        $this->load->view('footer');
    }
    
    function clear($orderID = NULL) {
        if (!is_numeric($orderID))
            show_404();
        
        $this->load->model('Kitchen/Orders_model');
        
        $det = $this->Orders_model->getOrderDetails($orderID);
        if ($det['OrganizationID'] != ($orgID = $this->User_model->getOrgID()))
            show_error(ACCESS_DENIED_MSG, 403, ACCESS_DENIED);
        
        $this->POS_model->removeOrder($orderID);
        
        redirect('POS');
    }
}
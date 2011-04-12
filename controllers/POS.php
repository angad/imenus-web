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
        
        $this->table->set_heading('Table', 'Items Ordered', 'Total Payable');
        
        foreach ($active as $order) {
            $this->table->add_row($order['TableNumber'], $order['ItemsOrdered'], '$'.$order['TotalBill']);
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
//        if ($det['OrganizationID'] != $this->User_model->getOrgID())
//            show_error(ACCESS_DENIED_MSG, 403, ACCESS_DENIED);
        
        $data['table'] = $this->orders($orderID);
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
//            if ($det['OrganizationID'] != $this->User_model->getOrgID())
//                show_error(ACCESS_DENIED_MSG, 403, ACCESS_DENIED);
        }
        
        $updated = date('Y-m-d h:i:s A T');
        
        $orders = $this->POS_model->getOrderItems($orderID);
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
        
        //if AJAX: http://snipplr.com/view/1060/check-for-ajax-request/
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
        $det = $this->Orders_model->getOrderDetails($orderID);
//        if ($det['OrganizationID'] != $this->User_model->getOrgID())
//            show_error(ACCESS_DENIED_MSG, 403, ACCESS_DENIED);
        
        $data['table'] = $this->orders($orderID);
        
        $this->load->view('sidebar', array('title' => 'Bill Payment'));
		$this->load->view('POS_payment_view', $data);
        $this->load->view('footer');
    }
    
}
<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

define('PAGESIZE_ITEMS', 10);
define('ITEMS_TYPE_ITEM', 0);
define('ITEMS_TYPE_MEAL', 1);

class Items extends CI_Controller {

	public function view($catID = NULL, $page = 1) {
		if (!is_numeric($catID))
            show_404('', FALSE);
            
        $this->load->library('table');        
        $this->load->helper(array('url', 'form', 'html'));
        $this->load->model('Items_model');
        
        $links = anchor('items/additem/'.$catID.'/'.ITEMS_TYPE_ITEM, 'Add Item').anchor('items/additem/'.$catID.'/'.ITEMS_TYPE_MEAL, 'Add Meal');
        
        $this->table->set_heading('Pic', 'Name', 'Price', 'Edit', 'Delete');
        $this->table->set_template(array('row_start' => '<tr class="odd">', 'row_alt_start' => '<tr class = "even">'));
        
        foreach ($this->Items_model->getAll($catID) as $item) {
            $this->table->add_row(img($item['ImageSmall']), anchor('items/viewitem/'.$item['ID'], $item['Name']), $item['Price'], anchor('items/edititem/'.$item['ID'], 'Edit Item'), anchor('items/deleteitem/'.$item['ID'], 'Delete Item'));
        }
        $this->load->view('content_view', array('title' => 'Items', 'content' => $links.br().$this->table->generate()));
	}
    
    public function additem($catID, $itemType = ITEMS_TYPE_ITEM) {
        $this->_details(NULL, FALSE, $itemType, $catID);
    }
    
    public function edititem($itemID) {
        $this->_details($itemID, FALSE);
    }
    
    public function viewitem($itemID) {
        $this->_details($itemID, TRUE);
    }
    
    public function submitadditem($catID, $itemType) {
        $this->_handlesubmit($catID, $itemType, TRUE);
    }
    
    public function submitedititem($itemID) {
        $this->_handlesubmit(NULL, NULL, FALSE, $itemID);
    }
    
    private function _handlesubmit($catID, $itemType, $insert, $itemID = NULL) {
        $this->load->helper('url');
        $this->load->library("form_validation");
        $this->form_validation->set_rules('catID', 'Category', 'required')->set_rules('name', 'Name', 'required')->set_rules('longdesc', 'Long Description', 'required')->set_rules('price', 'Price', 'required|numeric');
        if ($itemType == ITEMS_TYPE_MEAL)
            $this->form_validation->set_rules('items[]', 'Meal Items', 'required');
        
        if (!$this->form_validation->run())
            if ($insert)
                return $this->_details(NULL, FALSE, $itemType, $catID);
            else
                return $this->_details($this->input->post('ID'), FALSE);
        
        $this->load->model('Items_model');
        
        if ($insert) {
            $this->Items_model->addItem($catID, $this->input->post('name'), $this->input->post('longdesc'), $this->input->post('shortdesc'), $this->input->post('price'), isset($_POST['items']) ? ITEMS_TYPE_MEAL : ITEMS_TYPE_ITEM);
            redirect ('items/view/'.$catID);
        } else {
            $this->Items_model->updateItem($itemID, $this->input->post('catID'), $this->input->post('name'), $this->input->post('longdesc'), $this->input->post('shortdesc'), $this->input->post('price'), isset($_POST['items']) ? ITEMS_TYPE_MEAL : ITEMS_TYPE_ITEM);
            redirect ('items/view/'.$this->input->post('catID'));
        }
        
        
    }
    
    private function _details($itemID, $readonly, $itemType = NULL, $catID = NULL) {
        $this->load->model('Items_model');
        $item = $this->Items_model->getItem($itemID);
        $this->load->helper(array('url', 'html', 'form'));
        
        if (isset($item['CategoryID']))
            $catID = $item['CategoryID'];
        
        $output = anchor('items/view/'.$catID, '< Back').br(2).validation_errors();

        if (isset($items['ImageSmall']))        
            $output .= img(array('src' => $item['ImageSmall'], 'class' => 'zooming'));
        if (isset($items['ImageMedium']))
            $output .= img(array('src' => $item['ImageMedium'], 'class' => 'zooming'));
        if (isset($items['ImageLarge']))
            $output .= img(array('src' => $item['ImageLarge'], 'class' => 'zooming'));
        
        $submit_url = substr(current_url(), strlen(base_url().index_page()) + 1);
        $slash_pos = strpos($submit_url, '/');
        $submit_url = substr($submit_url, 0, $slash_pos + 1).'submit'.substr($submit_url, $slash_pos + 1);
        
        $output .= form_open($submit_url);
        
        $this->load->model('Categories_model');
        
        $readonly_text = $readonly ? 'readonly="readonly"' : '';
        
        $output .= '<div class="form-item"><label for="edit-catID">Category: <span class="form-required" title="This field is required">*</span></label>'.$this->load->view('tree_select_view', array('tree' => $this->Categories_model->getTreeFromCurrentMenu($catID), 'selected' => $catID, 'name' => 'catID', 'readonly' => $readonly), TRUE).'</div>';
        
        $output .= '<div class="form-item"><label for="edit-name">Name: <span class="form-required" title="This field is required">*</span></label>'.form_input('name', isset($item['Name']) ? $item['Name'] : '', $readonly_text).'</div>';
        
        $output .= '<div class="form-item"><label for="edit-shortdesc">Short Description: </label>'.form_input('shortdesc', isset($item['ShortDescription']) ? $item['ShortDescription'] : '', $readonly_text).'</div>';
        
        $output .= '<div class="form-item"><label for="edit-longdesc">Long Description: <span class="form-required" title="This field is required">*</span></label>'.form_textarea('longdesc', isset($item['LongDescription']) ? $item['LongDescription'] : '', $readonly_text).'</div>';
        
        $output .= '<div class="form-item"><label for="edit-price">Price: <span class="form-required" title="This field is required">*</span></label>$ '.form_input('price', isset($item['Price']) ? $item['Price'] : '', $readonly_text).'</div>';

        if (isset($itemType) && $itemType == ITEMS_TYPE_MEAL)        
            $output .= '<div class="form-item"><label for="edit-items[]">Meal Items: <span class="form-required" title="This field is required">*</span></label>'.$this->load->view('tree_select_view', array('tree' => $this->Categories_model->getTreeFromCurrentMenu($catID, TRUE), 'selected' => array(), 'name' => 'items[]', 'readonly' => $readonly), TRUE).'</div>';
        
        if (!$readonly)
            $output .= form_submit('submit', 'Add');
            
        $output .= form_close();
        
//        $this->load->library('form_validation');
//		
//		
//		//Input validation rules
//        $this->form_validation->set_rules('username', 'Username', 'callback_username_check');
//		$this->form_validation->set_rules('password', 'Password', 'required');
//		$this->form_validation->set_rules('repeat', 'Password Confirmation', 'required');
//		$this->form_validation->set_rules('email', 'Email', 'required');
//
//		if ($this->form_validation->run() == FALSE)
//		{
//			//Reload the form if there is any error with the inputs
//			$this->load->view('register_form');
//		}
//		else
//		{
//			$data['name'] = $this->input->post('name');
//            $data['username'] = $this->input->post('username');
//			$data['owner_name'] = $this->input->post('owner_name');
//			$data['contact_number'] = $this->input->post('name');
//			$data['address'] = $this->input->post('name');
//			$data['email'] = $this->input->post('name');
//			
//			//need to md5 it
//			$data['password'] = $this->input->post('name');
//			
//			//Call the model
//			$this->organizAtion->newOrganization($data);
//			
//			//Load the login screen
//			$this->load->view('login');
//		}
     
        $this->load->view('content_view', array('title' => 'Add Item', 'content' => $output, 'load_jquery_zoom' => TRUE));

    }

}
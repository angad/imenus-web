<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

define('PAGESIZE_ITEMS', 10);


class Items extends CI_Controller {
    
    private function _checkAccess($catID) {
        $this->load->model('User_model');
        $this->load->model('Categories_model');
        $this->load->helper('url');
        
 		if (($menuID = $this->User_model->getMenuId()) === FALSE)
            redirect('/user');

        if (!is_numeric($catID))
            show_404('', FALSE);
        else if (!in_array($catID, $this->Categories_model->getAllIDs($menuID)))
            show_error(ACCESS_DENIED_MSG, 403, ACCESS_DENIED);
    }

	public function view($catID = NULL, $page = 1) {
		$this->_checkAccess($catID);
            
        $this->load->library('table');        
        $this->load->helper(array('url', 'form', 'html'));
        $this->load->model('Items_model');
        
        $this->load->model('Categories_model');
        $cat = $this->Categories_model->getCat($catID);
        $parentCat = $cat['parentID'];
        
        $note = '<h3 class="note">You may sort the Items and Set Meals by dragging them around.</h3>';
        $note .= anchor('items/additem/'.$catID.'/'.ITEMS_TYPE_ITEM, 'Add Item').' '.anchor('items/additem/'.$catID.'/'.ITEMS_TYPE_MEAL, 'Add Meal');
        
        $this->table->set_template(array('table_open' => '<table id="order" border="0" cellpadding="4" cellspacing="0">', 'table_close' => '</table><span id="order-save">Save</span>'));
        $this->table->set_heading('Pic', 'Name', 'Price', 'Edit', 'Delete');
        
        foreach ($this->Items_model->getAll($catID) as $item) {
            $this->table->add_row(img($item['ImageSmall']), anchor('items/viewitem/'.$item['ID'], $item['Name']), $item['Price'], anchor('items/edititem/'.$item['ID'], 'Edit Item'), anchor('items/deleteitem/'.$item['ID'], 'Delete Item'));
        }
        
        $data = array('title' => 'Categories', 'content' => $note.$this->table->generate(), 'back' => 'categories/index/'.$parentCat, 'include_scripts' => array(site_url('../scripts/jquery.tablednd_0_5.js'), site_url('../scripts/reorder.js')));
        $data['document_ready'] = 'handleReOrder("../reorder/'.$catID.'");';
        $this->load->view('content_view', $data);
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
        $this->_checkAccess($catID);
        
        $this->load->helper('url');
        $this->load->library("form_validation");
        $this->form_validation->set_rules('catID', 'Category', 'required')->set_rules('name', 'Name', 'required')->set_rules('longdesc', 'Long Description', 'required')->set_rules('price', 'Price', 'required|numeric');
        if ($itemType == ITEMS_TYPE_MEAL)
            $this->form_validation->set_rules('items[]', 'Meal Items', 'required');
        
        if (!$this->form_validation->run())
            if ($insert)
                return $this->_details(NULL, FALSE, $itemType, $catID);
            else
                return $this->_details($itemID, FALSE);
        
        $this->load->model('Items_model');
        
        if ($insert) {
            $this->Items_model->addItem($catID, $this->input->post('name'), $this->input->post('longdesc'), $this->input->post('shortdesc'), $this->input->post('price'), isset($_POST['items']) ? ITEMS_TYPE_MEAL : ITEMS_TYPE_ITEM, '', '', '', $this->input->post('items'));
            redirect ('items/view/'.$catID);
        } else {
            $this->Items_model->updateItem($itemID, $this->input->post('catID'), $this->input->post('name'), $this->input->post('longdesc'), $this->input->post('shortdesc'), $this->input->post('price'), isset($_POST['items']) ? ITEMS_TYPE_MEAL : ITEMS_TYPE_ITEM, NULL, NULL, NULL, $this->input->post('items'));
            redirect ('items/view/'.$this->input->post('catID'));
        }       
    }
    
    private function _details($itemID, $readonly, $itemType = NULL, $catID = NULL) {
        $this->load->model('Items_model');
        $item = $this->Items_model->getItem($itemID);
        $this->load->helper(array('url', 'html', 'form'));
        
        $mode = isset($catID) ? 'Add' : ($readonly ? 'View' : 'Edit');
        
        if (!empty($item['CategoryID'])) { 
            $catID = $item['CategoryID'];
            $itemType = $item['Type'];
        }
        
        $this->_checkAccess($catID);
        
        $output = '';

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
            $output .= '<div class="form-item"><label for="edit-items[]">Meal Items: <span class="form-required" title="This field is required">*</span></label>'.$this->load->view('tree_select_view', array('tree' => $this->Categories_model->getTreeFromCurrentMenu($catID, TRUE), 'selected' => isset($itemID) ? $this->Items_model->getMealItems($itemID, TRUE) : array(), 'name' => 'items[]', 'readonly' => $readonly, 'leaffilter' => ITEMS_TYPE_ITEM), TRUE).'</div>';
        
        if (!$readonly)
            $output .= form_submit('submit', $mode);
            
        $output .= form_close();
     
        $data = array('title' => $mode.' Item', 'content' => $output, 'back' => 'items/view/'.$catID);
        if ($itemType == ITEMS_TYPE_MEAL) {
            $data['include_scripts'] = array('https://github.com/odyniec/selectlist/raw/master/jquery.selectlist.dev.js');
            $data['include_css'] = array('https://github.com/odyniec/selectlist/raw/master/distfiles/css/selectlist.css');
            $data['document_ready'] = '$("select[multiple=\'multiple\']").selectList();';
        }
        $this->load->view('content_view', $data);
    }
    
    public function reorder($catID) {
        $this->load->model('User_model');
        $this->load->helper('url');
        
        $this->load->model('Categories_model');
        
 		if (($menuID = $this->User_model->getMenuId()) === FALSE || !in_array($catID, $this->Categories_model->getAllIDs($menuID)))
            return;
            
        $i = 1;
        
        foreach ($this->input->post('order') as $item)
            if (is_numeric($item))
                $this->db->query('UPDATE '.ITEMS_TABLE.' SET SortOrder = ? WHERE ID = ?', array($i++, $item));
    }
}
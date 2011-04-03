<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author patrick
 */

define('CATPREFIX', 'cat');
define('PLACEHOLDER', 'Add New Category');
define('EDITTITLE', 'Click to Edit');
define('CATDELPROMPT', 'Are you sure you want to delete this category? If you do, %d Item(s) and %d Set Meal(s) will also be deleted!');

 class Categories extends CI_Controller {
    
    private function _checkAccess($catID) {
        $this->load->model('User_model');
        $this->load->model('Categories_model');
        $this->load->helper('url');
        
 		if (($menuID = $this->User_model->getMenuId()) === FALSE)
            redirect('/user');

        if (!is_numeric($catID))
            show_404('', FALSE);
        else if ($catID != 0 && !in_array($catID, $this->Categories_model->getAllIDs($menuID)))
            show_error(ACCESS_DENIED_MSG, 403, ACCESS_DENIED);
    }
 
 	public function index($parentID = 0) {   
        $this->load->model('User_model');
        $this->load->helper(array('form', 'url'));
        
 		if (($menuID = $this->User_model->getMenuId()) === FALSE)
            redirect('/user');
            
        $this->load->library('table');
        $this->load->model('Categories_model');
        $this->load->model('Items_model');
        
        $note = '<h3 class="note">A Category can have Sub-Categories or Items, but not both.<br />You may sort the Categories by dragging them around.</h3>';
        if (count($this->Items_model->getAll($parentID, NULL, NULL, 1)) == 0)
            $note .= anchor('categories/add/'.$parentID, 'Add Sub-Category');
        
        $this->table->set_template(array('table_open' => '<table id="order" border="0" cellpadding="4" cellspacing="0">', 'table_close' => '</table><span id="order-save">Save</span>'));
        $this->table->set_heading('Category', 'Edit', 'Sub-Categories', 'Items', 'Delete');
        
        foreach ($this->Categories_model->getAll($menuID, TRUE, $parentID) as $cat) {
            $this->table->add_row(anchor('categories/view/'.$cat['ID'], $cat['Name']), anchor('categories/edit/'.$cat['ID'], 'Edit Category'), $cat['ItemCount'] ? '' : anchor('categories/index/'.$cat['ID'], 'View Sub-Categories'), $cat['SubcatCount'] ? '' : anchor('items/view/'.$cat['ID'], 'View Items'), anchor('categories/delete/'.$cat['ID'], 'Delete Category', array('class' => 'modalconfirm', 'data-modaltext' => sprintf(CATDELPROMPT, $cat['ItemCount'], $cat['MealCount'], $cat['SubcatCount']))));
        }
        $data = array('title' => 'Categories', 'content' => $note.$this->table->generate(), 'include_scripts' => array(site_url('../scripts/jquery.tablednd_0_5.js'), site_url('../scripts/reorder.js')));
        $data['document_ready'] = 'handleReOrder("../reorder/'.$parentID.'");';
        $this->load->view('content_view', $data);
 	}
    
    public function add($parentID) {
        $this->_detail(NULL, FALSE, $parentID);
    }
    
    public function view($catID) {
        $this->_detail($catID, TRUE);
    }
    
    public function edit($catID) {
        $this->_detail($catID);
    }
    
    public function submitadd($parentID) {
        $this->_handlesubmit($parentID);
    }
    
    public function submitedit($catID) {
        $this->_handlesubmit(NULL, $catID);
    }
    
    private function _detail($catID, $readonly = FALSE, $parentID = NULL) {
        $this->load->model('User_model');
        $this->load->helper(array('url', 'form', 'html'));
        
        $mode = isset($parentID) ? 'Add' : ($readonly ? 'View' : 'Edit');
        
        $name = '';
        
        $this->load->model('Categories_model');
        
        if (isset($catID)) {
            $cat = $this->Categories_model->getCat($catID);
            $parentID = $cat['parentID'];
            $name = $cat['Name'];
        }
        
        $this->_checkAccess($parentID);
       
        $submit_url = substr(current_url(), strlen(base_url().index_page()) + 1);
        $slash_pos = strpos($submit_url, '/');
        $submit_url = substr($submit_url, 0, $slash_pos + 1).'submit'.substr($submit_url, $slash_pos + 1);
        
        $output = form_open($submit_url);
        
        $readonly_text = $readonly ? 'readonly="readonly"' : '';
        
        $output .= '<div class="form-item"><label for="edit-parentID">Parent Category: <span class="form-required" title="This field is required">*</span></label>'.$this->load->view('tree_select_view', array('tree' => $this->Categories_model->getTreeFromCurrentMenu($parentID), 'selected' => $parentID, 'name' => 'parentID', 'readonly' => $readonly, 'allselectable' => TRUE), TRUE).'</div>';
        
        $output .= '<div class="form-item"><label for="edit-name">Name: <span class="form-required" title="This field is required">*</span></label>'.form_input('name', $name, $readonly_text).'</div>';
        
        if (!$readonly)
            $output .= form_submit('submit', $mode);
            
        $output .= form_close();
     
        $data = array('title' => $mode.' Category', 'content' => $output, 'back' => 'categories/index/'.$parentID);
        $this->load->view('content_view', $data);
    }
    
    private function _handlesubmit($parentID, $catID = NULL) {
        $this->_checkAccess(isset($catID) ? $catID : $parentID);
        
        $this->load->helper('url');
        $this->load->library("form_validation");
        $this->form_validation->set_rules('name', 'Name', 'required');
        
        $insert = isset($parentID);
        
        if (!$this->form_validation->run())
            if ($insert)
                return $this->_details(NULL, FALSE, $parentID);
            else
                return $this->_details($catID, FALSE);
        
        $this->load->model('Categories_model');
        
        $parentID = $this->input->post('parentID');
        
        if ($insert) {
            //$this->Items_model->addItem($catID, $this->input->post('name'), $this->input->post('longdesc'), $this->input->post('shortdesc'), $this->input->post('price'), isset($_POST['items']) ? ITEMS_TYPE_MEAL : ITEMS_TYPE_ITEM, NULL, NULL, NULL, $this->input->post('items'));
            $this->Categories_model->add($this->User_model->getMenuId(), $this->input->post('name'), $parentID);
            redirect ('categories/index/'.$parentID);
        } else {
            $this->Categories_model->update($catID, $this->input->post('name'), $parentID);
            redirect ('categories/index/'.$parentID);
        }       
    }
    
    public function reorder($parentID) {
        $this->load->model('User_model');
        $this->load->helper('url');
        
        $this->load->model('Categories_model');
        
 		if (($menuID = $this->User_model->getMenuId()) === FALSE || ($parentID != 0 && !in_array($parentID, $this->Categories_model->getAllIDs($menuID))))
            return;
            
        $i = 1;
        
        foreach ($this->input->post('order') as $cat)
            if (is_numeric($cat))
                $this->db->query('UPDATE '.CATEGORIES_TABLE.' SET SortOrder = ? WHERE ID = ?', array($i++, $cat));
    }
    
    public function delete($catID) {
        $this->load->model('User_model');
        $this->load->helper('url');
        
        $this->load->model('Categories_model');
        
 		if (($menuID = $this->User_model->getMenuId()) === FALSE)
            redirect('/user');
        else if (!in_array($catID, $this->Categories_model->getAllIDs($menuID)))
            redirect('/user');
        
        $this->Categories_model->remove($catID);
    }
 
 }
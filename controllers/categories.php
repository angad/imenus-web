<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');
/**
 * iMenus Categories Controller
 *
 * @package		iMenus
 * @category	Controllers
 * @author		Patrick
 */

define('CATPREFIX', 'cat');
define('PLACEHOLDER', 'Add New Category');
define('EDITTITLE', 'Click to Edit');
define('CATDELPROMPT', 'Are you sure you want to delete this category?');
define('CATDELPROMPTI', 'Are you sure you want to delete this category? If you do, %d Item(s) and %d Set Meal(s) will also be deleted!');
define('CATDELPROMPTS', 'Are you sure you want to delete this category? If you do, you will lose %d Sub-Categories and ALL their Items and Set Meals!');


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
    
    /**
     * Index / Default Page
     *
     * Lists Categories in a Table, allowing them to be reordered  
     *
     * @access	public
     * @param	int   parentID
     */
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
        
        $this->table->set_template(array('table_open' => '<table id="order" border="0" cellpadding="4" cellspacing="0">'));
        $this->table->set_heading('Category', 'Edit', 'Sub-Categories', 'Items', 'Delete');
        
        foreach ($this->Categories_model->getAll($menuID, TRUE, $parentID) as $cat) {
            if ($cat['ItemCount'] != 0)
                $prompt = sprintf(CATDELPROMPTI, $cat['ItemCount'], $cat['MealCount']);
            else if ($cat['SubcatCount'] != 0)
                $prompt = sprintf(CATDELPROMPTS, $cat['SubcatCount']);
            else
                $prompt = CATDELPROMPT;
            $this->table->add_row(anchor('categories/view/'.$cat['ID'], $cat['Name']), anchor('categories/edit/'.$cat['ID'], 'Edit Category'), $cat['ItemCount'] ? '' : anchor('categories/index/'.$cat['ID'], 'View Sub-Categories'), $cat['SubcatCount'] ? '' : anchor('items/view/'.$cat['ID'], 'View Items'), anchor('categories/delete/'.$cat['ID'], 'Delete Category', array('class' => 'modalconfirm', 'data-modaltext' => $prompt)));
        }
        $data = array('title' => 'Categories', 'content' => $note.$this->table->generate(), 'include_scripts' => array(site_url('../scripts/jquery.tablednd_0_5.js'), site_url('../scripts/reorder.js')));
        $data['document_ready'] = 'handleReOrder("order", "'.site_url('categories/reorder/'.$parentID).'");';
        $this->load->view('content_view', $data);
 	}
    
    /**
     * Add Cat page / form  
     *
     * @access	public
     * @param	int   parentID
     */
    public function add($parentID) {
        if ($this->input->post())
            $this->_handlesubmit($parentID);
        else
            $this->_detail(NULL, FALSE, $parentID);
    }
    
    /**
     * View Cat page / form
     *
     * @access	public
     * @param	int   catID
     */
    public function view($catID) {
        $this->_detail($catID, TRUE);
    }
    
    /**
     * Edit Cat page / form
     *
     * @access	public
     * @param	int   catID
     */
    public function edit($catID) {
        if ($this->input->post())
            $this->_handlesubmit(null, $catID);
        else
            $this->_detail($catID);
    }
    
    /**
     * Cat Form Generator
     *
     * @access	private
     * @param	int
     * @param   boolean
     * @param   int
     */
    private function _detail($catID, $readonly = FALSE, $parentID = NULL) {
        $this->load->model('User_model');
        $this->load->helper(array('url', 'form', 'html', 'form_items'));
        
        $mode = isset($parentID) ? 'Add' : ($readonly ? 'View' : 'Edit');
        
        $name = '';
        
        $this->load->model('Categories_model');
        
        if (isset($catID) && $cat = $this->Categories_model->getCat($catID)) {
            if (!empty($cat)) {
                $parentID = $cat['parentID'];
                $name = $cat['Name'];
            }
        }
        
        $this->_checkAccess(isset($catID) ? $catID : $parentID);
       
        $output = form_open();
        
        $readonly_text = $readonly ? 'readonly="readonly"' : '';
        
        $output .= tree_select_item('parentID', 'Parent Category', $this->Categories_model->getTreeFromMenu($this->User_model->getMenuId()), $parentID, TRUE, $readonly, TRUE);
        // $output .= '<div class="form-item"><label for="edit-parentID">Parent Category: <span class="form-required" title="This field is required">*</span></label>'.$this->load->view('tree_select_view', array('tree' => $this->Categories_model->getTreeFromCurrentMenu($parentID), 'selected' => $parentID, 'name' => 'parentID', 'readonly' => $readonly, 'allselectable' => TRUE), TRUE).'</div>';
        $output .= text_item('name', 'Name', $name, TRUE, $readonly);
        // $output .= $this->load->view('text_item_view', array('name' => 'name', 'label' => 'Name', 'required' => TRUE, 'value' => $name), TRUE);
        
        if (!$readonly)
            $output .= form_submit('submit', 'Save');
            
        $output .= form_close();
     
        $data = array('title' => $mode.' Category', 'content' => $output, 'back' => 'categories/index/'.$parentID);
        $this->load->view('content_view', $data);
    }
    
    /**
     * Cat Form Submission Handler
     *
     * @access	private
     * @param	int
     * @param   int
     */
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
    
    /**
     * Cat Reordering AJAX handler
     *
     * @access	public
     * @param	int
     */
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
    
    /**
     * Cat Delete Callback
     *
     * @access	public
     * @param	int
     */
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
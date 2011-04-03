<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');
/**
 * iMenus Items Controller
 *
 * @package		iMenus
 * @category	Controllers
 * @author		Patrick
 */

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

    /**
     * Listing Page
     *
     * Lists Items for a specified Category  
     *
     * @access	public
     * @param	int   catID
     * @param   int   page
     */
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
        
        $this->table->set_template(array('table_open' => '<table id="order" border="0" cellpadding="4" cellspacing="0">'));
        $this->table->set_heading('Pic', 'Name', 'Price', 'Edit', 'Delete');
        
        foreach ($this->Items_model->getAll($catID) as $item) {
            $type = $item['Type'];
            $typeName = ($type == ITEMS_TYPE_MEAL ? 'Meal' : 'Item');
            $prompt = ($type == ITEMS_TYPE_MEAL ? 'Are you sure you want to delete this Set Meal? Don\'t worry, it will not delete the items.' :
                                                    'Are you sure you want to delete this Item? It\'ll be removed from any Set Meals that include it.');
            $this->table->add_row($item['ImageSmall'] != '' ? img($item['ImageSmall']) : '', anchor('items/viewitem/'.$item['ID'], $item['Name']), $item['Price'], anchor('items/edititem/'.$item['ID'], 'Edit '.$typeName), anchor('items/deleteitem/'.$item['ID'], 'Delete '.$typeName, array('class' => 'modalconfirm', 'data-modaltext' => $prompt)));
        }
        
        $data = array('title' => 'Items', 'content' => $note.$this->table->generate(), 'back' => 'categories/index/'.$parentCat, 'include_scripts' => array(site_url('../scripts/jquery.tablednd_0_5.js'), site_url('../scripts/reorder.js')));
        $data['document_ready'] = 'handleReOrder("order", "'.site_url('items/reorder/'.$catID).'");';
        $this->load->view('content_view', $data);
	}
    
    /**
     * Add Item page / form  
     *
     * @access	public
     * @param	int   catID
     * @param   int   Type (ITEMS_TYPE_ITEM or ITEMS_TYPE_MEAL)
     */
    public function additem($catID, $itemType = ITEMS_TYPE_ITEM) {
        if ($this->input->post())
            $this->_handlesubmit($catID, $itemType, TRUE);
        else
            $this->_details(NULL, FALSE, $itemType, $catID);
    }
    
    /**
     * Edit Item page / form  
     *
     * @access	public
     * @param	int   itemID
     */
    public function edititem($itemID) {
        if ($this->input->post())
            $this->_handlesubmit(NULL, NULL, FALSE, $itemID);
        else
            $this->_details($itemID, FALSE);
    }
    
    /**
     * View Item page / form  
     *
     * @access	public
     * @param	int   itemID
     */
    public function viewitem($itemID) {
        $this->_details($itemID, TRUE);
    }
    
    /**
     * Cat Form Generator
     *
     * @access	private
     * @param	int     catID
     * @param   int     Type (ITEMS_TYPE_ITEM or ITEMS_TYPE_MEAL)
     * @param   boolean Insert Mode
     * @param   int     ItemID
     */
    private function _handlesubmit($catID, $itemType, $insert, $itemID = NULL) {
        $this->load->model('Items_model');
        
        if (isset($itemID)) {
            $item = $this->Items_model->getItem($itemID);
            $catID = $item['CategoryID']; 
        }
        
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
        
        
        if ($insert) {
            $itemID = $this->Items_model->addItem($catID, $this->input->post('name'), $this->input->post('longdesc'), $this->input->post('shortdesc'), $this->input->post('price'), isset($_POST['items']) ? ITEMS_TYPE_MEAL : ITEMS_TYPE_ITEM, '', '', '', $this->input->post('items'));
            
        } else {
            $catID = $this->input->post('catID');
            $this->Items_model->updateItem($itemID, $catID, $this->input->post('name'), $this->input->post('longdesc'), $this->input->post('shortdesc'), $this->input->post('price'), isset($_POST['items']) ? ITEMS_TYPE_MEAL : ITEMS_TYPE_ITEM, NULL, NULL, NULL, $this->input->post('items'));
        }   
        
        $n = rand(10e16, 10e20);
		$file_name =  base_convert($n, 10, 36);
		
		//upload configuration
		$config['file_name'] = $file_name;
		$config['upload_path'] = BASEPATH.'../uploads/raw/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '1024';	//Max 1MB
		$config['max_width']  = '1024';
		$config['max_height']  = '768';
		
		$this->load->library('upload', $config);
        $this->load->model('image');

		if ($this->upload->do_upload(ITEM_IMAGE_SMALL))
		{
			
    		$file_data = $this->upload->data();
    		$full_path = $file_data['full_path'];
    		$file_path = $file_data['file_path'];
    		$raw_name = $file_data['raw_name'];
    		$file_ext = $file_data['file_ext'];
    		
    		$this->Items_model->updateItemImage($itemID, ITEM_IMAGE_SMALL, $this->image->small($file_path, $raw_name, $file_ext));
        }
        
        if ($this->upload->do_upload(ITEM_IMAGE_MEDIUM))
		{
			
    		$file_data = $this->upload->data();
    		$full_path = $file_data['full_path'];
    		$file_path = $file_data['file_path'];
    		$raw_name = $file_data['raw_name'];
    		$file_ext = $file_data['file_ext'];
    		
    		$this->Items_model->updateItemImage($itemID, ITEM_IMAGE_MEDIUM, $this->image->medium($file_path, $raw_name, $file_ext));
        }
        
        if ($this->upload->do_upload(ITEM_IMAGE_LARGE))
		{
    		$file_data = $this->upload->data();
    		$full_path = $file_data['full_path'];
    		$file_path = $file_data['file_path'];
    		$raw_name = $file_data['raw_name'];
    		$file_ext = $file_data['file_ext'];
    		
    		$this->Items_model->updateItemImage($itemID, ITEM_IMAGE_LARGE, $this->image->large($file_path, $raw_name, $file_ext));
        }
        
        redirect ('items/view/'.$catID);
    }
    
    /**
     * Cat Form Generator
     *
     * @access	private
     * @param	int       itemID
     * @param   boolean   readonly
     * @param   int       itemType
     * @param   int       catID
     */
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

        
        if (isset($items['ImageMedium']))
            $output .= img(array('src' => $item['ImageMedium'], 'class' => 'zooming'));
        if (isset($items['ImageLarge']))
            $output .= img(array('src' => $item['ImageLarge'], 'class' => 'zooming'));
        
        $output .= form_open_multipart('');
        
        $this->load->model('Categories_model');
        
        $readonly_text = $readonly ? 'readonly="readonly"' : '';
        
        $output .= '<div class="form-item"><label for="edit-catID">Category: <span class="form-required" title="This field is required">*</span></label>'.$this->load->view('tree_select_view', array('tree' => $this->Categories_model->getTreeFromCurrentMenu($catID), 'selected' => $catID, 'name' => 'catID', 'readonly' => $readonly), TRUE).'</div>';
        
        $output .= $this->load->view('text_item_view', array('name' => 'name', 'label' => 'Name', 'required' => TRUE, 'readonly' => $readonly, 'value' => isset($item['Name']) ? $item['Name'] : ''), TRUE);
        $output .= $this->load->view('text_item_view', array('name' => 'shortdesc', 'label' => 'Short Description', 'required' => FALSE, 'value' => isset($item['ShortDescription']) ? $item['ShortDescription'] : ''), TRUE);
        
        $output .= '<div class="form-item"><label for="edit-longdesc">Long Description: <span class="form-required" title="This field is required">*</span></label>'.form_textarea('longdesc', isset($item['LongDescription']) ? $item['LongDescription'] : '', 'id="edit-longdesc"'.$readonly_text).'</div>';
        
        $output .= '<div class="form-item"><label for="edit-price">Price: <span class="form-required" title="This field is required">*</span></label>$ '.form_input('price', isset($item['Price']) ? $item['Price'] : '', 'id="edit-price"'.$readonly_text).'</div>';
        
        $output .= '<div class="form-item"><label for="edit-imageSmall">Small Image:</label>'.($readonly ? '' : form_upload('imageSmall', '', 'id="edit-imageSmall"')).(!empty($item['ImageSmall']) ? img(array('src' => $item['ImageSmall'], 'class' => 'zooming')) : '').'</div>';
        $output .= '<div class="form-item"><label for="edit-imageMedium">Medium Image:</label>'.($readonly ? '' : form_upload('imageMedium', '', 'id="edit-imageMedium"')).(!empty($item['ImageMedium']) ? img(array('src' => $item['ImageMedium'], 'class' => 'zooming')) : '').'</div>';
        $output .= '<div class="form-item"><label for="edit-imageLarge">Large Image:</label>'.($readonly ? '' : form_upload('imageLarge', '', 'id="edit-imageLarge"')).(!empty($item['ImageLarge']) ? img(array('src' => $item['ImageLarge'], 'class' => 'zooming')) : '').'</div>';
    
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
    
    /**
     * Item Reordering AJAX handler
     *
     * @access	public
     * @param	int
     */
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
    
    /**
     * Item Delete Callback
     *
     * @access	public
     * @param	int
     */
    public function deleteitem($itemID) {
        $this->load->model('Items_model');
        
        $item = $this->Items_model->getItem($itemID);
        $catID = $item['CategoryID'];
        
        $this->_checkAccess($catID);
        
        $this->Items_model->removeItem($itemID);
        
        redirect('items/view/'.$catID);
    }
}
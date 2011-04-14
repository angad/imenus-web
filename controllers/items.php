<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');
/**
 * iMenus Items Controller
 *
 * @package		iMenus
 * @category	Controllers
 * @author		Patrick
 */

define('PAGESIZE_ITEMS', 10);
define('ITEMLIST_SIZE', 10);


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
        $note .= anchor('items/additem/'.$catID.'/'.ITEMS_TYPE_ITEM, 'Add Item').' / '.anchor('items/additem/'.$catID.'/'.ITEMS_TYPE_MEAL, 'Add Meal');
        
        $this->table->set_template(array('table_open' => '<table id="order" border="0" cellpadding="4" cellspacing="0">'));
        // $this->table->set_heading('Pic', 'Name', 'Price', 'Edit', 'Delete');
        $this->table->set_heading('Pic', 'Item', 'Price', 'Delete');
        
        foreach ($this->Items_model->getAll($catID) as $item) {
            $type = $item['Type'];
            $typeName = ($type == ITEMS_TYPE_MEAL ? 'Meal' : 'Item');
            $prompt = ($type == ITEMS_TYPE_MEAL ? 'Are you sure you want to delete this Set Meal? Don\'t worry, it will not delete the items.' :
                                                    'Are you sure you want to delete this Item? It\'ll be removed from any Set Meals that include it.');
            $this->table->add_row($item['ImageSmall'] != '' ? img($item['ImageSmall']) : '', anchor('items/edititem/'.$item['ID'], htmlspecialchars($item['Name'])), sprintf('$%01.2f', $item['Price']), anchor('items/deleteitem/'.$item['ID'], 'Delete '.$typeName, array('class' => 'modalconfirm', 'data-modaltext' => $prompt)));
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
    
    public function time_check($str) {
        if (strtotime($str, 0) === FALSE) {
            $this->form_validation->set_message('time_check', 'The %s field is not a valid time');
            return FALSE;
        }
        return TRUE;
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
        else
            $this->form_validation->set_rules('dur', 'Preparation Time', 'callback_time_check');
        
        if (!$this->form_validation->run())
            if ($insert)
                return $this->_details(NULL, FALSE, $itemType, $catID);
            else
                return $this->_details($itemID, FALSE);
        
        
        if ($insert) {
            $itemID = $this->Items_model->addItem($catID, $this->input->post('name'), $this->input->post('longdesc'), $this->input->post('shortdesc'), $this->input->post('price'), strtotime($this->input->post('dur'), 0), isset($_POST['items']) ? ITEMS_TYPE_MEAL : ITEMS_TYPE_ITEM, '', '', '', $this->input->post('items'), $this->input->post('features'));
            
        } else {
            $catID = $this->input->post('catID');
            $this->Items_model->updateItem($itemID, $catID, $this->input->post('name'), $this->input->post('longdesc'), $this->input->post('shortdesc'), $this->input->post('price'), strtotime($this->input->post('dur'), 0), isset($_POST['items']) ? ITEMS_TYPE_MEAL : ITEMS_TYPE_ITEM, NULL, NULL, NULL, $this->input->post('items'), $this->input->post('features'));
        }   
        
        $n = rand(10e16, 10e20);
		$file_name =  base_convert($n, 10, 36);

		//upload configuration
		$config['file_name'] = $file_name;
		$config['upload_path'] = BASEPATH.'../uploads/raw/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '1024';	//Max 1MB
		$config['max_width']  = '500';
		$config['max_height']  = '375';

		$this->load->library('upload', $config);
        $this->load->model('image');
        
        if ($this->upload->do_upload(ITEM_IMAGE_LARGE))
		{
    		$file_data = $this->upload->data();
			$this->image_resize($file_data);
        }

		if ($this->upload->do_upload(ITEM_IMAGE_SMALL))
		{
			$file_data = $this->upload->data();
    		
    		$full_path = $file_data['full_path'];
	   		$file_path = $file_data['file_path'];
	   		$raw_name = $file_data['raw_name'];
	   		$file_ext = $file_data['file_ext'];
	
			$path_small = $this->image->small($file_path, $raw_name, $file_ext);
			$this->Items_model->updateItemImage($itemID, ITEM_IMAGE_SMALL, $path_small);
        }
		
        redirect ('items/view/'.$catID);
    }
    
	function image_resize($file_data)
	{
		$full_path = $file_data['full_path'];
   		$file_path = $file_data['file_path'];
   		$raw_name = $file_data['raw_name'];
   		$file_ext = $file_data['file_ext'];
    		
		$path_large = $this->image->large($file_path, $raw_name, $file_ext);
		$this->Items_model->updateItemImage($itemID, ITEM_IMAGE_LARGE, $path_large);

		$path_medium = $this->image->medium($file_path, $raw_name, $file_ext);			
		$this->Items_model->updateItemImage($itemID, ITEM_IMAGE_MEDIUM, $path_medium);

		$path_small = $this->image->small($file_path, $raw_name, $file_ext);			
		$this->Items_model->updateItemImage($itemID, ITEM_IMAGE_SMALL, $path_small);
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
        $this->load->model('Features_model');
        $item = $this->Items_model->getItem($itemID);
        $this->load->helper(array('url', 'html', 'form', 'form_items'));
        
        $mode = isset($catID) ? 'Add' : ($readonly ? 'View' : 'Edit');
        
        if (!empty($item['CategoryID'])) { 
            $catID = $item['CategoryID'];
            $itemType = $item['Type'];
        }
        
        $this->_checkAccess($catID);
        
        $data['document_ready'] = '';
        
        $output = '';

        
        if (isset($items['ImageMedium']))
            $output .= img(array('src' => $item['ImageMedium'], 'class' => 'zooming'));
        if (isset($items['ImageLarge']))
            $output .= img(array('src' => $item['ImageLarge'], 'class' => 'zooming'));
        
        $output .= form_open_multipart('');
        
        $this->load->model('Categories_model');
        
        $readonly_text = $readonly ? 'readonly="readonly"' : '';
        
        $output .= tree_select_item('catID', 'Category', $this->Categories_model->getTreeFromCurrentMenu($catID), $catID, TRUE, $readonly);
        
        $output .= text_item('name', 'Name', isset($item['Name']) ? $item['Name'] : '', TRUE, $readonly);
        $output .= text_item('shortdesc', 'Short Description', isset($item['ShortDescription']) ? $item['ShortDescription'] : '', FALSE, $readonly);
        
        $output .= textarea_item('longdesc', 'Long Description', isset($item['LongDescription']) ? $item['LongDescription'] : '', TRUE, $readonly);
        
        $output .= text_item('price', 'Price', isset($item['Price']) ? $item['Price'] : '', TRUE, $readonly, '$ ');
        
        if (!isset($itemType) || $itemType == ITEMS_TYPE_ITEM) {
            $output .= text_item('dur', 'Preparation Time', isset($item['Duration']) ? date('H:i:s', $item['Duration']) : '00:00:00', true, $readonly);
            $data['include_scripts'][] = site_url('../scripts/anytimec.js');
            $data['include_css'][] = site_url('../anytimec.css');
            $data['document_ready'] .= 'AnyTime.picker("edit-dur", { format: "%H:%i:%s" });';
        }
        
		$output .= '<div id = "fileupload" class="form-item"><label for="edit-imageSmall">Small Image(Max 200x150):</label>'.($readonly ? '' : form_upload('imageSmall', '', 'id="edit-imageSmall"')).(!empty($item['ImageSmall']) ? img(array('src' => $item['ImageSmall'], 'class' => 'zooming')) : '').'</div>';

        $output .= '<div id = "fileupload" class="form-item"><label for="edit-imageLarge">Large Image(Max 500x375):</label>'.($readonly ? '' : form_upload('imageLarge', '', 'id="edit-imageLarge"')).(!empty($item['ImageLarge']) ? img(array('src' => $item['ImageLarge'], 'class' => 'zooming')) : '').'</div>';
    
        $seljs = '[]';
        if (isset($itemType) && $itemType == ITEMS_TYPE_MEAL) {
            $sel = isset($itemID) ? $this->Items_model->getMealItems($itemID, TRUE) : array();
            $seljs = '';
            foreach ($sel as $selItem)
                $seljs .= ',['.$selItem['ItemID'].','.$selItem['ItemQuantity'].']';
            $seljs = '['.substr($seljs, 1).']';
            $output .= tree_select_item('itemSelect', 'Meal Items', $this->Categories_model->getTreeFromCurrentMenu($catID, TRUE), NULL, TRUE, $readonly, FALSE, ITEMS_TYPE_ITEM, 'size="'.ITEMLIST_SIZE.'"');
            $output .= '<table id="itemListTable"><tbody></tbody></table>';
        }
        
        $allFeatures = $this->Features_model->getFeaturesFromMenu($this->User_model->getMenuId());
        
        if (!empty($allFeatures)) {
            $feat = isset($itemID) ? $this->Items_model->getItemFeatures($itemID, TRUE) : array();
            $featjs = '';
            $featRangesJS = 'var featRanges = [];';
            $featVals = array('' => 'Add Feature');
            
            foreach ($allFeatures as $feature) {
                $featVals[$feature['ID']] = $feature['Name'];
                $featRangesJS .= 'featRanges['.$feature['ID'].'] = '.($feature['Type'] == FEATURES_TYPE_NUMERIC ? $feature['MaxValue'] : '"'.addslashes($feature['StringValues']).'"').';';
            }
            
            foreach ($feat as $featItem)
                $featjs .= ',['.$featItem['FeatureID'].','.$featItem['Value'].']';
            $featjs = '['.substr($featjs, 1).']';
            $output .= select_item('featSelect', 'Features', $featVals, NULL, FALSE, $readonly);
            $output .= '<table id="featListTable"><tbody></tbody></table>';
        }
        
        
        if (!$readonly)
            $output .= form_submit('submit', 'Save');
            
        $output .= form_close();
     
        $data['title'] = $mode.' Item';
        $data['content'] = $output;
        $data['back'] = 'items/view/'.$catID;
        
        if (!empty($allFeatures)) {
            $data['include_scripts'][] = site_url('../scripts/features.js');
            $data['document_ready'] .= $featRangesJS.'handleFeatures("edit-featSelect", "featListTable", '.$featjs.', featRanges);';
        }
        
        if ($itemType == ITEMS_TYPE_MEAL) {
            $data['include_scripts'][] = site_url('../scripts/setmeal.js');
            $data['document_ready'] .= 'handleSetMeal("edit-itemSelect", "itemListTable", '.$seljs.');';
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
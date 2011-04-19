<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');
/**
 * iMenus Categories Controller
 *
 * @package		iMenus
 * @category	Controllers
 * @author		Patrick
 */

define('CATPREFIX', 'cat');
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
        
        $breadcrumbs = '';
        
        if ($parentID != 0) {
            $ancestorDet = $det = $this->Categories_model->getCat($parentID, TRUE);
            $ancestor = $det['parentID'];
            if ($det['ItemCount'] + $det['MealCount'] > 0)
                redirect('categories/index/'.$ancestor);
            $title = 'Sub-Categories of '.htmlspecialchars($det['Name']);
            
            while ($ancestor != 0) {
                $ancestorDet = $this->Categories_model->getCat($ancestor);
                $breadcrumbs = anchor('categories/index/'.$ancestor, htmlspecialchars($ancestorDet['Name'])).($breadcrumbs == '' ? '' : ' &gt; '.$breadcrumbs);
                $ancestor = $ancestorDet['parentID'];
            }
            
            $breadcrumbs = 'Parent Categories: '.anchor('categories', ROOT_CATEGORY).($breadcrumbs == '' ? '' : ' &gt; '.$breadcrumbs).'<br />';
        } else $title = 'Categories';
        
        $note = $breadcrumbs.'<h3 class="note">A Category can have Sub-Categories or Items, but not both.<br />You may sort the Categories by dragging them around.</h3>';
        $note .= anchor('categories/add/'.$parentID, 'Add Sub-Category');
        
        $this->table->set_template(array('table_open' => '<table id="order" border="0" cellpadding="4" cellspacing="0">'));
        // $this->table->set_heading('Category', 'Edit', 'Sub-Categories', 'Items', 'Delete');
        $this->table->set_heading('Category', 'Sub-Categories', 'Items', 'Delete');
        
        foreach ($this->Categories_model->getAll($menuID, TRUE, $parentID) as $cat) {
            if ($cat['ItemCount'] + $cat['MealCount'] != 0)
                $prompt = sprintf(CATDELPROMPTI, $cat['ItemCount'], $cat['MealCount']);
            else if ($cat['SubcatCount'] != 0)
                $prompt = sprintf(CATDELPROMPTS, $cat['SubcatCount']);
            else
                $prompt = CATDELPROMPT;
            $this->table->add_row(anchor('categories/edit/'.$cat['ID'], htmlspecialchars($cat['Name'])), $cat['ItemCount'] + $cat['MealCount'] ? '' : anchor('categories/index/'.$cat['ID'], 'View Sub-Categories'), $cat['SubcatCount'] ? '' : anchor('items/view/'.$cat['ID'], 'View Items'), anchor('categories/delete/'.$cat['ID'], 'Delete Category', array('class' => 'modalconfirm', 'data-modaltext' => $prompt)));
        }
        $data = array('title' => $title, 'content' => $note.$this->table->generate(), 'include_scripts' => array(site_url('../scripts/jquery.tablednd_0_5.js'), site_url('../scripts/reorder.js')));
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
        $this->load->model('TSV_model');
        $this->load->model('Categories_model');
        
        $this->load->helper(array('url', 'form', 'html', 'form_items'));
        
        $mode = isset($parentID) ? 'Add' : ($readonly ? 'View' : 'Edit');
        
        if (isset($parentID) && $parentID != 0) {
            $det = $this->Categories_model->getCat($parentID, TRUE);
            if ($det['ItemCount'] + $det['MealCount'] > 0)
                redirect('categories/index/'.$parentID);
        }
        
        $name = '';
        
        if (isset($catID) && $cat = $this->Categories_model->getCat($catID)) {
            if (!empty($cat)) {
                $parentID = $cat['parentID'];
                $name = $cat['Name'];
            }
        }
        
        $this->_checkAccess(isset($catID) ? $catID : $parentID);
       
        $output = form_open_multipart('');
        
        $readonly_text = $readonly ? 'readonly="readonly"' : '';
        
        $output .= tree_select_item('parentID', 'Parent Category', $this->Categories_model->getTreeFromMenu($menuID = $this->User_model->getMenuId(), FALSE, isset($catID) ? $catID : NULL, TRUE), $parentID, TRUE, $readonly, TRUE);
        $output .= text_item('name', 'Name', $name, TRUE, $readonly);
        
        $TVOptions = $this->TSV_model->getThemeValueOptions($menuID, TSV_TYPE_CATEGORY);
        $TVDetails = $this->TSV_model->getThemeValueDetails($menuID, TSV_TYPE_CATEGORY);
        if ($TVOptions)
            $output .= select_item('TSV1', THEMEVALUE_LABEL_PREFIX.': '.$TVDetails['Label'], $TVOptions, isset($cat['TSV1']) ? $cat['TSV1'] : $TVDetails['Default'], TRUE, $readonly);
        
        $output .= '<div class="fileupload form-item"><label for="edit-catImage">Category Icon (Recommended 200x150):</label>'.($readonly ? '' : form_upload('catImage', '', 'id="edit-catImage"')).(!empty($cat['Image']) ? img(array('src' => $cat['Image'], 'class' => 'zooming')) : '').'</div>';
        
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
        
        if (isset($_POST['TSV1'])) {
            $this->form_validation->set_rules('TSV1', THEMEVALUE_LABEL_PREFIX, 'required');
            $TSV1 = $this->input->post('TSV1');
        } else $TSV1 = NULL;
        
        $insert = isset($parentID);
        
        if (!$this->form_validation->run())
            if ($insert)
                return $this->_details(NULL, FALSE, $parentID);
            else
                return $this->_details($catID, FALSE);
        
        $this->load->model('Categories_model');
        
        $parentID = $this->input->post('parentID');
        
        if ($parentID != 0) {
            $parentDet = $this->Categories_model->getCat($parentID, TRUE);
            if ($parentDet['ItemCount'] + $parentDet['MealCount'] > 0)
                redirect('categories/index/'.$parentID);
        }
        
        if ($insert)
            $catID = $this->Categories_model->add($this->User_model->getMenuId(), $this->input->post('name'), $parentID, $TSV1);
        else {
            if ($catID == $parentID)
                redirect('categories/index/'.$parentID);
            $this->Categories_model->update($catID, $this->input->post('name'), $parentID, $TSV1);
        }
        
        $n = rand(10e16, 10e20);
		$file_name =  base_convert($n, 10, 36);

		//upload configuration
		$config['file_name'] = $file_name;
		$config['upload_path'] = BASEPATH.'../uploads/raw/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '1024';	//Max 1MB
		$config['max_width']  = '800';
		$config['max_height']  = '600';

		$this->load->library('upload', $config);
        $this->load->model('image');
        
		if ($this->upload->do_upload('catImage'))
		{
            $file_data = $this->upload->data();
            
            $full_path = $file_data['full_path'];
	   		$file_path = $file_data['file_path'];
	   		$raw_name = $file_data['raw_name'];
	   		$file_ext = $file_data['file_ext'];
	
			$path_small = $this->image->small($file_path, $raw_name, $file_ext);
			$this->Categories_model->updateCategoryImage($catID, $path_small);

          
	   }
        
         redirect ('categories/index/'.$parentID);
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
        
 		if (($menuID = $this->User_model->getMenuId()) === FALSE || ($parentID != 0 && !in_array($parentID, $this->Categories_model->getAllIDs($menuID))) || !is_array($cats = $this->input->post('order')))
            return;
            
        $i = 1;
        
        $cats = array_filter($cats, 'is_numeric');
        
        // check if they all belong to the specified parent 
        $check_SQL = 'SELECT MAX(NoMatch) FROM (SELECT ISNULL(C.ID) AS NoMatch FROM '.CATEGORIES_TABLE.' C RIGHT JOIN (SELECT '.implode(' AS ID UNION SELECT ', $cats).') X ON X.ID = C.ID AND C.ParentID = '.$parentID.') Z';
        if (current($this->db->query($check_SQL)->row_array()))
            return;
        
        foreach ($cats as $cat)
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
            show_error(ACCESS_DENIED_MSG, 403, ACCESS_DENIED);
        
        $cat = $this->Categories_model->getCat($catID);
        $this->Categories_model->remove($catID);
        
        redirect('categories/index/'.$cat['parentID']);
    }
 
 }
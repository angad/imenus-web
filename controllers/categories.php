<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

define('CATPREFIX', 'cat');
define('PLACEHOLDER', 'Add New Category');
define('EDITTITLE', 'Click to Edit');

 class Categories extends CI_Controller {
 
 	public function view($menuID = NULL)
 	{     
 		if (!is_numeric($menuID))
            show_404('', FALSE);
        
        $this->load->library('table');
        $this->load->helper(array('form', 'url'));
        $this->load->model('Categories_model');
        
        
        $this->table->set_heading('Name', 'Items', 'Delete');
        $this->table->set_template(array('row_start' => '<tr class="odd">', 'row_alt_start' => '<tr class = "even">'));
        
        $this->table->add_row(array('data' => form_open('categories/add/'.$menuID).form_input('value', '', 'placeholder = "'.PLACEHOLDER.'"').form_close(), 'colspan' => 3));
        
        foreach ($this->Categories_model->getAll($menuID) as $cat) {
            $this->table->add_row('<div class="edit" id="'.CATPREFIX.$cat['ID'].'" title="'.EDITTITLE.'">'.$cat['Name'].'</div>', anchor('items/view/'.$cat['ID'], 'View Items'), anchor('categories/delete/'.$cat['ID'], 'Delete Category'));
        }
        $this->load->view('content_view', array('title' => 'Categories', 'content' => $this->table->generate(), 'editable_uri' => $this->config->site_url().'/categories/rename/'.$menuID));
 	}
    
    public function rename($menuID) {
        $this->load->model('Categories_model');
        if (($id = $this->input->post('id')) && ($value = $this->input->post('value'))) {
            $this->Categories_model->rename(substr($id, strlen(CATPREFIX)), $value);
            echo $value;
        }
    }
    
    public function add($menuID) {
        $this->load->model('Categories_model');
        $this->load->helper('url');
        if ($value = $this->input->post('value')) {
            $this->Categories_model->add($menuID, $value);
            redirect('categories/view/'.$menuID);
        }
    }
 
 }
<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * This is the controller class for Manage Tenants 
 */
class Metadata extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('manage_tenant_model', 'manage_tenant');
        $this->load->model('meta_values', 'meta');
        $this->meta_map = $this->meta->get_param_map();
        $this->view_folder = 'tenant/';
        $this->controller_name = 'metadata/';
    }
    /**
     * This function to add, view and delete meta data
     */
    public function index() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $input = array();
            $cat = $this->input->post('cat');
            $sub_cat = $this->input->post('sub_cat');
            $input['category_name'] = $this->input->post('cat_name');
            $input['description'] = $this->input->post('desc');
            $parameter_id = $this->input->post('param');
            $input['parameter_id'] = empty($parameter_id) ? NULL : strtoupper($parameter_id);
            $child_cat = $this->input->post('child_cat');
            if ($child_cat) {
                $input['child_category']['category_name'] = $this->input->post('child_cat_name');
                $input['child_category']['description'] = $this->input->post('child_desc');
                $input['child_category']['parameter_id'] = strtoupper($this->input->post('child_param'));
            }
            $child_cat = empty($child_cat) ? 0 : $child_cat;
            $result = $this->manage_tenant->create_metadata($input, $child_cat, $cat, $sub_cat);
            if ($result == TRUE) {
                $this->session->set_flashdata("success", "Metadata created successfully.");
            } else {
                $this->session->set_flashdata("error", "Unable to create metadata. Please try again later.");
            }
            $extra = '';
            if (!empty($cat)) {
                $extra .= '?cat=' . $cat;
                if (!empty($sub_cat)) {
                    $extra .= '&sub_cat=' . $sub_cat;
                }
            }
            redirect($this->controller_name . $extra);
        }
        $data['controllerurl'] = $this->controller_name;
        $data['meta_map'] = $this->meta_map;
        $data['categories'] = $this->manage_tenant->get_meta_categories()->result();
        $cat = $this->input->get('cat');
        $sub_cat = $this->input->get('sub_cat');
        if ($cat) {
            $data['tabledata'] = $this->manage_tenant->get_meta_categories($cat)->result();
        }
        if (!empty($sub_cat)) {
            $data['subcategories'] = $data['tabledata'];
            $data['tabledata'] = $this->manage_tenant->get_meta_categories($sub_cat)->result();
        }
        $data['page_title'] = 'Metadata';
        $data['main_content'] = $this->view_folder . 'viewmeta';
        $this->load->view('layout', $data);
    }
    /**
     * update metadata
     */
    public function update_meta() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $input = array();
            $where = array();
            $cat = $this->input->post('cat_id');
            $sub_cat = $this->input->post('subcat_id');
            $input['category_name'] = $this->input->post('category_name');
            $input['description'] = $this->input->post('description');
            $where['parameter_id'] = $this->input->post('parameter_id');
            $where['category_id'] = $this->input->post('category_id');
            $result = $this->manage_tenant->update_metadata($input, $where);
            if ($result == TRUE) {
                $this->session->set_flashdata("success", "Metadata updated successfully.");
            } else {
                $this->session->set_flashdata("error", "Unable to update metadata. Please try again later.");
            }
            $extra = '';
            if (!empty($cat)) {
                $extra .= '?cat=' . $cat;
                if (!empty($sub_cat)) {
                    $extra .= '&sub_cat=' . $sub_cat;
                }
            }
            redirect($this->controller_name . $extra);
        }
    }
    /**
     * unique param id check for metadata
     */
    public function unique_param_check() {
        $param = $this->input->post('param');
        $cat = $this->input->post('cat');
        $sub_cat = $this->input->post('sub_cat');
        $category = (empty($sub_cat)) ? $cat : $sub_cat;
        $rows = $this->manage_tenant->unique_param_check($category, $param)->num_rows();
        echo $rows;
        exit();
    }

}

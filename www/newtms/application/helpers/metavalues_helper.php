<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * fetch the metavalues
 * @param varchar $category_id
 * @return array metavalues
 */
function fetch_metavalues_by_category_id($category_id = '') {

    $ci = & get_instance();
    $ci->load->model('meta_values');
    static $category_list = array();
    static $i = -1;
    $i = $i + 1;
    $cat_id = $ci->meta_values->fetch_child_category_by_category_id($category_id);
    $category_list[$i] = $cat_id;
    if ($cat_id && $i <= 0) {

        $category_list[$i] = fetch_metavalues_by_category_id($cat_id);
    } else {
        
    }
    if (empty($category_list)) {
        $data = $ci->meta_values->get_metavalues(array(), $category_id);
    }
    array_pop($category_list);

    $data = $ci->meta_values->get_metavalues($category_list, $category_id);
    $i = -1;
    unset($category_list);
    return $data;
}
function get_param_value($param_id){
    $category_name = '';
    $param_id = trim($param_id);
    if(! empty($param_id)) {
        $ci = & get_instance();    
        $category_name = $ci->db->select('category_name')->where('parameter_id',$param_id)->get('metadata_values')->row();
    }
    return $category_name;
}
/**
 * get meta values names from comma-separated list of ids
 */
function get_param_values_from_map ($meta_map, $meta_ids){
    $arr = explode(",",$meta_ids);
    $names = array();
    foreach ($arr as $el) {
        $name = $meta_map[trim($el)];
        if (!empty($name))
            $names[] = $name;
    }
    return implode(", ", $names);
}
/*
 * fetch all the metavalues 
 * 
 */
function fetch_all_metavalues() {
    $ci = & get_instance();
    $ci->load->model('meta_values');
    $result = $ci->meta_values->fetch_metadata_categories();
    return $result;
}
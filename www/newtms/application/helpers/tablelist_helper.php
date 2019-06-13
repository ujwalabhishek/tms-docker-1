<?php  
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
  * This is the common helper class for converting a simple query to codeigniter active record
  */
function createtable($tablename, $heading, $controllerurl='', $sort_by, $sort_order) {
            $data = array();
            $url = base_url();
            $tmpl = array(
                'table_open' => '<table id="listview" class="table table-striped" cellpadding="4" cellspacing="0">',
                'heading_row_start' => '<tr>',
                'heading_row_end' => '</tr>',
                'heading_cell_start' => '<th class="th_header" width="13%">',
                'heading_cell_end' => '</th>',
                'row_start' => '<tr>',
                'row_end' => '</tr>',
                'cell_start' => '<td>',
                'cell_end' => '</td>',
                'row_alt_start' => '<tr>',
                'row_alt_end' => '</tr>',
                'cell_alt_start' => '<td>',
                'cell_alt_end' => '</td>',
                'table_close' => '</table>'
            );

            $ci =& get_instance();
            $ci->load->library('table');
            $ci->load->library('pagination');
            
            $per_page = PER_PAGE;
            $offsetval = PER_PAGE;
            
            $pageno = $ci->uri->segment(3);
            if ($pageno != '') {
                $offsetval = $pageno * $per_page;
            }
            if ($pageno == '') {
                $pageno = 1;
            }
            $offset = $offsetval;  
            $ci->load->model('tablelist_model');
            $config['base_url'] = base_url() . 'index.php/'.$controllerurl.'/';
           $totalrows = $ci->tablelist_model->record_count($tablename);
            $details['list'] = $ci->tablelist_model->get_table($tablename, $per_page, $offset, $sort_by, $sort_order);
            $ci->load->helper('pagination');              
            $data['pagination'] =  prikazi($per_page,$pageno,$config['base_url'],$totalrows);      
            $ci->table->set_heading($heading);
            $ci->table->set_template($tmpl);            

            $ci->pagination->create_links();
            $data['table']  = $ci->table->generate($details['list'], $controllerurl, $sort_order);
            $data['tablename'] = $table;
            $data['controller'] = $controller;
            $data['heading'] = $heading;
            $data['orderparms'] = $orderparms;
             
        return $data;
    }
?>
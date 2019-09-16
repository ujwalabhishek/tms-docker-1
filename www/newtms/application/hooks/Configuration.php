<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of file
 *
 * @author Ujwal
 */
class ConfigurationClass extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function configurationfunctionaa() {
        $CI = & get_instance();
        $tenentName = "";
        $domain = str_replace("www.", "", $_SERVER ["HTTP_HOST"]);
        $exploded = explode('.', $domain);
        $mainDomain = str_replace($exploded[0], "", $_SERVER ["HTTP_HOST"]);
        $gd = count($exploded);
       
        $segmentCount = '2';
       
        // collect tenant name from url
        $tenentName =  $gd  > $segmentCount ? $exploded[0] : DEFAULT_TENANT;

        // fetch tenent id based on tenant name
        $CI->db->select('tenant_id');
        $CI->db->from('tenant_master');
        $CI->db->where('tenant_short_name', $tenentName);
        $CI->db->limit(1);
        $res = $CI->db->get()->row()->tenant_id;
        echo $CI->db->last_query();exit;
        // if the tenent name doesnot exist in db redirect to default tenant        
        if(empty($res))
           //redirect(DEFAULT_TENANT);
	  // redirect ("http://www.biipmi.com");
           redirect ("http://xprienzhr.com/");
        
        //rename cookie according to tenant so that session data are domain specific
        $CI->config->set_item('cookie_prefix', $res);
        
        // define tenant id 
        define('TENANT_ID', $res);
    }

}

/* End of file configuration.php */
/* Location: ./application/hooks/configuration.php */

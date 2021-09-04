<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
///// This File & The code inside Was Added by Shubhranshu to Connect Multiple DB.
function switch_db_dynamic($name_db)
{
    $config_app['dsn'] = '';
    $config_app['hostname'] = '172.18.0.3';
    $config_app['username'] = 'biipmico_tms_dev';
    $config_app['password'] = 'biipmi@#*123@biipbyte';
    $config_app['database'] = $name_db;
    $config_app['dbdriver'] = 'mysqli';
    $config_app['dbprefix'] = '';
    $config_app['pconnect'] = FALSE;
    $config_app['db_debug'] = (ENVIRONMENT !== 'production');
    $config_app['cache_on'] = FALSE;
    $config_app['cachedir'] = '';
    $config_app['char_set'] = 'utf8';
    $config_app['dbcollat'] = 'utf8_general_ci';
    $config_app['swap_pre'] = '';
    $config_app['encrypt'] = FALSE;
    $config_app['compress'] = FALSE;
    $config_app['stricton'] = FALSE;
    $config_app['failover'] = array();
    $config_app['save_queries'] = TRUE;
    
    return $config_app;
}

function fetch_dynamic_db_details(){
    $host=$_SERVER['HTTP_HOST'];
    if($host == 'xprienz.net'){
        $object = new stdClass();
        $object->tenant_db_name = 'biipmico_tms_masterdata';
        $object->tenant_db_user = 'biipmico_tms_dev';
        $object->tenant_db_password = 'biipmi@#*123@biipbyte';
        
        return $object;
    }
    $config_app = switch_db_dynamic('biipmico_tms_masterdata');
    $CI =& get_instance();
    //$CI->load->library('session');
    $CI->dbs = $CI->load->database($config_app,TRUE);
    //print_r($CI->dbs);exit;
    $CI->dbs->select('*');
        $CI->dbs->from('tenant_master');
        $CI->dbs->where('tenant_url',$host);
        $res = $CI->dbs->get()->row();
        //$CI->session->set_userdata('master_tenant_id', $res->tenant_id);
        define('TENANT_ID',  $res->tenant_id); //////very very imporatant line by shubhranshu
        define('TENANT_LOGO',  $res->tenant_logo);//////very very imporatant line by shubhranshu
        if(empty($res)){
            redirect('http://xprienz.net/'); // this if condition was added by shubhranshu check if the url is invalid redirect to home page
        }
        return $res;
        //print_r( $CI->dbs->get()->row());exit;
}




<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


function switch_db_dynamic($name_db)
{
    $config_app['dsn'] = '';
    $config_app['hostname'] = 'localhost';
    $config_app['username'] = 'root';
    $config_app['password'] = '';
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
        $object->tenant_db_user = 'root';
        $object->tenant_db_password = '';
        
        return $object;
    }
    $config_app = switch_db_dynamic('biipmico_tms_masterdata');
    $CI =& get_instance();
    $CI->dbs = $CI->load->database($config_app,TRUE);
    //print_r($CI->dbs);exit;
    $CI->dbs->select('*');
        $CI->dbs->from('tenant_master');
        $CI->dbs->where('tenant_url',$host);
        return $CI->dbs->get()->row();
        //print_r( $CI->dbs->get()->row());exit;
}

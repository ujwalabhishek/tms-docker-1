
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


function database_connection_check_url(){
    $host=$_SERVER['HTTP_HOST'];
    if(preg_match('/xprienz3/', $host)){
        $db_name=connect_primary('t01_t16_db');
    }else if(preg_match('/focus/', $host)){
        $db_name=connect_primary('t01_t04_db');
    }else if(preg_match('/carrie/', $host)){
        $db_name=connect_primary('t01_t03_db');
    }else if(preg_match('/xprienz2/', $host)){
        $db_name=connect_primary('t01_t12_db');
    }else{
        $db_name=connect_primary('t01_t02_db');
    }
    return $db_name;
}

function connect_primary($url_str){
   // $conn = new mysqli('localhost', 'root', '','db_parameters');
    //$sql = "SELECT db_name FROM db_parameters WHERE url_string='$url_str'";
    //$result = $conn->query($sql);
    //$row= $result->fetch_assoc();
    //mysqli_close($conn); 
    //return $row['db_name'];
    return $url_str;
}

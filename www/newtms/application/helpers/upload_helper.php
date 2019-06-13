<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * function to upload the image for the user
 * @param string $folder the folder name location
 * @param string $file_name the filename of an image
 * @return array image data
 */
function upload_image($folder, $file_name, $input_name = 'userfile') {   
    $CI = & get_instance();
    $config = array();
    $config['upload_path'] = "./$folder";
    $config['allowed_types'] = 'gif|jpg|png|jpeg';
    $config['file_name'] = $file_name . '_' . time();
    $config['overwrite'] = TRUE;
    $config['max_size'] = '3175'; 
  
     $CI->load->library('upload', $config);
     if (!$CI->upload->do_upload($input_name)) {        
        $data = array('error' => $CI->upload->display_errors(), 'status' => FALSE);
        return $data;
     } else {
         $image_data = $CI->upload->data();         
         if ($image_data['image_width'] > $image_data['image_height']) {
             $config['width'] = 150;
             $config['height'] = 160;
         }else {
             $config['height'] = 150;
             $config['width'] = 160;
         }
         $image_data['system_path'] = $folder;
         $config['image_library'] = 'gd2';
         $config['source_image'] = $image_data['full_path'];
         $config['create_thumb'] = TRUE;
         $config['maintain_ratio'] = TRUE;

         $CI->load->library('image_lib', $config);

         $CI->image_lib->resize();
         $data['status'] = TRUE;
         $data['image'] = $image_data;
         return $data;
     }
}
/**
 * function to upload the file
 * @param string $folder the folder name location
 * @param string $file_name the filename of an image
 * @return array file data
 */
function upload_file($folder, $file_name) {    
    $CI = & get_instance();
    $config = array();
    $config['upload_path'] = "./$folder";
    $config['allowed_types'] = 'zip|tar';
    $config['file_name'] = $file_name;
    $config['overwrite'] = TRUE;
    $config['max_size'] = '26915';
  
    $CI->load->library('upload', $config, 'zip_file_upload');
    $CI->zip_file_upload->initialize($config);
    if (!$CI->zip_file_upload->do_upload()) {
        $data = array('error' => $CI->zip_file_upload->display_errors(), 'status' => FALSE);
        return $data;
    } else {
         $data['file'] = $CI->zip_file_upload->data();
         $data['status'] = TRUE;
         return $data;
     }
}
/**
 * function to save the image path
 * @param int $uid unique user id
 * @param string $image_path thumbnail image path
 */
function save_image_path($uid, $image_path = NULL) {
    if (empty($uid)) {
        return;
    }
    $data = array(
        'photo_upload_path' => $image_path  
    );
    $CI =& get_instance();
    $CI->db->where('user_id', $uid);
    $CI->db->update('tms_users_pers', $data); 
}
/**
 * function to fetch the image path
 * @param int $uid unique user id
 * @return string image path
 */
function fetch_image_path_by_uid($uid = NULL) {
    if (empty($uid)) {
        return;
    }
    $CI =& get_instance();
    return $CI->db->select("photo_upload_path")->from("tms_users_pers")->
        where_in("user_id", $uid)->get()->row()->photo_upload_path;
}
/**
 * function to remove the image path
 * @param string $path relative image path
 */
function remove_previous_image($path = NULL) {
    if (empty($path)) {
        return;
    }
    $previous_original_path = str_ireplace("_thumb","",$path);
    unlink($path);
    unlink($previous_original_path);
    return;
}



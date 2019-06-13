<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * BreadCrumb Helper Class - Takes an array as input and returns a random element
 * @param	Url
 * @return	mixed	depends on the url contains
 */	
function create_breadcrumb() {
	$ci = &get_instance();
        $segments = $ci->uri->segment_array(); 
        $segment_count = count($segments);//echo $ci->uri->segment(1);exit;
        $uri = $ci->uri->segment(1); ///adede (1) by shubhranshu
        $role = $ci->data['user']->role_id;
	$link = '<ol class="breadcrumb breadcrumb_style"> <a href="'.site_url().'">Home</a>'; 
        if ($segment_count >= 2) {
            for ($i=0; $i<2;$i++) {
                if ($role == 'COMPACT') {
                    if ($segments[1] == 'profile' && $segments[2] == 'change_password') {
                        $i=2;
                        $link .= ' >> ';
                        break;
                    }
                }
                $prep_link .= $ci->uri->segment($i).'/';
                $link_text = ucwords(str_replace('_', ' ', $segments[$i]));
                $link.='<li><a href="'.site_url($prep_link).'">'. $link_text .'</a></li>';
            }
        }else {
            $link = '<ol class="breadcrumb breadcrumb_style"><li><a href="'.site_url().'">Home</a></li>'; 
            $i=1;
        }
        if ($segments[$i]) {
            if (is_numeric($segments[$i])) {
                $link_text = 'Page '. $segments[$i];
                $link.='<li>'. $link_text .'</li>';
            }else {
                $link.='<li>'. ucwords(str_replace('_', ' ', $segments[$i])) .'</li>';
            }
        }
        $link .= '</ol>';
        return $link;
}

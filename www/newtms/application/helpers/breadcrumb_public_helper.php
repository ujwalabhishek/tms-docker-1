<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Code Igniter BreadCrumb Helpers
 *
 * @package	CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author	ManiKandaRaja.S
 */

// ------------------------------------------------------------------------

/**
 * BreadCrumb Helper Class - Takes an array as input and returns a random element
 *
 * @param	Url
 * @return	mixed	depends on the url contains
 */	
if(!function_exists('create_breadcrumb')) {

function create_breadcrumb1(){
	$ci = &get_instance();
	$i=1;
        $uri = $ci->uri->segment($i); 
        
	$link = '<ol class="breadcrumb breadcrumb_style"> <li><a href="'.site_url().'">Home</a></li>'; //<li>

        while($uri != ''){
		$prep_link = '';
		for($j=1; $j<=$i;$j++){
			$prep_link .= $ci->uri->segment($j).'/';
		}
             
                if ($i <= 1)
                {
                    if($ci->uri->segment($i+1) == ''){
                        $link.='<li><a href="'.site_url($prep_link).'"><b>';
                        $link.=$ci->uri->segment($i).'</b></a></li> ';
                    }else{
                        $link.='<li><a href="'.site_url($prep_link).'">';
                        $link.=$ci->uri->segment($i).'</a></li> ';
                    }
                } else{
                    if($ci->uri->segment($i+1) == ''){
                        $link.='<li>'.$ci->uri->segment($i).'</li>';
                    }else{
                        $link.='<li>'.$ci->uri->segment($i).'</li>';
                    }
                }
                $i++;
                $uri = $ci->uri->segment($i);
	}

    $link .= '</ol><span class="chat_icon"><a href=""><img border="0" title="Chat" src="'.base_url().'assets/images/chat_icon.png"></a></span>';
    return $link;
	}
}

function create_breadcrumb() {
	$ci = &get_instance();
        $segments = $ci->uri->segment_array(); 
        $segment_count = count($segments);
        $uri = $ci->uri->segment(0); ///adede (0) by shubhranshu
        
	$link = '<ol class="breadcrumb breadcrumb_style"> <a href="'.site_url('').'">Home</a>'; 
        if ($segment_count >= 2) {
            for ($i=0; $i<2;$i++) {
                if($ci->uri->segment($i) != 'user'){
                $prep_link .= $ci->uri->segment($i).'/';                
                $link_text = ucwords(str_replace('_', ' ', $segments[$i]));
                $link.='<li><a href="'.site_url($prep_link).'">'. $link_text .'</a></li>';
                }
            }
        }else {
            $link = '<ol class="breadcrumb breadcrumb_style"><li><a href="'.site_url('').'">Home</a></li>'; 
            $i=1;
        }         
        // last element in chain should not be a link
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
?>
<style>
    .commentlist:before {
  display:none;
}
    </style>
    
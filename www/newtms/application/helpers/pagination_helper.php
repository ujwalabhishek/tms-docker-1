<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function get_pagination($per_page, $page, $url, $total, $field = NULL, $order_by = NULL)
    {
    $CI =& get_instance();
    $adjacents = 2;
    $page = ($page == 0 ? 1 : $page); 
    $start = ($page - 1) * $per_page;                              
    $prev = $page - 1;                         
    $next = $page + 1;
    $lastpage = ceil($total/$per_page);
    $lpm1 = $lastpage - 1;
    $pagination = "";
    if($lastpage > 1)
    {  
       $pagination .= "<ul class='pagination'>";
       if ($page > 1) { 
            $prevurl = $prev;
            $firsturl = 1;
            $query_string = pagination_add_query_string($field, $order_by);
            if ($query_string) {
                $prevurl .= $query_string;
                $firsturl .= $query_string; 
            }
            $pagination.= "<li><a href='{$url}$firsturl'>First</a></li>";
            $pagination.= "<li><a href='{$url}$prevurl'>Prev</a></li>";
       }
        if ($lastpage < 7 + ($adjacents * 2))
        {  
            for ($counter = 1; $counter <= $lastpage; $counter++)
            {
                $counterurl = $urlpre.$counter;
                $query_string = pagination_add_query_string($field, $order_by);
                if ($query_string) {
                    $counterurl .= $query_string;
                }
                if ($counter == $page)
                    $pagination.= "<li class='active'><a>$counter</a></li>";
                else
                    $pagination.= "<li><a href='{$url}$counterurl'>$counter</a></li>";                   
            }
        }
        elseif($lastpage > 5 + ($adjacents * 2))
        {
            if($page < 1 + ($adjacents * 2))    
            {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                {
                    $counterurl = $urlpre.$counter;
                    $query_string = pagination_add_query_string($field, $order_by);
                    if ($query_string) {
                        $counterurl .= $query_string;
                    }
                    if ($counter == $page)
                        $pagination.= "<li class='active'><a>$counter</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}$counterurl'>$counter</a></li>";                   
                }
            }
            elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
            {
                $pagination.= "<li><a href='{$url}1'>1</a></li>";
                $pagination.= "<li><a href='{$url}2'>2</a></li>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                {
                    $counterurl = $urlpre.$counter;
                    $query_string = pagination_add_query_string($field, $order_by);
                    if ($query_string) {
                        $counterurl .= $query_string;
                    }
                    
                    if ($counter == $page)
                        $pagination.= "<li class='active'><a>$counter</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}$counterurl'>$counter</a></li>";                   
                }
            }
            else
            {
                $pagination.= "<li><a href='{$url}1'>1</a></li>";
                $pagination.= "<li><a href='{$url}2'>2</a></li>";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                {
                    $counterurl = $urlpre.$counter;
                    $query_string = pagination_add_query_string($field, $order_by);
                    if ($query_string) {
                        $counterurl .= $query_string;
                    }
                    
                    if ($counter == $page)
                        $pagination.= "<li class='active'><a>$counter</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}$counterurl'>$counter</a></li>";                   
                }
            }
        }

        if ($page < $counter - 1){
            $query_string = pagination_add_query_string($field, $order_by);
            if ($query_string) {
                $pagination.= "<li><a href='{$url}$urlpre$next$query_string'>Next</a></li>";
                $pagination.= "<li><a href='{$url}$lastpage$query_string'>Last</a></li>";
            }else {
                $pagination.= "<li><a href='{$url}$urlpre$next'>Next</a></li>";
                $pagination.= "<li><a href='{$url}$lastpage'>Last</a></li>";
            }
        }
        $pagination.= "</ul>\n";     
    }          
        return $pagination;
}
/*
 * add the query parameters to the url
 */
function pagination_add_query_string($field = NULL, $order_by = NULL) {
    $url = NULL;
    if ($field) {
        $url .= '?f='. $field;
    }
    if ($order_by) {
        $url .= '&o='. $order_by;
    }
    return $url;            
}
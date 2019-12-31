<?php 
class Tablelist_Model extends CI_Model {

    var $title   = '';
    var $content = '';
    var $date    = '';

    function __construct()
    {
        parent::__construct();
    }
    /**
     * Get record count
     * @param type $table
     * @return type
     */
   public function record_count($table){

        $query = $this->db->query($table);
        return $total_result =  $query->num_rows();       
    }
    /**
     * Get list table
     * @param type $table
     * @param type $limit
     * @param type $offset
     * @param type $sort_by
     * @param type $sort_order
     * @return type
     */
    public function get_table($table, $limit, $offset, $sort_by, $sort_order)
                {

                    $limitstart = $offset - $limit;
                     $query = $table;
                  if($sort_by) {  
                         $query .= ' ORDER BY '.$sort_by; 
                         $query .= ' '.$sort_order;
                     }                 
                   if($limit > 1){
                       $limitvalue = $offset - $limit;
                 $query .= ' LIMIT '.$limitvalue;
                   }
                   if($offset)
                 $query .= ', '.$limit;   
                  // echo $query;
                   return $this->db->query($query);

        }
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Scheduler_Model extends CI_Model
{
    /**
     * sales comm calc
     * @return type
     */
    public function get_data_for_recalc()
    {
        $month = date('n') -1;                
        $year = date('Y');

        if($month == 0){
            $month = 12;
            $year--;
        }

        $dt_start = "$year-$month-01";
        $dt_end = "$year-$month-". cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
          $sql= 
                "SELECT ce.tenant_id, ce.course_id, ce.class_id, cc.class_start_datetime, cc.class_end_datetime, ce.sales_executive_id, 
                cc.class_fees, 
                SUM( cc.class_fees ) AS class_feets,COALESCE( cse.commission_rate,c.default_commission_rate, 0 ) AS commission_rate,
                COALESCE( c.default_commission_rate,0) AS d_comm_rate, ce.pymnt_due_id, MONTH( cc.class_start_datetime ) 
                month ,YEAR( cc.class_start_datetime) year
                FROM class_enrol ce
                JOIN enrol_pymnt_due epd ON epd.pymnt_due_id =ce.pymnt_due_id and epd.user_id=ce.user_id
                JOIN course_class cc ON cc.course_id = ce.course_id  AND cc.class_id = ce.class_id
                LEFT JOIN course_sales_exec cse ON cse.user_id = ce.sales_executive_id AND cse.course_id = ce.course_id
                JOIN course c ON c.course_id = ce.course_id
                WHERE ce.sales_executive_id IS NOT NULL and DATE( cc.class_end_datetime )>='{$dt_start}' 
                    AND DATE( cc.class_end_datetime ) <= '{$dt_end}'
                AND epd.att_status=1 AND ce.comm_status=0 AND ce.payment_status!='PYNOTREQD' AND ce.enrol_status IN ('ENRLACT','ENRLBKD')
                GROUP BY ce.course_id,ce.sales_executive_id";
                return $this->db->query($sql);
        
      /*  $sql = "SELECT
	ce.tenant_id,
	ce.course_id,
	ce.sales_executive_id,
	SUM(cc.class_fees) as class_feets,
	COALESCE(cse.commission_rate,0) as commission_rate,
	ce.pymnt_due_id,
	MONTH(cc.class_start_datetime) month,
	YEAR(cc.class_start_datetime) year
	FROM class_enrol ce
		JOIN course_class cc ON cc.course_id = ce.course_id AND cc.class_id = ce.class_id
		JOIN course_sales_exec cse ON cse.user_id = ce.sales_executive_id AND cse.course_id = ce.course_id
	WHERE   DATE(cc.class_end_datetime) >= '{$dt_start}' and DATE(cc.class_end_datetime) <= '{$dt_end}'
		and ce.enrol_status IN ('ENRLACT', 'ENRLBKD')
	GROUP BY ce.course_id, ce.sales_executive_id";
        return $this->db->query($sql);       */
        
    }
    public function get_data_for_recalc1()
    {
         $month = date('n') -1;    
         $year = date('Y');
          if($month == 0){
            $month = 12;
            $year--;
        }
        
        $dt_start = "$year-$month-01";
        $dt_end = "$year-$month-". cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
        $sql= "SELECT ce.tenant_id, ce.course_id as courses, ce.class_id as classes,
                    ce.user_id as users,epd.att_status as att,
                cc.class_start_datetime, cc.class_end_datetime, ce.sales_executive_id, 
                cc.class_fees, 
                 ce.pymnt_due_id, MONTH( cc.class_start_datetime ) 
                month ,YEAR( cc.class_start_datetime) year
                FROM class_enrol ce
                JOIN enrol_pymnt_due epd ON epd.pymnt_due_id =ce.pymnt_due_id and epd.user_id=ce.user_id
                JOIN course_class cc ON cc.course_id = ce.course_id  AND cc.class_id = ce.class_id
                LEFT JOIN course_sales_exec cse ON cse.user_id = ce.sales_executive_id AND cse.course_id = ce.course_id
                JOIN course c ON c.course_id = ce.course_id
                WHERE ce.sales_executive_id IS NOT NULL and DATE( cc.class_end_datetime )>='{$dt_start}' 
                AND epd.att_status=1 AND ce.comm_status=0 AND DATE( cc.class_end_datetime ) <= '{$dt_end}'
                AND ce.enrol_status IN ('ENRLACT','ENRLBKD')";
                //GROUP BY ce.course_id,ce.sales_executive_id";
        return $this->db->query($sql);
    }
    /**
     * sales commission
     * @param type $data
     * @return boolean
     */
    public function insert_data_for_recalc($data)
    {
        if(!$this->db->insert('sales_comm_due', $data)){            
            log_message('error', "      ERROR insert to 'sales_comm_due' sales_exec_id={$row->sales_executive_id} pymnt_due_id={$row->pymnt_due_id} ". $this->db->_error_message());
            return false;
        }        
         $this->db->last_query();
        return true;
    }
    /**
     * update sales commission status for trainee
     * @param type $data
     * @return boolean
     */
    public function update_data_for_recalc()
    {
        $table = $this->get_data_for_recalc1();
        foreach ($table->result() as $row) 
        {
            $comm_status=array('comm_status' => 1);
            $this->db->where('course_id', $row->courses);
            $this->db->where('class_id',  $row->classes);
            $this->db->where('user_id',   $row->users);
            $this->db->update('class_enrol', $comm_status);
} 
         $this->db->last_query();
    }
} 
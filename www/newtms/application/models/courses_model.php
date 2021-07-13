<?php

/*
 * Model class for Course List  
 * Author : Mir
 */

class Courses_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->helper('common');
        $this->load->library('bcrypt');
    }

    /*
     * Function to fetch tenant details for storing the session.
     * Author : Bineesh.
     * Date : 06 Oct 2014.
     */

    public function get_tenant_details() {
        $tenant_id = TENANT_ID;
        if (!empty($tenant_id)) {
            $output = '';
            $this->db->select('tenant_name,Logo,tenant_id, contact_name, CopyRightText, ApplicationName, Currency, Country,tenant_address,tenant_city,tenant_contact_num,tenant_email_id');
            $this->db->from('tenant_master');
            //$this->db->where('account_status', ACTIVE); //changed on 09/02/15
            $this->db->where('tenant_id', $tenant_id);
            $output = $this->db->get();
            return $output->row();
        }
    }

    /*
      Function to retrieve tenant details.
      Author: Balwant Singh
     */

    public function get_tanant() {
        $tenant_id = TENANT_ID;
        if (!empty($tenant_id)) {
            $output = '';
            $this->db->select('tenant_name, tenant_id, contact_name, tenant_address, tenant_city, Currency, tenant_contact_num,
                 tenant_state, tenant_country, tenant_email_id, invoice_name, invoice_footer_text');
            $this->db->from('tenant_master');
            //$this->db->where('account_status', ACTIVE);
            $this->db->where('tenant_id', $tenant_id);
            $output = $this->db->get()->result_array();
            return $output[0];
        }
    }

    /*
     * function to return the acive course list
     * @param int $limt
     * @param int $offset
     * @param string $search_value
     * @return array $courses
     * Author:Bineesh
     */

    public function get_course_list($limit, $offset, $search_value = NULL) {
        $tenant_id = TENANT_ID;

        if (!empty($search_value)) {
            $this->db->select('course_id,crse_name,description,crse_icon')->like('crse_name', $search_value, 'both');
        } else {
            $this->db->select('course_id,crse_name,description,crse_icon,certi_level,crse_duration,language,pre_requisite');
        }
        $this->db->from('course');
        $this->db->where('crse_status', ACTIVE);
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('display_on_portal', '1');
        $this->db->order_by('crse_name');
        if ($limit == $offset) {
            $this->db->limit($offset);
        } else if ($limit > 0) {
            $limitvalue = $offset - $limit;
            $this->db->limit($limit, $limitvalue);
        }
        return $this->db->get()->result();
    }
    
    /* Gets the trainer feedback for patricular class and user skm start */
    public function get_trainer_feedback_by_user_id( $course_id, $class_id, $user_id) {
        $this->db->select('tf.*, mv.category_name, mv.parameter_id');
        $this->db->from('metadata_values mv');
        $this->db->join('trainer_feedback tf', 'tf.feedback_question_id = mv.parameter_id and tf.class_id = ' . $class_id . ' and tf.user_id =' . $user_id .
                ' and tf.course_id =' . $course_id, 'left');

        $this->db->like('mv.category_id', 'CAT42_01', 'after');
       $query = $this->db->get();
        $result = $query->result_array();
        $grouped_by_question = array();
        foreach ($result as $res) {
            $grouped_by_question[$res['parameter_id']] = $res;
        }
        return $grouped_by_question;
    }//end
    
    /* Ftech particular user details skm start */
    public function get_trainee_details($user_id) {
      
        $tenant_id = TENANT_ID; 
        $this->db->select('usr.tax_code,  pers.first_name, pers.last_name');
        $this->db->from('tms_users usr');
        $this->db->join('tms_users_pers pers', 'usr.tenant_id = pers.tenant_id '
                . 'AND usr.user_id = pers.user_id');
        $this->db->where('usr.user_id', $user_id);
        $this->db->where('usr.tenant_id', TENANT_ID);
        return $this->db->get()->row();
    } //end
    
    /**
     * this function to get all the class details by class_id skm start
     */
    public function get_class_details_feedback($class_id) {
        $tenant_id = TENANT_ID; 
        $this->db->select('*');
        $this->db->from('course_class');
        $this->db->where('tenant_id', TENANT_ID);
        $this->db->where('class_id', $class_id);
        $result = $this->db->get()->row();
        return $result;
    }//end
    
    /**

    * This Method For getting the Course details for View Couses.

    * @param type $id

    * @return boolean

    */

    public function get_course_detailse($id) {

        if ($id != '') {

            $this->db->select('*');

            $this->db->from('course');

            $this->db->where('course_id', $id);

            $result = $this->db->get()->row();

            return $result;

        } else {

            return FALSE;

        }

    }
    

    /* fetch class details skm start     */
    public function get_class_details($parm = array()) {
        $classid = $parm[class_id];
        $courseid = $parm[course_id];
        $output = array();
//        $class_status=array(IN_PROG,YT_STRT); ### commented by sankar
        if ($classid && $courseid) {
            $this->db->select('cc.class_id, cc.class_name, cc.class_start_datetime, cc.class_end_datetime, 
                              cc.total_classroom_duration, cc.class_fees, cc.class_discount, cc.class_pymnt_enrol, cc.classroom_location');
            $this->db->from('course_class cc');
//            $this->db->where_in('cc.class_status', $class_status); ### commented by sankar
            $this->db->where('cc.class_id', $classid);
            $this->db->where('cc.course_id', $courseid);
            $this->db->where('cc.tenant_id', TENANT_ID);
            $res = $this->db->get()->result_array();
            $output['class'] = $res[0];
            $course_det = $this->get_course_name($courseid);
            $output['course'] = array('name' => $course_det[0]['crse_name'],
                'id' => $courseid,
                'isgst' => $this->get_course_gst($courseid),
            );
            $output['refrals'] = $this->get_referrals();
            $output['gst'] = $this->get_gst_rate();
            $output['tanant'] = $this->get_tanant();
           
        }
        

        return $output;
    }//end
    
    /* This Method for getting values from metadata_values skm start*/

    public function get_metadata_on_parameter_id($course_type) {

        $category_names = "";

         $course_type = trim($course_type);

        if (empty($course_type)) {

            $category_names = "";

         } else {

            $metaparams = explode(',', $course_type);

            for ($i = 0; $i < count($metaparams); $i++) {

                $parameter_id = trim($metaparams[$i]);

                $this->db->select("category_name");

                $this->db->from("metadata_values");

                $this->db->where("parameter_id", $parameter_id);

                $res = $this->db->get();

                if($res->num_rows()){

                    $category_name = $res->row('category_name');

                }else{

                    $category_name='';

                }

                $category_names .=$category_name . ", ";

            }

        }

        $category_names = rtrim($category_names, ', ');

        return $category_names;

    }//end

    /*
      Function to get current gst rate.
      Author: Balwant Singh
     * Modified By:Blessy
     * 13/11/14
     */

    public function get_gst_rate() {
        $this->db->select('gst_rate');
        $this->db->from('gst_rates');
        $this->db->where('is_current', '1');
        $this->db->where('tenant_id', TENANT_ID);
        $r = $this->db->get()->row()->gst_rate;
        return $r;
    }

    /*
     * Function to return count of course class based on course_id, class_id and tenant_id
     */

    public function get_course_class_count($course_id, $class_id) {
        if (!empty($course_id) && !empty($class_id)) {
            $this->db->where('tenant_id', TENANT_ID);
            $this->db->where('course_id', $course_id);
            $this->db->where('class_id', $class_id);
            $this->db->where('enrol_status !=', RESHLD);
            // $this->db->where('pymnt_due_id != ', "");
            $this->db->from('class_enrol');
            return $this->db->count_all_results();
        }
    }

    /*
     * Function to return count of course class based on course_id and tenant_id
     */

    public function get_course_count($course_id) {
        $date = date('Y-m-d');
        if (!empty($course_id)) {
            $this->db->where('tenant_id', TENANT_ID);
            $this->db->where('course_id', $course_id);
            $this->db->where('class_status !=', COMPLTD);
            $this->db->where('class_status !=', INACTIVE);
            $this->db->where('DATE(class_end_datetime) >= ', $date);
            $this->db->from('course_class');
            return $this->db->count_all_results();
        }
    }
   
    /*
     * This function is used to get the parameter value based on the parameter ID
     * MOVE to GENERIC CLASS
     * Author: siddappa
     */

    public function get_param_value($param_id) {
        return $this->db->select('category_name')->where('parameter_id', $param_id)->get('metadata_values')->row();
    }

    /**
     * Get the list of course class
     * @param string $tenant_id
     * @param int $limit
     * @param int $offset
     * @param string $sort_by
     * @param string $sort_order
     * @return array
     * Author : Mir
     */
    public function get_classes_by_date($class_date = NULL, $tenant_id, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL) {
        if ($offset < 0 || empty($tenant_id)) {
            return;
        }
        $this->db->select('crs.crse_name,c_cls.class_id, c_cls.course_id, c_cls.total_seats, c_cls.class_name, c_cls.class_start_datetime,c_cls.class_end_datetime, c_cls.description,
                c_cls.total_classroom_duration, c_cls.total_lab_duration,c_cls.class_fees, c_cls.classroom_location, c_cls.lab_location,c_cls.class_language,c_cls.class_pymnt_enrol,c_cls.class_status');
        $this->db->from('course_class c_cls');
        $this->db->join('course crs', 'crs.course_id = c_cls.course_id');
        $this->db->where('crs.crse_status', ACTIVE);
        $this->db->where('c_cls.class_status !=', COMPLTD);
        $this->db->where('c_cls.class_status !=', INACTIVE);
        $this->db->where('crs.display_on_portal', '1'); /* Added by Blessy */
        $this->db->where('DATE(c_cls.class_start_datetime) <= ', $class_date);
        $this->db->where('c_cls.tenant_id', $tenant_id);
        $this->db->where('DATE(c_cls.class_end_datetime) >= ', $class_date);
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('c_cls.class_start_datetime');
        }
        if ($limit == $offset) {
            $this->db->limit($offset);
        } else if ($limit > 0) {
            $limitvalue = $offset - $limit;
            $this->db->limit($limit, $limitvalue);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * Function to return all classes count based on the selected date
     * Modified by:Blessy
     */

    public function get_classes_by_date_count($class_date = NULL, $tenant_id) {
        if (empty($class_date)) {
            return FALSE;
        }
        $this->db->select('count(*) as totalrows');
        $this->db->from('course_class c_cls');
        $this->db->join('course crs', 'crs.course_id = c_cls.course_id');
        $this->db->where('crs.crse_status', ACTIVE);
        $this->db->where('c_cls.class_status !=', COMPLTD);
        $this->db->where('c_cls.class_status !=', INACTIVE);
        $this->db->where('DATE(c_cls.class_start_datetime) <= ', $class_date);
        $this->db->where('c_cls.tenant_id', $tenant_id);
        $this->db->where('DATE(c_cls.class_end_datetime) >= ', $class_date);
        $result = $this->db->get()->result();
        return $result[0]->totalrows;
    }

    /*
     * Function to return course name 
     * Author:Bineesh
     * Modified  by: Blessy
     */

    public function get_course_name($course_id = NULL) {
        $output = '';
        $this->db->select('course_id,crse_name');
        $this->db->from('course');
        if (!empty($course_id)) {

            $this->db->where('course_id', $course_id);
        }
        $output = $this->db->get();
        return $output->result_array();
    }

    /*
      Function to get gst on and off information for a course by course id.
      Author: Balwant Singh
     */

    public function get_course_gst($course_id = NULL) {
        if (!empty($course_id)) {
            $output = '';
            $this->db->select('gst_on_off');
            $this->db->from('course');
            $this->db->where('course_id', $course_id);
            return $this->db->get()->row()->gst_on_off;
        }
        return NULL;
    }

    /*
      Function to get referals of the current user.
      Author: Balwant Singh
     * Modified By:Blessy Paul
     * Modified  On:28/10/14
     */

    public function get_referrals() {
        $userid = $this->session->userdata('userDetails')->user_id;
        if (empty($userid))
            return NULL;
        $output = '';
        $this->db->select('per.user_id , per.first_name ,per.last_name');
        $this->db->from('tms_users u');
        $this->db->join('tms_users_pers per', 'u.user_id=per.user_id');
        $this->db->where('u.friend_id', $userid);
        $output = $this->db->get()->result_array();
        return $output;
    }

    /*
     * function to return the autocomplete list of course based on the input recieved
     * @param string $query
     * 
     * @return array matches
     */

    public function course_list_autocomplete($query = NULL) {
        $matches = array();
        if (!empty($query)) {
            $results = $this->db->select('course_id, crse_name')->where('crse_status', ACTIVE)->where('display_on_portal', '1')->like('crse_name', $query, 'both')->get('course')->result();
            foreach ($results as $result) {
                $matches[$result->crse_name] = $result->crse_name;
            }
        }
        return $matches;
    }

    /*
     * This method validates the user credentials and checks if the user is active
     * Author:Bineesh
     */

    public function check_public_user_valid() {
        extract($_POST);
        $uname = mysql_real_escape_string($username);
        $pwd = mysql_real_escape_string($password);
        $this->db->select('usr.password, pers.first_name, pers.last_name, pers.tenant_id, usr.user_id, ten.logo, ten.copyrighttext, '
                . 'ten.currency, ten.country,ten.applicationname');
        $this->db->from('tms_users_pers pers');
        $this->db->join('tms_users usr', 'usr.tenant_id = pers.tenant_id and usr.user_id = pers.user_id');
        $this->db->join('tenant_master ten', 'usr.tenant_id=ten.tenant_id');
        $this->db->where_not_in('usr.account_type', array('INTUSR'));
        $this->db->where('usr.user_name', $uname);
        $this->db->where('usr.account_status', ACTIVE);
        $result = $this->db->get()->row();
        if ($this->bcrypt->check_password($password, $result->password)) {
            unset($result->password);
            return $result;
        } else {
            return FALSE;
        }
    }
    /*function to get the num rows in the landing page*/
 public function course_list_count($search_value = NULL) {
        $tenant_id = TENANT_ID;
        if (empty($search_value)) {
            $this->db->select('course_id');
        } else {
            $this->db->select('course_id')->like('crse_name', $search_value, 'both');
        }
        $this->db->from('course');
        $this->db->where('crse_status', 'ACTIVE');
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('display_on_portal', '1');
        $result = $this->db->get();
        return $result->num_rows();
    }
  
    public function get_course_class_list_count($course_id = NULL, $tenant_id) {
        $date = date('Y-m-d');
        $class_status = array(COMPLTD, INACTIVE);
        if (empty($course_id)) {
            return 0;
        }
        $this->db->select('count(*) as totalrows');
        $this->db->from('course_class c_cls');
        $this->db->join('course crs', 'crs.course_id = c_cls.course_id and c_cls.tenant_id=crs.tenant_id');
        $this->db->where('crs.crse_status', ACTIVE);
        $this->db->where_not_in('c_cls.class_status', $class_status);
        $this->db->where('crs.display_on_portal', '1');
        $this->db->where('c_cls.display_class_public', '1');
        $this->db->where('c_cls.course_id', $course_id);
        $this->db->where('c_cls.tenant_id', $tenant_id);
        $this->db->where('DATE(c_cls.class_end_datetime) >= ', $date);
        $result = $this->db->get()->result();
        return $result[0]->totalrows;
    }

    /**
     * Get the list of course class
     * @param string $tenant_id
     * @param int $limit
     * @param int $offset
     * @param string $sort_by
     * @param string $sort_order
     * @return array
     * Author : Mir
     * Modified  By:Blessy
     */
    public function get_course_class_list($course_id = NULL, $tenant_id, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL) {
        $date = date('Y-m-d');
        $class_status = array(COMPLTD, INACTIVE);

        if ($offset < 0 || empty($tenant_id)) {
            return;
        }
        $this->db->select('crs.crse_name,c_cls.class_id, c_cls.course_id, c_cls.total_seats, c_cls.class_name, c_cls.class_start_datetime,c_cls.class_end_datetime, c_cls.description,
                c_cls.total_classroom_duration, c_cls.total_lab_duration,c_cls.class_fees,c_cls.classroom_location, c_cls.lab_location ,c_cls.class_language,c_cls.class_pymnt_enrol,c_cls.class_status');
        $this->db->from('course_class c_cls');
        $this->db->join('course crs', 'crs.course_id = c_cls.course_id and c_cls.tenant_id=crs.tenant_id');
        $this->db->where('crs.crse_status', ACTIVE);
        $this->db->where_not_in('c_cls.class_status', $class_status);
        $this->db->where('crs.display_on_portal', '1');
        $this->db->where('c_cls.display_class_public', '1');
        $this->db->where('c_cls.course_id', $course_id);
        $this->db->where('c_cls.tenant_id', $tenant_id);
        $this->db->where('DATE(c_cls.class_end_datetime) >= ', $date);

        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('c_cls.last_modified_on', 'DESC');
        }

        if ($limit == $offset) {
            $this->db->limit($offset);
        } else if ($limit > 0) {
            $limitvalue = $offset - $limit;
            $this->db->limit($limit, $limitvalue);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * Function to return count of course list 
     * Author:Bineesh
     */

    public function course_count($search_value = NULL) {
        $tenant_id = TENANT_ID;
         $class_status = array(COMPLTD, INACTIVE);
          $date = date('Y-m-d');
        if (empty($search_value)) {
            $this->db->distinct();
            $this->db->select('crs.course_id');
        } else {
            $this->db->distinct();
            $this->db->select('crs.course_id')->like('crs.crse_name', $search_value, 'both');
        }
        $this->db->from('course crs');
        $this->db->join('course_class c_cls', 'crs.course_id = c_cls.course_id');
        $this->db->where('crs.crse_status', ACTIVE);
       $this->db->where_not_in('c_cls.class_status', $class_status);
        $this->db->where('crs.tenant_id', $tenant_id);
        $this->db->where('crs.display_on_portal', '1');
        $this->db->where('c_cls.display_class_public', '1');
        $this->db->where('DATE(c_cls.class_end_datetime) >= ', $date);
        $result = $this->db->get();
        return $result->num_rows();
    }

    public function get_active_course_class_list($tenant_id, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL) {
         $class_status = array(COMPLTD, INACTIVE);
          $date = date('Y-m-d');
        if ($offset < 0 || empty($tenant_id)) {
            return;
        }
        $this->db->distinct();
        $this->db->select('crs.course_id,crs.crse_name,crs.description,crs.crse_icon,crs.certi_level,crs.crse_duration,crs.language,crs.pre_requisite');
        $this->db->from('course crs');
        $this->db->join('course_class c_cls', 'crs.course_id = c_cls.course_id');
        $this->db->where('crs.crse_status', ACTIVE);
        $this->db->where_not_in('c_cls.class_status', $class_status);
        $this->db->where('crs.tenant_id', $tenant_id);
        $this->db->where('crs.display_on_portal', '1');
        $this->db->where('c_cls.display_class_public', '1');
        $this->db->where('DATE(c_cls.class_end_datetime) >= ', $date);
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('crs.last_modified_on', 'DESC');
        }

        if ($limit == $offset) {
            $this->db->limit($offset);
        } else if ($limit > 0) {
            $limitvalue = $offset - $limit;
            $this->db->limit($limit, $limitvalue);
        }
        $query = $this->db->get();
        return $query->result_array();
    }
/*store enrol data while paypal payment*/
    public function store_paypal_enrol_details($param = NULL) {
        if (empty($param)) {
            return;
        }
        $str = serialize($param);
        $data = array(
            'enrol_details' => $str
        );
        $this->db->insert('paypal_payment_details', $data);
        //echo $this->db->last_query();
        return $this->db->insert_id();
    }

    // check if the enrol details are updated succesfully
    public function check_paypal_payment_details_exists($tran_id = NULL) {
        $status = FALSE;

        $this->db->select('pid');
        $this->db->from('paypal_payment_details');
        $this->db->where('txn_id', $tran_id);
        if ($this->db->get()->num_rows() > 0) {
            $status = TRUE;
        }

        return $status;
    }

    /*
     * Modified By:Blessy Paul
     * 31/10/14

     *  */

    public function insert_class_enroll($parm) {
        extract($parm);
        $friend_id = '';
        $dateTime = date('Y-m-d H:i:s');
        $user = $this->session->userdata('userDetails');
        $enrolment_mode = 'SELF';
        $payment_due_id = get_max_lookup(ENROL_PYMNT_DUE);
       
        if ($enrol_to != $user->user_id) {

            $enrolled_by = $user->user_id;
        }
        $gst_rule = $this->get_gst_rule($course_id);
        $enrol_status = ($payment_status == 'PAID') ? 'ENRLACT' : 'ENRLBKD'; ### added by sankar
        $class_enroll_data = array(
            'tenant_id' => $user->tenant_id,
            'course_id' => $course_id,
            'class_id' => $class_id,
            'user_id' => $enrol_to,
            'enrolment_type' => 'PUBLIC',
            'enrolment_mode' => $enrolment_mode,
            'pymnt_due_id' => $payment_due_id,
            'company_id' => '',
            'friend_id' => $enrolled_by,
            'enrolled_on' => $dateTime,
            'enrolled_by' => $user->user_id,
            'tg_number' => '',
            'rescheduled_class_id' => '',
            'rescheduled_reason' => '',
            'rescheduled_reason_oth' => '',
            'trainee_feedback_rating' => '',
            'attendance_status' => '',
            'payment_status' => $payment_status,
            'trainer_fdbck_by' => '',
            'other_remarks_trainee' => '',
            'trainer_fdbck_modi_by' => '',
            'class_status' => '',
            'enrol_status' => $enrol_status,
        );
        $this->db->insert('class_enrol', $class_enroll_data);

        $enrol_pymnt_due_data = array(
            'user_id' => $enrol_to,
            'pymnt_due_id' => $payment_due_id,
            'class_fees' => $class_fee,
            'total_amount_due' => $amount,
            'discount_type' => $discount_type,
            'discount_rate' => $discount,
            'subsidy_amount' => NULL, ### Commented by sankar
            'subsidy_recd_date' => '',
            'subsidy_modified_on' => $dateTime, ### Added by sankar
            'gst_amount' => $gstamount
        );
        $this->db->insert('enrol_pymnt_due', $enrol_pymnt_due_data);

        // modifeid by sankar for tenant based invoice id, date :17-03-2015.
        $invoice_id = $this->generate_invoice_id();
        $enrol_invoice_data = array(
            'invoice_id'=>$invoice_id,
            'pymnt_due_id' => $payment_due_id,
            'inv_date' => $dateTime,
            'inv_type' => 'INVINDV',
            'total_unit_fees' => $class_fee,
            'total_inv_amount' => $amount,
            'total_inv_discnt' => $discount_amount, ### modified by sankar
            'total_inv_subsdy' => '',
            'total_gst' => $gstamount,
            'gst_rate' => $gstrate,
            'gst_rule' => $gst_rule ### modified by sankar
        );
        $this->db->insert('enrol_invoice', $enrol_invoice_data);
        // modifeid by sankar for tenant based invoice id, date :17-03-2015.
        //return $this->db->insert_id();
        return $invoice_id;
    }

    public function insert_invoice_record($parm) {
        extract($parm);
        $dateTime = date('Y-m-d H:i:s');
        $paymnt_recd_data = array(
            'invoice_id' => $invoiceId,
            'recd_on' => $dateTime,
            'mode_of_pymnt' => $mode_of_pymnt,
            'amount_recd' => $amount,
            'cheque_number' => '',
            'cheque_date' => '',
            'bank_name' => '',
            'recd_by' => '',
        );
        $this->db->insert('enrol_paymnt_recd', $paymnt_recd_data);

        $pymnt_brkup_dt_data = array(
            'invoice_id' => $invoiceId,
            'user_id' => $enrol_to,
            'amount_recd' => $amount
        );
        $this->db->insert('enrol_pymnt_brkup_dt', $pymnt_brkup_dt_data);
    }

    public function check_user_enrol_exists($parms) {
        extract($parms);
        $this->db->select('user_id');
        $this->db->from('class_enrol');
        $this->db->where('user_id', $enrol_to);
        $this->db->where('course_id', $course_id);
        $this->db->where('class_id', $class_id);
        $this->db->limit(1);

        return $this->db->get()->row()->user_id;
    }

    // update the details of pappal upon succesful payment
    public function update_paypal_enrol_details($pid = NULL, $details = NULL) {
        if (empty($details) || empty($pid)) {
            return;
        }
        $this->db->where('pid', $pid);
        $this->db->update('paypal_payment_details', $details);
    }

    /*
      Function to name by userid (first name and last name).
      Author: Balwant Singh
     */

    public function get_username_by_userid($userid, $is_salutation = 0) {
        if (empty($userid)) {
            return FALSE;
        }
        $this->db->select('first_name, last_name,gender');
        $this->db->from('tms_users_pers');
        $this->db->where('user_id', $userid);
        $res = $this->db->get()->row();
        $gender_text = ($res->gender == 'MALE') ? 'Mr. ' : 'Ms. ';
        $name = $res->first_name . ' ' . $res->last_name;
        return ($is_salutation) ? $gender_text . $name : $name;
    }

    /*
      Function to Update Payment status.
      Author: Balwant Singh
     */

    public function update_payment_status($parm) {
        extract($parm);
        $update_data = array(
            'payment_status' => 'PAID',
            'enrol_status' => 'ENRLACT' ### added by sankar
        );
        $this->db->where('course_id', $course_id);
        $this->db->where('class_id', $class_id);
        $this->db->where('user_id', $enrol_to);
        $this->db->update('class_enrol', $update_data);
    }

    /*
      Function to get invoice id to update payment status and information.
      Author: Balwant Singh
     */

    public function get_invoice_id($parm) {
        extract($parm);
        $tenant_id = TENANT_ID;

//        $userid = $this->session->userdata('userDetails')->user_id;
        $this->db->select('ei.invoice_id');
        $this->db->from('enrol_pymnt_due epd');
        $this->db->join('class_enrol ce', 'ce.pymnt_due_id = epd.pymnt_due_id');
        $this->db->join('enrol_invoice ei', 'epd.pymnt_due_id = ei.pymnt_due_id');
        $this->db->where('ce.user_id', $enrol_to);
        $this->db->where('ce.course_id', $course_id);
        $this->db->where('ce.class_id', $class_id);
        $this->db->where('ce.tenant_id', $tenant_id);

        return $this->db->get()->row()->invoice_id;
    }

    /*
     * Author:Blessy Paul
      Function to Save feedback data.
     */

    public function save_feedback($class_id, $course_id) {

        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
        $rating = $this->input->post('rating');
        $remarks = $this->input->post('remarks');
        $data = array(
            'tenant_id' => $tenant_id,
            'user_id' => $user_id,
            'course_id' => $course_id,
            'class_id' => $class_id,
            'trainee_feedback_rating' => $rating,
            'other_remarks_trainee' => $remarks
        );
        $q1 = $this->input->post('Q01');
        $q2 = $this->input->post('Q02');
        $q3 = $this->input->post('Q03');
        $q4 = $this->input->post('Q04');
        $q5 = $this->input->post('Q05');
        $q6 = $this->input->post('Q06');
        $q7 = $this->input->post('Q07');
        $q8 = $this->input->post('Q08');
        $q9 = $this->input->post('Q09');
        $q10 = $this->input->post('Q10');
        $q11 = $this->input->post('Q11');
        $q12 = $this->input->post('Q12');
        $q13 = $this->input->post('Q13');
        $q14 = $this->input->post('Q14');
        $q15 = $this->input->post('Q15');
        $que_ans = array(
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q01',
                'feedback_answer' => $q1),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q02',
                'feedback_answer' => $q2),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q03',
                'feedback_answer' => $q3),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q04',
                'feedback_answer' => $q4),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q05',
                'feedback_answer' => $q5),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q06',
                'feedback_answer' => $q6),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q07',
                'feedback_answer' => $q7),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q08',
                'feedback_answer' => $q8),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q09',
                'feedback_answer' => $q9),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q10',
                'feedback_answer' => $q10),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q11',
                'feedback_answer' => $q11),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q12',
                'feedback_answer' => $q12),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q13',
                'feedback_answer' => $q13),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q14',
                'feedback_answer' => $q14),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q15',
                'feedback_answer' => $q15)
        );
        $this->db->where('user_id', $user_id);
        $this->db->where('class_id', $class_id);
        $this->db->delete('trainee_feedback');

        $this->db->insert_batch('trainee_feedback', $que_ans);
        $where = array(
            'tenant_id' => $tenant_id,
            'user_id' => $user_id,
            'course_id' => $course_id,
            'class_id' => $class_id
        );
        $this->db->where($where);
        $result = $this->db->update('class_enrol', $data);
        return $result;
    }

    /*
     * Author:Blessy Paul
     * This  function is for viewing feedback
     *  */

    public function get_trainee_feedback_by_user_id($tenant_id, $course_id, $class_id, $user_id) {
        $this->db->select('tf.*, mv.category_name, mv.parameter_id');
        $this->db->from('metadata_values mv');
        $this->db->join('trainee_feedback tf', 'tf.feedback_question_id = mv.parameter_id and tf.class_id = ' . $class_id . ' and tf.user_id =' . $user_id .
                ' and tf.course_id =' . $course_id, 'left');
        $this->db->like('mv.category_id', 'CAT32_01', 'after');
        //$this->db->where('tf.tenant_id', $tenant_id);
        $query = $this->db->get();
        $result = $query->result_array();
        $grouped_by_question = array();
        foreach ($result as $res) {
            $grouped_by_question[$res['parameter_id']] = $res;
        }
        return $grouped_by_question;
    }

    /*
     * Author:Blessy Paul
     * Function is for getting  class_id,course_id,trainee_feedback_rating,other_remarks_trainee in  view feedback */

    public function get_feedback_values($tenant_id, $user_id, $course_id, $class_id) {
        $this->db->select("class_id,course_id,trainee_feedback_rating,other_remarks_trainee,payment_status,enrolment_mode");
        $this->db->from("class_enrol");
        $this->db->where("user_id", $user_id);
        $this->db->where("tenant_id", $tenant_id);
        $this->db->where("course_id", $course_id);
        $this->db->where("class_id", $class_id);
        $query = $this->db->get();
        $data = array();
        $data['details'] = $query->result_array();
        return $data;
    }

    /*
     * Author:Blessy Paul
     * Function total count of completed  class list 
     * Modified by bineesh.
     * modified for class end date time and enrol_status checking.
     */

    public function get_completed_class_list_count() {
        $date = date('Y-m-d');
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
        $this->db->where('e.tenant_id', $tenant_id);
        $this->db->where('e.user_id', $user_id);
        $this->db->where("e.course_id = c.course_id");
        $this->db->where("e.class_id = c.class_id");
        //$this->db->where('DATE(c.class_end_datetime) <', $date);// commented by skm bcoz of public portal traing completed.
        $this->db->where('e.enrol_status !=', RESHLD);
        $this->db->from('class_enrol e,course_class  c');
//        echo $this->db->get();
//        echo"<br/>";echo"<br/>";echo"<br/>";echo"<br/>";
        return $this->db->count_all_results();
    }

    /*
     * Author:Blessy Paul
     * Function for  viewing  completed  class list 
     * Modified by bineesh.
     * modified for class end date time and enrol_status checking.
     */

    public function get_completed_class_list($limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL) {
        $date = date('Y-m-d');
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
        if ($offset < 0 || empty($tenant_id)) {
            return;
        }
        $this->db->select("*");
        $this->db->from("class_enrol e");
        $this->db->join("course_class cl", "e.course_id = cl.course_id AND e.class_id = cl.class_id AND cl.tenant_id=e.tenant_id");
        $this->db->join("course c", "c.course_id = e.course_id AND e.class_id = cl.class_id AND c.tenant_id=e.tenant_id");
        $this->db->join("enrol_pymnt_due epd","epd.pymnt_due_id=e.pymnt_due_id and epd.user_id=e.user_id");
        $this->db->where("e.user_id", $user_id);
        $this->db->where("epd.user_id", $user_id);
        $this->db->where("e.tenant_id", $tenant_id);
        //$this->db->where('DATE(cl.class_end_datetime) <', $date);
        $this->db->where('e.enrol_status !=', RESHLD); // commented by skm bcoz of public portal traing completed.
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('cl.class_name', 'DESC');
        }
        if ($limit == $offset) {
            $this->db->limit($offset);
        } else if ($limit > 0) {
            $limitvalue = $offset - $limit;
            $this->db->limit($limit, $limitvalue);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * Author:Blessy Paul
     * Function  for getting class_id,for  checking  feedback  already  given  or  not
     */

    public function get_feedback_status() {
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
        $this->db->select("*");
        $this->db->from("trainee_feedback");
        $this->db->where("user_id", $user_id);
        $this->db->where("tenant_id", $tenant_id);
        $query = $this->db->get();
        $num = $query->num_rows();
        if ($num > 0) {
            return $query->result_array();
        } else {
            return 0;
        }
    }

    /* Function  for  saving  certificate  collection date
      Author:Blessy */

    public function save_certi_colln_date() {
        extract($_POST);
        $user_id = $this->session->userdata('userDetails')->user_id;
        $class_id = $this->input->post('class_id');
        $d = $this->input->post('cert_colln_date');
        $col_date = date('Y-m-d H:i:s', strtotime($d));
        $data = array('certificate_coll_on' => $col_date);
        $where = array('user_id' => $user_id, 'class_id' => $class_id);
        $this->db->where($where);
        $result = $this->db->update('class_enrol', $data);
        return $result;
    }

    /* Function  to get latest  updated certi_collection date
      Author:Blessy */

    public function get_certificate_coll_on($class_id) {
        $user_id = $this->session->userdata('userDetails')->user_id;
        $where = array('user_id' => $user_id, 'class_id' => $class_id);
        $this->db->select('en.certificate_coll_on');
        $this->db->from('class_enrol en');
        $this->db->where($where);
        $result = $this->db->get();
        return $result->result();
    }

    /* Training  details  for  view refer trainee
     * Author: */

    public function get_training_details($user_id) {
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $this->db->select("enrol.enrolled_on, enrol.enrolment_mode, crse.crse_name,cls.class_name,cls.class_start_datetime,cls.class_end_datetime,crse.crse_cert_validity");
        $this->db->from('class_enrol  enrol');
        $this->db->join("course crse", "crse.course_id=enrol.course_id and crse.tenant_id=enrol.tenant_id");
        $this->db->join("course_class cls", "cls.course_id=enrol.course_id and cls.class_id=enrol.class_id and cls.tenant_id=enrol.tenant_id");
        $this->db->where('enrol.user_id', $user_id);
        $this->db->where('enrol.tenant_id', $tenant_id);
        $result = $this->db->get();
        return $result->result();
    }

    /*     * *
     * created to get the gst rule while enrolling
     * Author: Sankar
     * Date: 20/11/2014
     */

    private function get_gst_rule($course_id) {
        $this->db->select('gst_on_off, subsidy_after_before')->from('course')->where('course_id', $course_id);
        $query_data = $this->db->get()->row();
        $gst_rule = (empty($query_data->gst_on_off)) ? '' : $query_data->subsidy_after_before;
        return $gst_rule;
    }

    /*
     * Function to get the individual discount
     * Author:Balwant 
     * Modified By:Sankar
     *  */

    public function get_individual_discount($userid, $course_id) {

        $this->db->select('discount_percent')
                ->from('tms_users_discount')
                ->where('user_id', $userid)
                ->where('course_id', $course_id);
        return $this->db->get()->row()->discount_percent;
    }

    /**
     * get the company discount
     * @param int $userid current user id
     * @return float discount
     * Author:Balwant
     */
    public function get_company_discount($userid = NULL) {
        if (empty($userid)) {
            return FALSE;
        }
        $this->db->select('tenant_org_id');
        $this->db->from('tms_users');
        $this->db->where('user_id', $userid);
        $company_id = $this->db->get()->row()->tenant_org_id;

        if ($company_id) {
            $this->db->select('comp_discount');
            $this->db->from('tenant_company');
            $this->db->where('company_id', $company_id);
            return $this->db->get()->row()->comp_discount;
        }
        return FALSE;
    }

    /* Funtion to get the LOC details
     * Author:Blessy Paul
      Date:13/11/14 */

    public function get_loc_details($tenant_id, $class, $user) {
        $result = $this->db->select('cc.class_name, c.crse_name, tup.first_name, tup.last_name, tup.gender,
                        cc.class_end_datetime, c.competency_code, tu.tax_code, tm.*')
                        ->from('class_enrol ce')
                        ->join('course_class cc', 'cc.class_id=ce.class_id')
                        ->join('course c', 'c.course_id=ce.course_id')
                        ->join('tms_users tu', 'tu.user_id=ce.user_id')
                        ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                        ->join('tenant_master tm', 'tm.tenant_id=ce.tenant_id')
                        ->where('ce.user_id', $user)->where('ce.class_id', $class)
                        ->where('ce.tenant_id', $tenant_id)
                        ->get()->row();
        return $result;
    }
        //function to generate invoice id
    ////added by shubhranshu for everest invoice id changes
   private function generate_invoice_id() {

        //$date_array = explode("-",$class_start_date);

      $pre_fix_array = array("T01" => "T01", "T02" => "XPR", "T03" => "CAI", "T04" => "FL", "T12" => "XPR.A.","T16" => "XPR.B.","T17" => "EVI","T20" => "WABLAB","T23" => "DEMO", "T24" => "RLIS");

        $lookup_table = array("T01" => "test_invoice_id", "T02" => "xprienz_invoice_id", "T03" => "carrie_invoice_id", "T04" => "focus_invoice_id", "T12" => "xprienz2_invoice_id","T16" => "xprienz3_invoice_id","T17" => "ei_new_invoice_id","T20" => "wablab_invoice_id","T23" => "demo_invoice_id", "T24" => "rlis_invoice_id");

        $tenant_id = $this->tenant_id;

        $invoice_id_tmp = get_max_lookup($lookup_table[$tenant_id]);

        if($tenant_id == 'T17'){
            if(strlen($invoice_id_tmp)== 1){
                $invoice_id_tmp = '0000'.$invoice_id_tmp;
            }elseif(strlen($invoice_id_tmp)== 2){
                $invoice_id_tmp = '000'.$invoice_id_tmp;
            }elseif(strlen($invoice_id_tmp)== 3){
                $invoice_id_tmp = '00'.$invoice_id_tmp;
            }elseif(strlen($invoice_id_tmp)== 4){
                $invoice_id_tmp = '0'.$invoice_id_tmp;
            }elseif(strlen($invoice_id_tmp)== 5){
                $invoice_id_tmp = $invoice_id_tmp;
            }else{
                $invoice_id_tmp = $invoice_id_tmp;
            }
            
            $invoice_id = $pre_fix_array[$tenant_id] .'-20'.date('y').'-'.$invoice_id_tmp;
        }else{
            $invoice_id = $pre_fix_array[$tenant_id] . $invoice_id_tmp;
        }

        return $invoice_id;
    }
//code modification ends here
}

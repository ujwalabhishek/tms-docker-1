<?php

/*
 * Model class for Course List   

 */

class Course_Public_Model extends CI_Model {

    public function __construct() {

        parent::__construct();

        $this->load->helper('common');
        
        $this->load->library('bcrypt');
    }
    /*
     * Function to fetch tenant details for storing the session.    
     */
    public function get_tenant_details() {

        $tenant_id = TENANT_ID;

        if (!empty($tenant_id)) {

            $output = '';

            $this->db->select('Logo,tenant_id, contact_name, CopyRightText, ApplicationName, Currency, Country, tenant_name');

            $this->db->from('tenant_master');

            $this->db->where('account_status', 'ACTIVE');

            $this->db->where('tenant_id', $tenant_id);

            $output = $this->db->get();
            //echo $this->db->last_query();exit;

            return $output->row();
        }
    }

    /*





     * function to return the course list   





     */

    public function get_course_list($limit, $offset, $search_value = NULL) {

        $tenant_id = TENANT_ID;

        if (!empty($search_value)) {

            $this->db->select('course_id,crse_name,description,crse_icon')->like('crse_name', $search_value, 'both');
        } else {

            $this->db->select('course_id,crse_name,description,crse_icon,certi_level,crse_duration,language,pre_requisite');
        }

        $this->db->from('course');

        $this->db->where('crse_status', 'ACTIVE');

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

// skm code start for iframe

    public function get_course_list1() {

        $tenant_id = TENANT_ID;

        $this->db->from('course');

        $this->db->where('crse_status', 'ACTIVE');

        $this->db->where('tenant_id', $tenant_id);

        $this->db->where('display_on_portal', '1');

        $this->db->order_by('crse_name');

        return $this->db->get()->result();
    }

// skm code start for iframe





    /*





     * Function to return count of course list 

     */



    public function course_count($search_value = NULL) {

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

    /*





     * Function to return count of course class based on course_id, class_id and tenant_id

     */

    public function get_course_class_count($course_id, $class_id) {

        if (!empty($course_id) && !empty($class_id)) {

            $this->db->where('tenant_id', TENANT_ID);

            $this->db->where('course_id', $course_id);

            $this->db->where('class_id', $class_id);

            $this->db->from('class_enrol');

            return $this->db->count_all_results();
        }
    }

    /*





     * Function to return count of course class based on course_id and tenant_id

     */

    public function get_course_count($course_id) {

        //$date = date('Y-m-d');

        $date = date('Y-m-d h:i A');

        $time = date("H:i:s", strtotime($date));

        $today = date('Y-m-d') . ' ' . $time;



        if (!empty($course_id)) {

            $this->db->where('tenant_id', TENANT_ID);

            $this->db->where('course_id', $course_id);

            $this->db->where('class_status !=', 'INACTIV');

            //$this->db->where('DATE(class_end_datetime) >= ', $date);

            $this->db->where('class_start_datetime >= ', $today);

            $this->db->where('display_class_public', '1');

            $this->db->from('course_class');

            return $this->db->count_all_results();
        }
    }

    /**

     * Get the list of course class

     * @param string $tenant_id

     * @param int $limit

     * @param int $offset

     * @param string $sort_by

     * @param string $sort_order





     * @return array     





     */
    public function get_classes_by_date($class_date = NULL, $tenant_id, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL) {

        if ($offset < 0 || empty($tenant_id)) {

            return;
        }

        $date = date('Y-m-d h:i A');

        $time = date("H:i:s", strtotime($date));

        $today_date = date('Y-m-d') . ' ' . $time;





        $this->db->select('crs.crse_name,crs.crse_manager,c_cls.class_id, c_cls.course_id, c_cls.total_seats,

            c_cls.class_name, c_cls.class_start_datetime,c_cls.class_end_datetime, c_cls.description,

                c_cls.total_classroom_duration, c_cls.total_lab_duration,c_cls.class_fees,c_cls.classroom_trainer,





                c_cls.classroom_location, c_cls.lab_location,c_cls.class_language,c_cls.class_pymnt_enrol,c_cls.class_status,c_cls.training_aide');





        $this->db->from('course_class c_cls');

        $this->db->join('course crs', 'crs.course_id = c_cls.course_id');

        $this->db->where('crs.crse_status', 'ACTIVE');

        $this->db->where('c_cls.class_status !=', 'INACTIV');

//        $this->db->where('DATE(c_cls.class_start_datetime) <= ', $class_date);

        $this->db->where('c_cls.tenant_id', $tenant_id);

//        $this->db->where('DATE(c_cls.class_end_datetime) >= ', $class_date);

        $this->db->where('c_cls.class_start_datetime >= ', $today_date);



        $this->db->where('c_cls.display_class_public', '1');





        $this->db->where('crs.display_on_portal', '1');





        if ($sort_by) {

            $this->db->order_by($sort_by, $sort_order);
        } else {

            $this->db->order_by('c_cls.class_start_datetime');
        }





        $query = $this->db->get();

        return $query->result_array();
    }

    /*





     * Function to return all classes count based on the selected date

     */

    public function get_classes_by_date_count($class_date = NULL, $tenant_id) {

        if (empty($class_date)) {

            return FALSE;
        }
        $date = date('Y-m-d h:i A');

        $time = date("H:i:s", strtotime($date));

        $today_date = date('Y-m-d') . ' ' . $time;

        $this->db->select('count(*) as totalrows');

        $this->db->from('course_class c_cls');

        $this->db->join('course crs', 'crs.course_id = c_cls.course_id');

        $this->db->where('crs.crse_status', 'ACTIVE');

        $this->db->where('c_cls.class_status !=', 'INACTIV');





        $this->db->where('c_cls.display_class_public', '1');





        $this->db->where('display_on_portal', '1');





        $this->db->where('DATE(c_cls.class_start_datetime) <= ', $class_date);

        $this->db->where('c_cls.tenant_id', $tenant_id);

//          $this->db->where('c_cls.class_start_datetime >= ', $today_date);

        $this->db->where('DATE(c_cls.class_end_datetime) >= ', $class_date);

        $result = $this->db->get()->result();

        return $result[0]->totalrows;
    }

    /*





     * Function to return course name based on course id





     */

    public function get_course_name($course_id = NULL) {

        $tenant_id = TENANT_ID;

        if (!empty($course_id)) {

            $output = '';

            $this->db->select('crse_name');

            $this->db->from('course');

            $this->db->where('course_id', $course_id);



            $this->db->where('tenant_id', $tenant_id);



            $output = $this->db->get();

            return $output->row();
        }

        return NULL;
    }

    /*

     * Function to return class name based on course id and class id

     */

    public function class_name($course_id, $class_id) {

        $tenant_id = TENANT_ID;

        if (!empty($course_id)) {

            $output = '';

            $this->db->select('class_name');

            $this->db->from('course_class');

            $this->db->where('course_id', $course_id);

            $this->db->where('class_id', $class_id);

            $this->db->where('tenant_id', $tenant_id);



            $output = $this->db->get();

            return $output->row();
        }

        return NULL;
    }

    /*





     * 





     * Function to return list of course name

     */

    public function fetch_course_names_list($search_value = NULL) {

        $tenant_id = TENANT_ID;

        $output = '';

        if (!empty($search_value)) {

            $this->db->select('course_id,crse_name')->like('crse_name', $search_value, 'both');
        } else {

            $this->db->select('course_id,crse_name');
        }



        $this->db->from('course');



        $this->db->where('tenant_id', $tenant_id);



        $output = $this->db->get();

        return $output->result_array();
    }

    /*

     * function to return the autocomplete list of course based on the input recieved

     * @param string $query

     * @return array matches





     *





     */

    public function course_list_autocomplete($query = NULL) {

        $matches = array();

        $tenant_id = TENANT_ID;

        if (!empty($query)) {



            $results = $this->db->select('course_id, crse_name')
                            ->where('tenant_id', $tenant_id)
                            ->where('crse_status', 'ACTIVE')->where('display_on_portal', '1')->like('crse_name', $query, 'both')->get('course')->result();



            foreach ($results as $result) {

                $matches[$result->crse_name] = $result->crse_name;
            }
        }

        return $matches;
    }

    /*





     * 





     * Function returns count of classes based on selected date 

     */

    public function get_course_class_list_count($course_id = NULL, $tenant_id) {

        $date = date('Y-m-d');

        if (empty($course_id)) {

            return 0;
        }

        $this->db->select('count(*) as totalrows');

        $this->db->from('course_class c_cls');

        $this->db->join('course crs', 'crs.course_id = c_cls.course_id');

        $this->db->where('crs.crse_status', 'ACTIVE');

        $this->db->where('c_cls.class_status !=', 'INACTIV');





        $this->db->where('c_cls.display_class_public', '1');





        $this->db->where('display_on_portal', '1');





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





     * 





     */
    public function get_course_class_list($course_id = NULL, $tenant_id, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL) {

        //$date = date('Y-m-d');

        $date = date('Y-m-d h:i A');

//        echo "<br/>";

        $time = date("H:i:s", strtotime($date));

//        echo "<br/>";

        $today_date = date('Y-m-d') . ' ' . $time;

//        echo "<br/>";



        if ($offset < 0 || empty($tenant_id)) {

            return;
        }

        $this->db->select('crs.crse_name, crs.crse_manager,c_cls.class_id, c_cls.course_id, c_cls.total_seats, c_cls.classroom_trainer,c_cls.class_name, c_cls.class_start_datetime,c_cls.class_end_datetime, c_cls.description,c_cls.classroom_venue_oth,

                c_cls.total_classroom_duration, c_cls.total_lab_duration,c_cls.assmnt_duration,c_cls.class_fees,c_cls.classroom_location, c_cls.lab_location,c_cls.lab_venue_oth ,c_cls.class_language,c_cls.class_pymnt_enrol,c_cls.class_status,c_cls.training_aide');

        $this->db->from('course_class c_cls');

        $this->db->join('course crs', 'crs.course_id = c_cls.course_id and c_cls.tenant_id=crs.tenant_id');

        $this->db->where('crs.crse_status', 'ACTIVE');

        $this->db->where('c_cls.class_status !=', 'INACTIV');

        $this->db->where('crs.display_on_portal', '1');

        $this->db->where('c_cls.display_class_public', '1');

        if ($course_id != NULL) {

            $this->db->where('c_cls.course_id', $course_id);
        }

        $this->db->where('c_cls.tenant_id', $tenant_id);

//        $this->db->where('DATE(c_cls.class_end_datetime) >= ', $date);
        //$this->db->where('c_cls.class_end_datetime >= ', $date);

        $this->db->where('c_cls.class_start_datetime >= ', $today_date);

        //$this->db->where('c_cls.class_start_datetime <= ', $date);

        if ($sort_by) {

            $this->db->order_by($sort_by, $sort_order);
        } else {

            $this->db->order_by('c_cls.class_start_datetime', 'ASC');
        }

        $query = $this->db->get();

//     echo $this->db->last_query();

        return $query->result_array();
    }

    /*





     * Function  to  get the active  courses_class list     





     *   





     */

    public function get_active_course_class_list($tenant_id, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL, $search_value = NULL) {



        $date = date('Y-m-d h:i A');

        $time = date("H:i:s", strtotime($date));

        $today = date('Y-m-d') . ' ' . $time;



        if ($offset < 0 || empty($tenant_id)) {

            return;
        }

        if (!empty($search_value)) {

            $this->db->select('course_id,crse_name,crse_type,description,crse_icon,certi_level,crse_duration,language,pre_requisite')->like('crse_name', $search_value, 'both');
        } else {

            $this->db->select('course_id,crse_name,crse_type,description,crse_icon,certi_level,crse_duration,language,pre_requisite');
        }



        $this->db->from('course');

        $this->db->where('crse_status', 'ACTIVE');

        $this->db->where('tenant_id', $tenant_id);

        $this->db->where('display_on_portal', '1');



        if ($sort_by) {

            $this->db->order_by($sort_by, $sort_order);
        } else {

            $this->db->order_by('last_modified_on', 'DESC');
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

    public function available_course($tenant_id, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL, $search_value = NULL) {



        $date = date('Y-m-d h:i A');

        $time = date("H:i:s", strtotime($date));

        $today = date('Y-m-d') . ' ' . $time;



        if ($offset < 0 || empty($tenant_id)) {

            return;
        }

        if (!empty($search_value)) {

            $this->db->select('crs.course_id')->like('crs.crse_name', $search_value, 'both');
        } else {

            $this->db->select('crs.course_id');
        }



        $this->db->from('course crs');

        $this->db->join('course_class cc', 'cc.course_id = crs.course_id and cc.tenant_id=crs.tenant_id');

        $this->db->where('crs.crse_status', 'ACTIVE');

        $this->db->where('crs.crse_status', 'ACTIVE');

        $this->db->where('crs.tenant_id', $tenant_id);

        $this->db->where('crs.display_on_portal', '1');

        $this->db->where('cc.class_start_datetime >= ', $today);

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

        return $query->result();
    }

    public function get_active_course_class_junk($tenant_id, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL, $search_value = NULL) {



        $date = date('Y-m-d h:i A');

        $time = date("H:i:s", strtotime($date));

        $today = date('Y-m-d') . ' ' . $time;



        if ($offset < 0 || empty($tenant_id)) {

            return;
        }

        if (!empty($search_value)) {

            $this->db->select('crs.course_id,crs.crse_name,crs.description,crs.crse_icon,crs.certi_level,crs.crse_duration,crs.language,crs.pre_requisite')
                    ->like('crs.crse_name', $search_value, 'both');
        } else {

            $this->db->select('crs.course_id,crs.crse_name,crs.description,crs.crse_icon,crs.certi_level,crs.crse_duration,crs.language,crs.pre_requisite');
        }



        $this->db->from('course crs');

        $this->db->join('course_class cc', 'cc.course_id = crs.course_id and cc.tenant_id=crs.tenant_id');

        $this->db->where('crs.crse_status', 'ACTIVE');

        $this->db->where('crs.crse_status', 'ACTIVE');

        $this->db->where('crs.tenant_id', $tenant_id);

        $this->db->where('crs.display_on_portal', '1');

        $this->db->where('cc.class_start_datetime >= ', $today);

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

    /*





     * function used to display notifications in public portal

     */

    public function get_notifications() {

        $tenant_id = TENANT_ID;

        $date = date('Y-m-d');

        $this->db->select('*');

        $this->db->from('custom_notifications');

        $this->db->where('tenant_id', $tenant_id);

        $this->db->where_in('noti_type', array('LNDPGE', 'LNDEMAIL', 'LNDEMALDB'));

        $this->db->where('broadcast_from <=', $date);

        $where = "(broadcast_to >='" . date('Y-m-d') . "' OR broadcast_to is NULL )";

        $this->db->where($where);

        $output = $this->db->get();

        return $output->result();
    }

    /**

     * function to get user_id from taxcode

     */
    public function get_user_id_from_taxcode($taxcode) {

        $tenant_id = TENANT_ID;

        $user_id = $this->db->select('tu.user_id,tu.tax_code,tup.first_name, tup.last_name, tup.gender, '
                                . 'tup.personal_address_bldg, tup.personal_address_city,tup.personal_address_state,'
                                . 'tup.personal_address_country')->from('tms_users tu')
                        ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                        ->where(array('tu.tax_code' => $taxcode))->get()->row();

        return $user_id;
    }

    /**

     * This method gets the details of the user if present in the DB

     * NO TENANT CHECK - checks across all tenants

     * @param type $taxcode

     * @return type

     */
    public function validate_Taxcode($taxcode) {

        $tenant_id = TENANT_ID;

        $user_id = $this->db->select('tu.user_id,tu.tax_code,tup.first_name, tup.last_name, tup.gender, '
                                . 'tup.personal_address_bldg, tup.personal_address_city,tup.personal_address_state,'
                                . 'tup.personal_address_country')->from('tms_users tu')
                        ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                        ->where(array('tu.tax_code' => $taxcode))->get()->row();

        return $user_id;
    }

    /**





     * function to get user_data from taxcode   





     */
    public function get_user_data($user_id) { //Modified for CR03
        $tenant_id = TENANT_ID;

        $user_data = $this->db->select('tu.tenant_id,tu.tax_code,tu.user_id,tu.tax_code,tup.first_name, '
                                . 'tup.last_name, tup.gender, tup.personal_address_city,tup.personal_address_state,'
                                . 'tup.personal_address_country,tup.additional_remarks')->from('tms_users tu')
                        ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                        ->where(array('tu.user_id' => $user_id))
                        ->get()->row();

















        return $user_data;
    }

    /**





     * get admin userid





     * @return type





     */
    private function _get_admin_user_id() {





        $tenant_id = TENANT_ID;





        $this->db->select('tu.user_id');





        $this->db->from('tenant_master tm');





        $this->db->join('tms_users tu', 'tu.user_id = tm.tenant_org_id');





        $this->db->where('tm.tenant_id', $tenant_id);





        return $this->db->get()->row()->user_id;
    }

//  skm check nric exists or not start

    public function check_taxcode_exists($taxcode) {

        $this->db->select('*');

        $this->db->from('tms_users');

        $this->db->where('tax_code', $taxcode);

        $sql = $this->db->get();

//        echo $this->db->last_query();

        if ($sql->num_rows() > 0) {

            echo TRUE;

            return TRUE;
        } else {
            return FALSE;
        }
    }

//end

    public function check_taxcode_exists_cc($taxcode, $course_id, $class_id) {
        $tenant_id = TENANT_ID;
        $this->db->select('*');

        $this->db->from('tms_users');

        $this->db->where('tax_code', $taxcode);

        $this->db->where('tenant_id', $tenant_id);

        $sql = $this->db->get();

        $data = $sql->row();

        if ($sql->num_rows() > 0) {

            if ($data->account_status == 'ACTIVE') {
                $res = $this->nric_exits_cc($taxcode, $course_id, $class_id); // check wether trainee enrolled into class or not

                if ($res == 1) {
                    echo 2; // already enrolled
                } else {
                    echo 1; // not enrolled in class
                }
            } else {
                echo 3; // user exits in db but his status is not active
            }
        } else {

            echo 0;
        } exit();
    }

//end

    public function nric_exits_cc($taxcode, $course_id, $class_id) {

        $tenant_id = TENANT_ID;

        $this->db->select('*');

        $this->db->from('class_enrol ce');

        $this->db->join('tms_users tu', 'tu.user_id = ce.user_id');

        $this->db->where('ce.tenant_id', $tenant_id);

        $this->db->where('tu.tenant_id', $tenant_id);

        $this->db->where('tu.tax_code', $taxcode);

        $this->db->where('ce.course_id', $course_id);

        $this->db->where('ce.class_id', $class_id);

        $sql = $this->db->get();

//        echo $this->db->last_query();

        return $sql->num_rows();
    }

    //  skm check referal nric exists or not start

    public function check_referal_taxcode_exists($taxcode) {

        $this->db->select('*');

        $this->db->from('tms_users tu');

        $this->db->join('tms_users_pers tup', 'tu.user_id = tup.user_id');

        $this->db->where('tu.tax_code', $taxcode);

        $this->db->where('tu.tenant_id', TENANT_ID);

        $sql = $this->db->get();

//        echo $this->db->last_query();

        if ($sql->num_rows() > 0) {

            $sql = $sql->row();

            echo json_encode($sql);
        } else {

            echo 0;
        } exit();
    }

//end

    public function get_loggedin_taxcode($user_id) {

        $this->db->select('user_id,tax_code');

        $this->db->from('tms_users');

        $this->db->where('user_id', $user_id);

        $this->db->where('tenant_id', $this->session->userdata('userDetails')->tenant_id);///modified by shubhranshu

        $sql = $this->db->get()->row();

//        echo $this->db->last_query();

        return $sql->tax_code;
    }

// check existing user data deatils skm start

    public function check_existing_emailDetails($nirc, $argument, $radio) {

        $this->db->select('*');

        $this->db->from('tms_users');

        $this->db->where('tax_code', $nirc);

        $this->db->where('registered_email_id', $argument);

        $this->db->where('tenant_id', TENANT_ID);

        $sql = $this->db->get();

        if ($sql->num_rows() > 0) {

            echo 1;
        } else {

            echo 0;
        }

        exit();
    }

    public function check_existing_contactDetails($nric, $argument, $radio) {

        $this->db->select('*');

        $this->db->from('tms_users tu');

        $this->db->join('tms_users_pers tup', 'tu.user_id = tup.user_id');

        $this->db->where('tup.contact_number', $argument);

        $this->db->where('tu.tax_code', $nric);

        $this->db->where('tu.tenant_id', TENANT_ID);

        $sql = $this->db->get();

        if ($sql->num_rows() > 0) {



            echo 1;
        } else {

            echo 0;
        }

        exit();
    }

    public function check_existing_dobDetails($nric, $argument, $radio) {
        $date = explode("-", $argument);

        $dob = $date[2] . '-' . $date[1] . '-' . $date[0];



        $this->db->select('*');

        $this->db->from('tms_users tu');

        $this->db->join('tms_users_pers tup', 'tu.user_id = tup.user_id');

        $this->db->like('tup.dob', $dob);

        $this->db->where('tu.tax_code', $nric);

        $this->db->where('tu.tenant_id', TENANT_ID);

        $sql = $this->db->get();

//        echo $this->db->last_query();

        if ($sql->num_rows() > 0) {



            echo 1;
        } else {

            echo 0;
        }

        exit();
    }

// end
    // skm check referal user deatils start

    public function check_match_found($username, $argument, $radio) {

        $this->db->select('registered_email_id');

        $this->db->from('tms_users');

        $this->db->where('user_name', $username);

        $this->db->where('registered_email_id', $argument);

        $this->db->where('tenant_id', TENANT_ID);

        $sql = $this->db->get();

//        echo $this->db->last_query();

        if ($sql->num_rows() > 0) {

            echo 1;
        } else {

            echo 0;
        } exit();
    }

    public function check_match_found1($username, $argument, $radio) {

        $this->db->select('*');

        $this->db->from('tms_users tu');

        $this->db->join('tms_users_pers tup', 'tu.user_id = tup.user_id');

        if ($radio == 3) {

            $this->db->where('tup.first_name', $argument);
        }

        $this->db->where('tu.user_name', $username);

        if ($radio == 4) {

            $this->db->where('tup.contact_number', $argument);
        }



        $this->db->where('tu.tenant_id', TENANT_ID);

        $sql = $this->db->get();



        // echo $sql->num_rows();

        if ($sql->num_rows() > 0) {



            echo 1;
        } else {

            echo 0;
        }

        exit();
    }

    //end



    public function check_user_enrollment($user_id, $course_id, $class_id) {

        $tenant_id = TENANT_ID;



        $this->db->select('*')
                ->from('class_enrol')->where('tenant_id', $tenant_id)->where('course_id', $course_id)
                ->where('class_id', $class_id)->where('user_id', $user_id);

        $sql = $this->db->get();



        if ($sql->num_rows() > 0) {



            echo 1;
        } else {

            echo 0;
        }

        exit();
    }

    /**



     * method to save trainee mandatory field in refer a friend link  



     */
    public function save_imp_trainee() {



        $tenant_id = TENANT_ID;

        $taxcode_found = '';

        $ssr = '';

        $search_select = '';

        $loggedin = '';



        foreach ($this->input->post() as $key => $value) {

            $$key = $value;
        }



        $dateTime = date('Y-m-d H:i:s');

        $other_identi_type = NULL;

        $other_identi_code = NULL;

        if ($country_of_residence == 'IND') {

            $tax_code_type = 'PAN';

            $tax_code = $PAN;
        }

        if ($country_of_residence == 'SGP') {

            $tax_code_type = $NRIC;

            $tax_code = $NRIC_ID;

            if ($NRIC == "SNG_3") {

                $other_identi_type = $NRIC_OTHER;

                $other_identi_code = $tax_code;
            }
        }



        //s1 start

        if ($country_of_residence1 == 'SGP') {

            $r_tax_code_type = $r_NRIC;

            $r_tax_code = $r_NRIC_ID;

            if ($r_NRIC == "SNG_3") {

                $r_other_identi_type = $r_NRIC_OTHER;

                $r_other_identi_code = $r_tax_code;
            }
        }// s1 end



        if ($country_of_residence == 'USA') {

            $tax_code_type = 'SSN';

            $tax_code = $SSN;
        }

        $password = random_key_generation();

        $encrypted_password = $this->bcrypt->hash_password($password);

        // s2 start

        $r_password = random_key_generation();

        $r_encrypted_password = $this->bcrypt->hash_password($r_password);

        // s2 end



        $activation_key = NULL;



        $friend_relationship = NULL;

        if (!empty($relationship)) {

            if ($relationship == 'OTHERS') {

                $friend_relationship = $others;
            } else {

                $friend_relationship = $relationship;
            }
        }

// skm alternative code for referal email correct it later start

        if ($r_email != '' && $r_name != '' && $r_contact_number != '' && $country_of_residence1 != '') {

            $referal_email = $r_email;
        } elseif ($ref_email != '') {

            $referal_email = $r_email;
        } else {

            $referal_email = $r_email;
        }



        if ($ssr != '') {

            if ($ssr == 4 && $r_username != '') {

                $r_res = $this->user_model->check_tms_contact_no($r_username, $r_contact_number);
            } else if ($ssr == 3 && $r_name != '') {

                $r_res = $this->user_model->check_tmsuser_name($r_username, $r_name);
            } else if ($ssr == 2 && $r_email != '') {

                $r_res = $this->user_model->check_tmsuser_email($r_username, $r_email);
            } else if ($ssr == 2 && $ref_email != '') {

                $r_res = $this->user_model->check_tmsuser_email($r_username, $ref_email);
            }

//         echo $r_res.'value'; echo "<br/>";

            if ($r_res == 0) {

                return FALSE;
            } else {

                $r_user_result = $this->user_model->r_userDetails($r_res);
            }
        }



        if ($search_select != '' && $taxcode_found != '') {

            if ($search_select == 1 && $pers_dob != '') {

                $res = $this->user_model->check_tmsuser_dob($taxcode_found, $pers_dob);
            } else if ($search_select == 2 && $e_email != '') {

                $res = $this->user_model->check_tmsuser_eemail($taxcode_found, $e_email);
            } else if ($search_select == 3 && $e_contact_no != '') {

                $res = $this->user_model->check_tmsuser_econtact($taxcode_found, $e_contact_no);
            }

//        echo $res.'val'; echo "<br/>";

            if ($res == 0) {

                return FALSE;
            } else {

                $user_result = $this->user_model->r_userDetails($res);

//                print_r($user_result); 
//                echo"<br/>";
            }
        }

        if ($r_res != 0 && $res != 0) { //skm if referance and referal deatils found in db start

//            $e_user_details = array('username' => $user_result->user_name,
//            'email' => $user_result->registered_email_id, 'password' => $user_result->password,
//            'firstname' => strtoupper($user_result->first_name), 'lastname' => strtoupper($user_result->last_name),
//            'gender' => $user_result->gender);
//            
//            $r_user_details = array('username' => $user_result->user_name,
//            'email' => $r_user_result->registered_email_id, 'password' => $user_result->password,
//            'firstname' => strtoupper($first_name), 'lastname' => strtoupper($last_name),
//            'gender' => $pers_gender);
//            
//            // this email goes to referal which conatain details of referance start
//            $e_to_r_details = array('username' => $user_result->user_name,
//            'email' => $r_user_result->registered_email_id, 'password' => $r_user_result->password,
//            'firstname' => strtoupper($first_name), 'lastname' => strtoupper($last_name),
//            'gender' => $pers_gender);// end
//            
//            $this->send_trainee_email($r_user_details, 'BPEMAC');
//

//            $this->send_trainee_email($e_user_details, 'BPEMAC');
//            
//            //$this->send_trainee_email($e_to_r_details, 'BPEMAC');
//            
//            $this->send_referal_email($r_user_result->registered_email_id,$e_user_details, 'BPEMAC');
//            

            $data = array('tax_code' => $user_result->tax_code, 'user_id' => $res, 'friend_id' => $r_res);



            return $data;
        }







// skm alternative code for referal email start        

        $r_tms_users_data = array(
            'tenant_id' => $tenant_id,
            'account_type' => 'TRAINE',
            'registration_mode' => 'TRAINE',
            'friend_id' => NULL,
            'registration_date' => $dateTime,
            'user_name' => $r_username,
            'password' => $r_encrypted_password,
            'acc_activation_type' => 'BPEMAC',
            'activation_key' => $activation_key,
            'registered_email_id' => trim($referal_email),
            'country_of_residence' => trim($country_of_residence1),
            'tax_code_type' => $r_tax_code_type,
            'tax_code' => strtoupper($r_tax_code),
            'other_identi_type' => $r_other_identi_type,
            'other_identi_code' => strtoupper($r_other_identi_code),
            'other_identi_upload' => '',
            'acct_acti_date_time' => $dateTime,
            'acct_deacti_date_time' => NULL,
            'account_status' => "ACTIVE",
            'deacti_reason' => NULL,
            'deacti_reason_oth' => NULL,
            'deacti_by' => NULL,
            'created_by' => "",
            'created_on' => $dateTime,
            'last_modified_by' => NULL,
            'last_modified_on' => $dateTime,
            'last_login_date_time' => NULL,
            'last_ip_used' => NULL,
            'pwd_last_chgd_on' => NULL,
            'friend_relation' => NULL
        );

//print_r($r_tms_users_data);

        $tms_users_data = array(
            'tenant_id' => $tenant_id,
            'account_type' => 'TRAINE',
            'registration_mode' => 'TRAINE',
            'friend_id' => NULL,
            'registration_date' => $dateTime,
            'user_name' => $user_name,
            'password' => $encrypted_password,
            'acc_activation_type' => 'BPEMAC',
            'activation_key' => $activation_key,
            'registered_email_id' => trim($frnd_registered_email),
            'country_of_residence' => trim($country_of_residence),
            'tax_code_type' => $tax_code_type,
            'tax_code' => strtoupper($tax_code),
            'other_identi_type' => $other_identi_type,
            'other_identi_code' => strtoupper($other_identi_code),
            'other_identi_upload' => '',
            'acct_acti_date_time' => $dateTime,
            'acct_deacti_date_time' => NULL,
            'account_status' => "ACTIVE",
            'deacti_reason' => NULL,
            'deacti_reason_oth' => NULL,
            'deacti_by' => NULL,
            'created_by' => "",
            'created_on' => $dateTime,
            'last_modified_by' => NULL,
            'last_modified_on' => $dateTime,
            'last_login_date_time' => NULL,
            'last_ip_used' => NULL,
            'pwd_last_chgd_on' => NULL,
            'friend_relation' => $friend_relationship
        );



        if (!empty($user_id)) {// it happen when user looged in and enroll for some
            $tms_users_data['friend_id'] = $user_id;

//this is commented bcoz when logged in user enroll for someone then it insert into create dy tenent rather then the logged in user id 
//            if ($validate_tenant != TENANT_ID) {
//                $user_id = $this->_get_admin_user_id();
//            } end

            $tms_users_data['created_by'] = $user_id;

            $tms_users_data['last_modified_by'] = $user_id;
        }//end
        //$this->db->trans_start();

        if ($taxcode_found == '') {

            $this->db->insert('tms_users', $tms_users_data);

            $user_id = $this->db->insert_id();
        }





        if (!empty($r_username) && !empty($country_of_residence1) && !empty($r_contact_number) && $ssr == '') {

            $referal_code_work = 1;

            $this->db->insert('tms_users', $r_tms_users_data);

            $r_user_id = $this->db->insert_id();

//        echo $this->db->last_query(); echo"<br/>";
        }

        if (empty($tax_code)) {

            $this->db->where('user_id', $user_id);

            $update_data = array(
                'tax_code' => $user_id,
                'other_identi_type' => 'NOTAXCODE',
                'tax_code_type' => ($tax_code_type) ? $tax_code_type : 'SNG_3'
            );

            $this->db->update('tms_users', $update_data);
        }



        if (empty($r_tax_code)) {

            $this->db->where('user_id', $r_user_id);

            $update_data = array(
                'tax_code' => $r_user_id,
                'other_identi_type' => 'NOTAXCODE',
                'tax_code_type' => ($r_tax_code_type) ? $r_tax_code_type : 'SNG_3'
            );

            $this->db->update('tms_users', $update_data);
        }



        if (!empty($r_user_id)) {

            $this->db->where('user_id', $user_id);

            $r_update_data = array(
                'friend_id' => $r_user_id,
                'created_by' => $r_user_id,
                'last_modified_by' => $r_user_id,
            );

            $this->db->update('tms_users', $r_update_data);
        }



        if ($r_res != 0) {

            $this->db->where('user_id', $user_id);

            $r_update_data = array(
                'friend_id' => $r_user_result->user_id,
                'created_by' => $r_user_result->user_id,
                'last_modified_by' => $r_user_result->user_id
            );

            $this->db->update('tms_users', $r_update_data);
        }



        //if register some one when logged in start

        if ($friend_id != 0) {  //echo"friend id present"; 
            $this->db->where('user_id', $user_id);

            $r_update_data = array(
                'friend_id' => $friend_id,
                'created_by' => $friend_id,
                'last_modified_by' => $friend_id
            );

            $this->db->update('tms_users', $r_update_data);
        }



        $dob_date = NULL;

        $dob = (empty($dob)) ? NULL : date('Y-m-d', strtotime($dob));

        $tms_users_pers_data = array(
            'tenant_id' => $tenant_id,
            'user_id' => $user_id,
            'first_name' => strtoupper($pers_first_name),
            'dob' => $dob,
            'gender' => $pers_gender,
            'contact_number' => trim($pers_contact_number),
            'individual_discount' => NULL,
            'indi_setting_list_size' => NULL,
            'occupation_code' => $occupation,
            'highest_educ_level' => $highest_educ_level,
            'nationality' => $nationality,
            'additional_remarks' => trim($pers_additional_remarks), //CR03
        );



        $this->db->insert('tms_users_pers', $tms_users_pers_data);

        if ($referal_code_work == 1) {

            $r_tms_users_pers_data = array(
                'tenant_id' => $tenant_id,
                'user_id' => $r_user_id,
                'first_name' => strtoupper($r_name),
                'dob' => $dob,
                'contact_number' => trim($r_contact_number)
            );

            $this->db->insert('tms_users_pers', $r_tms_users_pers_data);
        }





//        $this->db->trans_complete();
//        if ($this->db->trans_status() === FALSE) {
//            return FALSE;
//        }

        $user_details = array('username' => $user_name,
            'email' => $frnd_registered_email, 'password' => $password,
            'firstname' => strtoupper($pers_first_name), 'lastname' => strtoupper($pers_second_name),
            'gender' => $pers_gender);



        $r_user_details = array('username' => $r_username,
            'email' => $r_email, 'password' => $r_password,
            'firstname' => strtoupper($r_name), 'lastname' => strtoupper($pers_second_name),
            'gender' => $pers_gender);



//        $this->send_trainee_email($user_details, 'BPEMAC'); // referance
//      $this->send_tenant_mail($user_details, 'BPEMAC');



        /* when user loggedin and enroll for some */

        if (!empty($loggedin) && !empty($this->session->userdata('userDetails')->user_id) && empty($res)) {

            $user_data = $this->user_model->r_userDetails($r_user_id);

            $r_someone = array(
                'firstname' => strtoupper($user_data->first_name),
                'lastname' => strtoupper($user_data->last_name),
                'email' => $user_data->registered_email_id
            );

            $this->send_reg_someone_referance_email($r_someone, $user_details, 'BPEMAC'); // referance

            $this->send_reg_someone_referal_email($r_someone, $user_details, 'BPEMAC'); // referance referal

            $this->send_tenant_mail($user_details, 'BPEMAC'); // tenent email
        }//end



        /* cond1 when refrenace  and referal both insert start */

        if ($user_id != '' && $r_user_id != '' && empty($loggedin)) {

            $r_someone = array('firstname' => strtoupper($r_name), 'lastname' => strtoupper($pers_second_name), 'email' => $r_email);

            $u_someone = array('firstname' => strtoupper($pers_first_name), 'lastname' => strtoupper($pers_second_name));



            $this->send_reg_someone_referance_email($r_someone, $user_details, 'BPEMAC'); // referance

            $this->send_reg_someone_referal_email($r_someone, $user_details, 'BPEMAC'); // referance referal

            $this->send_trainee_email($r_user_details, 'BPEMAC'); // referal



            $this->send_tenant_mail($user_details, 'BPEMAC'); // tenent email

            $this->send_tenant_mail($r_user_details, 'BPEMAC'); // tenent email
        }//end



        /* cond2 when refrenace insert and referal found start */

        if ($r_res != '' && $user_id != '' && $r_user_id == '') {

            $r_user_details = array('username' => $user_result->user_name,
                'email' => $r_user_result->registered_email_id, 'password' => $user_result->password,
                'firstname' => strtoupper($first_name), 'lastname' => strtoupper($last_name),
                'gender' => $pers_gender);



            $r_someone = array('firstname' => strtoupper($r_user_result->first_name),
                'lastname' => strtoupper($r_user_result->last_name),
                'email' => $r_user_result->registered_email_id);



            $this->send_reg_someone_referance_email($r_someone, $user_details, 'BPEMAC'); // referance

            $this->send_tenant_mail($user_details, 'BPEMAC'); // tenent email

            $this->send_reg_someone_referal_email($r_someone, $user_details, 'BPEMAC'); // referance referal
        }//end





        /* cond3 when refrenace found and referal data insert start */

        if (!empty($search_select) && $res != 0 && empty($ssr) && $r_res == '' && $r_user_id != '') {

            $e_user_details = array('username' => $user_result->user_name,
                'email' => $user_result->registered_email_id, 'password' => $user_result->password,
                'firstname' => strtoupper($user_result->first_name), 'lastname' => strtoupper($user_result->last_name),
                'gender' => $user_result->gender);



            $this->send_trainee_email($r_user_details, 'BPEMAC'); // REFERAL

            $this->send_tenant_mail($r_user_details, 'BPEMAC'); // tenent email



            $data = array('tax_code' => $user_result->tax_code, 'user_id' => $res, 'friend_id' => $r_user_id);

//            exit;
            // return $data;
        }//end
//        echo $search_select; echo "<br/>";
//        echo $taxcode_found; echo "<br/>";
//        echo $res; echo "<br/>";
//        print_r($res); echo "<br/>";
//        echo $r_user_id; echo "<br/>";
//        exit();
//        if($search_select!='' && $taxcode_found!='' && $res!='' && $r_user_id!='')// case3 : refrance found and referal inserted
//        {
//            $e_user_details = array('username' => $user_result->user_name,
//            'email' => $user_result->registered_email_id, 'password' => $user_result->password,
//            'firstname' => strtoupper($user_result->pers_first_name), 'lastname' => strtoupper($user_result->pers_second_name),
//            'gender' => $user_result->pers_gender);
//            
//            $this->send_trainee_email($e_user_details, 'BPEMAC'); // referance
//            $this->send_trainee_email($r_user_details, 'BPEMAC'); // REFERAL
//            $this->send_referal_email($r_email,$e_user_details, 'BPEMAC'); // referance referal 
//        }
//        
//        exit();



        if ($user_id != '' && $r_user_id == '' && $res == '' && $r_res == '') { // register when nric not found

            $this->send_tenant_mail($user_details, 'BPEMAC'); // tenent email

            $this->send_trainee_email($user_details, 'BPEMAC'); // referance
        }



        if (!empty($search_select) && $taxcode_found != '' && $user_id == '' && $r_user_id == '' && $res != '' && $r_res == '') { // register when nric found found

            $e_user_details = array('username' => $user_result->user_name,
                'email' => $user_result->registered_email_id, 'password' => $user_result->password,
                'firstname' => strtoupper($user_result->first_name), 'lastname' => strtoupper($user_result->last_name),
                'gender' => $user_result->pers_gender);

            //$this->send_trainee_email($e_user_details, 'BPEMAC'); // referance
            // no email send when try to register and nric found



            $data = array('tax_code' => $user_result->tax_code, 'user_id' => $user_result->user_id);
        }





//        // cond 5 when register
//        if(!empty($user_id) )
//        {
//            $this->send_trainee_email($r_user_details, 'BPEMAC');
//        }
//        
        //

//

    /* commented   start



          if(!empty($r_user_id))

          {

          $this->send_trainee_email($r_user_details, 'BPEMAC');

          }

          if($r_res!=0)

          {

          $user_details = array('username' => $user_name,

          'email' => $r_user_result->registered_email_id, 'password' => $password,

          'firstname' => strtoupper($pers_first_name), 'lastname' => strtoupper($pers_second_name),

          'gender' => $pers_gender);

          $this->send_trainee_email($user_details, 'BPEMAC');

          }

          if($res!=0)

          {

          $user_details = array('username' => $user_result->user_name,

          'email' => $user_result->registered_email_id, 'password' => $user_result->password,

          'firstname' => strtoupper($user_result->first_name), 'lastname' => strtoupper($user_result->last_name),

          'gender' => $user_result->pers_gender);

          $this->send_trainee_email($user_details, 'BPEMAC');

          }



          commnted end */



        if (!empty($user_id)) {

            $user_data = $this->user_model->r_userDetails($user_id);

            $data = array('tax_code' => $tax_code, 'user_id' => $user_id, 'friend_id' => $user_data->friend_id);
        } else if (!empty($r_user_id)) {

//         print_r($user_result);

            $data = array('tax_code' => $user_result->tax_code, 'user_id' => $user_result->user_id, 'friend_id' => $r_user_id);

//               print_r($data);
        }

        /*

         * 1. referance and referal both data insert into tbl then if codition work work.

         * 2. referance insert and referal data exists then if condition work.

         * 3. referance data exists and referal insert then else codition work.

         * 4. refrance and referal both exists in db then top condiotn work.

         * */





        return $data;
    }

// public function save_imp_trainee_skm() {
//            $status = TRUE;         
//        $tenant_id = TENANT_ID;
//        $taxcode_found = '';
//        $search_select = '';
//        $NRIC_ID = $taxcode_nric;
//       
//        foreach ($this->input->post() as $key => $value)
//        {
//         $$key = $value;
//        }
//

//        
//        $dateTime = date('Y-m-d H:i:s');
//        $other_identi_type = NULL;
//        $other_identi_code = NULL;
//        
//        if ($country_of_residence == 'SGP') {
//            $tax_code_type = $NRIC;
//           // $tax_code = $NRIC_ID;
//                $tax_code = $taxcode_nric;
//            if ($NRIC == "SNG_3") {
//                $other_identi_type = $NRIC_OTHER;
//                $other_identi_code = $tax_code;
//            }
//        }
//        
//        //s1 start
//        if ($country_of_residence1 == 'SGP') {
//            $r_tax_code_type = $r_NRIC;
//            $r_tax_code = $r_NRIC_ID;
//            $r_tax_code = r_nric;
//            if ($r_NRIC == "SNG_3") 
//            {
//                $r_other_identi_type = $r_NRIC_OTHER;
//                $r_other_identi_code = $r_tax_code;
//            }
//        }// s1 end
//        
//       
//        $password = random_key_generation();
//        $encrypted_password = $this->bcrypt->hash_password($password);
//

//

//        $activation_key = NULL;
//        
//        $friend_relationship = NULL;
//        if (!empty($relationship)) {
//            if ($relationship == 'OTHERS') { 
//                $friend_relationship = $others;
//            } else {
//                $friend_relationship = $relationship;
//            }
//        }
//        
//        if($search_select!='' && $taxcode_found!='')
//        {   
//            if($search_select==1 && $pers_dob !='')
//            {
//               $res = $this->user_model->check_tmsuser_dob($taxcode_found,$pers_dob);
//            }
//            else if($search_select==2 && $e_email !='')
//            {
//                $res = $this->user_model->check_tmsuser_eemail($taxcode_found,$e_email);
//            }
//            else if($search_select == 3 && $e_contact_no !='')
//            {
//                $res = $this->user_model->check_tmsuser_econtact($taxcode_found,$e_contact_no);
//            }
//

//                if($res == 0)
//                {
//                        return FALSE;
//                }
//                 else
//                    {
//                        $user_result = $this->user_model->r_userDetails($res);
//                    }
//        }
//        /* if refernace exists cond1 and referal not want to give his details*/
//        if($checkbox == '' && $res!=0 && !empty($user_result) && $res_found1==1)
//        {      
//            $data = array('tax_code'=>$user_result->tax_code,'user_id'=>$user_result->user_id,'status' => $status);
//            return $data;
//        }//end
//        
//        /* if refernace exists cond2 and referal want to give his details and NRIC found*/
//        if($checkbox == 1 && $r_nric!='' && $res!=0 && !empty($user_result) && $res_found1==1 && $r_NRIC =='' && $r_NRIC_OTHER =='')
//        {  
//            $result = $this->user_model->get_user_details($r_nric);
//            $r_user_id = $result->user_id;
//

//            $data = array('tax_code'=>$user_result->tax_code,'user_id'=>$user_result->user_id,'friend_id'=>$r_user_id,'status' => $status);
//            
//            return $data;
//            
//        }//end
//        
//       
//        $tms_users_data = array(
//            'tenant_id' => $tenant_id,
//            'account_type' => 'TRAINE',
//            'registration_mode' => 'TRAINE',
//            'friend_id' => NULL,
//            'registration_date' => $dateTime,
//            'user_name' => $user_name,
//            'password' => $encrypted_password,
//            'acc_activation_type' => 'BPEMAC',
//            'activation_key' => $activation_key,
//            'registered_email_id' => trim($frnd_registered_email),
//            'country_of_residence' => trim($country_of_residence),
//            'tax_code_type' => $tax_code_type,
//            'tax_code' => strtoupper($tax_code),
//            'other_identi_type' => $other_identi_type,
//            'other_identi_code' => strtoupper($other_identi_code),
//            'other_identi_upload' => '',
//            'acct_acti_date_time' => $dateTime,
//            'acct_deacti_date_time' => NULL,
//            'account_status' => "ACTIVE",
//            'deacti_reason' => NULL,
//            'deacti_reason_oth' => NULL,
//            'deacti_by' => NULL,
//            'created_by' => "",
//            'created_on' => $dateTime,
//            'last_modified_by' => NULL,
//            'last_modified_on' => $dateTime,
//            'last_login_date_time' => NULL,
//            'last_ip_used' => NULL,
//            'pwd_last_chgd_on' => NULL,
//            'friend_relation' => $friend_relationship
//            );
//        /* it check referal nric found or not start*/
//        if(!empty($r_res_one))
//        {
//            $r_res = $r_res_one;
//        }else if(!empty($r_res_zero)){ 
//            $r_res = $r_res_zero;
//        }//end
//        
//        /* when user want to give his details and his nric not found then user deatils should add into db*/
//        if($checkbox == 1 && $r_res == 0 && $r_NRIC !='')
//        {   
//            if ($country_of_residence1 == 'SGP') {
//                $r_tax_code_type = $r_NRIC;
//                //$r_tax_code = $r_NRIC_ID;
//                $r_tax_code = $r_nric;
//                if ($r_NRIC == "SNG_3") 
//                {
//                    $r_other_identi_type = $r_NRIC_OTHER;
//                    $r_other_identi_code = $r_tax_code;
//                }
//            }
//       
//            $r_password = random_key_generation();
//            $r_encrypted_password = $this->bcrypt->hash_password($r_password);
//                
//            $r_tms_users_data = array(
//                'tenant_id' => $tenant_id,
//                'account_type' => 'TRAINE',
//                'registration_mode' => 'TRAINE',
//                'friend_id' => NULL,
//                'registration_date' => $dateTime,
//                'user_name' => $r_tax_code,
//                'password' => $r_encrypted_password,
//                'acc_activation_type' => 'BPEMAC',
//                'activation_key' => $activation_key,
//                'registered_email_id' => trim($r_email),
//                'country_of_residence' => trim($country_of_residence1),
//                'tax_code_type' => $r_tax_code_type,
//                'tax_code' => strtoupper($r_tax_code),
//                'other_identi_type' => $r_other_identi_type,
//                'other_identi_code' => strtoupper($r_other_identi_code),
//                'other_identi_upload' => '',
//                'acct_acti_date_time' => $dateTime,
//                'acct_deacti_date_time' => NULL,
//                'account_status' => "ACTIVE",
//                'deacti_reason' => NULL,
//                'deacti_reason_oth' => NULL,
//                'deacti_by' => NULL,
//                'created_by' => "",
//                'created_on' => $dateTime,
//                'last_modified_by' => NULL,
//                'last_modified_on' => $dateTime,
//                'last_login_date_time' => NULL,
//                'last_ip_used' => NULL,
//                'pwd_last_chgd_on' => NULL,
//                'friend_relation' => NULL
//                );
//

//        }//end
//

//        //$this->db->trans_start();
//

//        $this->db->insert('tms_users', $tms_users_data);
//         $user_id = $this->db->insert_id();
//        
//       
//        
//        if($checkbox == 1 && $r_nric!='' && $r_res!='' && $r_NRIC =='' && $r_NRIC_OTHER =='')
//        {  
//            $result = $this->user_model->get_user_details($r_nric);
//            
//             $r_user_id = $result->user_id;
//        }
//       
//        if($checkbox == 1 && $r_res == 0 && $res_found == 0 || $checkbox == 1 && $r_res == 0 && $res_found == 1){
//             $referal_code_work = 1; 
//            $this->db->insert('tms_users', $r_tms_users_data);
//            $r_user_id = $this->db->insert_id();
//             }
//        
//        if(!empty($r_user_id) && $res_found==0)
//        {   
//                $this->db->where('user_id', $user_id);
//                $r_update_data = array(
//                                        'friend_id' => $r_user_id,
//                                        'created_by' => $r_user_id,
//                                        'last_modified_by' => $r_user_id,
//                                        );
//                $this->db->update('tms_users', $r_update_data);
////                echo $this->db->last_query(); echo "<br/>";
//        }
//

//        $dob_date = NULL;
//        $dob = (empty($dob)) ? NULL : date('Y-m-d', strtotime($dob));
//        $tms_users_pers_data = array(
//            'tenant_id' => $tenant_id,
//            'user_id' => $user_id,
//            'first_name' => strtoupper($pers_first_name),
//            'dob' => $dob,
//            'gender' => $pers_gender,
//            'contact_number' => trim($pers_contact_number),
//            'individual_discount' => NULL,
//            'indi_setting_list_size' => NULL,
//            'occupation_code' => $occupation,
//            'highest_educ_level' => $highest_educ_level,
//            'nationality' => $nationality,
//            'additional_remarks' => trim($pers_additional_remarks), //CR03
//        );
//       
//        $this->db->insert('tms_users_pers', $tms_users_pers_data);
//        
//        if( $referal_code_work == 1)
//        {    
////            echo $referal_code_work;
//            $r_tms_users_pers_data = array(
//                                            'tenant_id' => $tenant_id,
//                                            'user_id' => $r_user_id,
//                                            'first_name' => strtoupper($r_name),
//                                            'gender' => NULL,
//                                            'contact_number' => trim($r_contact),                                            
//                                            'individual_discount' => NULL,
//                                            'indi_setting_list_size' => NULL
//                                         );
//             $this->db->insert('tms_users_pers', $r_tms_users_pers_data);
////             echo $this->db->last_query();
//           
//

//        }
//        /* if refernace exists cond3 and referal want to give his details and NRIC not found*/
//        if($checkbox == 1 &&  $res!=0 && !empty($user_result) && $r_user_id!='' && $res_found1==1 && $r_NRIC !='' && $r_NRIC_OTHER !='')
//        {  
//            
//            $r_user_details = array('username' => $r_tax_code,
//            'email' => $r_email, 'password' => $r_password,
//            'firstname' => strtoupper($r_name));
//            $data = array('tax_code'=>$user_result->tax_code,'user_id'=>$user_result->user_id,'friend_id'=>$r_user_id, 'friend_pass' =>$r_password, 'status' => $status);
//            return $data;
//            
//        }//end
//        
//        
////        
////        $this->db->trans_complete();
////        if ($this->db->trans_status() === FALSE) {
////            $status = FALSE;
////        }
////       
////    
//        
//    
//      
//            $data = array(
//                        'tax_code'=>$tax_code,
//                        'user_id'=>$user_id,
//                        'user_pass' => $password,
//                        'friend_id'=>$r_user_id,
//                        'friend_pass' =>$r_password,
//                        'status' => $status
//                    );
//            return $data;
//        
//   
//

//    }



    public function save_imp_trainee_skm() {

        $status = TRUE;

//            extract($_POST);
//            print_r($_POST);
//            echo "<br/>";echo "<br/>";echo "<br/>";echo "<br/>";
////            exit();
////            

        $tenant_id = TENANT_ID;

        $taxcode_found = '';

        $search_select = '';

        $NRIC_ID = $taxcode_nric;



        foreach ($this->input->post() as $key => $value) {

            $$key = $value;
        }





        $dateTime = date('Y-m-d H:i:s');

        $other_identi_type = NULL;

        $other_identi_code = NULL;



        if ($country_of_residence == 'SGP') {

            $tax_code_type = $NRIC;

            // $tax_code = $NRIC_ID;

            $tax_code = $taxcode_nric;

            if ($NRIC == "SNG_3") {

                $other_identi_type = $NRIC_OTHER;

                $other_identi_code = $tax_code;
            }
        }







        //s1 start

        if ($country_of_residence1 == 'SGP') {

            $r_tax_code_type = $r_NRIC;

            $r_tax_code = $r_NRIC_ID;

            $r_tax_code = r_nric;

            if ($r_NRIC == "SNG_3") {

                $r_other_identi_type = $r_NRIC_OTHER;

                $r_other_identi_code = $r_tax_code;
            }
        }// s1 end





        $password = random_key_generation();

        $encrypted_password = $this->bcrypt->hash_password($password);





        $activation_key = NULL;



        $friend_relationship = NULL;

        if (!empty($relationship)) {

            if ($relationship == 'OTHERS') {

                $friend_relationship = $others;
            } else {

                $friend_relationship = $relationship;
            }
        }



//        if($search_select!='' && $taxcode_found!='')
//        {   
//
//            if($search_select==1 && $pers_dob !='')
//
//            {
//
//               $res = $this->user_model->check_tmsuser_dob($taxcode_found,$pers_dob);
//
//            }
//
//            else if($search_select==2 && $e_email !='')
//
//            {
//
//                $res = $this->user_model->check_tmsuser_eemail($taxcode_found,$e_email);
//
//            }
//
//            else if($search_select == 3 && $e_contact_no !='')
//
//            {
//
//                $res = $this->user_model->check_tmsuser_econtact($taxcode_found,$e_contact_no);
//
//            }
//
//
//
//                if($res == 0)
//
//                {
//
//                        return FALSE;
//
//                }
//
//                 else
//
//                    {
//
//                        $user_result = $this->user_model->r_userDetails($res);
//
//                    }
//
//        }
        $this->load->model('user_model');
        /* Check existing user details skm start */
        if ($res_found1 == 1 && $taxcode_found != '') {

            $res = $this->user_model->check_tmsuser_taxcode($taxcode_found);

            if ($res != 0) {

                $user_result = $this->user_model->existing_userDetails($res);
            }
        }//end


        /* if refernace exists cond1 and referal not want to give his details */

        if ($checkbox == '' && $res != 0 && !empty($user_result) && $res_found1 == 1) {

            $data = array('tax_code' => $user_result->tax_code, 'user_id' => $user_result->user_id, 'status' => $status);

            return $data;
        }//end



        /* if refernace exists cond2 and referal want to give his details and NRIC found */

        if ($checkbox == 1 && $r_nric != '' && $res != 0 && !empty($user_result) && $res_found1 == 1 && $r_NRIC == '' && $r_NRIC_OTHER == '') {

            $result = $this->user_model->get_user_details($r_nric);

            $r_user_id = $result->user_id;



            $data = array('tax_code' => $user_result->tax_code, 'user_id' => $user_result->user_id, 'friend_id' => $r_user_id, 'status' => $status);



            return $data;
        }//end





        $tms_users_data = array(
            'tenant_id' => $tenant_id,
            'account_type' => 'TRAINE',
            'registration_mode' => 'TRAINE',
            'friend_id' => NULL,
            'registration_date' => $dateTime,
            'user_name' => $user_name,
            'password' => $encrypted_password,
            'acc_activation_type' => 'BPEMAC',
            'activation_key' => $activation_key,
            'registered_email_id' => trim($frnd_registered_email),
            'country_of_residence' => trim($country_of_residence),
            'tax_code_type' => $tax_code_type,
            'tax_code' => strtoupper($tax_code),
            'other_identi_type' => $other_identi_type,
            'other_identi_code' => strtoupper($other_identi_code),
            'other_identi_upload' => '',
            'acct_acti_date_time' => $dateTime,
            'acct_deacti_date_time' => NULL,
            'account_status' => "ACTIVE",
            'deacti_reason' => NULL,
            'deacti_reason_oth' => NULL,
            'deacti_by' => NULL,
            'created_by' => "",
            'created_on' => $dateTime,
            'last_modified_by' => NULL,
            'last_modified_on' => $dateTime,
            'last_login_date_time' => NULL,
            'last_ip_used' => NULL,
            'pwd_last_chgd_on' => NULL,
            'friend_relation' => $friend_relationship
        );

        /* it check referal nric found or not start */

        if (!empty($r_res_one)) {

            $r_res = $r_res_one;
        } else if (!empty($r_res_zero)) {

            $r_res = $r_res_zero;
        }//end



        /* when user want to give his details and his nric not found then user deatils should add into db */

        if ($checkbox == 1 && $r_res == 0 && $r_NRIC != '') {

            if ($country_of_residence1 == 'SGP') {

                $r_tax_code_type = $r_NRIC;

                //$r_tax_code = $r_NRIC_ID;

                $r_tax_code = $r_nric;

                if ($r_NRIC == "SNG_3") {

                    $r_other_identi_type = $r_NRIC_OTHER;

                    $r_other_identi_code = $r_tax_code;
                }
            }



            $r_password = random_key_generation();

            $r_encrypted_password = $this->bcrypt->hash_password($r_password);



            $r_tms_users_data = array(
                'tenant_id' => $tenant_id,
                'account_type' => 'TRAINE',
                'registration_mode' => 'TRAINE',
                'friend_id' => NULL,
                'registration_date' => $dateTime,
                'user_name' => $r_tax_code,
                'password' => $r_encrypted_password,
                'acc_activation_type' => 'BPEMAC',
                'activation_key' => $activation_key,
                'registered_email_id' => trim($r_email),
                'country_of_residence' => trim($country_of_residence1),
                'tax_code_type' => $r_tax_code_type,
                'tax_code' => strtoupper($r_tax_code),
                'other_identi_type' => $r_other_identi_type,
                'other_identi_code' => strtoupper($r_other_identi_code),
                'other_identi_upload' => '',
                'acct_acti_date_time' => $dateTime,
                'acct_deacti_date_time' => NULL,
                'account_status' => "ACTIVE",
                'deacti_reason' => NULL,
                'deacti_reason_oth' => NULL,
                'deacti_by' => NULL,
                'created_by' => "",
                'created_on' => $dateTime,
                'last_modified_by' => NULL,
                'last_modified_on' => $dateTime,
                'last_login_date_time' => NULL,
                'last_ip_used' => NULL,
                'pwd_last_chgd_on' => NULL,
                'friend_relation' => NULL
            );
        }//end
        //$this->db->trans_start();

         ////Added by shubhranshu if the first NRIC not exist then only need to insert data
        if(empty($taxcode_found)){
            $this->db->insert('tms_users', $tms_users_data);
            $user_id = $this->db->insert_id();
        }
        //////////////////////shubhranshu code end
        

        





        // if referal nric exists and found 
        //if($checkbox == 1 && $r_nric!='' && $r_res==1 && $r_NRIC && $r_NRIC_OTHER =='')

        if ($checkbox == 1 && $r_nric != '' && $r_res == 1 && $r_NRIC_OTHER == '') {

            $result = $this->user_model->get_user_details($r_nric);



            $r_user_id = $result->user_id;
        }



        if ($checkbox == 1 && $r_res == 0 && $res_found == 0 || $checkbox == 1 && $r_res == 0 && $res_found == 1) {

            $referal_code_work = 1;

            $this->db->insert('tms_users', $r_tms_users_data);

            $r_user_id = $this->db->insert_id();
        }



        if (!empty($r_user_id) && $res_found == 0) {

            $this->db->where('user_id', $user_id);

            $r_update_data = array(
                'friend_id' => $r_user_id,
                'created_by' => $r_user_id,
                'last_modified_by' => $r_user_id,
            );

            $this->db->update('tms_users', $r_update_data);

//                echo $this->db->last_query(); echo "<br/>";
        }



        $dob_date = NULL;

        $dob = (empty($dob)) ? NULL : date('Y-m-d', strtotime($dob));

        $tms_users_pers_data = array(
            'tenant_id' => $tenant_id,
            'user_id' => $user_id,
            'first_name' => strtoupper($pers_first_name),
            'dob' => $dob,
            'gender' => $pers_gender,
            'contact_number' => trim($pers_contact_number),
            'individual_discount' => NULL,
            'indi_setting_list_size' => NULL,
            'occupation_code' => $occupation,
            'highest_educ_level' => $highest_educ_level,
            'nationality' => $nationality,
            'additional_remarks' => trim($pers_additional_remarks), //CR03
        );


       ////Added by shubhranshu if the first NRIC not exist then only need to insert data
        if(empty($taxcode_found)){
             $this->db->insert('tms_users_pers', $tms_users_pers_data);
        }
        //////////////////////shubhranshu code end
       



        if ($referal_code_work == 1) {

//            echo $referal_code_work;

            $r_tms_users_pers_data = array(
                'tenant_id' => $tenant_id,
                'user_id' => $r_user_id,
                'first_name' => strtoupper($r_name),
                'gender' => NULL,
                'contact_number' => trim($r_contact),
                'individual_discount' => NULL,
                'indi_setting_list_size' => NULL
            );

            $this->db->insert('tms_users_pers', $r_tms_users_pers_data);
        }

        /* if refernace exists cond3 and referal want to give his details and NRIC not found */

        //if($checkbox == 1 &&  $res!=0 && !empty($user_result) && $r_user_id!='' && $res_found1==1 && $r_NRIC !='' && $r_NRIC_OTHER !='')

        if ($checkbox == 1 && $res != 0 && !empty($user_result) && $r_user_id != '' && $res_found1 == 1 && $r_NRIC != '') {



            $r_user_details = array('username' => $r_tax_code,
                'email' => $r_email, 'password' => $r_password,
                'firstname' => strtoupper($r_name));

            $data = array('tax_code' => $user_result->tax_code, 'user_id' => $user_result->user_id, 'friend_id' => $r_user_id, 'friend_pass' => $r_password, 'status' => $status);

            return $data;
        }//end
//        
//        $this->db->trans_complete();
//        if ($this->db->trans_status() === FALSE) {
//            $status = FALSE;
//        }
//       
//    







        $data = array(
            'tax_code' => $tax_code,
            'user_id' => $user_id,
            'user_pass' => $password,
            'friend_id' => $r_user_id,
            'friend_pass' => $r_password,
            'status' => $status
        );



        return $data;
    }

    /* This method enrollsome one user Loggedin */

    public function loggedin_enroll_someone() {



        $tenant_id = TENANT_ID;

        $taxcode_found = '';

        $ssr = '';

        $search_select = '';

        $loggedin = '';



        foreach ($this->input->post() as $key => $value) {

            $$key = $value;
        }



        $dateTime = date('Y-m-d H:i:s');

        $other_identi_type = NULL;

        $other_identi_code = NULL;



        if ($country_of_residence == 'SGP') {

            $tax_code_type = $NRIC;

            $NRIC_ID = $taxcode_nric; // have to check in dofferent conditions



            $tax_code = $NRIC_ID;

            if ($NRIC == "SNG_3") {

                $other_identi_type = $NRIC_OTHER;

                $other_identi_code = $tax_code;
            }
        }



        $password = random_key_generation();

        $encrypted_password = $this->bcrypt->hash_password($password);





        $activation_key = NULL;



        $friend_relationship = NULL;

        if (!empty($relationship)) {

            if ($relationship == 'OTHERS') {

                $friend_relationship = $others;
            } else {

                $friend_relationship = $relationship;
            }
        }



//    if($search_select!='' && $taxcode_found!='')
//    {   
//
//        if($search_select==1 && $pers_dob !='')
//
//        {
//
//           $res = $this->user_model->check_tmsuser_dob($taxcode_found,$pers_dob);
//
//        }
//
//        else if($search_select==2 && $e_email !='')
//
//        {
//
//            $res = $this->user_model->check_tmsuser_eemail($taxcode_found,$e_email);
//
//        }
//
//        else if($search_select == 3 && $e_contact_no !='')
//
//        {
//
//            $res = $this->user_model->check_tmsuser_econtact($taxcode_found,$e_contact_no);
//
//        }
//
//        if($res == 0)
//
//        {
//
//                return FALSE;
//
//        }
//
//         else
//
//            {
//
//                $user_result = $this->user_model->r_userDetails($res);
//
//
//
//            }
//
//    }  

        /* Check existing user details skm start */
        if ($res_found1 == 1 && $taxcode_found != '') {

            $res = $this->user_model->check_tmsuser_taxcode($taxcode_found);

            if ($res != 0) {

                $user_result = $this->user_model->existing_userDetails($res);
            }
        }//end


        if ($res_found1 == 1 && !empty($res) && $user_result != '' && empty($r_user_id)) {

            $data = array('tax_code' => $user_result->tax_code, 'user_id' => $user_result->user_id, 'friend_id' => '');

            return $data;
        }


        /* When referral loggedin and refrance NRIC found */
        if (!empty($r_user_id) && $res_found1 == 1 && !empty($res) && $user_result != '') {

            $data = array('tax_code' => $user_result->tax_code, 'user_id' => $user_result->user_id, 'friend_id' => $r_user_id);

            return $data;
        }//end



        $tms_users_data = array(
            'tenant_id' => $tenant_id,
            'account_type' => 'TRAINE',
            'registration_mode' => 'TRAINE',
            'friend_id' => NULL,
            'registration_date' => $dateTime,
            'user_name' => $user_name,
            'password' => $encrypted_password,
            'acc_activation_type' => 'BPEMAC',
            'activation_key' => $activation_key,
            'registered_email_id' => trim($frnd_registered_email),
            'country_of_residence' => trim($country_of_residence),
            'tax_code_type' => $tax_code_type,
            'tax_code' => strtoupper($tax_code),
            'other_identi_type' => $other_identi_type,
            'other_identi_code' => strtoupper($other_identi_code),
            'other_identi_upload' => '',
            'acct_acti_date_time' => $dateTime,
            'acct_deacti_date_time' => NULL,
            'account_status' => "ACTIVE",
            'deacti_reason' => NULL,
            'deacti_reason_oth' => NULL,
            'deacti_by' => NULL,
            'created_by' => "",
            'created_on' => $dateTime,
            'last_modified_by' => NULL,
            'last_modified_on' => $dateTime,
            'last_login_date_time' => NULL,
            'last_ip_used' => NULL,
            'pwd_last_chgd_on' => NULL,
            'friend_relation' => $friend_relationship
        );

        $user_id = $r_user_id;

        if (!empty($user_id)) {// it happen when user looged in and enroll for some
            $tms_users_data['friend_id'] = $user_id;

            $tms_users_data['created_by'] = $user_id;

            $tms_users_data['last_modified_by'] = $user_id;
        }//end



        $this->db->trans_start();

        if ($taxcode_found == '') {

            $this->db->insert('tms_users', $tms_users_data);

            $user_id = $this->db->insert_id();
        }



        $dob_date = NULL;

        $dob = (empty($dob)) ? NULL : date('Y-m-d', strtotime($dob));

        $tms_users_pers_data = array(
            'tenant_id' => $tenant_id,
            'user_id' => $user_id,
            'first_name' => strtoupper($pers_first_name),
            'dob' => $dob,
            'gender' => $pers_gender,
            'contact_number' => trim($pers_contact_number),
            'individual_discount' => NULL,
            'indi_setting_list_size' => NULL,
            'occupation_code' => $occupation,
            'highest_educ_level' => $highest_educ_level,
            'nationality' => $nationality,
            'additional_remarks' => trim($pers_additional_remarks), //CR03
        );



        $this->db->insert('tms_users_pers', $tms_users_pers_data);



        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            return FALSE;
        }



        $user_details = array('username' => $user_name,
            'email' => $frnd_registered_email, 'password' => $password,
            'firstname' => strtoupper($pers_first_name), 'lastname' => strtoupper($pers_second_name),
            'gender' => $pers_gender);





        /* when user loggedin and enroll for some */

  
        $this->send_reg_someone_referance_email($r_someone, $user_details, 'BPEMAC'); // send mail
               
        $data = array(
            'tax_code' => $tax_code,
            'user_id' => $user_id,
            'user_password' => $password,
            'friend_id' => $r_user_id,
            'status' => TRUE
        );



        return $data;

//end
//      return $data;
    }

//end

    public function save_reg_trainee() {



        $tenant_id = TENANT_ID;



        foreach ($this->input->post() as $key => $value) {

            $$key = $value;
        }





        $dateTime = date('Y-m-d H:i:s');

        $other_identi_type = NULL;

        $other_identi_code = NULL;

        if ($country_of_residence == 'IND') {

            $tax_code_type = 'PAN';

            $tax_code = $PAN;
        }

        if ($country_of_residence == 'SGP') {

            $tax_code_type = $NRIC;

            $tax_code = $NRIC_ID;

            if ($NRIC == "SNG_3") {

                $other_identi_type = $NRIC_OTHER;

                $other_identi_code = $tax_code;
            }
        }





        $password = random_key_generation();

        $encrypted_password = $this->bcrypt->hash_password($password);





        $activation_key = NULL;



        $friend_relationship = NULL;

        if (!empty($relationship)) {

            if ($relationship == 'OTHERS') {

                $friend_relationship = $others;
            } else {

                $friend_relationship = $relationship;
            }
        }







        $tms_users_data = array(
            'tenant_id' => $tenant_id,
            'account_type' => 'TRAINE',
            'registration_mode' => 'TRAINE',
            'friend_id' => NULL,
            'registration_date' => $dateTime,
            'user_name' => $user_name,
            'password' => $encrypted_password,
            'acc_activation_type' => 'BPEMAC',
            'activation_key' => $activation_key,
            'registered_email_id' => trim($frnd_registered_email),
            'country_of_residence' => trim($country_of_residence),
            'tax_code_type' => $tax_code_type,
            'tax_code' => strtoupper($tax_code),
            'other_identi_type' => $other_identi_type,
            'other_identi_code' => strtoupper($other_identi_code),
            'other_identi_upload' => '',
            'acct_acti_date_time' => $dateTime,
            'acct_deacti_date_time' => NULL,
            'account_status' => "ACTIVE",
            'deacti_reason' => NULL,
            'deacti_reason_oth' => NULL,
            'deacti_by' => NULL,
            'created_by' => "",
            'created_on' => $dateTime,
            'last_modified_by' => NULL,
            'last_modified_on' => $dateTime,
            'last_login_date_time' => NULL,
            'last_ip_used' => NULL,
            'pwd_last_chgd_on' => NULL,
            'friend_relation' => $friend_relationship
        );



        if ($taxcode_found == '') {

            $this->db->insert('tms_users', $tms_users_data);

            $user_id = $this->db->insert_id();
        }





        if (empty($tax_code)) {

            $this->db->where('user_id', $user_id);

            $update_data = array(
                'tax_code' => $user_id,
                'other_identi_type' => 'NOTAXCODE',
                'tax_code_type' => ($tax_code_type) ? $tax_code_type : 'SNG_3'
            );

            $this->db->update('tms_users', $update_data);
        }



        $dob_date = NULL;

        $dob = (empty($dob)) ? NULL : date('Y-m-d', strtotime($dob));

        $tms_users_pers_data = array(
            'tenant_id' => $tenant_id,
            'user_id' => $user_id,
            'first_name' => strtoupper($pers_first_name),
            'dob' => $dob,
            'gender' => $pers_gender,
            'contact_number' => trim($pers_contact_number),
            'individual_discount' => NULL,
            'indi_setting_list_size' => NULL,
            'occupation_code' => $occupation,
            'highest_educ_level' => $highest_educ_level,
            'nationality' => $nationality,
            'additional_remarks' => trim($pers_additional_remarks), //CR03
        );



        $this->db->insert('tms_users_pers', $tms_users_pers_data);

//         echo $this->db->last_query();



        $user_details = array('username' => $user_name,
            'email' => $frnd_registered_email, 'password' => $password,
            'firstname' => strtoupper($pers_first_name), 'lastname' => strtoupper($pers_second_name),
            'gender' => $pers_gender);



        // register when nric not found

        if ($user_id != '') {

            //$this->send_tenant_mail($user_details, 'BPEMAC'); // tenent email

            $this->send_trainee_email($user_details, 'BPEMAC'); // REFERAL
        }

        return $user_id;
    }

    /*

     * This methord for sending the email to the user (Copied from usermodel).





     * 





     */

    //private function send_trainee_email($user, $bypassemail) {

    public function send_trainee_email($user, $bypassemail) {

        $tenant_details = fetch_tenant_details(TENANT_ID);

        $footer_data = str_replace("<Tenant_Company_Name>", $tenant_details->tenant_name, MAIL_FOOTER);





        $footer_data = str_replace("<Tenant_Company_Email>", $tenant_details->tenant_email_id, $footer_data);

        $subject = NULL;

        $body = NULL;

        if ($user['gender'] == 'MALE') {

            $body = "Dear Mr. " . $user['firstname'] . ' ' . $user['lastname'] . ',';
        } elseif ($user['gender'] == '') {

            $body .="Dear Mr./Ms. " . $user['firstname'] . ' ' . $user['lastname'] . ',';
        } else {

            $body .="Dear Ms. " . $user['firstname'] . ' ' . $user['lastname'] . ',';
        }



        $subject = 'Your Account Activation Acknowledgment';

        $body .= "<br/><br/>Thank you for registering with us at <b>'" . $tenant_details->tenant_name . "'</b>.Your Account has been successfully created.<br/>";



        $body .= "<br/><br/><strong>Your Username:</strong> " . $user['username'] . "<br/>";

        $body .= "<strong>Your Password:</strong> " . $user['password'] . "<br/><br/>";





        $body .= "<strong>You can access the TMS portal at <a href='" . base_url() . "'>" . base_url() . "</a></strong><br/><br/>";

        $body .= $footer_data;

        return send_mail($user['email'], '', $subject, $body);
    }

    // This method for referal email which contains information about refrance
    //private function send_referal_email($r_email, $user, $bypassemail) {

    public function send_referal_email($r_email, $user, $bypassemail) {

        $tenant_details = fetch_tenant_details(TENANT_ID);

        $footer_data = str_replace("<Tenant_Company_Name>", $tenant_details->tenant_name, MAIL_FOOTER);





        $footer_data = str_replace("<Tenant_Company_Email>", $tenant_details->tenant_email_id, $footer_data);

        $subject = NULL;

        $body = NULL;

        if ($user['gender'] == 'MALE') {

            $body = "Dear Mr. " . $user['firstname'] . ' ' . $user['lastname'] . ',';
        } elseif ($user['gender'] == '') {

            $body .="Dear Mr./Ms. " . $user['firstname'] . ' ' . $user['lastname'] . ',';
        } else {

            $body .="Dear Ms. " . $user['firstname'] . ' ' . $user['lastname'] . ',';
        }



        $subject = 'Your Account Activation Acknowledgment';

        $body .= "<br/><br/>Thank you for registering with us at <b>'" . $tenant_details->tenant_name . "'</b>.Your Account has been successfully created.<br/>";



        $body .= "<br/><br/><strong>Your Username:</strong> " . $user['username'] . "<br/>";

        $body .= "<strong>Your Password:</strong> " . $user['password'] . "<br/><br/>";





        $body .= "<strong>You can access the TMS portal at <a href='" . base_url() . "'>" . base_url() . "</a></strong><br/><br/>";

        $body .= $footer_data;

        return send_mail($r_email, '', $subject, $body);
    }

    /* when register from enroll for some one cond1 start  */

    //private function send_reg_someone_referance_email($r_user, $user, $bypassemail) {

    public function send_reg_someone_referance_email($r_user, $user, $bypassemail) {

        $tenant_details = fetch_tenant_details(TENANT_ID);

        $footer_data = str_replace("<Tenant_Company_Name>", $tenant_details->tenant_name, MAIL_FOOTER);





        $footer_data = str_replace("<Tenant_Company_Email>", $tenant_details->tenant_email_id, $footer_data);

        $subject = NULL;

        $body = NULL;

        if ($user['gender'] == 'MALE') {

            $body = "Dear Mr. " . $user['firstname'] . ' ' . $user['lastname'] . ',';
        } elseif ($user['gender'] == '') {

            $body .="Dear Mr./Ms. " . $user['firstname'] . ' ' . $user['lastname'] . ',';
        } else {

            $body .="Dear Ms. " . $user['firstname'] . ' ' . $user['lastname'] . ',';
        }



        $subject = 'Your Account Activation Acknowledgment';

        $body .= "<br/><br/>Thank you for registering with us at <b>'" . $tenant_details->tenant_name . "'</b>.Your Account has been successfully created by " . $r_user['firstname'] . ' ' . $r_user['lastname'] . "<br/>";



        $body .= "<br/><br/><strong>Your Username:</strong> " . $user['username'] . "<br/>";

        $body .= "<strong>Your Password:</strong> " . $user['password'] . "<br/><br/>";





        $body .= "<strong>You can access the TMS portal at <a href='" . base_url() . "'>" . base_url() . "</a></strong><br/><br/>";

        $body .= $footer_data;

        return send_mail($user['email'], '', $subject, $body);
    }

// correct
    //private function send_reg_someone_referal_email($r_user, $user, $bypassemail) {

    public function send_reg_someone_referal_email($r_user, $user, $bypassemail) {

        $tenant_details = fetch_tenant_details(TENANT_ID);

        $footer_data = str_replace("<Tenant_Company_Name>", $tenant_details->tenant_name, MAIL_FOOTER);





        $footer_data = str_replace("<Tenant_Company_Email>", $tenant_details->tenant_email_id, $footer_data);

        $subject = NULL;

        $body = NULL;

//        if ($user['gender'] == 'MALE') {
//            $body = "Dear Mr. " . $r_user['firstname'] . ' ' . $r_user['lastname'] . ',';
//        } elseif($user['gender'] == '') {
//            $body .="Dear Mr./Ms. " . $r_user['firstname'] . ' ' . $r_user['lastname'] . ',';
//        }
//        else
//        {
//            $body .="Dear Ms. " . $r_user['firstname'] . ' ' . $r_user['lastname'] . ',';
//        }



        $body .="Dear Mr./Ms. " . $r_user['firstname'] . ' ' . $r_user['lastname'] . ',';



        $subject = $user['firstname'] . ' Account Activation Acknowledgment';

        $body .= "<br/><br/>You have Successfully  Registred " . $user['firstname'] . ' ' . $user['lastname'] . '' . "with<b>" . $tenant_details->tenant_name . "</b>";

        $body .= " and ";

        if ($user['gender'] == 'MALE') {

            $body .="his";
        } else if ($user['gender'] == 'FEMALE') {

            $body .="her";
        } else {

            $body .="his/her";
        }

        $body .= " login credentials are :";



        $body .= "<br/><br/><strong> Username:</strong> " . $user['username'] . "<br/>";

        $body .= "<strong> Password:</strong> " . $user['password'] . "<br/><br/>";



        $body .= "<strong>You can access the TMS portal at <a href='" . base_url() . "'>" . base_url() . "</a></strong><br/><br/>";

        $body .= $footer_data;



        return send_mail($r_user['email'], '', $subject, $body);

//        exit;
    }

//    private function send_reg_someone_referal_email1($r_user, $user, $bypassemail) {
//        $tenant_details = fetch_tenant_details(TENANT_ID);
//        $footer_data = str_replace("<Tenant_Company_Name>", $tenant_details->tenant_name, MAIL_FOOTER);
//

//

//        $footer_data = str_replace("<Tenant_Company_Email>", $tenant_details->tenant_email_id, $footer_data);
//        $subject = NULL;
//        $body = NULL;
//        if ($user['gender'] == 'MALE') {
//            $body = "Dear Mr. " . $r_user['firstname'] . ' ' . $r_user['lastname'] . ',';
//        } elseif($user['gender'] == '') {
//            $body .="Dear Mr./Ms. " . $r_user['firstname'] . ' ' . $r_user['lastname'] . ',';
//        }
//        else
//        {
//            $body .="Dear Ms. " . $r_user['firstname'] . ' ' . $r_user['lastname'] . ',';
//        }
//

//        $subject = 'Your Account Activation Acknowledgment';
//        $body .= "<br/><br/>You Enrolled ".$user['firstname'].' '.$user['lastname'].''."with <b>". $tenant_details->tenant_name . "</b>";
//    
//        $body .= "<br/><br/>";
////        $body .= "<strong>Your Password:</strong> " . $user['password'] . "<br/><br/>";
//       
//

//        $body .= "<strong>You can access the TMS portal at <a href='" . base_url() . "'>" . base_url() . "</a></strong><br/><br/>";
//        $body .= $footer_data;
//        
//        return send_mail($r_user['email'], '', $subject, $body);
////        exit;
//    }
    //end



    /* when enroll in course and class from enroll for some one start  */

    public function send_referance_email_someone($userDetails, $bypassemail) {

        $tenant_details = fetch_tenant_details(TENANT_ID);

        $footer_data = str_replace("<Tenant_Company_Name>", $tenant_details->tenant_name, MAIL_FOOTER);





        $footer_data = str_replace("<Tenant_Company_Email>", $tenant_details->tenant_email_id, $footer_data);

        $subject = NULL;

        $body = NULL;

        if ($userDetails['gender'] == 'MALE') {

            $body = "Dear Mr. " . $userDetails['firstname'] . ' ' . $userDetails['lastname'] . ',';
        } elseif ($user['gender'] == '') {

            $body .="Dear Mr./Ms. " . $userDetails['firstname'] . ' ' . $userDetails['lastname'] . ',';
        } else {

            $body .="Dear Ms. " . $userDetails['firstname'] . ' ' . $userDetails['lastname'] . ',';
        }



        $subject = 'Your Booking Acknowledgment';



        $body .= "<br/><br/>You are successufully enrolled by <b>" . $userDetails['r_firstname'] . ' ' . $userDetails['lastname'] . ' </b>';

        $body .= "in following course: <br/><br/>";

        $body .= "<strong>Course:</strong> " . $userDetails['course_name'] . ' ' . '<br/>';

        $body .="<strong>Class:</strong> " . $userDetails['class_name'] . ' ' . '<br/>';



        $body .= "<br/><strong>Class Start Date:</strong> " . $userDetails['class_start'] . "<br/>";

        $body .= "<strong>Class End Date-Time:</strong> " . $userDetails['class_end'] . "<br/><br/>";

        $body .= "<strong>Classroom Venue:</strong> " . $userDetails['class_venue'] . "<br/><br/>";





        $body .= "<strong>You can access the TMS portal at <a href='" . base_url() . "'>" . base_url() . "</a></strong><br/><br/>";

        $body .= $footer_data;

        return send_mail($userDetails['email'], '', $subject, $body);
    }

    public function send_referal_email_someone($userDetails, $bypassemail) {

        $tenant_details = fetch_tenant_details(TENANT_ID);

        $footer_data = str_replace("<Tenant_Company_Name>", $tenant_details->tenant_name, MAIL_FOOTER);





        $footer_data = str_replace("<Tenant_Company_Email>", $tenant_details->tenant_email_id, $footer_data);

        $subject = NULL;

        $body = NULL;

//        if ($userDetails['gender'] == 'MALE') {
//            $body = "Dear Mr. " . $userDetails['r_firstname'] . ' ' . $userDetails['r_lastname'] . ',';
//        } elseif($user['gender'] == '') {
//            $body .="Dear Mr./Ms. " . $userDetails['r_firstname'] . ' ' . $userDetails['r_lastname'] . ',';
//        }
//        else
//        {
//            $body .="Dear Ms. " . $userDetails['r_firstname'] . ' ' . $userDetails['r_lastname'] . ',';
//        }



        $body .="Dear Mr./Ms. " . $userDetails['r_firstname'] . ' ' . $userDetails['r_lastname'] . ',';



        $subject = $userDetails['firstname'] . ' Booking Acknowledgment';

        $body .= "<br/><br/>You have successufully enrolled <b>" . $userDetails['firstname'] . ' ' . $userDetails['lastname'] . '</b>';

        $body .= "in following course: <br/><br/>";



        $body .= "<strong>Course:</strong> " . $userDetails['course_name'] . ' ' . '<br/>';

        $body .="<strong>Class:</strong> " . $userDetails['class_name'] . ' ' . '<br/>';



        $body .= "<br/><strong>Class Start Date:</strong> " . $userDetails['class_start'] . "<br/>";

        $body .= "<strong>Class End Date-Time:</strong> " . $userDetails['class_end'] . "<br/><br/>";

        $body .= "<strong>Classroom Venue:</strong> " . $userDetails['class_venue'] . "<br/><br/>";





        $body .= "<strong>You can access the TMS portal at <a href='" . base_url() . "'>" . base_url() . "</a></strong><br/><br/>";

        $body .= $footer_data;

        return send_mail($userDetails['r_email'], '', $subject, $body);
    }

    //end



    /* when user comes from HOME registeration page with NRIC OR NOT NRIC */

    public function send_reg_enroll($userDetails, $bypassemail) {

        $tenant_details = fetch_tenant_details(TENANT_ID);

        $footer_data = str_replace("<Tenant_Company_Name>", $tenant_details->tenant_name, MAIL_FOOTER);





        $footer_data = str_replace("<Tenant_Company_Email>", $tenant_details->tenant_email_id, $footer_data);

        $subject = NULL;

        $body = NULL;

        if ($userDetails['gender'] == 'MALE') {

            $body = "Dear Mr. " . $userDetails['firstname'] . ' ' . $userDetails['lastname'] . ',';
        } elseif ($user['gender'] == '') {

            $body .="Dear Mr./Ms. " . $userDetails['firstname'] . ' ' . $userDetails['lastname'] . ',';
        } else {

            $body .="Dear Ms. " . $userDetails['firstname'] . ' ' . $userDetails['lastname'] . ',';
        }



        $subject = 'Your Booking Acknowledgment';

        $body .= "<br/><br/>You have successufully enrolled in following course:" . ' ' . '<br/>' . '<br/>';

        $body .= "<strong>Course:</strong> " . $userDetails['course_name'] . ' ' . '<br/>';

        $body .="<strong>Class:</strong> " . $userDetails['class_name'] . ' ' . '<br/>';





        $body .= "<br/><strong>Class Start Date:</strong> " . $userDetails['class_start'] . "<br/>";

        $body .= "<strong>Class End Date-Time:</strong> " . $userDetails['class_end'] . "<br/><br/>";

        $body .= "<strong>Classroom Venue:</strong> " . $userDetails['class_venue'] . "<br/><br/>";





        $body .= "<strong>You can access the TMS portal at <a href='" . base_url() . "'>" . base_url() . "</a></strong><br/><br/>";

        $body .= $footer_data;

        return send_mail($userDetails['email'], '', $subject, $body);
    }

    //end

    /**





     * This method returns active_class_count





     * @return type





     */
    public function active_class_count() {

        $tenant_id = TENANT_ID;

        $date = date('Y-m-d');

        $this->db->select('c_cls.class_id, c_cls.course_id, c_cls.total_seats, c_cls.class_pymnt_enrol');

        $this->db->from('course_class c_cls');

        $this->db->join('course crs', 'crs.course_id = c_cls.course_id');

        $this->db->where('crs.crse_status', 'ACTIVE');

        $this->db->where('c_cls.class_status !=', 'INACTIV');

        $this->db->where('c_cls.tenant_id', $tenant_id);

        $this->db->where('DATE(c_cls.class_end_datetime) >= ', $date);

        return $this->db->get()->result_array();
    }

    /* Get individul discount from enroll some one skm start */

    public function get_indv_class_details($course_id, $uid) {

        $tenant_id = TENANT_ID;

//	$val = 0;

        $this->db->select('*');

        $this->db->from('tms_users_discount');

        $this->db->where('tenant_id', $tenant_id);

        $this->db->where('course_id', $course_id);

        $this->db->where('user_id', $uid);

//	$this->db->where('discount_percent !=', $val);
//        
//        $this->db->where('discount_amount !=', $val);
//        $result = $this->db->get()->row();

        $where = '(discount_percent!="0" or discount_amount!= "0")';
        $this->db->where($where);

        $sql = $this->db->get();

        //echo $this->db->last_query();

        if ($sql->num_rows() > 0) {

            $x = $sql->row();

            return $x;
        } else {

            return $x = 0;
        }
    }

//end

    /**





     * This method returns class_status





     * @param type $cid





     * @return string





     */
    public function get_class_status($cid) {

        $cur_date = strtotime(date("Y-m-d"));

        $data = $this->db->select('class_status,date(class_start_datetime) as start,date(class_end_datetime) as end')
                        ->from('course_class')->where('class_id', $cid)->get()->row(0);

        $start = strtotime($data->start);

        $end = strtotime($data->end);

        if ($data->class_status == 'INACTIV') {

            return 'Inactive';
        } elseif ($start > $cur_date && $end > $cur_date) {

            return 'Yet to Start';
        } else if ($start <= $cur_date && $end >= $cur_date) {

            return 'In-Progress';
        } elseif ($end < $cur_date && $start < $cur_date) {

            return 'Completed';
        } else {





            return 'Status Unknown!!!!';
        }
    }

    /**





     * this method returns tenant_masters details





     * @param type $tenant_id





     * @return type





     */
    public function get_tenant_masters($tenant_id) {

        $result = $this->db->select('*')->from('tenant_master')->where('tenant_id', $tenant_id)->get()->row();

        return $result;
    }

    /**





     * This method returns class_enrol table





     * @param type $trainee_id





     * @param type $class_id





     * @return type





     */
    public function get_paydue_invoice($trainee_id, $class_id) {

        $result = $this->db->select('*')->from('class_enrol ce')
                        ->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ce.pymnt_due_id')
                        ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                        ->where('ce.class_id', $class_id)->where('ce.user_id', $trainee_id)->get()->row();

        return $result;
    }

    /**





     * Function to get the course manager details 





     * @param type $manager_ids





     * @return type





     */
    public function get_managers($manager_ids) {

        $mgr_name = "";

        if ($manager_ids == "") {

            $mgr_name = "";
        } else {

            $manager_ids_array = explode(",", $manager_ids);

            $this->db->select("first_name,last_name, gender");

            $this->db->from("tms_users_pers");





            $this->db->where('tenant_id', TENANT_ID);





            $this->db->where_in("user_id", $manager_ids_array);

            $result = $this->db->get()->result();

            foreach ($result as $row) {



                $mgr_name .=$row->first_name . " " . $row->last_name . ', ';
            }
        }

        $mgr_name = rtrim($mgr_name, ', ');

        return $mgr_name;
    }

    /**





     * Function to get the class details





     * @param type $class_id





     * @return type





     */
    public function get_class_details($class_id) {

        $tenant_id = TENANT_ID;

        $this->db->select('*');

        $this->db->from('course_class');

        $this->db->where('tenant_id', $tenant_id);

        $this->db->where('class_id', $class_id);

        $result = $this->db->get()->row();





        return $result;
    }

    /**





     * Function to get the gst





     * @return type





     */
    public function get_gst_current() {

        $result = $this->db->select('gst_rate')->
                        from('gst_rates')
                        ->where('is_current', 1)->get()->row()->gst_rate;

        return $result;
    }

    /**





     * Function to get the course details





     * @param type $course_id





     * @return type





     */
    public function course_basic_details($course_id) {

        $tenant_id = TENANT_ID;

        $this->db->select('crse_name, certi_level, crse_manager, subsidy_after_before, gst_on_off, class_type')
                ->from('course')
                ->where('course_id', $course_id)
                ->where('tenant_id', $tenant_id);

        return $this->db->get()->row();
    }

    /**





     * Function will return  the class status 





     * @param type $class_id





     * @return string





     */
    public function get_class_statustext($class_id) {

        $cur_date = time();



        $data = $this->db->select('class_status,class_start_datetime as start,class_end_datetime as end')
                        ->from('course_class')->where('class_id', $class_id)->get()->row(0);

        $start = strtotime($data->start);

        $end = strtotime($data->end);

        if ($data->class_status == 'INACTIV') {

            return 'INACTIV';
        } elseif ($start > $cur_date) {

            return 'YTOSTRT';
        } elseif ($end < $cur_date) {

            return 'COMPLTD';
        } else {





            return 'IN_PROG';
        }
    }

    /**





     * Function to check whether the user already enrolled or not





     * @param type $user_id





     * @param type $class_id





     * @param type $course_id





     * @return type





     */
    public function is_user_enrolled($user_id, $class_id, $course_id) {

        $tenant_id = TENANT_ID;



        $this->db->select('*')
                ->from('class_enrol')->where('tenant_id', $tenant_id)->where('course_id', $course_id)
                ->where('class_id', $class_id)->where('user_id', $user_id);

        return $this->db->get();
    }

    public function is_user_enrolled1($user_id, $class_id, $course_id) {

        $tenant_id = TENANT_ID;



        $this->db->select('*')
                ->from('class_enrol')->where('tenant_id', $tenant_id)->where('course_id', $course_id)
                ->where('class_id', $class_id)->where('user_id', $user_id);

        $sql = $this->db->get();

//        echo $this->db->last_query();echo "<br/>";  

        return $sql->num_rows();
    }

    /**





     * Function for saving  enrol and payment details





     * @param type $data





     * @return type





     */
    public function create_new_enroll($data) {

        $tenant_id = TENANT_ID;

        $status = TRUE;

        $payment_due_id = get_max_lookup(ENROL_PYMNT_DUE);

        $cur_date = date('Y-m-d H:i:s');



        $class_id = $data['class_id'];

        $dat = $this->db->select('class_start_datetime as start,class_name,class_end_datetime as end')
                        ->from('course_class')
                        ->where('class_id', $class_id)
                        ->get()->row(0);

        $start = $dat->start;







        $class_status = $this->get_class_statustext($data['class_id']);



        if (!empty($data['refer_id'])) {

            $frnd_id = $data['refer_id'];

            $enrol_by = $data['refer_id'];
        } else {

            $frnd_id = NULL;

            $enrol_by = $data['user_id'];
        }

        $ce_data = array(
            'tenant_id' => $tenant_id,
            'course_id' => $data['course_id'],
            'class_id' => $data['class_id'],
            'user_id' => $data['user_id'],
            'enrolment_type' => 'PUBLIC',
            'enrolment_mode' => 'SELF',
            'pymnt_due_id' => $payment_due_id,
            'friend_id' => $frnd_id,
            'friend_relation' => $data['relation'],
            'enrolled_on' => $cur_date,
            'enrolled_by' => $enrol_by,
            'payment_status' => $data['pay_status'],
            'class_status' => $class_status,
            'enrol_status' => $data['enrol_status']
        );

        $this->db->trans_start();

        $this->db->insert('class_enrol', $ce_data);

        $epd_data = array(
            'user_id' => $data['user_id'],
            'pymnt_due_id' => $payment_due_id,
            'class_fees' => round($data['class_fees'], 2),
            'total_amount_due' => round($data['net_due'], 2),
//            'discount_type' => (empty($data['discount_rate'])) ? '' : 'DISCLASS',
            'discount_type' => $data['discount_type'],
            'discount_rate' => round($data['discount_rate'], 2),
            'gst_amount' => round($data['gst_amount'], 2)
        );

        $this->db->insert('enrol_pymnt_due', $epd_data);

        $gst_rule = (empty($data['gst_onoff'])) ? '' : $data['gst_rule'];





        $invoice_id = $this->generate_invoice_id();

        $ei_data = array(
            'invoice_id' => $invoice_id,
            'pymnt_due_id' => $payment_due_id,
            'inv_date' => $start,
            'inv_type' => 'INVINDV',
            'total_inv_amount' => round($data['net_due'], 2),
            'total_unit_fees' => round($data['class_fees'], 2),
            'total_inv_discnt' => round($data['discount_amount'], 2),
            'total_gst' => round($data['gst_amount'], 2),
            'gst_rate' => round($data['gst_rate'], 2),
            'gst_rule' => $gst_rule,
        );

        $this->db->insert('enrol_invoice', $ei_data);










//
//        if ($data['pay_status'] == 'PAID') {
//
//
//
//
//
//            $epr_data = array(
//
//                'invoice_id' => $invoice_id,
//
//                'recd_on' => $cur_date,
//
//
//
//
//
//                'mode_of_pymnt' => 'ONLINE', 
//
//
//
//
//
//                'amount_recd' => round($data['net_due'], 2),
//
//                'cheque_number' => NULL,
//
//                'cheque_date' => NULL,
//
//                'bank_name' => NULL,
//
//                'recd_by' => $data['user_id'],
//
//            );
//
//            $this->db->insert('enrol_paymnt_recd', $epr_data);
//
//            $epbd_data = array(
//
//                'invoice_id' => $invoice_id,
//
//                'user_id' => $data['user_id'],
//
//                'amount_recd' => round($data['net_due'], 2),
//
//                'recd_on' => $cur_date
//
//            );
//
//            $this->db->insert('enrol_pymnt_brkup_dt', $epbd_data);
//
//        }



        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }





        $enrolled_user_data = $this->get_enrolled_user_data($data['user_id'], $data['course_id'], $data['class_id']);





        $this->send_tenant_mail($enrolled_user_data, 'enrol');





        return array('invoice_id' => $invoice_id, 'payment_due_id' => $payment_due_id, 'status' => $status);
    }

    public function create_new_enroll2($data) {



        $r_name1 = $this->input->post('r_name1');

        $r_email1 = $this->input->post('r_email1');

        $r_contact1 = $this->input->post('r_contact1');

        $r_company = $this->input->post('r_company');



        $referal_info = array('name' => $r_name1, 'email' => $r_email1, 'contact' => $r_contact1, 'company' => $r_company);

        $referal_info = json_encode($referal_info);



        $tenant_id = TENANT_ID;

        $status = TRUE;

        $payment_due_id = get_max_lookup(ENROL_PYMNT_DUE);

        $cur_date = date('Y-m-d H:i:s');



        $class_id = $data['class_id'];

        $dat = $this->db->select('class_start_datetime as start,class_name,class_end_datetime as end')
                        ->from('course_class')
                        ->where('class_id', $class_id)
                        ->get()->row(0);

        $start = $dat->start;



        $class_status = $this->get_class_statustext($data['class_id']);



        if (!empty($data['refer_id'])) {

            $frnd_id = $data['refer_id'];

            $enrol_by = $data['refer_id'];
        } else {

            $frnd_id = NULL;

            $enrol_by = $data['user_id'];
        }

        $ce_data = array(
            'tenant_id' => $tenant_id,
            'course_id' => $data['course_id'],
            'class_id' => $data['class_id'],
            'user_id' => $data['user_id'],
            'enrolment_type' => 'PUBLIC',
            'enrolment_mode' => 'SELF',
            'pymnt_due_id' => $payment_due_id,
            'friend_id' => $frnd_id,
            'friend_relation' => $data['relation'],
            'enrolled_on' => $cur_date,
            'enrolled_by' => $enrol_by,
            'payment_status' => $data['pay_status'],
            'class_status' => $class_status,
            'enrol_status' => $data['enrol_status'],
            'referral_details' => $referal_info
        );





        //$this->db->trans_start();



        $this->db->insert('class_enrol', $ce_data);

//        echo $this->db->last_query();
//        exit();

        $epd_data = array(
            'user_id' => $data['user_id'],
            'pymnt_due_id' => $payment_due_id,
            'class_fees' => round($data['class_fees'], 2),
            'total_amount_due' => round($data['net_due'], 2),
            //'discount_type' => (empty($data['discount_rate'])) ? '' : 'DISCLASS',
            'discount_type' => $data['discount_type'],
            'discount_rate' => round($data['discount_rate'], 2),
            'gst_amount' => round($data['gst_amount'], 2)
        );

        $this->db->insert('enrol_pymnt_due', $epd_data);

//        echo $this->db->last_query();



        $gst_rule = (empty($data['gst_onoff'])) ? '' : $data['gst_rule'];





        $invoice_id = $this->generate_invoice_id();

        $ei_data = array(
            'invoice_id' => $invoice_id,
            'pymnt_due_id' => $payment_due_id,
            'inv_date' => $start,
            'inv_type' => 'INVINDV',
            'total_inv_amount' => round($data['net_due'], 2),
            'total_unit_fees' => round($data['class_fees'], 2),
            'total_inv_discnt' => round($data['discount_amount'], 2),
            'total_gst' => round($data['gst_amount'], 2),
            'gst_rate' => round($data['gst_rate'], 2),
            'gst_rule' => $gst_rule,
        );

        $this->db->insert('enrol_invoice', $ei_data);

//       echo $this->db->last_query();
        // $this->db->trans_complete();
//        if ($this->db->trans_status() === FALSE) {
//            $status = FALSE;
//        }



        $enrolled_user_data = $this->get_enrolled_user_data($data['user_id'], $data['course_id'], $data['class_id']);





        $this->send_tenant_mail($enrolled_user_data, 'enrol'); 



        $x = array('invoice_id' => $invoice_id, 'payment_due_id' => $payment_due_id, 'status' => $status);



        return array('invoice_id' => $invoice_id, 'payment_due_id' => $payment_due_id, 'status' => $status);
    }

    /**





     * Store the enrol details in the paypal details





     * @param type $param





     * @param type $invoice_id





     * @return type





     */
    public function store_paypal_enrol_details($param = NULL, $invoice_id) {

        if (empty($param)) {

            return;
        }

        $str = serialize($param);

        $data = array(
            'enrol_details' => $str,
            'invoice_id' => $invoice_id
        );

        $this->db->insert('paypal_payment_details', $data);





        return $this->db->insert_id();
    }

    /**





     * get the enrol details for paypal





     * @param type $pid





     * @return type





     */
    public function get_paypal_enrol_details($pid = NULL) {

        if (empty($pid)) {

            return;
        }

        $this->db->select('enrol_details');

        $this->db->from('paypal_payment_details');

        $this->db->where('pid', $pid);

        $result = $this->db->get()->row()->enrol_details;

        return unserialize($result);
    }

    /**





     * update the details of pappal upon succesful payment





     * @param type $invoice_id





     * @param type $details





     * @return type





     */
    public function update_paypal_enrol_details($invoice_id = NULL, $details = NULL) {

        if (empty($details) || empty($invoice_id)) {

            return;
        }





        $this->db->where('invoice_id', $invoice_id);

        $this->db->update('paypal_payment_details', $details);
    }

    /**





     * check if the enrol details are updated succesfully





     * @param type $tran_id





     * @return boolean





     */
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

    /**

     * function used to get taxcode for class_enroll

     * @param type $taxcode

     * @return type

     */
    public function validate_taxcode_data($taxcode) {



        $this->db->select('tu.*, tup.*');

        $this->db->from('tms_users tu');

        $this->db->join('tms_users_pers tup', 'tup.user_id = tu.user_id');

        $this->db->where('tu.tax_code', $taxcode);

        $query = $this->db->get();

//        echo $this->db->last_query();

        return $query->row();
    }

    /**

     * This method returns back the tenant name

     * @param type $tenant_id

     */
    public function get_tenant_name($tenant_id) {

        $this->db->select('*');

        $this->db->from('tenant_master tm');

        $this->db->where('tm.tenant_id', $tenant_id);

        $query = $this->db->get();

        return $query->row();
    }

    /**





     * function for class enroll   





     */
    public function create_new_classenroll($data) {

        $tenant_id = TENANT_ID;

        $payment_due_id = get_max_lookup(ENROL_PYMNT_DUE);

        $cur_date = date('Y-m-d H:i:s');



        $dat = $this->db->select('class_start_datetime as start,class_name,class_end_datetime as end')
                        ->from('course_class')
                        ->where('class_id', $class_id)
                        ->get()->row(0);

        $start = $dat->start;



        $class_status = $this->get_class_statustext($data['class_id']);

        $ce_data = array(
            'tenant_id' => $tenant_id,
            'course_id' => $data['course_id'],
            'class_id' => $data['class_id'],
            'user_id' => $data['user_id'],
            'enrolment_type' => 'PUBLIC',
            'enrolment_mode' => 'SELF',
            'pymnt_due_id' => $payment_due_id,
            'enrolled_on' => $cur_date,
            'enrolled_by' => $data['user_id'],
            'payment_status' => $data['pay_status'],
            'class_status' => $class_status,
            'enrol_status' => $data['enrol_status']
        );

        $this->db->trans_start();

        $this->db->insert('class_enrol', $ce_data);

        $epd_data = array(
            'user_id' => $data['user_id'],
            'pymnt_due_id' => $payment_due_id,
            'class_fees' => round($data['class_fees'], 2),
            'total_amount_due' => round($data['net_due'], 2),
            'discount_type' => (empty($data['discount_rate'])) ? '' : 'DISCLASS',
            'discount_rate' => round($data['discount_rate'], 2),
            'gst_amount' => round($data['gst_amount'], 2)
        );

        $this->db->insert('enrol_pymnt_due', $epd_data);





        $gst_rule = (empty($data['gst_onoff'])) ? '' : $data['gst_rule'];





        $invoice_id = $this->generate_invoice_id();

        $ei_data = array(
            'invoice_id' => $invoice_id,
            'pymnt_due_id' => $payment_due_id,
            'inv_date' => $start,
            'inv_type' => 'INVINDV',
            'total_inv_amount' => round($data['net_due'], 2),
            'total_unit_fees' => round($data['class_fees'], 2),
            'total_inv_discnt' => round($data['discount_amount'], 2),
            'total_gst' => round($data['gst_amount'], 2),
            'gst_rate' => round($data['gst_rate'], 2),
            'gst_rule' => $gst_rule,
        );

        $this->db->insert('enrol_invoice', $ei_data);





        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            return FALSE;
        }





        $enrolled_user_data = $this->get_enrolled_user_data($data['user_id'], $data['course_id'], $data['class_id']);





        $this->send_tenant_mail($enrolled_user_data, 'enrol');





        return array('invoice_id' => $invoice_id, 'payment_due_id' => $payment_due_id);
    }

    /**





     * send email to tenant's admin with enroll data





     * @param type $usrid





     * @param type $crsid





     * @param type $clsid





     * @return type





     */
    private function get_enrolled_user_data($usrid, $crsid, $clsid) {

        $this->db->select('*');

        $this->db->from('tms_users u');

        $this->db->join('tms_users_pers up', 'u.user_id = up.user_id and u.tenant_id = up.tenant_id');

        $this->db->where('u.user_id', $usrid);

        $this->db->where('u.tenant_id', TENANT_ID);

        $enrolled_user_data[0] = $this->db->get()->result_array();



        $this->db->select('*');

        $this->db->from('class_enrol ce');

        $this->db->join('course_class cc', 'cc.class_id = ce.class_id');

        $this->db->join('course crs', 'crs.course_id = cc.course_id');

        $this->db->where('ce.user_id', $usrid);

        $this->db->where('ce.course_id', $crsid);

        $this->db->where('ce.class_id', $clsid);

        $this->db->where('ce.tenant_id', TENANT_ID);

        $enrolled_user_data[1] = $this->db->get()->result_array();

        return $enrolled_user_data;
    }

    /**





     * get the trainer names in course class list





     * @param type $trainer_ids





     * @return type





     */
    public function get_trainers($trainer_ids) {

        $trainer_name = "";

        if ($trainer_ids == "") {

            $trainer_name = "";
        } else {

            $trainer_ids_array = explode(",", $trainer_ids);

            $this->db->select("first_name,last_name, gender");

            $this->db->from("tms_users_pers");

            $this->db->where_in("user_id", $trainer_ids_array);

            $result = $this->db->get()->result();

            foreach ($result as $row) {



                $trainer_name .=$row->first_name . " " . $row->last_name . ', ';
            }
        }

        $trainer_name = rtrim($trainer_name, ', ');

        return $trainer_name;
    }

    /**





     * send tenant's admin  email notification





     * @param type $traineedata





     * @param type $bypassemail





     * @return type





     */
    public function send_tenant_mail($traineedata, $bypassemail) {

        $admin_details = $this->get_admin_details();

        $tenant_details = fetch_tenant_details(TENANT_ID);

        $footer_data = str_replace("<Tenant_Company_Name>", $tenant_details->tenant_name, MAIL_FOOTER);

        $footer_data = str_replace("<Tenant_Company_Email>", $tenant_details->tenant_email_id, $footer_data);











        $subject = 'Trainee Account Registration Notification';





        if ($admin_details[0]['gender'] == 'MALE') {

            $body = "Dear Mr. " . $admin_details[0]['first_name'] . ' ' . $admin_details[0]['last_name'] . ',';
        } else {

            $body = "Dear Ms. " . $admin_details[0]['first_name'] . ' ' . $admin_details[0]['last_name'] . ',';
        }

        if ($bypassemail == 'enrol') {

            $subject = 'Trainee Enrollment Notification';

            if ($traineedata[0][0]['gender'] == 'MALE') {

                $sal = "Mr.";
            } elseif ($traineedata[0][0]['gender'] == '') {

                $sal = "Mr / Ms.";
            } else {

                $sal = "Ms.";
            }

            $body .= "<br/><br/>" . $sal . '' . $traineedata[0][0]['first_name'] . '' . " has been enrolled to" . "&nbsp;&nbsp;<b> " . $traineedata[1][0]['crse_name'] . '-' . $traineedata[1][0]['class_name'] . '' . "</b> course-class in TMS public portal.<br/>";

            $body .= $footer_data;
        } else {

            if ($traineedata['gender'] == 'MALE') {

                $sal = "Mr.";
            } else if ($traineedata['gender'] == '') {

                $sal = "Mr/Ms.";
            } else {

                $sal = "Ms.";
            }

            $body .= "<br/><br/>New trainee" . ' ' . $sal . ' ' . $traineedata['firstname'] . " has been successfully registered into TMS public portal for the tenant " . $tenant_details->tenant_name . ".<br/>";

            $body .= $footer_data;
        }

        // added by shubhranshu for FRCS requirement dt 18.05.2021 to send mail to different mail id for NSA courses!
        if ((strpos($traineedata[1][0]['crse_name'], 'NSA') !== false) && TENANT_ID == 'T24') {
            $tenant_details->tenant_email_id = FRCSMAILID;
        }
        
        return send_mail($tenant_details->tenant_email_id, '', $subject, $body);
    }

    /**





     * To get admin details





     * @return type





     */
    private function get_admin_details() {

        $this->db->select('*');

        $this->db->from('tms_users u');

        $this->db->join('internal_user_role role', 'u.user_id = role.user_id and u.tenant_id = role.tenant_id');

        $this->db->join('tms_users_pers up', 'u.user_id = up.user_id and u.tenant_id = up.tenant_id');

        $this->db->where('role.role_id ', 'ADMN');

        $this->db->where('role.tenant_id ', TENANT_ID);

        $res = $this->db->get()->result_array();

        return $res;
    }

    /**





     * This method check is user is paid





     * @param type $invoice_id





     * @return type





     */
    public function is_user_paid($invoice_id) {

        $tenant_id = TENANT_ID;



        $this->db->select('*')
                ->from('enrol_paymnt_recd')->where('invoice_id', $invoice_id);

        return $this->db->get();
    }

    //Added for CR03 - Updates the Additional Remarks data to the DB

    public function update_additional_information($data) {

        $ce_data = array(
            'additional_remarks' => trim($data['additional_remarks'])
        );

        $this->db->where('user_id', $data['user_id']);

        $this->db->update('tms_users_pers', $ce_data);

        return $data['user_id'];
    }

    /**





     * This method update enroll payment





     * @param type $data





     * @return type





     */
    public function update_enroll_payment($data) {

        $tenant_id = TENANT_ID;

        $status = TRUE;

        $payment_due_id = $data['payment_due_id'];

        $invoice_id = $data['invoice_id'];

        $cur_date = date('Y-m-d H:i:s');



        $this->db->trans_start();











        if ($data['pay_status'] == 'PAID') {





            $ce_data = array(
                'payment_status' => $data['pay_status'],
                'enrol_status' => $data['enrol_status']
            );

            $this->db->where('tenant_id', $tenant_id);

            $this->db->where('pymnt_due_id', $payment_due_id);

            $this->db->where('user_id', $data['user_id']);

            $this->db->update('class_enrol', $ce_data);

            $epr_data = array(
                'invoice_id' => $invoice_id,
                'recd_on' => $cur_date,
                'mode_of_pymnt' => 'ONLINE',
                'amount_recd' => round($data['net_due'], 2),
                'cheque_number' => NULL,
                'cheque_date' => NULL,
                'bank_name' => NULL,
                'recd_by' => $data['user_id'],
            );

            $this->db->insert('enrol_paymnt_recd', $epr_data);

            $epbd_data = array(
                'invoice_id' => $invoice_id,
                'user_id' => $data['user_id'],
                'amount_recd' => round($data['net_due'], 2),
                'recd_on' => $cur_date
            );

            $this->db->insert('enrol_pymnt_brkup_dt', $epbd_data);
        }



        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }

        return array('invoice_id' => $invoice_id, 'payment_due_id' => $payment_due_id, 'status' => $status);
    }

    /**





     * function to generate invoice id





     * @return string





     */
    ////added by shubhranshu for everest invoice id changes
    private function generate_invoice_id() {

        //$date_array = explode("-",$class_start_date);

        $pre_fix_array = array("T01" => "T01", "T02" => "XPR", "T03" => "CAI", "T04" => "FL", "T12" => "XPR.A.","T16" => "XPR.B.","T17" => "EVI","T20" => "WABLAB","T23" => "DEMO", "T24" => "RLIS", "T18" => "SSI", "T25" => "FGE");

        $lookup_table = array("T01" => "test_invoice_id", "T02" => "xprienz_invoice_id", "T03" => "carrie_invoice_id", "T04" => "focus_invoice_id", "T12" => "xprienz2_invoice_id","T16" => "xprienz3_invoice_id","T17" => "ei_new_invoice_id","T20" => "wablab_invoice_id","T23" => "demo_invoice_id", "T24" => "rlis_invoice_id", "T18" => "ssi_invoice_id", "T25" => "fge_invoice_id");

        $tenant_id = $this->tenant_id ?? TENANT_ID;

        $invoice_id_tmp = get_max_lookup($lookup_table[$tenant_id]);

        if($tenant_id == 'T17'){
            if(strlen($invoice_id_tmp)== 1){
                $invoice_id_tmp = '000'.$invoice_id_tmp;
            }elseif(strlen($invoice_id_tmp)== 2){
                $invoice_id_tmp = '00'.$invoice_id_tmp;
            }elseif(strlen($invoice_id_tmp)== 3){
                $invoice_id_tmp = '0'.$invoice_id_tmp;
            }elseif(strlen($invoice_id_tmp)== 4){
                $invoice_id_tmp = $invoice_id_tmp;
            }else{
                $invoice_id_tmp = $invoice_id_tmp;
            }
            
            $invoice_id = $pre_fix_array[$tenant_id] .'-20'.date('y').'-'.date('m').$invoice_id_tmp;
        }else{
            $invoice_id = $pre_fix_array[$tenant_id] . $invoice_id_tmp;
        }

        return $invoice_id;
    }

    /*

     * This function is used to get the parameter value based on the parameter ID

     * MOVE to GENERIC CLASS

     * Author: pritam

     */

    public function get_param_value($param_id) {

        return $this->db->select('category_name')->where('parameter_id', $param_id)->get('metadata_values')->row();
    }

    /*

     * Author:Pritam

     * Function total count of completed  class list */

    public function get_completed_class_list_count() {

        $tenant_id = $this->session->userdata('userDetails')->tenant_id;

        $user_id = $this->session->userdata('userDetails')->user_id;

        $this->db->where('e.tenant_id', $tenant_id);

        $this->db->where('user_id', $user_id);

        $this->db->where("e.course_id = c.course_id");

        $this->db->where("e.class_id = c.class_id");

        $this->db->where('e.class_status', 'COMPLETED');

        $this->db->from('class_enrol e,course_class  c');

        return $this->db->count_all_results();
    }

    /*

     * Author:Pritam

     * Function for  viewing  completed  class list 

     */

    public function get_completed_class_list($limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL) {

        $tenant_id = $this->session->userdata('userDetails')->tenant_id;

        $user_id = $this->session->userdata('userDetails')->user_id;

        if ($offset < 0 || empty($tenant_id)) {

            return;
        }

        $this->db->select("*");

        $this->db->from("class_enrol e,course_class cl,course c");

        $this->db->where("e.course_id = cl.course_id");

        $this->db->where("e.class_id = cl.class_id");

        $this->db->where("c.course_id = e.course_id");

        $this->db->where("e.user_id", $user_id);

        $this->db->where("e.tenant_id", $tenant_id);

        $this->db->where("cl.tenant_id", $tenant_id);

        $this->db->where("e.class_status", 'COMPLTD');



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

     * Author: Pritam

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

    public function get_course_list_home($limit=NULL, $offset=NULL, $search_value = NULL) {

        $tenant_id = TENANT_ID;
      
        $date = date('Y-m-d h:i A');
        $time = date("H:i:s", strtotime($date));
        $today = date('Y-m-d') . ' ' . $time;

        $this->db->distinct();

        if (!empty($search_value)) {

            $this->db->select('crs.course_id,crs.crse_name,crs.description,crs.crse_icon')->like('crs.crse_name', $search_value, 'both');
        } else {

            $this->db->select('crs.course_id,crs.crse_name,crs.description,crs.crse_icon,crs.certi_level,crs.crse_duration,crs.language,crs.pre_requisite');
        }

        $this->db->from('course crs');

        $this->db->join('course_class c_cls', 'crs.course_id = c_cls.course_id');

        $this->db->where('crs.crse_status', 'ACTIVE');
        $this->db->where('c_cls.class_status !=', 'INACTIV');
        $this->db->where('c_cls.class_start_datetime >= ', $today);

        $this->db->where('c_cls.display_class_public', '1');

        $this->db->where('crs.tenant_id', $tenant_id);

        $this->db->where('crs.display_on_portal', '1');

        $this->db->order_by('crs.crse_name');

        if ($limit == $offset) {

            $this->db->limit($offset);
        } else if ($limit > 0) {

            $limitvalue = $offset - $limit;

            $this->db->limit($limit, $limitvalue);
        }

         return $this->db->get()->result();
        
    }
    
    public function get_all_course_class_list($tenant_id,$limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL)
    {

        $date = date('Y-m-d h:i A');
        $time = date("H:i:s", strtotime($date));
        $today_date = date('Y-m-d').' '.$time; 
        
        if ($offset < 0 || empty($tenant_id)) {

            return;
        }       

        $this->db->select('crs.crse_name, crs.crse_manager,c_cls.class_id, c_cls.course_id, c_cls.total_seats, c_cls.classroom_trainer,c_cls.class_name, c_cls.class_start_datetime,c_cls.class_end_datetime, c_cls.description,c_cls.classroom_venue_oth,

                c_cls.total_classroom_duration, c_cls.total_lab_duration,c_cls.assmnt_duration,c_cls.class_fees,c_cls.classroom_location, c_cls.lab_location ,c_cls.class_language,c_cls.class_pymnt_enrol,c_cls.class_status,c_cls.training_aide');

        $this->db->from('course_class c_cls');

        $this->db->join('course crs', 'crs.course_id = c_cls.course_id and c_cls.tenant_id=crs.tenant_id'); 

        $this->db->where('crs.crse_status', 'ACTIVE');

        $this->db->where('c_cls.class_status !=', 'INACTIV');

        $this->db->where('crs.display_on_portal', '1');

        $this->db->where('c_cls.display_class_public', '1');

        $this->db->where('c_cls.tenant_id', $tenant_id);

        $this->db->where('c_cls.class_start_datetime >= ', $today_date);

        if ($sort_by) {

            $this->db->order_by($sort_by, $sort_order);

        } else {

            $this->db->order_by('c_cls.class_start_datetime', 'ASC');

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
    
    /* skm end */
    
    /* skm start : get count of all course and class list */
    public function get_all_course_class_list_count($tenant_id,$limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL)
    {

        $date = date('Y-m-d h:i A');
        $time = date("H:i:s", strtotime($date));
        $today_date = date('Y-m-d').' '.$time; 
        
        if ($offset < 0 || empty($tenant_id)) {

            return;
        }       

        $this->db->select('crs.crse_name, crs.crse_manager,c_cls.class_id, c_cls.course_id, c_cls.total_seats, c_cls.classroom_trainer,c_cls.class_name, c_cls.class_start_datetime,c_cls.class_end_datetime, c_cls.description,c_cls.classroom_venue_oth,

                c_cls.total_classroom_duration, c_cls.total_lab_duration,c_cls.assmnt_duration,c_cls.class_fees,c_cls.classroom_location, c_cls.lab_location ,c_cls.class_language,c_cls.class_pymnt_enrol,c_cls.class_status,c_cls.training_aide');

        $this->db->from('course_class c_cls');

        $this->db->join('course crs', 'crs.course_id = c_cls.course_id and c_cls.tenant_id=crs.tenant_id'); 

        $this->db->where('crs.crse_status', 'ACTIVE');

        $this->db->where('c_cls.class_status !=', 'INACTIV');

        $this->db->where('crs.display_on_portal', '1');

        $this->db->where('c_cls.display_class_public', '1');

        $this->db->where('c_cls.tenant_id', $tenant_id);

        $this->db->where('c_cls.class_start_datetime >= ', $today_date);

        if ($sort_by) {

            $this->db->order_by($sort_by, $sort_order);

        } else {

            $this->db->order_by('c_cls.class_start_datetime', 'ASC');

        }

        $query = $this->db->get();

        return $query->num_rows();

    }
    
    ///////////added by shubhranshu for new requirement for elearning
    public function check_taxcode_exists_public($taxcode, $course_id, $class_id) {
        $tenant_id = TENANT_ID;
        $this->db->select('tu.user_id,tu.tax_code,tup.first_name');

        $this->db->from('tms_users tu');
        $this->db->join('tms_users_pers tup','tup.user_id=tu.user_id and tup.tenant_id=tu.tenant_id');
        $this->db->where('tu.tax_code', $taxcode);

        $this->db->where('tu.tenant_id', $tenant_id);

        
        $sql = $this->db->get();

        $data = $sql->row();
        $res = $this->nric_exits_cc($taxcode, $course_id, $class_id);
        if ($res == 1) {
            echo 1; // already enrolled
        } else {
            if ($sql->num_rows() > 0) {
                echo json_encode($data);
            } else {
                echo 0;  
            }
        }
        
       exit();
    }
    


}

/*End  of  the course_model*/






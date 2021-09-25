<?php

class Tpg_api_Model extends CI_Model {

    private $user;

    /**
     * constructor - loads Model and other objects required in this controller
     */
    public function __construct() {
        parent::__construct();
        $this->load->helper('common');
        $this->sess_user = $this->session->userdata('userDetails'); // added by shubhranshu to het the user data
        $this->user = $this->session->userdata('userDetails');
        $this->tenant_id = $this->session->userdata('userDetails')->tenant_id;
    }

    function encrypt_decrypt($action, $string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";

        $tenant_id = $this->tenant_id;
        $key = base64_decode($this->config->item(TPG_KEY_ . $tenant_id));  // don't hash to derive the (32 bytes) key

        $iv = 'SSGAPIInitVector';                                              // don't hash to derive the (16 bytes) IV
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv); // remove explicit Base64 encoding (alternatively set OPENSSL_RAW_DATA)
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt($string, $encrypt_method, $key, 0, $iv); // remove explicit Base64 decoding (alternatively set OPENSSL_RAW_DATA)
        }
        return $output;
    }

    // Modified by abdulla for dynamic pem files.
    public function curl_request($mode, $url, $encrypted_data, $api_version) {

        $tenant_id = $this->tenant_id;

        $pemfile = "/var/www/newtms/assets/certificates/" . $tenant_id . "/cert.pem";
        $keyfile = "/var/www/newtms/assets/certificates/" . $tenant_id . "/key.pem";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $mode,
            CURLOPT_SSLCERT => $pemfile,
            CURLOPT_SSLCERTTYPE => 'PEM',
            CURLOPT_SSLKEY => $keyfile,
            CURLOPT_POSTFIELDS => $encrypted_data,
            CURLOPT_HTTPHEADER => array(
                "Authorization:  ",
                "Cache-Control: no-cache",
                "Content-Type: application/json",
                "x-api-version: $api_version"
            ),
        ));

        $response = curl_exec($curl);
        if ($response === false) {
            print_r(curl_error($curl));
            exit;
        } else {

            return $response;
        }
        curl_close($curl);
    }

    public function correct_live_dev_api_data($crse_ref_no, $tp_uen, $skillCode = '', $nric = '') {
        //Training Partner
        $tenant_id = $this->tenant_id;
        $tenant_details = fetch_tenant_details($tenant_id);
        
        if (TPG_ENVIRONMENT == 'PRODUCTION') {
            $crse_ref_no = $crse_ref_no;
            $tp_uen = $tp_uen;
            $domain = TPG_URL;
            $skillCode = $skillCode;
            $nric = $nric;
            $corpassid = $tenant_details->corp_pass_id;
        } else {
            $crse_ref_no = 'TGS-2020002096';
            $tp_uen = '201000372W';            
            $domain = TPG_URL;
            $skillCode = 'AER-MAT-2019-2.1';
            $nric = 'S8195288D';
            $dob = '1981-01-10';
            $corpassid = 'S5883425D';
        }
        $data = array(
            'ref_no' => $crse_ref_no,
            'tp_uen' => $tp_uen,
            'domain' => $domain,
            'skillcode' => $skillCode,
            'nric' => $nric,
            'dob' => $dob,
            'corpassid' => $corpassid
        );
        return $data;
    }

    public function get_trainer_details($trainer_ids) {
        $tenantId = $this->session->userdata('userDetails')->tenant_id;
        $tids = explode(',', $trainer_ids);
        $ids = join("','", $tids);

        $sql = "SELECT iued.off_email_id,pers.user_id,tu.registered_email_id, pers.first_name, pers.last_name, rl.role_id, mtv.category_name
                        FROM `tms_users_pers` pers
                        JOIN internal_user_role rl on rl.tenant_id = pers.tenant_id AND rl.user_id = pers.user_id
                        JOIN tms_users tu on tu.user_id = pers.user_id
                        JOIN internal_user_emp_detail iued on iued.user_id = pers.user_id AND iued.tenant_id = pers.tenant_id
                        JOIN metadata_values mtv on mtv.parameter_id = pers.highest_educ_level
                        WHERE pers.tenant_id = '$tenantId' 
                        AND rl.role_id='TRAINER' 
                        AND rl.user_id in ('$ids')";
        $query = $this->db->query($sql);
        $trainer = $query->result();
        //echo $this->db->last_query();exit;


        return $trainer;
    }

    public function updateSsgData($class_id, $crse_run_id, $ssg_data) {
        if (!empty($crse_run_id)) {
            $tenantId = $this->session->userdata('userDetails')->tenant_id;
            $data = array(
                'tpg_course_run_id' => $crse_run_id,
                'tpg_qr_code' => $ssg_data->qrCodeLink
            );
            $this->db->trans_start();
            $this->db->where('class_id', $class_id);
            $this->db->where('tenant_id', $tenantId);
            $this->db->update('course_class', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public function get_all_class_schedule($tenant_id, $cid) {
        $result = $this->db->query("select class_date, session_type_id, session_start_time,session_end_time
                from class_schld where tenant_id='$tenant_id' and class_id='$cid' and session_type_id!='BRK' 
                order by class_date ASC, session_start_time ASC");
        return $result->result_array();
    }

    ///////////////////////////////API MODEL START///////////////////////////////////////////
    ///// api added  by shubhranshu create a courserun inside SSG system
    public function create_courserun_tpg($tenant_id, $tp_uen) {
        extract($_POST);
        $crse_ref_no = $this->input->post('crse_ref_no');
        $modeoftraining = $this->input->post('modeoftraining');
        $crs_admin_email = $this->input->post('crs_admin_email'); //Course admin email is under course run level that can be received the email from 'QR code Attendance Taking','Course Attendance with error' and 'Trainer information not updated'
        $reg_open_date = date("d-m-Y"); 
        $reg_close_date = $this->input->post('start_date');
        $crse_start_date = $this->input->post('start_date');
        $crse_end_date = $this->input->post('end_date');
        $schedule_info_des = 'Description'; //Course run schedule info Description
        $schedule_info = date('dM', strtotime($crse_start_date)) . ' : ' . date('D', strtotime($crse_start_date)) . ' / ' . date('h:i A', strtotime($this->input->post('start_time'))) . ' - ' . date('h:i A', strtotime($this->input->post('end_date')));
        $venue_building = $this->input->post('venue_building');
        $venue_block = $this->input->post('venue_block');
        $venue_street = $this->input->post('venue_street');
        $venue_floor = $this->input->post('venue_floor');
        $venue_unit = $this->input->post('venue_unit');
        $venue_postalcode = $this->input->post('venue_postalcode');
        $venue_room = $this->input->post('venue_room');
        //Added by abdulla
        $wheel_chair_access = $this->input->post('wheel_chair_accessible');
        if($wheel_chair_access == '0') {
            $wheel_chair_access = 'false';
        } else {
            $wheel_chair_access = 'true';
        }
        $crse_intake_size = $this->input->post('total_seats'); //Course run intake size. It's maximum pax for a class
        $crse_vacancy_code = "A"; //A - Available ,F - Full, L - Limited Vacancy
        $crse_vacancy_description = "Available"; /////A - Available ,F - Full, L - Limited Vacancy
        if (!empty($control_4)) {
            $control_4 = implode(",", $control_4);
        }
        if (!empty($control_5)) {
            $control_5 = implode(",", $control_5);
        }
        if (!empty($control_6)) {
            $control_6 = implode(",", $control_6);
        }
        if (!empty($control_7)) {
            $control_7 = implode(",", $control_7);
        }
        if (!empty($control_3)) {
            $control_3 = implode(",", $control_3);
        }
        $trainer_name = $this->input->post('trainer_name');
        $trainer_id = $this->input->post('trainer_id');
        $trainer_email = $this->input->post('trainer_email');
        $course_id = $this->input->post('class_course');

        if (!empty($schlded_date)) {
            foreach ($schlded_date as $k => $v) {
                if ($schlded_session_type[$k] != 'BRK') {
                    $dates = date('Ymd', strtotime($schlded_date[$k]));
                    $starttime = date("H:i", strtotime($schlded_start_time[$k]));
                    $endtime = date("H:i", strtotime($schlded_end_time[$k]));
                    $sessions[] = array(
                        "startDate" => "$dates",
                        "endDate" => "$dates",
                        "startTime" => "$starttime",
                        "endTime" => "$endtime",
                        "modeOfTraining" => "$mode_of_training[$k]",
                        "venue" => array
                            (
                            "block" => "$venue_block",
                            "street" => "$venue_street",
                            "floor" => "$venue_floor",
                            "unit" => "$venue_unit",
                            "building" => "$venue_building",
                            "postalCode" => "$venue_postalcode",
                            "room" => "$venue_room",
                            "wheelChairAccess" => "$wheel_chair_access",
                            "primaryVenue" => true,
                        ),
                    );
                }
            }
        }


        if (!empty($assmnt_date)) {
            foreach ($assmnt_date as $k => $v) {

                $assdates = date('Ymd', strtotime($assmnt_date[$k]));
                $assstarttime = date("H:i", strtotime($assmnt_start_time[$k]));
                $assendtime = date("H:i", strtotime($assmnt_end_time[$k]));
                $assessments[] = array(
                    "startDate" => "$assdates",
                    "endDate" => "$assdates",
                    "startTime" => "$assstarttime",
                    "endTime" => "$assendtime",
                    "modeOfTraining" => "8",
                    "venue" => array
                        (
                        "block" => "$venue_block",
                        "street" => "$venue_street",
                        "floor" => "$venue_floor",
                        "unit" => "$venue_unit",
                        "building" => "$venue_building",
                        "postalCode" => "$venue_postalcode",
                        "room" => "$venue_room",
                        "wheelChairAccess" => "$wheel_chair_access",
                        "primaryVenue" => true,
                    ),
                );
            }
        }
        if (!empty($assessments)) {
            $session_arr = array_merge($sessions, $assessments);
        } else {
            $session_arr = $sessions;
        }

        //print_r($session_arr);exit;
        $ClassTrainers = $this->get_trainer_details($control_5);
        //print_r($ClassTrainers);exit;
        if (!empty($ClassTrainers)) {
            $i=1;
            foreach ($ClassTrainers as $trainer) {
                $trainers[] = array("trainer" => array(
                        "trainerType" => array(
                            "code" => "2",
                            "description" => "New"
                        ),
                        "indexNumber" => "$i",
                        "id" => "",
                        "name" => "$trainer->first_name",
                        "email" => "$trainer->off_email_id",
                        "inTrainingProviderProfile" => true,
                        "domainAreaOfPractice" => "$trainer->category_name",
                        "experience" => "",
                        "linkedInURL" => "",
                        "salutationId" => 1,
                        "photo" => array(
                            "name" => "",
                            "content" => ""
                        ),
                        "linkedSsecEQAs" => array(
                            "description" => "",
                            "ssecEQA" => array(
                                "code" => ""
                            )
                        )
                    )
                );
                $i++;
            }
        }
        ///salutationId    = Available value - 1(Mr) 2(Ms) 3(Mdm) 4(Mrs) 5(Dr) 6(Prof).

        $retun = $this->correct_live_dev_api_data($crse_ref_no, $tp_uen);

        $tpg_course_run_json = '{
                    "course": {
                      "courseReferenceNumber": "' . $retun[ref_no] . '",
                      "trainingProvider": {
                        "uen": "' . $retun[tp_uen] . '"
                      },
                      "runs": [
                        {
                          "sequenceNumber": 0,
                          "modeOfTraining": "' . $modeoftraining . '",
                          "registrationDates": {
                            "opening": "' . date("Ymd", strtotime($reg_open_date)) . '",
                            "closing": "' . date("Ymd", strtotime($reg_close_date)) . '"
                          },
                          "courseDates": {
                            "start": "' . date("Ymd", strtotime($crse_start_date)) . '",
                            "end": "' . date("Ymd", strtotime($crse_end_date)) . '"
                          },
                          "scheduleInfoType": {
                            "code": "01",
                            "description": "' . $schedule_info_des . '"
                          },
                          "scheduleInfo": "' . $schedule_info . '",
                          "venue": {
                              "block": "' . $venue_block . '",
                              "street": "' . $venue_street . '",
                              "floor": "' . $venue_floor . '",
                              "unit": "' . $venue_unit . '",
                              "building": "' . $venue_building . '",
                              "postalCode": "' . $venue_postalcode . '",
                              "room": "' . $venue_room . '",
                              "wheelChairAccess": "' . $wheel_chair_access . '"
                          },
                          "intakeSize": ' . $crse_intake_size . ',
                          "courseAdminEmail": "' . $crs_admin_email . '",
                          "threshold": 0,
                          "registeredUserCount": "",
                          "courseVacancy": {
                            "code": "' . $crse_vacancy_code . '",
                            "description": "' . $crse_vacancy_description . '"
                          },
                          "file": {
                            "Name": "",
                            "content": ""
                          },
                          "sessions": 
                            ' . json_encode($session_arr) . '
                          ,
                          "linkCourseRunTrainer": ' . json_encode($trainers) . '
                        }
                      ]
                    }
                  }';
        //print_r($tpg_course_run_json);exit;
        $api_version = 'v1.3';
        $url = "https://" . $retun[domain] . "/courses/runs";
        $response = $this->curl_request('POST', $url, $tpg_course_run_json, $api_version);
        //print_r($response);exit;
        $obj = json_decode($response);
        //$obj = json_decode('{ "data": { "runs": [ { "id": 223389 } ] }, "error": {}, "meta": {}, "status": 200 }');
        $this->session->set_flashdata('resp', $obj);
        $this->session->set_flashdata('cid', $class_id);
        //if($obj->status == 200){
        //redirect('tp_gateway/courserun_status');
        return $obj;
        ///}else{
        //redirect('tp_gateway/check_status');
        //}
    }

    public function create_copy_courserun_tpg($tenant_id, $tp_uen, $datas) {
        //print_r($datas);exit;
        $crse_vacancy_code = "A"; //A - Available ,F - Full, L - Limited Vacancy
        $crse_vacancy_description = "Available"; /////A - Available ,F - Full, L - Limited Vacancy
        $reg_open_date = date("d-m-Y"); 
        $reg_close_date = $this->input->post('start_date');
        $class_name = $this->input->post('class_name');
        $start_date = $this->input->post('start_date');
        $start_time = $this->input->post('start_time');
        $end_date = $this->input->post('end_date');
        $end_time = $this->input->post('end_time');
        $schedule_info_des = 'Description'; //Course run schedule info Description
        $schedule_info = date('dM', strtotime($start_date)) . ' : ' . date('D', strtotime($start_date)) . ' / ' . date('h:i A', strtotime($this->input->post('start_time'))) . ' - ' . date('h:i A', strtotime($this->input->post('end_date')));
        $class_id = $this->input->post('class_hid');

        $old_start_datetime = $datas['class']->class_start_datetime;
        $old_end_datetime = $datas['class']->class_end_datetime;
        $start_date_ = date('Y-m-d', strtotime($old_start_datetime));
        $end_date_ = date('Y-m-d', strtotime("1 day", strtotime($old_end_datetime)));
        $begin = new DateTime($start_date_);
        $end = new DateTime($end_date_);

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);
        //print_r($period);exit;
        $new_date = date("Y-m-d", strtotime($start_date));
        $session_schdl_arr = array();
        foreach ($period as $dt) {
            $class_schedule = $this->get_all_class_schedule_new($tenant_id, $datas['class']->class_id, $dt->format("Y-m-d"));
            //print_r($class_schedule);exit;
            if (empty($class_schedule)) {
                $your_date = strtotime("1 day", strtotime($new_date));
                $new_date = date("Y-m-d", $your_date);
            } else {
                foreach ($class_schedule as $clssch) {
                    $session_schdl_arr[] = array(
                        'startDate' => $new_date,
                        'endDate' => $new_date,
                        "startTime" => substr($clssch['session_start_time'], 0, 5),
                        "endTime" => substr($clssch['session_end_time'], 0, 5),
                        'mode_of_training' => $clssch['mode_of_training']
                    );
                }
                $your_date = strtotime("1 day", strtotime($new_date));
                $new_date = date("Y-m-d", $your_date);
            }
        }

        //echo "ss";exit;
        $venue_block = $datas['class']->venue_block;
        $venue_street = $datas['class']->venue_street;
        $venue_floor = $datas['class']->venue_floor;
        $venue_unit = $datas['class']->venue_unit;
        $venue_building = $datas['class']->venue_building;
        $venue_postalcode = $datas['class']->venue_postalcode;
        $venue_room = $datas['class']->venue_room;
        
        //Added by abdulla
        $wheel_chair_access = $datas['class']->wheel_chair_accessible;
        if($wheel_chair_access == '0') {
            $wheel_chair_access = 'false';
        } else {
            $wheel_chair_access = 'true';
        }

        if (!empty($session_schdl_arr)) {
            foreach ($session_schdl_arr as $objj) {

                $dates = date('Ymd', strtotime($objj['startDate']));
                $starttime = date("H:i", strtotime($objj['startTime']));
                $endtime = date("H:i", strtotime($objj['endTime']));
                $sessions[] = array(
                    "startDate" => "$dates",
                    "endDate" => "$dates",
                    "startTime" => "$starttime",
                    "endTime" => "$endtime",
                    "modeOfTraining" => "$objj[mode_of_training]",
                    "venue" => array
                        (
                        "block" => "$venue_block",
                        "street" => "$venue_street",
                        "floor" => "$venue_floor",
                        "unit" => "$venue_unit",
                        "building" => "$venue_building",
                        "postalCode" => "$venue_postalcode",
                        "room" => "$venue_room",
                        "wheelChairAccess" => "$wheel_chair_access",
                        "primaryVenue" => true,
                    ),
                );
            }
        }

        //print_r($sessions);exit;
        $new_date1 = date("Y-m-d", strtotime($start_date));        
        foreach ($period as $dt) {
            $assm_schedule = $this->get_def_assessments_new($tenant_id, $datas['class']->class_id, $datas['class']->course_id, $datas['def_assessment'][0]->assmnt_type, $dt->format("Y-m-d"));
            //print_r($assm_schedule);exit;
            if (empty($assm_schedule)) {
                $your_date1 = strtotime("1 day", strtotime($new_date1));
                $new_date1 = date("Y-m-d", $your_date1);
            } else {
                foreach ($assm_schedule as $clssch1) {
                    $assmt_schdl_arr[] = array(
                        'startDate' => $new_date1,
                        'endDate' => $new_date1,
                        "startTime" => substr($clssch1->assmnt_start_time, 0, 5),
                        "endTime" => substr($clssch1->assmnt_end_time, 0, 5),
                        'mode_of_training' => $clssch1->mode_of_training
                    );
                }
                $your_date1 = strtotime("1 day", strtotime($new_date1));
                $new_date1 = date("Y-m-d", $your_date1);
            }
        }
         //print_r($assmt_schdl_arr);exit;

        if (!empty($assmt_schdl_arr)) {
            foreach ($assmt_schdl_arr as $kv) {

                $assdates = date('Ymd', strtotime($kv['startDate']));
                $assstarttime = date("H:i", strtotime($kv['startTime']));
                $assendtime = date("H:i", strtotime($kv['endTime']));
                $assessments[] = array(
                    "startDate" => "$assdates",
                    "endDate" => "$assdates",
                    "startTime" => "$assstarttime",
                    "endTime" => "$assendtime",
                    "modeOfTraining" => "8",
                    "venue" => array
                        (
                        "block" => "$venue_block",
                        "street" => "$venue_street",
                        "floor" => "$venue_floor",
                        "unit" => "$venue_unit",
                        "building" => "$venue_building",
                        "postalCode" => "$venue_postalcode",
                        "room" => "$venue_room",
                        "wheelChairAccess" => "$wheel_chair_access",
                        "primaryVenue" => true,
                    ),
                );
            }
        }
        if (!empty($assessments)) {
            $session_arr = array_merge($sessions, $assessments);
        } else {
            $session_arr = $sessions;
        }

        //print_r($session_arr);exit;
        if (!empty($datas['ClassTrainer'])) {
            $control_6 = implode(",", $control_6);
        }
        $ClassTrainers = $this->get_trainer_details($control_6);
        //print_r($ClassTrainers);exit;
        if (!empty($ClassTrainers)) {
            foreach ($ClassTrainers as $trainer) {
                $trainers[] = array("trainer" => array(
                        "trainerType" => array(
                            "code" => "2",
                            "description" => "New"
                        ),
                        "indexNumber" => 0,
                        "id" => "",
                        "name" => "$trainer->first_name",
                        "email" => "$trainer->off_email_id",
                        "inTrainingProviderProfile" => true,
                        "domainAreaOfPractice" => "$trainer->category_name",
                        "experience" => "",
                        "linkedInURL" => "",
                        "salutationId" => 1,
                        "photo" => array(
                            "name" => "",
                            "content" => ""
                        ),
                        "linkedSsecEQAs" => array(
                            "description" => "",
                            "ssecEQA" => array(
                                "code" => ""
                            )
                        )
                    )
                );
            }
        }
        ///salutationId    = Available value - 1(Mr) 2(Ms) 3(Mdm) 4(Mrs) 5(Dr) 6(Prof).
        //print_r($trainers);print_r($datas);exit;
        $retun = $this->correct_live_dev_api_data($datas['course']->reference_num, $tp_uen);

        $tpg_course_run_json = '{
                    "course": {
                      "courseReferenceNumber": "' . $retun[ref_no] . '",
                      "trainingProvider": {
                        "uen": "' . $retun[tp_uen] . '"
                      },
                      "runs": [
                        {
                          "sequenceNumber": 0,
                          "modeOfTraining": "' . $modeoftraining . '",
                          "registrationDates": {
                            "opening": "' . date("Ymd", strtotime($reg_open_date)) . '",
                            "closing": "' . date("Ymd", strtotime($reg_close_date)) . '"
                          },
                          "courseDates": {
                            "start": "' . date("Ymd", strtotime($start_date)) . '",
                            "end": "' . date("Ymd", strtotime($end_date)) . '"
                          },
                          "scheduleInfoType": {
                            "code": "01",
                            "description": "' . $schedule_info_des . '"
                          },
                          "scheduleInfo": "' . $schedule_info . '",
                          "venue": {
                              "block": "' . $venue_block . '",
                              "street": "' . $venue_street . '",
                              "floor": "' . $venue_floor . '",
                              "unit": "' . $venue_unit . '",
                              "building": "' . $venue_building . '",
                              "postalCode": "' . $venue_postalcode . '",
                              "room": "' . $venue_room . '",
                              "wheelChairAccess": "' . $wheel_chair_access . '"
                          },
                          "intakeSize": ' . $datas['class']->total_seats . ',
                          "courseAdminEmail": "' . $datas['course']->crse_admin_email . '",
                          "threshold": 0,
                          "registeredUserCount": "",
                          "courseVacancy": {
                            "code": "' . $crse_vacancy_code . '",
                            "description": "' . $crse_vacancy_description . '"
                          },
                          "file": {
                            "Name": "",
                            "content": ""
                          },
                          "sessions": 
                            ' . json_encode($session_arr) . '
                          ,
                          "linkCourseRunTrainer": ' . json_encode($trainers) . '
                        }
                      ]
                    }
                  }';


        //print_r($tpg_course_run_json);exit;
        $api_version = 'v1.3';
        $url = "https://" . $retun[domain] . "/courses/runs";
        $response = $this->curl_request('POST', $url, $tpg_course_run_json, $api_version);
        //print_r($response);exit;
        $obj = json_decode($response);
        //$obj = json_decode('{ "data": { "runs": [ { "id": 223389 } ] }, "error": {}, "meta": {}, "status": 200 }');
        $this->session->set_flashdata('resp', $obj);
        $this->session->set_flashdata('cid', $class_id);
        //if($obj->status == 200){
        //redirect('tp_gateway/courserun_status');
        return $obj;
        ///}else{
        //redirect('tp_gateway/check_status');
        //}
    }

    public function get_def_assessments_new($tenant_id, $class_id, $course_id, $assmnt_type = '', $assdate) {
        if ($assmnt_type == 'DEFAULT') {
            $result = $this->db->select('*')->from('class_assmnt_schld')->where('tenant_id', $tenant_id)
                            ->where('class_id', $class_id)->get()->row();
        } elseif ($assmnt_type == 'CUSTOM') {
            $this->db->select('*');
            $this->db->from('class_assmnt_schld');
            $this->db->where('tenant_id', $tenant_id);
            $this->db->where('course_id', $course_id);
            $this->db->where('assmnt_date', $assdate);
            $this->db->where('class_id', $class_id);
            $result = $this->db->get()->result();
        }
        return $result;
    }

    public function get_all_class_schedule_new($tenant_id, $cid, $dt) {
        $result = $this->db->query("select *
                from class_schld where tenant_id='$tenant_id' and class_id='$cid' and class_date='$dt' and session_type_id !='BRK'");
        return $result->result_array();
    }

    //////beloe function added by shubhranshu to delete created courserun completely from the SSG system
    public function delete_courserun_tpg($crse_ref_no, $tp_uen, $courserunid) {
        $retun = $this->correct_live_dev_api_data($crse_ref_no, $tp_uen);
        $tpg_delete_courserun_json = '{"course": {

              "courseReferenceNumber": "' . $retun[ref_no] . '",

              "trainingProvider": {

                "uen": "' . $retun[tp_uen] . '"

              },

              "run": {

                "action": "delete"

              }

            }

          }';



        $api_version = 'v1.3';
        $url = "https://" . $retun[domain] . "/courses/runs/" . $courserunid;
        $response = $this->curl_request('POST', $url, $tpg_delete_courserun_json, $api_version);
        //print_r($tpg_delete_courserun_json);echo $url;exit;
        $obj = json_decode($response);
        return $obj;
    }

    //// added by shubhranshu to get course run id
    public function getCourseByRunId($tpg_course_run_id) {
        $api_version = 'v1.3';
        $retun = $this->correct_live_dev_api_data('', '');
        $url = "https://" . $retun[domain] . "/courses/runs/" . $tpg_course_run_id;
        $response = $this->curl_request('GET', $url, '', $api_version);
        //print_r($tpg_delete_courserun_json);echo $url;exit;
        $obj = json_decode($response);
        return $obj;
    }

    ///added by shubhranshu to fetch the latest ssg sessions
    public function fetch_ssg_session($tenant_id, $CourseRunId, $classId, $tp_uen, $crse_ref_no) {
        $retun = $this->correct_live_dev_api_data($crse_ref_no, $tp_uen);
        $api_version = 'v1.3';
        $url = "https://" . $retun[domain] . "/courses/runs/" . $CourseRunId . '/sessions?uen=' . $retun[tp_uen] . '&courseReferenceNumber=' . $retun[ref_no];
        $response = $this->curl_request('GET', $url, '', $api_version);
        //print_r($response);echo $url;exit;
        //$obj=json_decode($response);
        return $response;
    }

    //// added by shubhranshu to cancel tpg assessmement
    public function create_asssessment_to_tpg($trainee, $tp_uen) {
        $retun = $this->correct_live_dev_api_data($trainee->reference_num, $tp_uen, $trainee->skillCode, $trainee->tax_code);

        $score = empty($trainee->feedback_score) ? 0 : $trainee->feedback_score;
        $grade = empty($trainee->feedback_grade) ? '' : $trainee->feedback_grade;   
        
        if ($trainee->tax_code_type == 'SNG_1') {
            $taxcode_type = 'NRIC';
        } else if ($trainee->tax_code_type == 'SNG_2') {
            $taxcode_type = 'FIN';
        } else if ($trainee->tax_code_type == 'SNG_3') {
            $taxcode_type = 'OTHERS';
        }
        
        $assessment_date = !empty($trainee->assmnt_date) ? $trainee->assmnt_date : $trainee->assessmentDate;
        
        $assessment_json = '{"assessment": {
                      "trainingPartner": {
                        "code": "' . $retun[tp_uen] . '-03",
                        "uen": "' . $retun[tp_uen] . '"
                      },
                      "course": {
                        "referenceNumber": "' . $retun[ref_no] . '",
                        "run": {
                          "id": "' . $trainee->tpg_course_run_id . '"
                        }
                      },
                      "trainee": {
                        "idType": "' . $taxcode_type . '",
                        "id": "' . $trainee->tax_code . '",
                        "fullName": "' . $trainee->fullname . '"
                      },
                      "result": "' . $trainee->result . '",
                      "score": ' . $score . ',
                      "grade": "' . $grade . '",
                      "assessmentDate": "' . $assessment_date . '",
                      "skillCode": "' . $retun[skillcode] . '",
                      "conferringInstitute": {
                        "code": "' . $retun[tp_uen] . '-03"
                      }
                    }

                  }';
        //print_r($assessment_json);exit;
        $encrypted_data = $this->encrypt_decrypt('encrypt', $assessment_json);
        //echo $encrypted_data;exit;
        $api_version = 'v1';
        $url = "https://" . $retun[domain] . "/tpg/assessments";
        $response = $this->curl_request('POST', $url, $encrypted_data, $api_version);
        $decrypted_data = $this->encrypt_decrypt('decrypt', $response);
        $asessment_resp = json_decode($decrypted_data);
        return $asessment_resp;
    }

    //// added by shubhranshu to view assessment from tpg
    public function view_asssessment_from_tpg($assessment_ref_no) {
        $retun = $this->correct_live_dev_api_data('', '');
        $api_version = 'v1';
        $url = "https://" . $retun['domain'] . "/tpg/assessments/details/" . $assessment_ref_no;
        $response = $this->curl_request('GET', $url, '', $api_version);
        $decrypted_data = $this->encrypt_decrypt('decrypt', $response);
        //print_r($response);echo $url;exit;
        //$obj=json_decode($response);
        return $decrypted_data;
    }

    //// added by shubhranshu to update void assessment to tpg
    public function update_void_assessment_to_tpg($fullname, $result, $assessment_date, $score, $grade, $skillcode, $action, $assessment_ref_no) {
        $update_assessment_json = '{"assessment": {
                              "grade": "' . $grade . '",
                              "score": ' . $score . ',
                              "action": "' . $action . '",
                              "result": "' . $result . '",
                              "trainee": {
                                "fullName": "' . $fullname . '"
                              },
                              "skillCode": "' . $skillcode . '",
                              "assessmentDate": "' . date('Y-m-d', strtotime($assessment_date)) . '"
                            }
                        }';

        $retun = $this->correct_live_dev_api_data('', '');
        $encrypted_data = $this->encrypt_decrypt('encrypt', $update_assessment_json);
        $api_version = 'v1';
        $url = "https://" . $retun['domain'] . "/tpg/assessments/details/" . $assessment_ref_no;
        $response = $this->curl_request('POST', $url, $encrypted_data, $api_version);
        $decrypted_data = $this->encrypt_decrypt('decrypt', $response);
        return $decrypted_data;
    }

    //// added by shubhranshu to search assessment
    public function search_assessments($tenant_id, $tp_uen, $crs_ref_no, $crs_run_id) {

        $retun = $this->correct_live_dev_api_data($crs_ref_no, $tp_uen);
        $search_assessment_json = '{"assessments": {
                                  "trainingPartner": {
                                    "uen": "' . $retun[tp_uen] . '",
                                    "code": "' . $retun[tp_uen] . '-03"
                                  },
                                  "course": {
                                    "referenceNumber": "' . $retun[ref_no] . '",
                                    "run": {
                                      "id": "' . $crs_run_id . '"
                                    }
                                  },
                                  "trainee": {
                                    "id": ""
                                  },
                                  "skillCode": "",
                                  "enrolment": {
                                    "referenceNumber": ""
                                  }
                                },
                                "meta": {
                                  "lastUpdateDateFrom": "",
                                  "lastUpdateDateTo": ""
                                },
                                "sortBy": {
                                  "field": "updatedOn",
                                  "order": "asc"
                                },
                                "parameters": {
                                  "page": 0,
                                  "pageSize": 50
                                }
                              }';
        //print_r($search_assessment_json);exit;
        $encrypted_data = $this->encrypt_decrypt('encrypt', $search_assessment_json);
        $api_version = 'v1';
        $url = "https://" . $retun[domain] . "/tpg/assessments/search";
        $response = $this->curl_request('POST', $url, $encrypted_data, $api_version);
        $decrypted_data = $this->encrypt_decrypt('decrypt', $response);
        $asessment_resp = json_decode($decrypted_data);
        return $asessment_resp;
    }

    public function updateEnrolmentReferenceNumber($course_id, $class_id, $user_id, $enrolmentReferenceNumber, $enrolmentReferenceStatus) {

        if (!empty($enrolmentReferenceNumber)) {
            $tenantId = $this->session->userdata('userDetails')->tenant_id;
            $data = array(
                'eid_number' => $enrolmentReferenceNumber,
                'tpg_enrolment_status' => $enrolmentReferenceStatus
            );
            $this->db->trans_start();
            $this->db->where('tenant_id', $tenantId);
            $this->db->where('course_id', $course_id);
            $this->db->where('class_id', $class_id);
            $this->db->where('user_id', $user_id);
            $this->db->update('class_enrol', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public function cancelEnrolment($course_id, $class_id, $user_id, $enrolmentReferenceNumber, $enrolmentReferenceStatus) {

        if (!empty($enrolmentReferenceNumber)) {
            $tenantId = $this->session->userdata('userDetails')->tenant_id;
            $data = array(
                'tpg_enrolment_status' => $enrolmentReferenceStatus
            );
            $this->db->trans_start();
            $this->db->where('tenant_id', $tenantId);
            $this->db->where('course_id', $course_id);
            $this->db->where('class_id', $class_id);
            $this->db->where('user_id', $user_id);
            $this->db->where('eid_number', $enrolmentReferenceNumber);
            $this->db->update('class_enrol', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    /////added by shubhranshu fro submit attendance to tpg
    function submit_attendance_to_tpg($tp_uen, $tpg_course_run_id, $tax_code, $crs_reference_num, $tenant_id, $user_id, $course_id, $class_id, $survey_language, $noOfHours, $tpgCourseId, $tpg_session_id, $attn_status_code, $fullname, $registered_email_id, $idtype, $mobileNo) {

        $retun = $this->correct_live_dev_api_data($crs_reference_num, $tp_uen);


        $tpg_attn_json_data = '{"uen": "' . $retun[tp_uen] . '",
                                        "course": {
                                          "sessionID": "' . $tpg_session_id . '",
                                          "attendance": {
                                            "status": {
                                              "code": ' . $attn_status_code . '
                                            },
                                            "trainee": {
                                              "id": "' . $tax_code . '",
                                              "name": "' . $fullname . '",
                                              "email": "' . $registered_email_id . '",
                                              "idType": {
                                                "code": "' . $idtype . '"
                                              },
                                              "contactNumber": {
                                                "mobile": "' . $mobileNo . '",
                                                "areaCode": null,
                                                "countryCode": 65
                                              },
                                              "numberOfHours": ' . $noOfHours . ',
                                              "surveyLanguage": {
                                                "code": "' . $survey_language . '"
                                              }
                                            }
                                          },
                                          "referenceNumber": "' . $retun[ref_no] . '"
                                        },
                                        "corppassId": "' . $retun[corpassid] . '"
                                      }';

        $encrypted_data = $this->encrypt_decrypt('encrypt', $tpg_attn_json_data);
        //echo $tpg_attn_json_data;exit;
        $api_version = 'v1.3';
        $url = "https://" . $retun[domain] . "/courses/runs/" . $tpg_course_run_id . "/sessions/attendance";
        $response = $this->curl_request('POST', $url, $encrypted_data, $api_version);
        //$decrypted_data = $this->encrypt_decrypt('decrypt', $response);
        //print_r($response);exit;
        $attn_resp = json_decode($response);
        return $attn_resp;
    }
    
    /*
     * Created by abdulla nofal
     * Update class API
     */

    public function update_courserun_tpg() {
        //Training Partner
        $tenant_id = $this->tenant_id;        
        $tenant_details = fetch_tenant_details($tenant_id);
        $trainingPartnerUEN = $tenant_details->comp_reg_no;
        
        extract($_POST);
        $class_id = $this->input->post('class_hid');
        $tpg_course_run_id = $this->input->post('tpg_crse_run_id');
        $crse_ref_no = $this->input->post('crse_ref_no');        
        $crs_admin_email = $this->input->post('crs_admin_email'); //Course admin email is under course run level that can be received the email from 'QR code Attendance Taking','Course Attendance with error' and 'Trainer information not updated'                
        $modeoftraining = $this->input->post('modeoftraining');
        $reg_open_date = date("d-m-Y"); 
        $reg_close_date = $this->input->post('start_date');
        $crse_start_date = $this->input->post('start_date');
        $crse_end_date = $this->input->post('end_date');
        $schedule_info_des = 'Description'; //Course run schedule info Description
        $schedule_info = date('dM', strtotime($crse_start_date)) . ' : ' . date('D', strtotime($crse_start_date)) . ' / ' . date('h:i A', strtotime($this->input->post('start_time'))) . ' - ' . date('h:i A', strtotime($this->input->post('end_date')));
        $venue_building = $this->input->post('venue_building');
        $venue_block = $this->input->post('venue_block');
        $venue_street = $this->input->post('venue_street');
        $venue_floor = $this->input->post('venue_floor');
        $venue_unit = $this->input->post('venue_unit');
        $venue_postalcode = $this->input->post('venue_postalcode');
        $venue_room = $this->input->post('venue_room');
        //Added by abdulla
        $wheel_chair_access = $this->input->post('wheel_chair_accessible');
        if($wheel_chair_access == 0) {
            $wheel_chair_access = 'false';
        } else {
            $wheel_chair_access = 'true';
        }
        $crse_intake_size = $this->input->post('total_seats'); //Course run intake size. It's maximum pax for a class
        $crse_vacancy_code = "A"; //A - Available ,F - Full, L - Limited Vacancy
        $crse_vacancy_description = "Available"; /////A - Available ,F - Full, L - Limited Vacancy
        
        if (!empty($control_4)) {
            $control_4 = implode(",", $control_4);
        }
        if (!empty($control_5)) {
            $control_5 = implode(",", $control_5);
        }
        if (!empty($control_6)) {
            $control_6 = implode(",", $control_6);
        }
        if (!empty($control_7)) {
            $control_7 = implode(",", $control_7);
        }
        if (!empty($control_3)) {
            $control_3 = implode(",", $control_3);
        }
        
        if (!empty($schlded_date)) {
            foreach ($schlded_date as $k => $v) {
                if ($schlded_session_type[$k] != 'BRK') {
                    $dates = date('Ymd', strtotime($schlded_date[$k]));
                    $starttime = date("H:i", strtotime($schlded_start_time[$k]));
                    $endtime = date("H:i", strtotime($schlded_end_time[$k]));
                    $sessions[] = array(
                        "startDate" => "$dates",
                        "endDate" => "$dates",
                        "sessionId" => "$tpg_session_id[$k]",
                        "startTime" => "$starttime",
                        "endTime" => "$endtime",
                        "modeOfTraining" => "$mode_of_training[$k]",                        
                        "action" => "update",          
                        "venue" => array
                            (
                            "room" => "$venue_room",
                            "unit" => "$venue_unit",
                            "block" => "$venue_block",
                            "floor" => "$venue_floor",
                            "street" => "$venue_street",                                                        
                            "building" => "$venue_building",
                            "postalCode" => "$venue_postalcode",                            
                            "primaryVenue" => true,
                            "wheelChairAccess" => "$wheel_chair_access",                            
                        ),
                    );
                }
            }
        }


        if (!empty($assmnt_date)) {
            foreach ($assmnt_date as $k => $v) {

                $assdates = date('Ymd', strtotime($assmnt_date[$k]));
                $assstarttime = date("H:i", strtotime($assmnt_start_time[$k]));
                $assendtime = date("H:i", strtotime($assmnt_end_time[$k]));
                $assessments[] = array(
                    "startDate" => "$assdates",
                    "endDate" => "$assdates",
                    "sessionId" => "Fuchun 019-41618-S1",
                    "startTime" => "$assstarttime",
                    "endTime" => "$assendtime",
                    "modeOfTraining" => "8",
                    "action" => "update",
                    "venue" => array
                        (
                        "room" => "$venue_room",
                        "unit" => "$venue_unit",
                        "block" => "$venue_block",
                        "floor" => "$venue_floor",
                        "street" => "$venue_street",                                                
                        "building" => "$venue_building",
                        "postalCode" => "$venue_postalcode",
                        "primaryVenue" => true,
                        "wheelChairAccess" => "$wheel_chair_access",                        
                    ),
                );
            }
        }
        if (!empty($assessments)) {
            $session_arr = array_merge($sessions, $assessments);
        } else {
            $session_arr = $sessions;
        }
        
        //print_r($session_arr);exit;
        $ClassTrainers = $this->get_trainer_details($control_5);
        //print_r($ClassTrainers);exit;
        if (!empty($ClassTrainers)) {
            $i=1;
            foreach ($ClassTrainers as $trainer) {
                $trainers[] = array("trainer" => array(
                        "id" => "",
                        "name" => "$trainer->first_name",
                        "email" => "$trainer->off_email_id",
                        "photo" => array(
                                "name" => "",
                                "content" => ""
                            ),
                        "experience" => "",
                        "indexNumber" => "$i",
                        "linkedInURL" => "",
                        "trainerType" => array(
                                "code" => "2",
                                "description" => "New"
                            ),
                        "salutationId" => 1,
                        "inTrainingProviderProfile" => true,
                        "domainAreaOfPractice" => "$trainer->category_name",                                                                                                                        
                        "linkedSsecEQAs" => array(
                            "description" => "",
                            "ssecEQA" => array(
                                "code" => ""
                            )
                        )
                    )
                );
                $i++;
            }
        }
                        
        $retun = $this->correct_live_dev_api_data($crse_ref_no, $trainingPartnerUEN);

        $tpg_course_run_json = '{
                                "course": {
                                  "run": {
                                    "file": {
                                        "Name": "",
                                        "content": ""
                                      },
                                    "venue": {
                                      "room": "' . $venue_room . '",
                                      "unit": "' . $venue_unit . '",
                                      "block": "' . $venue_block . '",
                                      "floor": "' . $venue_floor . '",
                                      "street": "' . $venue_street . '",
                                      "building": "' . $venue_building . '",
                                      "postalCode": ' . $venue_postalcode . ',
                                      "wheelChairAccess": ' . $wheel_chair_access . '
                                    },
                                    "action": "update",
                                    "sessions": ' . json_encode($session_arr) . ',
                                    "threshold": 0,
                                    "intakeSize": ' . $crse_intake_size . ',
                                    "courseDates": {
                                        "start": "' . date("Ymd", strtotime($crse_start_date)) . '",
                                        "end": "' . date("Ymd", strtotime($crse_end_date)) . '"
                                    },
                                    "scheduleInfo": "' . $schedule_info . '",
                                    "courseVacancy": {
                                        "code": "' . $crse_vacancy_code . '",
                                        "description": "' . $crse_vacancy_description . '"
                                    },
                                    "modeOfTraining": "' . $modeoftraining . '",
                                    "sequenceNumber": 0,
                                    "courseAdminEmail": "' . $crs_admin_email . '",
                                    "scheduleInfoType": {
                                      "code": "01",
                                      "description": "Description"
                                    },
                                    "registrationDates": {
                                        "closing": "' . date("Ymd", strtotime($reg_close_date)) . '",
                                        "opening": "' . date("Ymd", strtotime($reg_open_date)) . '"                                       
                                    },
                                    "registeredUserCount": "",
                                    "linkCourseRunTrainer": ' . json_encode($trainers) . '
                                  },
                                  "trainingProvider": {
                                    "uen": "' . $retun[tp_uen] . '"
                                  },
                                  "courseReferenceNumber": "' . $retun[ref_no] . '"
                                }
                            }';
        print_r($tpg_course_run_json);exit;
        $api_version = 'v1.3';
        $url = "https://" . $retun[domain] . "/courses/runs/". $tpg_course_run_id;
        $response = $this->curl_request('POST', $url, $tpg_course_run_json, $api_version);        
        $obj = json_decode($response);       
        $this->session->set_flashdata('resp', $obj);
        $this->session->set_flashdata('cid', $class_id);        
        return $obj;                       
    }
}

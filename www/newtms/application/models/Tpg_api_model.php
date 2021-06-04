<?php

class Tpg_api_Model extends CI_Model {

/**
     * this function to get trainer names
     */    
    private $user;
    public function __construct() {
        parent::__construct();
        $this->load->helper('common');
        $this->sess_user = $this->session->userdata('userDetails'); // added by shubhranshu to het the user data
        $this->user = $this->session->userdata('userDetails');
    }
    
    
    public function get_trainer_details($trainer_id) 
    {        
        $tenantId = $this->session->userdata('userDetails')->tenant_id;
        $tids = explode(',', $trainer_id);
        if (!empty($tids)) 
        {
            $this->load->model('course_model', 'course');
            
            foreach ($tids as $tid) 
            {                
                $sql = "SELECT pers.user_id,tu.registered_email_id, pers.first_name, pers.last_name, rl.role_id 
                        FROM `tms_users_pers` pers, internal_user_role rl, tms_users tu
                        WHERE pers.tenant_id = rl.tenant_id 
                        AND pers.user_id = rl.user_id 
                        AND tu.user_id = pers.user_id 
                        AND pers.tenant_id = '$tenantId' 
                        AND rl.role_id='TRAINER' 
                        AND rl.user_id='$tid' ";                
                $query = $this->db->query($sql);

                $data = $query->row(0);
                $trainer[] = $data;

                
            }
            return $trainer;
        }
    }
    
    public function updateCourseRunId($class_id,$crse_run_id){
        $tenantId = $this->session->userdata('userDetails')->tenant_id;
         $data = array(
                    'tpg_course_run_id' => $crse_run_id
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
    }
    
    public function get_all_class_schedule($tenant_id, $cid) {
        $result = $this->db->query("select class_date, session_type_id, session_start_time,session_end_time
                from class_schld where tenant_id='$tenant_id' and class_id='$cid' and session_type_id!='BRK' 
                order by class_date ASC, session_start_time ASC");
        return $result->result_array();
    }
    
    
    ///////////////////////////////API MODEL START///////////////////////////////////////////
    
    public function create_courserun_tpg($tenantId, $userId){
       $crse_ref_no= $this->input->post('crse_ref_no');
       $tp_uen= $this->input->post('tp_uen');
       $modeoftraining= $this->input->post('modeoftraining');
       $crs_admin_email= $this->input->post('crs_admin_email');
       $reg_open_date= $this->input->post('reg_open_date');
       $reg_close_date= $this->input->post('reg_close_date');
       $crse_start_date= $this->input->post('crse_start_date');
       $crse_end_date= $this->input->post('crse_end_date');
       $schedule_info_code= $this->input->post('schedule_info_code');
       $schedule_info_des= $this->input->post('schedule_info_des');
       $schedule_info= $this->input->post('schedule_info');
       $venue_building= $this->input->post('venue_building');
       $venue_block= $this->input->post('venue_block');
       $venue_street= $this->input->post('venue_street');
       $venue_floor= $this->input->post('venue_floor');
       $venue_unit= $this->input->post('venue_unit');
       $venue_postalcode= $this->input->post('venue_postalcode');
       $venue_room= $this->input->post('venue_room');
       $crse_intake_size= $this->input->post('crse_intake_size');
       $crse_vacancy_code= $this->input->post('crse_vacancy_code');
       $crse_vacancy_description= $this->input->post('crse_vacancy_description');
       $trainer_name= $this->input->post('trainer_name');
       $trainer_id= $this->input->post('trainer_id');
       $trainer_email= $this->input->post('trainer_email');
       $course_id= $this->input->post('course_id');
       $class_id= $this->input->post('class_id');
        $tenant_id = $this->tenant_id;
       $booked_seats = $this->classModel->get_class_booked($course_id, $class_id,$tenant_id);
       $sessions = $this->tpgModel->get_all_class_schedule($tenant_id, $class_id);
       foreach($sessions as $session){
           if($session[session_type_id] != 'BRK'){
               $dates = date('Ymd', strtotime($session['class_date']));
               $session_arr[] = array(
                "startDate" => "$dates",
                "endDate" => "$dates",
                "startTime" => "$session[session_start_time]",
                "endTime" => "$session[session_end_time]",
                "modeOfTraining" => "$modeoftraining",
                "venue" => array
                    (
                        "block" => "$venue_block",
                        "street" => "$venue_street",
                        "floor" => "$venue_floor",
                        "unit" => "$venue_unit",
                        "building" => "$venue_building",
                        "postalCode" => "$venue_postalcode",
                        "room" => "$venue_room",
                        "wheelChairAccess" => true,
                        "primaryVenue" => true,
                    ),

            );
          }
       }
       
       if(TPG_ENVIRONMENT == 'PRODUCTION'){
           $crse_ref_no = $crse_ref_no;
           $tp_uen  = $tp_uen;
       }else{
          $crse_ref_no =  'TGS-2020002096';
          $tp_uen = '201000372W';
       }
       
       
        $tpg_course_run_json='{
                    "course": {
                      "courseReferenceNumber": "'.$crse_ref_no.'",
                      "trainingProvider": {
                        "uen": "'.$tp_uen.'"
                      },
                      "runs": [
                        {
                          "sequenceNumber": 0,
                          "modeOfTraining": "'.$modeoftraining.'",
                          "registrationDates": {
                            "opening": '.$reg_open_date.',
                            "closing": '.$reg_close_date.' 
                          },
                          "courseDates": {
                            "start": '.$crse_start_date.',
                            "end": '.$crse_end_date.'
                          },
                          "scheduleInfoType": {
                            "code": "'.$schedule_info_code.'",
                            "description": "'.$schedule_info_des.'"
                          },
                          "scheduleInfo": "'.$schedule_info.'",
                          "venue": {
                              "block": "'.$venue_block.'",
                              "street": "'.$venue_street.'",
                              "floor": "'.$venue_floor.'",
                              "unit": "'.$venue_unit.'",
                              "building": "",
                              "postalCode": "'.$venue_postalcode.'",
                              "room": "'.$venue_room.'",
                              "wheelChairAccess": true
                          },
                          "intakeSize": '.$crse_intake_size.',
                          "courseAdminEmail": "'.$crs_admin_email.'",
                          "threshold": 0,
                          "registeredUserCount": '.$booked_seats.',
                          "courseVacancy": {
                            "code": "'.$crse_vacancy_code.'",
                            "description": "'.$crse_vacancy_description.'"
                          },
                          "file": {
                            "Name": "",
                            "content": ""
                          },
                          "sessions": 
                            '.json_encode($session_arr).'
                          ,
                          "linkCourseRunTrainer": [
                            {
                              "trainer": {
                                "indexNumber": 0,
                                "id": "'.$tenant_id.'-TMS-'.$trainer_id.'-'.$course_id.'-'.$class_id.'",
                                "name": "'.$trainer_name.'",
                                "inTrainingProviderProfile": true,
                                "domainAreaOfPractice": "Testing Management in Computer Application and Diploma in Computer Application",
                                "experience": "Testing ABC",
                                "linkedInURL": "https://sg.linkedin.com/company/linkedin/abc",
                                "salutationId": 1,
                                "photo": {
                                  "name": "",
                                  "content": ""
                                },
                                "email": "'.$trainer_email.'",
                                "trainerType": {
                                  "code": "2",
                                  "description": "New"
                                },
                                "linkedSsecEQAs": [
                                  {
                                    "description": "EQA test 4",
                                    "ssecEQA": {
                                      "code": "12"
                                    }
                                  }
                                ]
                              }
                            }
                          ]
                        }
                      ]
                    }
                  }';
       
       
        //print_r($tpg_course_run_json);exit;
        $api_version = 'v1.3';
        $url = "https://uat-api.ssg-wsg.sg/courses/runs";
        $response = $this->curl_request('POST',$url,$tpg_course_run_json,$api_version);
        //print_r($response);exit;
        $obj=json_decode($response);
        //$obj = json_decode('{ "data": { "runs": [ { "id": 223389 } ] }, "error": {}, "meta": {}, "status": 200 }');
        $this->session->set_flashdata('resp',$obj);
        $this->session->set_flashdata('cid',$class_id);
        if($obj->status == 200){
            redirect('tp_gateway/courserun_status');
        }else{
            redirect('tp_gateway/check_status');
        }
        
    }

}
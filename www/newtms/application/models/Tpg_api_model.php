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
    
    public function curl_request($mode,$url,$encrypted_data,$api_version){
       // echo $encrypted_data;exit;
        $pemfile = "/var/www/newtms/assets/certificates/cert.pem";
        $keyfile = "/var/www/newtms/assets/certificates/key.pem";
        //print_r($data);exit;
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
         if($response === false){
             print_r(curl_error($curl));exit;
         }else{
             //print_r(json_decode($response));exit;
             return $response;
         }
        curl_close($curl);
    }
    
    public function correct_live_dev_api_data($crse_ref_no,$tp_uen){
        if(TPG_ENVIRONMENT == 'PRODUCTION'){
           $crse_ref_no = $crse_ref_no;
           $tp_uen  = $tp_uen;
           $domain='api.ssg-wsg.sg';
        }else{
          $crse_ref_no =  'TGS-2020002096';
          $tp_uen = '201000372W';
          $domain='uat-api.ssg-wsg.sg';
        }
        $data =array(
            'ref_no' => $crse_ref_no,
            'tp_uen' => $tp_uen,
            'domain' => $domain
        );
        return $data;
    }
    
    public function get_trainer_details($trainer_ids) 
    {    
        $tenantId = $this->session->userdata('userDetails')->tenant_id;
        $tids = explode(',', $trainer_ids);
        $ids = join("','",$tids); 
                    
                $sql = "SELECT iued.off_email_id,pers.user_id,tu.registered_email_id, pers.first_name, pers.last_name, rl.role_id
                        FROM `tms_users_pers` pers
                        JOIN internal_user_role rl on rl.tenant_id = pers.tenant_id AND rl.user_id = pers.user_id
                        JOIN tms_users tu on tu.user_id = pers.user_id
                        JOIN internal_user_emp_detail iued on iued.user_id = pers.user_id AND iued.tenant_id = pers.tenant_id
                        WHERE pers.tenant_id = '$tenantId' 
                        AND rl.role_id='TRAINER' 
                        AND rl.user_id in ('$ids')";                
                $query = $this->db->query($sql);
                $trainer = $query->result();
                //echo $this->db->last_query();exit;
                
                
            return $trainer;
        
        }
    
    public function updateCourseRunId($class_id,$crse_run_id){
        if(!empty($crse_run_id)){
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
        }else{
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
    
    public function create_courserun_tpg($tenant_id, $userId,$tp_uen){
        extract($_POST);
       $crse_ref_no= $this->input->post('crse_ref_no');
       $modeoftraining= $this->input->post('modeoftraining');
       $crs_admin_email= $this->input->post('crs_admin_email');//Course admin email is under course run level that can be received the email from 'QR code Attendance Taking','Course Attendance with error' and 'Trainer information not updated'
       $reg_open_date= $this->input->post('start_date');
       $reg_close_date= $this->input->post('end_date');
       $crse_start_date= $this->input->post('start_date');
       $crse_end_date= $this->input->post('end_date');
       $schedule_info_des= 'Description';//Course run schedule info Description
       $schedule_info= date('dM', strtotime($reg_open_date)).' : '.date('D', strtotime($reg_open_date)).' / '.date('h:i A', strtotime($this->input->post('start_time'))).' - '.date('h:i A', strtotime($this->input->post('end_date')));
       $venue_building= $this->input->post('venue_building');
       $venue_block= $this->input->post('venue_block');
       $venue_street= $this->input->post('venue_street');
       $venue_floor= $this->input->post('venue_floor');
       $venue_unit= $this->input->post('venue_unit');
       $venue_postalcode= $this->input->post('venue_postalcode');
       $venue_room= $this->input->post('venue_room');
       $crse_intake_size= $this->input->post('total_seats'); //Course run intake size. It's maximum pax for a class
       $crse_vacancy_code="A"; //A - Available ,F - Full, L - Limited Vacancy
       $crse_vacancy_description= "Available";/////A - Available ,F - Full, L - Limited Vacancy
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
       $trainer_name= $this->input->post('trainer_name');
       $trainer_id= $this->input->post('trainer_id');
       $trainer_email= $this->input->post('trainer_email');
       $course_id= $this->input->post('class_course');
            
        if (!empty($schlded_date)) {    
            foreach ($schlded_date as $k => $v) {
                if($schlded_session_type[$k] != 'BRK'){
                    $dates = date('Ymd', strtotime($schlded_date[$k]));
                    $session_arr[] = array(
                     "startDate" => "$dates",
                     "endDate" => "$dates",
                     "startTime" => "$schlded_start_time[$k]",
                     "endTime" => "$schlded_end_time[$k]",
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
        }
        
        $ClassTrainers = $this->get_trainer_details($control_5);
        //print_r($ClassTrainers);exit;
        if (!empty($ClassTrainers)) {    
            foreach ($ClassTrainers as $trainer) {
                $trainers[] = array("trainer"=> array(
                  "trainerType"=> array(
                        "code"=> "2",
                        "description"=> "New"
                  ),
                  "indexNumber"=> 0,
                  "id"=> "",
                  "name"=> "$trainer->first_name.' '.$trainer->last_name",
                  "email"=> "$trainer->off_email_id",
                  "inTrainingProviderProfile"=> true,
                  "domainAreaOfPractice"=> "Testing Management in Computer Application and Diploma in Computer Application",
                  "experience"=> "Testing ABC",
                  "linkedInURL"=> "https=>//sg.linkedin.com/company/linkedin/abc",
                  "salutationId"=> 1,
                  "photo"=> array(
                        "name"=> "",
                        "content"=> ""
                  ),
                  "linkedSsecEQAs"=> array(
                          "description"=> "EQA test 4",
                          "ssecEQA"=> array(
                                "code"=> "12"
                          ) 
                        )
                    )
                );
            }
        }
       ///salutationId    = Available value - 1(Mr) 2(Ms) 3(Mdm) 4(Mrs) 5(Dr) 6(Prof).
       
       $retun = $this->correct_live_dev_api_data($crse_ref_no,$tp_uen);
       
        $tpg_course_run_json='{
                    "course": {
                      "courseReferenceNumber": "'.$retun[ref_no].'",
                      "trainingProvider": {
                        "uen": "'.$retun[tp_uen].'"
                      },
                      "runs": [
                        {
                          "sequenceNumber": 0,
                          "modeOfTraining": "'.$modeoftraining.'",
                          "registrationDates": {
                            "opening": "'.date("Ymd", strtotime($reg_open_date)).'",
                            "closing": "'.date("Ymd", strtotime($reg_close_date)).'"
                          },
                          "courseDates": {
                            "start": "'.date("Ymd", strtotime($crse_start_date)).'",
                            "end": "'.date("Ymd", strtotime($crse_end_date)).'"
                          },
                          "scheduleInfoType": {
                            "code": "01",
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
                          "registeredUserCount": "",
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
                          "linkCourseRunTrainer": '.json_encode($trainers).'
                        }
                      ]
                    }
                  }';
       
       
        ///print_r($tpg_course_run_json);exit;
        $api_version = 'v1.3';
        $url = "https://".$retun[domain]."/courses/runs";
        $response = $this->curl_request('POST',$url,$tpg_course_run_json,$api_version);
        //print_r($response);exit;
        $obj=json_decode($response);
        //$obj = json_decode('{ "data": { "runs": [ { "id": 223389 } ] }, "error": {}, "meta": {}, "status": 200 }');
        $this->session->set_flashdata('resp',$obj);
        $this->session->set_flashdata('cid',$class_id);
        //if($obj->status == 200){
            //redirect('tp_gateway/courserun_status');
            return $obj;
        ///}else{
            //redirect('tp_gateway/check_status');
        //}
        
    }
    
    public function delete_courserun_tpg($crse_ref_no,$tp_uen,$courserunid){
        $retun = $this->correct_live_dev_api_data($crse_ref_no,$tp_uen);
        $tpg_delete_courserun_json='{

            "course": {

              "courseReferenceNumber": "'.$retun[ref_no].'",

              "trainingProvider": {

                "uen": "'.$retun[tp_uen].'"

              },

              "run": {

                "action": "delete"

              }

            }

          }';
        
        
        
        $api_version = 'v1.3';
        $url = "https://".$retun[domain]."/courses/runs/".$courserunid;
        //$response = $this->curl_request('POST',$url,$tpg_delete_courserun_json,$api_version);
        //print_r($tpg_delete_courserun_json);echo $url;exit;
        $obj=json_decode($response);
        return $obj;
    }
    


}
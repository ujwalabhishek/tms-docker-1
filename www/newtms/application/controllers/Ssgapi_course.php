<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * his is the controller class for Accounting Use Cases
 */

class ssgapi_course extends CI_Controller {

    private $user;

    /**
     * constructor - loads Model and other objects required in this controller
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('class_trainee_model', 'classTraineeModel');
        $this->load->model('class_Model', 'classModel');
        $this->load->model('company_model', 'companyModel');
        $this->load->model('tenant_model', 'tenantModel');
        $this->load->helper('common');
        $this->load->helper('metavalues_helper');
        $this->load->model('meta_values', 'meta');
        $this->user = $this->session->userdata('userDetails');
        $this->tenant_id = $this->session->userdata('userDetails')->tenant_id;
    }

    /*
     * This function loads the initial static page for accounting.
      */
   public function index() {
       $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'Accounting';
        $data['main_content'] = 'classtrainee/accounting';
        $this->load->view('layout', $data);
    }
    
    public function list_search_course(){
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'Accounting';
        $data['main_content'] = 'ssgapi_course/course';
        $this->load->view('layout', $data);
    }
    
    public function get_course_list_autocomplete(){   
        
        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');

        //$encr = base64_encode('c0d3cf1102b248a097846d7232d6ad8f:YTlkNzgyN2YtMjEyNi00ZjU0LWIxMTctMTlhMGMzODY4YWJm');
        $encr = base64_encode('c785f2331e314192a886bafcc8cd99b4:OTc1NTNlYjUtNmM5ZC00ZjNlLTg2ODMtNGExNWFiZGM2ODA3');
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://public-api.ssg-wsg.sg/dp-oauth/oauth/token",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_POST =>1,
        CURLOPT_POSTFIELDS => "grant_type=client_credentials",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => array(
       "Authorization: Basic $encr",
       "Cache-Control: no-cache",
       "Content-Type: application/x-www-form-urlencoded"
       
        ),
      ));
        
        $response_token = curl_exec($curl);

        curl_close($curl);

        $response_token=json_decode($response_token);
        
        
        
        //$result = file_get_contents($google_api_url);

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://public-api.ssg-wsg.sg/courses/directory?pageSize=2&page=1&keyword=$query_string",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
       "Authorization: Bearer $response_token->access_token",
       "Cache-Control: no-cache",
       "Content-Type: application/x-www-form-urlencoded",
       "grant_type=client_credentials"
        ),
      ));


        $response = curl_exec($curl);

        curl_close($curl);

        //print_r(json_decode($response));exit;
        
        
        $resp= json_decode($response);
        foreach ($resp->data->courses as $result) {
                $matches[] = array(
                    'label' => $result->title,
                    'key' => $result->referenceNumber
                );
            }
        echo json_encode($matches);
        exit;
    }
    
    public function course_details(){
        $query_string = $this->input->get('course_code_id');

        $encr = base64_encode('c785f2331e314192a886bafcc8cd99b4:OTc1NTNlYjUtNmM5ZC00ZjNlLTg2ODMtNGExNWFiZGM2ODA3');
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://public-api.ssg-wsg.sg/dp-oauth/oauth/token",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_POST =>1,
        CURLOPT_POSTFIELDS => "grant_type=client_credentials",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => array(
       "Authorization: Basic $encr",
       "Cache-Control: no-cache",
       "Content-Type: application/x-www-form-urlencoded"
       
        ),
      ));
        
        $response_token = curl_exec($curl);

        curl_close($curl);

        $response_token=json_decode($response_token);
        
        
        
        //$result = file_get_contents($google_api_url);

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://public-api.ssg-wsg.sg/courses/directory/$query_string",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
       "Authorization: Bearer $response_token->access_token",
       "Cache-Control: no-cache",
       "Content-Type: application/x-www-form-urlencoded",
       "grant_type=client_credentials"
        ),
      ));


        $response = curl_exec($curl);

        curl_close($curl);

        //print_r(json_decode($response));exit;
        
        
        $data['resp']= json_decode($response)->data->courses[0];
        
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'SSG API COURSE DETAILS';
        $data['main_content'] = 'ssgapi_course/view_course';
        $this->load->view('layout', $data);
    }
    
    
    public function course_details_by_run_id(){
        $query_string = $this->input->get('course_run_id');
        $dat ='{
  "course": {
    "courseReferenceNumber": "TGS-2020002052",
    "trainingProvider": {
      "uen": "201000372W"
    },
    "runs": [
      {
        "sequenceNumber": 0,
        "modeOfTraining": "1",
        "registrationDates": {
          "opening": 20211024,
          "closing": 20211024 
        },
        "courseDates": {
          "start": 20211024,
          "end": 20211024
        },
        "scheduleInfoType": {
          "code": "01",
          "description": "Description"
        },
        "scheduleInfo": "04Mar : Sat / 5 Sats / 9am - 6pm",
        "venue": {
          "block": "",
          "street": "",
          "floor": "1",
          "unit": "1",
          "building": "",
          "postalCode": 760635,
          "room": "1",
          "wheelChairAccess": true
        },
        "intakeSize": 70,
        "courseVacancy": {
          "code": "L",
          "description": "Limited Vacancy"
        },
        "file": {
          "Name": "",
          "content": ""
        },
        "sessions": [
          {
            "startDate": "20211024",
            "endDate": "20211024",
            "startTime": "11:00",
            "modeOfTraining": "3",
            "endTime": "17:30",
            "venue": {
              "block": "112A",
              "street": "Street ABC",
              "floor": "1",
              "unit": "1",
              "building": "",
              "postalCode": 760635,
              "room": "24",
              "wheelChairAccess": true,
              "primaryVenue": true
            }
          }
        ],
        "linkCourseRunTrainer": [
          {
            "trainer": {
              "indexNumber": 0,
              "id": "FE9DA6F2-103D-4E2A-8AD1-D8E246E002155",
              "name": "ww",
              "inTrainingProviderProfile": true,
              "domainAreaOfPractice": "Testing Management in Computer Application and Diploma in Computer Application",
              "experience": "Testing ABC",
              "linkedInURL": "https://sg.linkedin.com/company/linkedin/abc",
              "salutationId": 1,
              "photo": {
                "name": "",
                "content": ""
              },
              "email": "abc@test.com",
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
        $pemfile = "/var/www/newtms/assets/certificates/cert.pem";
        $keyfile = "/var/www/newtms/assets/certificates/key.pem";
        //$url = "https://uat-api.ssg-wsg.sg/courses/runs/$query_string";
        $url = "https://uat-api.ssg-wsg.sg/courses/runs";
        //$requestXml =  file_get_contents("net.xml");
        $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'x-api-version: v1.2';
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        $user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';
 
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_SSLCERT => $pemfile,
        CURLOPT_SSLCERTTYPE => 'PEM', 
        CURLOPT_SSLKEY => $keyfile, 
        CURLOPT_POSTFIELDS, $dat, 
        CURLOPT_HTTPHEADER => array(
       "Authorization:  ",
       "Cache-Control: no-cache",
       "Content-Type: application/json"
      
        ),
      ));
        
        
        
        
        
        
        
         $response = curl_exec($curl);
         if($response === false){
             print_r(curl_error($curl));exit;
         }else{
             print_r(json_decode($response));exit;
         }
        curl_close($curl);

        
    }
}
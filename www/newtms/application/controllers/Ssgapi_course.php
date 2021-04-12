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
    
    public function curl_request($mode,$url,$data,$api_version){
        
        $pemfile = "/var/www/newtms/assets/certificates/cert.pem";
        $keyfile = "/var/www/newtms/assets/certificates/key.pem";
        $encrypted = 'ggrR1uwMpea4GWQbhu6+iZ/KZvwhlblrRspkqEg9dVszEjqIiDKnWe4u6PfsD/ntzFfbazfu1I6YmomjmsaCCXPEdJ6sPmrVDyxgVvnScrn6XhZXRQMRpXCSwC5PUh0SXEyr/jw0HtsOFT0JseoJ7nxj8qM/rKv4e9OhNmrIysykBlfEAZ3MsCfnZL9O7kpsVvi2yANJfNoVYBSAs6hUdHc5jlvn2tmLf7kKMNiaP/x7rjB4+XGnWbXfTxQXZLO7wf9fZctrqWIJwqU32PkQEgKgkSWwEVDqh0rzS4zQTd6oYJTyd+psdoU8LHNpCP0Qs21iSYYLJAQNDzLOeXP6ijOCGDe93cdzgA0DIWQoCo4C7rtC0Se12YPQLpwb6W6EG1OpFkPlXqn4+cer88Fn4n0m87xdnmUfTizFFokvLdSbXNim5ipe5wW0lvwIKPreUwsi6okX6Cg+wXR1U4CjDfwgkMujkIE8P9sXspqI6p3t8LutLbVIm/U85kfmPMLTVS6vEiwaAmGlKR6Q7/v4Hly5j8EZufXbDvIwNEMNdNO+imkHOeZw6wfM5lCrM2SeRCCE22oIwhMMmYt8eXQf8yYspAMX7OqI9zXGXchfh5q5Vtpshj0HPxMowgjO5UXBCLmixbaEChA1Er+xjdXuv+UQthYGChntrM04wtEM3KiMNT0Pq98VE9gUimdFu8VspgKbgpBua0jFIEi8g7rDkHLqKaTbU2aiCyDnR4VXOHIpiQ1kl3mKdxvbNftWA5tdCqoplu0bdGoe2MD0FIxipO+J28JA8EYphMn+o2nClp3Q/O9iQCcwd+rkF/6aQyNQ0beMs9KlWzzVfWDRsgRrRQ==';
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
        CURLOPT_POSTFIELDS => $encrypted, 
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
             print_r($response);exit;
         }
        curl_close($curl);
    }
    

    function encrypting($string, $key) 
    {
       $result = '';
       for($i=0; $i<strlen($string); $i++) {
         $char = substr($string, $i, 1);
         $keychar = substr($key, ($i % strlen($key))-1, 1);
         $char = chr(ord($char)+ord($keychar));
         $result.=$char;
       }
       return base64_encode($result);
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
        
        $pemfile = "/var/www/newtms/assets/certificates/cert.pem";
        $keyfile = "/var/www/newtms/assets/certificates/key.pem";
        $url = "https://uat-api.ssg-wsg.sg/courses/runs/$query_string";
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
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_SSLCERT => $pemfile,
        CURLOPT_SSLCERTTYPE => 'PEM', 
        CURLOPT_SSLKEY => $keyfile, 
        //CURLOPT_POSTFIELDS, $requestXml, 
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
    
    public function course_details_entry(){
        $query_string = $this->input->get('course_run_id');
        $dat = array(
            'course' =>
            array(
                'courseReferenceNumber' => 'TGS-2020002106',
                'trainingProvider' =>
                array(
                    'uen' => '201000372W',
                ),
                'runs' =>
                array(
                    0 =>
                    array(
                        'sequenceNumber' => 0,
                        'modeOfTraining' => '1',
                        'registrationDates' =>
                        array(
                            'opening' => 20211124,
                            'closing' => 20211124,
                        ),
                        'courseDates' =>
                        array(
                            'start' => 20211124,
                            'end' => 20211124,
                        ),
                        'scheduleInfoType' =>
                        array(
                            'code' => '01',
                            'description' => 'Description',
                        ),
                        'scheduleInfo' => '04Mar : Sat / 5 Sats / 9am - 6pm',
                        'venue' =>
                        array(
                            'block' => '',
                            'street' => '',
                            'floor' => '1',
                            'unit' => '1',
                            'building' => '',
                            'postalCode' => 760635,
                            'room' => '1',
                            'wheelChairAccess' => true,
                        ),
                        'intakeSize' => 70,
                        'courseVacancy' =>
                        array(
                            'code' => 'L',
                            'description' => 'Limited Vacancy',
                        ),
                        'file' =>
                        array(
                            'Name' => '',
                            'content' => '',
                        ),
                        'sessions' =>
                        array(
                            0 =>
                            array(
                                'startDate' => '20211124',
                                'endDate' => '20211124',
                                'startTime' => '11:00',
                                'modeOfTraining' => '3',
                                'endTime' => '17:30',
                                'venue' =>
                                array(
                                    'block' => '112A',
                                    'street' => 'Street ABC',
                                    'floor' => '1',
                                    'unit' => '1',
                                    'building' => '',
                                    'postalCode' => 760635,
                                    'room' => '24',
                                    'wheelChairAccess' => true,
                                    'primaryVenue' => true,
                                ),
                            ),
                        ),
                        'linkCourseRunTrainer' =>
                        array(
                            0 =>
                            array(
                                'trainer' =>
                                array(
                                    'indexNumber' => 0,
                                    'id' => 'FE9DA6F2-103D-4E2A-8AD1-D8E2464533',
                                    'name' => 'ww',
                                    'inTrainingProviderProfile' => true,
                                    'domainAreaOfPractice' => 'Testing Management in Computer Application and Diploma in Computer Application',
                                    'experience' => 'Testing ABC',
                                    'linkedInURL' => 'https://sg.linkedin.com/company/linkedin/abc',
                                    'salutationId' => 1,
                                    'photo' =>
                                    array(
                                        'name' => '',
                                        'content' => '',
                                    ),
                                    'email' => 'abc@test.com',
                                    'trainerType' =>
                                    array(
                                        'code' => '2',
                                        'description' => 'New',
                                    ),
                                    'linkedSsecEQAs' =>
                                    array(
                                        0 =>
                                        array(
                                            'description' => 'EQA test 4',
                                            'ssecEQA' =>
                                            array(
                                                'code' => '12',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $dat=json_encode($dat);
        $pemfile = "/var/www/newtms/assets/certificates/cert.pem";
        $keyfile = "/var/www/newtms/assets/certificates/key.pem";
        
        $url = "https://uat-api.ssg-wsg.sg/courses/runs";
        //$requestXml =  file_get_contents("net.xml");
       
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
        CURLOPT_POSTFIELDS => $dat, 
        CURLOPT_HTTPHEADER => array(
       "Authorization:  ",
       "Cache-Control: no-cache",
       "Content-Type: application/json",
       "x-api-version: v1.2"
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
    
    
    public function session_attendace_upload(){
        //$query_string = $this->input->get('course_run_id');
        $dat = "NMKIMzLMLpcTdmLHTX5ShfrnM00GYz2s4cL9QiJ0nGU2H7SbHxhwFPXuVY+hBlJba1EAfZ4MCR4eahxG+mTnfQqTayLkqjbZbcAVXoCA+H6XMBmOvo/sG20qISZPoY3Bag+hQwDLwY7RjjhiRUgKUVoGFHaxg01L/ZdcHiBoTZYKmfnzD5U5aM3TPY9ijLF1GKPWTjjXNnzns2zA6FugU/4LpZKsw7XU2sqHXpcePevLMsWeKPuAy2mtGhju+4tuen6Pk82Ec+MDiBE9RU7ByPL1g8ZrTWn/2lu+Vw4pOC7Pk0gj6sCh5t/JyQJWhOTH2yKM68usr5DzVhCgvno8UtD+AnYJbLrUMTAv4iWjEYp/ZMMJo1XNi/Jsd1CMpu7swQPaUcntTdtW2SUPPrcktItgSe8YXuEMK1sw6frzBQrSKhrj4JSIWzor7rNL+5AHxaBFGvXxIb0dNQpTABQKbmdeRD6DiWN4yRtZulayrNdyE4VvqgF6OvHwucTdbu0D0GdRVKVNfiTSnWlFOoPrjOf16BCX+3CY/u4+R90CmFm2020l+dwXDXgG0oQPQNzD6WWuPjUtMpnWHUP4O7ypKWEHnjCqOxdmDa+K7VQjADFBQCP24TtNBO1gnIVW6iE9bE2oe0MmRarR8C0KpuHGN34NOGFxRE0ouPbxZfRBdx9aXbXc58pbPVxavGAyBXE4MRpYMyqIyy5RiEUQLlGLuOjDGsYquR4zIe/gTeIHJwioaEqvYQMpniD/cwYeXlJJ2YhqkJPMR/Px7XP3FwCdUcum/Mjqc4QrLJ2gv0I0YP0=";
        $dat=json_encode($dat);
        $pemfile = "/var/www/newtms/assets/certificates/cert.pem";
        $keyfile = "/var/www/newtms/assets/certificates/key.pem";
        
        $url = "https://uat-api.ssg-wsg.sg/courses/runs/50793/sessions/attendance";
        //$requestXml =  file_get_contents("net.xml");
       
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
        CURLOPT_POSTFIELDS => $dat, 
        CURLOPT_HTTPHEADER => array(
       "Authorization:  ",
       "Cache-Control: no-cache",
       "Content-Type: application/json",
       "x-api-version: v1.3"
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
    
    public function proceed_enrol(){
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'TPG NEW TRAINEE ENROL';
        $data['main_content'] = 'ssgapi_course/proceed_enrol';
        $this->load->view('layout', $data);
    }
    
    public function proceed_enrol_final(){
        $nric = $this->input->post('taxcode');
        $trainee_id = $this->input->post('taxcode_id');
        $tpcode = $this->input->post('tpcode');
        $tpuen = $this->input->post('tpuen');
        $course_ref_no = $this->input->post('course');
        $courserunid = $this->input->post('courserunid');
        $discount_amount = $this->input->post('discount_amount');
        $collection_status = $this->input->post('collection_status');
        $enrolment_date = $this->input->post('enrolment_date');
     
        $traine = $this->classTraineeModel->get_full_trainee_details($trainee_id);
        
        
       

        
       
       $data=array(
            "enrolment"=> array(
              "trainingPartner"=> array(
                "code"=> $tpcode,
                "uen"=> $tpuen
              ),
              "course"=> array(
                "referenceNumber"=> $course_ref_no,
                "run"=> array(
                  "id"=> $courserunid
                )
              ),
              "trainee"=> array(
                "idType"=> array(
                  "type"=> "NRIC"
                ),
                "id"=> $traine['tax_code'],
                "dateOfBirth"=> $traine['dob'],
                "fullName"=> $traine['first_name'].' '.$traine['last_name'],
                "contactNumber"=> array(
                  "countryCode"=> "+65",
                  "areaCode"=> "",
                  "phoneNumber"=> $traine['contact_number']
                ),
                "emailAddress"=> $traine['registered_email_id'],
                "sponsorshipType"=> "EMPLOYER",
                "employer"=> array(
                  "uen"=> "201000372W",
                  "contact"=> array(
                    "fullName"=> $traine['first_name'].' '.$traine['last_name'],
                    "contactNumber"=> array(
                      "countryCode"=> "+65",
                      "areaCode"=> "",
                      "phoneNumber"=> $traine['contact_number']
                    ),
                    "emailAddress"=> $traine['registered_email_id']
                  )
                ),
                "fees"=> array(
                  "discountAmount"=> $discount_amount,
                  "collectionStatus"=>$collection_status
                ),
                "enrolmentDate"=> $enrolment_date
              )
            )
          );
       
       
       
        
        $data=json_encode($data);
        //print_r($data);exit;
        

        
        $url = "https://uat-api.ssg-wsg.sg/tpg/enrolments";
        //$requestXml =  file_get_contents("net.xml");
       
        $api_version = 'v1';
        
        $response = $this->curl_request('POST',$url,$data,$api_version);
        
        
        
        
        
         
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'TPG NEW TRAINEE ENROL';
        $data['main_content'] = 'ssgapi_course/';
        $this->load->view('layout', $data);
    }
}
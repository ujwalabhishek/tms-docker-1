<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * his is the controller class for Accounting Use Cases
 */

class tp_gateway extends CI_Controller {

    private $user;

    /**
     * constructor - loads Model and other objects required in this controller
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('class_trainee_model', 'classTraineeModel');
        $this->load->model('class_Model', 'classModel');
        $this->load->model('course_model', 'coursemodel');
        $this->load->model('company_model', 'companyModel');
        $this->load->model('tpg_api_model', 'tpgModel');
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

    public function get_classroom_location($venue, $other) {
        if ($venue == 'OTH') {
            return 'Others (' . $other . ')';
        } else {
            return $this->coursemodel->get_metadata_on_parameter_id($venue);
        }
    }

    public function curl_request($mode, $url, $encrypted_data, $api_version) {
        //$encrypted_data='ggrR1uwMpea4GWQbhu6+iZ/KZvwhlblrRspkqEg9dVszEjqIiDKnWe4u6PfsD/ntzFfbazfu1I6YmomjmsaCCXPEdJ6sPmrVDyxgVvnScrn6XhZXRQMRpXCSwC5PUh0SXEyr/jw0HtsOFT0JseoJ7nxj8qM/rKv4e9OhNmrIysykBlfEAZ3MsCfnZL9O7kpsVvi2yANJfNoVYBSAs6hUdHc5jlvn2tmLf7kKMNiaP/z+qusGppVZfbvXPq2LNaLl/osEJZDASgGbzJOwLxzDG90E9cyTqhoeREl5KxUud37U41Gx0ufui2bGzA9meFdK6sWefTdIFIfZlh7MK7xKfEyDaTZTyYTObC2p8/PoLq9RAfcRPFCCvOYAFIMB2din+XQ+u+ZqMHzF0cz6A/HPdkSpze2NB96EJwhUXHF5tMMgwq7kKc/ELg6etD8FDrai/klmj7svqsBYfm7fJTwMXDvTWnNWbRhT94JT9RpWGq2V6Gph/16CuAMYt0QZ1mEkzV27m149P5QrPGXvd4CDqSE7lR55Kfs6CujYx4s4PyP7naOEPBUn7DCb6Bv6bJSM6B+K+dAhMArlf1Ov6yKepX0qRzq/XU140sM3xpQs0+/dTLWiiYM5WmIAbj5Ohb0KX9tpccfQ/xo8Xn6sU0mJx5xslh48il1aQOhz/54iAI0+WR8Pf3+x7R/3U6V4tasaWlhPhqdPfzkPbwsSbKK4b/g7UZCU0XgNY0l4ELK+swnh/zv0nzJlHji7a8B0elxAZCRU2EOA+JZDjyEHC1xSNPnss8hNc3c9y3RcmTG6H3EjrPth19e8M3jvSsYGNi0JhoGaojPaXRsCjwI6qHhU2uvn5CmNPvVxxzI5v+0sI46oIoijBfrkZEIFElu6nVwcvFm5b+/nZhM2VuUhO85UIA==';
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
        if ($response === false) {
            print_r(curl_error($curl));
            exit;
        } else {
            //print_r($response);exit;
            return $response;
        }
        curl_close($curl);
    }

    function encrypting($string, $key) {
        $result = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result.=$char;
        }
        return base64_encode($result);
    }

    public function list_search_course() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'Accounting';
        $data['main_content'] = 'ssgapi_course/course';
        $this->load->view('layout', $data);
    }

    public function get_course_list_autocomplete() {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');

        //$encr = base64_encode('c0d3cf1102b248a097846d7232d6ad8f:YTlkNzgyN2YtMjEyNi00ZjU0LWIxMTctMTlhMGMzODY4YWJm');
        $encr = base64_encode('c785f2331e314192a886bafcc8cd99b4:OTc1NTNlYjUtNmM5ZC00ZjNlLTg2ODMtNGExNWFiZGM2ODA3');
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://public-api.ssg-wsg.sg/dp-oauth/oauth/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_POST => 1,
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

        $response_token = json_decode($response_token);



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


        $resp = json_decode($response);
        foreach ($resp->data->courses as $result) {
            $matches[] = array(
                'label' => $result->title,
                'key' => $result->referenceNumber
            );
        }
        echo json_encode($matches);
        exit;
    }

    public function course_details() {
        $query_string = $this->input->get('course_code_id');

        $encr = base64_encode('c785f2331e314192a886bafcc8cd99b4:OTc1NTNlYjUtNmM5ZC00ZjNlLTg2ODMtNGExNWFiZGM2ODA3');
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://public-api.ssg-wsg.sg/dp-oauth/oauth/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_POST => 1,
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

        $response_token = json_decode($response_token);



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


        $data['resp'] = json_decode($response)->data->courses[0];

        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'SSG API COURSE DETAILS';
        $data['main_content'] = 'tp_gateway/view_course';
        $this->load->view('layout', $data);
    }

    public function course_details_by_run_id() {
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
        if ($response === false) {
            print_r(curl_error($curl));
            exit;
        } else {
            print_r(json_decode($response));
            exit;
        }
        curl_close($curl);
    }

    public function course_details_entry() {
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

        $dat = json_encode($dat);
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
        if ($response === false) {
            print_r(curl_error($curl));
            exit;
        } else {
            print_r(json_decode($response));
            exit;
        }
        curl_close($curl);
    }

    public function session_attendace_upload() {
        //$query_string = $this->input->get('course_run_id');
        $dat = "NMKIMzLMLpcTdmLHTX5ShfrnM00GYz2s4cL9QiJ0nGU2H7SbHxhwFPXuVY+hBlJba1EAfZ4MCR4eahxG+mTnfQqTayLkqjbZbcAVXoCA+H6XMBmOvo/sG20qISZPoY3Bag+hQwDLwY7RjjhiRUgKUVoGFHaxg01L/ZdcHiBoTZYKmfnzD5U5aM3TPY9ijLF1GKPWTjjXNnzns2zA6FugU/4LpZKsw7XU2sqHXpcePevLMsWeKPuAy2mtGhju+4tuen6Pk82Ec+MDiBE9RU7ByPL1g8ZrTWn/2lu+Vw4pOC7Pk0gj6sCh5t/JyQJWhOTH2yKM68usr5DzVhCgvno8UtD+AnYJbLrUMTAv4iWjEYp/ZMMJo1XNi/Jsd1CMpu7swQPaUcntTdtW2SUPPrcktItgSe8YXuEMK1sw6frzBQrSKhrj4JSIWzor7rNL+5AHxaBFGvXxIb0dNQpTABQKbmdeRD6DiWN4yRtZulayrNdyE4VvqgF6OvHwucTdbu0D0GdRVKVNfiTSnWlFOoPrjOf16BCX+3CY/u4+R90CmFm2020l+dwXDXgG0oQPQNzD6WWuPjUtMpnWHUP4O7ypKWEHnjCqOxdmDa+K7VQjADFBQCP24TtNBO1gnIVW6iE9bE2oe0MmRarR8C0KpuHGN34NOGFxRE0ouPbxZfRBdx9aXbXc58pbPVxavGAyBXE4MRpYMyqIyy5RiEUQLlGLuOjDGsYquR4zIe/gTeIHJwioaEqvYQMpniD/cwYeXlJJ2YhqkJPMR/Px7XP3FwCdUcum/Mjqc4QrLJ2gv0I0YP0=";
        $dat = json_encode($dat);
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
        if ($response === false) {
            print_r(curl_error($curl));
            exit;
        } else {
            print_r(json_decode($response));
            exit;
        }
        curl_close($curl);
    }

    public function proceed_enrol() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'TPG NEW TRAINEE ENROL';
        $data['main_content'] = 'tp_gateway/proceed_enrol';
        $this->load->view('layout', $data);
    }

    public function proceed_enrol_final() {
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






        $object = array(
            "enrolment" => array(
                "trainingPartner" => array(
                    "code" => $tpcode,
                    "uen" => $tpuen
                ),
                "course" => array(
                    "referenceNumber" => $course_ref_no,
                    "run" => array(
                        "id" => $courserunid
                    )
                ),
                "trainee" => array(
                    "idType" => array(
                        "type" => "NRIC"
                    ),
                    "id" => $traine['tax_code'],
                    "dateOfBirth" => $traine['dob'],
                    "fullName" => $traine['first_name'] . ' ' . $traine['last_name'],
                    "contactNumber" => array(
                        "countryCode" => "+65",
                        "areaCode" => "",
                        "phoneNumber" => $traine['contact_number']
                    ),
                    "emailAddress" => $traine['registered_email_id'],
                    "sponsorshipType" => "EMPLOYER",
                    "employer" => array(
                        "uen" => $tpuen,
                        "contact" => array(
                            "fullName" => $traine['contact_name'],
                            "contactNumber" => array(
                                "countryCode" => "+65",
                                "areaCode" => "",
                                "phoneNumber" => $traine['tenant_contact_num']
                            ),
                            "emailAddress" => $traine['tenant_email_id']
                        )
                    ),
                    "fees" => array(
                        "discountAmount" => $discount_amount,
                        "collectionStatus" => $collection_status
                    ),
                    "enrolmentDate" => $enrolment_date
                )
            )
        );



        $object = json_encode($object);
        //print_r($object);exit;
        $data['trainee'] = $traine;
        $data['tpg_data'] = $object;
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'TPG NEW TRAINEE ENROL';
        $data['main_content'] = 'tp_gateway/proceed_enrol_final';
        $this->load->view('layout', $data);
    }

    public function proceed_enrol_toTpg() {
        $encrypted_data = $this->input->post('tpg_data');

        $api_version = 'v1';
        $url = "https://uat-api.ssg-wsg.sg/tpg/enrolments";
        $response = $this->curl_request('POST', $url, $encrypted_data, $api_version);
        //print_r($response); exit;
        echo " <div id='out'></div>
            
            <script src='https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js'></script>
            <script src='https://code.jquery.com/jquery-3.4.1.min.js' integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=' crossorigin='anonymous'></script>
            <script>
            decrypt();
            function decrypt() {
            var strings = '$response';
				var key = 'DLTmpjTcZcuIJEYixeqYU4BvE+8Sh4jDtDBDT3yA8D0=';
				var cipher = CryptoJS.AES.decrypt(
					strings,
					CryptoJS.enc.Base64.parse(key), {
					  iv: CryptoJS.enc.Utf8.parse('SSGAPIInitVector'),
					  mode: CryptoJS.mode.CBC,
					  keySize: 256 / 32,
					  padding: CryptoJS.pad.Pkcs7
					});
				var decrypted = cipher.toString(CryptoJS.enc.Utf8);
				$('#out').html(decrypted);
			  }</script>";
    }

    ///  //////  Below functions added by shubhranshu for SSG API integration ///////////////////////////////////////////
    /////to fetch the course api required parameters
    public function get_courserun($class_id, $course_id) {
        $tenant_id = $this->tenant_id;
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['tenant'] = $this->classTraineeModel->get_tenant_masters($tenant_id);
        $data['coursedetails'] = $this->coursemodel->get_course_detailse($course_id);
        $data['class'] = $class = $this->classModel->get_class_details_assmnts($tenant_id, $class_id);
        $data['sessions'] = $this->tpgModel->get_all_class_schedule($tenant_id, $class_id);
        $data['ClassTrainer'] = $this->tpgModel->get_trainer_details($class->classroom_trainer);
        $data['ClassLoc'] = $this->get_classroom_location($class->classroom_location, $class->classroom_venue_oth);
        $data['booked_seats'] = $this->classModel->get_class_booked($course_id, $class_id, $tenant_id);
        $data['page_title'] = 'SSG CREATE COURSE RUN';
        $data['main_content'] = 'tp_gateway/get_courserun';
        $this->load->view('layout', $data);
    }

    ///to verify the course api parameters
    function verify_courserun() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('crse_ref_no', 'Course Reference No', 'required|max_length[30]');
        $this->form_validation->set_rules('tp_uen', 'Training Provider UEN', 'required|max_length[30]|alpha_numeric_spaces');
        $this->form_validation->set_rules('modeoftraining', 'Mode Of Training', 'required|max_length[30]|numeric');
        $this->form_validation->set_rules('crs_admin_email', 'Course Admin Email', 'trim|required|valid_email|max_length[30]');
        $this->form_validation->set_rules('reg_open_date', 'Registration Open Date', 'required|max_length[30]|numeric');
        $this->form_validation->set_rules('reg_close_date', 'Registration Close Date', 'required|max_length[30]|numeric');
        $this->form_validation->set_rules('crse_start_date', 'Course Start Date', 'required|max_length[30]|numeric');
        $this->form_validation->set_rules('crse_end_date', 'Course End Date', 'required|max_length[30]|numeric');
        $this->form_validation->set_rules('venue_block', 'Venue Block', 'required|max_length[30]|alpha_numeric_spaces');
        $this->form_validation->set_rules('venue_street', 'Venue Street', 'required|max_length[30]|alpha_numeric_spaces');
        $this->form_validation->set_rules('venue_building', 'Venue Building', 'required|max_length[30]|alpha_numeric_spaces');
        $this->form_validation->set_rules('venue_floor', 'Venue Floor', 'required|max_length[30]|numeric');
        $this->form_validation->set_rules('venue_unit', 'Venue Unit', 'required|max_length[30]|alpha_numeric_spaces');
        $this->form_validation->set_rules('venue_postalcode', 'Venue Postal Code', 'required|max_length[30]|numeric');
        $this->form_validation->set_rules('venue_room', 'Venue Room', 'required|max_length[30]|alpha_numeric_spaces');
        $this->form_validation->set_rules('crse_intake_size', 'Course INtake Size', 'required|max_length[30]|numeric');
        $this->form_validation->set_rules('crse_vacancy_code', 'Course Vacancy Code', 'required|max_length[2]|in_list[A,F,L]|alpha');
        $this->form_validation->set_rules('crse_vacancy_description', 'Course Vacancy Description', 'required|max_length[30]|in_list[Available,Full,Limited Vacancy]');
        $this->form_validation->set_rules('trainer_email[]', 'Trainer Email', 'trim|required|valid_email|max_length[30]');
        $this->form_validation->set_rules('trainer_name[]', 'Trainer Name', 'trim|required|max_length[30]');
        $this->form_validation->set_rules('ttcode[]', 'Trainer Type Code', 'required|max_length[30]|numeric');
        $this->form_validation->set_rules('itpf[]', 'Trainer Description', 'required|max_length[30]|numeric');
        $ttcode = $this->input->post('ttcode');
        foreach ($ttcode as $code) {
            if ($code == 1) {
                $this->form_validation->set_rules('trainer_id[]', 'Trainer ID', 'required|max_length[30]');
            }
        }
        $update = $this->input->get('status');
        if ($this->form_validation->run() == TRUE) {
            //print_r($_POST);exit;
            $data_nextlevel = $_POST;
            // store data to flashdata
            $this->session->set_flashdata('dat', $data_nextlevel);
            if ($update == 'update') {
                redirect('tp_gateway/update_tpg_courserun');
            } else {
                redirect('tp_gateway/crosscheck_tpg_courserun');
            }
        } else {
            $class_id = $this->input->post('class_id');
            $course_id = $this->input->post('course_id');
            redirect('tp_gateway/get_courserun/' . $class_id . '/' . $course_id);
        }
    }

    public function crosscheck_tpg_courserun() {
        $tenant_id = $this->tenant_id;
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['dat'] = $this->session->flashdata('dat');
        $data['sessions'] = $this->tpgModel->get_all_class_schedule($tenant_id, $data['dat'][class_id]);
        $data['page_title'] = 'TPG VERIFY COURSE RUN DETAILS';
        $data['main_content'] = 'tp_gateway/crosscheck_tpg_courserun';
        $this->load->view('layout', $data);
    }

    public function courserun_call_tpg() {
        $crse_ref_no = $this->input->post('crse_ref_no');
        $tp_uen = $this->input->post('tp_uen');
        $modeoftraining = $this->input->post('modeoftraining');
        $crs_admin_email = $this->input->post('crs_admin_email');
        $reg_open_date = $this->input->post('reg_open_date');
        $reg_close_date = $this->input->post('reg_close_date');
        $crse_start_date = $this->input->post('crse_start_date');
        $crse_end_date = $this->input->post('crse_end_date');
        $schedule_info_code = $this->input->post('schedule_info_code');
        $schedule_info_des = $this->input->post('schedule_info_des');
        $schedule_info = $this->input->post('schedule_info');
        $venue_building = $this->input->post('venue_building');
        $venue_block = $this->input->post('venue_block');
        $venue_street = $this->input->post('venue_street');
        $venue_floor = $this->input->post('venue_floor');
        $venue_unit = $this->input->post('venue_unit');
        $venue_postalcode = $this->input->post('venue_postalcode');
        $venue_room = $this->input->post('venue_room');
        $crse_intake_size = $this->input->post('crse_intake_size');
        $crse_vacancy_code = $this->input->post('crse_vacancy_code');
        $crse_vacancy_description = $this->input->post('crse_vacancy_description');
        $trainer_name = $this->input->post('trainer_name');
        $trainer_id = $this->input->post('trainer_id');
        $trainer_email = $this->input->post('trainer_email');
        $course_id = $this->input->post('course_id');
        $class_id = $this->input->post('class_id');
        $tenant_id = $this->tenant_id;
        $booked_seats = $this->classModel->get_class_booked($course_id, $class_id, $tenant_id);
        $sessions = $this->tpgModel->get_all_class_schedule($tenant_id, $class_id);
        foreach ($sessions as $session) {
            if ($session[session_type_id] != 'BRK') {
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

        if (TPG_ENVIRONMENT == 'PRODUCTION') {
            $crse_ref_no = $crse_ref_no;
            $tp_uen = $tp_uen;
        } else {
            $crse_ref_no = 'TGS-2020002096';
            $tp_uen = '201000372W';
        }


        $tpg_course_run_json = '{
                    "course": {
                      "courseReferenceNumber": "' . $crse_ref_no . '",
                      "trainingProvider": {
                        "uen": "' . $tp_uen . '"
                      },
                      "runs": [
                        {
                          "sequenceNumber": 0,
                          "modeOfTraining": "' . $modeoftraining . '",
                          "registrationDates": {
                            "opening": ' . $reg_open_date . ',
                            "closing": ' . $reg_close_date . ' 
                          },
                          "courseDates": {
                            "start": ' . $crse_start_date . ',
                            "end": ' . $crse_end_date . '
                          },
                          "scheduleInfoType": {
                            "code": "' . $schedule_info_code . '",
                            "description": "' . $schedule_info_des . '"
                          },
                          "scheduleInfo": "' . $schedule_info . '",
                          "venue": {
                              "block": "' . $venue_block . '",
                              "street": "' . $venue_street . '",
                              "floor": "' . $venue_floor . '",
                              "unit": "' . $venue_unit . '",
                              "building": "",
                              "postalCode": "' . $venue_postalcode . '",
                              "room": "' . $venue_room . '",
                              "wheelChairAccess": true
                          },
                          "intakeSize": ' . $crse_intake_size . ',
                          "courseAdminEmail": "' . $crs_admin_email . '",
                          "threshold": 0,
                          "registeredUserCount": ' . $booked_seats . ',
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
                          "linkCourseRunTrainer": [
                            {
                              "trainer": {
                                "indexNumber": 0,
                                "id": "' . $tenant_id . '-TMS-' . $trainer_id . '-' . $course_id . '-' . $class_id . '",
                                "name": "' . $trainer_name . '",
                                "inTrainingProviderProfile": true,
                                "domainAreaOfPractice": "Testing Management in Computer Application and Diploma in Computer Application",
                                "experience": "Testing ABC",
                                "linkedInURL": "https://sg.linkedin.com/company/linkedin/abc",
                                "salutationId": 1,
                                "photo": {
                                  "name": "",
                                  "content": ""
                                },
                                "email": "' . $trainer_email . '",
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
        $response = $this->curl_request('POST', $url, $tpg_course_run_json, $api_version);
        //print_r($response);exit;
        $obj = json_decode($response);
        //$obj = json_decode('{ "data": { "runs": [ { "id": 223389 } ] }, "error": {}, "meta": {}, "status": 200 }');
        $this->session->set_flashdata('resp', $obj);
        $this->session->set_flashdata('cid', $class_id);
        if ($obj->status == 200) {
            redirect('tp_gateway/courserun_status');
        } else {
            redirect('tp_gateway/check_status');
        }
    }

    public function courserun_status() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $api_version = 'v1.3';
        $resp = $this->session->flashdata('resp');
        $class_id = $this->session->flashdata('cid');
        $crse_run_id = $resp->data->runs[0]->id;
        $url = "https://uat-api.ssg-wsg.sg/courses/runs/$crse_run_id";
        //$url = "https://uat-api.ssg-wsg.sg/courses/runs/223382";
        $response = json_decode($this->curl_request('GET', $url, '', $api_version));
        $this->session->set_flashdata('success', "Congratulations! You Have Successfully Add Course Run To TPG");
        $status = $this->tpgModel->updateCourseRunId($class_id, $crse_run_id);
        if ($status === FALSE) {
            $this->session->set_flashdata('error', "Oops ! Unable To Update CourseRun ID");
        }
        //print_r($resp);print_r($response);echo $url;exit;
        //print_r($response);exit;
        $data['support'] = $response->data->course->support;
        $data['run'] = $response->data->course->run;
        $data['course_title'] = $response->data->course->title;
        $data['refno'] = $response->data->course->referenceNumber;
        $data['exrefno'] = $response->data->course->externalReferenceNumber;
        $data['page_title'] = 'Course Run Status';
        $data['main_content'] = 'tp_gateway/courserun_status';
        $this->load->view('layout', $data);
    }

    public function check_status() {
        $response = $this->session->flashdata('resp');
        //print_r($response);exit;
        if ($response->status == 400) {
            $this->session->set_flashdata('error', "Oops! Bad request!");
        } elseif ($response->status == 403) {
            $this->session->set_flashdata('error', "Oops! Forbidden. Authorization information is missing or invalid.");
        } elseif ($response->status == 404) {
            $this->session->set_flashdata('error', "Oops! Not Found!");
        } elseif ($response->status == 500) {
            $this->session->set_flashdata('error', "Oops! Internal Error!!");
        } else {
            $this->session->set_flashdata('error', "Oops ! Something Went Wrong Contact System Administrator");
        }

        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['error'] = $response->error->details;
        ////print_r($data);exit;
        $data['page_title'] = 'Course Run Status';
        $data['main_content'] = 'tp_gateway/error_status';
        $this->load->view('layout', $data);
    }

    public function update_courserun($class_id, $course_id, $courserunid) {
        $tenant_id = $this->tenant_id;
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['tenant'] = $this->classTraineeModel->get_tenant_masters($tenant_id);
        $data['coursedetails'] = $this->coursemodel->get_course_detailse($course_id);
        $data['class'] = $class = $this->classModel->get_class_details_assmnts($tenant_id, $class_id);
        $data['sessions'] = $this->tpgModel->get_all_class_schedule($tenant_id, $class_id);
        $data['ClassTrainer'] = $this->tpgModel->get_trainer_details($class->classroom_trainer);
        $data['ClassLoc'] = $this->get_classroom_location($class->classroom_location, $class->classroom_venue_oth);
        $data['booked_seats'] = $this->classModel->get_class_booked($course_id, $class_id, $tenant_id);
        $data['courserun_id'] = $courserunid;
        $data['page_title'] = 'TPG UPDATE COURSE RUN';
        $data['main_content'] = 'tp_gateway/update_courserun';
        $this->load->view('layout', $data);
    }

    public function update_tpg_courserun() {
        $tenant_id = $this->tenant_id;
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['dat'] = $this->session->flashdata('dat');
        $data['sessions'] = $this->tpgModel->get_all_class_schedule($tenant_id, $data['dat'][class_id]);
        $data['page_title'] = 'TPG VERIFY COURSE RUN DETAILS';
        $data['main_content'] = 'tp_gateway/update_tpg_courserun';
        $this->load->view('layout', $data);
    }

    public function update_courserun_call_tpg() {
        $crse_ref_no = $this->input->post('crse_ref_no');
        $tp_uen = $this->input->post('tp_uen');
        $modeoftraining = $this->input->post('modeoftraining');
        $crs_admin_email = $this->input->post('crs_admin_email');
        $reg_open_date = $this->input->post('reg_open_date');
        $reg_close_date = $this->input->post('reg_close_date');
        $crse_start_date = $this->input->post('crse_start_date');
        $crse_end_date = $this->input->post('crse_end_date');
        $schedule_info_code = $this->input->post('schedule_info_code');
        $schedule_info_des = $this->input->post('schedule_info_des');
        $schedule_info = $this->input->post('schedule_info');
        $venue_building = $this->input->post('venue_building');
        $venue_block = $this->input->post('venue_block');
        $venue_street = $this->input->post('venue_street');
        $venue_floor = $this->input->post('venue_floor');
        $venue_unit = $this->input->post('venue_unit');
        $venue_postalcode = $this->input->post('venue_postalcode');
        $venue_room = $this->input->post('venue_room');
        $crse_intake_size = $this->input->post('crse_intake_size');
        $crse_vacancy_code = $this->input->post('crse_vacancy_code');
        $crse_vacancy_description = $this->input->post('crse_vacancy_description');
        $sess_start_time = $this->input->post('sess_start_time');
        $sess_end_time = $this->input->post('sess_end_time');
        $trainer_name = $this->input->post('trainer_name');
        $trainer_id = $this->input->post('trainer_id');
        $trainer_email = $this->input->post('trainer_email');
        $course_id = $this->input->post('course_id');
        $class_id = $this->input->post('class_id');
        $courserun_id = $this->input->post('courserun_id');
        $tenant_id = $this->tenant_id;
        $booked_seats = $this->classModel->get_class_booked($course_id, $class_id, $tenant_id);
        $sessions = $this->tpgModel->get_all_class_schedule($tenant_id, $class_id);
        if (TPG_ENVIRONMENT == 'PRODUCTION') {
            $crse_ref_no = $crse_ref_no;
            $tp_uen = $tp_uen;
        } else {
            $crse_ref_no = 'TGS-2020002096';
            $tp_uen = '201000372W';
        }

        $count = 1;
        foreach ($sessions as $session) {
            if ($session[session_type_id] != 'BRK') {
                $dates = date('Ymd', strtotime($session['class_date']));
                $session_arr[] = array(
                    "action" => "update",
                    "sessionId" => "$crse_ref_no.'-'.$courserun_id.'-S'.$count",
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
            }$count++;
        }



        $tpg_course_run_json = '{
                            "course": {
                              "courseReferenceNumber": "' . $crse_ref_no . '",
                              "trainingProvider": {
                                "uen": "' . $tp_uen . '"
                              },
                              "run": {
                                "action": "update",
                                "sequenceNumber": 0,
                                "registrationDates": {
                                  "opening": ' . $reg_open_date . ',
                                  "closing": ' . $reg_close_date . '
                                },
                                "courseDates": {
                                  "start": ' . $crse_start_date . ',
                                  "end": ' . $crse_end_date . '
                                },
                                "scheduleInfoType": {
                                  "code": "' . $schedule_info_code . '",
                                  "description": "' . $schedule_info_des . '"
                                },
                                "scheduleInfo": "' . $schedule_info . '",
                                "venue": {
                                  "block": "' . $venue_block . '",
                                  "street": "' . $venue_street . '",
                                  "floor": "' . $venue_floor . '",
                                  "unit": "' . $venue_unit . '",
                                  "building": "",
                                  "postalCode": "' . $venue_postalcode . '",
                                  "room": "' . $venue_room . '",
                                  "wheelChairAccess": true
                                },
                                "intakeSize": ' . $crse_intake_size . ',
                                "threshold": 0,
                                "registeredUserCount": ' . $booked_seats . ',
                                "modeOfTraining": "' . $modeoftraining . '",
                                "courseAdminEmail": "' . $crs_admin_email . '",
                                "courseVacancy": {
                                  "code": "' . $crse_vacancy_code . '",
                                  "description": "' . $crse_vacancy_description . '"
                                },
                                "file": {
                                  "Name": "",
                                  "content": ""
                                },
                                "sessions": [
                                  {
                                    "action": "update",
                                    "sessionId": "TGS-2020002096-' . $courserun_id . '-S1",
                                    "startDate": "' . $crse_start_date . '",
                                    "endDate": "' . $crse_end_date . '",
                                    "startTime": "' . $sess_start_time . '",
                                    "endTime": "' . $sess_end_time . '",
                                    "modeOfTraining": "' . $modeoftraining . '",
                                    "venue": {
                                      "block": "' . $venue_block . '",
                                      "street": "' . $venue_street . '",
                                      "floor": "' . $venue_floor . '",
                                      "unit": "' . $venue_unit . '",
                                      "building": "",
                                      "postalCode": "' . $venue_postalcode . '",
                                      "room": "' . $venue_room . '",
                                      "wheelChairAccess": true,
                                      "primaryVenue": false
                                    }
                                  }
                                ],
                                "linkCourseRunTrainer": [
                                  {
                                    "trainer": {
                                      "trainerType": {
                                        "code": "2",
                                        "description": "New"
                                      },
                                      "indexNumber": 0,
                                      "id": "",
                                      "name": "' . $trainer_name . '",
                                      "email": "' . $trainer_email . '",
                                      "inTrainingProviderProfile": true,
                                      "domainAreaOfPractice": "Testing Management in Computer Application and Diploma in Computer Application",
                                      "experience": "Testing ABC",
                                      "linkedInURL": "https://sg.linkedin.com/company/linkedin/abc",
                                      "salutationId": 1,
                                      "photo": {
                                        "name": "",
                                        "content": ""
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
                            }
                          }';


        //print_r($tpg_course_run_json);exit;
        $api_version = 'v1.3';
        $url = "https://uat-api.ssg-wsg.sg/courses/runs/$courserun_id";
        $response = $this->curl_request('POST', $url, $tpg_course_run_json, $api_version);
        //print_r($response);exit;
        $obj = json_decode($response);
        //$obj = json_decode('{ "data": { "runs": [ { "id": 223389 } ] }, "error": {}, "meta": {}, "status": 200 }');
        $this->session->set_flashdata('resp', $obj);
        $this->session->set_flashdata('crid', $courserun_id);
        if ($obj->status == 200) {
            redirect('tp_gateway/update_courserun_status');
        } else {
            redirect('tp_gateway/check_status');
        }
    }

    public function update_courserun_status() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $api_version = 'v1.3';
        $resp = $this->session->flashdata('resp');
        $courserun_id = $this->session->flashdata('crid');
        $url = "https://uat-api.ssg-wsg.sg/courses/runs/$courserun_id";
        //$url = "https://uat-api.ssg-wsg.sg/courses/runs/223382";
        $response = json_decode($this->curl_request('GET', $url, '', $api_version));
        $this->session->set_flashdata('success', "Congratulations! You Have Successfully Updated The Course Run Inside TPG");
        //print_r($resp);print_r($response);echo $url;exit;
        //print_r($response);exit;
        $data['support'] = $response->data->course->support;
        $data['run'] = $response->data->course->run;
        $data['course_title'] = $response->data->course->title;
        $data['page_title'] = 'Course Run Update Status';
        $data['main_content'] = 'tp_gateway/courserun_status';
        $this->load->view('layout', $data);
    }

    public function delete_courserun($class_id, $course_id, $courserunid) {
        
    }

    /**
     * Send enrolment data to TPG
     * 
     *      
     * */
    public function send_trainee_enrolment_data_tpg() {

        //Course 
        $courseRunId = $this->input->post('courseRunId');
        $courseReferenceNumber = $this->input->post('courseReferenceNumber');

        //Trainee
        $userId = $this->input->post('userId');
        $traineeDetails = $this->classTraineeModel->get_full_trainee_details($userId);

        $traineeId = $traineeDetails['tax_code'];

        if ($traineeDetails['tax_code_type'] == 'SNG_1') {
            $traineeIdType = "NRIC";
        } else if ($traineeDetails['tax_code_type'] == 'SNG_2') {
            $traineeIdType = "FIN";
        } else if ($traineeDetails['tax_code_type'] == 'SNG_3') {
            $traineeIdType = "Others";
        } else if ($traineeDetails['tax_code_type'] == 'SNG_4') {
            $traineeIdType = "Others";
        }

        $traineeFullName = $traineeDetails['first_name'];
        $traineeDateOfBirth = $traineeDetails['dob'];
        $traineeEmailAddress = $traineeDetails['registered_email_id'];
        $traineeContactNumber = $traineeDetails['contact_number'];
        $traineeEnrolmentDate = date("Y-m-d");

        $enrolmentMode = $this->input->post('enrolmentMode');
        $tenant_id = $this->tenant_id;
        $companyId = $this->input->post('company_id');
        if ($enrolmentMode == 'COMPSPON') {
            $traineeSponsorshipType = "EMPLOYER";

            $company = $this->companyModel->get_company_details($tenant_id, $companyId);

            //Employer
            $employerUEN = $company[0]->comp_regist_num;
            $emploerFullName = $company[0]->company_name;
            $employerEmailAddress = $company[0]->comp_email;
            $employerContactNumber = $company[0]->comp_phone;
        } else {
            $traineeSponsorshipType = "INDIVIDUAL";

            $tenant_details = fetch_tenant_details($tenant_id);

            //Individual
            $employerUEN = $tenant_details->comp_reg_no;
            $emploerFullName = $tenant_details->tenant_name;
            $employerEmailAddress = $tenant_details->tenant_email_id;
            $employerContactNumber = $tenant_details->tenant_contact_num;
        }

        $feeDiscountAmount = "";
        $paymentStatus = $this->input->post('paymentStatus');
        if ($paymentStatus == 'PAID') {
            $feeCollectionStatus = "Full Payment";
        } else if ($paymentStatus == 'NOTPAID') {
            $feeCollectionStatus = "Pending Payment";
        } else if ($paymentStatus == 'PARTPAID') {
            $feeCollectionStatus = "Partial Payment";
        } else if ($paymentStatus == 'PYNOTREQD') {
            $feeCollectionStatus = "Pending Payment";
        }

        //Training Partner
        $trainingPartnerUEN = $tenant_details->comp_reg_no;
        $trainingPartnerCode = $tenant_details->comp_reg_no . '-01';

        if (TPG_ENVIRONMENT == 'PRODUCTION') {
            $courseReferenceNumber = $courseReferenceNumber;
            $courseRunId = $courseRunId;
            $trainingPartnerUEN = $trainingPartnerUEN;
            $trainingPartnerCode = $trainingPartnerCode;
            $employerUEN = $employerUEN;
        } else {
            $courseReferenceNumber = 'TGS-2020002110';
            $courseRunId = '49842';
            $trainingPartnerUEN = '201000372W';
            $trainingPartnerCode = '201000372W-03';
            $employerUEN = '201000372W';
        }

        $tpg_enrolment_json = array(
            "enrolment" => array(
                "trainingPartner" => array(
                    "code" => $trainingPartnerCode,
                    "uen" => $trainingPartnerUEN
                ),
                "course" => array(
                    "referenceNumber" => $courseReferenceNumber,
                    "run" => array(
                        "id" => $courseRunId
                    )
                ),
                "trainee" => array(
                    "idType" => array(
                        "type" => $traineeIdType
                    ),
                    "id" => $traineeId,
                    "dateOfBirth" => $traineeDateOfBirth,
                    "fullName" => $traineeFullName,
                    "contactNumber" => array(
                        "countryCode" => "+65",
                        "areaCode" => "",
                        "phoneNumber" => $traineeContactNumber
                    ),
                    "emailAddress" => $traineeEmailAddress,
                    "sponsorshipType" => "EMPLOYER",
                    "employer" => array(
                        "uen" => $employerUEN,
                        "contact" => array(
                            "fullName" => $emploerFullName,
                            "contactNumber" => array(
                                "countryCode" => "+65",
                                "areaCode" => "",
                                "phoneNumber" => $employerContactNumber
                            ),
                            "emailAddress" => $employerEmailAddress
                        )
                    ),
                    "fees" => array(
                        "discountAmount" => '2',
                        "collectionStatus" => $feeCollectionStatus
                    ),
                    "enrolmentDate" => $traineeEnrolmentDate
                )
            )
        );

        $tpg_enrolment_json_data = json_encode($tpg_enrolment_json);
        //print_r($object);exit;
        $data['trainingPartnerCode'] = $trainingPartnerCode;
        $data['trainingPartnerUEN'] = $trainingPartnerUEN;

        $data['feeCollectionStatus'] = $feeCollectionStatus;
        $data['traineeEnrolmentDate'] = $traineeEnrolmentDate;

        $data['employerUEN'] = $employerUEN;
        $data['emploerFullName'] = $emploerFullName;
        $data['employerEmailAddress'] = $employerEmailAddress;
        $data['employerContactNumber'] = $employerContactNumber;

        $data['traineeId'] = $traineeId;
        $data['traineeEmailAddress'] = $traineeEmailAddress;
        $data['traineeIdType'] = $traineeIdType;
        $data['traineeDateOfBirth'] = $traineeDateOfBirth;
        $data['traineeFullName'] = $traineeFullName;
        $data['traineeContactNumber'] = $traineeContactNumber;

        $data['courseReferenceNumber'] = $courseReferenceNumber;
        $data['courseRunId'] = $courseRunId;

        $data['$traineeSponsorshipType'] = $traineeSponsorshipType;

        $data['courseId'] = $this->input->post('courseId');
        $data['classId'] = $this->input->post('classId');
        $data['userId'] = $userId;

        $data['tpg_json_data'] = $tpg_enrolment_json_data;
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'TPG NEW TRAINEE ENROL';
        $data['main_content'] = 'classtrainee/enrol_trainee_tpg';
        $this->load->view('layout', $data);
    }

    public function response_trainee_enrolment_data_tpg() {
        $encrypted_data = $this->input->post('tpg_data');
        $course_id = $this->input->post('courseId');
        $class_id = $this->input->post('classId');
        $user_id = $this->input->post('userId');
        $api_version = 'v1';

        $url = "https://" . TPG_DEV_URL . "/tpg/enrolments";
        //$url = "https://uat-api.ssg-wsg.sg/tpg/enrolments";
        $request = $this->curl_request('POST', $url, $encrypted_data, $api_version);

        //$output = false;
        $encrypt_method = "AES-256-CBC";
        $key = base64_decode('DLTmpjTcZcuIJEYixeqYU4BvE+8Sh4jDtDBDT3yA8D0=');  // don't hash to derive the (32 bytes) key
        $iv = 'SSGAPIInitVector';                                              // don't hash to derive the (16 bytes) IV

        $tpg_enrolment_decoded = openssl_decrypt($request, $encrypt_method, $key, 0, $iv); // remove explicit Base64 decoding (alternatively set OPENSSL_RAW_DATA)

        $tpg_response = json_decode($tpg_enrolment_decoded);

        if ($tpg_response->status == 200) {
            $enrolmentReferenceNumber = $tpg_response->data->enrolment[0]->referenceNumber;

            $updated = $this->tpgModel->updateEnrolmentReferenceNumber($course_id, $class_id, $user_id, $enrolmentReferenceNumber);

            if ($updated) {
                $this->session->set_flashdata("success", "Enrolment has been created with reference number - " . $enrolmentReferenceNumber);
            }
            redirect('class_trainee?course_id=' . $course_id . '&class=' . $class_id);
        } else {
            if ($tpg_response->status == 400) {
                $this->session->set_flashdata('error', $tpg_response->error->details[0]->message);
            } elseif ($tpg_response->status == 403) {
                $this->session->set_flashdata('error', $tpg_response->error->details[0]->message);
            } elseif ($tpg_response->status == 404) {
                $this->session->set_flashdata('error', $tpg_response->error->details[0]->message);
            } elseif ($tpg_response->status == 500) {
                $this->session->set_flashdata('error', $tpg_response->error->details[0]->message);
            } else {
                $this->session->set_flashdata('error', $tpg_response->error->details[0]->message);
            }
            redirect('class_trainee?course_id=' . $course_id . '&class=' . $class_id);
        }
    }

    public function view_enrolment_tpg($enrolmentReferenceNumber) {

        $encrypt_method = "AES-256-CBC";
        $key = base64_decode('DLTmpjTcZcuIJEYixeqYU4BvE+8Sh4jDtDBDT3yA8D0=');  // don't hash to derive the (32 bytes) key
        $iv = 'SSGAPIInitVector';                                          // don't hash to derive the (16 bytes) IV        

        $api_version = 'v1';
        $url = "https://" . TPG_DEV_URL . "/tpg/enrolments/details/" . $enrolmentReferenceNumber;

        $request = $this->curl_request('GET', $url, "", $api_version);

        $output = openssl_decrypt($request, $encrypt_method, $key, 0, $iv); // remove explicit Base64 decoding (alternatively set OPENSSL_RAW_DATA)

        $tpg_response = json_decode($output);

        if ($tpg_response->status == 200) {
            $data['enrolmentReferenceNumber'] = $enrolmentReferenceNumber;

            //echo "aaa" . print_r($tpg_response);
            //exit;

            $data['referenceNumber'] = $tpg_response->data->enrolment->referenceNumber;
            $data['status'] = $tpg_response->data->enrolment->status;

            $data['trainingPartnerCode'] = $tpg_response->data->enrolment->trainingPartner->code;
            $data['trainingPartnerUEN'] = $tpg_response->data->enrolment->trainingPartner->uen;
            $data['trainingPartnerName'] = $tpg_response->data->enrolment->trainingPartner->name;

            $data['courseReferenceNumber'] = $tpg_response->data->enrolment->course->referenceNumber;
            $data['courseTitle'] = $tpg_response->data->enrolment->course->title;
            $data['courseRunId'] = $tpg_response->data->enrolment->course->run->id;
            $data['courseStartDate'] = $tpg_response->data->enrolment->course->run->startDate;
            $data['courseEndDate'] = $tpg_response->data->enrolment->course->run->endDate;

            $data['traineeId'] = $tpg_response->data->enrolment->trainee->id;
            $data['traineeEmailAddress'] = $tpg_response->data->enrolment->trainee->email->full;
            $data['traineeIdType'] = $tpg_response->data->enrolment->trainee->idType->type;
            $data['traineeDateOfBirth'] = $tpg_response->data->enrolment->trainee->dateOfBirth;
            $data['traineeFullName'] = $tpg_response->data->enrolment->trainee->fullName;
            $data['traineeContactNumber'] = $tpg_response->data->enrolment->trainee->contactNumber->phoneNumber;

            $data['employerUEN'] = $tpg_response->data->enrolment->trainee->employer->uen;
            $data['employerName'] = $tpg_response->data->enrolment->trainee->employer->name;
            $data['employerContactFullName'] = $tpg_response->data->enrolment->trainee->employer->contact->fullName;
            $data['employerContactNumber'] = $tpg_response->data->enrolment->trainee->employer->contact->contactNumber->phoneNumber;
            $data['employerEmailAddress'] = $tpg_response->data->enrolment->trainee->employer->contact->email->full;

            $data['feeDiscountAmount'] = $tpg_response->data->enrolment->trainee->fees->discountAmount;
            $data['feeCollectionStatus'] = $tpg_response->data->enrolment->trainee->fees->collectionStatus;

            $data['traineeEnrolmentDate'] = $tpg_response->data->enrolment->trainee->enrolmentDate;

            $data['traineeSponsorshipType'] = $tpg_response->data->enrolment->trainee->sponsorshipType;

            $data['sideMenuData'] = fetch_non_main_page_content();
            $data['page_title'] = 'TPG VIEW ENROL DATA';
            $data['main_content'] = 'classtrainee/view_enrolled_trainee_tpg';

            $this->load->view('layout', $data);
        } else {
            if ($tpg_response->status == 400) {
                $this->session->set_flashdata('error', $tpg_response->error->details[0]->message);
            } elseif ($tpg_response->status == 403) {
                $this->session->set_flashdata('error', $tpg_response->error->details[0]->message);
            } elseif ($tpg_response->status == 404) {
                $this->session->set_flashdata('error', $tpg_response->error->details[0]->message);
            } elseif ($tpg_response->status == 500) {
                $this->session->set_flashdata('error', $tpg_response->error->details[0]->message);
            } else {
                $this->session->set_flashdata('error', $tpg_response->error->details[0]->message);
            }
            redirect('class_trainee');
        }
    }

    public function update_fee_collection_tpg() {
        
        $enrolmentReferenceNumber = $this->input->post('tpgEnrolmentReferenceNumber');
        $course_id = $this->input->post('tpgCourseId');
        $class_id = $this->input->post('tpgClassId');
        $feeCollectionStatus = $this->input->post('FEESTATUS');
        
        $encrypt_method = "AES-256-CBC";
        $key = base64_decode('DLTmpjTcZcuIJEYixeqYU4BvE+8Sh4jDtDBDT3yA8D0=');  // don't hash to derive the (32 bytes) key
        $iv = 'SSGAPIInitVector';                                          // don't hash to derive the (16 bytes) IV        

        $api_version = 'v1';
        $url = "https://" . TPG_DEV_URL . "/tpg/enrolments/feeCollections/" . $enrolmentReferenceNumber;


        $tpg_enrolment_json_data = '{
                                    "enrolment": {
                                      "fees": {
                                        "collectionStatus": "'.$feeCollectionStatus.'"
                                      }
                                    }
                                  }';
        
        $encrypted_output = openssl_encrypt($tpg_enrolment_json_data, $encrypt_method, $key, 0, $iv); // remove explicit Base64 encoding (alternatively set OPENSSL_RAW_DATA)

        $request = $this->curl_request('POST', $url, $encrypted_output, $api_version);

        $decrypted_output = openssl_decrypt($request, $encrypt_method, $key, 0, $iv); // remove explicit Base64 decoding (alternatively set OPENSSL_RAW_DATA)

        $tpg_response = json_decode($decrypted_output);
        
        print_r($tpg_response); exit;
        
        redirect('class_trainee?course_id=' . $course_id . '&class=' . $class_id);
        
    }

}

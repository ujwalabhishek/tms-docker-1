<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Email
| -------------------------------------------------------------------------
| This file lets you define parameters for sending emails.
| Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/libraries/email.html
|*/

//$config['protocol'] = 'smtp';
//$config['smtp_host'] = 'server.biipmi.net';
//$config['smtp_port'] = '25';
//$config['smtp_user'] = 'enquiries@biipmi.co';
//$config['smtp_pass'] = 'biipmisg2014';


//$config['smtp_host'] = 'mail.bid4jeet.in';
//$config['smtp_port'] = '587';
//$config['smtp_user'] = 'test@bid4jeet.in';
//$config['smtp_pass'] = 'dipu1234@@';

$config['protocol'] = 'smtp';
//$config['smtp_host'] = 'server.biipmi.net';
//$config['smtp_port'] = '25';
//$config['smtp_user'] = 'enquiries@biipmi.co';
//$config['smtp_pass'] = 'biipmisg2014';


$config['smtp_host'] = 'ssl://smtp.gmail.com';
$config['smtp_port'] = '465';
$config['smtp_user'] = 'biipmisg@gmail.com';
$config['smtp_pass'] = 'biipmiSG2015';

$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['newline'] = "\r\n";
$config['smtp_timeout'] = 30;


//$this->email->initialize($config);
/* End of file email.php */
/* Location: ./application/config/email.php */
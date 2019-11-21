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

$config['protocol'] = 'smtp';
//$config['smtp_host'] = 'server.biipmi.net';
//$config['smtp_port'] = '25';
//$config['smtp_user'] = 'enquiries@biipmi.co';
//$config['smtp_pass'] = 'biipmisg2014';


$config['smtp_host'] = 'ssl://smtp.gmail.com';
//$config['smtp_port'] = '465';
$config['smtp_user'] = 'biipmisg@gmail.com';
$config['smtp_pass'] = 'biipmiSG2015';

//$this->email->initialize($config);
/* End of file email.php */
/* Location: ./application/config/email.php */
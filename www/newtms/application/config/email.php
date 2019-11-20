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
$config['smtp_host'] = 'mail.biipmi.co';
$config['smtp_port'] = '25';
$config['smtp_user'] = 'enquiries@biipmi.co';
$config['smtp_pass'] = 'biipmisg2014';
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['newline'] = "\r\n";
$config['smtp_timeout'] = 30;

/* End of file email.php */
/* Location: ./application/config/email.php */
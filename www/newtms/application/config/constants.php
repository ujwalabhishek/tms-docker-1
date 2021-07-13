<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/

defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');
define('PAY_D_ENROL','PDENROL');
define('PAY_A_ENROL', 'PAENROL');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

////added by shubhranshu ////////////////
define('RECORDS_PER_PAGE', 50);

define('ACTIVE', 'ACTIVE'); //Added for DMS
define('ARCHIVED', 'INACTIVE'); //Added for DMS

define('FROM_EMAIL_ID', 'biipmisg2020@gmail.com');
define('INBOX_MAIL_NAME', 'TMS'); 
define('NOTIFICATION_MAIL_SUBJECT', 'Notification from TMS Administrator');

define('MAIL_FOOTER', 
        'Thanks and Regards,<br/>
        Your Administrator @ <Tenant_Company_Name><br/><br/><br/>
        Disclaimer: This is an auto-generated mail, please do not reply back. In case of any discrepancy please contact ADMIN TEAM
        (<Tenant_Company_Email>) if you are not the intended recipient. You are notified that disclosing, 
        copying, distributing or taking any action in reliance on the contents of this information is strictly prohibited. ');

define('FORGOT_PWD_MAIL_FOOTER', 
        'Thanks and Regards,<br/>
        Your TMS Administrator<br/><br/><br/>
        Disclaimer: This is an auto-generated mail, please do not reply back. In case of any discrepancy please contact ADMIN TEAM
        (admin@biipmi.com) if you are not the intended recipient. You are notified that disclosing, 
        copying, distributing or taking any action in reliance on the contents of this information is strictly prohibited. ');
define('NOTIFICATION_MAIL_HEADER', 'Dear TMS User, ');

//for nric
define('NRIC_OTHERS', "SNG_3");
define('NRIC', "SNG_1");
define('NRIC_FIN', "SNG_2");
define('PAX_PER_SHEET', "25");
define('FIN_ARRAY', array("F","G"));
define('NRIC_ARRAY', array("S","T"));

define('CONST_DATE','2016-03-29');
//multiple role based login
define('ROLE_PRIORITY_ARRAY', 'ADMN,CRSEMGR,TRAINER,SLEXEC');


/*
 * Date time formats
 */

define('CLIENT_DATE_FORMAT','d/m/Y');
define('SERVER_DATE_TIME_FORMAT','Y-m-d H:i:s');

//define('APP_ADMN_LOGIN_LINK', "https://tms.xxxx.com/tmsadmin"); 
define('ENROL_PYMNT_DUE', "enrol_pymnt_due"); 
//define('TENANT_ID', $this->session->userdata('master_tenant_id')); 
define('METADATA', "metadata_id"); 
define('RESETPWD', "1234"); 
define('RECORDS_IN_MAIN_PAGE',10);

/* End of file constants.php */
/* Location: ./application/config/constants.php */

/* For DMS */
define('ASSMNT_TEMAPLTE_ID', "assmnt_template_id");
//paypal
define('SANDBOX', 0);
define('ONLINE', 'ONLINE');//11/12/14-MODE OF PAYMENT
define('ENROL_PYMNT_DUE', "enrol_pymnt_due"); 

////added by shubhranshu for google captcha////////////////
define('GOOGLE_CAPTCHA_SITEKEY', "6Le7puAUAAAAAPUPDMn2d98Zf6ABHAB1fen1Te-R"); 
define('GOOGLE_CAPTCHA_SECRETKEY', "6Le7puAUAAAAAISg1yqxoXAur7FxGj5u9a-OSd66"); 
/////added by shubhranshu for NSA courses mail send to anther mail id for FRCS
define('FRCSMAILID', "nsadivya@mailinator.com"); 
////added by shubhranshu for TPG Gateway Environment variable
//define('TPG_ENVIRONMENT', "PRODUCTION"); 
define('TPG_ENVIRONMENT', "DEVELOPEMENT");
define('TPG_LIVE_URL', "api.ssg-wsg.sg");
define('TPG_DEV_URL', "uat-api.ssg-wsg.sg");
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = "course_public";
$route['404_override'] = 'login/_404';
$route['activity_log/:num'] = "activity_log";
$route['internal_user/:num'] = "internal_user";
$route['course/:num'] = "course";
$route['classes/:num'] = "classes";
$route['class_trainee/:num'] = "class_trainee";
$route['reports/:num'] = "reports";
$route['company/:num'] = "company";
$route['trainee/:num'] = "trainee";
$route['settings_public/:num'] = "settings_public";
$route['manage_tenant/:num'] = "manage_tenant"; 
$route['manage_block_nric/:num'] = "manage_block_nric"; 
$route['manage_subsidy/:num'] = "manage_subsidy"; 
$route['course_public/:num'] = "course_public";
$route['translate_uri_dashes'] = FALSE;
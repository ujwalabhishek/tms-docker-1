<?php

function get_data_for_renderring_attendance($tenant_id, $course_id, $class_id, $subsidy, $from_date, $to_date, $week_start_date, $week, $sort_by, $sort_order, $attendance_status) {

    $CI = & get_instance();

    $CI->load->model('Meta_Values', 'meta');

    $CI->load->model('Course_Model', 'course');

    $CI->load->model('Class_Model', 'class');

    $CI->load->helper('common');

    

    $data['courses'] = $CI->course->get_class_courses_all($tenant_id);

    $data['subsidy'] = $subsidy;

    if (!empty($course_id)) {

        $data['classes'] = $CI->class->get_course_class($tenant_id, $course_id, 'mark_attendance',1,'classTrainee');

        if (!empty($class_id)) {

            $class_details = $CI->class->get_class_by_id($tenant_id, $course_id, $class_id);

        }

    }

    if (!$data['courses'])

        $data['courses'] = array('0' => 'Please Select');

    else {

        $crse = $data['courses'];

        $data['courses'] = array(0 => 'Please Select');



        foreach ($crse as $key => $val) {

            $data['courses'][$key] = $val;

        }

    }

    if (!$data['classes'])

        $data['classes'] = array('0' => 'Please Select');

    else {

        $class = $data['classes'];

        $data['classes'] = array(0 => 'Please Select');

        foreach ($class as $key => $val) {

            $data['classes'][$key] = $val;

        }

    }

    $class_start = parse_date($class_details->class_start_datetime, SERVER_DATE_TIME_FORMAT);

    $class_end = parse_date($class_details->class_end_datetime, SERVER_DATE_TIME_FORMAT);

    $data['class_start'] = date_format_singapore($class_details->class_start_datetime);

    $data['class_end'] = date_format_singapore($class_details->class_end_datetime);

    if (empty($from_date) || $from_date < $class_start) {

        $from_date = $class_start;

    }

    if (empty($to_date) || $to_date > $class_end) {

        $to_date = $class_end;

    }

    if (empty($to_date))

        $to_date = new DateTime();

    if (empty($from_date))

        $from_date = clone $to_date;

   
//echo $from_date.'--------'.$to_date;exit;
   
    list($week_start_date, $week_end_date) = calculate_start_end_date_range($from_date, $to_date, $class_start, $class_end, $week_start_date, $week);

 //previus commneted by sushil

//    if ($week_start_date > $week_end_date) {

//        $week_start_date = $class_start;

//        $sunday_check = date_format($week_start_date, 'l');

//        if ($sunday_check == 'Sunday') {

//            $week_end_date = $week_start_date;

//        } else {

//            $HOUR = '00';

//            $MINUTES = '00';

//            $SECONDS = '00';

//            $MONTH = date_format($week_start_date, 'n');

//            $DATE = date_format($week_start_date, 'j');

//            $YEAR = date_format($week_start_date, 'Y');

//            for ($i = 1; $i <= 6; $i++) {

//                $next_date = date('l', mktime($HOUR, $MINUTES, $SECONDS, $MONTH, $DATE + $i, $YEAR));

//                if ($next_date == 'Sunday') {

//                    $end_date = $next_date = date('Y-m-d', mktime($HOUR, $MINUTES, $SECONDS, $MONTH, $DATE + $i, $YEAR));

//                }

//            }

//            $week_end_date = DateTime::createFromFormat('U', strtotime($end_date));

//        }

//    }

    

    

     // skm code start from here

    if ($week_start_date == $week_end_date or $week_start_date < $week_end_date)

    {

         if (empty($week_start_date))

        $week_start_date = new DateTime();



        if (empty($week_end_date))

        $week_end_date = clone $week_start_date;

    }

    // skm code end here

    if (empty($week_start_date))

        $week_start_date = new DateTime();

    if (empty($week_end_date))

        $week_end_date = clone $week_start_date;

    $data['tabledata'] = $tabledata = $CI->classtraineemodel->get_class_trainee_list_for_attendance($tenant_id, $course_id, $class_id, $subsidy, $week_start_date, $week_end_date, $sort_by, $sort_order, $attendance_status);


    if ($attendance_status == 'ab' || $attendance_status == 'pr') {



        $date1 = $week_start_date->format('Y-m-d');
        $date2 = $week_end_date->format('Y-m-d');
       

        if (strtotime($date2) > strtotime(date('Y-m-d'))) {

                $date2 = date('Y-m-d'); 

        }

        if (strtotime($date2) < strtotime($date1)) {

            $user_present = array();

            $data['tabledata'] = array();

        } else {

            $second_diff = strtotime($date2) - strtotime($date1);

            $interval = floor($second_diff / 3600 / 24);

            $user_present = array();

            foreach ($tabledata as $row)

            {



              //  if ($row['user_count'] == ($interval + 1)) {

                    $user_present[] = $row['user_id'];

            }

        

           // }

            $data['tabledata'] = $CI->classtraineemodel->present_absent_attendance_list($tenant_id, $course_id, $class_id, $subsidy, $week_start_date, $week_end_date, $sort_by, $sort_order, $attendance_status, $user_present);


        }

    }

    $data['week_start'] = $week_start_date->format(CLIENT_DATE_FORMAT);

    $data['week_end'] = $week_end_date->format(CLIENT_DATE_FORMAT);


    $data['class_id'] = $class_id;

    $data['course_id'] = $course_id;

    $data['sort_order'] = $sort_order;

    $data['class_session_day'] = $class_details->class_session_day;

    $data['class_start_date'] = date('Y-m-d',strtotime($class_details->class_start_datetime));

    $data['class_end_date'] = date('Y-m-d',strtotime($class_details->class_end_datetime));

//print_r($data);exit;

    return $data;

}

function calculate_start_end_date_range_for_month(DateTime $from_date) {

    $start = strtotime('first day of this month 12:00:00', $from_date->getTimestamp());

    $first_monday = strtotime('first monday of this month 12:00:00', $start);

    if ($first_monday > $start) {

        $start = strtotime('last Monday 12:00:00', $start);

    } else {

        $start = $first_monday;

    }

    $end = strtotime('last day of this month 12:00:00', $from_date->getTimestamp());

    $last_sunday = strtotime('last sunday of this month 12:00:00', $end);

    if ($end > $last_sunday) {

        $end = strtotime('next Sunday 12:00:00', $end);

    } else {

        $end = $last_sunday;

    }

    return array(DateTime::createFromFormat('U', $start), DateTime::createFromFormat('U', $end));

}

function calculate_start_end_date_range(DateTime $from_date, $to_date, $class_start, $class_end, $week_start_date = null, $week = null) {
//echo date('D',$week_start_date->getTimestamp());print_r($week_start_date);exit;
     // print_r($week_start_date);exit;
    if (empty($week_start_date) || (strtotime($class_start->format('Y-m-d')) == strtotime($class_end->format('Y-m-d'))) ) {        

        $week_start_time = strtotime('Monday this week 12:00:00', $from_date->getTimestamp());

    } else {

        if(date('D',$week_start_date->getTimestamp())=='Sun'){

            $week_start_time = strtotime('Monday previous week 12:00:00', $week_start_date->getTimestamp());
            $week_start_time = $week_start_time + 604800;
            
        }else if(strtotime('Monday this week 12:00:00', $week_start_date->getTimestamp()) == strtotime('Monday this week 12:00:00') && strtotime($class_end->format('Y-m-d')) < strtotime($week_start_date->format('Y-m-d'))) {


            $week_start_time = strtotime('Monday this week 12:00:00', $class_start->getTimestamp());
            $week_start_time = $week_start_time + 604800;

        }else{

            $week_start_time = strtotime('Monday this week 12:00:00', $week_start_date->getTimestamp());

        }

    }
    //echo date_default_timezone_get();
    //echo 1571025600-$week_start_time.'-------';
    //$week_start_time = $week_start_time + 604800;
//print_r($week_start_time);exit;
    if ($week == 1) {

        $week_start_time = strtotime("-7 days", $week_start_time);

    } else if ($week == 2) {

        $week_start_time = strtotime("+7 days", $week_start_time);

    }

    $start_datetime = DateTime::createFromFormat('U', $week_start_time);

    if ($start_datetime < $class_start) {

        $start_datetime = $class_start;

    }

    $first_sunday = strtotime("next Sunday 12:00:00", $week_start_time);

    $first_sunday_datetime = DateTime::createFromFormat('U', $first_sunday);

    if (!empty($to_date) && $to_date < $first_sunday_datetime && $to_date > $start_datetime) {

        $first_sunday_datetime = $to_date;

    }

    if ($first_sunday_datetime > $class_end) {

        $first_sunday_datetime = $class_end;

    }    
//echo print_r($start_datetime);print_r($first_sunday_datetime);exit;////
    return array($start_datetime, $first_sunday_datetime);

}


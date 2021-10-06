<style>#select_course_id{width:500px;}#select_class_id{width:200px;}
    .overlay {
        background: #959191;
        display: none;
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        height: 900px;
        left: 0;
        opacity: 0.8;
        z-index: 9999999999;
    }
    #loading-img {
        background: url(assets/images/loading_1.gif) center center no-repeat;
        height: 100%;
        background-size:123px;

    }
</style> 
<?php
$this->load->helper('common_helper');

$ci = & get_instance();
$ci->load->model('class_model');
$post_course_id = $this->input->post('course_id');
$post_class_id = $this->input->post('class_id');

$tenant_id = $this->session->userdata('userDetails')->tenant_id;

$value_of_schedule_class = count($class_schedule);
$start_class = date_create_from_format("d/m/Y", $class_start);
$end_class = date_create_from_format("d/m/Y", $class_end);

if ($value_of_schedule_class <= 0) {
    echo "bbbb";
    $attendance_status = $this->input->post('attendance_status');
    $start_class = date_create_from_format("d/m/Y", $class_start);
    $end_class = date_create_from_format("d/m/Y", $class_end);
    $from_date_post_value = set_value('from_date');
    $from_date = empty($from_date_post_value) ? $class_start : $from_date_post_value;
    $to_date_post_value = set_value('to_date');
    $to_date = empty($to_date_post_value) ? $class_start : $to_date_post_value;

    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
    ?>
    <script type="text/javascript">
        var CLIENT_DATE_FORMAT = 'dd/mm/yy';
        var SITE_URL = '<?php echo site_url(); ?>';
        var class_start = '<?php echo $class_start; ?>';
        var class_end = '<?php echo $class_end; ?>';
        var pageurl = '<?php echo $controllerurl; ?>';
        var ancher = '<?php echo $ancher; ?>';
    </script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/mark_attendance.js"></script>
    <div class="overlay">
        <div id="loading-img"></div>
    </div>
    <div class="col-md-10">

        <h2 class="panel_heading_style"><span class="glyphicon glyphicon-pencil"></span> <?php echo $page_title; ?></h2>
        <?php
        if (!empty($message)) {
            echo "<div class='success'>" . $message . "</div>";
        }
        ?>
        <div class="table-responsive">
            <span class="error" id="markAttendanceSubmitErrorForm"></span>
            <?php
            $atr = 'id="search_form" name="search_form"';
            echo form_open($controllerurl, $atr);
            echo form_hidden('orientation', 'P', 'orientation');
            ?>
            <table class="table table-striped">

                <tbody>
                    <tr>
                        <td class="td_heading">Course Name:<span class="required">*</span></td>
                        <td>
                            <?php echo form_dropdown("course_id", $courses, set_value('course_id'), 'id="select_course_id"') ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Class Name:<span class="required">*</span></td>

                        <td colspan='3'>
                            <?php
                            $attr_js = 'id="select_class_id"';
                            echo form_dropdown('class_id', $classes, set_value('class_id'), $attr_js);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Subsidy</td>
                        <td <?php echo ($is_report_page) ? '' : "colspan='2'"; ?>>                        
                            <?php
                            $subsidy_array = array('' => 'All', 'ws' => 'With Subsidy', 'wts' => 'Without Subsidy', 'fr' => 'Foreginer');
                            $attr_js = 'id="subsidy">';
                            echo form_dropdown('subsidy', $subsidy_array, $subsidy, $attr_js);
                            ?>
                        </td>                        
                        <td>
                            <?php
                            $attr_js = 'id="input_to_date" style="width:40%;"';
                            ?>

                            <div class="pull-right">
                                <?php
                                echo form_hidden('week_start', $week_start);
                                echo form_hidden('week_end', $week_end);
                                echo form_hidden('week', null);
                                echo form_hidden('export', null);
                                echo form_hidden('export1', null);
                                ?>
                                <button id="markAttendanceSubmit" class="btn btn-xs btn-primary no-mar" name="markattendance_search_button" title="Search" value="Search"><span class="glyphicon glyphicon-search"></span> Search</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php echo form_close(); ?>
        </div>
        <br>
        <table class="table table-striped">
            <tr class="danger">
                <td colspan="10" style="color:red;text-align: center;">There is no assessment schedule.</td>
            </tr>
        </table>
    </div>
    <?php
} else if ($start_class != $end_class) {  // else condition on line no : 526
    echo "bbbb";
    $attendance_status = $this->input->post('attendance_status');
    $start_class = date_create_from_format("d/m/Y", $class_start);
    $end_class = date_create_from_format("d/m/Y", $class_end);
    $from_date_post_value = set_value('from_date');
    $from_date = empty($from_date_post_value) ? $class_start : $from_date_post_value;
    $to_date_post_value = set_value('to_date');
    $to_date = empty($to_date_post_value) ? $class_start : $to_date_post_value;

    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');

    function check_for_session_checked($session_data, $date, $is_first) {
        $format_date = $date->format('Y-m-d');
        if (isset($session_data[$format_date])) {
            $session_by_date_data = $session_data[$format_date];
            if ($is_first) {
                return $session_by_date_data['session_01'] == '1';
            } else {
                return $session_by_date_data['session_02'] == '1';
            }
        }
        return false;
    }

    function check_class_date_range(DateTime $start_class, DateTime $end_class, DateTime $curr_date) {
        return $curr_date <= $end_class && $curr_date >= $start_class && $curr_date <= new DateTime();
    }
    ?>
    <script type="text/javascript">
        var CLIENT_DATE_FORMAT = 'dd/mm/yy';
        var SITE_URL = '<?php echo site_url(); ?>';
        var class_start = '<?php echo $class_start; ?>';
        var class_end = '<?php echo $class_end; ?>';
        var pageurl = '<?php echo $controllerurl; ?>';
        var ancher = '<?php echo $ancher; ?>';
    </script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/mark_attendance.js"></script>
    <div class="overlay">
        <div id="loading-img"></div>
    </div>
    <div class="col-md-10">

        <h2 class="panel_heading_style"><span class="glyphicon glyphicon-pencil"></span> <?php echo $page_title; ?></h2>
        <?php
        if (!empty($message)) {
            echo "<div class='success'>" . $message . "</div>";
        }
        ?>
        <div class="table-responsive">
            <span class="error" id="markAttendanceSubmitErrorForm"></span>
            <?php
            $atr = 'id="search_form" name="search_form"';
            echo form_open($controllerurl, $atr);
            echo form_hidden('orientation', 'P', 'orientation');
            ?>
            <table class="table table-striped">

                <tbody>
                    <tr>
                        <td class="td_heading">Course Name:<span class="required">*</span></td>
                        <td>
                            <?php echo form_dropdown("course_id", $courses, set_value('course_id'), 'id="select_course_id"') ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Class Name:<span class="required">*</span></td>

                        <td colspan='3'>
                            <?php
                            $attr_js = 'id="select_class_id"';
                            echo form_dropdown('class_id', $classes, set_value('class_id'), $attr_js);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Subsidy</td>
                        <td <?php echo ($is_report_page) ? '' : "colspan='2'"; ?>>                        
                            <?php
                            $subsidy_array = array('' => 'All', 'ws' => 'With Subsidy', 'wts' => 'Without Subsidy', 'fr' => 'Foreginer');
                            $attr_js = 'id="subsidy">';
                            echo form_dropdown('subsidy', $subsidy_array, $subsidy, $attr_js);
                            ?>
                        </td>                        
                        <td>
                            <?php
                            $attr_js = 'id="input_to_date" style="width:40%;"';
                            ?>

                            <div class="pull-right">
                                <?php
                                echo form_hidden('week_start', $week_start);
                                echo form_hidden('week_end', $week_end);
                                echo form_hidden('week', null);
                                echo form_hidden('export', null);
                                echo form_hidden('export1', null);
                                ?>
                                <button id="markAttendanceSubmit" class="btn btn-xs btn-primary no-mar" name="markattendance_search_button" title="Search" value="Search"><span class="glyphicon glyphicon-search"></span> Search</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php echo form_close(); ?>
        </div>
        <br>
        <?php
        $check_classid = $this->input->post('class_id');
        if (empty($tabledata) && !empty($check_classid) && empty($attendance_status)) {
            ?>
            <table class="table table-striped">
                <tr class="danger">
                    <td colspan="10" style="color:red;text-align: center;">There are no students enrolled for the class.</td>
                </tr>
            </table>
            <?php
        }

        //Restrict the class schedule
        $data_arr = array();
        $datas_arr_list = array();
        foreach ($class_schedule as $row) {
            $data_arr[$row['assmnt_date']][] = array(
                'assmnt_date' => $row['assmnt_date'],
                'start' => $row['assmnt_start_time'],
                'end' => $row['assmnt_end_time']
            );
            $datas_arr_list[] = $row['assmnt_date'];
        }

        if (!empty($tabledata) || !empty($attendance_status)) {
            $week_start_date = date_create_from_format("d/m/Y", $week_start);
            $week_end_date = date_create_from_format("d/m/Y", $week_end);

            usort($datas_arr_list, function($a, $b) {
                $dateTimestamp1 = strtotime($a);
                $dateTimestamp2 = strtotime($b);

                return $dateTimestamp1 < $dateTimestamp2 ? -1 : 1;
            });

            $ifPrevWeekDisabled = $week_start_date->format("Y-m-d") <= $datas_arr_list[0];
            $ifNextWeekDisabled = $week_end_date->format("Y-m-d") >= end($datas_arr_list);

            //$ifPrevWeekDisabled = $week_start_date <= $start_class;
            //$ifNextWeekDisabled = $week_end_date >= $end_class;

            $same_values = $start_class == $end_class;
            if ($same_values == 1) {
                $ifPrevWeekDisabled = $same_values;
                $ifNextWeekDisabled = $same_values;
            }
            ?>                
            <div class="row_dim1">            
                <div class="add_button space_style">
                    <?php
                    $current_date_time = strtotime(date("Y-m-d H:i:s"));
                    ?>
                </div>
                <div style="clear:both;"></div>
                <?php
                if (strtotime($class_start_date) <= strtotime(date('Y-m-d')) || $is_report_page) {
                    ?>
                    <div class="scroll">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="th_header sort_header" sort="tu.tax_code" width="5%"><span style="color:#000000;">NRIC / FIN No.</span></th>
                                    <th class="th_header sort_header" sort="name" width="20%"><span style="color:#000000;">Trainee Name</span></th>
                                    <th colspan="7" align="center">
                                        <button class="btn btn-info nxt pull-left" <?php echo ($ifPrevWeekDisabled ? "disabled" : "") ?> type="button" id="prev_week_but"><span class="glyphicon glyphicon-backward"></span> Prev Week </button>
                                        <b class="pad-top-1 display-d">Class Dates for the period <?php echo $week_start_date->format('d F Y') ?> to
                                            <?php echo $week_end_date->format('d F Y') ?> </b>
                                        <button class="btn btn-info nxt pull-right" <?php echo ($ifNextWeekDisabled ? "disabled" : "") ?> type="button" id="next_week_but">Next Week <span class="glyphicon glyphicon-forward"></span></button>
                            <!--<div>Check All:<input type="checkbox" id='checkall'></div>  added by shubhranshu--->
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="sub_head">
                                    <td colspan="2">&nbsp;</td>
                                    <?php
                                    $days = array();

                                    for ($curr_day = clone $week_start_date; $curr_day <= $week_end_date; date_add($curr_day, date_interval_create_from_date_string('1 day'))) {
                                        echo print_r($data_arr, true); echo $curr_day->format('Y-m-d'); echo $data_arr[$curr_day->format('Y-m-d')][0]['assmnt_date'];
                                        
                                        if ($curr_day->format('Y-m-d') == $data_arr[$curr_day->format('Y-m-d')][0]['assmnt_date']) {
                                        ?>                                    
                                        <td align="center"><strong>gf<?php echo date_format($curr_day, 'j M [D]') ?></strong></td>
                                        <?php
                                        $days[] = clone $curr_day;
                                        }
                                    }

                                    $days_count = count($days);
                                    ?>
                                </tr>
                                    <?php
                                    if (count($tabledata) == 0) {
                                        echo '<tr><td align="center" colspan="' . ($i + 2) . '" style="color:red">No result found for the search criteria.</td></tr>';
                                    } else {
                                        $i = 0;
                                        $trainees = array();
                                        foreach ($tabledata as $key => $data) {
                                            $trainees[] = $key;

                                            $color = $i % 2 == 0 ? 'white' : 'grey';
                                            ?>
                                        <tr class="<?php echo $color ?>">
                                            <td>
                                                <span style=" float:left;"><?php echo $data['record']['tax_code'] ?></span>
                                            </td>
                                            <td>
                                                <span style="float:left;"><?php echo $data['record']['name'] ?></span>
                                            </td>
                    <?php
                    for ($day = 0; $day < $days_count; $day++) {
                        $cur_date = $days[$day];
                        $ses_date = $cur_date->format('Y-m-d');
                        ?>
                                                <td align="center">
                                                <?php
                                                //echo $days_count;
                                                if (check_class_date_range($start_class, $end_class, $cur_date)) {
                                                    $checkbox_value = check_for_session_checked($data, $cur_date, TRUE);
                                                    echo form_checkbox("mark_attendance[$key][$ses_date][session_01]", '1', $checkbox_value);
                                                }
                                                ?>
                                                </td>
                                                    <?php
                                                }
                                                ?>
                                        </tr>
                                            <?php
                                            $i++;
                                        }
                                    }
                                    ?>

                            </tbody>
                        </table>
                    </div>            
                    <div style="clear:both;"></div>
                    <br>
            <?php
            if (!$is_report_page) {
                $atr = 'id="update_form" name="update_form"';
                echo form_open("class_trainee/mark_attendance_update", $atr);
                echo form_hidden('course_id', set_value('course_id'));
                echo form_hidden('class_id', set_value('class_id'));
                echo form_hidden('subsidy', set_value('subsidy'));
                $trainees = array();
                foreach ($tabledata as $key => $data) {
                    echo form_hidden('trainees[]', $key);
                }
                echo form_hidden('from_date', $from_date);
                echo form_hidden('to_date', $to_date);
                echo form_hidden('week_start', $week_start);
                echo form_hidden('week_end', $week_end);
                echo form_close();
            }
        } else {
            ?>
                    <div class="scroll"> 
                        <label class="red">This class has not yet started. You will only be able to print the manual attendance sheet for the class at point of time. </label>
                    </div>
            <?php
        }
        ?>
            </div>
                <?php
            }
            ?>
    </div>
    <div class="modal" id="ex1" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Heading Goes Here...</h2>
        Detail Goes here.  <br>

        <div class="popup_cancel">
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a>
        </div>
    </p>
    </div>        
    <?php
} else {
    echo "ccc";
    $attendance_status = $this->input->post('attendance_status');
    $start_class = date_create_from_format("d/m/Y", $class_start);
    $end_class = date_create_from_format("d/m/Y", $class_end);
    $from_date_post_value = set_value('from_date');
    $from_date = empty($from_date_post_value) ? $class_start : $from_date_post_value;
    $to_date_post_value = set_value('to_date');
    $to_date = empty($to_date_post_value) ? $class_start : $to_date_post_value;

    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
    // skm st

    $date_1 = str_replace('/', '-', $class_start);
    $date_2 = str_replace('/', '-', $class_end);
    $x = date('Y-m-d', strtotime($date_1));
    $x1 = date('Y-m-d', strtotime($date_2));

    $class_st_date = strtotime($x); //echo "<br/>";
    $class_ed_date = strtotime($x1);
    //$class_ed_date =  strtotime($y);
    if ($class_st_date == $class_ed_date) {
        $xxx = 1;
    } else {
        $today_date = strtotime(CONST_DATE); //echo "<br/>";
        if ($today_date >= $class_st_date) {//or $class_st_date == $class_ed_date)
            $xxx = 1;
        } else {
            $xxx = 0;
        }
    }

// skm ed
    function check_for_session_checked($session_data, $date, $is_first) {
        $format_date = $date->format('Y-m-d');
        if (isset($session_data[$format_date])) {
            $session_by_date_data = $session_data[$format_date];
            if ($is_first) {
                return $session_by_date_data['session_01'] == '1';
            } else {
                return $session_by_date_data['session_02'] == '1';
            }
        }
        return false;
    }

    function check_class_date_range(DateTime $start_class, DateTime $end_class, DateTime $curr_date) {
        return $curr_date <= $end_class && $curr_date >= $start_class && $curr_date <= new DateTime();
    }
    ?>
    <script type="text/javascript">
        var CLIENT_DATE_FORMAT = 'dd/mm/yy';
        var SITE_URL = '<?php echo site_url(); ?>';
        var class_start = '<?php echo $class_start; ?>';
        var class_end = '<?php echo $class_end; ?>';
        var pageurl = '<?php echo $controllerurl; ?>';
        var ancher = '<?php echo $ancher; ?>';
    </script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/mark_attendance.js"></script>
    <div class="overlay">
        <div id="loading-img"></div>
    </div>
    <div class="col-md-10">

        <h2 class="panel_heading_style"><span class="glyphicon glyphicon-pencil"></span> <?php echo $page_title; ?></h2>
    <?php
    if (!empty($message)) {
        echo "<div class='success'>" . $message . "</div>";
    }
    ?>
        <div class="table-responsive">
            <span class="error" id="markAttendanceSubmitErrorForm"></span>
    <?php
    $atr = 'id="search_form" name="search_form"';
    echo form_open($controllerurl, $atr);
    echo form_hidden('orientation', 'P', 'orientation');
    ?>
            <table class="table table-striped">

                <tbody>
                    <tr>
                        <td class="td_heading">Course Name:<span class="required">*</span></td>
                        <td>
    <?php echo form_dropdown("course_id", $courses, set_value('course_id'), 'id="select_course_id"') ?>
                        </td>

                        <td class="td_heading">Class Name:<span class="required">*</span></td>

                        <td colspan='3'>
    <?php
    $attr_js = 'id="select_class_id">';
    echo form_dropdown('class_id', $classes, set_value('class_id'), $attr_js);
    ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Subsidy</td>
                        <td <?php echo ($is_report_page) ? '' : "colspan='2'"; ?>>                        
    <?php
    $subsidy_array = array('' => 'All', 'ws' => 'With Subsidy', 'wts' => 'Without Subsidy', 'fr' => 'Foreginer');
    $attr_js = 'id="subsidy">';
    echo form_dropdown('subsidy', $subsidy_array, $subsidy, $attr_js);
    ?>
                        </td>                        
                        <td>
    <?php
    $attr_js = 'id="input_to_date" style="width:40%;"';
    ?>

                            <div class="pull-right">
    <?php
    echo form_hidden('week_start', $week_start);
    echo form_hidden('week_end', $week_end);
    echo form_hidden('week', null);
    echo form_hidden('export', null);
    echo form_hidden('export1', null);
    ?>
                                <button id="markAttendanceSubmit" class="btn btn-xs btn-primary no-mar" name="markattendance_search_button" title="Search" value="Search">
                                    <span class="glyphicon glyphicon-search"></span> Search
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
    <?php echo form_close(); ?>
        </div>
        <br>
    <?php
    if ($xxx == 1) {
        $check_classid = $this->input->post('class_id');
        if (empty($tabledata) && !empty($check_classid) && empty($attendance_status)) {
            ?>
                <table class="table table-striped">
                    <tr class="danger">
                        <td colspan="10" style="color:red;text-align: center;">There are no students enrolled for the class.</td>
                    </tr>
                </table>
            <?php
        }
        if (!empty($tabledata) || !empty($attendance_status)) {
            $week_start_date = date_create_from_format("d/m/Y", $week_start);
            $week_end_date = date_create_from_format("d/m/Y", $week_end);

            $ifPrevWeekDisabled = $week_start_date <= $start_class;
            $ifNextWeekDisabled = $week_end_date >= $end_class;
            ?>
                <div class="row_dim1">                    
                    <div class="add_button space_style">
            <?php
            //echo $this->session->flashdata('success');
            $current_date_time = strtotime(date("Y-m-d H:i:s"));
            ?>
                    </div>
                    <div style="clear:both;"></div>
            <?php
            if (strtotime($class_start_date) <= strtotime(date('Y-m-d')) || $is_report_page) {
                ?>
                        <div class="scroll">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="th_header sort_header" sort="tu.tax_code" width="5%"><span style="color:#000000;">NRIC / FIN No.</span></th>
                                        <th class="th_header sort_header" sort="name" width="20%"><span style="color:#000000;">Trainee Name</span></th>
                                        <th colspan="7" align="center">
                                            <button class="btn btn-info nxt pull-left" <?php echo ($ifPrevWeekDisabled ? "disabled" : "") ?> type="button" id="prev_week_but"><span class="glyphicon glyphicon-backward"></span> Prev Week </button>
                                            <b class="pad-top-1 display-d">Class Dates for the period <?php echo $week_start_date->format('d F Y') ?> to
                <?php echo $week_end_date->format('d F Y') ?> </b>
                                            <button class="btn btn-info nxt pull-right" <?php echo ($ifNextWeekDisabled ? "disabled" : "") ?> type="button" id="next_week_but">Next Week <span class="glyphicon glyphicon-forward"></span></button>
                                        <!--<div>Check All:<input type="checkbox" id='checkall1'></div>-->
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="sub_head">
                                        <td colspan="2">&nbsp;</td>
                <?php
                $days = array();

                for ($curr_day = clone $week_start_date; $curr_day <= $week_end_date; date_add($curr_day, date_interval_create_from_date_string('1 day'))) {
                    ?>
                                            <td align="center"><strong>bb<?php echo date_format($curr_day, 'j M [D]') ?></strong></td>
                                            <?php $days[] = clone $curr_day; ?>

                                            <?php
                                        }

                                        $days_count = count($days);
                                        ?>
                                    </tr>
                                        <?php
                                        if (count($tabledata) == 0) {
                                            echo '<tr><td align="center" colspan="' . ($i + 2) . '" style="color:red">No result found for the search criteria.</td></tr>';
                                        } else {
                                            $i = 0;
                                            $trainees = array();
                                            foreach ($tabledata as $key => $data) {
                                                $trainees[] = $key;

                                                $color = $i % 2 == 0 ? 'white' : 'grey';
                                                ?>
                                            <tr class="<?php echo $color ?>">
                                                <td>
                                                    <span style=" float:left;"><?php echo $data['record']['tax_code'] ?></span>
                                                </td>
                                                <td>
                                                    <span style="float:left;"><?php echo $data['record']['name'] ?></span>
                                                </td>
                        <?php
                        for ($day = 0; $day < $days_count; $day++) {
                            ?>
                                                    <td align="center">
                                                    <?php
                                                    $cur_date = $days[$day];
                                                    if (check_class_date_range($start_class, $end_class, $cur_date)) {
                                                        $checkbox_value = check_for_session_checked($data, $cur_date, TRUE);
                                                        $ses_date = $cur_date->format('Y-m-d');
                                                        echo form_checkbox("mark_attendance[$key][$ses_date][session_01]", '1', $checkbox_value);
                                                    }
                                                    ?>
                                                    </td>
                                                        <?php
                                                    }
                                                    ?>
                                            </tr>
                                                <?php
                                                $i++;
                                            }
                                        }
                                        ?>
                                </tbody>
                            </table>
                        </div>            
                        <div style="clear:both;"></div>
                        <br>
                <?php
            } else {
                ?>
                        <div class="scroll"> 
                            <label class="red">This class has not yet started. You will only be able to print the manual attendance sheet for the class at point of time. </label>
                        </div>
                <?php
            }
            ?>
                </div>
                    <?php
                }
                ?>
        </div>
        <div class="modal" id="ex1" style="display:none;">
            <p>
            <h2 class="panel_heading_style">Heading Goes Here...</h2>
            Detail Goes here.  <br>

            <div class="popup_cancel">
                <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a>
            </div></p>
        </div>        
        <?php
    } else {
        $courseid = $this->input->post('course_id');
        $classid = $this->input->post('class_id');
        ?>
        <form action="<?php echo base_url(); ?>classes/edit_class" method="post" name ="my_form" id ="my_form">   
            <table class="table table-striped"> 
                <tr class="danger">

                <input type="hidden" class="class_id"  name="class_id"  value="<?php echo $classid; ?>"/>
                <input type="hidden" class="course_id" name="course_id"  value="<?php echo $courseid; ?>"/>
                <td colspan="" style="color:red;text-align: center;">Please Schedule The Class First.
                    &nbsp;&nbsp;&nbsp;<input type="submit"  value ="Schedule Class" /></td>
                </tr>
            </table>
        </form> 
        <?php
    }
} // previous code ends

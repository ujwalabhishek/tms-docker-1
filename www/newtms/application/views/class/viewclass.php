<?php
$colspan = 5;
if (!empty($deactivate_reason)) {
    $colspan = 1;
}
$role_array = array("COMPACT");
?>
<div class="col-md-10">
    <?php if (!in_array($this->session->userdata('userDetails')->role_id, $role_array)) { ?>
        <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/class.png"> Class Detail 
            <span class="label label-default pull-right white-btn"><a href="<?php echo base_url() . 'classes/print_class/' . $class->class_id; ?>"><span class="glyphicon glyphicon-print"></span> Print as PDF</a></span>
        </h2>
    <?php } ?>
    <div class="bs-example">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td class="td_heading">Public Registration</td>
                        <td >
                            <?php
                            echo ($class->display_class_public == '1') ? 'Yes' : 'No';
                            ?>
                        </td>   
                        <td class="td_heading">Class Status</td>
                        <td colspan="3">
                            <?php echo $class_status; ?>
                            <?php
                            if (!empty($deactivate_reason)) {
                                ?>
                                De-activation Reason:&nbsp; 
                                <span class="red"><?php echo $deactivate_reason; ?></span>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="18%" class="td_heading">Class Name:</td>
                        <td width="14%"><label class="label_font"><?php echo $class->class_name; ?></label></td>
                        <td width="19%" class="td_heading">Start Date & Time:</td>
                        <td width="16%"><label class="label_font"><?php echo date('d/m/Y h:i A', strtotime($class->class_start_datetime)); ?></label></td>
                        <td width="19%" class="td_heading">End Date & Time:</td>
                        <td width="14%"><label class="label_font"><?php echo date('d/m/Y h:i A', strtotime($class->class_end_datetime)); ?></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Total Seats:</td>
                        <td><label class="label_font"><?php echo $class->total_seats; ?></label></td>
                        <td class="td_heading">Minimum  Students:</td>
                        <td><label class="label_font"><?php echo $class->min_reqd_students; ?></label></td>
                        <td colspan="2">
                            <?php
                            if ($class->min_reqd_noti_freq1) {
                                $days = ($class->min_reqd_noti_freq1 > 1) ? 'days' : 'day';
                                echo '1st Reminder : ' . $class->min_reqd_noti_freq1 . ' ' . $days;
                            }
                            if ($class->min_reqd_noti_freq2) {
                                $days = ($class->min_reqd_noti_freq2 > 1) ? 'days' : 'day';
                                echo ', 2nd Reminder : ' . $class->min_reqd_noti_freq2 . ' ' . $days;
                            }
                            if ($class->min_reqd_noti_freq3) {
                                $days = ($class->min_reqd_noti_freq3 > 1) ? 'days' : 'day';
                                echo ', 3rd Reminder : ' . $class->min_reqd_noti_freq3 . ' ' . $days;
                            }
                            ?></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Classroom Duration (hrs):</td>
                        <td><label class="label_font"><?php echo $class->total_classroom_duration; ?></label></td>
                        <td class="td_heading">Lab Duration (hrs):</td>
                        <td><label class="label_font"><?php echo $class->total_lab_duration; ?></label></td>
                        <td class="td_heading">Assmnt. Duration (hrs):</td>
                        <td><label class="label_font"><?php echo $class->assmnt_duration; ?></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Fees:</td>
                        <td><label class="label_font">$<?php echo number_format($class->class_fees, 2, '.', ''); ?> SGD</label></td>
                        <td class="td_heading">Class Discount:</td>
                        <td><label class="label_font"><?php echo number_format($class->class_discount, 2, '.', ''); ?>%</label></td>
                        <td class="td_heading">Cert. Collection Date:</td>
                        <td><label class="label_font"><?php
                                if ($class->certi_coll_date != '0000-00-00' && $class->certi_coll_date != NULL) {
                                    echo date('d/m/Y', strtotime($class->certi_coll_date));
                                }
                                ?></label></td>
                    </tr>
                    <?php if ($tpg_crse) { ?>
                        <tr>
                            <td class="td_heading">TPGateway Course Run ID:</td>
                            <td colspan="5"><label class="label_font" id='crs_run_id'><?php echo $class->tpg_course_run_id; ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">TPGateway QR-Code Link:</td>
                            <td colspan="5"><label class="label_font" id='crs_run_id'><a href='<?php echo $class->tpg_qr_code; ?>' target="_blank"><?php echo $class->tpg_qr_code; ?></a></label></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td class="td_heading">Class Language:</td>
                        <td ><?php echo rtrim($ClassLang, ', '); ?></td>
                        <td class="td_heading">Payment Details:</td>
                        <td colspan="3" style="color:blue;"><?php echo rtrim($ClassPay, ', '); ?></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Classroom Venue:</td>
                        <td colspan="5"><label class="label_font"><?php echo rtrim($ClassLoc, ', '); ?></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Lab Venue:</td>
                        <td colspan="5"><label class="label_font"><?php echo rtrim($LabLoc, ', '); ?></label></td>
                    </tr>
                    <?php if ($tpg_crse) { ?>
                        <tr>
                            <td class="td_heading">Venue Room:</td>
                            <td colspan='3'>
                                <label class="label_font">                                    
                                    <?php echo $class->venue_room; ?>
                                </label>                                
                            </td>
                            <td class="td_heading">Survey Language:</td>
                            <td>
                                <label class="label_font">                                    
                                    <?php 
                                    if($class->survey_language == 'EL') {
                                        echo 'English'; 
                                    } else if($class->survey_language == 'MN') {
                                        echo 'Mandarin';
                                    } else if($class->survey_language == 'MY') {
                                        echo 'Malay';
                                    } else if($class->survey_language == 'TM') {
                                        echo 'Tamil';
                                    } else {
                                        echo 'N/A';
                                    }                                    
                                    ?>
                                </label>                                
                            </td>
                        </tr>
                        <tr>
                            <td class="td_heading">Venue Unit:</td>
                            <td>
                                <label class="label_font">
                                    <?php echo $class->venue_unit; ?>
                                </label>                            
                            </td>
                            <td class="td_heading">Venue Block:</td>
                            <td>
                                <label class="label_font">
                                    <?php echo $class->venue_block; ?>
                                </label>                                
                            </td>
                            <td class="td_heading">Venue Floor:</td>
                            <td>
                                <label class="label_font">
                                    <?php echo $class->venue_floor; ?>
                                </label>                                
                            </td>                    
                        </tr>
                        <tr>  
                            <td class="td_heading">Venue Street:</td>
                            <td>
                                <label class="label_font">
                                    <?php echo $class->venue_street; ?>
                                </label>                                
                            </td>
                            <td class="td_heading">Venue Building:</td>
                            <td>
                                <label class="label_font">
                                    <?php echo $class->venue_building; ?>
                                </label>
                            </td>
                            <td class="td_heading">Venue Postal Code:</td>
                            <td>
                                <label class="label_font">
                                    <?php echo $class->venue_postalcode; ?>
                                </label>                                
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="td_heading">  
                                <?php                                
                                if($class->wheel_chair_access == '1') {
                                    $checked = 'checked';
                                } else {
                                    $checked = '';
                                }                                
                                $wheel_chair_accessible = array(
                                    'name' => 'wheel_chair_accessible',
                                    'id' => 'wheel_chair_accessible',
                                    'checked' => $checked                                    
                                );
                                echo form_checkbox($wheel_chair_accessible, '1', set_checkbox('wheel_chair_accessible', '1'));
                                ?>
                                &nbsp;The course run location is wheel chair accessible</td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td class="td_heading">Classroom Trainer:</td>
                        <td><label class="label_font"><?php echo rtrim($ClassTrainer, ', '); ?></label></td>
                        <td class="td_heading">Lab Trainer:</td>
                        <td><label class="label_font"><?php echo rtrim($LabTrainer, ', '); ?></label></td>
                        <td class="td_heading">Assessor:</td>
                        <td><label class="label_font"><?php echo rtrim($Assessor, ', '); ?></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Training Aide:</td>
                        <td><label class="label_font"><?php echo rtrim($TrainingAide, ', '); ?></label></td>
                        <td class="td_heading">No. of sessions per day:</td>
                        <td colspan="3"><label class="label_font"> <?php echo ($class->class_session_day == 1) ? 'One Session' : 'Two Sessions'; ?> </label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Class Description:</td>
                        <td colspan="5" width="83%">
                            <div class="table-responsive payment_scroll" style="height: 87px;min-height: 87px;">
                                <?php echo $class->description; ?>
                            </div>
                        </td>
                    </tr>

                    <?php
                    if (!empty($copy_reason)) {
                        ?>
                    <td class="td_heading">Copied Reason:</td>
                    <td colspan="5">
                        Class copied from Class Id: <?php echo $class->class_copied_from; ?>. 
                        Class Copied by User '<?php echo $copied_user; ?>'. 
                        Copy Reason: <?php echo $copy_reason; ?>.
                    </td>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <!--    <div class="table-responsive"><br>
            <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/officail_details.png"> Sales Executive and Commission Payable</h2>
            <table class="table table-striped">
                <tbody>
    <?php
    if (!empty($SalesExec)) {
        foreach ($SalesExec as $row) {
            ?>
                                                                                                                            <tr>
                                                                                                                                <td class="td_heading">Sales Executive:</td>
                                                                                                                                <td><label class="label_font"><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></label>
                                                                                                                                </td>
                                                                                                                                <td class="td_heading">Commission Rate:</td>
                                                                                                                                <td><label class="label_font"><?php echo number_format($row['commission_rate'], 2, '.', ''); ?>%</label></td>
                                                                                                                            </tr>
            <?php
        }
    } else {
        echo '<tr class="error"><td colspan="4">There is no sales executive available.</td></tr>';
    }
    ?>
                </tbody>
            </table>
        </div>-->

    <div style="clear:both;"></div><br>
    <div class="row marketing">
        <div class="col-lg-6">
            <h4 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/schedule.png"> Class / Lab Schedule 
                <?php if ($tpg_crse) { ?>
                    <a class="small_text" rel="modal:open" href="#view_ssg_session_modal" id='view_ssg_session' style='float:right;cursor:pointer;color:blue;'>View TPG Sessions
                    </a>
                <?php } ?>
            </h4> 
            <p>
            <div class="scroll_schedule">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Class Date</th>
                                <th>Session Type</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $date = '';
                            $data_arr = array();
                            $session_arr = array('S1' => 'Session 1', 'BRK' => 'Break', 'S2' => 'Session 2');
                            foreach ($class_schedule as $row) {
                                $data_arr[$row['class_date']][] = array(
                                    'session' => $row['session_type_id'],
                                    'start' => $row['session_start_time'],
                                    'end' => $row['session_end_time']
                                );
                            }
                            if (!empty($data_arr)) {
                                foreach ($data_arr as $k => $v):
                                    $rowspan = count($v);
                                    $rowspan_td = '<td rowspan="' . $rowspan . '">' . date('F d Y', strtotime($k)) . '</td>';
                                    foreach ($v as $r) {
                                        echo '<tr>
                                            ' . $rowspan_td . '
                                            <td>' . $session_arr[$r['session']] . '</td>
                                            <td>' . date('h:i A', strtotime($r['start'])) . '</td>
                                            <td>' . date('h:i A', strtotime($r['end'])) . '</td>
                                        </tr>';
                                        $rowspan_td = '';
                                    }
                                endforeach;
                            } else {
                                echo '<tr class="error"><td colspan="4">There is no class/lab schedule available.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            </p>
        </div>
        <div class="col-lg-6">
            <h4 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/schedule.png"> Assessment Schedule</h4>
            <p>
            <div class="scroll_schedule1">
                <div class="table-responsive">
                    <table class="table table-striped">

                        <thead>
                            <tr>
                                <th width="18%">Assmnt. Date</th>
                                <?php
                                if (!$tpg_crse) {
                                    if (!in_array($this->session->userdata('userDetails')->role_id, $role_array)) {
                                        ?>
                                        <th>Trainee Name</th>
                                        <?php
                                    }
                                }
                                ?>
                                <th>Assessor</th>
                                <th width="20%">Assmnt. Time</th>
                                <th width="20%">Assmnt. Venue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($def_assessment)) {
                                if ($class->assmnt_type == 'DEFAULT') {
                                    ?>
                                    <tr>
                                        <td><?php echo date('F d Y', strtotime($def_assessment->assmnt_date)); ?></td>
                                        <?php
                                        if (!$tpg_crse) {
                                            if (!in_array($this->session->userdata('userDetails')->role_id, $role_array)) {
                                                ?>
                                                <td><?php echo ($def_assessment->assmnt_type == 'DEFAULT') ? 'ALL' : ''; ?></td>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <td><?php echo $DefAssId; ?></td>
                                        <td><?php echo date('h:i A', strtotime($def_assessment->assmnt_start_time)) . ' - ' . date('h:i A', strtotime($def_assessment->assmnt_end_time)); ?></td>
                                        <td><?php echo $DefAssLoc; ?></td>
                                    </tr>
                                    <?php
                                } else {
                                    foreach ($def_assessment as $row) {
                                        $assess_date = date('d-m-Y', strtotime($row['assmnt_date']));
                                        $start_time = date('H:i', strtotime($row['assmnt_start_time']));
                                        $end_date = date('H:i', strtotime($row['assmnt_end_time']));
                                        ?>
                                        <tr>
                                            <td><?php echo $assess_date; ?></td>
                                            <?php
                                            if (!$tpg_crse) {
                                                if (!in_array($this->session->userdata('userDetails')->role_id, $role_array)) {
                                                    ?>
                                                    <td><?php echo implode(', ', $row['trainee']); ?></td>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <td><?php echo $row['DefAssId']; ?></td>
                                            <td><?php echo $start_time . ' - ' . $end_date; ?></td>
                                            <td><?php echo $row['DefAssLoc']; ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                            } else {
                                echo '<tr class="error"><td colspan="5">There is no assessment schedule available.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            </p>
            <div style="clear:both;">&nbsp;</div>
            <div class="button_class">
                <a href="<?php echo site_url(); ?>classes?course_id=<?php echo $class->course_id; ?>">
                    <button class="btn btn-primary" type="button">
                        <span class="glyphicon glyphicon-step-backward"></span></span>&nbsp;Back</button>
                </a>
            </div>
        </div>
    </div>
</div>


<div class="modal1_0001" id="view_ssg_session_modal" style="display:none;height:227px;min-height: 200px;">
    <h2 class="panel_heading_style">View TPG Live Sessions</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="td_heading">SL.NO</th>
                <th class="td_heading">Session ID</th>
                <th class="td_heading">Start Date</th>
                <th class="td_heading">End Date</th>
                <th class="td_heading">Start Time</th>
                <th class="td_heading">End Time</th>
                <th class="td_heading">Attendance Taken</th>
            </tr>
        </thead>
        <tbody id='ssg_sess'>


        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        $siteurl = '<?php echo site_url(); ?>';
        $('#view_ssg_session').click(function () {
            $('#ssg_sess').html('');
            $crs_run_id = $('#crs_run_id').html();
            $class_id = '<?php echo $classid; ?>';
            $.ajax({
                type: 'post',
                url: $siteurl + 'classes/get_ssg_session',
                data: {crs_run_id: $crs_run_id, class_id: $class_id},
                async: false,
                success: function (res) {
                    json_data = $.parseJSON(res);
                    if (json_data != '') {
                        $.each(json_data.data.sessions, function (i, item) {
                            $('#ssg_sess').append('<tr><td>' + (i + 1) + '</td><td>' + item.id + '</td><td>' + item.startDate + '</td><td>' + item.endDate + '</td><td>' + item.startTime + '</td><td>' + item.endTime + '</td><td>' + item.attendanceTaken + '</td></tr>');
                        });
                    }
                }
            });
        });
    });

</script>
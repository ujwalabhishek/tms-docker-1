<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.ui.timepicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery.ui.timepicker.css" />
<?php
$this->load->helper('form');
$this->load->helper('metavalues_helper');
$CI = & get_instance();
$CI->load->model('course_model');
$CI->load->model('meta_values');
if (!empty($tax_error)) {
    echo '<div class="error1">' . $tax_error . '</div>';
}
?>
<script>
    $siteurl = '<?php echo site_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/copyclass.js"></script>
<div class="col-md-10">
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/class.png"> Class - Copy</h2>
    <div class="table-responsive">
        <?php
        $atr = 'id="search_form" name="search_form"';
        echo form_open("classes/copy_class", $atr);
        ?>  
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading" width="20%">Search by Course Name:<span class="required">*</span></td>
                    <td width="30%">
                        <?php
                        $course_name = array(
                            'name' => 'course_name',
                            'id' => 'course_name',
                            'value' => $this->input->post('course_name'),
                            'maxlength' => 250
                        );
                        echo form_input($course_name);

                        $course_id = array(
                            'name' => 'course_id',
                            'id' => 'course_id',
                            'value' => $this->input->post('course_id'),
                            'maxlength' => 5,
                            'type' => 'hidden'
                        );
                        echo form_input($course_id);
                        ?>
                        <span id="course_name_err"></span>
                    </td>
                    <td class="td_heading" width="20%">Class Name:<span class="required">*</span></td>
                    <td  width="20%">
                        <?php
                        $class_options = array();
                        $class_js = 'id="class_id"';
                        $class_options[''] = 'Select';
                        $class_js = (!empty($classes)) ? $class_js : $class_js . ' disabled="disabled"';
                        foreach ($classes as $k => $v) {
                            $class_options[$k] = $v;
                        }
                        echo form_dropdown('class_id', $class_options, $this->input->post('class_id'), $class_js);
                        ?>
                        <span id="class_id_err"></span>
                    </td>
                    <td align="center"><button title="Search" value="Search" type="submit" class="btn btn-xs btn-primary no-mar">
                            <span class="glyphicon glyphicon-search"></span>
                            Search
                        </button></td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
    <br>
    <?php if (!empty($class)) { ?>
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/company-detail.png"> Class Details</h2>
        <div class="bs-example">
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
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
                                    echo '1st Reminder : ' . $class->min_reqd_noti_freq1 . ' days';
                                }
                                if ($class->min_reqd_noti_freq2) {
                                    echo ', 2nd Reminder : ' . $class->min_reqd_noti_freq2 . ' days';
                                }
                                if ($class->min_reqd_noti_freq3) {
                                    echo ', 3rd Reminder : ' . $class->min_reqd_noti_freq3 . ' days';
                                }
                                ?>
                            </td>
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
                            <td colspan="2" class="td_heading">
                                <?php
                                $checked = FALSE;
                                if ($class->display_class_public == 1) {
                                    $checked = TRUE;
                                }
                                $display_class = array(
                                    'name' => 'display_class',
                                    'id' => 'display_class',
                                    'disabled' => 'disabled',
                                    'checked' => $checked,
                                );
                                echo form_checkbox($display_class, 1);
                                ?>&nbsp;Display class for public registration</td>
                            <td class="td_heading">Class Language:</td>
                            <td colspan="3"><label class="label_font"><?php echo rtrim($ClassLang, ', '); ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">No. of sessions per day:</td>
                            <td colspan="5"><label class="label_font"> <?php echo ($class->class_session_day == 1) ? 'One Session' : 'Two Sessions'; ?> </label></td>
                        </tr>
                        <tr>
                            <td class="td_heading red">Payment Details:<span class="required">*</span></td>
                            <td colspan="5" class="red"><?php echo rtrim($ClassPay, ', '); ?></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Classroom Location:</td>
                            <td colspan="5"><label class="label_font"><?php echo rtrim($ClassLoc, ', '); ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Lab Location:</td>
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
                                        if ($class->survey_language == 'EL') {
                                            echo 'English';
                                        } else if ($class->survey_language == 'MN') {
                                            echo 'Mandarin';
                                        } else if ($class->survey_language == 'MY') {
                                            echo 'Malay';
                                        } else if ($class->survey_language == 'TM') {
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
                                <td colspan="6" class="td_heading">  
                                    <?php
                                    if ($class->wheel_chair_access == '1') {
                                        $checked_wheel = 'checked';
                                    } else {
                                        $checked_wheel = '';
                                    }
                                    $wheel_chair_accessible = array(
                                        'name' => 'wheel_chair_accessible',
                                        'id' => 'wheel_chair_accessible',
                                        'checked' => $checked_wheel,
                                        'disabled' => 'disabled'
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
                            <td>
                                <label class="label_font"><?php echo rtrim($LabTrainer, ', '); ?></label>
                            </td>
                            <td class="td_heading">Assessor:</td>
                            <td><label class="label_font"><?php echo rtrim($Assessor, ', '); ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Training Aide:</td>
                            <td colspan="5"><label class="label_font"><?php echo rtrim($TrainingAide, ', '); ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Class Description:</td>
                            <td colspan="5" width="83%">
                                <div class="table-responsive payment_scroll" style="height: 87px;min-height: 87px;">
                                    <?php echo $class->description; ?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-responsive"><br>
            <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/officail_details.png"> Sales Executive and Commission Rate</h2>
            <table class="table table-striped">
                <tbody>
                    <?php
                    if ($SalesExec) {
                        foreach ($SalesExec as $row) {
                            array_search($S, $haystack)
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
        </div>
        <br>
        <span class="required required_i">* Required Fields</span>
        <div class="button_class"><a class="small_text" rel="modal:open" href="#ex9"><button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-file"></span>&nbsp;Copy</button></a></div>
    </div>
    <div class="modal" id="ex1" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Heading Goes Here...</h2>
        Detail Goes here.  <br>
        <div class="popup_cancel">
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a></div></p>
    </div>
    <?php
    if ($tpg_crse) {
        $atr = 'id="copy_form" name="copy_form" method="post"';
        echo form_open("classes/copy_classes_tpg", $atr);
    } else {
        $atr = 'id="copy_form" name="copy_form" method="post"';
        echo form_open("classes/copy_classes", $atr);
    }
    ?>
    <div class="modal1_55555_99" id="ex9" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Copy Class</h2>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="30%" class="td_heading">Class Name:</td>
                    <td> <?php
                        $class_name = array(
                            'name' => 'class_name',
                            'id' => 'class_name',
                            'value' => $this->input->post('class_name'),
                            'maxlength' => 50
                        );
                        echo form_input($class_name);
                        ?>
                        <span id="class_name_err"></span>
                        <?php
                        $course_name = array(
                            'name' => 'course_name_hidden',
                            'id' => 'course_name_hidden',
                            'value' => $this->input->post('course_name'),
                            'maxlength' => 50,
                            'type' => 'hidden'
                        );
                        echo form_input($course_name);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Class Start Date & Time:<span class="required">*</span></td>
                    <td><?php
                        $start_date = array(
                            'name' => 'start_date',
                            'id' => 'start_date',
                            'readonly' => 'readonly',
                            'value' => $this->input->post('start_date'),
                        );
                        echo form_input($start_date);
                        $start_time = array(
                            'name' => 'start_time',
                            'id' => 'start_time',
                            'readonly' => 'readonly',
                            'style' => '',
                            'value' => $this->input->post('start_time'),
                        );
                        echo '&nbsp;&nbsp;&nbsp;';
                        echo form_input($start_time);
                        ?>
                        &nbsp; 
                        <div>
                            <span style="max-width:45%;" id="start_date_err"></span>
                            <span id="start_time_err" style="max-width:45%;float:right;"></span>
                        </div></td>
                </tr>
                <tr>
                    <td class="td_heading">Class End Date & Time:<span class="required">*</span></td>
                    <td><?php
                        $end_date = array(
                            'name' => 'end_date',
                            'id' => 'end_date',
                            'readonly' => 'readonly',
                            'value' => $this->input->post('end_date'),
                        );
                        echo form_input($end_date);
                        $end_time = array(
                            'name' => 'end_time',
                            'id' => 'end_time',
                            'readonly' => 'readonly',
                            'style' => '',
                            'value' => $this->input->post('end_time'),
                        );
                        echo '&nbsp;&nbsp;&nbsp;';
                        echo form_input($end_time);
                        ?>
                        &nbsp; 
                        <div>
                            <span style="max-width:45%;" id="end_date_err"></span>
                            <span style="max-width:100%;float:right;" id="end_time_err"></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Reason:<span class="required">*</span></td>
                    <td><?php
                        $copy_reason_options[''] = 'Select';
                        $copy_reason = fetch_metavalues_by_category_id(Meta_Values::COPY_REASON);
                        foreach ($copy_reason as $val):
                            $copy_reason_options[$val['parameter_id']] = $val['category_name'];
                        endforeach;
                        $copy_reason_options['OTHERS'] = 'Others';
                        echo form_dropdown('copy_reason', $copy_reason_options, $this->input->post('copy_reason'), 'id="copy_reason"');
                        ?> &nbsp; <div id="row_dim_new1" style="float:right; margin-right:25%;"> <?php
                        $other_reason = array(
                            'name' => 'other_reason',
                            'id' => 'other_reason',
                            'value' => $this->input->post('other_reason'),
                            'maxlength' => 250,
                            'style' => 'display:none',
                            'class' => 'upper_case'
                        );
                        echo form_input($other_reason);
                        $class_hid = array(
                            'name' => 'class_hid',
                            'id' => 'class_hid',
                            'value' => $this->input->post('class_id'),
                            'maxlength' => 50,
                            'type' => 'hidden'
                        );
                        echo form_input($class_hid);
                        ?></div><div id="copy_reason_err"></div></td>
                </tr>
                <tr>
                    <td colspan="2"><span class="required">Note: <i>Class name if entered should be unique or leave it blank to use class code.</i></span></td>
                </tr>
                <tr><!--added by shubhranshu on 12/3/2018-->
                    <td colspan="2"><span class="required">Note: <i>If the class is more than one days then kindly create the session before using the class.</i></span></td>
                </tr>
            </tbody>
        </table>
        <span class="required required_i">* Required Fields</span>
        <div class="popup_cance89">
            <div rel="modal:close"><button class="btn btn-primary copy_save" type="submit">Ok</button>&nbsp;&nbsp;<a href="#" rel="modal:close"><button class="btn btn-primary deactivate_cancel" type="button">Cancel/ Delete</button></a></div></div>
    </p>
    </div>
    <?php echo form_close(); ?>
<?php } ?>

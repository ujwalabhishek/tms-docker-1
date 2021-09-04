<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/multiselectdropdown.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/multiselectdropdown.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/multidropdown.js"></script>
<script>
    $siteurl = '<?php echo site_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/reschedule.js"></script>
<div class="col-md-10">
    <?php echo validation_errors('<div class="error1">', '</div>'); ?> 
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-refresh"></span> Class Trainee Enrollment - Re-Schedule</h2>

    <?php
    $atr = 'id="search_form" name="search_form" method="post" onsubmit= "return disable_button()"';
    echo form_open("class_trainee/re_schedule", $atr);
    ?>  
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading" width="20%">Re-Schedule By:<span class="required">*</span></td>
                    <td>
                        <?php
                        $type_js = 'id="type"';

                        $type_options = array(
                            '' => 'Select',
                            '3' => 'Company',
                            '2' => 'Course - Class',
                            '1' => 'NRIC/FIN No.',
                            '4' => 'Trainee Name',
                        );
                        echo form_dropdown('type', $type_options, $this->input->post('type'), $type_js);
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="row_dim1" style="<?php echo ($this->input->post('type') == 1) ? '' : 'display:none;'; ?>">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td  width="20%" class="td_heading no-bg">NRIC/FIN No.:<span class="required">*</span></td>
                        <td class="no-bg"><?php
                            $tax_code = array(
                                'id' => 'tax_code',
                                'name' => 'tax_code',
                                'value' => $this->input->post('tax_code')
                            );
                            echo form_input($tax_code);
                            $taxcode_id = array(
                                'id' => 'taxcode_id',
                                'name' => 'taxcode_id',
                                'type' => 'hidden',
                                'value' => $this->input->post('taxcode_id')
                            );
                            echo form_input($taxcode_id);
                            ?>
                            <span id="tax_code_err"></span>                        
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="required required_i">* Required Fields</span>
                            <button class="search_button pull-right btn btn-xs btn-primary no-mar" name="submit" type="submit" value="Search" title="Search">
                                <span class="glyphicon glyphicon-search"></span>
                                Search
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div id="row_dim4" style="<?php echo ($this->input->post('type') == 4) ? '' : 'display:none;'; ?>">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td  width="20%" class="td_heading no-bg">Trainee Name:<span class="required">*</span></td>
                        <td class="no-bg"><?php
                            $trainee_name_serach = array(
                                'id' => 'trainee_name_serach',
                                'name' => 'trainee_name_serach',
                                'value' => $this->input->post('trainee_name_serach'),
                                'style' => 'width:400px',
                            );
                            echo form_input($trainee_name_serach);
                            $taxcode_id = array(
                                'id' => 'taxcode_user_id',
                                'name' => 'taxcode_user_id',
                                'type' => 'hidden',
                                'value' => $this->input->post('taxcode_user_id')
                            );
                            echo form_input($taxcode_id);
                            ?>
                            <span id="trainee_name_serach_err"></span>                        
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="required required_i">* Required Fields</span>
                            <button class="search_button pull-right btn btn-xs btn-primary no-mar" name="submit" type="submit" value="Search" title="Search">
                                <span class="glyphicon glyphicon-search"></span>
                                Search
                            </button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div id="row_dim3" style="<?php echo ($this->input->post('type') == 3) ? '' : 'display:none;'; ?>">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr style="display:none"><td colspan="2"></td></tr>
                    <tr>
                        <td  width="20%" class="td_heading">Company:<span class="required">*</span></td>
                        <td class="">
                            <?php
                            $company_js = 'id="company" style="width:50%"';
                            $company_options = array(
                                '' => 'Select',
                            );
                            foreach ($companies as $row):
                                $company_options[$row->company_id] = $row->company_name;
                            endforeach;

                            $tenant_details = fetch_tenant_details($this->session->userdata('userDetails')->tenant_id);
                            $company_options[$tenant_details->tenant_id] = $tenant_details->tenant_name;
                            echo form_dropdown('company', $company_options, $this->input->post('company'), $company_js);
                            ?>
                            <span id="company_err"></span>                        
                        </td>
                    </tr>
                    <tr>
                        <td  width="20%" class="td_heading">Trainee Name:<span class="required">*</span></td>
                        <td class=""><?php
                            $tax_code = array(
                                'id' => 'trainee_name',
                                'name' => 'trainee_name',
                                'value' => $this->input->post('trainee_name'),
                                'style' => 'width:400px',
                            );
                            echo form_input($tax_code);
                            $taxcode_id = array(
                                'id' => 'trainee_id',
                                'name' => 'trainee_id',
                                'type' => 'hidden',
                                'value' => $this->input->post('trainee_id')
                            );
                            echo form_input($taxcode_id);
                            ?>
                            <div style='color:blue; font-size:10px;'>Enter minimum of 4 characters to search</div>
                            <span id="trainee_name_err"></span>                        
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><span class="required required_i">* Required Fields</span>
                            <button class="search_button pull-right btn btn-xs btn-primary no-mar" name="submit" type="submit" value="Search" title="Search">
                                <span class="glyphicon glyphicon-search"></span>
                                Search
                            </button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div id="row_dim2" style="<?php echo ($this->input->post('type') == 1 || $this->input->post('type') == 4 || $this->input->post('type') == 3) ? '' : 'display:none;'; ?>">
        <?php
        if ($this->input->post('type') == 2) {
            $taxcode_id = $this->input->post('taxcode_id');
        } else if ($this->input->post('type') == 3) {
            $taxcode_id = $this->input->post('trainee_id');
        } else if ($this->input->post('type') == 4) {
            $taxcode_id = $this->input->post('taxcode_user_id');
        }
        if (!empty($taxcode_id)) {
            if (!empty($active_enroll_class)) {
                $but_sty = '';
                ?>
                <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-repeat"></span> Re-Schedule Details</h2>
                <div style="width:100%">
                    <strong>Current enrollments - <?php echo $users; ?></strong> &nbsp; &nbsp; <span id="active_class_err"></span>
                    <div class="border_div">
                        <?php
                        if ($active_enroll_class) {
                            foreach ($active_enroll_class as $k => $v) {
                                if ($active_enroll_lock_att_status[$k] == 1) {
                                    $data = array(
                                        'name' => 'active_class',
                                        'id' => 'active_class',
                                        'class' => 'active_class',
                                        'value' => $k,
                                        'class' => 'active_class course_' . $active_enroll_course_id[$k],
                                        'course_id' => $active_enroll_course_id[$k],
                                        'checked' => set_checkbox('active_class', $k),
                                        'disabled' => 'disabled'
                                    );
                                    echo form_radio($data) . $v . '<br/>';                                    
//                                } else if ($active_enroll_eid_no[$k] != "" && $active_enroll_tpg_status[$k] != "") {
//                                    $data = array(
//                                        'name' => 'active_class',
//                                        'id' => 'active_class',
//                                        'class' => 'active_class',
//                                        'value' => $k,
//                                        'class' => 'active_class course_' . $active_enroll_course_id[$k],
//                                        'course_id' => $active_enroll_course_id[$k],
//                                        'checked' => set_checkbox('active_class', $k),
//                                        'disabled' => 'disabled'
//                                    );
//                                    echo form_radio($data) . $v . '<br/>';
//                                    if ($active_enroll_eid_no[$k] != "" && $active_enroll_tpg_status[$k] != "") {
//                                        echo "<i>The enrolment details of this class has been submitted to TPG. Please, use TPG edit enrolment option for same.</i>";
//                                    }
                                } else {
                                    $data = array(
                                        'name' => 'active_class',
                                        'id' => 'active_class',
                                        'class' => 'active_class',
                                        'value' => $k,
                                        'class' => 'active_class course_' . $active_enroll_course_id[$k],
                                        'course_id' => $active_enroll_course_id[$k],
                                        'checked' => set_checkbox('active_class', $k)
                                    );
                                    echo form_radio($data) . $v . '<br/>';
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                <div style="clear:both;"></div>
                <br>
                <div style="width:100%">

                    <strong>Available active classes for Re-Schedule</strong> &nbsp; &nbsp; <span id="reschedule_class_err"></span>
                    <div class="border_div">
        <?php
        if ($reschedule_enroll_class) {
            $i = 0;
            foreach ($reschedule_enroll_class as $k => $v) {
                if ($reschedule_enroll_lock_att_status[$k] == 1) {
                    $data = array(
                        'name' => 'reschedule_class',
                        'id' => 'reschedule_class',
                        'class' => 'reschedule_class',
                        'value' => $k,
                        'class' => 'reschedule_class course_' . $reschedule_enroll_course_id[$k],
                        'course_id' => $reschedule_enroll_course_id[$k],
                        'checked' => set_checkbox('reschedule_class', $k),
                        'disabled' => 'disabled'
                    );
                    echo form_radio($data) . $v . '<br/>';
                    $lockdata = array(
                        'name' => 'reschedule_class_lock_status',
                        'id' => 'reschedule_class_lock_status',
                        'class' => 'reschedule_class_lock_status_' . $i,
                        'value' => $reschedule_enroll_lock_att_status[$k]
                    );
                    echo form_hidden($lockdata);
                } else {
                    $data = array(
                        'name' => 'reschedule_class',
                        'id' => 'reschedule_class',
                        'class' => 'reschedule_class',
                        'value' => $k,
                        'class' => 'reschedule_class course_' . $reschedule_enroll_course_id[$k],
                        'course_id' => $reschedule_enroll_course_id[$k],
                        'checked' => set_checkbox('reschedule_class', $k)
                    );
                    echo form_radio($data) . $v . '<br/>';
                    $lockdata = array(
                        'name' => 'reschedule_class_lock_status',
                        'id' => 'reschedule_class_lock_status',
                        'class' => 'reschedule_class_lock_status_' . $i,
                        'value' => $reschedule_enroll_lock_att_status[$k]
                    );
                    echo form_hidden($lockdata);
                }
                $i++;
            }
        }
        ?>

                    </div>
                </div>

                        <?php
                    } else {
                        $but_sty = 'display:none;';
                        echo "<span class='error' id='taxcode_alert'>There is no active enrollment(s) for this trainee.</span>";
                    }
                    ?>
            <div style="width:100%;float:left;<?php echo $but_sty; ?>"><br/><div class="push_right"><button type="submit" class="btn btn-primary re_schedule" name="submit" value="reschedule"><span class="glyphicon glyphicon-saved"></span> Re-Schedule</button>
                </div></div>
            <div style="clear:both;"></div>            
        <?php } ?>    

    </div>
    <div id="row_dim" style="<?php echo ($this->input->post('type') == 2) ? '' : 'display:none;'; ?>">
        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-repeat"></span> Re-Schedule Details</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td width="21%" class="td_heading">Select Course:<span class="required">*</span></td>
                        <td>
<?php
$course_options = array();
$course_js = 'id="course_id"';
$course_options[''] = 'Select';
foreach ($courses as $k => $v) {
    $course_options[$k] = $v;
}
echo form_dropdown('course_id', $course_options, $this->input->post('course_id'), $course_js);
?>
                            <span id="course_id_err"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Re-Schedule From Class:<span class="required">*</span></td>
                        <td>
                            <?php
                            $active_options = array();
                            $aactive_js = 'id="course_active_class" ';
                            $active_options[''] = 'Select';
                            foreach ($course_active_enroll_class as $k => $v) {
                                $active_options[$k] = $v;
                            }
                            $default = $this->input->post('course_active_class') . ',0'; ///added by shubhranshu since zero is coming for default
                            echo form_dropdown('course_active_class', $active_options, $default, $aactive_js);
                            ?>
                            <span id="course_active_class_err"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Re-Schedule To Class:<span class="required">*</span></td>
                        <td>
                            <?php
                            $reschedule_options = array();
                            $reschedule_js = 'id="course_reschedule_class" ';
                            $reschedule_options[''] = 'Select';
                            foreach ($course_reschedule_enroll_class as $k => $v) {
                                $reschedule_options[$k] = $v;
                            }
                            $default = $this->input->post('course_reschedule_class') . ',0'; ////added by shubhranshu since zero is coming
                            echo form_dropdown('course_reschedule_class', $reschedule_options, $default, $reschedule_js);
                            ?>
                            <span id="course_reschedule_class_err"></span>
                        </td>
                    </tr>
                    <tr class='trainee_tr'>
                        <td class="td_heading">Select From Class Trainee:<span class="required">*</span></td>
                        <td>
                            <?php
                            $trainee_options = array();
                            $trainee_js = 'id="control_6" multiple="multiple" size="5"';
                            $trainee_options[''] = 'Select';
                            foreach ($course_trainee as $k => $v) {
                                $trainee_options[$k] = $v;
                            }
                            if ($course_trainee) {
                                echo form_dropdown('control_6[]', $trainee_options, $this->input->post('control_6'), $trainee_js);
                            } else {
                                echo '<select disabled="disabled" id="control_n"><option>select</option></select>';
                            }
                            ?>
                            <span id="control_6_err"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div style="width:100%;float:left;"><div class="push_right"><button value="reschedule" name="submit" class="btn btn-primary re_schedule" type="submit"><span class="glyphicon glyphicon-saved"></span> Re-Schedule</button>
                </div>
            </div>
            <div><span class="required required_i">* Required Fields</span></div>
        </div>
    </div>
<?php
if (!empty($reschedule_classes)) {
    ?>
        <div class="row_main">
            <div class="table-responsive"><br>
                <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-ok"></span> Confirm Reschedule: Course - Class Details</h2>
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td width="8%" class="td_heading">Course Name:</td>
                            <td width="17%"><label class="label_font"><?php echo $reshedule_courses->crse_name; ?></label></td>
                            <td width="10%" class="td_heading">Course Manager:</td>
                            <td width="14%"><label class="label_font"><?php echo rtrim($courseManager, ', '); ?></label></td>          
                        </tr>
                        <tr>
                            <td class="td_heading">Course Duration:</td>
                            <td ><label class="label_font"><?php echo $reshedule_courses->crse_duration; ?> hrs</label></td>
                            <td class="td_heading">No. of sessions per day:</td>
                            <td ><label class="label_font"> <?php echo ($reschedule_classes->class_session_day == 1) ? 'One Session' : 'Two Sessions'; ?> </label></td>
                        </tr>
                        <tr>
                            <td width="8%" class="td_heading">Course Ref. Number:</td>
                            <td width="17%"><label class="label_font"><?php echo $reshedule_courses->reference_num; ?></label></td>
                            <td width="10%" class="td_heading">Course Competency Code:</td>
                            <td width="14%"><label class="label_font"><?php echo $reshedule_courses->competency_code; ?></label></td>
                        </tr>
                        <tr>
                            <td width="8%" class="td_heading">Cert. Code/ Level:</td>
                            <td width="17%"><label class="label_font"><?php echo rtrim($courseLevel, ', '); ?></label></td>
                            <td width="10%" class="td_heading">Sales Executive:</td>
                            <td width="14%"><label class="label_font"><?php
    $sales = '';
    foreach ($SalesExec as $row) {
        $sales .=$row['first_name'] . ' ' . $row['last_name'] . ', ';
    }
    echo rtrim($sales, ', ');
    ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Class Name:</td>
                            <td ><label class="label_font"><?php echo $reschedule_classes->class_name; ?></label></td>
                            <td class="td_heading">Class Language:</td>
                            <td><label class="label_font"><?php echo rtrim($ClassLang, ', '); ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Start Date & Time:</td>
                            <td ><label class="label_font"><?php echo date('d/m/Y H:i A', strtotime($reschedule_classes->class_start_datetime)); ?></label></td>
                            <td class="td_heading">End Date & Time:</td>
                            <td ><label class="label_font"><?php echo date('d/m/Y H:i A', strtotime($reschedule_classes->class_end_datetime)); ?></label></td>          
                        </tr>
                        <tr>
                            <td class="td_heading">Total Seats:</td>
                            <td><label class="label_font"><?php echo $reschedule_classes->total_seats; ?></label></td>
                            <td class="td_heading">Available Seats:</td>
                            <td><label class="label_font"><?php echo ($available < 0) ? 0 : $available; ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Booked Seats:</td>
                            <td><label class="label_font"><?php echo $totalbooked; ?></label></td>
                            <td class="td_heading">Payment Mode:</td>
    <?php
    $extra_pay = ($reschedule_classes->class_pymnt_enrol == 'PAENROL') ? ' <span style="color:red;">(Over Booking Allowed)</span>' : '';
    ?>
                            <td><label class="label_font"><?php echo $ClassPay . $extra_pay; ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Class Unit Fees:</td>
                            <td><label class="label_font">$<?php echo number_format($reschedule_classes->class_fees, 2); ?> SGD</label></td>
                            <td class="td_heading">Trainer:</td>
                            <td><label class="label_font"><?php echo $ClassTrainer; ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Assessor:</td>
                            <td >
    <?php if (!empty($Assessor)) { ?>
                                    <label class="label_font"><?php echo $Assessor; ?></label>
    <?php } else { ?>
                                    <label class="label_font">--</label>
    <?php } ?>
                            </td>
                            <td class="td_heading">Training Aide:</td>
                            <td>
                                <?php if (!empty($TrainingAide)) { ?>
                                    <label class="label_font"><?php echo $TrainingAide; ?></label>
                                <?php } else { ?>
                                    <label class="label_font">--</label>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="td_heading">Location/Address:</td>
                            <td colspan="3"><label class="label_font"><?php echo rtrim($ClassLoc, ', '); ?></label></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="error">
                <i>(Re-Schedule mail will be sent to all the trainees. They will be removed from the current class assignment.)</i>
            </p>
            <div style="clear:both;"></div>
            <div class="push_right"><a href="#ex9" rel="modal:open" class="small_text"><button class="btn btn-primary" type="button">
                        <span class="glyphicon glyphicon-saved"></span>&nbsp;Confirm Re-Schedule</button></a>
            </div>
            <div class="modal1_55 modal1_popup" id="ex9" style="display:none;">
                <p>
                <h2 class="panel_heading_style">Confirm Re-Schedule</h2>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td colspan="3">Are you sure you want to re-schedule this class?</td>
                            </tr>
                            <tr>
                                <td class="td_heading">Reason:<span class="required">*</span></td>
                                <td>
    <?php
    $copy_reason_options[''] = 'Select';
    $copy_reason = fetch_metavalues_by_category_id(Meta_Values::RESCH_REASON);
    foreach ($copy_reason as $val):
        $copy_reason_options[$val['parameter_id']] = $val['category_name'];
    endforeach;
    $copy_reason_options['OTHERS'] = 'Others';
    echo form_dropdown('reschedule_reason', $copy_reason_options, $this->input->post('reschedule_reason'), 'id="reschedule_reason"');
    ?>
                                    <span id="reschedule_reason_err"></span>                                     
                                    <div class="row_dimm row_dimm_popup no_border_table"> <?php
                                    $style = ($this->input->post('reschedule_reason') == 'OTHERS') ? '' : 'display:none';
                                    $style .=";width:400px";
                                    $other_reason = array(
                                        'name' => 'other_reason',
                                        'id' => 'other_reason',
                                        'value' => $this->input->post('other_reason'),
                                        'maxlength' => 250,
                                        'style' => $style,
                                        'class' => 'upper_case'
                                    );
                                    echo form_input($other_reason);
                                    ?>
                                        <span id="other_reason_err"></span>
                                    </div>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                <span class="required required_i">* Required Fields</span>
                <div class="popup_cancel9" style="margin-top:0px;">
                    <div href="#" rel="modal:close"><button class="btn btn-primary save" type="submit" name="submit" value="save">Save</button></div></div></p>
            </div>
        </div>
    <?php
}
echo form_close();
?>
    <div class="cannot_change_div" style="display: none; background-color: #f4fcff; height: 50px;text-align: center">                    
        <span style="color:red;font-weight: bold">
            Payment has been received for the invoice in which this trainee is linked. Trainee cannot be re-scheduled. 
            <br/>Please use refund to return excess amount collected and enroll new.
        </span>
        <br/>                       
    </div>
    <div style=" height: 50px;text-align:">                    
        <p class="error"><i>(***    Can`t Re-schedule in class whose attendance is locked. To Re-schedule it please contact to Administrator.)</i>
        </p>
        <br/>                       
    </div>
</div>
<div class="modal1_00_55" id="ex8" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Confirm Re-Schedule</h2>
    <table class="no_border_table">
        <tbody>
            <tr>
                <td></td>
                <td>
                    <div class="popup_cancel9">
                        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Yes</button></a>&nbsp;&nbsp;<a href="#" rel="modal:close"><button class="btn btn-primary" type="button">No</button></a></div></td>
            </tr>
        </tbody>
    </table>
    <table class="no_border_table">
        <tbody>
            <tr>
                <td class="td_heading">Reason:<span class="required">*</span></td>
                <td></td>
                <td><div class="row_dimm">
                        <table class="no_border_table">
                            <tbody>
                                <tr>
                                    <td><input type="text" value="" style="width:165%;">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div></td>
            </tr>
        </tbody>
    </table>
    <br>
    <span class="required required_i">* Required Fields</span>
    <div class="popup_cancel9" style="margin-top:0px;">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Save</button></a>
    </div></p>
</div>


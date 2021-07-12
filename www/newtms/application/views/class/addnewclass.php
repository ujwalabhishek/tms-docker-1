<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/multiselectdropdown.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/multiselectdropdown.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/multidropdown.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.ui.timepicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery.ui.timepicker.css" />
<script>
    $siteurl = '<?php echo site_url(); ?>';  
    $js_role = '<?php echo $role; ?>';   
    $js_tenant = '<?php echo TENANT_ID; ?>';  
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/add_new_class.js?0.00708614899707789889908114598"></script>
<?php
$this->load->helper('form');
$this->load->helper('metavalues_helper');
$this->load->model('meta_values');
if (!empty($tax_error)) { 
    echo '<div class="error1">' . $tax_error . '</div>';
}
  if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }

$atr = 'id="AddclassForm" name="AddclassForm"';
echo form_open("classes/add_new_class", $atr);
?>  
<div class="col-md-10">
    <?php echo validation_errors('<div class="error1">', '</div>'); 
    if(!empty($error)){
        foreach($error as $err){

        echo '<div class="alert alert-danger dang">
            <strong>'.$err->field.'</strong>'.$err->message.'
        </div>';
        }
    }
    ?> 
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/class.png"> Class - Add New(Create Course Run)</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="20%" class="td_heading">Select Course:<span class="required">*</span></td>
                    <td width="80%">
                        <?php
                        $course_options = array();
                        $course_options[''] = 'Select';
                        foreach ($courses as $k => $v):
                            $course_options[$k] = $v;
                        endforeach;
                        echo form_dropdown('class_course', $course_options, $this->input->post('class_course'), 'id="class_course"');
                        ?> 
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <h2 class="sub_panel_heading_style class_display_none" style="<?php echo ($display ?? 'display:none;');?>"><img src="<?php echo base_url(); ?>/assets/images/company-detail.png"> Class Details</h2>
    <div class="bs-example class_display_none" style="<?php echo ($display ?? 'display:none;');?>">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td width="17%" class="td_heading">Class Name:</td>
                        <td width="14%">
                            <?php
                            $class_name = array(
                                'name' => 'class_name',
                                'id' => 'class_name',
                                'value' => $this->input->post('class_name'),
                                'maxlength' => 50,
                                "class" => 'upper_case'
                            );
                            echo form_input($class_name);
                            ?>
                            <span id="class_name_err"></span>
                        </td>
                        <td width="18%" class="td_heading">Start Date & Time:<span class="required">*</span></td>
                        <td width="19%">
                            <?php
                            $start_date = array(
                                'name' => 'start_date',
                                'id' => 'start_date',
                                'readonly' => 'readonly',
                                'style' => 'width:45%',
                                'placeholder' => 'dd/mm/yyyy',
                                'value' => $this->input->post('start_date'),
                            );
                            echo form_input($start_date);
                            ?>
                            <?php
                            $start_time = array(
                                'name' => 'start_time',
                                'id' => 'start_time',
                                'readonly' => 'readonly',
                                'style' => 'width:45%;float:right;',
                                'value' => $this->input->post('start_time'),
                            );
                            echo form_input($start_time);
                            ?>
                            <div>
                                <span style="max-width:45%;" id="start_date_err"></span>
                                <span id="start_time_err" style="max-width:45%;float:right;"></span>
                            </div>
                        </td>
                        <td width="14%" class="td_heading">End Date & Time:<span class="required">*</span></td>
                        <td width="18%">
                            <?php
                            $end_date = array(
                                'name' => 'end_date',
                                'id' => 'end_date',
                                'readonly' => 'readonly',
                                'style' => 'width:45%',
                                'placeholder' => 'dd/mm/yyyy',
                                'value' => $this->input->post('end_date'),
                            );
                            echo form_input($end_date);
                            ?>
                            <?php
                            $end_time = array(
                                'name' => 'end_time',
                                'id' => 'end_time',
                                'readonly' => 'readonly',
                                'style' => 'width:45%;float:right;',
                                'value' => $this->input->post('end_time'),
                            );
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
                        <td class="td_heading">Course Intake Size:<span class="required">*</span></td>
                        <td><?php
                            $total_seats = array(
                                'name' => 'total_seats',
                                'id' => 'total_seats',
                                'value' => $this->input->post('total_seats'),
                                'maxlength' => 10
                            );
                            echo form_input($total_seats);
                            ?>
                            <span id="total_seats_err"></span>
                        </td>
                        <td class="td_heading">Minimum  Students:</td>
                        <td>
                            <?php
                            $attr = array('name' => 'minimum_students', 'id' => 'minimum_students', 'maxlength' => '10');
                            echo form_input($attr, $this->input->post('minimum_students'));
                            ?>
                            <span id="minimum_students_err"></span></td>
                        <td colspan="2" class="td_heading">Click here to set <a href="#ex4" rel="modal:open" class="small_text1">Notification Frequency</a></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Classroom Hours:<span class="required">*</span></td>
                        <td>
                            <?php
                            $cls_duration = array(
                                'name' => 'cls_duration',
                                'id' => 'cls_duration',
                                'value' => $this->input->post('cls_duration'),
                                'maxlength' => 10
                            );
                            echo form_input($cls_duration);
                            ?>
                            <br>
                            <span id="cls_duration_err"></span>
                        </td>
                        <td class="td_heading">Lab Hours:</td>
                        <td>
                            <?php
                            $lab_duration = array(
                                'name' => 'lab_duration',
                                'id' => 'lab_duration',
                                'value' => $this->input->post('lab_duration'),
                                'maxlength' => 10
                            );
                            echo form_input($lab_duration);
                            ?>
                            <span id="lab_duration_err"></span>
                        </td>
                        <td class="td_heading">Assmnt. Hours:</td>
                        <td>
                            <?php
                            $class_assmnt_duration = array(
                                'name' => 'class_assmnt_duration',
                                'id' => 'class_assmnt_duration',
                                'value' => $this->input->post('class_assmnt_duration'),
                                'maxlength' => 10
                            );
                            echo form_input($class_assmnt_duration);
                            ?>
                            <span id="lab_duration_err"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Fees:<span class="required">*</span></td>
                        <td>
                            <?php
                            $fees = array(
                                'name' => 'fees',
                                'id' => 'fees',
                                'style' => 'width:40%;',
                            );
                            ?>
                            $ <?php echo form_input($fees, $this->input->post('fees')); ?> SGD
                            <span id="fees_err"></span>
                        </td>
                        <td class="td_heading">Class Discount:</td>
                        <td><?php
                            $attr = array('name' => 'class_discount', 'id' => 'class_discount', 'style' => 'width:30%');
                            echo form_input($attr, $this->input->post('class_discount'));
                            ?> %
                            <span id="class_discount_err"></span>
                        </td>
                        <td class="td_heading">Cert. Collection Date:</td>
                        <td>
                            <?php
                            $coll_date = array(
                                'name' => 'coll_date',
                                'id' => 'coll_date',
                                'placeholder' => 'dd/mm/yyyy',
                                'value' => $this->input->post('coll_date'),
                            );
                            echo form_input($coll_date);
                            ?>
                            &nbsp;</td>
                    </tr>
                   
                    <tr>
<!--                        <td class="td_heading">TPGateway Course Run ID:</td>
                        <td colspan="2">
                            <?php
//                            $tpg_course_run_id = array(
//                                'name' => 'tpg_course_run_id',
//                                'id' => 'tpg_course_run_id',
//                                'value' => $this->input->post('tpg_course_run_id'),
//                                'maxlength' => 60,
//                                'width' => '300px',
//                                "class" => 'upper_case'
//                            );
//                            echo form_input($tpg_course_run_id);
                            ?>
                            </span>
                            <br>
                            <span id="tpg_crse_err" class="tpg_crse_err"></span>
                        </td>-->
                        <td class="td_heading">Course Admin Email:<span class="required">*</span></td>
                        <td colspan='2'> <label class="label_font"></label>
                        <label class="label_font">
                            <?php
                            $crs_admin_email = array(
                                'name' => 'crs_admin_email',
                                'id' => 'crs_admin_email',
                                'value' => $this->input->post('crs_admin_email'),
                                'maxlength' => 50,
                                "class" => "upper_case",
                                "readonly" => "readonly"
                            );
                            echo form_input($crs_admin_email);
                            ?>
                        </label>
                        <span id="crs_admin_email_err"></span>
                        </td>
                        <td class="td_heading" width="20%">Course Reference Number:<span class="required">*</span></td>
                        <td colspan='2'> <label class="label_font"></label>
                        <label class="label_font">
                            <?php
                            $crse_ref_no = array(
                                'name' => 'crse_ref_no',
                                'id' => 'crse_ref_no',
                                'value' => $this->input->post('crse_ref_no'),
                                'maxlength' => 50,
                                "class" => "upper_case",
                                "readonly" => "readonly"
                            );
                            echo form_input($crse_ref_no);
                            ?>
                        </label>
                        <span id="crse_ref_no_err"></span>
                        </td>
                    </tr>
                   
                    <tr>
                        <td colspan="2" class="td_heading">  
                            <?php
                            $display_class = array(
                                'name' => 'display_class',
                                'id' => 'display_class'
                            );
                            echo form_checkbox($display_class, '1', set_checkbox('display_class', '1'));
                            ?>
                            &nbsp;Display class for public registration</td>
                        <td class="td_heading">Class Language:<span class="required">*</span></td>
                        <td>
                            <?php
                            $languages_options[''] = 'Select';
                            $languages_js = 'id="languages" maxlength="10"';
                            echo form_dropdown('languages', $languages_options, $this->input->post('languages'), $languages_js);
                            ?>
                            <span id="languages_err"></span>
                        </td>
                        
<!--                        <td class="td_heading">Sales Executive:</td>
                        <td><?php
                            $sales_exec_options[''] = 'Select';                            
                            echo form_dropdown('control_4[]', $sales_exec_options, $this->input->post('control_4'), 'id="control_4" class="control_4" style="width:78%;" multiple="multiple"');
                            ?>
                            <span id="control_4_err"></span>
                        </td>-->
                    </tr>
                    <tr>
                        <td class="td_heading">No. of sessions per day:</td>
                        <td colspan="5">
                            <?php
                            $sessions_perday1 = array(
                                'name' => 'sessions_perday',
                                'id' => 'sessions_perday',
                                'class' => 'sessions_perday',
                            );
                            $sessions_perday2 = array(
                                'name' => 'sessions_perday',
                                'id' => 'sessions_perday',
                                'class' => 'sessions_perday',
                                'checked' => TRUE,
                            );
                            echo form_radio($sessions_perday1, '1', set_radio('sessions_perday', '1'));
                            ?>
                            One Session &nbsp;&nbsp; <?php echo form_radio($sessions_perday2, '2', set_radio('sessions_perday', '2')); ?> Two Sessions</td>
                    </tr>
                    <tr>
                        <td class="td_heading red">Payment Details:<span class="required">*</span></td>
                        <td colspan="5" class="red">
                            <?php
                            $payment_details1 = array(
                                'name' => 'payment_details',
                                'id' => 'payment_details',
                                'class' => 'payment_details',                                
                            );
                            $payment_details2 = array(
                                'name' => 'payment_details',
                                'id' => 'payment_details',
                                'class' => 'payment_details',
                                'checked' => TRUE
                            );
                            echo form_radio($payment_details1, 'PDENROL', set_radio('payment_details', 'PDENROL'));
                            ?>
                            Pay During Enrollment &nbsp;&nbsp; 
                            <?php echo form_radio($payment_details2, 'PAENROL', set_radio('payment_details', 'PAENROL')); ?> Pay After Enrollment
                            <div class="payment_details_err"></div></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Classroom Venue:<span class="required">*</span></td>
                        <td colspan="5">
                            <?php
                            $cls_venue_options[''] = 'Select';
                            $cls_venue = fetch_metavalues_by_category_id(Meta_Values::LOCATION);
                            foreach ($cls_venue as $val):
                                $cls_venue_options[$val['parameter_id']] = $val['category_name'];
                            endforeach;
                            $lab_venue_options = $cls_venue_options;
                            $cls_venue_options['OTH'] = 'Others';
                            echo form_dropdown('cls_venue', $cls_venue_options, $this->input->post('cls_venue'), 'id="cls_venue" maxlength="250"');
                            ?>
                            &nbsp; &nbsp; &nbsp; &nbsp; 
                            <span class="clsven_oth_span" style="display:none;">
                                <?php
                                $classroom_venue_oth = array(
                                    'name' => 'classroom_venue_oth',
                                    'id' => 'classroom_venue_oth',
                                    'maxlength' => '250',
                                    'style' => 'width:370px;',
                                    'value' => $this->input->post('classroom_venue_oth'),
                                    "class" => "upper_case"
                                );
                                echo form_input($classroom_venue_oth);
                                ?>
                            </span>
                            <br>
                            <span id="cls_venue_err" class="classroom_venue_oth_err"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Lab Venue:</td>
                        <td colspan="5">
                            <?php
                            $lab_venue_options['OTH'] = 'Others';
                            echo form_dropdown('lab_venue', $lab_venue_options, $this->input->post('lab_venue'), 'id="lab_venue" maxlength="250"');
                            ?>
                            &nbsp; &nbsp; &nbsp; &nbsp; 
                            <span class="labven_oth_span" style="display:none;">
                                <?php
                                $lab_venue_oth = array(
                                    'name' => 'lab_venue_oth',
                                    'id' => 'lab_venue_oth',
                                    'maxlength' => '250',
                                    'style' => 'width:370px;',
                                    'value' => $this->input->post('lab_venue_oth'),
                                    "class" => "upper_case"
                                );
                                echo form_input($lab_venue_oth);
                                ?>
                            </span>
                            <br>
                            <span id="lab_venue_err" class="lab_venue_oth_err"></span>
                        </td>
                    </tr>
                    <tr>                        
                        <td class="td_heading"> Venue Building:<span class="required">*</span></td>
                        <td colspan='3'>
                            <label class="label_font">
                                <?php
                                $venue_building = array(
                                    'name' => 'venue_building',
                                    'id' => 'venue_building',
                                    'value' => $this->input->post('venue_building'),
                                    'maxlength' => 50,
                                    "class" => "upper_case"
                                );
                                echo form_input($venue_building);
                                ?>
                            </label>
                            <span id="venue_building_err"></span>
                        </td>
                         <td class="td_heading"> Survey Language:<span class="required">*</span></td>
                        <td>
                            <label class="label_font">
                                <?php
                                $survey_language = array();
                                $survey_language['']='Please Choose';
                                $survey_language['EL'] ='EL- English';
                                $survey_language['MN'] ='MN- Mandarin';
                                $survey_language['MY'] ='MY- Malay';
                                $survey_language['TM'] ='TM- Tamil';
                                echo form_dropdown('survey_language', $survey_language, $this->input->post('survey_language'), 'id="survey_language"');
                                ?>
                            </label>
                            <span id="survey_language_err"></span>
                        </td>
                    </tr>
                
                <tr>  
                    <td class="td_heading"> Venue Block:<span class="required">*</span></td>
                    <td>
                        <label class="label_font">
                            <?php
                            $venue_block = array(
                                'name' => 'venue_block',
                                'id' => 'venue_block',
                                'value' => $this->input->post('venue_block'),
                                'maxlength' => 50,
                                "class" => "upper_case"
                            );
                            echo form_input($venue_block);
                            ?>
                        </label>
                        <span id="venue_block_err"></span>
                    </td>
                    <td class="td_heading">Venue Street:<span class="required">*</span></td>
                    <td>
                        <label class="label_font">
                        <?php
                        $venue_street = array(
                            'name' => 'venue_street',
                            'id' => 'venue_street',
                            'value' => $this->input->post('venue_street'),
                            'maxlength' => 50,
                            "class" => "upper_case"
                        );
                        echo form_input($venue_street);
                        ?>
                        </label>
                         <span id="venue_street_err"></span>
                    </td>
                    <td class="td_heading"> Venue Floor:<span class="required">*</span></td>
                    <td>
                        <label class="label_font">
                            <?php
                            $venue_floor = array(
                                'name' => 'venue_floor',
                                'id' => 'venue_floor',
                                'value' => $this->input->post('venue_floor'),
                                'maxlength' => 50,
                                "class" => "upper_case"
                            );
                            echo form_input($venue_floor);
                            ?>
                        </label>
                        <span id="venue_floor_err"></span>
                    </td>
                    
                </tr>

                <tr>  
                    <td class="td_heading">Venue Unit:<span class="required">*</span></td>
                    <td>
                        <label class="label_font">
                        <?php
                        $venue_unit = array(
                            'name' => 'venue_unit',
                            'id' => 'venue_unit',
                            'value' => $this->input->post('venue_unit'),
                            'maxlength' => 50,
                            "class" => "upper_case"
                        );
                        echo form_input($venue_unit);
                        ?>
                        </label>
                         <span id="venue_unit_err"></span>
                    </td>
                    <td class="td_heading">Venue Postal Code:<span class="required">*</span></td>
                    <td>
                        <label class="label_font">
                        <?php
                        $venue_postalcode = array(
                            'name' => 'venue_postalcode',
                            'id' => 'venue_postalcode',
                            'value' => $this->input->post('venue_postalcode'),
                            'maxlength' => 50,
                            "class" => "upper_case"
                        );
                        echo form_input($venue_postalcode);
                        ?>
                        </label>
                        <span id="venue_postalcode_err"></span>
                    </td>
                    <td class="td_heading">Venue Room:<span class="required">*</span></td>
                    <td>
                        <label class="label_font">
                        <?php
                        $venue_room = array(
                            'name' => 'venue_room',
                            'id' => 'venue_room',
                            'value' => $this->input->post('venue_room'),
                            'maxlength' => 50,
                            "class" => "upper_case"
                        );
                        echo form_input($venue_room);
                        ?>
                        </label>
                         <span id="venue_room_err"></span>
                    </td>
                </tr>
                    <tr>
                        <td class="td_heading">Class Room Trainer:<span class="required">*</span></td>
                        <td><?php
                            $cls_trainer_options = array();
                            $cls_trainer_options[''] = 'Select';
                            $cls_trainer_options = $trainer;
                            foreach ($trainer as $k => $v):
                                $cls_trainer_options[$k] = $v;
                            endforeach;
                            echo form_dropdown('control_5[]', $cls_trainer_options, $this->input->post('control_5'), 'id="control_5" style="width:78%;" multiple="multiple"');
                            ?>
                            <span id="control_5_err"></span></td>
                        <td class="td_heading">Lab Trainer/ Assistant Trainer:</td>
                        <td>
                            <?php
                            $lab_trainer_options = $cls_trainer_options;
                            echo form_dropdown('control_6[]', $lab_trainer_options, $this->input->post('control_6'), 'id="control_6" style="width:78%;" multiple="multiple"');
                            ?>
                        </td>
                        <td class="td_heading">Assessor:</td>
                        <td>
                            <?php
                            $assessor_options = $cls_trainer_options;
                            echo form_dropdown('control_7[]', $assessor_options, $this->input->post('control_7'), 'id="control_7" style="width:78%;" multiple="multiple"');
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Training Aide:</td>
                        <td colspan="5">
                            <?php
                            $tra_aide_options = array();                            
                            $tra_aide_options = $course_manager;
                            echo form_dropdown('control_3[]', $tra_aide_options, $this->input->post('control_3'), 'id="control_3" style="width:78%;" multiple="multiple"');
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Description:</td>
                        <td colspan="5">
                            <?php
                            $description = array(
                                'name' => 'description',
                                'id' => 'description',
                                'value' => $this->input->post('description'),
                                'rows' => 3,
                                'cols' => 100
                            );
                            echo form_textarea($description);
                            ?>
                            <span id="description_err"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <br>
    <div class="row marketing class_display_none" style="<?php echo ($display ?? 'display:none;');?>">
        <div class="col-lg-6">
             <span id="dis-error"></span>
            <h4 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/schedule.png"> Class / Lab Schedule 
                <span class="label label-default push_right black-btn" id='sales_exec_addmore'>  
                    <a href="#ex1" rel="modal:open" class="small_text add_schld_form alert_message">
                        <span class="glyphicon glyphicon-plus glyphicon1"></span> Add Class / Lab Schedule</a>                          
                </span>  
            </h4> 
            <p>
            <div class="scroll_schedule schld_div"><div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Class Date</th>
                                <th>Session Type</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                          
                        </tbody>
                    </table>
                    
                    <div class="error error_text">No Schedule available</div>
                </div>
            </div>
            </p>
        </div>
        <div class="col-lg-6">
            <h4 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/schedule.png"> Assessment Schedule 
                <span class="label label-default push_right black-btn"><a href="#ex2" rel="modal:open" class="small_text alert_message1"><span class="glyphicon glyphicon-plus glyphicon1"></span> Add Assessment Schedule</a></span>
                &nbsp;&nbsp;</h4>
            <div class="scroll_schedule1 def_schld_div">
                <div class="table-responsive  table-scroll-x">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="">Action</th>
                                <th width="">Assmnt. Date</th>
                              
                                <th width="">Assessor</th>
                                <th width="">Assmnt. Time</th>
                                <th width="">Assmnt. Venue</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="error error_text">No Schedule available</div>
                </div>
            </div>
            </p>
        </div>
    </div>
    <span class="required required_i">* Required Fields</span>
    <div class="button_class class_display_none" style="<?php echo ($display ?? 'display:none;');?>">
        <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-saved"></span>&nbsp;Save</button> &nbsp; &nbsp; 
    </div>
</div>
<div class="modalassessment009911" id="ex1" style="display:none;height: 300px;">
    <h2 class="panel_heading_style">Add Class / Lab Schedule</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <tr>
                    <td class="td_heading">Mode Of Training:<span class="required">*</span></td>
                        <td width="20%">
                        <?php
                        $modeoftraining = array();
                        $modeoftraining[''] = 'Please Choose';
                        $modeoftraining[1] = '1-Classroom';
                        $modeoftraining[2] = '2-Asynchronous eLearning';
                        $modeoftraining[3] = '3-In-house';
                        $modeoftraining[4] = '4-On-the-Job';
                        $modeoftraining[5] = '5-Practical / Practicum';
                        $modeoftraining[6] = '6-Supervised Field';
                        $modeoftraining[7] = '7-Traineeship';
                        //$modeoftraining[8] = '8-Assessment';
                        $modeoftraining[9] = '9-Synchronous eLearning';
                        echo form_dropdown('modeoftraining', $modeoftraining, $this->input->post('modeoftraining'), 'id="modeoftraining"');
                        ?>
                        <span id="modeoftraining_err"></span>
                    </td>
                </tr>
                    <td class="td_heading">Date:<span class="required">*</span></td>
                    <td><?php
                        $schld_date = array(
                            'name' => 'schld_date',
                            'id' => 'schld_date',
                            'readonly' => 'readonly',
                            'placeholder' => 'dd/mm/yyyy',
                            'value' => $this->input->post('schld_date'),
                        );
                        echo form_input($schld_date);
                        ?>
                        &nbsp; 
                        <span id="schld_date_err"></span> </td>
                </tr>
                <tr>
                    <td class="td_heading">Session Type:<span class="required">*</span></td>
                    <td>
                        <?php
                        $schld_session_type_options = array(
                            '' => 'Select',
                            'S1' => 'Session 1',
                            'BRK' => 'Break',
                            'S2' => 'Session 2'
                        );
                        echo form_dropdown('schld_session_type', $schld_session_type_options, $this->input->post('schld_session_type'), 'id="schld_session_type" style="width:50%"');
                        ?>
                        <span id="schld_session_type_err"></span> 
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Start Time:<span class="required">*</span></td>
                    <td><?php
                        $schld_start_time = array(
                            'name' => 'schld_start_time',
                            'id' => 'schld_start_time',
                            'readonly' => 'readonly',
                            'value' => $this->input->post('schld_start_time'),
                        );
                        echo form_input($schld_start_time);
                        ?>
                        &nbsp; 
                        <span id="schld_start_time_err"></span></td>
                </tr>
                <tr>
                    <td class="td_heading">End Time:<span class="required">*</span></td>
                    <td><?php
                        $schld_end_time = array(
                            'name' => 'schld_end_time',
                            'id' => 'schld_end_time',
                            'readonly' => 'readonly',
                            'value' => $this->input->post('schld_end_time'),
                        );
                        echo form_input($schld_end_time);
                        ?>
                        &nbsp; 
                        <span id="schld_end_time_err"></span></td>
                </tr>
            </tbody>
        </table>
    </div>
    <span class="required required_i">* Required Fields</span>
    <div class="button_class"><a href="#" rel="modal:close"><button class="btn btn-primary schld_save" type="button"><span class="glyphicon glyphicon-saved"></span>&nbsp;Save</button></a> &nbsp; &nbsp; <a href="#" rel="modal:close"><button class="btn btn-primary schld_cancel" type="button"><span class="glyphicon glyphicon-remove"></span>&nbsp;Cancel/ Delete</button></a></div>
</p></div>

<div class="modalassessment" id="ex2" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Assessment Schedule</h2>
    <table class="table table-striped">
        <tbody>
            <tr>
                <td class="td_heading">Date:<span class="red">*</span></td>
                <td>
                    <?php
                    $ass_date = array(
                        'name' => 'ass_date',
                        'id' => 'ass_date',
                        'readonly' => 'readonly',
                        'placeholder' => 'dd/mm/yyyy',
                        'value' => '',
                    );
                    echo form_input($ass_date);
                    ?>
                    <span id="ass_date_err"></span>
                </td>
            </tr>
            <tr>
                <td class="td_heading">Start Time:<span class="red">*</span></td>
                <td>
                    <?php
                    $ass_start_time = array(
                        'name' => 'ass_start_time',
                        'id' => 'ass_start_time',
                        'readonly' => 'readonly',
                        'value' => $this->input->post('ass_start_time'),
                    );
                    echo form_input($ass_start_time);
                    ?>
                    <span id="ass_start_time_err"></span>
                </td>
            </tr>
            <tr>
                <td class="td_heading">End Time:<span class="red">*</span></td>
                <td>
                    <?php
                    $ass_end_time = array(
                        'name' => 'ass_end_time',
                        'id' => 'ass_end_time',
                        'readonly' => 'readonly',
                        'value' => $this->input->post('ass_end_time'),
                    );
                    echo form_input($ass_end_time);
                    ?>
                    <span id="ass_end_time_err"></span>
                </td>
            </tr>
            <tr>
                <td class="td_heading">Select Assessor:<span class="red">*</span></td>
                <td><?php
                    $cls_trainer_options = array();
                    $cls_trainer_options[''] = 'Select';
                    
                    foreach ($trainer as $k => $v):
                        $cls_trainer_options[$k] = $v;
                    endforeach;
                    echo form_dropdown('control_8[]', $cls_trainer_options, '', 'id="control_8" style="width:78%;" multiple="multiple"');
                    ?>
                    <span id="control_8_err"></span>
                </td>
            </tr>
            
            <tr>
                <td class="td_heading">Venue:<span class="red">*</span></td>
                <td style="width: 200px;">
                    <?php
                    $ass_venue_options[''] = 'Select';
                    $cls_venue = fetch_metavalues_by_category_id(Meta_Values::LOCATION);
                    foreach ($cls_venue as $val):
                        $ass_venue_options[$val['parameter_id']] = $val['category_name'];
                    endforeach;
                    
                    $ass_venue_options['OTH'] = 'Others';
                    echo form_dropdown('ass_venue', $ass_venue_options, '', 'id="ass_venue" maxlength="250" style="width:152px;"');
                    ?>
                    <span id="ass_venue_err"></span>
                    <br/>
                    
                    &nbsp; &nbsp; &nbsp; &nbsp; 
                    <span class="assven_oth_span" style="display:none;">
                        <?php
                        $ass_venue_oth = array(
                            'name' => 'ass_venue_oth',
                            'id' => 'ass_venue_oth',
                            'maxlength' => '250',
                            'style' => 'width:220px;',
                            
                            'class' => 'upper_case',
                            'maxlength'=>'250',
                            'value' => $class->ass_venue_oth,
                        );
                        echo form_input($ass_venue_oth);
                        ?>
                    </span>
                    <br>
                    <span id="ass_venue_oth_err" class="ass_venue_oth_err"></span>
                    <input type="hidden" id="ass_editid"/>
                </td>
            </tr>
        </tbody>
    </table>
    <div style="clear:both;"></div>
    <span class="required_i red">*Required Field</span>
    <br><br>
    <div class="button_class"><a href="javascript:;" rel="modal:close"><button class="btn btn-primary ass_save" type="button"><span class="glyphicon glyphicon-saved"></span>&nbsp;Save</button></a> &nbsp; &nbsp; <a href="#" rel="modal:close"><button class="btn btn-primary ass_schld_remove" type="button"><span class="glyphicon glyphicon-remove"></span>&nbsp;Cancel/ Delete</button></a></div>
</p>
</div>
<div class="modal1_5" id="ex9" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Delete</h2>
    Are you sure you want to delete this schedule?  <br>
    <div class="popup_cancel9">
        <a href="#" rel="modal:close"><button class="btn btn-primary schld_alert_yes" type="button">Yes</button></a>&nbsp;&nbsp;<a href="#" rel="modal:close"><button class="btn btn-primary schld_alert_no" type="button">No</button></a></div></p>
</div>
<div class="modalassessment00991" id="ex4" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Reminders (Days)</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">1st Reminder:</td>
                    <td>
                        <?php
                        $reminder1 = array(
                            'id' => 'reminder1',
                            'name' => 'reminder1',
                            'style' => 'width:20%',
                            'value' => $this->input->post('reminder1'),
                        );
                        echo form_input($reminder1)
                        ?> days <span id="reminder1_err"></span></td>
                </tr>
                <tr>
                    <td class="td_heading">2nd Reminder:</td>
                    <td><?php
                        $reminder2 = array(
                            'id' => 'reminder2',
                            'name' => 'reminder2',
                            'style' => 'width:20%',
                            'value' => $this->input->post('reminder2'),
                        );
                        echo form_input($reminder2);
                        ?>  days <span id="reminder2_err"></span></td>
                </tr>
                <tr>
                    <td class="td_heading">3rd Reminder:</td>
                    <td><?php
                        $reminder3 = array(
                            'id' => 'reminder3',
                            'name' => 'reminder3',
                            'style' => 'width:20%',
                            'value' => $this->input->post('reminder3'),
                        );
                        echo form_input($reminder3);
                        ?>  days <span id="reminder3_err"></span></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="button_class">
        <a href="#" rel="modal:close"><button class="btn btn-primary close_reminder_popup" type="button"><span class="glyphicon glyphicon-saved"></span>&nbsp;Save</button></a>
        &nbsp; &nbsp; <a href="#" rel="modal:close"><button class="btn btn-primary reminder_cancel" type="button"><span class="glyphicon glyphicon-remove"></span>&nbsp;Cancel/ Delete</button></a>
    </div>
</p>
</div>
<div class="modalassessment099" id="ex3" style="display:none;height:350px;">
    <p>
    <h2 class="panel_heading_style">Default Schedule</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">Date:<span class="required">*</span></td>
                    <td><?php
                        $def_date = array(
                            'name' => 'def_date',
                            'id' => 'def_date',
                            'readonly' => 'readonly',
                            'placeholder' => 'dd/mm/yyyy',
                            'value' => $this->input->post('def_date'),
                        );
                        echo form_input($def_date);
                        ?><span id="def_date_err"></span></td>
                </tr>
                <tr>
                    <td class="td_heading">Start Time:<span class="required">*</span></td>
                    <td><?php
                        $def_schld_start_time = array(
                            'name' => 'def_schld_start_time',
                            'id' => 'def_schld_start_time',
                            'readonly' => 'readonly',
                            'value' => $this->input->post('def_schld_start_time'),
                        );
                        echo form_input($def_schld_start_time);
                        ?><span id="def_schld_start_time_err"></span></td>
                </tr>
                <tr>
                    <td class="td_heading">End Time:<span class="required">*</span></td>
                    <td><?php
                        $def_schld_end_time = array(
                            'name' => 'def_schld_end_time',
                            'id' => 'def_schld_end_time',
                            'readonly' => 'readonly',
                            'value' => $this->input->post('def_schld_end_time'),
                        );
                        echo form_input($def_schld_end_time);
                        ?><span id="def_schld_end_time_err"></span></td>
                </tr>
                <tr>
                    <td class="td_heading">Select Assessor:<span class="required">*</span></td>
                    <td><?php
                        echo form_dropdown('control_9[]', $assessor_options, $this->input->post('control_9'), 'id="control_9" style="width:78%;" multiple="multiple"');
                        ?><span id="control_9_err"></span></td>
                </tr>
                <tr>
                    <td class="td_heading">Venue:<span class="required">*</span></td>
                    <td><?php
                        $def_schld_venue = $lab_venue_options;
                        echo form_dropdown('def_schld_venue', $def_schld_venue, $this->input->post('def_schld_venue'), 'id="def_schld_venue"');
                        ?><span id="def_schld_venue_err"></span>
                        <br/>
                                &nbsp; &nbsp; &nbsp; &nbsp; 
                                <span class="defven_oth_span" style="display:none;">
                                    <?php
                                    $def_venue_oth = array(
                                        'name' => 'def_venue_oth',
                                        'id' => 'def_venue_oth',
                                        'maxlength' => '250',
                                        'style' => 'width:220px;',
                                         'class' => 'upper_case',
                                        'value' => $class->def_venue_oth,
                                    );
                                    echo form_input($def_venue_oth);
                                    ?>
                                </span>
                                <br>
                                <span id="def_venue_oth_err" class="def_venue_oth_err"></span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <span class="required required_i">* Required Fields</span>
    <div class="button_class"><a href="#" rel="modal:close"><button class="btn btn-primary def_save" type="button"><span class="glyphicon glyphicon-saved"></span>&nbsp;Save</button></a> &nbsp; &nbsp; <a href="#" rel="modal:close"><button class="btn btn-primary def_schld_remove" type="button"><span class="glyphicon glyphicon-remove"></span>&nbsp;Cancel/ Delete</button></a></div>
</p>
</div>
<?php echo form_close(); ?>
<div class="modal0000" id="ex10" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Save Class</h2>
    Data has been saved successfully. 
    <div class="popup_cancel popup_cancel001">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
</p>
</div>
<div class="modal0000" id="ex11" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Alert Message</h2>
    Please set class start and end date before you can create the schedule.<br>
    <div class="popup_cancel popup_cancel001">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
</p>
</div>
<div class="modal0000 modal-al" id="ex231" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Alert Message</h2>
    Please select atleast one assessor before you can create the schedule.<br>
    <div class="popup_cancel popup_cancel001">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
</p>
</div>
<div class="modal0000" id="ex12" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Alert Message</h2>
    Assessment Schedule can be created only after the class is created and trainees are assigned to the class. <br>
    <div class="popup_cancel popup_cancel001">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
</p>
</div>
<script>  
    $(document).ready(function() {
        $tax_error = '<?php echo $tax_error_status; ?>';
        if($tax_error == 1) {            
            $("#class_course").trigger("change");
        }
        <?php if(!empty($display)){?>
        $('#class_course').val('<?php echo $this->input->post('class_course');?>').trigger('change');
        <?php } ?>
    });    
</script>
<style>
 .dang{
        padding: 20px !important;
    font-size: 14px !important;
    text-align: center;
    }   
</style>
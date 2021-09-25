<?php
$CI = & get_instance();
$CI->load->model('course_model');
$CI->load->model('class_model');
if (!empty($tax_error)) {
    echo '<div class="error1">' . $tax_error . '</div>';
}
?>
<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
    $js_class_status = '<?php echo $js_class_status; ?>';
    $js_tenant = '<?php echo TENANT_ID; ?>';
</script>
<script>
    $trainee_enroll = <?php echo json_encode($trainee_enrolled); ?>;
</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/multiselectdropdown.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/multiselectdropdown.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/multidropdown.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.ui.timepicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery.ui.timepicker.css" />

<script>
    $siteurl = '<?php echo site_url(); ?>';
    $course_duration = '<?php echo $course_duration; ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/editclass.js?0.01"></script>

<div class="col-md-10">
    <?php echo validation_errors('<div class="error1">', '</div>');
    if (!empty($error)) {
        ?>
        <div class="alert alert-danger dang" style="text-align:left;">
            <?php
            foreach ($error as $err) {
                echo 'Field Name : ' . $err->field . '</br>Message : ' . $err->message . '</br></br>';
            }
            ?>
        </div>
<?php } ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/class.png" /> Class - Edit</h2>    
    <div class="table-responsive">
        <?php
        $atr = 'id="search_form" name="search_form"';
        echo form_open("classes/edit_class", $atr);
        ?>  
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading" width="20%">Search by Course Name:<span class="required">*</span></td>
                    <td width="20%">
                        <?php
                        $course_name = array(
                            'name' => 'course_name',
                            'id' => 'course_name',
                            'value' => isset($coursename) ? $coursename : $this->input->post('course_name'),
                            'maxlength' => 250
                        );
                        echo form_input($course_name);

                        $course_id = array(
                            'name' => 'course_id',
                            'id' => 'course_id',
                            'value' => isset($course_id) ? $classid : $this->input->post('course_id'),
                            'maxlength' => 5,
                            'type' => 'hidden'
                        );
                        echo form_input($course_id);
                        ?>
                        <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
                        <span id="course_name_err"></span>
                    </td>
                    <td class="td_heading" width="20%">Class Name:<span class="required">*</span></td>
                    <td width="20%">
                        <?php
                        $class_options = array();
                        $class_js = 'id="class_id"';
                        $class_options[''] = 'Select';
                        $class_js = (!empty($classes)) ? $class_js : $class_js . ' disabled="disabled"';
                        foreach ($classes as $k => $v) {
                            $class_options[$k] = $v;
                        }
                        if (!$classid)
                            $classid = '';
                        echo form_dropdown('class_id', $class_options, $this->input->post('class_id'), $class_js, set_value('class_id', $classid));
                        ?>
                        <span id="class_id_err"></span>
                    </td>
                    <td align="right"><button title="Search" value="Search" type="submit" class="btn btn-xs btn-primary no-mar">
                            <span class="glyphicon glyphicon-search"></span>
                            Search
                        </button></td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
    <br>
    <?php
    if (!empty($class)) {
        $atr = 'id="EditClassForm" name="EditClassForm"';
        echo form_open("classes/update_class_tpg", $atr);
        ?>  
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/company-detail.png" /> Class Details</h2>
        <div class="bs-example" id="mks">
            <div class="table-responsive">
                <table class="table table-striped" >
                    <tbody>
                        <tr>
                            <td width="17%" class="td_heading">Class Name:</td>
                            <td width="14%">
                                <?php
                                $class_name = array(
                                    'name' => 'class_name',
                                    'id' => 'class_name',
                                    'value' => $class->class_name,
                                    'maxlength' => 50,
                                    "class" => "upper_case"
                                );
                                echo form_input($class_name);
                                $class_id = array(
                                    'name' => 'class_hid',
                                    'id' => 'class_hid',
                                    'value' => $class->class_id,
                                    'type' => 'hidden'
                                );
                                echo form_input($class_id);
                                $courses_id = array(
                                    'name' => 'course_id',
                                    'id' => 'course_id',
                                    'value' => $class->course_id,
                                    'maxlength' => 5,
                                    'type' => 'hidden'
                                );
                                echo form_input($courses_id);
                                ?>
                                <span id="class_name_err"></span>
                            </td>
                            <td width="18%" class="td_heading">Start Date & Time:<span class="required">*</span></td>
                            <td width="19%">
                                <?php
                                if (!empty($label['start'])) {
                                    $style = 'width:45%;display:none';
                                    $style_attr = form_label(date('d-m-Y', strtotime($class->class_start_datetime)));
                                } else {
                                    $style = 'width:45%;';
                                    $style_attr = '';
                                }
                                $start_date = array(
                                    'name' => 'start_date',
                                    'id' => 'start_date',
                                    'readonly' => 'readonly',
                                    'style' => $style,
                                    'value' => date('d-m-Y', strtotime($class->class_start_datetime)),
                                );

                                $start_date_hidden = array(
                                    'name' => 'start_date_hidden',
                                    'id' => 'start_date_hidden',
                                    'readonly' => 'readonly',
                                    'style' => "display:none",
                                    'value' => date('d-m-Y', strtotime($class->class_start_datetime)),
                                );
                                echo form_input($start_date_hidden);

                                echo form_input($start_date);
                                echo $style_attr;
                                if (!empty($label['start'])) {
                                    $style = 'width:45%;display:none';
                                    $style_attr = form_label(date('H:i', strtotime($class->class_start_datetime)));
                                } else {
                                    $style = 'width:45%;float:right;';
                                    $style_attr = '';
                                }
                                $start_time = array(
                                    'name' => 'start_time',
                                    'id' => 'start_time',
                                    'readonly' => 'readonly',
                                    'style' => $style,
                                    'value' => date('H:i', strtotime($class->class_start_datetime)),
                                );
                                echo form_input($start_time);
                                echo '&nbsp;&nbsp;' . $style_attr;
                                ?>
                                &nbsp; 
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
                                    'style' => 'width:45%;',
                                    'value' => date('d-m-Y', strtotime($class->class_end_datetime)),
                                );
                                echo form_input($end_date);
                                ?>
                                <?php
                                $end_time = array(
                                    'name' => 'end_time',
                                    'id' => 'end_time',
                                    'readonly' => 'readonly',
                                    'style' => 'width:45%;float:right;',
                                    'value' => date('H:i', strtotime($class->class_end_datetime)),
                                );
                                echo form_input($end_time);
                                ?>
                                &nbsp; 
                                <div>
                                    <span style="max-width:45%;" id="end_date_err"></span>
                                    <span id="end_time_err" style="max-width:100%;float:right;"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="td_heading">Total Seats:<span class="required">*</span></td>
                            <td><?php
                                $total_seats = array(
                                    'name' => 'total_seats',
                                    'id' => 'total_seats',
                                    'value' => $class->total_seats,
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
                                echo form_input($attr, empty($class->min_reqd_students) ? '' : $class->min_reqd_students);
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
                                    'value' => $class->total_classroom_duration,
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
                                    'value' => $class->total_lab_duration,
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
                                    'value' => $class->assmnt_duration,
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
                                if (!empty($label['fees'])) {
                                    $style = 'display:none';
                                    $style_attr = form_label("$class->class_fees");
                                } else {
                                    $style = '';
                                    $style_attr = '';
                                }
                                $fees = array(
                                    'name' => 'fees',
                                    'id' => 'fees',
                                    'style' => 'width:40%;' . $style,
                                );
                                ?>
                                $ <?php
                                echo form_input($fees, $class->class_fees);
                                echo $style_attr;
                                ?> SGD
                                <span id="fees_err"></span>
                            </td>
                            <td class="td_heading">Class Discount:</td>
                            <td><?php
                                if (!empty($label['discount'])) {
                                    $style = 'display:none';
                                    $style_attr = form_label("$class->class_discount");
                                } else {
                                    $style = '';
                                    $style_attr = '';
                                }
                                $attr = array('name' => 'class_discount', 'id' => 'class_discount', 'style' => 'width:30%;' . $style);
                                echo form_input($attr, $class->class_discount);
                                echo $style_attr;
                                ?> %
                                <span id="class_discount_err"></span>
                            </td>
                            <td class="td_heading">Cert. Collection Date:</td>
                            <td>
                                <?php
                                $coll_date = '';
                                if ($class->certi_coll_date != '0000-00-00' && $class->certi_coll_date != NULL) {
                                    $coll_date = date('d-m-Y', strtotime($class->certi_coll_date));
                                }
                                $coll_date = array(
                                    'name' => 'coll_date',
                                    'id' => 'coll_date',
                                    'placeholder' => 'dd/mm/yyyy',
                                    'value' => $coll_date,
                                );
                                echo form_input($coll_date);
                                ?>
                                &nbsp;</td>
                        </tr>                        
                        <tr>                            
                            <tr>
                                <td class="td_heading">TPGateway Course Run ID:</td>
                                <td colspan="5"><label class="label_font" id='crs_run_id'><?php echo $class->tpg_course_run_id; ?></label>
                                <input type="hidden" id="tpg_crse_run_id" name = "tpg_crse_run_id" value="<?php echo $class->tpg_course_run_id; ?>"/>
                                <input type="hidden" id="crse_ref_no" name = "crse_ref_no" value="<?php echo $reference_num; ?>"/>
                                </td>                                
                            </tr>
                            <tr>
                                <td class="td_heading">TPGateway QR-Code Link:</td>
                                <td colspan="5"><label class="label_font" id='crs_run_id'><a href='<?php echo $class->tpg_qr_code; ?>' target="_blank"><?php echo $class->tpg_qr_code; ?></a></label></td>
                            </tr>
                        </tr>
                        <tr>
                            <td class="td_heading">Course Admin Email:<span class="required">*</span></td>
                            <td colspan='5'> <label class="label_font"></label>
                                <label class="label_font">
                                    <?php
                                    $crs_admin_email = array(
                                        'name' => 'crs_admin_email',
                                        'id' => 'crs_admin_email',
                                        'value' => $crs_admin_email_val,
                                        'maxlength' => 50,
                                        "class" => "upper_case",
                                        "readonly" => "readonly"
                                    );
                                    echo form_input($crs_admin_email);
                                    ?>
                                </label>
                                <span id="crs_admin_email_err"></span>
                            </td>                            
                        </tr>                        
                        <tr>
                            <td colspan="2" class="td_heading">  
                                <?php
                                $display_class_checked = ($class->display_class_public == 1) ? true : false;
                                $display_class = array(
                                    'name' => 'display_class',
                                    'id' => 'display_class',
                                    'checked' => $display_class_checked
                                );
                                echo form_checkbox($display_class, '1');
                                ?>
                                &nbsp;Display class for public registration</td>
                            <td class="td_heading">Class Language:<span class="required">*</span></td>
                            <td colspan="3">
                                <?php
                                if (!empty($label['language'])) {
                                    $style = '';

                                    $style_attr = form_label(rtrim($CI->course_model->get_metadata_on_parameter_id($class->class_language), ', '));
                                } else {
                                    $style = '';
                                    $style_attr = '';
                                }
                                $languages_options[''] = 'Select';
                                foreach ($languages as $k => $v) {
                                    $languages_options[$k] = $v;
                                }
                                $languages_js = 'id="languages" maxlength="10" style="' . $style . '"';
                                echo form_dropdown('languages', $languages_options, $class->class_language, $languages_js);
                                //echo $style_attr;
                                ?>
                                <span id="languages_err"></span>
                            </td>
    <!--                            <td class="td_heading">Sales Executive:</td>
                            <td><?php
                            $sales_exec_options[''] = 'Select';
                            foreach ($salesexec as $k => $v) {
                                $sales_exec_options[$k] = $v;
                            }
                            $sales_exec_attr = 'id="control_4" class="control_4" style="width:78%;" multiple="multiple"';
                            echo form_dropdown('control_4[]', $sales_exec_options, explode(',', $class->sales_executive), $sales_exec_attr);
                            ?>
                                <span id="control_4_err"></span>
                            </td>-->
                        </tr>
                        <tr>
                            <td class="td_heading">No. of sessions per day:</td>
                            <td colspan="5">
                                <?php
                                if ($class->class_session_day == 1) {
                                    $checked1 = TRUE;
                                    $checked2 = FALSE;
                                } else {
                                    $checked2 = TRUE;
                                    $checked1 = FALSE;
                                }
                                $sessions_perday1 = array(
                                    'name' => 'sessions_perday',
                                    'id' => 'sessions_perday',
                                    'value' => 1,
                                    'class' => 'sessions_perday',
                                    'checked' => $checked1,
                                );
                                $sessions_perday2 = array(
                                    'name' => 'sessions_perday',
                                    'id' => 'sessions_perday',
                                    'value' => 2,
                                    'class' => 'sessions_perday',
                                    'checked' => $checked2,
                                );
                                echo form_radio($sessions_perday1);
                                ?>
                                One Session &nbsp;&nbsp; <?php echo form_radio($sessions_perday2); ?> Two Sessions</td>
                        </tr>
                        <tr>
                            <td class="td_heading red">Payment Details:<span class="required">*</span></td>
                            <td colspan="5" class="red">
                                <?php
                                if ($class->class_pymnt_enrol == 'PDENROL') {
                                    $pchecked1 = TRUE;
                                    $pchecked2 = FALSE;
                                } else {
                                    $pchecked2 = TRUE;
                                    $pchecked1 = FALSE;
                                }
                                $payment_details1 = array(
                                    'name' => 'payment_details',
                                    'id' => 'payment_details',
                                    'class' => 'payment_details',
                                    'value' => 'PDENROL',
                                    checked => $pchecked1,
                                );
                                $payment_details2 = array(
                                    'name' => 'payment_details',
                                    'id' => 'payment_details',
                                    'class' => 'payment_details',
                                    'value' => 'PAENROL',
                                    checked => $pchecked2,
                                );
                                echo form_radio($payment_details1);
                                ?>
                                Pay During Enrollment &nbsp;&nbsp; 
                                <?php echo form_radio($payment_details2); ?> Pay After Enrollment
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
                                echo form_dropdown('cls_venue', $cls_venue_options, $class->classroom_location, 'id="cls_venue" maxlength="250"');
                                ?>
                                &nbsp; &nbsp; &nbsp; &nbsp; 
                                <span class="clsven_oth_span" style="display:none;">
                                    <?php
                                    $classroom_venue_oth = array(
                                        'name' => 'classroom_venue_oth',
                                        'id' => 'classroom_venue_oth',
                                        'maxlength' => '250',
                                        'style' => 'width:370px;',
                                        'value' => $class->classroom_venue_oth,
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
                                echo form_dropdown('lab_venue', $lab_venue_options, $class->lab_location, 'id="lab_venue" maxlength="250"');
                                ?>

                                &nbsp; &nbsp; &nbsp; &nbsp; 
                                <span class="labven_oth_span" style="display:none;">
                                    <?php
                                    $lab_venue_oth = array(
                                        'name' => 'lab_venue_oth',
                                        'id' => 'lab_venue_oth',
                                        'maxlength' => '250',
                                        'style' => 'width:370px;',
                                        'value' => $class->lab_venue_oth,
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
                            <td class="td_heading">Venue Room:<span class="required">*</span></td>
                            <td colspan='3'>
                                <label class="label_font">
                                    <?php
                                    $venue_room = array(
                                        'name' => 'venue_room',
                                        'id' => 'venue_room',
                                        'value' => $class->venue_room,
                                        'maxlength' => 200,
                                        "class" => "upper_case"
                                    );
                                    echo form_input($venue_room);
                                    ?>
                                </label>
                                <span id="venue_room_err"></span>
                            </td>
                            <td class="td_heading"> Survey Language:<span class="required">*</span></td>
                            <td>
                                <label class="label_font">
                                    <?php
                                    $survey_language = array();
                                    $survey_language[''] = 'Please Choose';
                                    $survey_language['EL'] = 'EL- English';
                                    $survey_language['MN'] = 'MN- Mandarin';
                                    $survey_language['MY'] = 'MY- Malay';
                                    $survey_language['TM'] = 'TM- Tamil';
                                    echo form_dropdown('survey_language', $survey_language, $class->survey_language, 'id="survey_language"');
                                    ?>
                                </label>
                                <span id="survey_language_err"></span>
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
                                        'value' => $class->venue_unit,
                                        'maxlength' => 50,
                                        "class" => "upper_case"
                                    );
                                    echo form_input($venue_unit);
                                    ?>
                                </label>
                                <span id="venue_unit_err"></span>
                            </td>
                            <td class="td_heading"> Venue Block:</td>
                            <td>
                                <label class="label_font">
                                    <?php
                                    $venue_block = array(
                                        'name' => 'venue_block',
                                        'id' => 'venue_block',
                                        'value' => $class->venue_block,
                                        'maxlength' => 50,
                                        "class" => "upper_case"
                                    );
                                    echo form_input($venue_block);
                                    ?>
                                </label>
                                <span id="venue_block_err"></span>
                            </td>
                            <td class="td_heading"> Venue Floor:<span class="required">*</span></td>
                            <td>
                                <label class="label_font">
                                    <?php
                                    $venue_floor = array(
                                        'name' => 'venue_floor',
                                        'id' => 'venue_floor',
                                        'value' => $class->venue_floor,
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
                            <td class="td_heading">Venue Street:</td>
                            <td>
                                <label class="label_font">
                                    <?php
                                    $venue_street = array(
                                        'name' => 'venue_street',
                                        'id' => 'venue_street',
                                        'value' => $class->venue_street,
                                        'maxlength' => 50,
                                        "class" => "upper_case"
                                    );
                                    echo form_input($venue_street);
                                    ?>
                                </label>
                                <span id="venue_street_err"></span>
                            </td>
                            <td class="td_heading"> Venue Building:</td>
                            <td>
                                <label class="label_font">
                                    <?php
                                    $venue_building = array(
                                        'name' => 'venue_building',
                                        'id' => 'venue_building',
                                        'value' => $class->venue_building,
                                        'maxlength' => 50,
                                        "class" => "upper_case"
                                    );
                                    echo form_input($venue_building);
                                    ?>
                                </label>
                                <span id="venue_building_err"></span>
                            </td>
                            <td class="td_heading">Venue Postal Code:<span class="required">*</span></td>
                            <td>
                                <label class="label_font">
                                    <?php
                                    $venue_postalcode = array(
                                        'name' => 'venue_postalcode',
                                        'id' => 'venue_postalcode',
                                        'value' => $class->venue_postalcode,
                                        'maxlength' => 50,
                                        "class" => "upper_case"
                                    );
                                    echo form_input($venue_postalcode);
                                    ?>
                                </label>
                                <span id="venue_postalcode_err"></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" class="td_heading">  
                                <?php
                                $wheel_chair_accessible = array(
                                    'name' => 'wheel_chair_accessible',
                                    'id' => 'wheel_chair_accessible'
                                );
                                echo form_checkbox($wheel_chair_accessible, '1', set_checkbox('wheel_chair_accessible', '1'));
                                ?>
                                &nbsp;The course run location is wheel chair accessible</td>
                        </tr>
                        <tr>
                            <td class="td_heading">Class Room Trainer:<span class="required">*</span></td>
                            <td><?php
                                $cls_trainer_options = array();
                                $cls_trainer_options[''] = 'Select';

                                foreach ($trainer as $k => $v):
                                    $cls_trainer_options[$k] = $v;
                                endforeach;
                                echo form_dropdown('control_5[]', $cls_trainer_options, explode(',', $class->classroom_trainer), 'id="control_5" style="width:78%;" multiple="multiple"');
                                ?>
                                <span id="control_5_err"></span></td>
                            <td class="td_heading">Lab Trainer/ Assistant Trainer:</td>
                            <td>
                                <?php
                                $lab_trainer_options = $cls_trainer_options;
                                echo form_dropdown('control_6[]', $lab_trainer_options, explode(',', $class->lab_trainer), 'id="control_6" style="width:78%;" multiple="multiple"');
                                ?>
                            </td>
                            <td class="td_heading">Assessor:</td>
                            <td>
                                <?php
                                $assessor_options = $cls_trainer_options;
                                echo form_dropdown('control_7[]', $assessor_options, explode(',', $class->assessor), 'id="control_7" style="width:78%;" multiple="multiple"');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="td_heading">Training Aide:</td>
                            <td colspan="5">
                                <?php
                                $tra_aide_options = array();
                                $tra_aide_options = $course_manager;
                                echo form_dropdown('control_3[]', $tra_aide_options, explode(',', $class->training_aide), 'id="control_3" style="width:20%;" multiple="multiple"');
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
                                    'value' => $class->description,
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
        <div class="row marketing">
            <div class="col-lg-6">
                <span id="dis-error"></span>
                <h4 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/schedule.png" /> Class / Lab Schedule <span class="label label-default push_right black-btn"><a class="small_text" rel="modal:open" href="#ex1"><span class="glyphicon glyphicon-plus glyphicon1"></span> Add Class / Lab Schedule</a></span></h4> 
                <p>
                <div class="scroll_schedule schld_div">

                    <div class="table-responsive">
                        <table class="table table-striped" id="mks">
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
                                $d_cnt = 1;
                                foreach ($data_arr as $k => $v):
                                    //$x[]= $d_cnt;
                                    $rowspan = count($v);
                                    $schld_date = date('d-m-Y', strtotime($k));
                                    $rowspan_td = '<td rowspan="' . $rowspan . '">' . $schld_date . '</td>';
                                    $delete = '<td class="a_button">
                                       
                                    <a class="small_text delete_color schld_delete" href="#ex9" rel="modal:open"><input type="button" value="Delete" style="color:#000000; text-decoration:none;" /></a>
                                </td>';
                                    foreach ($v as $r) {
                                        $delete_td = ($rowspan == 1) ? $delete : '<td></td>';
                                        $start_time = date('H:i ', strtotime($r['start']));
                                        $end_time = date('H:i', strtotime($r['end']));
                                        echo '<tr class = "schld_tr' . $d_cnt . ' schlddate_' . $schld_date . '" data-session = "' . $r['session'] . '" data-date = "' . $schld_date . '"  data-count = "' . $d_cnt . '">
                                    <input type = "hidden" value = "' . $schld_date . '" name = "schlded_date[]" class = "schlded_date">
                                    <input type = "hidden" value = "' . $r['session'] . '" name = "schlded_session_type[]" class = "schlded_session_type">
                                    <input type = "hidden" value = "' . $start_time . '" name = "schlded_start_time[]" class = "schlded_start_time">
                                    <input type = "hidden" value = "' . $end_time . '" name = "schlded_end_time[]" class = "schlded_end_time">
                                    ' . $rowspan_td . '
                                    <td>' . $session_arr[$r['session']] . ' </td>
                                    <td>' . $start_time . ' </td>
                                    <td>' . $end_time . ' </td>
                                    ' . $delete_td . '
                                    </tr>';
                                        $rowspan_td = '';
                                        $d_cnt++;
                                        $rowspan--;
                                    }
                                endforeach;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                </p>
            </div>

            <div class="col-lg-6">
                <h4 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/schedule.png" /> Assessment Schedule <span class="label label-default push_right  black-btn"><a class="small_text alert_message1" rel="modal:open" href="#ex2"><span class="glyphicon glyphicon-plus glyphicon1"></span>Add Assessment Schedule</a></span></h4>
                <p>
                <div class="scroll_schedule def_schld_div">
                    <div class="table-responsive table-scroll-x">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="8%">Action</th>
                                    <th width="18%">Assmnt. Date</th>
                                    <th width="30%">Trainee Name</th>
                                    <th width="30%">Assessor</th>
                                    <th width="20%">Assmnt. Time</th>
                                    <th width="20%">Assmnt. Venue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($def_assessment)) {
                                    if ($class->assmnt_type == 'DEFAULT') {
                                        $assess_date = date('d-m-Y', strtotime($def_assessment->assmnt_date));
                                        $start_time = date('H:i', strtotime($def_assessment->assmnt_start_time));
                                        $end_date = date('H:i', strtotime($def_assessment->assmnt_end_time));
                                        ?>
                                        <tr>
                                    <input type="hidden" value="<?php echo $assess_date; ?>" name="def_schlded_date" class="def_schlded_date">
                                    <input type="hidden" value="<?php echo $start_time; ?>" name="def_schlded_start_time" class="def_schlded_start_time">
                                    <input type="hidden" value="<?php echo $end_date; ?>" name="def_schlded_end_time" class="def_schlded_end_time">
                                    <input type="hidden" value="<?php echo $def_assessment->assessor_id; ?>" name="def_schlded_assessor" class="def_schlded_assessor">
                                    <input type="hidden" value="<?php echo $def_assessment->assmnt_venue; ?>" name="def_schlded_venue" class="def_schlded_venue">
                                    <input type="hidden" value="<?php echo $def_assessment->assmnt_venue_oth; ?>" name="def_schlded_venue_oth" class="def_schlded_venue_oth">
                                    <td class="a_button">
                                        <a class="small_text" rel="modal:open" href="#ex3">
                                            <input type="button" value="E" style="color:#000000; text-decoration:none;" />
                                        </a><br>
                                        <a href="#ex9" rel="modal:open" class="small_text delete_color def_schld_delete">
                                            <input type="button" value="D" style="color:#000000; text-decoration:none;" />
                                        </a>
                                    </td>
                                    <td><?php echo $assess_date; ?></td>
                                    <td>All</td>
                                    <td><?php echo $DefAssId; ?></td>
                                    <td><?php echo $start_time . ' - ' . $end_date; ?></td>
                                    <td><?php echo $DefAssLoc; ?></td>
                                    </tr>
                                    <?php
                                } else {
                                    $cnt = 1;
                                    foreach ($def_assessment as $row) {
                                        $assess_date = date('d-m-Y', strtotime($row['assmnt_date']));
                                        $start_time = date('H:i', strtotime($row['assmnt_start_time']));
                                        $end_date = date('H:i', strtotime($row['assmnt_end_time']));
                                        ?>
                                        <tr class="ass_tr<?php echo $cnt; ?>">
                                            <?php echo array_to_input($row['trainee_id'], 'checking_trainee'); ?>
                                            <?php echo array_to_input(explode(',', $row['assessor_id']), 'checking_assessor'); ?>
                                        <input type="hidden" value="<?php echo $assess_date; ?>" name="assmnt_date[]" class="assmnt_date">
                                        <input type="hidden" value="<?php echo $start_time; ?>" name="assmnt_start_time[]" class="assmnt_start_time">
                                        <input type="hidden" value="<?php echo $end_date; ?>" name="assmnt_end_time[]" class="assmnt_end_time">
                                        <input type="hidden" value="<?php echo implode(',', $row['trainee_id']); ?>" name="assmnt_trainee[]" class="assmnt_trainee">
                                        <input type="hidden" value="<?php echo $row['assessor_id']; ?>" name="assmnt_assessor[]" class="assmnt_assessor">
                                        <input type="hidden" value="<?php echo $row['assmnt_venue']; ?>" name="ass_venue[]" class="ass_venue">

                                        <input type="hidden" value="<?php echo $row['assmnt_venue_oth']; ?>" name="ass_venue_oth[]" class="ass_venue_oth">
                                        <td class="a_button">
                                            <a class="small_text ass_edit" rel="modal:open" href="#ex2">
                                                <input type="button" data-edit="<?php echo $cnt; ?>" value="E" style="color:#000000; text-decoration:none;" />
                                            </a><br>
                                            <a href="#ex9" rel="modal:open" class="small_text delete_color ass_delete">
                                                <input type="button" data-del="<?php echo $cnt; ?>" value="D" style="color:#000000; text-decoration:none;" />
                                            </a>
                                        </td>
                                        <td><?php echo $assess_date; ?></td>
                                        <td><?php echo implode(', ', $row['trainee']); ?></td>
                                        <td><?php echo $row['DefAssId']; ?></td>
                                        <td><?php echo $start_time . ' - ' . $end_date; ?></td>
                                        <td><?php echo $row['DefAssLoc']; ?></td>
                                        </tr>
                                        <?php
                                        $cnt++;
                                    }
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                </p>
            </div>
        </div>
        <span class="required required_i">* Required Fields</span>
        <?php $deactivate_class = (!empty($label['deactivate'])) ? 'cancel_deactivate' : ''; ?>
        <div class="button_class">            
            <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-retweet"></span>&nbsp;Update</button> &nbsp; &nbsp; 
            <a href="#ex8" rel="modal:open" class="small_text <?php echo $deactivate_class; ?> check_deactivate" data-class="<?php echo $this->input->post('class_id'); ?>"><button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-remove-sign"></span>&nbsp;Delete</button></a> &nbsp; &nbsp;            
        </div>
        <div class="modalassessment009911 modal-al" id="ex1" style="display:none;height:300px;">
            <h2 class="panel_heading_style">Class / Lab Schedule</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
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
                        <tr>
                            <td class="td_heading">Date:<span class="required">*</span></td>
                            <td><?php
                                $schld_date = array(
                                    'name' => 'schld_date',
                                    'id' => 'schld_date',
                                    'readonly' => 'readonly',
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
                                echo form_dropdown('schld_session_type', $schld_session_type_options, $this->input->post('schld_session_type'), 'id = "schld_session_type" style = "width:50%"');
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
            <div style="clear:both;"></div><br>
            <span class="required_i red">*Required Field</span>

            <div class="button_class"><a href="#" rel="modal:close"><button class="btn btn-primary schld_save" type="button"><span class="glyphicon glyphicon-saved"></span>&nbsp;Save</button></a> &nbsp; &nbsp; <a href="#" rel="modal:close"><button class="btn btn-primary cls_schld_remove" type="button"><span class="glyphicon glyphicon-remove"></span>&nbsp;Cancel/ Delete</button></a></div>
            </p>
        </div>
        <div class="modalassessment00991 modal-al" id="ex4" style="display:none;">
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
                                    'value' => $class->min_reqd_noti_freq1,
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
                                    'value' => $class->min_reqd_noti_freq2,
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
                                    'value' => $class->min_reqd_noti_freq3,
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
        <div class="modalassessment099 modal-al" id="ex3" style="display:none;height:350px;">
            <h2 class="panel_heading_style">Default Schedule</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td class="td_heading">Date:<span class="required">*</span></td>
                            <td><?php
                                $def_date = !empty($def_assessment->assmnt_date) ? date('d-m-Y', strtotime($def_assessment->assmnt_date)) : '';
                                $def_date = array(
                                    'name' => 'def_date',
                                    'id' => 'def_date',
                                    'readonly' => 'readonly',
                                    'placeholder' => 'dd/mm/yyyy',
                                    'value' => $def_date,
                                );
                                echo form_input($def_date);
                                ?>
                                &nbsp; 
                                <span id="def_date_err"></span></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Start Time:<span class="required">*</span></td>
                            <td><?php
                                $def_schld_start_time = array(
                                    'name' => 'def_schld_start_time',
                                    'id' => 'def_schld_start_time',
                                    'readonly' => 'readonly',
                                    'value' => date('H:i', strtotime($def_assessment->assmnt_start_time)),
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
                                    'value' => date('H:i', strtotime($def_assessment->assmnt_end_time)),
                                );
                                echo form_input($def_schld_end_time);
                                ?><span id="def_schld_end_time_err"></span></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Select Assessor:<span class="required">*</span></td>
                            <td><?php
                                echo form_dropdown('control_9[]', $assessor_options, explode(',', $def_assessment->assessor_id), 'id = "control_9" style = "width:78%;" multiple = "multiple"');
                                ?><span id="control_9_err"></span></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Venue:<span class="required">*</span></td>
                            <td><?php
                                $def_schld_venue = $lab_venue_options;
                                echo form_dropdown('def_schld_venue', $def_schld_venue, $def_assessment->assmnt_venue, 'id = "def_schld_venue"');
                                ?><span id="def_schld_venue_err"></span>
                                <br/>

                                &nbsp; &nbsp; &nbsp; &nbsp; 

                                <span class="defven_oth_span" style="<?php echo ($def_assessment->assmnt_venue == 'OTH') ? '' : 'display:none;'; ?>">
                                    <?php
                                    $def_venue_oth = array(
                                        'name' => 'def_venue_oth',
                                        'id' => 'def_venue_oth',
                                        'maxlength' => '250',
                                        'style' => 'width:220px;',
                                        'class' => 'upper_case',
                                        'value' => $def_assessment->assmnt_venue_oth,
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
            <br>
            <div class="button_class" ><a href="#" rel="modal:close"><button class="btn btn-primary def_save" type="button"><span class="glyphicon glyphicon-saved"></span>&nbsp;Save</button></a> &nbsp; &nbsp; <a href="#" rel="modal:close"><button class="btn btn-primary def_schld_remove" type="button"><span class="glyphicon glyphicon-remove"></span>&nbsp;Cancel/ Delete</button></a></div>
            <span class="required_i red">*Required Field</span>
            </p>
        </div>

        <div class="modal1_5 modal-al" id="ex9" style="display:none;">
            <h2 class="panel_heading_style">Delete</h2>
            Are you sure you want to delete this schedule?  <br>

            <div class="popup_cancel9">
                <a href="#" rel="modal:close"><button class="btn btn-primary schld_alert_yes" type="button">Yes</button></a>&nbsp;&nbsp;<a href="#" rel="modal:close"><button class="btn btn-primary" type="button">No</button></a></div></p>
        </div>
        <div class="modal0000" id="ex10" style="display:none;">
            <p>
            <h2 class="panel_heading_style">Update Class</h2>
            Data has been updated successfully. 
            <div class="popup_cancel popup_cancel001">
                <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
            </p>
        </div>
        <div class="modal0000 modal-al" id="ex12" style="display:none;">
            <p>
            <h2 class="panel_heading_style">Alert Message</h2>
            Assessment Schedule can be created only after the class is created and trainees are assigned to the class. <br>
            <div class="popup_cancel popup_cancel001">
                <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
            </p>
        </div>
        <div class="modal0000 modal-al" id="ex21" style="display:none;">
            <p>
            <h2 class="panel_heading_style">Alert Message</h2>
            You already have default schedule set. Please remove the default schedule to be able to add a custom schedule. <br>
            <div class="popup_cancel popup_cancel001">
                <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
            </p>
        </div>
        <div class="modal0000 modal-al" id="ex22" style="display:none;">
            <p>
            <h2 class="panel_heading_style">Alert Message</h2>
            You have custom assessment schedule(s) created. Please remove them, if you want to create a default schedule.<br>
            <div class="popup_cancel popup_cancel001">
                <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
            </p>
        </div>
        <div class="modal0000 modal-al" id="ex23" style="display:none;">
            <p>
            <h2 class="panel_heading_style">Alert Message</h2>
            All trainees have been assigned to assessments.<br>
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
        <div class="modal0000 modal-al" id="ex13" style="display:none;">
            <p>
            <h2 class="panel_heading_style">Alert Message</h2>
            This class has active enrolments. Kindly re-schedule the enrolments and close the class before de-activating.
            <div class="popup_cancel popup_cancel001">
                <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
            </p>
        </div>

        <?php
        echo form_close();
    }
    ?>
</div>
<div class="modalassessment" id="ex2" style="display:none;height:420px;">
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
                    echo form_dropdown('ass_venue', $ass_venue_options, '', 'id="ass_venue" maxlength="250"');
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
                            'maxlength' => '250',
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
<?php
$form_attributes = array('name' => 'deactivate_class_form', 'id' => 'deactivate_class_form');
echo form_open("classes/deactivate_class_tpg", $form_attributes);
?>
<div class="modal1_051" id="ex8" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Delete Class</h2>
    <span class="error"><strong> This Class will be deleted from </strong>
        <?php
        $deactiv_date = array(
            'name' => 'deactivation_date',
            'id' => 'deactivation_date',
            'value' => date('d-m-Y'),
            'readonly' => TRUE,
            'style' => 'display:none;',
        );
        echo form_input($deactiv_date);
        echo form_label(date('d-m-Y'));
        ?>
    </span>
    <span id="deactivation_date_err"></span>
    <br><br>
    <strong>Reason for Deletion:<span class="red">*</span></strong>  <?php
    $d_reasons = fetch_metavalues_by_category_id(Meta_Values::CLASS_DEACTIVATE_REASONS);
    $reasons_options[''] = 'Select';
    foreach ($d_reasons as $item):
        $reasons_options[$item['parameter_id']] = $item['category_name'];
    endforeach;
    $reasons_options['OTHERS'] = 'Others';
    $attr = 'id="reason_for_deactivation"';
    echo form_dropdown('reason_for_deactivation', $reasons_options, $this->input->post('reason_for_deactivation'), $attr);
    ?> <span id="reason_for_deactivation_err"></span>
    <div id="row_dim_new1" style="float:right; margin-right:20%;display:none;">
        <?php
        $attr = array(
            'name' => 'other_reason_for_deactivation',
            'id' => 'other_reason_for_deactivation',
            'style' => 'width:200%',
            'class' => 'upper_case',
            'maxlength' => '250',
        );
        echo form_input($attr);
        echo form_hidden('class_id_deactive', $class->class_id);
        echo form_hidden('class_id_deactive', $class->class_id);
        ?>
    </div>
    <span id="other_reason_for_deactivation_err" style="float:right;clear:both;"></span>
    <br><br>
    Are you sure you want to Delete this class? <br>
    <span class="required_i red">*Required Field</span>

    <div class="popup_cancel9">
        <span rel="modal:close"><button class="btn btn-primary" type="submit">Yes</button></span>&nbsp;&nbsp;<a href="#" rel="modal:close"><button class="btn btn-primary" type="button">No</button></a></div></p>
</div>
<div class="modal0000" id="ex41" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Alert Message</h2>
    There are students enrolled in this class. To deactivate the class, please reschedule the student to other class.<br>
    <div class="popup_cancel popup_cancel001">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
</p>
</div>
<?php echo form_close(); ?>

<?php

function array_to_input($array, $prefix = '') {
    if ((bool) count(array_filter(array_keys($array), 'is_string'))) {
        foreach ($array as $key => $value) {
            if (empty($prefix)) {
                $name = $key;
            } else {
                $name = $prefix . '[' . $key . ']';
            }
            if (is_array($value)) {
                array_to_input($value, $name);
            } else {
                ?>
                <input type="hidden" value="<?php echo $value ?>" name="<?php echo $name ?>">
                <?php
            }
        }
    } else {
        foreach ($array as $item) {
            if (is_array($item)) {
                array_to_input($item, $prefix . '[]');
            } else {
                ?>
                <input type="hidden" name="<?php echo $prefix ?>[]" value="<?php echo $item ?>">
                <?php
            }
        }
    }
}
?>

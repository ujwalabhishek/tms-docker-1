<?php
$this->load->helper('form');
$this->load->helper('metavalues_helper');
$this->load->helper('common');
$CI = & get_instance();
$CI->load->model('course_model');
$CI->load->model('settings_model');
?>  
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-multiselect.css" type="text/css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap-multiselect.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap-2.3.2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.modal.js" type="text/javascript" charset="utf-8"></script>      
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/course_common.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/edit_course.js?0.00004"></script>        
<div class="col-md-10">        
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/course.png"> Course - Edit/ Deactivate</h2>        
    <div class="table-responsive">    
        <?php
        $atr = 'id="search_form" name="search_form"';
        echo form_open("course/edit_course", $atr);
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="22%" class="td_heading">Search by Course Name:</td>
                    <td colspan="3">
                        <input size="50" type="text" name="search_course_name" id="search_course_name" value="<?php echo $this->input->post('search_course_name'); ?>" class='upper_case' />
                        <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
                        <span id="search_course_name_err"></span>
                    </td>
                    <td width="13%" align="center"><button type="submit" title="Search" value="Search" onclick='validate_search();' class="btn btn-xs btn-primary no-mar">
                            <span class="glyphicon glyphicon-search"></span> Search
                        </button></td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
    <br> 
    <?php
    $form_attributes = 'id="editCourseForm" name="editCourseForm"';
    echo form_open_multipart("course/edit_course_by_tenant", $form_attributes);
    $readonly_array = ($enrol_count > 0) ? array('disabled' => 'disabled') : array('');
    ?>
    <div class="bs-example" <?php echo $form_style_attr ?> >
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/company-detail.png"> Course Details</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td class="td_heading" width="20%">Course Name:<span class="required">*</span></td>
                        <td  width="30%">                        
                            <?php
                            $course_details = array(
                                'name' => 'course_name',
                                'id' => 'course_name',
                                'value' => $course_data->crse_name,
                                'maxlength' => 100,
                                'class' => 'upper_case alphanumeric',
                                'onblur' => 'javascript:isunique_course(this.value,this.id);',
                                'style' => 'width:250px;',
                            );
                            ?>
                            <?php echo form_input($course_details); ?>
                            <span id="course_name_err"></span>
                            <?php echo form_error('course_name', '<div class="error">', '</div>'); ?>
                        </td>
                        <?php
                        $tenant_courses = $this->course->get_course_list_by_tenant($this->session->userdata('userDetails')->tenant_id);
                        unset($tenant_courses[$course_data->course_id]);
                        if (!empty($tenant_courses)) {
                            ?>
                            <td class="td_heading"  width="20%">Pre-requisite:</td>
                            <td  width="30%">
                                <?php
                                $tenant_courses_js = ($enrol_count > 0) ? 'id="pre_requisite" disabled="disabled"' : 'id="pre_requisite"';
                                echo form_multiselect('pre_requisites[]', $tenant_courses, $pre_requisite, $tenant_courses_js);
                                ?>        
                            </td>
                            <?php
                        } else {
                            echo "<td>&nbsp;</td><td>&nbsp;</td>";
                        }
                        ?>
                    </tr>
                    <tr>
                        <td class="td_heading">Language:<span class="required">*</span></td>
                        <td>
                            <select multiple="multiple" id="language" name="languages[]">
                                <?php
                                $languages = fetch_metavalues_by_category_id(Meta_Values::LANGUAGE);
                                foreach ($languages as $item) {
                                    $value = $item['parameter_id'];
                                    $label = $item['category_name'];
                                    $option = '';
                                    if (in_array($item['parameter_id'], $language)) {
                                        if (in_array($item['parameter_id'], $class_language))
                                            $option = "selected = 'selected' disabled='disabled'";
                                        else
                                            $option = "selected = 'selected'";
                                    }
                                    echo '<option value="' . $value . '" ' . $option . '>' . $label . '</option>';
                                }
                                ?> 
                            </select>
                            <span id="language_err"></span>
                            <?php echo form_error('languages[]', '<div class="error">', '</div>'); ?>
                        </td>
                        <td class="td_heading">Course Type:<span class="required">*</span></td>
                        <td>
                            <?php
                            $course_types = fetch_metavalues_by_category_id(Meta_Values::COURSE_TYPE);
                            $course_types_options[''] = 'Select';
                            foreach ($course_types as $item) {
                                $course_types_options[$item['parameter_id']] = $item['category_name'];
                            }
                            $course_types_js = ($enrol_count > 0) ? 'id="course_types" class="inp-wid" disabled="disabled"' : 'id="course_types" class="inp-wid"';
                            echo form_dropdown('course_types', $course_types_options, $course_data->crse_type, $course_types_js);
                            ?>
                            <span id="course_type_err"></span>
                            <?php echo form_error('course_types', '<div class="error">', '</div>'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Class Type:<span class="required">*</span></td>
                        <td>
                            <?php
                            $class_types = fetch_metavalues_by_category_id(Meta_Values::CLASS_TYPE);
                            $class_types_options[''] = 'Select';
                            foreach ($class_types as $item) {
                                $class_types_options[$item['parameter_id']] = $item['category_name'];
                            }
                            $class_types_js = 'id="class_types" class="inp-wid"';
                            echo form_dropdown('class_types', $class_types_options, $course_data->class_type, $class_types_js);
                            ?>
                            <span id="class_type_err"></span>
                            <?php echo form_error('class_types', '<div class="error">', '</div>'); ?>
                        </td>
                        <td class="td_heading">GST Rate:</td>
                        <td>
                            <?php
                            $gst_rates_attributes = array('class' => 'label_font');
                            $gst_rates = $CI->settings_model->get_active_gst_rates($this->session->userdata('userDetails')->tenant_id);
                            if ($gst_rates->gst_rate != false) {
                                $gst = number_format($gst_rates->gst_rate, 2);
                                echo form_label("$gst %", '', $gst_rates_attributes);
                            } else
                                echo form_label("GST-Not Defined", '', $gst_rates_attributes);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">GST Rules:<span class="required">*</span></td>
                        <td> 
                            <?php
                            $yes = FALSE;
                            $no = FALSE;
                            if ($course_data->gst_on_off == '1') {
                                $yes = TRUE;
                            } else {
                                $no = TRUE;
                            }
                            $gst_rules_yes = array(
                                'name' => 'gst_rules',
                                'value' => '1',
                                'id' => 'gst_rules_yes',
                                'checked' => $yes,
                            );
                            $gst_rules_yes_no = array(
                                'name' => 'gst_rules',
                                'id' => 'gst_rules_no',
                                'value' => '0',
                                'checked' => $no,
                            );
                            ?>              
                            <?php echo form_radio($gst_rules_yes); ?>Yes &nbsp;&nbsp; 
                            <?php echo form_radio($gst_rules_yes_no); ?> No                                                            
                            <span id="class_type_err"></span>
                        </td>
                        <td colspan='2'>
                            <?php
                            $yes = FALSE;
                            $no = FALSE;
                            if ($course_data->subsidy_after_before == 'GSTBSD') {
                                $yes = TRUE;
                            } else {
                                $no = TRUE;
                            }

                            $before_subsidy = array(
                                'name' => 'subsidy',
                                'value' => 'GSTBSD',
                                'id' => 'before_subsidy',
                                'checked' => $yes,
                            );
                            $after_subsidy = array(
                                'name' => 'subsidy',
                                'id' => 'after_subsidy',
                                'value' => 'GSTASD',
                                'checked' => $no,
                            );
                            ?>              
                            <?php echo form_radio($before_subsidy); ?>Apply GST before SUBSIDY. &nbsp;&nbsp; 
                            <?php echo form_radio($after_subsidy); ?> Apply GST after SUBSIDY.                               
                        </td>                          
                    </tr>
                    <tr>
                        <td class="td_heading">Course Duration (in hrs):</td>
                        <td>
                            <?php
                            $course_duration = array(
                                'name' => 'course_duration',
                                'id' => 'course_duration',
                                'value' => $course_data->crse_duration,
                                'maxlength' => 10,
                                'class' => 'float_number',
                                    ) + $readonly_array;
                            echo form_input($course_duration);
                            ?>
                            <span id="course_duration_err"></span>
                            <?php echo form_error('course_duration', '<div class="error">', '</div>'); ?>
                        </td>
                        <td class="td_heading">Course Reference Number:<span class="required">*</span></td>
                        <td>
                            <?php
                            $course_reference_num = array(
                                'name' => 'course_reference_num',
                                'id' => 'course_reference_num',
                                'value' => $course_data->reference_num,
                                'maxlength' => 50,
                                'class' => 'upper_case',
                                'style' => 'width:200px',
                            );
                            echo form_input($course_reference_num);
                            ?>
                            <span id="course_reference_num_err"></span>
                            <?php echo form_error('course_reference_num', '<div class="error">', '</div>'); ?>
                        </td>
                    </tr>
                    <?php if($course_data->tpg_crse) {?>
                    <tr>
                        <td class="td_heading">External Reference Number:<span class="required">*</span></td>
                        <td>
                            <?php
                            $external_reference_number = array(
                                'name' => 'external_reference_number',
                                'id' => 'external_reference_number',
                                'value' => $course_data->external_reference_number,
                                'maxlength' => 50,
                                'class' => 'upper_case',
                                'style' => 'width:200px',
                            );
                            echo form_input($external_reference_number);
                            ?>
                            <div style='color:grey'>Always Starts with "TGS-"</div>
                            <span id="external_reference_number_err"></span>
                            <?php //echo form_error('external_reference_number', '<div class="error">', '</div>'); ?>
                        </td>
                        <td class="td_heading">Course Admin Email:<span class="required">*</span></td>
                        <td>
                            <?php
                            $crse_admin_email = array(
                                'name' => 'crse_admin_email',
                                'id' => 'crse_admin_email',
                                'value' => $course_data->crse_admin_email,
                                'maxlength' => 50,
                                'class' => 'upper_case',
                                'style' => 'width:200px',
                            );
                            echo form_input($crse_admin_email);
                            ?>
                            <span id="crse_admin_email_err"></span>
                            <?php echo form_error('crse_admin_email', '<div class="error">', '</div>'); ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td class="td_heading">Course Competency Code:<span class="required">*</span></td>
                        <td>
                            <?php
                            $course_competency_code = array(
                                'name' => 'course_competency_code',
                                'id' => 'course_competency_code',
                                'value' => $course_data->competency_code,
                                'maxlength' => 50,
                                'class' => 'upper_case',
                                'style' => 'width:200px',
                            );
                            echo form_input($course_competency_code);
                            ?>
                            <span id="course_competency_code_err" ></span>
                            <?php echo form_error('course_competency_code', '<div class="error">', '</div>'); ?>
                        </td>
                        <td class="td_heading">Certification Code/ Level:<span class="required">*</span></td>
                        <td>                        
                            <?php
                            $certification_codes = fetch_metavalues_by_category_id(Meta_Values::CERTIFICATE_CODE);
                            $certification_codes_options[''] = 'Select';
                            foreach ($certification_codes as $item) {
                                $certification_codes_options[$item['parameter_id']] = $item['category_name'];
                            }
                            $certification_codes_attr = ($enrol_count > 0) ? 'id="certification_code" class="inp-wid" disabled="disabled"' : 'id="certification_code" class="inp-wid"';
                            echo form_dropdown('certification_code', $certification_codes_options, $course_data->certi_level, $certification_codes_attr);
                            ?>                            
                            <span id="certification_code_err"></span>
                            <?php echo form_error('certification_code', '<div class="error">', '</div>'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Course Manager:<span class="required">*</span></td>
                        <td colspan="3">
                            <?php
                            $course_managers = $this->course->get_tenant_users_by_role($this->session->userdata('userDetails')->tenant_id, 'CRSEMGR');
                            $course_managers_js = 'id="course_manager"';
                            echo form_multiselect('course_manager[]', $course_managers, $course_manager, $course_managers_js);
                            ?>
                            <span id="course_manager_err"></span>
                            <?php echo form_error('course_manager[]', '<div class="error">', '</div>'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Course Description:<span class="required">*</span></td>
                        <td colspan="3">
                            <?php
                            $course_description = array(
                                'name' => 'course_description',
                                'id' => 'course_description',
                                'rows' => '5',
                                'cols' => '100',
                                'value' => $course_data->description,
                            );
                            echo form_textarea($course_description);
                            ?>
                            <span id="course_description_err"></span>                    
                        </td>
                    </tr>
                    <tr>        
                        <td class="td_heading"> <?php echo $image_error; ?> Upload Course Icon:</td>

                        <td>
                            <input type="file" name="course_icon" id="userfile" onchange="showimagepreview(this)" />
                            <label id="image_err"></label>
                        </td>
                        <?php if ($course_data->crse_icon): ?> 
                            <td id="user_image_preview" class="td_heading">&nbsp;&nbsp;&nbsp;
                                <img width="150px"  src="<?php echo base_url() . $course_data->crse_icon; ?>" id="imgprvw" border="0" />                
                                <span id="removeimagebutton" onclick="remove_image();" class="glyphicon glyphicon-remove-circle"></span></td> 
                            <td colspan="2"><b>Use Previous Image:</b>&nbsp;&nbsp;&nbsp;<input type="radio" name="deleteimage" checked="checked" id="deleteimageyes" value="yes"/>Yes
                                <input type="radio" name="deleteimage" id="deleteimageno" value="no"/>No</td>
                        <?php else: ?>
                            <td id="user_image_preview" colspan="3" class="td_heading">&nbsp;&nbsp;&nbsp;&nbsp;
                                <img height="120px" width="120px" id="imgprvw" border="0" />
                                &nbsp;&nbsp;&nbsp;
                                <span style="display: none;" id="removeimagebutton" onclick="remove_image();" class="glyphicon glyphicon-remove-circle"></span></td>     
                        <?php endif; ?>    


                    </tr>
                    <?php if ($course_data->copied_from_id) { ?>
                        <tr>
                            <td class="td_heading">                            
                                <label>
                                    Remarks: 
                                </label>                            
                            </td>
                            <td>
                                Course Copied from <?php echo $this->course->course_name($course_data->copied_from_id); ?>
                            </td>
                            <td>
                                <label>
                                    Copy Reason:
                                </label>
                            </td>
                            <td>
                                <?php echo $course_data->copy_reason; ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="4">Does this course have a validity period? &nbsp;&nbsp;
                            <?php
                            $yes = FALSE;
                            $no = FALSE;
                            $display = 'display:none';
                            if ($course_data->crse_cert_validity > 0) {
                                $yes = TRUE;
                                $display = 'display:block';
                            } else {
                                $no = TRUE;
                            }
                            $course_validity_period_yes = array(
                                'name' => 'validity_period',
                                'value' => 'Yes',
                                'id' => 'validity_yes',
                                'checked' => $yes,
                            );
                            $course_validity_period_no = array(
                                'name' => 'validity_period',
                                'id' => 'validity_no',
                                'value' => 'No',
                                'checked' => $no,
                            );
                            ?>              
                            <?php echo form_radio($course_validity_period_yes); ?>Yes &nbsp;&nbsp; 
                            <?php echo form_radio($course_validity_period_no); ?> No                        
                        </td>
                    </tr>                    
                    <tr>
                        <td colspan="6">
                            <div id='show_me' style='<?php echo $display; ?>'>Please enter duration in days from certification date:                                     
                                <?php
                                $validity_array = array(
                                    'name' => 'validity',
                                    'id' => 'validity',
                                    'class' => 'number',
                                    'maxlength' => '10',
                                    'value' => $course_data->crse_cert_validity,
                                    'style' => "width:40px;",
                                );
                                echo form_input($validity_array);
                                ?> days.
                                <span id='validity_err'></span>
                            </div>
                        </td>
                    </tr>                    
                    <tr>
                        <td class="td_heading" colspan="2">
                            <?php
                            $status = FALSE;
                            if ($course_data->display_on_portal == 1) {
                                $status = TRUE;
                            }
                            $display_in_landing_page = array(
                                'name' => 'display_in_landing_page',
                                'id' => 'display_in_landing_page',
                                'value' => '1',
                                'checked' => $status,
                            );
                            echo form_checkbox($display_in_landing_page);
                            ?>&nbsp;Display Course on Landing Page
                        </td>
                        <td colspan="2" class="td_heading" align="left">
                            Upload Course Content: 
                            <span id='span_course_content' style="float:right; margin-right:22%;">
                                <?php if ($course_data->crse_content_path) { ?>
                                    <a href="#" onclick="remove_zip_file(<?php echo $course_data->course_id; ?>);">Remove Upload</a> &nbsp;&nbsp;                             
                                    <a href="<?php echo base_url() . 'course/download_course_content/?file_path=' . $course_data->crse_content_path . "&file_name=" . $course_data->crse_name; ?>">Download</a>
                                <?php } ?>
                            </span>
                            <span style="width:33%;">
                                <input type="file" name="userfile" id="zip_file" onchange="validate_course_file(this)">
                            </span>                            
                            <label id="zip_file_err"></label>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading" width="30%"> 
                            Default Sales Commission Rate :<span class="required">*</span>                            
                        </td> 
                        <td width="70%" colspan="3" >                             
                            <?php
                            $default_commission_rate = array(
                                'name' => 'default_commission_rate',
                                'id' => 'default_commission_rate',
                                'value' => $course_data->default_commission_rate,
                                'class' => 'float_number'
                            );
                            echo form_input($default_commission_rate);
                            ?> %
                            <span id="default_commission_rate_err"></span>
                            <?php echo form_error('default_commission_rate', '<div class="error">', '</div>'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="table-responsive"><br>                                
            <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/officail_details.png"> Sales Executive and Commission Rate<span class="required">*</span> 
                <span class="label label-default push_right black-btn" id='sales_exec_addmore'>                            
                    <span class="glyphicon glyphicon-plus glyphicon1"></span> Add More                            
                </span> 
            </h2>
            <table class="table table-striped" id='sales_exec_tbl'>
                <tbody>
                <label id="sales_exec_tbl_err"></label>
                <?php for ($i = 0; $i < count($sales_exec_array); $i++) { ?>
                    <tr id="sales_exec_tblrow_<?php echo $i; ?>">
                        <td class="td_heading">Sales Executive:</td>
                        <td>
                            <?php
                            $sales_executives = $this->course->get_tenant_users_by_role($this->session->userdata('userDetails')->tenant_id, 'SLEXEC');
                            $sales_executives = array('' => 'Select') + $sales_executives;
                            $sales_executives_js = 'id="sales_executives_' . $i . '"  class="disable_dropdown"';
                            echo form_dropdown('sales_executives[]', $sales_executives, $sales_exec_array[$i][2], $sales_executives_js);
                            ?>
                            <span id="sales_executives_err_<?php echo $i; ?>" ></span>
                            <?php echo form_error('sales_executives[]', '<div class="error">', '</div>'); ?>
                        </td>
                        <td class="td_heading">Commission Rate:</td>
                        <td>
                            <?php
                            $sales_exec_commission_rate = array(
                                'name' => 'sales_exec_commission_rates[]',
                                'id' => 'sales_exec_commission_rates_' . $i,
                                'value' => number_format($sales_exec_array[$i][1], 2, '.', ''),
                                'class' => 'float_number'
                            );
                            echo form_input($sales_exec_commission_rate);
                            ?> %
                            <span id="sales_exec_commission_rates_err_0"></span>                            
                            <?php echo form_error('sales_exec_commission_rates[]', '<div class="error">', '</div>'); ?>
                        </td>
                        <td>&nbsp;                              
                            <a class="btn btn-xs btn-default remove2" type="button" style="display:<?php echo (count($sales_exec_array) > 1) ? '' : 'none' ?>" onClick="remove_sales_exec_row(<?php echo $i; ?>)" name="sales_exec_remove[]" id="sales_exec_remove_btn_<?php echo $i; ?>" > 
                                <img src="<?php echo base_url() ?>/assets/images/remove-red.png" />     Remove 
                            </a> 
                        </td>
                    </tr>                    
                <?php } ?>
                </tbody>
            </table>
        </div>

        <br>
        <span class="required required_i">* Required Fields</span>
        <div class="button_class99">                    
            <button class="btn btn-primary" type="submit">
                <span class="glyphicon glyphicon-retweet" id="updatebtn"></span>&nbsp;Update
            </button>
            &nbsp; &nbsp;
            <?php if ($course_data->crse_status == 'ACTIVE'): ?>
                <a href="#ex9" class="small_text deactive">
                    <button class="btn btn-primary" type="button">
                        <span class="glyphicon glyphicon-remove-sign" id="deactivatebtn"></span>&nbsp;Deactivate
                    </button>
                </a> &nbsp;&nbsp; 
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
echo form_hidden('course_id', $course_data->course_id);
echo form_hidden('enrol_count', $enrol_count);
echo form_close();
?>
<?php
$form_attributes = array('name' => 'deactivate_course_form', 'id' => 'deactivate_course_form', "onsubmit" => "return(validate_deactivate_user());");
echo form_open("course/deactivate_course", $form_attributes);
?>    
<div class="modal1_055" id="ex9" style="display:none;">
    <h2 class="panel_heading_style">Deactivate Course</h2>  
    <div class="table-responsive" id="deacive_contents">
        <table class="table table-striped">      
            <tbody>
                <tr>
                    <td class="td_heading" width="30%"> De-Activation Date:<span class="red">*</span> </td>
                    <td> 
                        <label id="deactivation_date" class='error'> </label>
                        <span id="deactivation_date_err"></span>
                    </td>
                </tr>            
                <tr>
                    <td class="td_heading">Reason for De-Activation:<span class="red">*</span></td>
                    <td> 
                        <?php
                        $d_reasons = fetch_metavalues_by_category_id(Meta_Values::COURSE_DEACTIVATE_REASONS);
                        $reasons_option[''] = 'Select';
                        foreach ($d_reasons as $items):
                            $reasons_option[$items['parameter_id']] = $items['category_name'];
                        endforeach;
                        $reasons_option['OTHERS'] = 'Others';
                        $attr = 'id="reason_for_deactivation"';
                        echo form_dropdown('reason_for_deactivation', $reasons_option, $this->input->post('reason_for_deactivation'), $attr);
                        ?> &nbsp; 
                        <span id="reason_for_deactivation_err"></span>
                        <div id="other_reason" style="display:none;padding-top:10px">
                            <?php
                            $attr = array(
                                'name' => 'other_reason_for_deactivation',
                                'id' => 'other_reason_for_deactivation',
                                'size' => 27,
                            );
                            echo form_input($attr);
                            echo form_input(array('name' => 'course_id_deactive', 'type' => 'hidden', 'id' => 'course_id_deactive', 'value' => $course_data->course_id));
                            ?>  
                            <span id="other_reason_for_deactivation_err"></span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        Are you sure you want to deactivate this course? 
        <br>
        <span class="required_i red">*Required Field</span>
        <div class="popup_cancel9">
            <div rel="modal:close">
                <button class="btn btn-primary" type="submit">Save</button>&nbsp;&nbsp;
                <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a>
            </div>
        </div>
    </div>                                
</div>
<?php
echo form_close();
?>
<script>
    $("#reason_for_deactivation").change(function () {
        var reason_for_deactivation = $("#reason_for_deactivation").val();
        if (reason_for_deactivation == 'OTHERS') {
            $("#other_reason").show();
        }
        else
        {
            $("#other_reason").hide();
        }
    });

</script>

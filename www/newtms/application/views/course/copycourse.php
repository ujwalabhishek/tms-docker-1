<?php
$this->load->helper('form');
$this->load->helper('metavalues_helper');
$this->load->helper('common');

$CI = & get_instance();
$CI->load->model('course_model');
   
$CI->load->model('settings_model');
?>        
<div class="col-md-10">
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/course.png"> Course - Copy</h2>        
    <div class="table-responsive">
        <?php
        $atr = 'id="search_form" name="search_form"';
        echo form_open("course/copy_course", $atr);
        ?>    
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">Search by Course Name:</td>
                    <td>
                        <input size="50" type="text" name="copy_course_name" id="copy_course_name" value="<?php echo $this->input->post('copy_course_name'); ?>" class='upper_case'>
                        <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
                        <span id="copy_course_name_err"></span>
                    </td>      
                    <td align="center">
                        <button type="submit" title="Search" value="Search" name="search_form_btn" id="search_form_btn" class="btn btn-xs btn-primary no-mar" >
                            <span class="glyphicon glyphicon-search"></span> Search
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>    
    </div>
    <br>
    <div class="bs-example" <?php echo $form_style_attr ?> >
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/company-detail.png"> Course Details</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td class="td_heading" width="28%">Course Name:</td>
                        <td><label class="label_font"><?php echo $course_data->crse_name; ?></label></td>
                        <td class="td_heading">Pre-requisite:</td>
                        <td>
                            <label class="label_font">
                                <?php echo rtrim($CI->course_model->get_pre_requisite($course_data->pre_requisite), ', '); ?>
                            </label>
                        </td>
                    </tr>       
                    <tr>
                        <td class="td_heading">Language:</td>
                        <td>
                            <label class="label_font">
                                <?php echo rtrim($CI->course_model->get_metadata_on_parameter_id($course_data->language), ', '); ?>
                            </label>
                        </td>
                        <td class="td_heading">Course Type:</td>
                        <td>
                            <label class="label_font">
                                <?php echo rtrim($CI->course_model->get_metadata_on_parameter_id($course_data->crse_type), ', '); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Class Type:</td>
                        <td>
                            <label class="label_font">
                                <?php echo rtrim($CI->course_model->get_metadata_on_parameter_id($course_data->class_type), ', '); ?>
                            </label>
                        </td>
                        <td class="td_heading">GST Rate:</td>
                        <td>
                            <label class="label_font">
                                <?php
                                $gst_rates_attributes = array('class' => 'label_font');
                                $gst_rates = $CI->settings_model->get_active_gst_rates($this->session->userdata('userDetails')->tenant_id);
                                if ($gst_rates->gst_rate != false)
                                    echo form_label(number_format($gst_rates->gst_rate, 2, '.', '') . "%", '', $gst_rates_attributes);
                                else
                                    echo form_label("GST-Not Defined", '', $gst_rates_attributes);
                                ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">GST Rules:<span class="required">*</span></td>
                        <td> 
                            <?php
                            $gst_rules_yes = array(
                                'name' => 'gst_rules',
                                'value' => '1',
                                'id' => 'gst_rules_yes',
                                'checked' => TRUE,
                                'disabled' => "disabled",
                            );
                            $gst_rules_yes_no = array(
                                'name' => 'gst_rules',
                                'id' => 'gst_rules_no',
                                'value' => '0',
                                'disabled' => "disabled",
                            );
                            ?>              
                            <?php echo form_radio($gst_rules_yes); ?>Yes &nbsp;&nbsp; 
                            <?php echo form_radio($gst_rules_yes_no); ?> No                                                            
                            <span id="class_type_err"></span>
                        </td>
                        <td colspan='2'>
                            <?php
                            $before_subsidy = array(
                                'name' => 'subsidy',
                                'value' => 'GSTBSD',
                                'id' => 'before_subsidy',
                                'checked' => TRUE,
                                'disabled' => "disabled",
                            );
                            $after_subsidy = array(
                                'name' => 'subsidy',
                                'id' => 'after_subsidy',
                                'value' => 'GSTASD',
                                'disabled' => "disabled",
                            );
                            ?>              
                            <?php echo form_radio($before_subsidy); ?>Apply GST before SUBSIDY. &nbsp;&nbsp; 
                            <?php echo form_radio($after_subsidy); ?> Apply GST after SUBSIDY.                               
                        </td>                          
                    </tr>
                    <tr>
                        <td class="td_heading">Course Duration (in hrs):</td>
                        <td><label class="label_font"><?php echo $course_data->crse_duration; ?></label></td>
                        <td class="td_heading">Course Reference Number:</td>
                        <td><label class="label_font"><?php echo $course_data->reference_num; ?></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Course Competency Code:</td>
                        <td><label class="label_font"><?php echo $course_data->competency_code; ?></label></td>
                        <td class="td_heading">Certification Code/ Level:</td>
                        <td>
                            <label class="label_font">
                                <?php echo rtrim($CI->course_model->get_metadata_on_parameter_id($course_data->certi_level), ', '); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Course Manager:</td>
                        <td colspan="3">
                            <label class="label_font">
                                <?php echo rtrim($CI->course_model->get_managers($course_data->crse_manager), ', '); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Course Description:</td>
                        <td colspan="3">
                            <div class="table-responsive payment_scroll" style="height: 87px;min-height: 87px;">
                                <label class="label_font">
                                    <?php echo $course_data->description; ?>
                                </label>
                            </div>
                        </td>
                    </tr>        
                    <tr>
                        <td colspan='4'>Does this course have a validity period?   
                                <input name="1" type='radio' <?php if ($course_data->crse_cert_validity != 0) { ?> checked <?php } ?> disabled="disabled" />Yes &nbsp;&nbsp;
                                <input name="1" type='radio' <?php if ($course_data->crse_cert_validity == 0) { ?> checked <?php } ?> disabled="disabled" />No &nbsp;&nbsp; 
                                <?php
                                if ($course_data->crse_cert_validity != 0) {
                                    echo $course_data->crse_cert_validity . ' days from date of certification';
                                }
                                ?> 
                        </td>          
                    </tr> 
                    <?php if($course_data->copied_from_id) { ?>
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
                        <td colspan="4" class="td_heading">
                            <input type="checkbox" disabled  	<?php if ($course_data->display_on_portal == 1) { ?> checked <?php } ?>>
                            &nbsp;Display course on landing page
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-responsive"><br>
            <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/officail_details.png"> Sales Executive and Commission Rate</h2>
            <table class="table table-striped">
                <tbody>
                    <?php
                    foreach ($sales_exec_array as $sales_exec):
                        ?>
                        <tr>
                            <td class="td_heading">Sales Executive:</td>
                            <td><label class="label_font"><?php echo $sales_exec[0]; ?></label>
                            </td>
                            <td class="td_heading">Commission Rate:</td>
                            <td><label class="label_font"><?php echo number_format($sales_exec[1], 2, '.', ''); ?>%</label></td>          
                        </tr>
                    <?php endforeach; ?>        
                </tbody>
            </table>
        </div>
        <div class="button_class99">
            <a href="#ex9" rel="modal:open" class="small_text">
                <button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-file"></span>&nbsp;Copy</button>
            </a>
        </div>
    </div>

    <?php
    $form_attributes = array('name' => 'copy_course', 'id' => 'copy_course', "onsubmit" => "return(validate_copy_course());");
    echo form_open("course/duplicate_course", $form_attributes);
    ?>
    <div class="modal1_055" id="ex9" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Copy</h2>
        <div class="table-responsive">
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <td class="td_heading">Course Name:<span class="required">*</span></td>
                        <td>
                             <input type="text" value="" maxlength='100' id='couse_name_copy' name='couse_name' class='upper_case alphanumeric' 
                                   onblur = 'javascript:isunique_course(this.value, this.id);' /><br/>
                            <span id="couse_name_copy_err"></span>
                        </td>
                    </tr>            
                    <tr>
                        <td class="td_heading">Reason for Copy:<span class="required">*</span></td>
                        <td>
                            <?php
                            $d_reasons = fetch_metavalues_by_category_id(Meta_Values::COPY_REASONS);
                            $reasons_options[''] = 'Select';
                            foreach ($d_reasons as $item):
                                $reasons_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;
                            $reasons_options['OTHERS'] = 'Others';
                            $attr = 'id="reason_copy_course"';
                            echo form_dropdown('reason_copy_course', $reasons_options, $this->input->post('reason_copy_course'), $attr);
                            ?> &nbsp; 
                            <span id="reason_copy_course_err"></span>
                            <div id="other_reason" style="float:right; display:none; width: 40%;">
                                <?php
                                $attr = array(
                                    'name' => 'other_reason_copy_course',
                                    'id' => 'other_reason_copy_course',
                                    'size' => 50,
                                    'style' => 'width:100%',
                                     'class' => 'upper_case',
                                    'maxlength' => 500
                                );
                                echo form_input($attr);
                                ?>  
                                <span id="other_reason_copy_course_err"></span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <span class="required_i red">Course Name should be unique</span><br>
        <span class="required required_i">* Required Fields</span>

        <br>
        <div class="popup_cancel9">
            <div rel="modal:close">
                <!--Modified by shubhranshu on 3/1/2018-->
                <button class="btn btn-primary" type="submit">Create Copy Class</button>&nbsp;&nbsp;
                <!--<a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button>-->
                </a>
            </div>
        </div>
        </p>
    </div>
    <?php
    echo form_hidden('course_id', $course_data->course_id);
    echo form_close();
    ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/copycourse.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/course_common.js"></script>
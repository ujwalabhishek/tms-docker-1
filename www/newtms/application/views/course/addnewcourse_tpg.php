<?php
$this->load->helper('form');
$CI = & get_instance();
$CI->load->model('settings_model');
?>
<div class="col-md-10">
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/course.png"> Course - Add New(TPG)</h2>    
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/company-detail.png"> Course Details</h2>
    
    <div class="table-responsive">
        <?php echo validation_errors('<div class="error1">', '</div>'); ?> 
        <?php
        $form_attributes = array('name' => 'trainee_edit_search', 'id' => 'trainee_edit_search', "onsubmit" => "return(validate_search());");
        echo form_open("trainee/edit_trainee", $form_attributes);
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    
                    <td width="30%" class="td_heading">
                        Search by Trainee Name:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        
                        <?php     
                            $status = TRUE;
                            $disabled = '';
                            if($this->input->post('search_radio') == 'tax_radio'){
                                $status = FALSE;
                                $disabled ='disabled';
                            }
                            $name_radio = array(
                                'name' => 'search_radio',
                                'id' => 'name_radio',                            
                                'value' => 'name_radio',
                                'checked' => $status,
                                'class' => 'search',                                
                            );                       
                            echo form_radio($name_radio); 
                        ?>                    
                    </td>                    
                    <td colspan="4">
                        <?php
                        $un = array(
                            'name' => 'trainee_name',
                            'maxlength' => '250',
                            'value' => $this->input->post('trainee_name'),
                            'id' => 'search_by_name_autocomplete',
                            'style' => 'width:400px;',
                            $disabled => $disabled
                        );                    
                        echo form_input($un); 
                        
                        $user_id = ($this->input->post('user_id'))? $this->input->post('user_id') :$this->input->post('userid');
                        echo form_hidden('user_id',$user_id,'user_id');
                        ?>
                        <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
                        <span id="search_by_name_autocomplete_err"></span>
                    </td>                    
                </tr>
                <tr>
                    <td width="22%" class="td_heading">
                        Search by Trainee NRIC/FIN No.:&nbsp;&nbsp;
                        <?php $tax_radio = array(
                                   'name' => 'search_radio',
                                   'id' => 'tax_radio',                            
                                   'value' => 'tax_radio',
                                   'checked' => ($this->input->post('search_radio')=='tax_radio')?TRUE:FALSE,
                                   'class' => 'search'
                               );                 
                        echo form_radio($tax_radio); 
                        ?>
                    </td>                     
                    <td colspan="3" width="65%">
                        <?php
                        $tax_disabled = 'disabled';
                        if($this->input->post('search_radio') =='tax_radio') {
                            $tax_disabled = '';
                        }
                        $un = array(
                            'name' => 'taxcode',
                            'maxlength' => '250',
                            'value' => $this->input->post('taxcode'),
                            'id' => 'tax_code',
                            'style' => 'width:400px;',
                            $tax_disabled => $tax_disabled
                        );
                        echo form_input($un);
                        ?>
                        <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
                        <span id="search_by_taxcode_autocomplete_err"></span>
                    </td> 
                     <td width="18%" align="center"><button type="submit" title="Search" value="Search" class="btn btn-xs btn-primary no-mar"><span class="glyphicon glyphicon-search"></span> Search</button></td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>  
    </div>
    <br>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    <?php
    $form_attributes = 'id="addNewCourseForm" name="addNewCourseForm" onsubmit="return(validate());"';
    echo form_open_multipart("course/create_new_course_by_tenant", $form_attributes);
    ?>
    <div class="bs-example">
        <div class="table-responsive">
            <table class="table table-striped">      
                <tbody>                    
                    <tr>
                        <td class="td_heading" width="30%">Course Name:<span class="required">*</span></td>
                        <?php
                        $course_details = array(
                            'name' => 'course_name',
                            'id' => 'course_name',
                            'value' => set_value('course_name'),
                            'maxlength' => 100,
                            'class' => 'upper_case alphanumeric',
                            'onblur' => 'javascript:isunique_course(this.value,this.id);',
                            'style' => 'width:250px;',
                        );
                        ?>
                        <td>
                            <?php echo form_input($course_details); ?>
                            <span id="course_name_err"></span>
                            <?php echo form_error('course_name', '<div class="error">', '</div>'); ?>
                        </td>
                        <?php
                        $tenant_courses = $this->course->get_course_list_by_tenant($this->session->userdata('userDetails')->tenant_id);
                        if (!empty($tenant_courses)) {
                            ?>
                            <td class="td_heading">Pre-requisite:</td>
                            <td>
                                <?php
                                $tenant_courses_js = 'id="pre_requisite"';
                                echo form_multiselect('pre_requisites[]', $tenant_courses, array('', ''), $tenant_courses_js);
                                ?>                                      
                            </td>
                            <?php
                        } else {
                            echo '<td>&nbsp;</td><td>&nbsp;</td>';
                        }
                        ?>                          
                    </tr>
                    <tr>
                        <td class="td_heading">Language:<span class="required">*</span></td>
                        <td>
                            <?php
                            $languages = fetch_metavalues_by_category_id(Meta_Values::LANGUAGE);
                            foreach ($languages as $item) {
                                $languages_options[$item['parameter_id']] = $item['category_name'];
                            }
                            $languages_js = 'id="language"';
                            echo form_multiselect('languages[]', $languages_options, '', $languages_js);
                            ?>                            
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
                            $course_types_js = 'id="course_types"';
                            echo form_dropdown('course_types', $course_types_options, '', $course_types_js);
                            ?>
                            <span id="course_type_err"></span>
                            <?php echo form_error('course_types', '<div class="error">', '</div>'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Class Type:<span class="required">*</span></td>
                        <td>
                            <div class="form-drop">
                                <?php
                                $class_types = fetch_metavalues_by_category_id(Meta_Values::CLASS_TYPE);
                                $class_types_options[''] = 'Select';
                                foreach ($class_types as $item) {
                                    $class_types_options[$item['parameter_id']] = $item['category_name'];
                                }
                                $class_types_js = 'id="class_types"';
                                echo form_dropdown('class_types', $class_types_options, 'CLASSLAB', $class_types_js);
                                ?>
                                <span id="class_type_err"></span>
                                <?php echo form_error('class_types', '<div class="error">', '</div>'); ?>
                            </div>
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
                            $gst_rules_yes = array(
                                'name' => 'gst_rules',
                                'value' => '1',
                                'id' => 'gst_rules_yes',
                                'checked' => TRUE,
                            );
                            $gst_rules_yes_no = array(
                                'name' => 'gst_rules',
                                'id' => 'gst_rules_no',
                                'value' => '0',
                            );
                            ?>              
                            <?php echo form_radio($gst_rules_yes); ?>Yes &nbsp;&nbsp; 
                            <?php echo form_radio($gst_rules_yes_no); ?> No                                                            
                            <span id="class_type_err"></span>
                            <?php echo form_error('gst_rules', '<div class="error">', '</div>'); ?>
                        </td>
                        <td colspan='2'>
                            <?php
                            $before_subsidy = array(
                                'name' => 'subsidy',
                                'value' => 'GSTBSD',
                                'id' => 'before_subsidy',
                                'checked' => TRUE,
                            );
                            $after_subsidy = array(
                                'name' => 'subsidy',
                                'id' => 'after_subsidy',
                                'value' => 'GSTASD',
                            );
                            ?>              
                            <?php echo form_radio($before_subsidy); ?>Apply GST before SUBSIDY. &nbsp;&nbsp; 
                            <?php echo form_radio($after_subsidy); ?> Apply GST after SUBSIDY.   
                            <?php echo form_error('subsidy', '<div class="error">', '</div>'); ?>
                        </td>                          
                    </tr>                                                
                    <tr>
                        <td class="td_heading">Course Duration (in hrs):<span class="required">*</span></td>
                        <td>
                            <?php
                            $course_duration = array(
                                'name' => 'course_duration',
                                'id' => 'course_duration',
                                'value' => set_value('course_duration'),
                                'maxlength' => 10,
                                'class' => "float_number"
                            );
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
                                'value' => set_value('course_reference_num'),
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
                    <tr>
                        <td class="td_heading">External Reference Number:<span class="required">*</span></td>
                        <td>
                            <?php
                            $external_reference_number = array(
                                'name' => 'external_reference_number',
                                'id' => 'external_reference_number',
                                'value' => set_value('external_reference_number'),
                                'maxlength' => 50,
                                'class' => 'upper_case',
                                'style' => 'width:200px',
                            );
                            echo form_input($external_reference_number);
                            ?>
                            <div style='color:grey'>Always Starts with "TGS-"</div>
                            <span id="external_reference_number_err"></span>
                            <?php echo form_error('external_reference_number', '<div class="error">', '</div>'); ?>
                        </td>
                        <td class="td_heading">Course Admin Email:<span class="required">*</span></td>
                        <td>
                            <?php
                            $crse_admin_email = array(
                                'name' => 'crse_admin_email',
                                'id' => 'crse_admin_email',
                                'value' => set_value('crse_admin_email'),
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
                    <tr>
                        <td class="td_heading">Course Competency Code:<span class="required">*</span></td>
                        <td>
                            <?php
                            $course_competency_code = array(
                                'name' => 'course_competency_code',
                                'id' => 'course_competency_code',
                                'value' => set_value('course_competency_code'),
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
                            <div class="form-drop">
                                <?php
                                $certification_codes = fetch_metavalues_by_category_id(Meta_Values::CERTIFICATE_CODE);
                                $certification_codes_options[''] = 'Select';
                                foreach ($certification_codes as $item) {
                                    $certification_codes_options[$item['parameter_id']] = $item['category_name'];
                                }
                                $certification_codes_attr = 'id="certification_code"';
                                echo form_dropdown('certification_code', $certification_codes_options, '', $certification_codes_attr);
                                ?>                                
                                <span id="certification_code_err"></span>
                                <?php echo form_error('certification_code', '<div class="error">', '</div>'); ?>
                            </div>
                        </td>
                    </tr>        
                    <tr>
                        <td class="td_heading">Course Manager:<span class="required">*</span></td>
                        <td colspan="3">              
                            <?php
                            $course_managers = $this->course->get_tenant_users_by_role($this->session->userdata('userDetails')->tenant_id, 'CRSEMGR');
                            $course_managers_js = 'id="course_manager"';
                            echo form_multiselect('course_manager[]', $course_managers, '', $course_managers_js);
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
                                'value' => '',
                            );
                            echo form_textarea($course_description);
                            ?>
                            <span id="course_description_err"></span>                                                                                 
                        </td>                    
                    </tr>
                    <tr>        
                        <td class="td_heading">Upload Course Icon:</td>
                        <td>
                            <input type="file" name="course_icon" id="userfile" onchange="showimagepreview(this)" />
                            <label id="image_err"></label>
                        </td>
                        <td id="user_image_preview" colspan="3" class="td_heading">&nbsp;&nbsp;&nbsp;
                            <img height="120px" width="120px" id="imgprvw" border="0" />
                            &nbsp;&nbsp;&nbsp;
                            <span style="display: none;" id="removeimagebutton" onclick="remove_image();" class="glyphicon glyphicon-remove-circle"></span>
                        </td>     
                    </tr>
                    <tr>
                        <td colspan="4">Does this course have a validity period? &nbsp;&nbsp;
                            <?php
                            $course_validity_period_yes = array(
                                'name' => 'validity_period',
                                'value' => 'Yes',
                                'id' => 'validity_yes',
                            );
                            $course_validity_period_no = array(
                                'name' => 'validity_period',
                                'id' => 'validity_no',
                                'value' => 'No',
                                'checked' => TRUE,
                            );
                            ?>              
                            <?php echo form_radio($course_validity_period_yes); ?>Yes &nbsp;&nbsp; 
                            <?php echo form_radio($course_validity_period_no); ?> No          
                        </td>                    
                    </tr>        
                    <tr>         
                    </tr>        
                    <tr>
                        <td colspan="6">
                            <div id='show_me' style='display:none'>Please enter duration in days from certification date:                                     
                                <input type="text" value="" name='validity' id='validity' class="number" style="width:40px;" maxlength='10' > days.
                                <span id='validity_err'></span>
                            </div>
                        </td>                    
                    </tr>
                    <tr>
                        <td class="td_heading" width="20%" colspan="2">
                            <?php
                            $display_in_landing_page = array(
                                'name' => 'display_in_landing_page',
                                'id' => 'display_in_landing_page',
                                'value' => '1',
                                'checked' => TRUE,
                            );
                            echo form_checkbox($display_in_landing_page);
                            ?>&nbsp;Display Course on Landing Page
                        </td>                            
                        <td class="td_heading" align="right" width="20%"> 
                            Upload Course Content:                                
                        </td>  
                        <td width="60%" colspan="1" > 
                            <span>
                                <input type="file" name="userfile" id="zip_file" onchange="validate_course_file(this)">
                            </span>                            
                            <label id="zip_file_err"></label>
                            <span id='remove_upload_span' style="display:none">
                                <a href="#" onclick="remove_zip_file();">Remove Upload</a>
                            </span></td>
                    </tr>         
                    <tr>
                        <td class="td_heading"> 
                            Default Sales Commission Rate :<span class="required">*</span>                            
                        </td> 
                        <td colspan="1" >                             
                            <?php
                            $default_commission_rate = array(
                                'name' => 'default_commission_rate',
                                'id' => 'default_commission_rate',
                                'value' => '',
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
        <br>
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/officail_details.png"> Sales Executive and Commission Rate<span class="required">*</span> 
            <span class="label label-default push_right black-btn" id='sales_exec_addmore'>                            
                <span class="glyphicon glyphicon-plus glyphicon1"></span> Add More                            
            </span> 
        </h2>  
        <div class="table-responsive">  
            <table class="table table-striped" id="sales_exec_tbl">      
                <tbody>
                <label id="sales_exec_tbl_err"></label>  
                <tr id="sales_exec_tblrow_0">                          
                    <td class="td_heading">Sales Executive:<span class="required">*</span></td>                          
                    <td>
                        <?php
                        $sales_executives = $this->course->get_tenant_users_by_role($this->session->userdata('userDetails')->tenant_id, 'SLEXEC');
                        $sales_executives = array('' => 'Select') + $sales_executives;
                        $sales_executives_js = 'id="sales_executives_0" class="disable_dropdown"';
                        echo form_dropdown('sales_executives[]', $sales_executives, '', $sales_executives_js);
                        ?>
                        <span id="sales_executives_err_0"></span>
                        <?php echo form_error('sales_executives[]', '<div class="error">', '</div>'); ?>
                    </td>
                    <td class="td_heading">Commission Rate:<span class="required">*</span></td>
                    <td>                              
                        <?php
                        $sales_exec_commission_rate = array(
                            'name' => 'sales_exec_commission_rates[]',
                            'id' => 'sales_exec_commission_rates_0',
                            'value' => '',
                            'class' => 'float_number'
                        );
                        echo form_input($sales_exec_commission_rate);
                        ?> %
                        <span id="sales_exec_commission_rates_err_0"></span>
                        <?php echo form_error('sales_exec_commission_rates[]', '<div class="error">', '</div>'); ?>
                    </td>
                    <td>&nbsp; <a class="btn btn-xs btn-default remove2" type="button" style="display:none" onClick="remove_sales_exec_row(0)" name="sales_exec_remove[]" id="sales_exec_remove_btn_0" > 
                            <img src="<?php echo base_url() ?>/assets/images/remove-red.png" />     Remove 
                        </a> 
                    </td>          
                </tr>        
                </tbody>
            </table>
        </div>
        <br>
        <span class="required required_i">* Required Fields</span>	
        <div class="button_class99">                
            <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-saved"></span>&nbsp;Save</button> &nbsp; &nbsp;                
        </div>    
    </div>    
</div>
<?php echo form_close(); ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-multiselect.css" type="text/css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap-2.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap-multiselect.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/add_new_course.js?0.00001"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/course_common.js"></script>
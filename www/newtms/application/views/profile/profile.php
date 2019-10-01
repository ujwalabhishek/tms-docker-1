<?php
$this->load->helper('form');
$this->load->helper('metavalues_helper');
$this->load->helper('common');
$this->load->model('meta_values');
?>
<div class="col-md-10">
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-user"></span> My Profile 
        <span class="label label-default pull-right white-btn"><a href="<?php echo site_url() . 'profile/change_password'; ?>"><span class="glyphicon glyphicon-retweet"></span> Change Password</a></span>
    </h2>
    <div class="table-responsive">
        <?php
        echo validation_errors('<div class="error1">', '</div>');
        if ($this->session->flashdata('success_message')) {
            echo '<div class="success">' . $this->session->flashdata('success_message') . '</div>';
        }
        if ($this->session->flashdata('error_message')) {
            echo '<div class="error1">' . $this->session->flashdata('error_message') . '</div>';
        }
        $atr = 'id="edit_user_form" name="edit_user_form" onsubmit="return(validate());"' . $form_style_attr;
        echo form_open_multipart("profile/update_profile", $atr);
        ?>
        <input type="hidden" name="edit_user_id" id="edit_user_id" value="<?php echo $this->session->userdata('userDetails')->user_id; ?>" />
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/personal_details.png"/> Personal Details</h2>
        <div class="table-responsive">
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <td width="20%" class="td_heading">Country of Residence:<span class="required">*</span></td>
                        <td width="20%">        
                            <?php
                            $countries = fetch_metavalues_by_category_id(Meta_Values::COUNTRIES);
                            foreach ($countries as $item):
                                $country_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;

                            $attr = 'id="country_of_residence" style="width:200px"';
                            echo form_dropdown('country_of_residence', $country_options, $user_list_values->country_of_residence, $attr);
                            ?>
                            <span id="country_of_residence_err"></span>
                        </td>
                        <td colspan="2" class="td_heading">
                                <?php
                                $attr = array('name' => 'PAN', 'id' => 'PAN', 'onblur' => 'javascript:isunique_pan(this.value,this.id);', 'class' => 'upper_case alphanumeric');
                                ?>
                                <span id="PAN_err"></span>
                            <SPAN id="SGP" style="">NRIC Type: <span class="required">* </span>
                                <?php
                                $nrics = fetch_metavalues_by_category_id(Meta_Values::NRIC);
                                $nris_options[''] = 'Select';
                                foreach ($nrics as $item):
                                    $nris_options[$item['parameter_id']] = $item['category_name'];
                                endforeach;
                                $attr = 'id="NRIC" style="width:150px"';
                                echo form_dropdown('NRIC', $nris_options, $tax_code_type, $attr);
                                ?>
                                <span id="NRIC_err"></span>
                            </SPAN> 
                            <SPAN id="SGP_OTHERS" style="display:none;">
                                <br /><br />
                                <label id="SGP_OTHERS_label">NRIC Code: </label><span class="required">* </span>                  
                                <?php
                                $nric_other = fetch_metavalues_by_category_id(Meta_Values::NRIC_OTHER);
                                $nric_other_options[''] = 'Select';
                                foreach ($nric_other as $item):
                                    $nric_other_options[$item['parameter_id']] = $item['category_name'];
                                endforeach;

                                $attr = 'id="NRIC_OTHER" style="width:150px"';
                                echo form_dropdown('NRIC_OTHER', $nric_other_options, $other_identi_type, $attr);
                                ?>
                                <span id="NRIC_OTHER_err"></span>
                            </SPAN>
                            
                            <SPAN id="SGP_ID" style="display:none;">
                                <br /><br />
                                <label id="SGP_ID_label"> NRIC Code : </label><span class="required">* </span>
                                <?php
                                $attr = array('name' => 'NRIC_ID', 'id' => 'NRIC_ID', 'onblur' => 'javascript:isunique_pan(this.value,this.id);', 'class' => 'upper_case alphanumeric');
                                echo form_input($attr, $nric_number);
                                ?>
                                <span id="NRIC_ID_err"></span>
                            </SPAN>
                                <?php
                                $attr = array('name' => 'SSN', 'id' => 'SSN', 'onblur' => 'javascript:isunique_pan(this.value,this.id);', 'class' => 'upper_case alphanumeric');
                                ?>    
                                <span id="SSN_err"></span>
                        </td>                            
                    </tr>        
                    <tr>
                        <td class="td_heading">Name:<span class="required">*</span></td>
                        <td width="20%" colspan="3">
                            <?php
                            $attr = array(
                                'name' => 'pers_first_name',
                                'id' => 'pers_first_name',
                                'maxlength' => '50',
                                'value' => $user_list_values->first_name,
                                'class' => 'upper_case',
                                'style' => 'width:200px',
                            );
                            echo form_input($attr);
                            ?>  
                            <span id="pers_first_name_err"></span>
                        </td>                        
                    </tr>        
                    <tr>
                        <td class="td_heading">Gender:<span class="required">*</span></td>
                        <td>
                            <?php
                            $gender = fetch_metavalues_by_category_id(Meta_Values::GENDER);
                            $gender_options = array();
                            $gender_options[''] = 'Select';
                            foreach ($gender as $item):
                                $gender_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;
                            echo form_dropdown('pers_gender', $gender_options, $user_list_values->gender, 'id="pers_gender"');
                            ?> 
                            <span id="pers_gender_err"></span>
                        </td>
                        <td class="td_heading">Date of Birth:</td>
                        <td>
                            <?php
                            $pers_dob = array(
                                'name' => 'pers_dob',
                                'id' => 'pers_dob',
                                'value' => ($user_list_values->dob == '0000-00-00')?'':formated_date($user_list_values->dob, '-'),
                                'readonly' => 'readonly',
                            );
                            echo form_input($pers_dob);
                            ?> 
                            <span id="pers_dob_err"></span>
                        </td>
                    </tr>          
                    <tr>          
                        <td class="td_heading">Contact # (P):<span class="required">*</span></td>
                        <td>
                            <?php
                            $attr = array(
                                'name' => 'pers_contact_number',
                                'id' => 'pers_contact_number',
                                'maxlength' => '50',
                                'value' => $user_list_values->contact_number,
                                'class' => 'number',
                                'style' => 'width:200px'
                            );
                            echo form_input($attr);
                            ?> 
                            <span id="pers_contact_number_err"></span>
                        </td>
                        <td class="td_heading">Contact # (M):</td>
                        <td>
                            <?php
                            $attr = array(
                                'name' => 'pers_alternate_contact_number',
                                'id' => 'pers_alternate_contact_number',
                                'maxlength' => '50',
                                'value' => $user_list_values->alternate_contact_number,
                                'class' => 'number',
                                'style' => 'width:200px'
                            );
                            echo form_input($attr);
                            ?>    
                            <span id="pers_alternate_contact_number_err"></span> 
                        </td>
                    </tr>        
                    <tr>          
                        <td class="td_heading">Email Id(P):<span class="required">*</span></td>
                        <td>
                            <?php
                            $attr = array(
                                'name' => 'user_registered_email',
                                'id' => 'user_registered_email',
                                'maxlength' => '50',
                                'value' => $user_list_values->registered_email_id,
                                'onblur' => 'javascript:isunique_email(this.value,this.id);',
                                'style' => 'width:200px'
                            );
                            echo form_input($attr);
                            ?>
                            <span id="user_registered_email_err"></span>
                        </td>
                        <td class="td_heading">Alternate Email Id(P):</td>
                        <td>
                            <?php
                            $attr = array(
                                'name' => 'pers_alternate_email',
                                'id' => 'pers_alternate_email',
                                'maxlength' => '50',
                                'value' => $user_list_values->alternate_email_id,
                                'onblur' => 'javascript:validate_alternate_email(this.value,this.id);',
                                'style' => 'width:200px'
                            );
                            echo form_input($attr);
                            ?>
                            <span id="pers_alternate_email_err"></span>
                        </td>
                    </tr>
<!--                    <tr>
                        <td class="td_heading">Highest Education:<span class="required">*</span></td>
                        <?php
                        $highest_educ_level = fetch_metavalues_by_category_id(Meta_Values::HIGHEST_EDUC_LEVEL);
                        $highest_educ_level_options[''] = 'Select';
                        foreach ($highest_educ_level as $item):
                            $highest_educ_level_options[$item['parameter_id']] = $item['category_name'];
                        endforeach;
                        ?>
                        <td>
                            <?php echo form_dropdown('highest_educ_level', $highest_educ_level_options, $user_list_values->highest_educ_level, 'id="highest_educ_level" width:200px'); ?>
                            <span id="highest_educ_level_err"></span>
                        </td>
                        <td class="td_heading">Salary Range:<span class="required">*</span></td>
                        <?php
                        $sal_range = fetch_metavalues_by_category_id(Meta_Values::SAL_RANGE);
                        $sal_range_options[''] = 'Select';
                        foreach ($sal_range as $item):
                            $sal_range_options[$item['parameter_id']] = $item['category_name'];
                        endforeach;
                        ?>
                        <td>
                            <?php echo form_dropdown('sal_range', $sal_range_options, $user_list_values->salary_range, 'id="sal_range"'); ?>
                            <span id="sal_range_err"></span>
                        </td>
                    </tr>-->
                    
                       <tr>
                   
                    <td class="td_heading">Salary Range:<span class="required">*</span></td>
                    <?php
                    $sal_range = fetch_metavalues_by_category_id(Meta_Values::SAL_RANGE);
                    $sal_range_options[''] = 'Select';
                    foreach ($sal_range as $item):
                        $sal_range_options[$item['parameter_id']] = $item['category_name'];
                    endforeach;
                    ?>
                    <td colspan="4">
                        <?php echo form_dropdown('sal_range', $sal_range_options, $user_list_values->salary_range, 'id="sal_range"'); ?>
                        <span id="sal_range_err"></span>
                    </td>
                </tr>
                <tr>
                     <td class="td_heading">Highest Education:<span class="required">*</span></td>
                    <?php
                    $highest_educ_level = fetch_metavalues_by_category_id(Meta_Values::HIGHEST_EDUC_LEVEL);
                    $highest_educ_level_options[''] = 'Select';
                    foreach ($highest_educ_level as $item):
                        $highest_educ_level_options[$item['parameter_id']] = $item['category_name'];
                    endforeach;
                    ?>
                    <td colspan="4">
                        <?php echo form_dropdown('highest_educ_level', $highest_educ_level_options, $user_list_values->highest_educ_level, 'id="highest_educ_level" style="width:900px"'); ?>
                        <span id="highest_educ_level_err"></span>
                    </td>
                </tr>
                    
                    <tr>        
                        <td class="td_heading"> <?php echo $image_error; ?> Upload Image:</td>

                        <td><input type="file" name="userfile" id="userfile" onchange="showimagepreview(this)" /><label id="image_err"></label></td>
                        <?php if ($user_list_values->photo_upload_path): ?> 
                            <td id="user_image_preview" class="td_heading">&nbsp;&nbsp;&nbsp;
                                <img width="150px"  src="<?php echo base_url() . $user_list_values->photo_upload_path; ?>" id="imgprvw" border="0" />                
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
                    <tr>
                        <td class="td_heading">Personal Address:</td>
                        <td colspan="4" class="textarea_inp">
                            <?php
                            $attr = array(
                                'name' => 'pers_personal_address_bldg',
                                'id' => 'pers_personal_address_bldg',
                                'rows' => '2',
                                'cols' => '90',
                                'maxlength' => '255',
                                'value' => $user_list_values->personal_address_bldg,
                                'class' => 'upper_case',
                            );
                            echo form_textarea($attr);
                            ?>          
                        </td>            
                    </tr>
                    <tr>
                        <td class="td_heading">City:</td>
                        <td>
                            <?php
                            $attr = array(
                                'name' => 'pers_city',
                                'id' => 'pers_city',
                                'maxlength' => '50',
                                'value' => $user_list_values->personal_address_city,
                                'class' => 'upper_case alphabets',
                                'style' => 'width:200px'
                            );
                            echo form_input($attr);
                            ?>
                        </td>
                        <td class="td_heading">Country:</td>
                        <td colspan="2">
                            <?php
                            $attr = 'id="pers_country"';
                            echo form_dropdown('pers_country', $country_options, $user_list_values->personal_address_country, $attr);
                            echo form_hidden('current_pers_country', $user_list_values->personal_address_country, 'current_pers_country');
                            ?>
                            <span id="pers_country_err"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">State:</td>
                        <td>                           
                            <?php
                            $states = ($user_list_values->personal_address_country) ? $this->internaluser->get_states($user_list_values->personal_address_country) : 'Select';
                            $state_options[''] = 'Select';
                            foreach ($states as $item) {
                                $state_options[$item->parameter_id] = $item->category_name;
                            }
                            $attr_js = 'id="pers_states"';
                            echo form_dropdown('pers_states', $state_options, $user_list_values->personal_address_state, $attr_js);
                            echo form_hidden('current_pers_states', $user_list_values->personal_address_state, 'current_pers_states');
                            ?>                        
                        </td>
                        <td class="td_heading">Zip Code:</td>
                        <td colspan="2">
                            <?php
                            $attr = array(
                                'name' => 'pers_zip',
                                'id' => 'pers_zip',
                                'maxlength' => '10',
                                'value' => $user_list_values->personal_address_zip,
                                'class' => 'alphanumeric upper_case'
                            );
                            echo form_input($attr);
                            ?>          
                        </td>
                    </tr>        
                </tbody>
            </table>
        </div>
        <br>
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/officail_details.png"/> Official Details</h2>
        <div class="table-responsive">
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <td class="td_heading" width="16%">Company Name:<span class="required">*</span></td>
                        <td width="29%">
                            <?php
                            $attr = array(
                                'name' => 'emp_company_name',
                                'id' => 'emp_company_name',
                                'maxlength' => '50',
                                'value' => $user_list_values->company_name,
                                'class' => 'upper_case',
                                'style' => 'width:200px'
                            );
                            echo form_input($attr);
                            ?> 
                            <span id="emp_company_name_err"></span>
                        </td>
                        <td class="td_heading" width="20%" >Date of Joining:<span class="required">*</span></td>
                        <td>
                            <?php
                            $emp_doj = array(
                                'name' => 'emp_doj',
                                'id' => 'emp_doj',
                                'value' => formated_date($user_list_values->doj, '-'),
                                'readonly' => 'readonly',
                            );
                            echo form_input($emp_doj);
                            ?> 
                            <span id="emp_doj_err"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Designation:<span class="required">*</span></td>
                        <td>

                            <?php                       
                            $occupation = fetch_metavalues_by_category_id(Meta_Values::OCCUPATION);
                            $occupation_options[''] = 'Select';
                            foreach ($occupation as $item):
                                $occupation_options[$item['parameter_id']] = $item['category_name'];
                            endforeach; 
                            $occupation_options['OTHERS'] = 'OTHERS';
                            ?>
                            <?php echo form_dropdown('emp_designation', $occupation_options, $user_list_values->designation, 'id="emp_designation" style="width:170px"'); ?>
                            <span id="emp_designation_err"></span>                    
                        </td>
                        <?php $display = ($user_list_values->designation == 'OTHERS')?'block':'none'; ?>
                        <td class="occupation_others" colspan="2" style="display:<?php echo $display;?>">
                            <?php 
                                $attr = array(
                                'name' => 'emp_designation_others',
                                'id' => 'emp_designation_others',
                                'maxlength' => '50',
                                'value' => $user_list_values->designation_others,
                                'class' => 'upper_case',
                                'style' => 'width:250px',
                            );
                            echo form_input($attr);
                            ?>
                            <span id="emp_designation_others_err"></span> 
                        </td>                    
                    </tr>
                    <tr>                        
                        <td class="td_heading">Email Id(O):<span class="required">*</span></td>
                        <td>
                            <?php
                            $attr = array(
                                'name' => 'emp_email',
                                'id' => 'emp_email',
                                'maxlength' => '50',
                                'value' => $user_list_values->off_email_id,
                                'onblur' => 'javascript:validate_office_email(this.value,this.id);',
                                'disabled' =>true,
                                'style' => 'width:250px',
                            );
                            echo form_input($attr);
                            ?> 
                            <span id="emp_email_err"></span>
                        </td>
                        <td class="td_heading">Contact Number(O):</td>
                        <td>
                            <?php
                            $attr = array(
                                'name' => 'emp_contact_number',
                                'id' => 'emp_contact_number',
                                'maxlength' => '50',
                                'value' => $user_list_values->off_contact_number,
                                'class' => 'number',
                                'style' => 'width:250px',
                            );
                            echo form_input($attr);
                            ?>  
                            <span id="emp_contact_number_err"></span>
                        </td>
                    </tr>                          
                    <tr>
                        <td class="td_heading">Office Address:</td>
                        <td colspan="3" class="textarea_inp"><?php
                            $attr = array(
                                'name' => 'emp_address',
                                'rows' => '2',
                                'cols' => '90',
                                'maxlength' => '255',
                                'value' => $user_list_values->off_address_bldg,
                                'class' => 'upper_case'
                            );
                            echo form_textarea($attr);
                            ?> </td>
                    </tr>
                    <tr>
                        <td class="td_heading">City:</td>
                        <td>
                            <?php
                            $attr = array(
                                'name' => 'emp_city',
                                'id' => 'emp_city',
                                'maxlength' => '50',
                                'value' => $user_list_values->off_address_city,
                                'class' => 'upper_case alphabets',
                                'style' => 'width:250px',
                            );
                            echo form_input($attr);
                            ?>          
                        </td>
                        <td class="td_heading">Country:</td>
                        <td>
                            <?php
                            $attr_js = 'id="emp_country"';
                            echo form_dropdown('emp_country', $country_options, $user_list_values->off_address_country, $attr_js);
                            echo form_hidden('current_emp_country', $user_list_values->off_address_country, 'current_emp_country');
                            ?>          
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">State:</td>
                        <td>
                            <?php
                            $states = ($user_list_values->off_address_country) ? $this->internaluser->get_states($user_list_values->off_address_country) : 'Select';
                            $state_options[''] = 'Select';
                            foreach ($states as $item) {
                                $state_options[$item->parameter_id] = $item->category_name;
                            }
                            $attr_js = 'id="emp_states"';
                            echo form_dropdown('emp_states', $state_options, $user_list_values->off_address_state, $attr_js);
                            echo form_hidden('current_emp_states', $user_list_values->off_address_state, 'current_emp_states');
                            ?>                  
                        </td>
                        <td class="td_heading">Zip Code:</td>
                        <td>
                            <?php
                            $attr = array(
                                'name' => 'emp_zip',
                                'id' => 'emp_zip',
                                'maxlength' => '10',
                                'value' => $user_list_values->off_address_zip,
                                'class' => 'alphanumeric upper_case'
                            );
                            echo form_input($attr);
                            ?>            
                        </td>
                    </tr>           
                </tbody>
            </table>
        </div>
        <br>
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/other_details.png"/>  Other Details</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td class="td_heading" width="16%">User Role:<span class="required">*</span></td>
                        <td width="29%">
                            <?php
                            echo $user_list_values->role_name;
                            ?>              
                            <span id="user_role_err"></span>
                        </td>
                        <td class="td_heading" width="20%" >Username:<span class="required">*</span></td>
                        <td>
                            <label>
                                <?php
                                echo $user_list_values->user_name;
                                ?>
                            </label>            
                        </td>
                    </tr>
                    <?php if ($user_list_values->account_status == 'PENDACT' || $user_list_values->account_status == 'ACTIVE') { ?>        
                        <tr>          
                            <td class="td_heading">User Status</td>
                            <td colspan="4">
                                <?php
                                if ($user_list_values->account_status == 'ACTIVE') {
                                    echo "<label style='color:green'>ACTIVE</label>";
                                }
                                if ($user_list_values->account_status == 'PENDACT') {
                                    $PENDACT = true;
                                    $attr = array(
                                        'name' => 'activate_user',
                                        'checked' => false,
                                        'value' => 'ACTIVE'
                                    );
                                    echo form_radio($attr);
                                    echo ' Active &nbsp;&nbsp;';

                                    $attr = array(
                                        'name' => 'activate_user',
                                        'checked' => true,
                                        'value' => 'PENDACT'
                                    );
                                    echo form_radio($attr);
                                    echo 'Activate Later/Pending Activation';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>        
                    <tr>
                        <td colspan="4">
                            <?php if ($user_list_values->account_status != "ACTIVE" && $user_list_values->account_status != "INACTIV") { ?>
                                <i>(On activation, mail will be sent to the user’s official email Id, with the username.)</i>
                            <?php } ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <span class="required required_i">* Required Fields</span>
        </div>		  
        <div class="button_class99">
            <button class="btn btn-primary" type="submit"  value="Update">
                <span class="glyphicon glyphicon-saved"></span>&nbsp;Update
            </button> &nbsp; &nbsp;             
            <a href="<?php echo base_url(); ?>"><button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-remove-sign"></span>&nbsp;Cancel</button></a>
        </div>
        <?php echo form_close(); ?>
    </div>    
    <script type="text/javascript">
        function validate_search() {
            fname = $("#search_user_firstname").val();
            if (fname == "") {
                alert("Firstname required.");
                return false;
            }
            return true;
        }
    </script>
    <script type="text/javascript">
        edituser_country_of_residence = '<?php echo $user_list_values->country_of_residence; ?>';
        tax_code_type = '<?php echo $user_list_values->tax_code_type; ?>';
        tax_code_type = (tax_code_type == 'SNG_3') ? 'OTHERS' : tax_code_type;
        $("#" + edituser_country_of_residence).show();
        $("#" + edituser_country_of_residence + "_ID").show();
        $("#SGP_" + tax_code_type).show();
        other_identi_type = '<?php echo $other_identi_type; ?>'; 
        if(other_identi_type == 'NOTAXCODE') {
            $('#SGP_ID').hide();
        }
        $("#country_of_residence").change(function() {
            country_of_residence = $('#country_of_residence').val();
            if (country_of_residence == "") {
                $("#country_of_residence > option").each(function() {
                    if (this.value != "")
                        $("#" + this.value).hide();
                });
            }
            $("#country_of_residence > option").each(function() {
                if (this.value == country_of_residence) {
                    $("#" + country_of_residence).show();
                }
                else {
                    if (this.value != "") {
                        $("#" + this.value).hide();
                        $("#" + this.value + "_OTHERS").hide();
                        $("#" + this.value + "_ID").hide();
                        remove_all_errors();
                    }
                }
            });
            if (country_of_residence == "IND") {
                $("#PAN").show();
                $("#NRIC").hide();
                $("#SSN").hide();
                $("#SGP_ID").hide();
                $("#SGP_OTHERS").hide();
                $('#SSN_err').text('').removeClass('error');
                $('#SSN').removeClass('error');
                remove_nric_errors();
            }
            if (country_of_residence == "SGP") {
                $("#NRIC").show();
                $('#NRIC option:first-child').attr("selected", "selected");
                $("#PAN").hide();
                $("#SSN").hide();
                remove_ind_usa_errors();
            }
            if (country_of_residence == "USA") {
                $("#SSN").show();
                $("#PAN").hide();
                $("#NRIC").hide();
                $("#SGP_ID").hide();
                $("#SGP_OTHERS").hide();
                $('#PAN_err').text('').removeClass('error');
                $('#PAN').removeClass('error');
                remove_nric_errors();
            }
        });
        function remove_nric_errors() {
            $('#NRIC_err').text('').removeClass('error');
            $('#NRIC').removeClass('error');
            $('#NRIC_OTHER_err').text('').removeClass('error');
            $('#NRIC_OTHER').removeClass('error');
            $('#NRIC_ID_err').text('').removeClass('error');
            $('#NRIC_ID').removeClass('error');
        }
        function remove_ind_usa_errors() {
            $('#PAN_err').text('').removeClass('error');
            $('#PAN').removeClass('error');
            $('#SSN_err').text('').removeClass('error');
            $('#SSN').removeClass('error');
        }
        function remove_all_errors() {
            $('#NRIC_err').text('').removeClass('error');
            $('#NRIC').removeClass('error');
            $('#NRIC_OTHER_err').text('').removeClass('error');
            $('#NRIC_OTHER').removeClass('error');
            $('#NRIC_ID_err').text('').removeClass('error');
            $('#NRIC_ID').removeClass('error');
            $('#PAN_err').text('').removeClass('error');
            $('#PAN').removeClass('error');
            $('#SSN_err').text('').removeClass('error');
            $('#SSN').removeClass('error');
        }

        $("#NRIC").change(function() {
            if (this.value == "") {
                $("#SGP_ID").hide();
                $("#SGP_OTHERS").hide();
            } else if (this.value == "SNG_3") {
                $("#SGP_OTHERS").show();
                $('#SGP_OTHERS option:first-child').attr("selected", "selected");
                $('#SGP_OTHERS_label').text('');
                $('#SGP_OTHERS_label').text('OTHERS :');
                $('#SGP_ID_label').text('');
                $('#SGP_ID_label').text('OTHERS :');
            } else {
                $('#NRIC_OTHER_err').text('').removeClass('error');
                $('#NRIC_OTHER').removeClass('error');
                $('#SGP_OTHERS_label').text('');
                $('#SGP_OTHERS_label').text('NRIC :');
                $('#SGP_ID_label').text('');
                $('#SGP_ID_label').text('NRIC Code :');
                $("#SGP_OTHERS").hide();
                $("#SGP_ID").show();
            }
            $('#NRIC_ID').val('');
        });
        $("#NRIC_OTHER").change(function() {
            if (this.value == "" || this.value == "NOTAXCODE") {
                $("#SGP_ID").hide();
            } else {
                $("#SGP_ID").show();
            }
        });
        $(function() {
            var d = new Date();
            var currentYear = d.getFullYear();
            var currenyMonth = d.getMonth();
            var CurrentDate = d.getDay();
            var startYear = currentYear - 110;
            var endYear = currentYear - 10;
            $(function() {
                $("#pers_dob").datepicker({
                    dateFormat: 'dd/mm/yy',
                    minDate: new Date(startYear, currenyMonth, CurrentDate),
                    maxDate: new Date(endYear, currenyMonth, CurrentDate),
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '-110:+0'
                });
            });
            $(function() {
                $("#emp_doj").datepicker({
                    dateFormat: 'dd/mm/yy',
                    minDate: new Date(startYear, currenyMonth, CurrentDate),
                    maxDate: 0,
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '-100:+0'
                });
            });
        });

        $('#emp_designation').change(function() {
           var  $selected_val = $('#emp_designation').val();           
           if($selected_val == 'OTHERS') {               
               $('.occupation_others').show();
            } else {
               $('.occupation_others').hide();
           }
        });
        $('#pers_country').change(function() {
            var country_param = $(this).val();
            if (country_param) {
                $.post('<?php echo site_url(); ?>profile/get_states_json', {country_param: country_param}, function(data) {
                    json_data = $.parseJSON(data);
                    $pers_states_select = $("#pers_states");
                    $pers_states_select.html('<option value="">Select</option>');
                    $.each(json_data, function(i, item) {
                        $pers_states_select.append('<option value="' + item.parameter_id + '">' + item.category_name + '</option>');
                    });
                });
            } else {
                $('#pers_states').find('option').remove().end().append('<option value="">Select</option>');
            }
        });
        $('#emp_country').change(function() {
            var country_param = $(this).val();
            if (country_param) {
                $.post('<?php echo site_url(); ?>profile/get_states_json', {country_param: country_param}, function(data) {
                    json_data = $.parseJSON(data);
                    $emp_states_select = $("#emp_states");
                    $emp_states_select.html('<option value="">Select</option>');
                    $.each(json_data, function(i, item) {
                        $emp_states_select.append('<option value="' + item.parameter_id + '">' + item.category_name + '</option>');
                    });
                });
            } else {
                $('#emp_states').find('option').remove().end().append('<option value="">Select</option>');
            }
        });
        function validate() {
            var retVal = true;
            var country_of_residence = $("#country_of_residence").val();
            if (country_of_residence == "") {
                $("#country_of_residence_err").text("[required]").addClass('error');
                $("#country_of_residence").addClass('error');
                retVal = false;
            } else {
                $("#country_of_residence_err").text("").removeClass('error');
                $("#country_of_residence").removeClass('error');
            }
            if (country_of_residence == "IND") {
                PAN = $("#PAN").val();
                var pan_error_text = $("#PAN_err").text();
                if (PAN == "") {
                    $("#PAN_err").text("[required]").addClass('error');
                    $("#PAN").addClass('error');
                    retVal = false;
                } else if (pan_error_text != '[code exists!]') {
                    $("#PAN_err").text("").removeClass('error');
                    $("#PAN").removeClass('error');
                }
            }
            if (country_of_residence == "SGP") {
                NRIC = $("#NRIC").val();
                var nric_error_text = $("#NRIC_ID_err").text();
                var NRIC_ID = $("#NRIC_ID").val();
                var NRIC_OTHER = $("#NRIC_OTHER").val();
                if (NRIC == "") {
                    $("#NRIC_err").text("[required]").addClass('error');
                    $("#NRIC").addClass('error');
                    retVal = false;
                } else if (NRIC == "SNG_3") {
                    if (NRIC_OTHER == "") {
                        $("#NRIC_OTHER_err").text("[required]").addClass('error');
                        $("#NRIC_OTHER").addClass('error');
                        retVal = false;
                    } else {
                        $("#NRIC_OTHER_err").text("").removeClass('error');
                        $("#NRIC_OTHER").removeClass('error');
                    }
                    if (NRIC_ID == "" && NRIC_OTHER != "NOTAXCODE") {
                        $("#NRIC_err").text("").removeClass('error');
                        $("#NRIC").removeClass('error');
                        $("#NRIC_ID_err").text("[required]").addClass('error');
                        $("#NRIC_ID").addClass('error');
                        retVal = false;
                    } else if (nric_error_text != '[code exists!]'){
                        $("#NRIC_ID_err").text("").removeClass('error');
                        $("#NRIC_ID").removeClass('error');
                    }
                } else if (NRIC_ID == "") {
                    $("#NRIC_err").text("").removeClass('error');
                    $("#NRIC").removeClass('error');
                    $("#NRIC_ID_err").text("[required]").addClass('error');
                    $("#NRIC_ID").addClass('error');
                    retVal = false;
                } else if (nric_error_text != '[code exists!]' && nric_error_text != '[Invalid!]') {
                    $("#NRIC_err").text("").removeClass('error');
                    $("#NRIC").removeClass('error');
                    $("#NRIC_ID_err").text("").removeClass('error');
                    $("#NRIC_ID").removeClass('error');
                }
            }
            if (country_of_residence == "USA") {
                SSN = $("#SSN").val();
                var ssn_error_text = $("#SSN_err").text();
                if (SSN == "") {
                    $("#SSN_err").text("[required]").addClass('error');
                    $("#SSN").addClass('error');
                    retVal = false;
                } else if (ssn_error_text != '[code exists!]') {
                    $("#SSN_err").text("").removeClass('error');
                    $("#SSN").removeClass('error');
                }
            }

            var pers_first_name = $("#pers_first_name").val();
            if (pers_first_name == "") {
                $("#pers_first_name_err").text("[required]").addClass('error');
                $("#pers_first_name").addClass('error');
                retVal = false;
            } else {
                $("#pers_first_name_err").text("").removeClass('error');
                $("#pers_first_name").removeClass('error');
            }
            var pers_gender = $("#pers_gender option:selected").val();
            if (pers_gender == "") {
                $("#pers_gender_err").text("[required]").addClass('error');
                $("#pers_gender").addClass('error');
                retVal = false;
            } else {
                $("#pers_gender_err").text("").removeClass('error');
                $("#pers_gender").removeClass('error');
            }

            var pers_contact_number = $("#pers_contact_number").val();
            if (pers_contact_number == "") {
                $("#pers_contact_number_err").text("[required]").addClass('error');
                $("#pers_contact_number").addClass('error');
                retVal = false;
            } else {
                if (valid_contact_number(pers_contact_number) == false) {
                    $("#pers_contact_number_err").text("[invalid]").addClass('error');
                    $("#pers_contact_number").addClass('error');
                    retVal = false;
                } else {
                    $("#pers_contact_number_err").text("").removeClass('error');
                    $("#pers_contact_number").removeClass('error');
                }
            }

            var pers_alternate_contact_number = $("#pers_alternate_contact_number").val();
            if (pers_alternate_contact_number != "" && valid_contact_number(pers_alternate_contact_number) == false) {
                $("#pers_alternate_contact_number_err").text("[invalid]").addClass('error');
                $("#pers_alternate_contact_number").addClass('error');
                retVal = false;
            } else {
                $("#pers_alternate_contact_number_err").text("").removeClass('error');
                $("#pers_alternate_contact_number").removeClass('error');
            }

            var user_registered_email = $("#user_registered_email").val();
            if (user_registered_email == "") {
                $("#user_registered_email_err").text("[required]").addClass('error');
                $("#user_registered_email").addClass('error');
                retVal = false;
            } else {
                if (valid_email_address(user_registered_email) == false) {
                    $("#user_registered_email_err").text("[invalid]").addClass('error');
                    $("#user_registered_email").addClass('error');
                    retVal = false;
                }
            }

            var pers_alternate_email = $("#pers_alternate_email").val();
            if (pers_alternate_email != "" && valid_email_address(pers_alternate_email) == false) {
                $("#pers_alternate_email_err").text("[invalid]").addClass('error');
                $("#pers_alternate_email").addClass('error');
                retVal = false;
            }
            else {
                $("#pers_alternate_email_err").text("").removeClass('error');
                $("#pers_alternate_email").removeClass('error');
            }
            
            var highest_educ_level = $.trim($("#highest_educ_level").val());        
            if (highest_educ_level == '') {            
                $("#highest_educ_level_err").text("[required]").addClass('error');
                $("#highest_educ_level").addClass('error');
                retVal = false;
            } else {            
                $("#highest_educ_level_err").text("").removeClass('error');
                $("#highest_educ_level").removeClass('error');
            } 
            var sal_range = $.trim($("#sal_range").val());
            if (sal_range == '') { 
                $("#sal_range_err").text("[required]").addClass('error');
                $("#sal_range").addClass('error');
                retVal = false;
            } else {
                $("#sal_range_err").text("").removeClass('error');
                $("#sal_range").removeClass('error');
            }

            var emp_company_name = $("#emp_company_name").val();
            if (emp_company_name == "") {
                $("#emp_company_name_err").text("[required]").addClass('error');
                $("#emp_company_name").addClass('error');
                retVal = false;
            } else {
                $("#emp_company_name_err").text("").removeClass('error');
                $("#emp_company_name").removeClass('error');
            }
            var emp_doj = $("#emp_doj").val();
            if (emp_doj == "") {
                $("#emp_doj_err").text("[required]").addClass('error');
                $("#emp_doj").addClass('error');
                retVal = false;
            } else {
                $("#emp_doj_err").text("").removeClass('error');
                $("#emp_doj").removeClass('error');
            }
            var emp_designation = $.trim($("#emp_designation").val());
            if (emp_designation == "") {
                $("#emp_designation_err").text("[required]").addClass('error');
                $("#emp_designation").addClass('error');
                retVal = false;
            } else {
                $("#emp_designation_err").text("").removeClass('error');
                $("#emp_designation").removeClass('error');
                var $emp_designation_others = $.trim($("#emp_designation_others").val());
                if (emp_designation == "OTHERS" && $emp_designation_others=='') {
                    $("#emp_designation_others_err").text("[required]").addClass('error');
                    $("#emp_designation_others").addClass('error');
                    retVal = false;
                } else {
                    $("#emp_designation_others_err").text("").removeClass('error');
                    $("#emp_designation_others").removeClass('error');
                }
            }

            var emp_email = $("#emp_email").val();
            if (emp_email == "") {
                $("#emp_email_err").text("[required]").addClass('error');
                $("#emp_email").addClass('error');
                retVal = false;
            } else {
                if (valid_email_address(emp_email) == false) {
                    $("#emp_email_err").text("[invalid]").addClass('error');
                    $("#emp_email").addClass('error');
                    retVal = false;
                } else {
                    $("#emp_email_err").text("").removeClass('error');
                    $("#emp_email").removeClass('error');
                }
            }

            var emp_contact_number = $("#emp_contact_number").val();
            if (emp_contact_number != "" && valid_contact_number(emp_contact_number) == false) {
                $("#emp_contact_number_err").text("[invalid]").addClass('error');
                $("#emp_contact_number").addClass('error');
                retVal = false;
            } else {
                $("#emp_contact_number_err").text("").removeClass('error');
                $("#emp_contact_number").removeClass('error');
            }
            var user_role = $("#user_role").val();
            if (user_role == "") {
                $("#user_role_err").text("[required]").addClass('error');
                $("#user_role").addClass('error');
                retVal = false;
            } else {
                $("#user_role_err").text("").removeClass('error');
                $("#user_role").removeClass('error');
            }
            if ($('span').hasClass('error')) {
                retVal = false;
            }
            return retVal;
        }        

        $('.number').keydown(function(event) {
            if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9
                    || event.keyCode == 27 || event.keyCode == 13
                    || (event.keyCode == 65 && event.ctrlKey === true)
                    || (event.keyCode >= 35 && event.keyCode <= 39)) {
                return;
            } else {
                if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {
                    event.preventDefault();
                }
            }
        });
        
        function validate_alternate_email(e, id) {
            e = $.trim(e);
            if (e == '') {
                $("#" + id + "_err").text("").removeClass('error');
                $("#" + id).removeClass('error');
                return false;
            } else if (valid_email_address(e) == false) {
                $("#" + id + "_err").text("[invalid]").addClass('error');
                $("#" + id).addClass('error');
                return false;
            } else {
                $("#" + id + "_err").text("").removeClass('error');
                $("#" + id).removeClass('error');
                return true;
            }
        }
        function validate_office_email(e, id) {
            e = $.trim(e);
            if (e == '') {
                $("#" + id + "_err").text("[required]").addClass('error');
                $("#" + id).addClass('error');
                return false;
            } else if (valid_email_address(e) == false) {
                $("#" + id + "_err").text("[invalid]").addClass('error');
                $("#" + id).addClass('error');
                return false;
            } else {
                $("#" + id + "_err").text("").removeClass('error');
                $("#" + id).removeClass('error');
                return true;
            }
        }

        primary_email = $('#user_registered_email').val();
        function isunique_email(e, id) {
            if (e == '') {
                $("#" + id + "_err").text("[required]").addClass('error');
                $("#" + id).addClass('error');
            } else if (valid_email_address(e) == false) {
                $("#" + id + "_err").text("[invalid]").addClass('error');
                $("#" + id).addClass('error');
            } else if (e != primary_email) {
                $.ajax({
                    url: baseurl + "internal_user/check_email_id",
                    type: "post",
                    data: 'email=' + e,
                    success: function(res) {
                        if (res == 1) {
                            window.email_id = 'exists';
                            $("#" + id + "_err").text("[Email Id exists!]").addClass('error');
                            $("#" + id).addClass('error');
                        } else {
                            window.email_id = 'notexists';
                            $("#" + id + "_err").text("").removeClass('error');
                            $("#" + id).removeClass('error');
                        }
                    },
                    error: function() {
                        return false;
                    }
                });
            } else {
                $("#" + id + "_err").text("").removeClass('error');
                $("#" + id).removeClass('error');
            }
            return false;
        }
        function isunique_pan(e, id) {
            var edit_user_id = $("#edit_user_id").val();
            e = $.trim(e);
            var NRIC = $("#NRIC").val();
            var NRIC_OTHER = $("#NRIC_OTHER").val();
            var country_of_residence = $("#country_of_residence").val();
            if(NRIC == 'SNG_3' && NRIC_OTHER == "NOTAXCODE" && country_of_residence == 'SGP') {
                return false;
            }
            if (e == '') {
                $("#" + id + "_err").text("[required]").addClass('error');
                $("#" + id).addClass('error');
                return false;
            } else {
                var $country_of_residence = $.trim($('#country_of_residence').val());
                var $nric = $.trim($('#NRIC').val());
                $.ajax({
                    url: baseurl + "profile/check_pan",
                    type: "post",
                    data: {pan_id: e, edit_user_id: edit_user_id, country_of_residence:$country_of_residence, nric:$nric},
                    success: function(res) {
                        if (res == 1) {
                            window.email_id = 'exists';
                            $("#" + id + "_err").text("[code exists!]").addClass('error');
                            $("#" + id).addClass('error');
                            return false;
                        } else if (res == 2) {
                            $("#" + id + "_err").text("[Invalid!]").addClass('error');
                            $("#" + id).addClass('error');
                            return false;
                        } else {
                            window.email_id = 'notexists';
                            $("#" + id + "_err").text("").removeClass('error');
                            $("#" + id).removeClass('error');
                            return true;
                        }
                    },
                    error: function() {
                        return false;
                    }
                });
            }
        }

        $(document).ready(function() {
            var check = 0;
            $('#edit_user_form').submit(function() {
                check = 1;
                return validate();
            });
            $('#edit_user_form select,#edit_user_form input').change(function() {
                if (check == 1) {
                    return validate();
                }
            });
        });

        $("#reason_for_deactivation").change(function() {
            var reason_for_deactivation = $("#reason_for_deactivation").val();
            if (reason_for_deactivation == 'OTHERS') {
                $("#other_reason").show();
            }
            else
            {
                $("#other_reason").hide();
            }
        });

        function valid_deactivate_reason(userName) {
            var pattern = new RegExp(/^[\sa-zA-Z0-9_,.-]+$/);
            return pattern.test(userName);
        }
        function validate_deactivate_user() {
            var retVal = true;

            deactivation_date = $("#deactivation_date").val();
            reason_for_deactivation = $("#reason_for_deactivation").val();
            other_reason_for_deactivation = $("#other_reason_for_deactivation").val();

            if (deactivation_date == "") {
                $("#deactivation_date_err").text("[required]").addClass('error');
                $("#deactivation_date").addClass('error');
                retVal = false;
            } else {
                $("#deactivation_date_err").text("").removeClass('error');
                $("#deactivation_date").removeClass('error');
            }
            if (reason_for_deactivation == "") {
                $("#reason_for_deactivation_err").text("[required]").addClass('error');
                $("#reason_for_deactivation").addClass('error');
                retVal = false;
            } else {
                $("#reason_for_deactivation_err").text("").removeClass('error');
                $("#reason_for_deactivation").removeClass('error');
            }

            if (reason_for_deactivation == "OTHERS") {
                if (other_reason_for_deactivation == "") {
                    $("#other_reason_for_deactivation_err").text("[required]").addClass('error');
                    $("#other_reason_for_deactivation").addClass('error');
                    retVal = false;
                } else {
                    if (valid_deactivate_reason(other_reason_for_deactivation) == false)
                    {
                        $("#other_reason_for_deactivation_err").text("[invalid]").addClass('error');
                        $("#other_reason_for_deactivation").addClass('error');
                        retVal = false;
                    } else {
                        $("#other_reason_for_deactivation_err").text("").removeClass('error');
                        $("#other_reason_for_deactivation").removeClass('error');
                    }
                }
            } else {
                $("#other_reason_for_deactivation_err").text("").removeClass('error');
            }
            return retVal;
        }
        $(".alphabets").keydown(function(e) {
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 32]) !== -1 ||
                            (e.keyCode == 65 && e.ctrlKey === true) ||
                                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                        return;
                    }
                    if ((e.shiftKey || (e.keyCode < 65 || e.keyCode > 90))) {
                        e.preventDefault();
                    }
                });
        $(".alphanumeric").keydown(function(e) {
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 32]) !== -1 ||
                            (e.keyCode == 65 && e.ctrlKey === true) ||
                                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                        return;
                    }
                    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 90) && (e.keyCode < 96 || e.keyCode > 105))) {
                        e.preventDefault();
                    }
                });
        $(document).ready(function() {
            $('#reset_form').click(function() {
                $(".error").text("").removeClass('error');
                $('#edit_user_form').each(function() {
                    this.reset();
                });
                $('#country_of_residence').trigger('change');
                var curr_country = $("#current_pers_country").val();
                var curr_state = $("#current_pers_states").val();
                reset_states(curr_country, curr_state, 'pers_states');
                var curr_emp_country = $("#current_emp_country").val();
                var curr_emp_state = $("#current_emp_states").val();
                reset_states(curr_emp_country, curr_emp_state, 'emp_states');
            });
        });
        function reset_states(curr_country, curr_state, id) {
            $.post(baseurl + "profile/get_states_json", {country_param: curr_country}, function(data) {
                var json_data = $.parseJSON(data);
                var $pers_states_select = $("#" + id);
                $pers_states_select.html('<option value="">Select</option>');
                var selected_attr_value = '';
                $.each(json_data, function(i, item) {
                    if (item.parameter_id == curr_state) {
                        selected_attr_value = 'selected="selected"';
                    }
                    else {
                        selected_attr_value = '';
                    }
                    $pers_states_select.append('<option value="' + item.parameter_id + '" ' + selected_attr_value + '>' + item.category_name + '</option>');
                });

            });
        }
    </script>
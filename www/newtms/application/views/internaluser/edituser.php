<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/multiselectdropdown.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/multiselectdropdown.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/multidropdown.js"></script>
<?php
$this->load->helper('form');
$this->load->helper('metavalues_helper');
$this->load->helper('common');

if (!empty($tax_error)) { 
    echo '<div class="error1">' . $tax_error . '</div>';
}
?>

<div class="col-md-10">
    <h2 class="panel_heading_style">
        <img src="<?php echo base_url(); ?>/assets/images/internal_user.png"/>  Internal Staff - Edit/ Deactivate
    </h2>
    <div class="table-responsive">
        <?php
        $atr = 'id="search_form" name="search_form"';
        echo form_open("internal_user/edit_user", $atr);
        ?>    
        <table class="table table-striped">      
            <tbody>
                <tr>
                    <td class="td_heading">Search by Staff Name:<span class="required">*</span></td>
                    <td>
                        <?php
                        echo form_hidden('search_user_id',$this->input->get('search_user_id'),'search_user_id');
                        ?>
                        <input size="50" type="text" name="search_user_firstname" id="search_user_firstname" value="<?php echo $this->input->post('search_user_firstname'); ?>">
                        <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
                        <span id="search_user_firstname_err"></span>
                    </td>      
                    <td align="center"><button type="submit" title="Search" value="Search" name="search_form_btn" id="search_form_btn" class="btn btn-xs btn-primary no-mar">
                            <span class="glyphicon glyphicon-search"></span>  Search</button>
                    
                    </td>
                </tr>                
            </tbody>
        </table>
        <?php echo form_close(); ?>    
    </div>
    <br>
    <?php echo validation_errors('<div class="error1">', '</div>'); ?>
    <?php
    $atr = 'id="edit_user_form" name="edit_user_form" onsubmit="return(validate(true));"' . $form_style_attr;
    echo form_open_multipart("internal_user/edit_user", $atr);
    ?>
    <input type="hidden" name="edit_user_id" id="edit_user_id" value="<?php echo $edit_user_id; ?>" />        
    <input size="50" type="hidden" name="search_user_firstname" id="search_user_firstname" value="<?php echo $this->input->post('search_user_firstname'); ?>">
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>assets/images/personal_details.png"> Personal Details</h2>
    <div class="table-responsive">
        <table class="table table-striped">      
            <tbody>
                <tr>
                    <td width="16%" class="td_heading">Country of Residence:<span class="required">*</span></td>
                    <td colspan="1">        
                        <?php
                        $countries = fetch_metavalues_by_category_id(Meta_Values::COUNTRIES);
                        foreach ($countries as $item):
                            $country_options[$item['parameter_id']] = $item['category_name'];
                        endforeach;
                        $attr = 'id="country_of_residence"';
                        echo form_dropdown('country_of_residence', $country_options, $user_list_values->country_of_residence, $attr);
                        ?>
                        <span id="country_of_residence_err"></span>
                    </td>
                    <td class="td_heading" colspan="2" >
                            <?php
                            $attr = array('name' => 'PAN', 'class' => 'upper_case alphanumeric', 'id' => 'PAN', 'onblur' => 'javascript:isunique_pan(this.value,this.id);');
                            ?>
                            <span id="PAN_err"></span>
                        </SPAN>
                        <SPAN id="SGP" style="">NRIC Type: <span class="required">* </span>
                            <?php
                            $nrics = fetch_metavalues_by_category_id(Meta_Values::NRIC);
                            $nris_options[''] = 'Select';
                            foreach ($nrics as $item):
                                $nris_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;
                            $attr = 'id="NRIC"';
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

                            $attr = 'id="NRIC_OTHER"';
                            echo form_dropdown('NRIC_OTHER', $nric_other_options, $other_identi_type, $attr);
                            ?>
                            <span id="NRIC_OTHER_err"></span>
                        </SPAN>
                        <SPAN id="SGP_ID" style="display:none;">
                            <br /><br />
                            <label id="SGP_ID_label"> NRIC Code : </label><span class="required">* </span>
                            <?php
                            $attr = array('name' => 'NRIC_ID', 'class' => 'upper_case alphanumeric', 'id' => 'NRIC_ID', 'onblur' => 'javascript:isunique_pan(this.value,this.id);', 'class' => 'upper_case');
                            echo form_input($attr, $nric_number);
                            ?>
                            <span id="NRIC_ID_err"></span>
                        </SPAN>
                            <?php
                            $attr = array('name' => 'SSN', 'class' => 'upper_case alphanumeric', 'id' => 'SSN', 'onblur' => 'javascript:isunique_pan(this.value,this.id);');
                            ?>    
                            <span id="SSN_err"></span>
                        </SPAN>
                    </td>
                </tr>        
                <tr>
                    <td class="td_heading">Name:<span class="required">*</span></td>
                    <td colspan="3">
                        <?php
                        $attr = array(
                            'name' => 'pers_first_name',
                            'id' => 'pers_first_name',
                            'maxlength' => '100',
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
                            'style' => 'width:200px',
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
                            'style' => 'width:200px',
                        );
                        echo form_input($attr);
                        ?>    
                        <span id="pers_alternate_contact_number_err"></span>               
                    </td>
                </tr>        
                <tr>          
                    <td class="td_heading">Email Id(P):</td>
                    <td>
                        <?php
                        $attr = array(
                            'name' => 'user_registered_email',
                            'id' => 'user_registered_email',
                            'maxlength' => '50',
                            'value' => $user_list_values->registered_email_id,
                            'onblur' => 'javascript:isunique_email(this.value,this.id);',
                            'style' => 'width:200px',
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
                            'style' => 'width:200px',
                        );
                        echo form_input($attr);
                        ?>
                        <span id="pers_alternate_email_err"></span>
                    </td>
                </tr>
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
                        <?php echo form_dropdown('highest_educ_level', $highest_educ_level_options, $user_list_values->highest_educ_level, 'id="highest_educ_level" style="width:100%"'); ?>
                        <span id="highest_educ_level_err"></span>
                    </td>
                </tr>
                <tr>        
                    <td class="td_heading"> <?php echo $image_error; ?> Upload Image:</td>            
                    <td>
                        <input type="file" name="userfile" id="userfile" onchange="showimagepreview(this)" />
                        <label id="image_err"></label>
                    </td>
                    <?php if ($user_list_values->photo_upload_path): ?> 
                        <td id="user_image_preview" class="td_heading">&nbsp;&nbsp;&nbsp;
                            <img width="150px"  src="<?php echo base_url() . $user_list_values->photo_upload_path; ?>" id="imgprvw" border="0" />                
                            <span id="removeimagebutton" onclick="remove_image();" class="glyphicon glyphicon-remove-circle"></span>
                        </td> 
                        <td colspan="2">
                            <b>Use Previous Image:</b>&nbsp;&nbsp;&nbsp;<input type="radio" name="deleteimage" checked="checked" id="deleteimageyes" value="yes"/>Yes
                            <input type="radio" name="deleteimage" id="deleteimageno" value="no"/>No
                        </td>
                    <?php else: ?>
                        <td id="user_image_preview" colspan="3" class="td_heading">&nbsp;&nbsp;&nbsp;&nbsp;
                            <img height="120px" width="120px" id="imgprvw" border="0" />
                            &nbsp;&nbsp;&nbsp;
                            <span style="display: none;" id="removeimagebutton" onclick="remove_image();" class="glyphicon glyphicon-remove-circle"></span>
                        </td>     
                    <?php endif; ?>                                
                </tr>
                <tr>          
                    <td class="td_heading">Personal Address:</td>
                    <td colspan="3" class="textarea_inp">
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
                            'style' => 'width:200px',
                        );
                        echo form_input($attr);
                        ?>
                        <span id="pers_city_err"></span>
                    </td>
                    <td class="td_heading">Country:</td>
                    <td>
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
                        $states = ($user_list_values->personal_address_country) ? $this->internaluser->get_states($user_list_values->personal_address_country) : '';
                        $state_options[''] = 'Select';
                        foreach ($states as $item) {
                            $state_options[$item->parameter_id] = $item->category_name;
                        }
                        $attr_js = 'id="pers_states"';
                        echo form_dropdown('pers_states', $state_options, $user_list_values->personal_address_state, $attr_js);
                        echo form_hidden('current_pers_states', $user_list_values->personal_address_state, 'current_pers_states');
                        ?>                        
                    </td>
                    <td class="td_heading">Postal Code:</td>
                    <td>
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
                        <span id="pers_zipcode_err"></span>
                    </td>           
                </tr>        
            </tbody>
        </table>
    </div>
    <br>
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/officail_details.png"> Official Details</h2>
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
                            'style' => 'width:200px',
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
                            'style' => 'width:200px',
                            'disabled' =>true
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
                            'style' => 'width:200px',
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
                            'value' => $user_list_values->off_address_bldg,
                            'class' => 'upper_case',
                            'maxlength' => '255'
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
                            'name' => 'emp_city',
                            'id' => 'emp_city',
                            'maxlength' => '50',
                            'value' => $user_list_values->off_address_city,
                            'class' => 'upper_case alphabets',
                            'style' => 'width:200px',
                        );
                        echo form_input($attr);
                        ?>
                        <span id="emp_city_err"></span>
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
                        $states = ($user_list_values->off_address_country) ? $this->internaluser->get_states($user_list_values->off_address_country) : '';
                        $state_options = array();
                        $state_options[''] = 'Select';
                        foreach ($states as $item) {
                            $state_options[$item->parameter_id] = $item->category_name;
                        }
                        $attr_js = 'id="emp_states"';
                        echo form_dropdown('emp_states', $state_options, $user_list_values->off_address_state, $attr_js);
                        echo form_hidden('current_emp_states', $user_list_values->off_address_state, 'current_emp_states');
                        ?>              
                    </td>
                    <td class="td_heading">Postal Code:</td>
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
                        <span id="emp_zip_err"></span>
                    </td>
                </tr>           
            </tbody>
        </table>
    </div>
    <br>
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/other_details.png"> Staff Other Details</h2>
    <div class="table-responsive">
        <table class="table table-striped">      
            <tbody>
                <tr>
                    <td class="td_heading" width="16%">Role:<span class="required">*</span></td>
                    <td width="29%">
                        <?php
                        $role_array = explode(', ', $user_list_values->role_id);
                        $role_options[''] = 'Select';
                        foreach ($roles as $role):
                            $role_options[$role->role_id] = $role->role_name;
                        endforeach;
                        unset($role_options['COMPACT']);
                        $attr_js = 'id="user_role" multiple="multiple" size="5"';
                        echo form_dropdown('user_role[]', $role_options, $role_array, $attr_js);
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
                        <input type="hidden" value="<?php echo $user_list_values->user_name; ?>" name="user_name" />            
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
                            <i style="color:blue">(On activation, mail will be sent to the userâ€™s official email Id, with the username.)</i>
                        <?php } ?>
                    </td>
                </tr>        
            </tbody>
        </table>
        <span class="required required_i">* Required Fields</span>
    </div>		  
    <div class="button_class99">
        <button class="btn btn-primary" type="submit" name="edit_user_form_btn" id="edit_user_form_btn" value="Update">
            <span class="glyphicon glyphicon-saved"></span>&nbsp;Update
        </button> &nbsp; &nbsp;        
        <?php
        if ($user_list_values->account_status == 'ACTIVE') {            
            if(empty($user_role_check)) {
                $href = "#ex8";
            } else {
                $href = "#ex10";
            }
        ?>
            <a class="small_text" href="<?php echo $href;?>" rel="modal:open">
                <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-remove-sign"></span>&nbsp;Deactivate</button>
            </a> &nbsp; &nbsp;
        <?php         
        } 
        ?>            
    </div>
    <?php echo form_close(); ?>
</div>   
<?php
$form_attributes = array('name' => 'trainee_edit_search', 'id' => 'trainee_edit_search', "onsubmit" => "return(validate_deactivate_user());");
echo form_open("internal_user/deactivate_user", $form_attributes);
?>       
<div class="modal1_051" id="ex8" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Deactivate Internal Staff</h2>  
    <div class="table-responsive">
        <table class="table table-striped">      
            <tbody>
                <tr>
                    <td class="td_heading"> De-Activation Date:<span class="red">*</span> </td>
                    <td> 
                        <label id="deactivation_date" class='error'> </label>
                        <span id="deactivation_date_err"></span>
                    </td>
                </tr>            
                <tr>
                    <td class="td_heading" width="30%">Reason for De-Activation:<span class="red">*</span></td>
                    <td> 
                        <?php
                        $d_reasons = fetch_metavalues_by_category_id(Meta_Values::DEACTIVATE_REASONS);
                        $reasons_options[''] = 'Select';
                        foreach ($d_reasons as $item):
                            $reasons_options[$item['parameter_id']] = $item['category_name'];
                        endforeach;
                        $reasons_options['OTHERS'] = 'Others';
                        $attr = 'id="reason_for_deactivation"';
                        echo form_dropdown('reason_for_deactivation', $reasons_options, $this->input->post('reason_for_deactivation'), $attr);
                        ?> &nbsp; 
                        <span id="reason_for_deactivation_err"></span>                
                        <div id="other_reason" style="display:none;">
                            <?php
                            $attr = array(
                                'name' => 'other_reason_for_deactivation',
                                'id' => 'other_reason_for_deactivation',
                                'size' => 35,
                                'style' => 'margin:5px 0 0 0',
                                'class' => 'upper_case',
                            );
                            echo form_input($attr);
                            ?>  
                            <span id="other_reason_for_deactivation_err"></span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>       
    <br>
    Are you sure you want to deactivate this Staff?
    <br>
    <span class="required_i red">*Required Field</span>
    <div class="popup_cancel9">
        <div rel="modal:close">
            <button class="btn btn-primary" type="submit">Save</button>
            &nbsp;&nbsp;
            <a href="#" rel="modal:close">
                <button class="btn btn-primary" type="button">Cancel</button>
            </a>    
        </div>
    </div>        
</div>
<?php
echo form_hidden('user_id', $edit_user_id);
echo form_close();
?>
<?php
$form_atr = 'id="role_transfer_form" name="role_transfer_form" onsubmit="return validate_role();"';
echo form_open("internal_user/trainee_role_change", $form_atr);
?>
<div class="modal1_trainee" id="ex9" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Assign Trainee as Internal Staff</h2>
    <div class="trainee_info_div">
        <div class="table-responsive">
            <div>
                <p>
                    This taxcode exists in our system.The user is currently Trainee  in your training institute
                    <?php
                    $tenant_details = fetch_tenant_details($this->session->userdata('userDetails')->tenant_id);
                    echo "<b>'". $tenant_details->tenant_name."'</b>";
                    ?>.
                    <br/>
                    <i id="inactive_id" style="display:none">
                        The account is in <i id="account_status" style="font-weight:bold" ></i> state.
                    </i>
                </p>
            </div>        
        </div>       
        <br/>
        <span style="color:blue">Do you wish to upgrade <b>Trainee</b> to an <b>Internal Staff</b> in your institute?
        <br/>        
        <div class="popup_cancel9">
            <div rel="modal:close">
                <button id="trainee_info_button" class="btn btn-primary" type="button">Yes</button>
                &nbsp;&nbsp;
                <a href="#" rel="modal:close">
                    <button class="btn btn-primary" type="button">No</button>
                </a>    
            </div>
        </div>
    </div> 
    <div class="trainee_info" style="display:none">        
        <table  class="table table-striped"> 
             <tbody>
            <tr>
                <td class="td_heading" style="width:19%">NRIC/FIN No.:</td>
                <td colspan="2" id="trainee_tax_code"></td>
                <td class="td_heading"><strong>Name:</strong></td>
                <td colspan="2"><span id="trainee_first_name"></span></td>
           </tr>          
            <tr>  
                <td class="td_heading"><strong>Nationality:</strong></td>
                <td><span id="trainee_nationality"></span></td>
                <td class="td_heading"><strong>Gender:</strong></td>
                <td><span id="trainee_gender"></span></td>
                <td class="td_heading"><strong>DOB:</strong></td>
                <td><span id="trainee_dob"></span></td>
            </tr>                             
            <tr>
                <td class="td_heading"><strong>Contact #:</strong></td>
                <td colspan="2"><span id="trainee_contact_no"></span></td>
                <td class="td_heading"><strong>Email Id:</strong></td>
                <td colspan="2"><span id="trainee_email_id"></span></td>
            </tr>          
            <tr>            
                <td class="td_heading"><strong>Address:</strong></td>
                <td colspan="5" id="trainee_building_street" style="height: 10px;"></td>
            </tr>
            <tr>
                <td class="td_heading"><label>Staff Role:<span class="required">*</span></label> </td>           
                <td colspan="5">
                    <?php
                        $role_options[''] = 'Select';
                        foreach ($roles as $role):
                            $role_options[$role->role_id] = $role->role_name;
                        endforeach;
                        unset($role_options['COMPACT']);
                        $attr_js = 'id="trainee_role" multiple="multiple" size="5"';
                        echo form_dropdown('trainee_role[]', $role_options, $this->input->post('trainee_role'), $attr_js);
                    ?>
                    <span id="trainee_role_err"></span>
                </td>
            </tr>
             </tbody>
        </table>
       
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/officail_details.png"/>Employment Details</h2>          
        <div class="table-responsive">
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <td class="td_heading" >Company Name:<span class="required">*</span></td>
                        <td >
                            <?php
                            $attr = array(
                                'name' => 'trainee_company_name',
                                'id' => 'trainee_company_name',
                                'maxlength' => '50',
                                'value' => $this->input->post('emp_company_name'),
                                'class' => 'upper_case',
                                'style' => 'width:200px',
                            );
                            echo form_input($attr);
                            ?> 
                            <span id="trainee_company_name_err"></span>                  
                        </td>
                        <td class="td_heading"  >Date of Joining:<span class="required">*</span></td>
                        <td>
                            <?php
                            $emp_doj = array(
                                'name' => 'trainee_doj',
                                'id' => 'trainee_doj',
                                'value' => $this->input->post('emp_doj'),
                                'readonly' => 'readonly',
                                'placeholder' => 'dd/mm/yyyy'
                            );
                            echo form_input($emp_doj);
                            ?> 
                            <span id="trainee_doj_err"></span>                  
                        </td>
                    </tr>        
                    <tr>
                        <td class="td_heading">Designation:<span class="required">*</span></td>
                        <td>
                            <?php                       
                        $trainee_occupation = fetch_metavalues_by_category_id(Meta_Values::OCCUPATION);
                        $trainee_occupation_options[''] = 'Select';
                        foreach ($trainee_occupation as $item):
                            $trainee_occupation_options[$item['parameter_id']] = $item['category_name'];
                        endforeach; 
                        $trainee_occupation_options['OTHERS'] = 'OTHERS';
                        ?>
                        <?php echo form_dropdown('trainee_designation', $trainee_occupation_options, set_value('trainee_designation'), 'id="trainee_designation" style="width:170px"'); ?>
                            
                            <span id="trainee_designation_err"></span>                    
                        </td>                        
                        <td class="trainee_occupation_others" colspan="2">
                            <?php
                            $attr = array(
                                'name' => 'trainee_designation_others',
                                'id' => 'trainee_designation_others',
                                'maxlength' => '50',
                                'value' => $this->input->post('trainee_designation_others'),
                                'class' => 'upper_case',
                                'style' => 'width:200px',
                            );
                            echo form_input($attr);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Email Id (O):<span class="required">*</span></td>
                        <td colspan="4">
                            <?php
                            $attr = array(
                                'name' => 'trainee_email',
                                'id' => 'trainee_email',
                                'maxlength' => '50',
                                'value' => $this->input->post('trainee_email'),
                                'onblur' => 'javascript:validate_office_email(this.value,this.id);',
                                'style' => 'width:200px',
                            );
                            echo form_input($attr);
                            ?> 
                            <span id="trainee_email_err"></span>               
                        </td>
                    </tr>
                    <tr>
                       <td class="td_heading">Office Address:</td>
                        <td colspan="3" class="textarea_inp"> 
                            <?php
                            $attr = array(
                                'name' => 'trainee_address',
                                'rows' => '2',
                                'cols' => '70',
                                'value' => $this->input->post('trainee_address'),
                                'class' => 'upper_case',
                                'maxlength' => '255',
                                
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
                                'name' => 'trainee_city',
                                'id' => 'trainee_city',
                                'maxlength' => '50',
                                'value' => $this->input->post('trainee_city'),
                                'class' => 'upper_case alphabets',
                                'style' => 'width:200px',
                            );
                            echo form_input($attr);
                            ?> 
                            <span id="trainee_city_err"></span>
                        </td>
                         <td class="td_heading">Country:</td>
                        <td>
                            <?php
                            $attr_js = 'id="trainee_country"';
                            echo form_dropdown('trainee_country', $country_options, $this->input->post('trainee_country'), $attr_js);
                            ?>          
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">State:</td>
                        <td>
                            <?php
                            $attr = array('' => 'Select');
                            $country_param = $this->input->post('trainee_country');
                            if ($country_param != '') {
                                $states = $this->internaluser->get_states($country_param);
                                foreach ($states as $item) {
                                    $attr[$item->parameter_id] = $item->category_name;
                                }
                            }
                            $attr_js = 'id="trainee_states"';
                            echo form_dropdown('trainee_states', $attr, $this->input->post("trainee_states"), $attr_js);
                            ?>              
                        </td>
                         <td class="td_heading">Postal Code:</td>
                        <td>
                            <?php
                            $attr = array(
                                'name' => 'trainee_zip',
                                'id' => 'trainee_zip',
                                'maxlength' => '10',
                                'value' => $this->input->post('trainee_zip'),
                                'class' => 'alphanumeric upper_case'
                            );
                            echo form_input($attr);
                            ?>
                            <span id="trainee_zip_err"></span>
                        </td>
                    </tr>
                    <tr>
                         <td class="td_heading">Contact Number (O):</td>
                        <td colspan="3">
                            <?php
                            $attr = array(
                                'name' => 'trainee_contact_number',
                                'id' => 'trainee_contact_number',
                                'maxlength' => '50',
                                'value' => $this->input->post('trainee_contact_number'),
                                'class' => 'number',
                                'style' => 'width:200px',
                            );
                            echo form_input($attr);
                            ?>  
                            <span id="trainee_contact_number_err"></span>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
         <span class="required required_i">* Required Fields</span>
         <div class="popup_cancel9 trainee_info" style="display:none;margin-top: 1%">
            <div rel="modal:close">
                <button class="btn btn-primary" type="submit">Confirm Upgrade to Internal Staff</button>
                &nbsp;&nbsp;
                <a href="#" rel="modal:close" class="cancel">
                    <button class="btn btn-primary" type="button">Cancel</button>
                </a>
            </div>
        </div>
    </div>
</div>
<?php 
echo form_hidden('trainee_tax_code',"");
echo form_hidden('trainee_user_id',"");
echo form_hidden('trainee_status', "");
echo form_close(); 
?>
<div class="modal1_051" id="ex10" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Deactivate Internal Staff</h2>  
    <div class="table-responsive">
        This Internal Staff is currently assigned in  
        <?php
        $course ="";
        foreach ($user_role_check as $data):
            $course .= $data->course_class_name.", ";
        endforeach;
        echo "<span style='color:blue'>'". rtrim($course,", ")."'</span>.";
        ?>
        <br/><br/>
        Please remove the Staff from the Course/ Class before deactivating.
    </div>           
    <div class="popup_cancel9">
        <div rel="modal:close">            
            <a href="#" rel="modal:close">
                <button class="btn btn-primary" type="button">Cancel</button>
            </a>    
        </div>
    </div>        
</div>



<script type="text/javascript">
    
    function validate_search() {
        var fname = $.trim($("#search_user_firstname").val());
        
        var search_user_id = $('#search_user_id').val();
        if (fname == "") {
            $("#search_user_firstname_err").text("[required]").addClass('error');
            $("#search_user_firstname").addClass('error');
            return false;
        } else if (fname.indexOf('(') === -1) {
            $("#search_user_firstname_err").text("[Select user from autofill-help]").addClass('error');
            $("#search_user_firstname").addClass('error');
            return false;
        }else if(search_user_id == ''){
            $("#search_user_firstname_err").text("[Select user from autofill-help]").addClass('error');
            $("#search_user_firstname").addClass('error');
            return false;
        }
        return true;
    }
    //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM////////////////////////////////////
        $('#search_form').on('submit',function() {
            search_check = 1;
            //alert("form click");
            var status=validate_search();
            if(status){
            var self = $(this),
            button = self.find('input[type="submit"],button'),
            submitValue = button.data('submit-value');
            button.attr('disabled','disabled').html('Please Wait..');
            return true;
           }else{
               return false;
           }
        }); //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM///////////////////////
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
        $("#deactivation_date").text($.datepicker.formatDate("dd/mm/yy", new Date()));
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
            $("#trainee_doj").datepicker({
                dateFormat: 'dd/mm/yy',
                minDate: new Date(startYear, currenyMonth, CurrentDate),
                maxDate: 0,
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+0'
            });
        });
    });

        var country_param = $('#pers_country').val();
        if (country_param) {
            $.post('<?php echo site_url(); ?>internal_user/get_states_json', {country_param: country_param}, function(data) {
                json_data = $.parseJSON(data);
                $pers_states_select = $("#pers_states");
                var current_pers_states = $("#current_pers_states").val();
                $pers_states_select.html('<option value="">Select</option>');
                $.each(json_data, function(i, item) {
                    if(current_pers_states == item.parameter_id)
                        $pers_states_select.append('<option value="' + item.parameter_id + '" selected="selected">' + item.category_name + '</option>');
                    else 
                        $pers_states_select.append('<option value="' + item.parameter_id + '">' + item.category_name + '</option>');
                });
            });
        } else {
            $('#pers_states').find('option').remove().end().append('<option value="">Select</option>');
        }
        
        var country_param = $('#emp_country').val();
        if (country_param) {
            $.post('<?php echo site_url(); ?>internal_user/get_states_json', {country_param: country_param}, function(data) {
                json_data = $.parseJSON(data);
                $emp_states_select = $("#emp_states");
                var current_emp_states = $("#current_emp_states").val();
                $emp_states_select.html('<option value="">Select</option>');
                $.each(json_data, function(i, item) {
                    if(current_emp_states == item.parameter_id)
                        $emp_states_select.append('<option value="' + item.parameter_id + '" selected="selected">' + item.category_name + '</option>');
                    else
                        $emp_states_select.append('<option value="' + item.parameter_id + '" >' + item.category_name + '</option>');
                });
            });
        } else {
            $('#emp_states').find('option').remove().end().append('<option value="">Select</option>');
        }

    $('#emp_designation').change(function() {
       var  $selected_val = $('#emp_designation').val();
       if($selected_val == 'OTHERS') {
           $('.occupation_others').show();
        } else {
           $('.occupation_others').hide();
       }
    });
    $('#trainee_designation').change(function() {
       var  $selected_val = $('#trainee_designation').val();       
       if($selected_val == 'OTHERS') {
           $('.trainee_occupation_others').show();
        } else {
           $('.trainee_occupation_others').hide();
       }
    });
    $('#trainee_country').change(function() {
        var country_param = $(this).val();
        if (country_param) {
            $.post('<?php echo site_url(); ?>internal_user/get_states_json', {country_param: country_param}, function(data) {
                var json_data = $.parseJSON(data);
                var $emp_states_select = $("#trainee_states");
                $emp_states_select.html('<option value="">Select</option>');
                $.each(json_data, function(i, item) {
                    $emp_states_select.append('<option value="' + item.parameter_id + '">' + item.category_name + '</option>');
                });
            });
        } else {
            $('#trainee_states').find('option').remove().end().append('<option value="">Select</option>');
        }
    });
    function valid_name($name) {
        var ck_name = /^[A-Za-z ]+$/;
        return ck_name.test($name);
    }
    function valid_zip($zip) {
        var ck_name = /^[A-Za-z0-9 ]+$/;
        return ck_name.test($zip);
    }

    function validate(retVal) {         
        country_of_residence = $("#country_of_residence").val();
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
            } else if (pan_error_text != '[NRIC/FIN No. exists!]') {
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
                } else if (nric_error_text != '[NRIC/FIN No. exists!]') {
                    $("#NRIC_ID_err").text("").removeClass('error');
                    $("#NRIC_ID").removeClass('error');
                }
            } else if (NRIC_ID == "") {
                $("#NRIC_err").text("").removeClass('error');
                $("#NRIC").removeClass('error');
                $("#NRIC_ID_err").text("[required]").addClass('error');
                $("#NRIC_ID").addClass('error');
                retVal = false;
            } else if (nric_error_text != '[NRIC/FIN No. exists!]' && nric_error_text != '[Invalid!]') {
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
            } else if (ssn_error_text != '[NRIC/FIN No. exists!]') {
                $("#SSN_err").text("").removeClass('error');
                $("#SSN").removeClass('error');
            }
        }

        pers_first_name = $.trim($("#pers_first_name").val());
        if (pers_first_name == "") {
            $("#pers_first_name_err").text("[required]").addClass('error');
            $("#pers_first_name").addClass('error');
            retVal = false;
        }  else {
            $("#pers_first_name_err").text("").removeClass('error');
            $("#pers_first_name").removeClass('error');
        }

        pers_gender = $("#pers_gender option:selected").val();
        if (pers_gender == "") {
            $("#pers_gender_err").text("[required]").addClass('error');
            $("#pers_gender").addClass('error');
            retVal = false;
        } else {
            $("#pers_gender_err").text("").removeClass('error');
            $("#pers_gender").removeClass('error');
        }
        pers_contact_number = $.trim($("#pers_contact_number").val());
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

        pers_alternate_contact_number = $("#pers_alternate_contact_number").val();
        if (pers_alternate_contact_number != "" && valid_contact_number(pers_alternate_contact_number) == false) {
            $("#pers_alternate_contact_number_err").text("[invalid]").addClass('error');
            $("#pers_alternate_contact_number").addClass('error');
            retVal = false;
        } else {
            $("#pers_alternate_contact_number_err").text("").removeClass('error');
            $("#pers_alternate_contact_number").removeClass('error');
        }

        user_registered_email = $.trim($("#user_registered_email").val());        
        if (valid_email_address(user_registered_email) == false && user_registered_email != "") {
            $("#user_registered_email_err").text("[invalid]").addClass('error');
            $("#user_registered_email").addClass('error');
            retVal = false;
        }        

        pers_alternate_email = $("#pers_alternate_email").val();
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
        
        var pers_city = $.trim($("#pers_city").val());
        if (pers_city != '') {
            if (valid_name(pers_city) == false) {
                $("#pers_city_err").text("[invalid]").addClass('error');
                $("#pers_city").addClass('error');
            } else {
                $("#pers_city_err").text("").removeClass('error');
                $("#pers_city").removeClass('error');
            }
        } else {
            $("#pers_city_err").text("").removeClass('error');
            $("#pers_city").removeClass('error');
        }

        var pers_zipcode = $.trim($("#pers_zip").val());
        if (pers_zipcode != '') {
            if (valid_zip(pers_zipcode) == false) {
                $("#pers_zipcode_err").text("[invalid]").addClass('error');
                $("#pers_zipcode").addClass('error');
            } else {
                $("#pers_zipcode_err").text("").removeClass('error');
                $("#pers_zipcode").removeClass('error');
            }
        } else {
            $("#pers_zipcode_err").text("").removeClass('error');
            $("#pers_zipcode").removeClass('error');
        }

        emp_company_name = $.trim($("#emp_company_name").val());
        if (emp_company_name == "") {
            $("#emp_company_name_err").text("[required]").addClass('error');
            $("#emp_company_name").addClass('error');
            retVal = false;
        } else {
            $("#emp_company_name_err").text("").removeClass('error');
            $("#emp_company_name").removeClass('error');
        }
        emp_doj = $("#emp_doj").val();
        if (emp_doj == "") {
            $("#emp_doj_err").text("[required]").addClass('error');
            $("#emp_doj").addClass('error');
            retVal = false;
        } else {
            $("#emp_doj_err").text("").removeClass('error');
            $("#emp_doj").removeClass('error');
        }
        emp_designation = $.trim($("#emp_designation").val());
        if (emp_designation == "") {
            $("#emp_designation_err").text("[required]").addClass('error');
            $("#emp_designation").addClass('error');
            retVal = false;
        } else {
            $("#emp_designation_err").text("").removeClass('error');
            $("#emp_designation").removeClass('error');
        }

        emp_email = $.trim($("#emp_email").val());
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

        emp_contact_number = $("#emp_contact_number").val();
        if (emp_contact_number != "" && valid_contact_number(emp_contact_number) == false) {
            $("#emp_contact_number_err").text("[invalid]").addClass('error');
            $("#emp_contact_number").addClass('error');
            retVal = false;
        } else {
            $("#emp_contact_number_err").text("").removeClass('error');
            $("#emp_contact_number").removeClass('error');
        }
        var emp_city = $.trim($("#emp_city").val());
        if (emp_city != '') {
            if (valid_name(emp_city) == false) {
                $("#emp_city_err").text("[invalid]").addClass('error');
                $("#emp_city").addClass('error');
            } else {
                $("#emp_city_err").text("").removeClass('error');
                $("#emp_city").removeClass('error');
            }
        } else {
            $("#emp_city_err").text("").removeClass('error');
            $("#emp_city").removeClass('error');
        }

        var emp_zip = $.trim($("#emp_zip").val());
        if (emp_zip != '') {
            if (valid_zip(emp_zip) == false) {
                $("#emp_zip_err").text("[invalid]").addClass('error');
                $("#emp_zip").addClass('error');
            } else {
                $("#emp_zip_err").text("").removeClass('error');
                $("#emp_zip").removeClass('error');
            }
        } else {
            $("#emp_zip_err").text("").removeClass('error');
            $("#emp_zip").removeClass('error');
        }

        var user_role = '';
        $('input[name="user_role[]"]:checked').each(function() {
            user_role += $(this).val();
        })
        if (user_role.length == 0) {
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
        if(retVal == true){
            $('.button_class99 #edit_user_form_btn').css('display','none');
            $('#emp_email').attr('disabled',false);/////added by shubhranshu on 04/12/2018
        }
        return retVal;

    }
    $('.number').keydown(function(event) {
        if ($.inArray(event.keyCode, [46, 8, 9, 27, 13, 32]) !== -1
                || ($.inArray(event.keyCode, [65, 67, 86]) !== -1 && event.ctrlKey === true)
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
            $("#" + id + "_err").text("").removeClass('error');
            $("#" + id).removeClass('error');
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
            var edit_user_id = $('#edit_user_id').val();
            var country_of_residence = $.trim($('#country_of_residence').val());
            var nric = $.trim($('#NRIC').val());
            $.ajax({
                url: baseurl + "internal_user/check_pan",
                type: "post",
                data: {pan_id: e, edit_user_id: edit_user_id, country_of_residence: country_of_residence, nric: nric},
                dataType: 'json',
                success: function(res) {
                    if (res.status == 1) {
                        window.email_id = 'exists';
                        $("#" + id + "_err").text("[NRIC/FIN No. exists!]").addClass('error');
                        $("#" + id).addClass('error');
                        if(res.account_type == 'TRAINE' && res.tenant_id == tenant_id) {
                            if(res.account_status == 'ACTIVE') {
                                $('#account_status').text("'Active'");
                            } else if(res.account_status == 'INACTIV') {
                                $('#inactive_id').show();
                                $('#account_status').text("'In-Active'");
                            } else if(res.account_status == 'PENDACT') {
                                $('#account_status').text("'Pending Activation'");
                                $('#inactive_id').show();
                            }
                            $('#trainee_tax_code').text(e.toUpperCase());
                            $('#trainee_first_name').text(res.first_name);
                            $('#trainee_nationality').text(res.nationality);
                            $('#trainee_gender').text(res.gender);
                            $('#trainee_dob').text(res.dob);
                            $('#trainee_contact_no').text(res.contact_number);
                            $('#trainee_email_id').text(res.registered_email_id);
                            $('#trainee_building_street').text(res.personal_address_bldg+" "+res.personal_address_city
                                    +" "+res.personal_address_country+" "+res.personal_address_state+" "+res.personal_address_zip);
                            $('#trainee_tax_code').val(e);
                            $('#trainee_user_id').val(res.user_id);
                            $('#trainee_status').val(res.account_status);
                            $('#ex9').modal();
                        }
                        return false;
                    } else if (res.status == 2) {
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
            return validate(true);
        });
//        $('#edit_user_form select,#edit_user_form input').change(function() {
//            if (check == 1) {
//                return validate(false);
//            }
//        });
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
        reason_for_deactivation = $("#reason_for_deactivation").val();
        other_reason_for_deactivation = $("#other_reason_for_deactivation").val();

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

        if(retVal == true){
            $('.popup_cancel9 button[type=submit]').css('display','none');
        }
        return retVal;
    }
    $(".alphabets").keydown(function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 32]) !== -1 ||
                        (e.keyCode == 65 && e.ctrlKey === true) ||
                                (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return;
                }
                if (((e.keyCode < 65 || e.keyCode > 90))) {
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
         $('#trainee_info_button').click(function() {
            $('#ex9').addClass("trainee_details_height");
            $('.trainee_info').show();
            $('.multiSelectOptions').css({'width': '168px'});
            $('.multiSelect span').css({'width': '143px'});
            $('.trainee_info_div').hide();
            $('#ex9').modal();
            
        });
        $('.cancel').click(function() {
            $('.trainee_info').hide();
            $('#ex9').removeClass("trainee_details_height");
            $('.trainee_info_div').show();
        });
    });

    function reset_states(curr_country, curr_state, id) {
        $.post(baseurl + "internal_user/get_states_json", {country_param: curr_country}, function(data) {
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
    function validate_role() {
        var retVal = true;
        var user_role = '';
        $('input[name="trainee_role[]"]:checked').each(function() {
            user_role += $(this).val();
        })
        if (user_role.length == 0) {
            $("#trainee_role_err").text("[required]").addClass('error');
            $("#trainee_role").addClass('error');
            retVal = false;
        } else {
            $("#trainee_role_err").text("").removeClass('error');
            $("#trainee_role").removeClass('error');
        }
        var trainee_company_name = $.trim($("#trainee_company_name").val());        
        if (trainee_company_name == "") {
            $("#trainee_company_name_err").text("[required]").addClass('error');
            $("#trainee_company_name").addClass('error');
            retVal = false;
        } else {
            $("#trainee_company_name_err").text("").removeClass('error');
            $("#trainee_company_name").removeClass('error');
        }
        var trainee_doj = $("#trainee_doj").val();
        if (trainee_doj == "") {
            $("#trainee_doj_err").text("[required]").addClass('error');
            $("#trainee_doj").addClass('error');
            retVal = false;
        } else {
            $("#trainee_doj_err").text("").removeClass('error');
            $("#trainee_doj").removeClass('error');
        }
        var trainee_designation = $.trim($("#trainee_designation").val());
        if (trainee_designation == "") {
            $("#trainee_designation_err").text("[required]").addClass('error');
            $("#trainee_designation").addClass('error');
            retVal = false;
        } else {
            $("#trainee_designation_err").text("").removeClass('error');
            $("#trainee_designation").removeClass('error');
        }
        var trainee_email = $("#trainee_email").val();
        if (trainee_email == "") {
            $("#trainee_email_err").text("[required]").addClass('error');
            $("#trainee_email").addClass('error');
            retVal = false;
        } else {
            if (valid_email_address(trainee_email) == false) {
                $("#trainee_email_err").text("[invalid]").addClass('error');
                $("#trainee_email").addClass('error');
                retVal = false;
            } else {
                $("#trainee_email_err").text("").removeClass('error');
                $("#trainee_email").removeClass('error');
            }
        }
        var trainee_contact_number = $("#trainee_contact_number").val();
        if (trainee_contact_number != "" && valid_contact_number(trainee_contact_number) == false) {
            $("#trainee_contact_number_err").text("[invalid]").addClass('error');
            $("#trainee_contact_number").addClass('error');
            retVal = false;
        } else {
            $("#trainee_contact_number_err").text("").removeClass('error');
            $("#trainee_contact_number").removeClass('error');
        }
        var trainee_city = $.trim($("#trainee_city").val());
        if (trainee_city != '') {
            if (valid_name(trainee_city) == false) {
                $("#trainee_city_err").text("[invalid]").addClass('error');
                $("#trainee_city").addClass('error');
                retVal = false;
            } else {
                $("#trainee_city_err").text("").removeClass('error');
                $("#trainee_city").removeClass('error');
            }
        } else {
            $("#trainee_city_err").text("").removeClass('error');
            $("#trainee_city").removeClass('error');
        }
        var trainee_zip = $.trim($("#trainee_zip").val());
        if (trainee_zip != '') {
            if (valid_zip(trainee_zip) == false) {
                $("#trainee_zip_err").text("[invalid]").addClass('error');
                $("#trainee_zip").addClass('error');
                retVal = false;
            } else {
                $("#trainee_zip_err").text("").removeClass('error');
                $("#trainee_zip").removeClass('error');
            }
        } else {
            $("#trainee_zip_err").text("").removeClass('error');
            $("#trainee_zip").removeClass('error');
        }
        return retVal;
    } 
</script>
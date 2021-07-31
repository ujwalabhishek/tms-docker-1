<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/multiselectdropdown.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/multiselectdropdown.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/multidropdown.js"></script>
<?php
$this->load->helper('form');
$this->load->helper('metavalues_helper');
?>
<?php
if ($this->session->flashdata('error_message')) {
    echo '<div class="error1">' . $this->session->flashdata('error_message') . '</div>';
}
if (!empty($tax_error)) { 
    echo '<div class="error1">' . $tax_error . '</div>';
}
$atr = 'id="signupForm" name="validate_form"';
echo form_open_multipart("internal_user/add_user", $atr);
?>        
<div class="col-md-10">
    <?php echo validation_errors('<div class="error1">', '</div>'); ?>        
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/internal_user.png"/>  Internal Staff - Add New</h2>
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/personal_details.png"/> Personal Details</h2>

    <div class="table-responsive">
        <table class="table table-striped">    
            <tbody>
                <tr>
                    <td width="20%" class="td_heading">Country of Residence:<span class="required">*</span></td>
                    <td colspan="1" width="20%">        
                        <?php
                        $countries = fetch_metavalues_by_category_id(Meta_Values::COUNTRIES);
                        foreach ($countries as $item):
                            $country_options[$item['parameter_id']] = $item['category_name'];
                        endforeach;

                        $attr = 'id="country_of_residence"';
                        echo form_dropdown('country_of_residence', $country_options, $this->input->post('country_of_residence'), $attr);
                        ?>
                        <span id="country_of_residence_err"></span>              
                    </td>
                    <td class="td_heading" colspan="2" >
                        <SPAN id="IND" style="display:none;">PAN : <span class="required">* </span>
                            <?php
                            $attr = array('name' => 'PAN', 'class' => 'upper_case alphanumeric', 'id' => 'PAN', 'onblur' => 'javascript:isunique_pan(this.value,this.id);');
                            echo form_input($attr, $this->input->post('PAN'));
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
                            echo form_dropdown('NRIC', $nris_options, $this->input->post('NRIC'), $attr);
                            ?>
                            <span id="NRIC_err"></span>
                        </SPAN>              
                        <SPAN id="SGP_OTHERS" style="<?php if(empty($this->input->post('NRIC_OTHER'))){echo "display:none";}?>">
                            <br /><br />
                            <label id="SGP_OTHERS_label">NRIC Code: </label><span class="required">* </span>                  
                            <?php
                            $nric_other = fetch_metavalues_by_category_id(Meta_Values::NRIC_OTHER);
                            $nric_other_options[''] = 'Select';
                            foreach ($nric_other as $item):
                                $nric_other_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;

                            $attr = 'id="NRIC_OTHER"';
                            echo form_dropdown('NRIC_OTHER', $nric_other_options, $this->input->post('NRIC_OTHER'), $attr);
                            ?>
                            <span id="NRIC_OTHER_err"></span>
                        </SPAN>
                        <SPAN id="SGP_ID" style="<?php if(empty($this->input->post('NRIC_ID'))){echo "display:none";}?>">
                            <br /><br />
                            <label id="SGP_ID_label"> NRIC Code : </label><span class="required">* </span>                  
                            <?php
                            $attr = array('name' => 'NRIC_ID', 'class' => 'upper_case alphanumeric', 'id' => 'NRIC_ID', 'onblur' => 'javascript:isunique_pan(this.value,this.id);');
                            echo form_input($attr, $this->input->post('NRIC_ID'));
                            ?>
                            <span id="NRIC_ID_err"></span>                    
                        </SPAN>
                        <SPAN id="USA" style="<?php if(empty($this->input->post('SSN'))){echo "display:none";}?>">SSN : <span class="required">* </span>
                            <?php
                            $attr = array('name' => 'SSN', 'class' => 'upper_case alphanumeric', 'id' => 'SSN', 'onblur' => 'javascript:isunique_pan(this.value,this.id);');
                            echo form_input($attr, $this->input->post('SSN'));
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
                            'value' => $this->input->post('pers_first_name'),
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
                        echo form_dropdown('pers_gender', $gender_options, $this->input->post('pers_gender'), 'id="pers_gender"');
                        ?> 
                        <span id="pers_gender_err"></span>                 
                    </td>
                    <td class="td_heading">Date of Birth:</td>
                    <td>
                        <?php
                        $pers_dob = array(
                            'name' => 'pers_dob',
                            'id' => 'pers_dob',
                            'value' => $this->input->post('pers_dob'),
                            'readonly' => 'readonly',
                            'placeholder' => 'dd/mm/yyyy'
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
                            'value' => $this->input->post('pers_contact_number'),
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
                            'value' => $this->input->post('pers_alternate_contact_number'),
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
                            'value' => $this->input->post('user_registered_email'),
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
                            'value' => $this->input->post('pers_alternate_email'),
                            'onblur' => 'javascript:validate_alternate_email(this.value,this.id);',
                            'style' => 'width:200px',
                        );
                        echo form_input($attr);
                        ?>
                        <span id="pers_alternate_email_err"></span>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Highest Education:<span class="required">*</span></td>
                   <?php
                        $highest_educ_level = fetch_metavalues_by_category_id(Meta_Values::HIGHEST_EDUC_LEVEL);
                        $highest_educ_level_options[''] = 'Select';
                        foreach ($highest_educ_level as $item):
                            if(TENANT_ID == 'T20' || TENANT_ID == 'T17' || TENANT_ID == 'T18'){
                           
                                 $highest_educ_level_options[$item['parameter_id']] = $item['category_name'];
                                    
                            }else{
                                if($item['parameter_id']=='WSQDP_74' || $item['parameter_id']=='LSEC_20' || $item['parameter_id']=='UNIVFD_80'
                                    || $item['parameter_id']=='UNIVDD_90'|| $item['parameter_id']=='MNITEC_53' || $item['parameter_id']=='TWGC_36'||
                                    $item['parameter_id']=='WSQGD_93' || $item['parameter_id']=='TSD_32' ||  $item['parameter_id']=='WSQCERT_54' ||
                                      $item['parameter_id']=='WSQSD_75' ||  $item['parameter_id']=='WSQHC_55' ||  $item['parameter_id']=='WSQAC_73'
                                     ){
                           
                                    }else{
                                         $highest_educ_level_options[$item['parameter_id']] = $item['category_name'];
                                    }
                            }
                            
//                            $highest_educ_level_options[$item['parameter_id']] = $item['category_name'];
                        endforeach;
                        ?>
                    <td colspan="4">
                        <?php echo form_dropdown('highest_educ_level', $highest_educ_level_options, set_value('highest_educ_level'), 'id="highest_educ_level" style="width:100%"'); ?>
                        <span id="highest_educ_level_err"></span>
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
                    <td colspan="3">
                        <?php echo form_dropdown('sal_range', $sal_range_options, set_value('sal_range'), 'id="sal_range"'); ?>
                        <span id="sal_range_err"></span>
                    </td>
                </tr>
                <tr>        
                    <td class="td_heading">Upload Image:</td>            
                    <td>
                        <input type="file" name="userfile" id="userfile" onchange="showimagepreview(this)" />
                        <label id="image_err"></label>                
                    </td>
                    <td id="user_image_preview" colspan="3" class="td_heading">&nbsp;&nbsp;&nbsp;
                        <img height="120px" width="120px" id="imgprvw" border="0" />
                        &nbsp;&nbsp;&nbsp;
                        <span style="display: none;" id="removeimagebutton" onclick="remove_image();" class="glyphicon glyphicon-remove-circle"></span>                
                    </td>                 
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
                            'value' => $this->input->post('pers_personal_address_bldg'),
                            'class' => 'upper_case'
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
                            'value' => $this->input->post('pers_city'),
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
                        echo form_dropdown('pers_country', $country_options, $this->input->post(pers_country), $attr);
                        ?>
                        <span id="pers_country_err"></span>
                    </td>                    
                </tr>
                <tr>
                    <td class="td_heading">State:</td>
                    <td>                           
                        <?php
                        $attr = array('' => 'Select');
                        $country_param = $this->input->post('pers_country');
                        if ($country_param != '') {
                            $states = $this->internaluser->get_states($country_param);
                            foreach ($states as $item) {
                                $attr[$item->parameter_id] = $item->category_name;
                            }
                        }
                        $attr_js = 'id="pers_states"';
                        echo form_dropdown('pers_states', $attr, $this->input->post(pers_states), $attr_js);
                        ?>                        
                    </td>
                    <td class="td_heading">Postal Code:</td>
                    <td>
                        <?php
                        $attr = array(
                            'name' => 'pers_zip',
                            'id' => 'pers_zip',
                            'maxlength' => '10',
                            'value' => $this->input->post('pers_zip'),
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

    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/officail_details.png"/> Official Details</h2>          
    <div class="table-responsive">
        <table class="table table-striped">      
            <tbody>
                <tr>
                    <td class="td_heading" width="20%" >Company Name:<span class="required">*</span></td>
                    <td width="30%">
                        <?php
                        $attr = array(
                            'name' => 'emp_company_name',
                            'id' => 'emp_company_name',
                            'maxlength' => '50',
                            'value' => $this->input->post('emp_company_name'),
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
                            'value' => $this->input->post('emp_doj'),
                            'readonly' => 'readonly',
                            'placeholder' => 'dd/mm/yyyy'
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
                        <?php echo form_dropdown('emp_designation', $occupation_options, set_value('emp_designation'), 'id="emp_designation" style="width:170px"'); ?>
                        <span id="emp_designation_err"></span>                    
                    </td>
                   
                    <td class="occupation_others" colspan="2">
                        <?php 
                            $attr = array(
                            'name' => 'emp_designation_others',
                            'id' => 'emp_designation_others',
                            'maxlength' => '50',
                            'value' => $this->input->post('emp_designation_others'),
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
                            'value' => $this->input->post('emp_email'),
                            'onblur' => 'javascript:validate_office_email(this.value,this.id);',
                            'style' => 'width:200px',
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
                            'value' => $this->input->post('emp_contact_number'),
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
                    <td colspan="3" class="textarea_inp"> 
                        <?php
                        $attr = array(
                            'name' => 'emp_address',
                            'rows' => '2',
                            'cols' => '90',
                            'value' => $this->input->post('emp_address'),
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
                            'value' => $this->input->post('emp_city'),
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
                        echo form_dropdown('emp_country', $country_options, $this->input->post('emp_country'), $attr_js);
                        ?>          
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">State:</td>
                    <td>
                        <?php
                        $attr = array('' => 'Select');
                        $country_param = $this->input->post('emp_country');
                        if ($country_param != '') {
                            $states = $this->internaluser->get_states($country_param);
                            foreach ($states as $item) {
                                $attr[$item->parameter_id] = $item->category_name;
                            }
                        }
                        $attr_js = 'id="emp_states"';
                        echo form_dropdown('emp_states', $attr, $this->input->post(emp_states), $attr_js);
                        ?>              
                    </td>
                    <td class="td_heading">Postal Code:</td>
                    <td>
                        <?php
                        $attr = array(
                            'name' => 'emp_zip',
                            'id' => 'emp_zip',
                            'maxlength' => '10',
                            'value' => $this->input->post('emp_zip'),
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
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/other_details.png"/> Staff Other Details</h2>

    <div class="table-responsive">
        <table class="table table-striped">      
            <tbody>
                <tr>
                    <td class="td_heading" width="20%" >Staff Role:<span class="required">*</span></td>
                    <td width="30%">
                        <?php
                        $role_options[''] = 'Select';
                        foreach ($roles as $role):
                            $role_options[$role->role_id] = $role->role_name;
                        endforeach;
                        unset($role_options['COMPACT']);
                        $attr_js = 'id="user_role" multiple="multiple" size="5"';
                        echo form_dropdown('user_role[]', $role_options, $this->input->post('user_role'), $attr_js);
                        ?>
                        <span id="user_role_err"></span>
                    </td>
                    <td class="td_heading" width="20%" >Username:<span class="required">*</span></td>
                    <td>
                        <?php
                        $attr = array(
                            'name' => 'user_name',
                            'id' => 'user_name',
                            'maxlength' => '15',
                            'value' => $this->input->post('user_name'),
                            'onblur' => 'javascript:isunique_username(this.value,this.id);',
                            'style' => 'width:200px',
                        );
                        echo form_input($attr);
                        ?> 
                        <span id="user_name_err"></span>
                    </td>
                </tr>        
                <tr>          
                    <td class="td_heading">Activate User?</td>
                    <td colspan="4">
                        <?php
                        $active_checked = true;
                        $pendact_checked = false;
                        $activate_user = $this->input->post('activate_user');
                        if ($activate_user == 'PENDACT') {
                            $pendact_checked = true;
                            $active_checked = false;
                        }
                        $this->input->post('activate_user') === 'ACTIVE';

                        $attr = array(
                            'name' => 'activate_user',
                            'checked' => $active_checked,
                            'value' => 'ACTIVE'
                        );
                        echo form_radio($attr);
                        ?>Activate User Now &nbsp;&nbsp; 

                        <?php
                        $checked = $this->input->post('activate_user') == 'PENDACT';
                        $attr = array(
                            'name' => 'activate_user',
                            'checked' => $pendact_checked,
                            'value' => 'PENDACT'
                        );
                        echo form_radio($attr);
                        ?>Activate Later/ Pending Activation
                    </td>
                </tr>        
                <tr>
                    <td colspan="4" style="color:blue"><i>(On activation, mail will be sent to the user’s official email Id, with the username.)</i></td>
                </tr>        
            </tbody>
        </table>    
        <span class="required required_i">* Required Fields</span>
    </div>		  
    <div class="button_class99">
        <a class="small_text white_color">
            <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-saved"></span>&nbsp;Save</button>
        </a> &nbsp; &nbsp; 
    </div>    
</div>
<?php echo form_close(); ?>
<?php
$form_atr = 'id="role_transfer_form" name="role_transfer_form" onsubmit="return validate_role();"';
echo form_open("internal_user/trainee_role_change", $form_atr);
?>
<div class="modal1_trainee" id="ex8" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Assign Trainee as Internal Staff</h2>
    <div class="trainee_info_div">
        <div class="table-responsive">
            <div>
                <p>
                    This taxcode exists in our system.The user is currently a Trainee  in your training institute
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
        <span style="color:blue">Do you wish to upgrade this <b>Trainee</b> to an <b>Internal Staff</b> in your institute?</span>
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
                                'name' => 'trainee_Postal',
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
echo form_hidden('trainee_tax_code', "");
echo form_hidden('trainee_user_id', "");
echo form_hidden('trainee_status', "");
echo form_close();
?>
<script type="text/javascript">
    country_of_residence = $("#country_of_residence").val();
    $("#" + country_of_residence).show();
    $("#country_of_residence").change(function() {
        country_of_residence = $('#country_of_residence').val();
        if (country_of_residence == "") {
            $("#country_of_residence > option").each(function() {
                if (this.value != "") {
                    $("#" + this.value).hide();
                }
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
    /// added by shubhranshu to remove special character & space from NRIC client side
    $('#NRIC_ID').keyup(function(){
        $filter_nric = $('#NRIC_ID').val();
        $('#NRIC_ID').val($filter_nric.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, ''));
    });
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
               // maxDate: 0,
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
                $pers_states_select.html('<option value="">Select</option>');
                $.each(json_data, function(i, item) {
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
                $emp_states_select.html('<option value="">Select</option>');
                $.each(json_data, function(i, item) {
                    $emp_states_select.append('<option value="' + item.parameter_id + '">' + item.category_name + '</option>');
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
            var PAN = $("#PAN").val();
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
            var NRIC = $("#NRIC").val();
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
            var SSN = $("#SSN").val();
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
        var pers_first_name = $.trim($("#pers_first_name").val());
        if (pers_first_name == "") {
            $("#pers_first_name_err").text("[required]").addClass('error');
            $("#pers_first_name").addClass('error');
            retVal = false;
        }  else {
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
        var pers_contact_number = $.trim($("#pers_contact_number").val());
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
        var user_registered_email = $.trim($("#user_registered_email").val());        
        if (valid_email_address(user_registered_email) == false && user_registered_email != "") {
            $("#user_registered_email_err").text("[invalid]").addClass('error');
            $("#user_registered_email").addClass('error');
            retVal = false;
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
        var emp_company_name = $.trim($("#emp_company_name").val());
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
        }
        var emp_email = $.trim($("#emp_email").val());
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
        var user_name = $.trim($("#user_name").val());
        if (user_name == "") {
            retVal = false;
            $("#user_name_err").text("[required]").addClass('error');
            $("#user_name").addClass('error');
        }
        else {
            if (valid_user_name(user_name) == false) {
                retVal = false;
                $("#user_name_err").text("[invalid]").addClass('error');
                $("#user_name").addClass('error');
            }
        }
        if ($('span').hasClass('error')) {
            retVal = false;
        }
        if(retVal == true){
            $('.button_class99 button[type=submit]').css('display','none');
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

    function isunique_email(e, id) {
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
            $.ajax({
                url: baseurl + "internal_user/check_email_id",
                type: "post",
                data: 'email=' + e,
                success: function(res) {
                    if (res == 1) {
                        window.email_id = 'exists';
                        $("#" + id + "_err").text("[Email Id exists!]").addClass('error');
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
            var $country_of_residence = $.trim($('#country_of_residence').val());
            var $nric = $.trim($('#NRIC').val());
            $.ajax({
                url: baseurl + "internal_user/check_pan",
                type: "post",
                data: {pan_id: e, country_of_residence: $country_of_residence, nric: $nric},
                dataType: 'json',
                success: function(res) {                                       
                    if (res.status == 1) {
                        window.email_id = 'exists';
                        $("#" + id + "_err").text("[NRIC/FIN No. exists!]").addClass('error');
                        $("#" + id).addClass('error');
                        if (res.account_type == 'TRAINE' && res.tenant_id == tenant_id) {
                            if (res.account_status == 'ACTIVE') {
                                $('#account_status').text("'Active'");
                            } else if (res.account_status == 'INACTIV') {
                                $('#account_status').text("'In-Active'");
                                $('#inactive_id').show();
                            } else if (res.account_status == 'PENDACT') {
                                $('#account_status').text("'Pending Activation'");
                                $('#inactive_id').show();
                            }
                            $('#trainee_tax_code').text(e.toUpperCase());
                            //$('#trainee_first_name').text(res.first_name+' '+res.last_name);
                            // commented by shubhranshu due to null issue
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
                            $('#ex8').modal();
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
    function isunique_username(e, id) {
        e = $.trim(e);
        if (e == '') {
            $("#" + id + "_err").text("[required]").addClass('error');
            $("#" + id).addClass('error');
            return false;
        } else if (valid_user_name(e) == false) {
            $("#" + id + "_err").text("[invalid]").addClass('error');
            $("#" + id).addClass('error');
        } else {
            $.ajax({
                url: baseurl + "internal_user/check_username",
                type: "post",
                data: 'username=' + e,
                success: function(res) {
                    if (res == 1) {
                        window.username = 'exists';
                        $("#" + id + "_err").text("[Username exists!]").addClass('error');
                        $("#" + id).addClass('error');
                        return false;
                    } else {
                        window.username = 'notexists';
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
        $('#signupForm').submit(function() {
            check = 1;
            return validate(true);
        });
        $('#signupForm select,#signupForm input').change(function() {
            if (check == 1) {
                return validate(false);
            }
        });
        
        ///////below ajax code was added by shubhranshu to checkif the company mail id exis?
        $('#emp_email').blur(function(){
            $emp_email= $('#emp_email').val();
            var id ='emp_email';
            $.ajax({
                    url: baseurl + "internal_user/check_unique_useremail_client",
                    type: "post",
                    data: {'emp_email': $emp_email},
                    success: function(res) {
                        if (res == 1) {
                           
                            $("#" + id + "_err").text("[Exists!]").addClass('error');
                            $("#" + id).addClass('error');
                            return false;
                        } else {
                            
                            $("#" + id + "_err").text("").removeClass('error');
                            $("#" + id).removeClass('error');
                            return true;
                        }
                    },
                    error: function() {
                        return false;
                    }
                });

        }) 
    });
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
            $('#signupForm').each(function() {
                this.reset();
            });
            $('#country_of_residence').trigger('change');
            $('#emp_states').find('option').remove().end().append('<option value="">Select</option>');
            $('#pers_states').find('option').remove().end().append('<option value="">Select</option>');
        });
        $('#trainee_info_button').click(function() {
            $('#ex8').addClass("trainee_details_height");
            $('.trainee_info').show();
            $('.multiSelectOptions').css({'width': '168px'});
            $('.multiSelect span').css({'width': '143px'});
            $('.trainee_info_div').hide();
            $('#ex8').modal();
        });
        $('.cancel').click(function() {            
            $('.trainee_info').hide();
            $('#ex8').removeClass("trainee_details_height");
            $('.trainee_info_div').show();
            $('#role_transfer_form').each(function() {
                this.reset();
            });
        });
    });
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
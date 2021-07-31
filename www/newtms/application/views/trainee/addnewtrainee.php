<?php 
$this->load->helper('form');
$this->load->helper('metavalues_helper');
$this->load->helper('common_helper');
$this->load->model('meta_values');
if ($user->role_id == 'ADMN' || $user->role_id == 'COMPACT') {
    $race_colspan = 1;
} else {
    $race_colspan = 3;
}
if (!empty($tax_error)) { 
    echo '<div class="error1">' . $tax_error . '</div>';
}
//Added by abdulla
$tenant_id = $this->session->userdata('userDetails')->tenant_id;
?>
<div class="col-md-10">
    <?php echo validation_errors('<div class="error1">', '</div>'); ?>   
    <?php
    $atr = 'id="trainee_form" name="trainee_form"';
    echo form_open_multipart("trainee/add_new_trainee", $atr);
    ?>

    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/trainee.png"/> Trainee - Add New</h2>
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/other_details.png"/> Access Detail</h2>
    <div id ='trainee_validation_div'>
        <div class="bs-example">
            <div class="table-responsive">
                <table class="table table-striped">

                    <tbody>
                        <tr>
                            <td width="20%" class="td_heading">Country of Residence:<span class="required">*</span></td>
                            <td colspan="1" width="15%">        
                                <?php
                                $countries = fetch_metavalues_by_category_id(Meta_Values::COUNTRIES);
                                $country_options = array();
                                foreach ($countries as $item):
                                    $country_options[$item['parameter_id']] = $item['category_name'];
                                endforeach;
                                echo form_dropdown('country_of_residence', $country_options, set_value('country_of_residence'), 'id="country_of_residence"');
                                ?>
                                <span id="country_of_residence_err"></span>
                            </td>
                            <td class="td_heading" colspan="2">
                                <SPAN id="SGP" style="">NRIC Type : <span class="required">* </span>   <!--Modified dummy on 10/03/15--->               
                                    <?php
                                    $nrics = fetch_metavalues_by_category_id(Meta_Values::NRIC);
                                    $nris_options[''] = 'Select';
                                    foreach ($nrics as $item):
                                        $nris_options[$item['parameter_id']] = $item['category_name'];
                                    endforeach;
                                    $nris_options['SNG_4'] = 'NO TAX CODE';
                                    $attr = 'id="NRIC"';
                                    echo form_dropdown('NRIC', $nris_options, $this->input->post('NRIC'), $attr);
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

                                    $attr = 'id="NRIC_OTHER" class="tax_code"';
                                    echo form_dropdown('NRIC_OTHER', $nric_other_options, $this->input->post('NRIC_OTHER'), $attr);
                                    ?>
                                    <span id="NRIC_OTHER_err"></span>
                                </SPAN>
                                <SPAN id="SGP_ID" style="display:none;">
                                    <label id="SGP_ID_label"> NRIC Code : </label><span class="required">* </span>                  
                                    <?php
                                    $attr = array('name' => 'NRIC_ID', 'autocomplete' => "off", 'class' => 'upper_case alphanumeric', 'id' => 'NRIC_ID', 'onblur' => 'javascript:isunique_taxcode(this.value,this.id);', 'maxlength' => '50');
                                    echo form_input($attr, $this->input->post('NRIC_ID'));
                                    ?>
                                    <span id="NRIC_ID_err"></span>
                                </SPAN>
                            </td>   
                            </td>

                        </tr>
                        <tr>
                            <td width="20%" class="td_heading">Username:<span class="required">*</span></td>
                            <td colspan="4">
                                <?php
                                $un = array(
                                    'name' => 'user_name',
                                    'id' => 'user_name',
                                    'maxlength' => '15',
                                    'value' => set_value('user_name'),
                                    'onblur' => 'javascript:isunique_username(this.value,this.id);',
                                    'style' => 'width:200px',
                                );
                                echo form_input($un);
                                ?> 
                                <span id="user_name_err"></span>
                            </td>
                        </tr>       
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        <h2 class="sub_panel_heading_style">
            <img src="<?php echo base_url(); ?>/assets/images/personal_details.png"/> Personal Details
            <?php if($this->session->userdata('userDetails')->role_id != 'COMPACT') { ?>
                <span class="pull-right"> <img src="<?php echo base_url(); ?>/assets/images/personal_details.png"> <a href="#ex3" rel="modal:open" style="color: blue;">Individual Discount</a></span>
            <?php } ?>
        </h2>
        <div class="table-responsive">
            <table class="table table-striped">      
                <tbody>               
                    <tr>
                        <td class="td_heading" width="20%">Name:<span class="required">*</span></td>
                        <td colspan="5">
                            <?php
                            $attr = array(
                                'name' => 'pers_first_name',
                                'id' => 'pers_first_name',
                                'maxlength' => '100',
                                'value' => set_value('pers_first_name'),
                                'class' => 'upper_case',
                                'autocomplete' => "off",
                                'style' => 'width:250px',
                            );
                            echo form_input($attr, 'javascript: text-transform: uppercase;');
                            ?>  
                            <span id="pers_first_name_err"></span>
                        </td>                        
                     </tr>
                     <tr>
                        <td class="td_heading">Nationality:<span class="required">*</span></td>
                        <td colspan="5">        
                            <?php
                            $nationality = fetch_metavalues_by_category_id(Meta_Values::NATIONALITY);                                    
                            $nationality_options = array();
                            $nationality_options[''] = 'Select';
                            foreach ($nationality as $item):
                                $nationality_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;                            
                            echo form_dropdown('nationality', $nationality_options, set_value('nationality'), 'id="nationality" style="width:170px"');
                            ?>
                            <span id="nationality_err"></span>
                        </td>  
                     </tr>
                    <tr>
                        <td class="td_heading" width="20%">Gender:<span class="required">*</span></td>
                        <td>
                            <?php
                            $gender = fetch_metavalues_by_category_id(Meta_Values::GENDER);
                            $gender_options = array();
                            $gender_options[''] = 'Select';
                            foreach ($gender as $item):
                                $gender_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;
                            echo form_dropdown('pers_gender', $gender_options, set_value('pers_gender'), 'id="pers_gender"');
                            ?> 
                            <span id="pers_gender_err"></span>
                        </td>
                        <td class="td_heading">Date of Birth:
						<?php if($tenant_id=='T24'){ ?>
							<span class="required">*
						<?php } ?>
						</td>
                        <td>
                            <?php
                            $pers_dob = array(
                                'name' => 'pers_dob',
                                'id' => 'pers_dob',
                                'maxlength' => '10',
                                'value' => set_value('pers_dob'),
                                'placeholder' => 'dd-mm-yyyy',
                            );
                            echo form_input($pers_dob);
                            ?> 
                            <span id="pers_dob_err"></span>
                        </td>
                        <td class="td_heading">Contact #:<span class="required">*</span></td>
                        <td>
                            <?php
                            $attr = array(
                                'name' => 'pers_contact_number',
                                'id' => 'pers_contact_number',
                                'maxlength' => '50',
                                'value' => set_value('pers_contact_number'),
                                'style' => 'width:200px',
                            );
                            echo form_input($attr);
                            ?> 
                            <span id="pers_contact_number_err"></span>
                        </td>                    
                    </tr>                
                    <tr>
                        <td class="td_heading">Alt. Contact #:</td>
                        <td colspan='2'>
                            <?php
                            $attr = array(
                                'name' => 'pers_alternate_contact_number',
                                'id' => 'pers_alternate_contact_number',
                                'maxlength' => '50',
                                'value' => set_value('pers_alternate_contact_number'),
                                'style' => 'width:250px',
                            );
                            echo form_input($attr);
                            ?>    
                            <span id="pers_alternate_contact_number_err"></span>
                        </td>
                        <td class="td_heading">Race:</td>
                        <?php
                        $race = fetch_metavalues_by_category_id(Meta_Values::RACE);
                        $race_options[''] = 'Select';
                        foreach ($race as $item):
                            $race_options[$item['parameter_id']] = $item['category_name'];
                        endforeach;                        
                        ?>
                        <td colspan="2">
                            <?php echo form_dropdown('race', $race_options, set_value('race'), 'style="width:150px" id="race"'); ?>
                        </td>
                    </tr>      
                    <tr>
                         <?php if ($user->role_id == 'ADMN' || $user->role_id=='CRSEMGR' || $user->role_id == 'SLEXEC') { ?>
                            <td class="td_heading">Assign Trainee to Company:</td>
                            <?php
                            $compnies = getcompnies();
                            if ($user->role_id == 'ADMN' || $user->role_id == 'CRSEMGR' || $user->role_id == 'SLEXEC') {
                                $company_options[''] = 'Select';
                            }
                            foreach ($compnies as $item):
                                $id_name=$item['company_id']."/".$item['company_name'];
                                
                                 $company_options[$id_name] = $item['company_name'];
                                
                           
                            endforeach;

                            ?>
                            <td colspan='5'>
                                <?php echo form_dropdown('assign_company', $company_options, set_value('assign_company'), 'id="assign_company" style="width:450px"'); ?>
                               
                            </td>
                        <?php } ?>
                    </tr>
                    <!--add by pritam -->
                    <tr id="cert_sent_t" style="display:none;">
                         <?php 
                            if ($user->role_id == 'ADMN' || $user->role_id=='CRSEMGR' || $user->role_id == 'SLEXEC') 
                            {   ?>
                                <td class="td_heading" >Certificate Sent To:</td>
                                    <?php
                                    $compnies = getcompnies();
                                    if ($user->role_id == 'ADMN' || $user->role_id == 'CRSEMGR' || $user->role_id == 'SLEXEC') 
                                    {
                                        $company_options[''] = 'Select';
                                    }
                                    foreach ($compnies as $item):
                                        $company_options[$item['company_id']] = $item['company_name'];
                                    endforeach;
                                    ?>
                                <td colspan='5'>
                                    <?php 
                                            echo form_dropdown('cert_sent_to', 
                                                $company_options, set_value('cert_sent_to'), 
                                                'id="cert_sent_to" style="width:450px;display:none;"'); ?>
                                </td>
                            <?php 
                            } 
                            ?>
                    </tr>
                    <tr id="cert_sent_to_others" style="display:none;">
                         <?php 
                            if ($user->role_id == 'ADMN' || $user->role_id=='CRSEMGR' || $user->role_id == 'SLEXEC') 
                            {   ?>
                                <td class="td_heading" >Certificate Sent To:</td>
                                <td colspan='5'>
                                    <?php 
                                            $data= array(
                                                    'name'=> 'cert_sent_to_others',
                                                    'id'  => 'cert_sent_to_others',
                                                    'style' => 'display:none',
                                                     
                                            );
                                            echo form_input($data); ?>
                                </td>
                            <?php 
                            } 
                            ?>
                    </tr>
                    <!--                    //end-->

                    <tr>                    
                        <td class="td_heading">Salary Range:<span id="span_sal_range"></span></td>
                        <?php
                        $sal_range = fetch_metavalues_by_category_id(Meta_Values::SAL_RANGE);
                        $sal_range_options[''] = 'Select';
                        foreach ($sal_range as $item):
                            $sal_range_options[$item['parameter_id']] = $item['category_name'];
                        endforeach;
                        ?>
                        <td>
                            <?php echo form_dropdown('sal_range', $sal_range_options, set_value('sal_range'), 'id="sal_range"'); ?>
                            <span id="sal_range_err"></span>
                        </td>
                        <td class="td_heading">Designation:<span id="span_occupation"></span></td>
                        <?php
                        $occupation = fetch_metavalues_by_category_id(Meta_Values::OCCUPATION);
                        $occupation_options[''] = 'Select';
                        
                        if(TENANT_ID=='T17' || TENANT_ID=='T20' || TENANT_ID=='T18'){
                            foreach ($occupation as $item):
                             
                                        $occupation_options[$item['parameter_id']] = $item['category_name'];
                                    
                            //$occupation_options[$item['parameter_id']] = $item['category_name'];
                        endforeach;
                        }else{
                            foreach ($occupation as $item):
                             if($item['parameter_id']=='WNCLBO_10' || $item['parameter_id']=='CLWRK_04' || 
                                     $item['parameter_id']=='SWASAM_05'){
                           
                                    }else{
                                         $occupation_options[$item['parameter_id']] = $item['category_name'];
                                    }
                            //$occupation_options[$item['parameter_id']] = $item['category_name'];
                        endforeach;
                        }
                        
                        ?>
                        <td colspan="3">
                            <?php echo form_dropdown('occupation', $occupation_options, set_value('occupation'), 'id="occupation" style="width:170px"'); ?>
                            <span id="occupation_err"></span>
                        </td>
                        
                    </tr>

                    <tr>
                        <td class="td_heading">Email Activation:<span class="required">*</span></td>
                        <td colspan="5">
                            <?php
                            $email_act = fetch_metavalues_by_category_id(Meta_Values::EMAIL_ACT);
                            foreach ($email_act as $item) {
                                $BPEMAC['value'] = $item['parameter_id'];
                                $EMACRQ ['value'] = $item['parameter_id'];
                            }
                            $bypass = array(
                                'name' => 'bypassemail',
                                'id' => 'bypassemail_1',
                                'checked' => TRUE,
                                'value' => BPEMAC,
                                'class' => 'email'
                            );
                            $donotbypass = array(
                                'name' => 'bypassemail',
                                'id' => 'bypassemail_2',
                                'value' => EMACRQ,
                                'class' => 'email'
                            );
                            ?>
                            <?php echo form_radio($bypass); ?>By-pass email activation &nbsp;&nbsp; 
                            <?php echo form_radio($donotbypass); ?> Do not By-pass email activation
                        </td>    
                    </tr>
                    <tr>
                        <td class="td_heading">Email Id:<span id="span_email_id" class="required" style="display:none">*</span></td>
                        <?php
                        $email = array(
                            'name' => 'user_registered_email',
                            'id' => 'user_registered_email',
                            'maxlength' => '50',
                            'value' => set_value('user_registered_email'),
                            'onblur' => 'javascript:isunique_email(this.value,this.id);',
                            'style' => 'width:250px',
                        );
                        $conf_email = array(
                            'name' => 'pers_conf_email',
                            'id' => 'pers_conf_email',
                            'maxlength' => '50',
                            'value' => set_value('pers_conf_email'),
                            'onblur' => 'javascript:confirm_email(this.value);',
                            'style' => 'width:250px',
                        );
                        
                        ?>
                        <td colspan="2"><?php echo form_input($email); ?> <span id="user_registered_email_err"> </span></td>
                        <td class="td_heading">Confirm Email Id:<span id="span_confirm_email_id" class="required" style="display:none">*</span></td>
                        <td colspan="2"><?php echo form_input($conf_email); ?><span id="pers_conf_email_err"></span></td>
                    </tr>
                    <tr>
                        <?php
                        $alt_email = array(
                            'name' => 'pers_alternate_email',
                            'id' => 'pers_alt_email',
                            'maxlength' => '50',
                            'value' => set_value('pers_alt_email'),
                            'onblur' => 'javascript:validate_alternate_email(this.value,this.id);',
                            'style' => 'width:250px',
                        );
                        ?>
                        <td class="td_heading">Alternate Email Id:</td>
                        <td colspan="5"><?php echo form_input($alt_email); ?><span id="pers_alt_email_err"></span></td>
 
                    </tr>    
                    <tr>
                        <td class="td_heading">Highest Education Level:<span class="required">*</span></td>
                        <?php
                        $highest_educ_level = fetch_metavalues_by_category_id(Meta_Values::HIGHEST_EDUC_LEVEL);
                        $highest_educ_level_options[''] = 'Select';
                        foreach ($highest_educ_level as $item):
                            if($user->tenant_id == 'T20' || $user->tenant_id == 'T17' || TENANT_ID=='T18'){
                           
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
                        <td colspan="5">
                            <?php echo form_dropdown('highest_educ_level', $highest_educ_level_options, set_value('highest_educ_level'), 'id="highest_educ_level" style="width:900px;"'); ?>
                            <span id="highest_educ_level_err"></span>
                        </td>
                    </tr>    
                    <tr>
                        <td class="td_heading">Certificate Pickup Detail:</td>
                        <?php
                        $cerit_mail = array(
                            'name' => 'certificate_pick_pref',
                            'value' => 'cerit_mail'
                        );
                        $cerit_self = array(
                            'name' => 'certificate_pick_pref',
                            'checked' => TRUE,
                            'value' => 'cerit_self'
                        );
                        $cerit_post = array(
                            'name' => 'certificate_pick_pref',
                            'value' => 'cerit_post'
                        );
                        ?>
                        <td colspan="5"><?php echo form_radio($cerit_mail); ?> Mail to my personal email Id &nbsp;&nbsp; 
                            <?php echo form_radio($cerit_self); ?> I will pickup myself &nbsp;&nbsp; 
                            <?php echo form_radio($cerit_post); ?> Mail to my postal address    
                        </td>     
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/address.png"/> Address</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td class="td_heading" width="15%">Building/Street:
						<?php if($tenant_id=='T24'){ ?>
							<span class="required">*
						<?php } ?>
						</td>
                        <?php
                        $p_addr = array(
                            'name' => 'pers_personal_address_bldg',
                            'id' => 'pers_personal_address_bldg',
                            'maxlength' => '255',
                            'rows' => '1',
                            'cols' => '70',
                            'class' => 'upper_case'
                        );
                        ?>
                        <td colspan="3" width="50%"><?php echo form_textarea($p_addr); ?>
							<span id="pers_personal_address_bldg_err"></span>
						</td>
                        <td class="td_heading" width="8%">City:
						<?php if($tenant_id=='T24'){ ?>
							<span class="required">*
						<?php } ?>
						</td>
                        <?php
                        $city = array(
                            'name' => 'pers_city',
                            'id' => 'pers_city',
                            'maxlength' => '50',
                            'value' => set_value('pers_city'),
                            'class' => 'upper_case alphabets',
                            'autocomplete' => "off",
                             'style' => 'width:250px',
                        );
                        ?>
                        <td width="20%">
                            <?php echo form_input($city); ?>
                            <span id="pers_city_err"></span>
                        </td>                    
                    </tr>
                    <tr>
                        <td class="td_heading">Country:
						<?php if($tenant_id=='T24'){ ?>
							<span class="required">*
						<?php } ?>
						</td>
                        <td width="25%">
                            <?php
                            $attr = 'id="pers_country"';
                            echo form_dropdown('pers_country', $country_options, set_value(pers_country), $attr);
                            ?>
                            <span id="pers_country_err"></span>
                        </td>

                        <td class="td_heading">State:
						<?php if($tenant_id=='T24'){ ?>
							<span class="required">*
						<?php } ?>
						</td>
                        <td>                           
                            <?php
                            $attr = array('' => 'Select');
                            $attr_js = 'id="pers_states"';
                            echo form_dropdown('pers_states', $attr, set_value(pers_states), $attr_js);
                            ?>
							<span id="pers_states_err"></span>
                        </td>

                        <td class="td_heading">Postal Code:
						<?php if($tenant_id=='T24'){ ?>
							<span class="required">*
						<?php } ?>
						</td>
                        <?php
                        $zip = array(
                            'name' => 'pers_zip_code',
                            'id' => 'pers_zipcode',
                            'value' => '',
                            'maxlength' => '10',
                            'class' => 'alphanumeric upper_case',
                            'autocomplete' => "off"
                        );
                        ?>
                        <td><?php echo form_input($zip); ?>
                            <span id="pers_zipcode_err"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Upload Photo:</td>
                        <td>
                            <input type="file" name="userfile" id="userfile" onchange="showimagepreview(this)" />
                            <label id="image_err"></label>
                        </td>
                        <td colspan="4" class="td_heading">&nbsp;&nbsp;&nbsp;
                            <div id="user_image_preview">
                                <img height="120px" width="120px" id="imgprvw" border="0" />
                                &nbsp;&nbsp;&nbsp;
                                <span style="display: none;" id="removeimagebutton" onclick="remove_image();" class="glyphicon glyphicon-remove-circle"></span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <br>
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/education.png"/> Educational Details 
        <span class="label label-default push_right black-btn" onclick="javascript:addmore('edu')"><span class="glyphicon glyphicon-plus glyphicon1"></span> Add More</span> 
    </h2>
    <div class="table-responsive">
        <table id="edu_tbl" class="table table-striped">
            <thead>
                <tr>
                    <th width="20%">Level</th>
                    <th width="20%">Year of Completion</th>
                    <th width="20%">Score/Grade</th>
                    <th width="40%">Remarks</th>
                </tr>
            </thead>
            <?php
            $edulevel = fetch_metavalues_by_category_id(Meta_Values::HIGHEST_EDUC_LEVEL);
            $edulevel_options[''] = 'Select';
            foreach ($edulevel as $item):
                $edulevel_options[$item['parameter_id']] = $item['category_name'];
            endforeach;
            $attr_edulevel = 'id="edu_level';
            $edu_level = form_dropdown('edu_level[]', $edulevel_options, $attr_edulevel, 'id="edu_firstcolumn_0" class="edu_level"');

            $score_grade = array(
                'name' => 'edu_score_grade[]',
                'id' => 'edu_score_grade_0',
                'value' => set_value('score_grade'),
                'maxlength' => '10',
            );
            $year_of_comp = array(
                'name' => 'edu_year_of_comp[]',
                'id' => 'year_of_comp_0',
                'maxlength' => '4',
                'placeholder' => 'yyyy',
                'value' => set_value('year_of_comp'),
                'readonly' => true
            );
            $edu_remarks = array(
                'name' => 'edu_remarks[]',
                'id' => 'edu_remarks_0',
                'maxlength' => '50',
                'value' => set_value('edu_remarks'),
                'style' => 'width:100%',
                'rows' => '1',
                'class' => 'upper_case'
            );
            ?>
            <tbody id="addedu">
                <tr id="edu_row_0">
                    <td><div class="form-drop"><?php echo $edu_level; ?><span id="edu_err0"></span></div></td>
                    <td><?php echo form_input($year_of_comp); ?>
                        <span  id="yr_comp_err_0"></span>
                    </td>
                    <td><?php echo form_input($score_grade); ?></td>
                    <td><?php echo form_textarea($edu_remarks); ?> <span style='display:none' class="pull-right remove_img remove2" onClick="javascript:remove_row('edu_remove_0');"></span> </td>
                </tr>
            </tbody>
        </table>
        <span id="addedu_err"></span>
    </div>
    <br>
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/personal_details.png"/> Other Certificates and Trainings 
        <span class="label label-default push_right black-btn" onclick="javascript:addmore('other')"><span class="glyphicon glyphicon-plus glyphicon1"></span> Add More</span> 
    </h2>
    <div class="table-responsive">
        <table class="table table-striped" id="other_tbl">
            <thead>
                <tr>
                    <th width="30%">Certificate Name</th>
                    <th width="15%">Year of Cert.</th>
                    <th width="15%">Validity</th>
                    <th width="40%">Remarks</th>
                </tr>
            </thead>
            <?php
            $certi_name = array(
                'name' => 'oth_certi_name[]',
                'id' => 'other_firstcolumn_0',
                'class' => 'certi_name',
                'rowno' => '0',
                'maxlength' => '50',
                'value' => set_value('certi_name'),
                'class' => 'upper_case',
                'autocomplete' => "off",
                  'style' => 'width:250px',
            );
            $year_of_certi = array(
                'name' => 'oth_year_of_certi[]',
                'id' => 'year_of_certi_0',
                'maxlength' => '4',
                'placeholder' => 'yyyy',
                'value' => set_value('year_of_certi'),
                'readonly' => true
            );
            $validity = array(
                'name' => 'oth_validity[]',
                'id' => 'validity_0',
                'placeholder' => 'dd-mm-yy',
                'maxlength' => '25',
                'value' => set_value('validity'),
                'readonly' => true
            );
            $other_remarks = array(
                'name' => 'oth_remarks[]',
                'id' => 'other_remarks_0',
                'maxlength' => '50',
                'value' => set_value('other_remarks'),
                'class' => 'upper_case',
                'style' => 'width:100%',
                'rows' => '1',
            );
            ?>
            <tbody id="addother">
                <tr  id="other_row_0">
                    <td><?php echo form_input($certi_name); ?><span id="other_err0"></td>
                    <td><?php echo form_input($year_of_certi); ?></td>
                    <td><?php echo form_input($validity); ?></td>
                    <td><?php echo form_textarea($other_remarks); ?> <span style='display:none' class="pull-right remove_img remove2" onClick="javascript:remove_row('other_remove_0');"></span> </td>
                </tr>
            </tbody>

        </table>
        <span id="addother_err"></span>
    </div>
    <br>
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/officail_details.png"/> Work Experience 
        <span class="label label-default push_right black-btn" onclick="javascript:addmore('work')"><span class="glyphicon glyphicon-plus glyphicon1"></span> Add More</span> 
    </h2>
    <div class="table-responsive">
        <table class="table table-striped" id="work_tbl">
            <thead>
                <tr>
                    <th width="30%">Name of Organization</th>
                    <th width="15%">Employment From</th>
                    <th width="15">Employment To</th>
                    <th width="40%">Designation</th>
                </tr>
            </thead>
            <?php
            $org_name = array(
                'name' => 'work_org_name[]',
                'id' => 'work_firstcolumn_0',
                'class' => 'org_name',
                'rowno' => '0',
                'maxlength' => '50',
                'value' => set_value('org_name'),
                'class' => 'upper_case',
                'autocomplete' => "off",
                  'style' => 'width:250px',
            );

            $empfrom = array(
                'name' => 'work_empfrom[]',
                'id' => 'empfrom_datetimepicker_0',
                'maxlength' => '25',
                'placeholder' => 'dd-mm-yy',
                'value' => set_value('empfrom'),
                'readonly' => true
            );
            $empto = array(
                'name' => 'work_empto[]',
                'id' => 'empto_datetimepicker_0',
                'maxlength' => '25',
                'placeholder' => 'dd-mm-yy',
                'value' => set_value('empto'),
                'readonly' => true
            );
            $designation = fetch_metavalues_by_category_id(Meta_Values::DESIGNATION);
            $designation_options = array();
            $designation_options[''] = 'Select';
            foreach ($designation as $item):
                $designation_options[$item['parameter_id']] = $item['category_name'];
            endforeach;
            $attr_designation = 'id="designation_0"';
            $designation = form_dropdown('work_designation[]', $designation_options, set_value('work_designation'), $attr_designation);
            ?>
            <tbody id="addwork">
                <tr  id="work_row_0">
                    <td><?php echo form_input($org_name); ?><span id="work_err0"></td>
                    <td><?php echo form_input($empfrom); ?></td>
                    <td><?php echo form_input($empto); ?></td>
                    <td><?php echo $designation; ?> <span style='display:none' class="pull-right remove_img remove2" onClick="javascript:remove_row('work_remove_0');"></span> </td>
                </tr>

            </tbody>
        </table>  
        <span id="addwork_err"></span>
    </div>
    </br>
    <span colspan="4" id="span_activate_user" style="display:''">
        <?php
        $now = array(
            'name' => 'activate_user',
            'checked' => TRUE,
            'value' => 'ACTIVE'
        );
        $later = array(
            'name' => 'activate_user',
            'value' => 'PENDACT'
        );
        ?>
        <?php echo form_radio($now); ?>Activate Trainee Now &nbsp;&nbsp; 
        <?php echo form_radio($later); ?> Activate Trainee Later
    </span>
    <span colspan="4">
        <i id="BPEMAC_content">(On activation, mail will be sent to the trainee's email Id, with the username.)</i>
        <i id="EMACRQ_content" style='display: none'>(An activation mail will be sent to the trainee's email Id, with the username and password.
            Trainee will be required to click on the activation link to get the account activated.)</i>
    </span>
    <br>
    <span class="required required_i">* Required Fields</span>
    <div class="button_class99">
        <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-saved"></span>&nbsp;Save</button> &nbsp; &nbsp;
    </div>   
    <?php if (empty($courses)) { ?>
        <div class="modal0000" id="ex3" style="display:none;">
            <p>
            <h2 class="panel_heading_style">Alert Message</h2>
            There are no courses available.<br>
            <div class="popup_cancel popup_cancel001">
                <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
            </p>
        </div>
    <?php } else { ?>
        <div class="modal_333" id="ex3" style="display:none;">
            <p>
            <h2 class="panel_heading_style">Individual Discount by Course</h2>
            <div class="table-responsive payment_scroll" style="height: 250px;">
                <table style="width:100%; margin:0 auto;" class="table table-striped cpayment_received">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Discount %</th>
                            <th>Discount Amt. (SGD)</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($courses as $k => $row):
                            echo "<tr>"
                            . "<td>" . $row . "</td>"
                            . "<td><input type='text' maxlength='10' id='indi_disc_" . $k . "' class='indi_disc' data-key='" . $k . "' value='' name='indi_disc[" . $k . "]'>%<span id='indi_disc_" . $k . "_err'></span></td>"
                            . "<td>$ <input type='text' maxlength='10' id='indi_disc_amt_" . $k . "' class='indi_disc_amt' data-key='" . $k . "' value='' name='indi_disc_amt[" . $k . "]'> <span id='indi_disc_amt_" . $k . "_err'></span></td>"
                            . "</tr>";
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
            <br>
            <div class="popup_cance89">
                <a rel="modal:close" href="#"><button type="button" class="btn btn-primary disc_save">Save</button></a>
            </div>
            </p>
        </div>
    <?php } ?>
    <?php echo form_close(); ?>       

    <div class="modal" id="ex1" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Heading Goes Here...</h2>
        Detail Goes here.  <br>
        <div class="popup_cancel">
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a></div></p>
    </div>
    <div class="modal1_051" id="ex9" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Deactivate Contact</h2>
        <strong> De-Activation Date:<span class="red">*</span> </strong><input type="date" style="width:25%;" placeholder="dd-mm-yy">&nbsp; <a href="#"><img border="0" src="images/date_icon.png"></a><br><br>
        <strong>Reason for De-Activation:<span class="red">*</span></strong> <select name="type" id="type11"><option>Select</option>
            <option>De-Activation Reason 01</option>
            <option>De-Activation Reason 02</option>
            <option name="parcel" value="parcel_01">OTHERS</option>
        </select> &nbsp; <div id="row_dim_new11" style="float:right; margin-right:20%;"><input type="text" value="" style="width:150%;"></div>
        <br><br>
        Are you sure you want to deactivate this Contact?  <br>
        <span class="required required_i">* Required Fields</span>
        <div class="popup_cancel9">
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Yes</button></a>&nbsp;&nbsp;<a href="#" rel="modal:close"><button class="btn btn-primary" type="button">No</button></a></div></p>
    </div>
    <div class="modal1_051" id="ex5" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Reactivate Contact</h2>

        <strong> Re-Activation Date:<span class="red">*</span> </strong><input type="date" style="width:45%;" placeholder="dd-mm-yy">&nbsp; <a href="#"><img border="0" src="images/date_icon.png"></a><br><br>
        <strong>Reason for Re-Activation:<span class="red">*</span></strong> <select name="type" id="type111"><option>Select</option>
            <option>Re-Activation Reason 01</option>
            <option>Re-Activation Reason 02</option>
            <option name="parcel" value="parcel_011">OTHERS</option>
        </select> &nbsp; <div id="row_dim_new111" style="float:right; margin-right:20%;"><input type="text" value="" style="width:150%;"></div>
        <br><br>
        Are you sure you want to reactivate this Contact?  <br>
        <span class="required required_i">* Required Fields</span>
        <div class="popup_cancel9">
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Yes</button></a>&nbsp;&nbsp;<a href="#" rel="modal:close"><button class="btn btn-primary" type="button">No</button></a></div></p>
    </div>
    <div class="modal1_051" id="ex8" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Deactivate Company</h2>
        <strong> De-Activation Date:<span class="red">*</span> </strong><input type="date" style="width:25%;" placeholder="dd-mm-yy">&nbsp; <a href="#"><img border="0" src="images/date_icon.png"></a><br><br>
        <strong>Reason for De-Activation:<span class="red">*</span></strong> <select name="type" id="type1"><option>Select</option>
            <option>De-Activation Reason 01</option>
            <option>De-Activation Reason 02</option>
            <option name="parcel" value="parcel">OTHERS</option>
        </select> &nbsp; <div id="row_dim_new1" style="float:right; margin-right:25%;"><input type="text" value="" style="width:150%;"></div>
        <br><br>
        Are you sure you want to deactivate this company?  
        <br>
        <span class="required_i red">*Required Field</span>
        <div class="popup_cancel9">
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Yes</button></a>&nbsp;&nbsp;<a href="" rel="modal:close"><button class="btn btn-primary" type="button">No</button></a></div></p>
    </div>
    <div class="modal0000" id="ex10" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Update Company</h2>
        Data has been updated successfully. 
        <div class="popup_cancel popup_cancel001">
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
        </p>
    </div>
    <div class="modal1_055" id="ex11" style="display:none;">         
        <h2 class="panel_heading_style">Warning</h2>   
        <div  style="margin-top:7%">
            <p style="text-align: center;">
                We have found a trainee with similar NRIC / FIN in our database.
                This user is not currently linked with your training organisation / institute.
                Do you want to link this user?
            </p>
            <div class="popup_cancel9">
                <div rel="modal:close">
                    <a href="#" rel="modal:close"><button class="btn btn-primary trainee_deatils_yes" type="button">Yes</button></a>
                    <a href="#" rel="modal:close"><button class="btn btn-primary trainee_deatils_no" type="button">NO</button></a>
                </div>
            </div>
        </div>
    </div>
    <!---------- /*  added by shubhranshu for client requirement on 21/03/2019 */-->
    <div class="modal1_055" id="ex111" style="display:none;">         
        <h2 class="panel_heading_style">Warning</h2>   
        <div  style="margin-top:7%">
            <p style="text-align: center;">
                The trainee whom you are about to register/enrol is part of the restricted list. Please acknowledge to continue !!!
            </p>
            <div class="popup_cancel9">
                <div rel="modal:close">
                    <a href="#" rel="modal:close"><button class="btn btn-primary trainee_deatils_yes" type="button">Yes, I understand.</button></a>
                </div>
            </div>
        </div>
    </div>
    <!---------- /*  added by shubhranshu for client requirement on 21/03/2019 */-->
    <script type="text/javascript">
        $role_id = "<?php echo $this->session->userdata('userDetails')->role_id;?>";
        $privilage = "<?php echo $privilage;?>";
        edu_cnt_array = [0];
        other_cnt_array = [0];
        work_cnt_array = [0];
        $("#empfrom_datetimepicker_0").datepicker({dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,
            maxDate: 0,
            onSelect: function(selectedStartDate) {
                $("#empto_datetimepicker_0").datepicker("option", {
                    minDate: selectedStartDate,
                    maxDate: 0
                });
            }
        });
        $("#empto_datetimepicker_0").datepicker({dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,
            yearRange: "-50:+50",
            minDate: 0,
            maxDate: -1,
        });

        $("#year_of_comp_0").datepicker({changeYear: true, maxDate: 0, dateFormat: 'yy', yearRange: '1945:' + (new Date).getFullYear()});
        $("#year_of_certi_0").datepicker({changeYear: true, maxDate: 0, dateFormat: 'yy', yearRange: '1945:' + (new Date).getFullYear(),
            onSelect: function(selectedStartDate) {
                $("#validity_0").datepicker("option", {
                    minDate: '01-01-' + selectedStartDate,
                    maxDate: ''
                });
            }
        });
        $("#validity_0").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
            minDate: 0,
            maxDate: -1,
            yearRange: "-100:+100",
        });
        function addmore(e) {
            selected_exec_array = [];
            retVal = true;
            $("#" + e + "_err").text("").removeClass('error');

            retVal = true;
            if (e == 'edu') {
                retVal = addmore_validate(e, edu_cnt_array);
            }
            if (e == 'other') {
                retVal = addmore_validate(e, other_cnt_array);
            }
            if (e == 'work') {
                retVal = addmore_validate(e, work_cnt_array);
            }
            if (retVal == false) {
                return false;
            } else {

                var first_tr_id = $('#' + e + '_tbl').closest('table').find(' tbody tr:first').attr('id');
                var first_tr_id_number = first_tr_id.split('_');

                var last_tr_id = $('#' + e + '_tbl').closest('table').find(' tbody tr:last').attr('id');
                var last_tr_id_number = last_tr_id.split('_');
                var cnt = last_tr_id_number[2];
                cnt++;
                $("#" + e + "_firstcolumn_" + last_tr_id_number[2] + " > option:not(:selected)").attr('disabled', true);
                $("#" + e + "_tbl_err").text("").removeClass('error');
                tbl_row = '<tr id="' + e + '_row_' + cnt + '">' + $("#" + first_tr_id).html() + '</tr>';
                tbl_row = tbl_row.replace(e + '_firstcolumn_' + first_tr_id_number[2], e + '_firstcolumn_' + cnt);
                tbl_row = tbl_row.replace("remove_row('" + e + "_remove_" + first_tr_id_number[2] + "')", "remove_row('" + e + "_remove_" + cnt + "')");
                tbl_row = tbl_row.replace(e + '_err' + first_tr_id_number[2], e + '_err' + cnt);
                tbl_row = tbl_row.replace('year_of_comp_' + first_tr_id_number[2], 'year_of_comp_' + cnt);
                tbl_row = tbl_row.replace('year_of_certi_' + first_tr_id_number[2], 'year_of_certi_' + cnt);
                tbl_row = tbl_row.replace('edu_remarks_' + first_tr_id_number[2], 'edu_remarks_' + cnt);
                tbl_row = tbl_row.replace('other_remarks_' + first_tr_id_number[2], 'other_remarks_' + cnt);
                tbl_row = tbl_row.replace('edu_score_grade_' + first_tr_id_number[2], 'edu_score_grade_' + cnt);
                tbl_row = tbl_row.replace('validity_' + first_tr_id_number[2], 'validity_' + cnt);
                tbl_row = tbl_row.replace('class="hasDatepicker"', '');
                tbl_row = tbl_row.replace('empfrom_datetimepicker_' + first_tr_id_number[2], 'empfrom_datetimepicker_' + cnt);
                tbl_row = tbl_row.replace('class="hasDatepicker"', '');
                tbl_row = tbl_row.replace('empto_datetimepicker_' + first_tr_id_number[2], 'empto_datetimepicker_' + cnt);
                tbl_row = tbl_row.replace('class="hasDatepicker"', '');
                $('#' + e + '_tbl tr').last().after(tbl_row);
                $('#' + e + '_firstcolumn_' + cnt).val($('#' + e + '_firstcolumn_' + cnt + ' option:first').val());
                $('#' + e + '_remarks_'+cnt).val('');
                if (e == 'edu') {
                    edu_cnt_array.push(cnt);
                    removeBasedOnValue('edu_firstcolumn_' + cnt);
                }
                if (e == 'other') {
                    other_cnt_array.push(cnt);
                }
                if (e == 'work') {
                    work_cnt_array.push(cnt);
                }
                $("#year_of_comp_" + cnt).datepicker({changeYear: true, maxDate: 0, dateFormat: 'yy', yearRange: '1945:' + (new Date).getFullYear()});
                $("#year_of_certi_" + cnt).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    maxDate: 0,
                    dateFormat: 'yy',
                    yearRange: "-100:+100",
                    onSelect: function(selectedStartDate) {
                        $("#validity_" + cnt).datepicker("option", {
                            minDate: '01-01-' + selectedStartDate,
                            maxDate: '',
                        });
                    }
                });
                $("#validity_" + cnt).datepicker({
                    dateFormat: 'dd-mm-yy',
                    changeMonth: true,
                    changeYear: true,
                    maxDate: -1,
                    minDate: 0,
                    yearRange: "-100:+100",
                });
                $("#empfrom_datetimepicker_" + cnt).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'dd-mm-yy',
                    maxDate: 0,
                    onSelect: function(selectedStartDate) {
                        $("#empto_datetimepicker_" + cnt).datepicker("option", {
                            minDate: selectedStartDate,
                            maxDate: 0
                        });
                    }
                });
                $("#empto_datetimepicker_" + cnt).datepicker({
                    dateFormat: 'dd-mm-yy',
                    changeMonth: true,
                    changeYear: true,
                    maxDate: -1,
                    minDate: 0,
                    yearRange: "-50:+50",
                });
            }
            if ($('#' + e + '_tbl tr').size() > 1) {
                $('#' + e + '_tbl .remove2').show();
            }
        }
        function removeBasedOnValue(id) {
            var current_val = $("#" + id).val();
            $("#" + id + " option").removeAttr('disabled');
            for (i = 0; i < selected_exec_array.length; i++) {
                if (current_val != selected_exec_array[i]) {
                    $("#" + id + " > option[value='" + selected_exec_array[i] + "']").attr('disabled', true);
                }
            }
        }
        function addmore_validate(e, cnt_array) {
            var stat_val = true;
            for (i = 0; i < cnt_array.length; i++) {
                if (e == 'edu')
                    var last_row_value = $.trim($("#" + e + '_firstcolumn_' + cnt_array[i] + " option:selected").val());
                else
                    var last_row_value = $.trim($("#" + e + "_firstcolumn_" + cnt_array[i]).val());
                if (last_row_value == "") {
                    $("#" + e + "_err" + cnt_array[i]).text("[required]").addClass('error');
                    $("#" + e + "_err" + cnt_array[i]).show();
                    $("#" + e + "_firstcolumn_" + cnt_array[i]).addClass('error');
                    stat_val = false;
                } else {
                    $("#" + e + "_err" + cnt_array[i]).text("").removeClass('error');
                    $("#" + e + "_firstcolumn_" + cnt_array[i]).removeClass('error');
                    if (e == 'edu') {
                        var selected_edu_level = $.trim($("#" + e + '_firstcolumn_' + cnt_array[i] + " option:selected").val());
                        if (selected_edu_level != "")
                            selected_exec_array.push(selected_edu_level);
                    }
                }
            }
            return stat_val;
        }

        function remove_row(rowid) {
            var rowarray = rowid.split('_');
            var e = rowarray[0];
            var rowId = rowarray[2];
            if ($('#' + e + '_tbl tbody tr').size() == 2) {
                var first_tr_id = $('#' + e + '_tbl').closest('table').find(' tbody tr:first').attr('id');
                var first_tr_id_number = first_tr_id.split('_');
                var id = 'edu_firstcolumn_' + first_tr_id_number[2];
                $("#" + id + " option").attr('disabled', false);
                $('#' + e + '_tbl tbody tr td span').hide();
            }
            if ($('#' + e + '_tbl tbody tr').size() < 2) {
                $("#add" + e + "_err").text("[ Atlease one row is required.]").addClass('error');
                return false;
            } else {
                $("#add" + e + "_err").text("").removeClass('error');
            }

            if ($('#' + e + '_tbl tr').size() > 1) {
                if (e == 'edu') {
                    selected_array_pop(rowId, e);
                }
                array_pop(rowId, e);
                $('#' + e + '_row_' + rowId).remove();
                $("#" + e + "_tbl_err").text("").removeClass('error');
            } else {
                $("#" + e + "_tbl_err").text("[ Atlease one row is required.]").addClass('error');
            }
            if (e == 'edu') {
                var last_tr_id = $('#' + e + '_tbl').closest('table').find(' tbody tr:last').attr('id');
                var last_tr_id_number = last_tr_id.split('_');
                removeBasedOnValue('edu_firstcolumn_' + last_tr_id_number[2]);
            }
        }

        function selected_array_pop(removeItem, e) {
            var remove_value = $("#" + e + '_firstcolumn_' + removeItem + " option:selected").val();
            selected_exec_array = jQuery.grep(selected_exec_array, function(value) {
                return value != remove_value;
            });
        }

        function array_pop(removeItem, e) {
            if (e == 'edu') {
                edu_cnt_array = jQuery.grep(edu_cnt_array, function(value) {
                    return value != removeItem;
                });
            }
            if (e == 'other') {
                other_cnt_array = jQuery.grep(other_cnt_array, function(value) {
                    return value != removeItem;
                });
            }
            if (e == 'work') {
                work_cnt_array = jQuery.grep(work_cnt_array, function(value) {
                    return value != removeItem;
                });
            }
        }
        function addmore1(e) {
            if (e == 'addedu') {
                var value = $('.edu_level:last').val()
                var rowno = $('.edu_level:last').attr("rowno");
            } else if (e == 'addother') {
                var value = $('#certi_name:last').val()
                var rowno = $('#certi_name:last').attr("rowno");
            } else if (e == 'addwork') {
                var value = $('.org_name:last').val()
                var rowno = $('.org_name:last').attr("rowno");
            }
            if (value != "") {
                $("#" + e + "_err").text("").removeClass('error');
                $.ajax({
                    url: "addmore",
                    type: "post",
                    data: 'add=' + e + '&value=' + value + '&rowno=' + rowno,
                    success: function(res) {
                        $("#" + e).append(res);
                    },
                    error: function() {
                        $("#" + e + "_err").append('There is error while Adding more rows');
                    }
                });
            } else {
                $("#" + e + "_err").text('Current row must have atleast one value').addClass('error');
            }
            return true;
        }

        $('.email').change(function() {
            if ($("#bypassemail_1").is(":checked")) {
                var email = $.trim($('#user_registered_email').val());
                if (email == '') {
                    $("#user_registered_email_err").text("").removeClass('error');
                    $("#user_registered_email").removeClass('error');
                    $("#pers_conf_email_err").text("").removeClass('error');
                    $("#pers_conf_email").removeClass('error');
                }
                $('#span_activate_user').css("display", "");
                $('#BPEMAC_content').css("display", "");
                $('#EMACRQ_content').css("display", "none");
                $("#span_email_id").css("display", "none");
                $("#span_confirm_email_id").css("display", "none");
            } else {
                $('#span_activate_user').css("display", "none");
                $('#BPEMAC_content').css("display", "none");
                $('#EMACRQ_content').css("display", "");
                $("#span_email_id").css("display", "");
                $("#span_confirm_email_id").css("display", "");
            }
            return false;
        });
        $('#assign_company').change(function() {
            var company_id = $(this).val();
            if (company_id) {
                $('#span_sal_range').text('*').addClass('required');
                $('#span_occupation').text('*').addClass('required');
            } else {
                $('#span_sal_range').text('').removeClass('required');
                $('#span_occupation').text('').removeClass('required');
            }
            return false;
        });

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
                $("#IND").show(); 
                $("#NRIC").hide();
                $("#SGP").hide(); 
                $("#SSN").hide();
                $("#USA").hide(); 
                $("#SGP_ID").hide();
                $("#SGP_OTHERS").hide();
                $('#SSN_err').text('').removeClass('error');
                $('#SSN').removeClass('error');
                remove_nric_errors();
            }
            if (country_of_residence == "SGP") {                
                $("#NRIC").show();
                $("#SGP").show(); 
                $('#NRIC option:first-child').attr("selected", "selected");
                $("#PAN").hide();
                $("#IND").hide(); 
                $("#SSN").hide();
                $("#USA").hide(); 
                remove_ind_usa_errors();
            }
            if (country_of_residence == "USA") {
                $("#SSN").show();
                $("#USA").show(); 
                $("#PAN").hide();
                $("#IND").hide(); 
                $("#NRIC").hide();
                $("#SGP").hide(); 
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
                $('#SGP_OTHERS').css('visibility','visible');
                $('#SGP_OTHERS option:first-child').attr("selected", "selected");
                $('#SGP_OTHERS_label').text('');
                $('#SGP_OTHERS_label').text('OTHERS :');
                $('#SGP_ID_label').text('');
                $('#SGP_ID_label').text('OTHERS :');
            }  else if (this.value == "SNG_4") { ////added by shubhranshu due to client requirement on 16/12/2019
                $("#SGP_OTHERS").show();
                $('#SGP_OTHERS option:first-child').attr("selected", "selected");
                $('#SGP_OTHERS_label').text('');
                $('#SGP_OTHERS_label').text('OTHERS :');
                $('#SGP_ID_label').text('');
                $('#SGP_ID_label').text('OTHERS :');
                $('#NRIC_OTHER option[value=NOTAXCODE]').attr('selected','selected');////added by shubhranshu for client requirement
                $('#SGP_OTHERS').css('visibility','hidden');
            $("#SGP_ID").hide();
            }else {
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
        //added by pritam
         var company=$("#assign_company").val()
         if(company=="")
         {
             $("#cert_sent_to").hide();
             $("#cert_sent_t").hide();
             
         }
        $("#assign_company").change(function() 
        {
            var data=this.value;
            var arr=data.split('/');
            
            //alert(arr[1]);
            if (data != "") 
            {
                
                if(arr[1] == "INDIVIDUAL")
                {
                    $("#cert_sent_to").show();
                    $("#cert_sent_t").show();
                     $("#cert_sent_to_others,#cert_sent_to_others").hide();
                }
                else if(arr[1]== "OTHERS")
                {
                    $("#cert_sent_to_others,#cert_sent_to_others").show();
                    $("#cert_sent_to").hide();
                    $("#cert_sent_t").hide();
                }
                else
                {
                     $("#cert_sent_to").hide();
                     $("#cert_sent_t").hide();
                       $("#cert_sent_to_others,#cert_sent_to_others").hide();
                }
            
            } 
            else 
            {
                $("#cert_sent_to").hide();
                $("#cert_sent_t").hide();
                 $("#cert_sent_to_others,#cert_sent_to_others").hide();
            }
        });
       
    
        // end
        $(function() {
            var d = new Date();
            var currentYear = d.getFullYear();
            var currenyMonth = d.getMonth();
            var CurrentDate = d.getDay();
            var startYear = currentYear - 110;
            var endYear = currentYear - 10;
            $(function() {
                $("#pers_dob").datepicker({
                    dateFormat: 'dd-mm-yy',
                    minDate: new Date(startYear, currenyMonth, CurrentDate),
                    maxDate: new Date(endYear, currenyMonth, CurrentDate),
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '-110:+0'
                });
            });
        });
           var country_param = $('#pers_country').val();
            if (country_param) {
                $.post('<?php echo site_url(); ?>trainee/get_states_json', {country_param: country_param}, function(data) {
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
        $('#emp_country').change(function() {
            var country_param = $(this).val();
            if (country_param) {
                $.post('<?php echo site_url(); ?>internal_user/get_states_json', {country_param: country_param}, function(data) {
                    json_data = $.parseJSON(data);
                    $emp_states_select = $("#emp_states");
                    $emp_states_select.html('<option value="">Select</option>');
                    $.each(json_data, function(i, item) {
                        $emp_states_select.append('<option value="' + item.parameter_id + '">' + item.category_name + '</option>');
                    });
                });
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
                    url: "check_username",
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
        var trainee_details;
        var trainee_auto_form_flag = true;
        /*  added by shubhranshu for client requirement on 21/03/2019 */
        function check_nric_restriction(){
            var $nric = $("#NRIC_ID").val(); 
            //alert($nric);
            if($nric !=''){
            $.ajax({
                    url: "check_nric_restriction",
                    type: "post",
                    dataType: "json",
                    async:false,
                    data: {tax_code: $nric,operation:'ADDNEWTRAINEE'},
                    success: function(res) {
                        if (res == 1) {
                            if($privilage == '0'){///added by shubhranshu
                                if($role_id == 'ADMN'){///added by shubhranshu
                                    if(res > 0){
                                        $('#ex111').modal();
                                    }
                                }///added by shubhranshu
                            }else if($privilage == '1'){
                                if(res > 0){
                                        $('#ex111').modal();
                                    }
                            } ///added by shubhranshu
                        } 
                    }
                });
            }
        }
        /*--------------------------------------------------------------*/
        function isunique_taxcode(e, id) {
            e = $.trim(e);
            var NRIC = $("#NRIC").val(); 
           check_nric_restriction();  ///added by shubhranshu

            
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
                    url: "check_taxcode",
                    type: "post",
                    dataType: "json",
                    data: {tax_code: e, country_of_residence: $country_of_residence, nric: $nric},
                    success: function(res) {
                        if (res == 1) {
                            window.username = 'exists';
                            $("#" + id + "_err").text("[code exists!]").addClass('error');
                            $("#" + id).addClass('error');
                            return false;
                        } else if (res == 2) {
                            $("#" + id + "_err").text("[Invalid!]").addClass('error');
                            $("#" + id).addClass('error');
                            return false;
                        } else if (res == 0) {
                            window.username = 'notexists';
                            $("#" + id + "_err").text("").removeClass('error');
                            $("#" + id).removeClass('error');
                            return true;
                        }else {
                            $('#ex11').modal();
                            trainee_details = res;
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
        $(document).ready(function(){
           $('.trainee_deatils_yes').click(function(){ 
               $("#pers_conf_email").val("");
               $('#pers_first_name').val(trainee_details.userdetails.first_name);
               $('#nationality').val(trainee_details.userdetails.nationality);
               $('#pers_gender').val(trainee_details.userdetails.gender);
               $('#pers_dob').val(trainee_details.userdetails.dob);
               $('#pers_contact_number').val(trainee_details.userdetails.contact_number);
               $('#pers_alternate_contact_number').val(trainee_details.userdetails.alternate_contact_number);
               $('#race').val(trainee_details.userdetails.race);
               $('#assign_company').val(trainee_details.company.company_id);
               $('#sal_range').val(trainee_details.userdetails.salary_range);
               $('#occupation').val(trainee_details.userdetails.occupation_code);
               $('#user_registered_email').val(trainee_details.userdetails.registered_email_id);
               $('#pers_alt_email').val(trainee_details.userdetails.alternate_email_id);
               $('#highest_educ_level').val(trainee_details.userdetails.highest_educ_level);
               $('input[name=certificate_pick_pref][value=' + trainee_details.userdetails.certificate_pick_pref + ']').attr('checked', true);               
               $('#pers_personal_address_bldg').val(trainee_details.userdetails.personal_address_bldg);
               $('#pers_city').val(trainee_details.userdetails.personal_address_city);
               $('#pers_country').val(trainee_details.userdetails.personal_address_country);               
               $('#pers_zipcode').val(trainee_details.userdetails.personal_address_zip);
               $('#pers_states').val(trainee_details.userdetails.personal_address_state);  
               $('#pers_country').trigger('change');               
               $('#assign_company').trigger('change');
               $('.email').trigger('change');
                if(trainee_auto_form_flag == true) {
                    populate_addmore('edu',trainee_details.edudetails);
                    populate_addmore('other',trainee_details.otherdetails);
                    populate_addmore('work',trainee_details.workdetails);
                }
                trainee_auto_form_flag = false;
           }); 
           $('.trainee_deatils_no').click(function(){               
               $('#trainee_form')[0].reset();
           });    
        });
        function populate_addmore(e, details_array) {            
            selected_exec_array = [];
            var $i =0;
            for($i=0; $i<details_array.length;$i++) {
                if($i==0) {
                    if(e == 'edu') {
                        $('#' + e + '_firstcolumn_0').val(details_array[$i].educ_level);
                        $('#year_of_comp_0').val(details_array[$i].educ_yr_completion);
                        $('#edu_score_grade_0').val(details_array[$i].educ_score);
                        $('#edu_remarks_0').val(details_array[$i].educ_remarks);
                    } else if(e == 'other') {
                        $('#' + e + '_firstcolumn_0').val(details_array[$i].cert_name);
                        $('#year_of_certi_0').val(details_array[$i].yr_completion);
                        $('#validity_0').val(details_array[$i].valid_till);
                        $('#other_remarks_0').val(details_array[$i].oth_remarks);
                    } else if(e == 'work') {
                        $('#' + e + '_firstcolumn_0').val(details_array[$i].org_name);
                        $('#empfrom_datetimepicker_0').val(details_array[$i].emp_from_date);
                        $('#empto_datetimepicker_0').val(details_array[$i].emp_to_date);
                        $('#designation_0').val(details_array[$i].designation);
                    }    
                } else {    
                    var first_tr_id = $('#' + e + '_tbl').closest('table').find(' tbody tr:first').attr('id');
                    var first_tr_id_number = first_tr_id.split('_');

                    var last_tr_id = $('#' + e + '_tbl').closest('table').find(' tbody tr:last').attr('id');
                    var last_tr_id_number = last_tr_id.split('_');
                    var cnt = last_tr_id_number[2];
                    cnt++;
                    $("#" + e + "_firstcolumn_" + last_tr_id_number[2] + " > option:not(:selected)").attr('disabled', true);
                    $("#" + e + "_tbl_err").text("").removeClass('error');
                    tbl_row = '<tr id="' + e + '_row_' + cnt + '">' + $("#" + first_tr_id).html() + '</tr>';
                    tbl_row = tbl_row.replace(e + '_firstcolumn_' + first_tr_id_number[2], e + '_firstcolumn_' + cnt);
                    tbl_row = tbl_row.replace("remove_row('" + e + "_remove_" + first_tr_id_number[2] + "')", "remove_row('" + e + "_remove_" + cnt + "')");
                    tbl_row = tbl_row.replace(e + '_err' + first_tr_id_number[2], e + '_err' + cnt);
                    tbl_row = tbl_row.replace('year_of_comp_' + first_tr_id_number[2], 'year_of_comp_' + cnt);
                    tbl_row = tbl_row.replace('year_of_certi_' + first_tr_id_number[2], 'year_of_certi_' + cnt);
                    
                    tbl_row = tbl_row.replace('edu_score_grade_' + first_tr_id_number[2], 'edu_score_grade_' + cnt);
                    tbl_row = tbl_row.replace('edu_remarks_' + first_tr_id_number[2], 'edu_remarks_' + cnt);
                    tbl_row = tbl_row.replace('other_remarks_' + first_tr_id_number[2], 'other_remarks_' + cnt);
                    tbl_row = tbl_row.replace('designation_' + first_tr_id_number[2], 'designation_' + cnt);                    
                    tbl_row = tbl_row.replace('validity_' + first_tr_id_number[2], 'validity_' + cnt);
                    tbl_row = tbl_row.replace('class="hasDatepicker"', '');
                    tbl_row = tbl_row.replace('empfrom_datetimepicker_' + first_tr_id_number[2], 'empfrom_datetimepicker_' + cnt);
                    tbl_row = tbl_row.replace('class="hasDatepicker"', '');
                    tbl_row = tbl_row.replace('empto_datetimepicker_' + first_tr_id_number[2], 'empto_datetimepicker_' + cnt);
                    tbl_row = tbl_row.replace('class="hasDatepicker"', '');
                    $('#' + e + '_tbl tr').last().after(tbl_row);                    
                    if(e == 'edu') {
                        $('#' + e + '_firstcolumn_'+ cnt).val(details_array[$i].educ_level);
                        $('#year_of_comp_'+ cnt).val(details_array[$i].educ_yr_completion);
                        $('#edu_score_grade_'+ cnt).val(details_array[$i].educ_score);
                        $('#edu_remarks_'+ cnt).val(details_array[$i].educ_remarks);
                    } else if(e == 'other') {
                        $('#' + e + '_firstcolumn_'+ cnt).val(details_array[$i].cert_name);
                        $('#year_of_certi_'+ cnt).val(details_array[$i].yr_completion);
                        $('#validity_'+ cnt).val(details_array[$i].valid_till);
                        $('#other_remarks_'+ cnt).val(details_array[$i].oth_remarks);
                    } else if(e == 'work') {
                        $('#' + e + '_firstcolumn_'+ cnt).val(details_array[$i].org_name);
                        $('#empfrom_datetimepicker_'+ cnt).val(details_array[$i].emp_from_date);
                        $('#empto_datetimepicker_'+ cnt).val(details_array[$i].emp_to_date);
                        $('#designation_'+ cnt).val(details_array[$i].designation);
                    } 
                    if (e == 'edu') {
                        edu_cnt_array.push(cnt);
                        removeBasedOnValue('edu_firstcolumn_' + cnt);
                    }
                    if (e == 'other') {
                        other_cnt_array.push(cnt);
                    }
                    if (e == 'work') {
                        work_cnt_array.push(cnt);
                    }
                    $("#year_of_comp_" + cnt).datepicker({changeYear: true, maxDate: 0, dateFormat: 'yy', yearRange: '1945:' + (new Date).getFullYear()});
                    $("#year_of_certi_" + cnt).datepicker({
                        changeMonth: true,
                        changeYear: true,
                        maxDate: 0,
                        dateFormat: 'yy',
                        yearRange: "-100:+100",
                        onSelect: function(selectedStartDate) {
                            $("#validity_" + cnt).datepicker("option", {
                                minDate: '01-01-' + selectedStartDate,
                                maxDate: '',
                            });
                        }
                    });
                    $("#validity_" + cnt).datepicker({
                        dateFormat: 'dd-mm-yy',
                        changeMonth: true,
                        changeYear: true,
                        maxDate: -1,
                        minDate: 0,
                        yearRange: "-100:+100",
                    });
                    $("#empfrom_datetimepicker_" + cnt).datepicker({
                        changeMonth: true,
                        changeYear: true,
                        dateFormat: 'dd-mm-yy',
                        maxDate: 0,
                        onSelect: function(selectedStartDate) {
                            $("#empto_datetimepicker_" + cnt).datepicker("option", {
                                minDate: selectedStartDate,
                                maxDate: 0
                            });
                        }
                    });
                    $("#empto_datetimepicker_" + cnt).datepicker({
                        dateFormat: 'dd-mm-yy',
                        changeMonth: true,
                        changeYear: true,
                        maxDate: -1,
                        minDate: 0,
                        yearRange: "-50:+50",
                    });            
                    if ($('#' + e + '_tbl tr').size() > 1) {
                        $('#' + e + '_tbl .remove2').show();
                    }
                }
            }
        }
        function confirm_email(confirm) {
            var email = $.trim($('#user_registered_email').val());
            if (confirm == '' && email != '') {
                $("#pers_conf_email_err").text("[required]").addClass('error');
                $("#pers_conf_email").addClass('error');
            } else if (valid_email_address(confirm) == false && confirm != '') {
                $("#pers_conf_email_err").text("[invalid]").addClass('error');
                $("#pers_conf_email").addClass('error');
            } else if (email != confirm) {
                $('#pers_conf_email_err').text('[Email does not match]').addClass('error');
                $('#pers_conf_email').addClass('error');
            } else {
                $('#pers_conf_email_err').text('').removeClass('error');
                $('#pers_conf_email').removeClass('error');
            }
            return false;
        }

        function isunique_email(e, id) {
            e = $.trim(e);
            if (e == '' && $("#bypassemail_1").is(":checked")) {
                $("#" + id + "_err").text("").removeClass('error');
                $("#" + id).removeClass('error');
                return false;
            }
            if (e == '') {
                $("#" + id + "_err").text("[required]").addClass('error');
                $("#" + id).addClass('error');
                return false;
            } else if (valid_email_address(e) == false) {
                $("#" + id + "_err").text("[invalid]").addClass('error');
                $("#" + id).addClass('error');
                return false;
            } else {
                $.ajax({                    
                    url: "check_email_id",
                    type: "post",
                    data: 'email=' + e,
                    success: function(res) {
                        if (res) {
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

        function valid_contact_number(contactNum) {
            return /^\d+$/.test(contactNum.replace(/[\s]/g, ''));
        }
        function valid_email_address(emailAddress) {
            var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
            return pattern.test(emailAddress);
        }
        function valid_user_name(userName) {
            var n = userName.indexOf(" "); 
            return (n == -1)?true:false;
        }
        function valid_discount(discountValue) {
            var pattern = new RegExp(/^(([1-8][0-9]?|9[0-8]?)\.\d+|[1-9][0-9]?)$/);
            return pattern.test(discountValue);
        }
        function  valid_date_field(dateofjoin) {
            var pattern = /^\d{1,2}-\d{1,2}-\d{4}$/;
            return pattern.test(dateofjoin);
        }
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
            $("#user_registered_email").trigger('blur');
            if (country_of_residence == "") {
                $("#country_of_residence_err").text("[required]").addClass('error');
                $("#country_of_residence").addClass('error');
                retVal = false;
            }
            else {
                $("#country_of_residence_err").text("").removeClass('error');
                $("#country_of_residence").removeClass('error');
            }

            if (country_of_residence == "IND") {
                PAN = $.trim($("#PAN").val());
                var pan_error_text = $("#PAN_err").text();
                if (PAN == "") {
                    $("#PAN_err").text("[required]").addClass('error');
                    $("#PAN").addClass('error');
                    retVal = false;
                }
                else if (pan_error_text != '[code exists!]') {
                    $("#PAN_err").text("").removeClass('error');
                    $("#PAN").removeClass('error');
                }
            }
            if (country_of_residence == "SGP") {
                NRIC = $.trim($("#NRIC").val());
                var nric_error_text = $("#NRIC_ID_err").text();
                var NRIC_ID = $("#NRIC_ID").val();
                var NRIC_OTHER = $("#NRIC_OTHER").val();
                if (NRIC == "") {
                    $("#NRIC_err").text("[required]").addClass('error');
                    $("#NRIC").addClass('error');
                    retVal = false;
                } else if (NRIC == "SNG_3" || NRIC == "SNG_4") {
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
                    } else if (nric_error_text != '[code exists!]') {
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
                SSN = $.trim($("#SSN").val());
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
            user_name = $.trim($("#user_name").val());
            if (user_name == "") {
                $("#user_name_err").text("[required]").addClass('error');
                $("#user_name").addClass('error');
                retVal = false;
            } else {
                if (valid_user_name(user_name) == false) {
                    retVal = false;
                    $("#user_name_err").text("[invalid]").addClass('error');
                    $("#user_name").addClass('error');
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

            nationality = $.trim($("#nationality").val());
            if (nationality == "") {
                $("#nationality_err").text("[required]").addClass('error');
                $("#nationality").addClass('error');
                retVal = false;
            } else {
                $("#nationality_err").text("").removeClass('error');
                $("#nationality").removeClass('error');
            }

            pers_gender = $.trim($("#pers_gender option:selected").val());
            if (pers_gender == "") {
                $("#pers_gender_err").text("[required]").addClass('error');
                $("#pers_gender").addClass('error');
                retVal = false;
            } else {
                $("#pers_gender_err").text("").removeClass('error');
                $("#pers_gender").removeClass('error');
            }

//Added by abdulla
$tenant_id = "<?php echo $this->session->userdata('userDetails')->tenant_id; ?>";

		if($tenant_id == 'T24') {
			pers_dob = $.trim($("#pers_dob").val());
            if (pers_dob == "") {
                $("#pers_dob_err").text("[required]").addClass('error');
                $("#pers_dob").addClass('error');
            } else if (valid_date_field(pers_dob) == false) {
                $("#pers_dob_err").text("[dd-mm-yy format]").addClass('error');
                $("#pers_dob").removeClass('error');
                retVal = false;
            } else {
                $("#pers_dob_err").text("").removeClass('error');
                $("#pers_dob").removeClass('error');
            }
		} else {
			pers_dob = $.trim($("#pers_dob").val());
            if (pers_dob == "") {
                $("#pers_dob_err").text("").removeClass('error');
                $("#pers_dob").removeClass('error');
            } else if (valid_date_field(pers_dob) == false) {
                $("#pers_dob_err").text("[dd-mm-yy format]").addClass('error');
                $("#pers_dob").removeClass('error');
                retVal = false;
            } else {
                $("#pers_dob_err").text("").removeClass('error');
                $("#pers_dob").removeClass('error');
            }
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

            pers_alternate_contact_number = $.trim($("#pers_alternate_contact_number").val());
            if (pers_alternate_contact_number != "") {
                if (valid_contact_number(pers_alternate_contact_number) == false) {
                    $("#pers_alternate_contact_number_err").text("[invalid]").addClass('error');
                    $("#pers_alternate_contact_number").addClass('error');
                    retVal = false;
                } else {
                    $("#pers_alternate_contact_number_err").text("").removeClass('error');
                    $("#pers_alternate_contact_number").removeClass('error');
                }
            } else {
                $("#pers_alternate_contact_number_err").text("").removeClass('error');
                $("#pers_alternate_contact_number").removeClass('error');
            }

            assigned_company = $.trim($('#assign_company').val());
            if (assigned_company != '') {
                sal_range = $.trim($('#sal_range').val());
                if (sal_range == '') {
                    $("#sal_range_err").text("[required]").addClass('error');
                    $("#sal_range").addClass('error');
                } else {
                    $("#sal_range_err").text("").removeClass('error');
                    $("#sal_range").removeClass('error');
                }
                occupation = $.trim($('#occupation').val());
                if (occupation == '') {
                    $("#occupation_err").text("[required]").addClass('error');
                    $("#occupation").addClass('error');
                } else {
                    $("#occupation_err").text("").removeClass('error');
                    $("#occupation").removeClass('error');
                }
            } else {
                $("#sal_range_err").text("").removeClass('error');
                $("#sal_range").removeClass('error');
                $("#occupation_err").text("").removeClass('error');
                $("#occupation").removeClass('error');
            }
            individual_discount = $.trim($("#individual_discount").val());
            if (individual_discount != "") {
                if (valid_discount(individual_discount) == false) {
                    $("#individual_discount_err").text("[invalid]").addClass('error');
                    $("#individual_discount").addClass('error');
                    retVal = false;
                } else {
                    $("#individual_discount_err").text("").removeClass('error');
                    $("#individual_discount").removeClass('error');
                }
            }
            user_registered_email = $.trim($("#user_registered_email").val());
            if ($("#bypassemail_2").is(":checked")) {
                if (user_registered_email == "") {
                    $("#user_registered_email_err").text("[required]").addClass('error');
                    $("#user_registered_email").addClass('error');
                    $("#pers_conf_email_err").text("[required]").addClass('error');
                    $("#pers_conf_email").addClass('error');
                    retVal = false;
                } else {
                    $("#user_registered_email_err").text("").removeClass('error');
                    $("#user_registered_email").removeClass('error');
                    $("#pers_conf_email_err").text("").removeClass('error');
                    $("#pers_conf_email").removeClass('error');
                }
            } else {
                $("#user_registered_email_err").text("").removeClass('error');
                $("#user_registered_email").removeClass('error');
                $("#pers_conf_email_err").text("").removeClass('error');
                $("#pers_conf_email").removeClass('error');
            }

            pers_conf_email = $.trim($("#pers_conf_email").val());
            if (pers_conf_email != user_registered_email) {
                if (valid_email_address(user_registered_email) == true) {
                    if (pers_conf_email == '') {
                        $("#pers_conf_email_err").text("[required]").addClass('error');
                        $("#pers_conf_email").addClass('error');
                    } else if (valid_email_address(pers_conf_email) == false) {
                        $("#pers_conf_email_err").text("[invalid]").addClass('error');
                        $("#pers_conf_email").addClass('error');
                    } else {
                        $("#pers_conf_email_err").text("[Email does not match]").addClass('error');
                        $("#pers_conf_email").addClass('error');
                    }
                    retVal = false;
                }
            } else {
                $("#pers_conf_email").removeClass('error');
                $("#pers_conf_email_err").text("").removeClass('error');
            }

            pers_alt_email = $.trim($("#pers_alt_email").val());
            if (pers_alt_email != "") {
                if (valid_email_address(pers_alt_email) == false) {
                    $("#pers_alt_email_err").text("[invalid]").addClass('error');
                    $("#pers_alt_email").addClass('error');
                    retVal = false;
                }
                else {
                    $("#pers_alt_email").removeClass('error');
                    $("#pers_alt_email_err").text("").removeClass('error');
                }
            } else {
                $("#pers_alt_email_err").text("").removeClass('error');
            }

            if (user_registered_email != "") {
                if (valid_email_address(user_registered_email) == false) {
                    $("#user_registered_email_err").text("[invalid]").addClass('error');
                    $("#user_registered_email").addClass('error');
                    retVal = false;
                }
                else {
                    $("#user_registered_email_err").text("").removeClass('error');
                    $("#user_registered_email").removeClass('error');
                }
            }

            highest_educ_level = $.trim($("#highest_educ_level").val());
            if (highest_educ_level == "") {
                $("#highest_educ_level_err").text("[required]").addClass('error');
                $("#highest_educ_level").addClass('error');
                retVal = false;
            } else {
                $("#highest_educ_level_err").text("").removeClass('error');
                $("#highest_educ_level").removeClass('error');
            }

			if($tenant_id == 'T24') {
				pers_states = $.trim($("#pers_states").val());
				if (pers_states == "") {
					$("#pers_states_err").text("[required]").addClass('error');
					$("#pers_states").addClass('error');
					retVal = false;
				} else {
					$("#pers_states_err").text("").removeClass('error');
					$("#pers_states").removeClass('error');
				}			
			}
			
			if($tenant_id == 'T24') {
				var pers_city = $.trim($("#pers_city").val());
				if (pers_city != '') {
					if (valid_name(pers_city) == false) {
						$("#pers_city_err").text("[invalid]").addClass('error');
						$("#pers_city").addClass('error');
					} else {
						$("#pers_city_err").text("").removeClass('error');
						$("#pers_city").removeClass('error');
					}
				} else if(pers_city == '') {
					$("#pers_city_err").text("[required]").addClass('error');
					$("#pers_city").addClass('error');				
				} else {
					$("#pers_city_err").text("").removeClass('error');
					$("#pers_city").removeClass('error');
				}
			} else {
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
			}
			
			if($tenant_id == 'T24') {
				pers_personal_address_bldg = $.trim($("#pers_personal_address_bldg").val());
				if (pers_personal_address_bldg == "") {
					$("#pers_personal_address_bldg_err").text("[required]").addClass('error');
					$("#pers_personal_address_bldg").addClass('error');
				} else {
					$("#pers_personal_address_bldg_err").text("").removeClass('error');
					$("#pers_personal_address_bldg").removeClass('error');
				}
			}
			
			if($tenant_id == 'T24') {
				var pers_zipcode = $.trim($("#pers_zipcode").val());
				if (pers_zipcode != '') {
					if (valid_zip(pers_zipcode) == false) {
						$("#pers_zipcode_err").text("[invalid]").addClass('error');
						$("#pers_zipcode").addClass('error');
					} else {
						$("#pers_zipcode_err").text("").removeClass('error');
						$("#pers_zipcode").removeClass('error');
					}
				} else if(pers_zipcode == '') {
					$("#pers_zipcode_err").text("[required]").addClass('error');
					$("#pers_zipcode").addClass('error');
				} else {
					$("#pers_zipcode_err").text("").removeClass('error');
					$("#pers_zipcode").removeClass('error');
				}
			} else {
				var pers_zipcode = $.trim($("#pers_zipcode").val());
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
			}
		    
            if ($('#trainee_validation_div span').hasClass('error')) {
                retVal = false;
            }
        
        if(retVal == true){
            $('.button_class99 button[type=submit]').css('display','none');
        }
            return retVal;
        }
        $('.payment_scroll input').change(function() {
            return indi_disc_validate(false);
        });
        $('.disc_save').click(function() {
            return indi_disc_validate(true);
        });
        function indi_disc_validate($retval) {
            $('.indi_disc').each(function(i) {
                $this = $(this);
                $key = $this.data('key');
                $val = $this.val().trim();
                if (isNaN($val) == true && $val != '') {
                    disp_err('#indi_disc_' + $key, '[invalid]');
                    $retval = false;
                } else if ($val.length > 0 && (parseFloat($val) >= 100 || parseFloat($val) < 0)) {
                    disp_err('#indi_disc_' + $key, '[invalid]');
                    $retval = false;
                } else {
                    remove_err('#indi_disc_' + $key);
                }
            });
            return $retval;
        }
        $('.indi_disc,.indi_disc_amt').keydown(function(e) { 
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
        $(document).ready(function() {
            var check = 0;
            $('#trainee_form').submit(function() {
                check = 1;
                return validate(true);
            });
            $('#trainee_form select,#trainee_form input').change(function() {
                if (check == 1) {
                    return validate(false);
                }
            });
        });

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
        $(".float_number").keydown(function(e) {
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 173]) !== -1 ||
                            (e.keyCode == 65 && e.ctrlKey === true) ||
                                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                        return;
                    }
                    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                        e.preventDefault();
                    }
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
                $('#trainee_form').each(function() {
                    this.reset();
                });
                $('#country_of_residence').trigger('change');
                $('#pers_states').find('option').remove().end().append('<option value="">Select</option>');
            });
        });
        function disp_err($id, $text) {
            $text = typeof $text !== 'undefined' ? $text : '[required]';
            $($id).addClass('error');
            $($id + '_err').addClass('error').addClass('error_text').html($text);
        }
        function remove_err($id) {
            $($id).removeClass('error');
            $($id + '_err').removeClass('error').text('');
        }
    </script>
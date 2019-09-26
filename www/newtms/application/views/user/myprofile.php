<?php
$action_name = explode('/', $_SERVER['PATH_INFO']);

?>
<div class="container_nav_style">	
    <div class="container container_row">
        <!-- Example row of columns -->
        <div class="row row_pushdown">
            <div style="clear:both;"></div>
            <div class="col-md-12">
                <span class="error"><?php echo validation_errors(); ?></span>
                <?php
                $atr = 'id="profileForm" name="validate_form" onsubmit="return(validate());"';
                echo form_open_multipart("user/myprofile", $atr);
                ?> 
                <br>
                <h2 class="panel_heading_style">
                    <span>My Profile</span>
                     <span class="label label-default push_right white-btn" style="background-color:white;">
                        <a  class="<?php echo ($action_name[1] == 'user' && $action_name[2] == 'change_password')?'active':'' ?>"
                        href="<?php echo base_url(); ?>user/change_password">
                        <img border="0" src="<?php echo base_url(); ?>assets/images/passwordicon.png">
                        <strong class="menu" style="color:black;">&nbsp;&nbsp;Change Password</strong>
                        </a>
                    </span>
                </h2>
                <?php
                if ($this->session->flashdata('Success')) {
                    echo "<p class='success'>" . $this->session->flashdata('Success') . "</p>";
                } else {
                    echo "<p class='error1'>" . $this->session->flashdata('Error') . "</p>";
                }
                ?>
                <h2 class="sub_panel_heading_style">Access Details</h2>
                <div id ='trainee_validation_div'>
                <div class="bs-example">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td class="td_heading">Country of Residence:<span class="required">*</span></td>
                                    <td>  
                                        <?php
                                        $meta_result = fetch_all_metavalues();
                                        
//                                        $countries = $meta_result[Meta_Values::COUNTRIES];
//                                        $country_options[''] = 'Select';
//                                        foreach ($countries as $item):
//                                            $country_options[$item['parameter_id']] = $item['category_name'];
//                                        endforeach;
//                                        $attr = 'id="country_of_residence" disabled';
//                                        echo form_dropdown('country_of_residence', $country_options, $profile['userdetails']['country_of_residence'], $attr);
                                        ?>
                                        <span id="country_of_residence_err"></span>
                                        <label class="label_font">
                                            <?php if($profile['userdetails']['country_of_residence']=='SGP'){ echo "SINGAPORE";}?></label>
                                    </td>
<!--                                    <td>
                                        <SPAN id="IND" style="display:none;"><b>PAN :</b> <span class="required">* </span>
                                            <?php
                                            $attr = array('name' => 'PAN', 'id' => 'PAN', 'onblur' => 'javascript:isunique_taxcode(this.value,this.id);', 'maxlength' => '50', 'oninput' => 'this.value=this.value.toUpperCase();');
                                            echo form_input($attr, $profile['userdetails']['tax_code']);
                                            ?>
                                            <span id="PAN_err"></span>
                                        </SPAN>
                                        <SPAN id="SGP" style="display:none;"><b>NRIC Type:</b> <span class="required">* </span>                  
                                            <?php
                                            $nrics = $meta_result[Meta_Values::NRIC];
                                            $nris_options[''] = 'Select';
                                            foreach ($nrics as $item):
                                                $nris_options[$item['parameter_id']] = $item['category_name'];
                                            endforeach;

                                            $attr = 'id="NRIC", disabled="disabled"';
                                            echo form_dropdown('NRIC', $nris_options, $profile['userdetails']['tax_code_type'], $attr);
                                            ?>
                                            <span id="NRIC_err"></span>
                                        </SPAN> 
                                        <SPAN id="SGP_OTHERS" style="display:none;">
                                        
                                          <span class="required">* </span>                  
                                            <?php
                                            $nric_other = $meta_result[Meta_Values::NRIC_OTHER];
                                            $nric_other_options[''] = 'Select';
                                            foreach ($nric_other as $item):
                                                $nric_other_options[$item['parameter_id']] = $item['category_name'];
                                            endforeach;

                                            $attr = 'id="NRIC_OTHER" disabled';
                                            echo form_dropdown('NRIC_OTHER', $nric_other_options, $profile['userdetails']['other_identi_type'], $attr);
                                            ?>
                                            <span id="NRIC_OTHER_err"></span>
                                        </SPAN>
                                        <SPAN id="SGP_ID" style="display:none;">                                            
                                            <label id="SGP_ID_label"> NRIC Code : </label><span class="required">* </span>                  
                                            <?php
                                            $attr = array('name' => 'NRIC_ID', 'id' => 'NRIC_ID', 'onblur' => 'javascript:isunique_taxcode(this.value,this.id);', 'maxlength' => '50', 'oninput' => 'this.value=this.value.toUpperCase();');
                                            echo form_input($attr, $profile['userdetails']['tax_code'],'disabled="disabled"');
                                            ?>
                                            <span id="NRIC_ID_err"></span>
                                        </SPAN>
                                        <SPAN id="USA" style="display:none;"><b>SSN :</b> <span class="required">* </span>
                                            <?php
                                            $attr = array('name' => 'SSN', 'id' => 'SSN', 'onblur' => 'javascript:isunique_taxcode(this.value,this.id);', 'maxlength' => '50', 'oninput' => 'this.value=this.value.toUpperCase();');
                                            echo form_input($attr, $profile['userdetails']['tax_code'],'disabled="disabled"');
                                            ?>    
                                            <span id="SSN_err"></span>
                                        </SPAN>
                                    </td>-->
                                    
                                    <td> <b>NRIC Type: </b>
                                        <?php if($profile['userdetails']['tax_code_type'] == 'SNG_3'){                                        
                                                    echo "Others";                                       
                                                }
                                                else if($profile['userdetails']['tax_code_type'] == 'SNG_2'){
                                                    echo "FIN";
                                                }else
                                                    { echo "NRIC";}
                                    
                                        ?>&nbsp;&nbsp;&nbsp;<?php if($profile['userdetails']['tax_code_type'] == 'SNG_3')
                                        {?>
                                            <SPAN id="SGP_OTHERS">
                                            <?php if($profile['userdetails']['other_identi_type'] == 'PP'){
                                                 echo "Passport";
                                                }else{ echo "Driving License"; }?>
                                        
                                            </SPAN>&nbsp;&nbsp;&nbsp;
                                        <?php } ?>
                                         <SPAN id="SGP_ID">                                            
                                            <label id="SGP_ID_label"> NRIC Code : <?php echo $profile['userdetails']['tax_code'];?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td_heading">Username:<span class="required">*</span></td>
                                    <td colspan="5"><label class="label_font"><?php echo $profile['userdetails']['user_name']; ?></label></td>
                                </tr>
                                <tr>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <h2 class="sub_panel_heading_style">Personal Details</h2>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>                            
                            <tr>
                                <td class="td_heading"> Name:<span class="required">*</span></td>
                                <td>
                                    <?php
                                    $attr = array(
                                        'name' => 'pers_first_name',
                                        'id' => 'pers_first_name',
                                        'maxlength' => '50',
                                        'value' => $profile['userdetails']['first_name'],
                                        'class' => ' upper_case',
                                        'autocomplete' =>"off"
                                    );
                                    echo form_input($attr);
                                    ?>  
                                    <span id="pers_first_name_err" class="remov_err"></span>
                                </td>                                
                               
                                <td class="td_heading">Gender:<span class="required">*</span></td>
                                <td>
                                    <?php
                                    $gender = $meta_result[Meta_Values::GENDER];
                                    $gender_options = array();
                                    $gender_options[''] = 'Select';
                                    foreach ($gender as $item):
                                        $gender_options[$item['parameter_id']] = $item['category_name'];
                                    endforeach;
                                    echo form_dropdown('pers_gender', $gender_options, $profile['userdetails']['gender'], 'id="pers_gender"');
                                    ?> 
                                    <span id="pers_gender_err"></span>
                                </td>
                            </tr>

                            <tr>
                                <td class="td_heading">Date of Birth:</td> 
                                <td> 
                                    
                                    <label class="label_font"><?php echo date('d-m-Y', strtotime($profile['userdetails']['dob'])); ?></label>
                                    <?php
//                                    $attr = array(
//                                        'name' => 'pers_dob',
//                                        'id' => 'pers_dob',
//                                        'maxlength' => '10',
//                                        'value' => (!empty($profile['userdetails']['dob'])) ? date('d-m-Y', strtotime($profile['userdetails']['dob'])) : '',
//                                        'placeholder'=>'dd-mm-yyyy',
//                                        'readonly' => 'readonly',
//                                        'disabled' => 'disabled'
//                                    );
//                                    echo form_input($attr);
//                                    ?>
<!--                                    <span id="pers_dob_err"></span>-->
                                </td>
                                <td class="td_heading">Contact Number:<span class="required">*</span></td>
                                <td> <label class="label_font"><?php echo $profile['userdetails']['contact_number']; ?></label>
                                    <?php
//                                        $attr = array(
//                                            'name' => 'pers_contact_number',
//                                            'id' => 'pers_contact_number',
//                                            'maxlength' => '50',
//                                            'value' => $profile['userdetails']['contact_number'],
//                                            'class' => 'number',
//                                             'onblur'=>'javascript:validate_pers_contact_number(this.value,this.id);',
//                                            'disabled' => 'disabled'
//                                        );
//                                        echo form_input($attr);
                                    
                                    ?>  
<!--                                    <span id="pers_contact_number_err"  class="remov_err"></span>-->
                                </td>
                                <td class="td_heading">Alternate Contact Number:</td>
                                <td>
                                    <?php
                                        $attr = array(
                                            'name' => 'pers_alternate_contact_number',
                                            'id' => 'pers_alternate_contact_number',
                                            'maxlength' => '50',
                                            'value' => $profile['userdetails']['alternate_contact_number'],
                                            'class' => 'number',
                                             'onblur'=>'javascript:validate_alternate_pers_contact_number(this.value,this.id);'
                                        );
                                        echo form_input($attr);
                                    ?>
                                    <span id="pers_alternate_contact_number_err"></span> 
                                </td>
                            </tr>                            
                            <tr>
                                <td class="td_heading">Email Id:</td>
                                <?php
//                                $email = array(
//                                    'name' => 'user_registered_email',
//                                    'id' => 'user_registered_email',
//                                    'maxlength' => '50',
//                                    'value' => $profile['userdetails']['registered_email_id'],
//                                    'onblur'  => 'javascript:isunique_email(this.value,this.id);',
//                                    'disabled' => 'disabled'
//                                );
//                                $conf_email = array(
//                                    'name' => 'pers_conf_email',
//                                    'id' => 'pers_conf_email',
//                                    'maxlength' => '50',
//                                    'value' => $profile['userdetails']['registered_email_id'],
//                                    'onblur'  => 'javascript:confirm_email(this.value,this.id);',
//                                    'disabled' => 'disabled'
//                                );
                                $alt_email = array(
                                    'name' => 'pers_alternate_email',
                                    'id' => 'pers_alt_email',
                                    'maxlength' => '50',
                                    'value' => $profile['userdetails']['alternate_email_id'],
                                    'onblur'  => 'javascript:validate_alternate_email(this.value,this.id);'
                                );
                                ?>
<!--                                <td><?php //echo form_input($email); ?> <span id="user_registered_email_err"> </span></td>
-->                                 
                                    <td><label class="label_font"><?php echo $profile['userdetails']['registered_email_id']; ?></label></td>
                                    <td class="td_heading">Confirm Email Id:</td>
                                    <td><label class="label_font"><?php echo $profile['userdetails']['registered_email_id']; ?></label></td>

<!--
                                <td><?php //echo form_input($conf_email); ?><span id="pers_conf_email_err"></span></td>-->
                               
                                <td class="td_heading">Alternate Email Id:</td>
                                <td><?php echo form_input($alt_email); ?><span id="pers_alt_email_err"></span></td>
                            </tr>                            
                            <tr>                                                              
                                <td class="td_heading">Nationality:<span class="required">*</span></td>
                                <td >        
                                    <?php
                                    $nationality = $meta_result[Meta_Values::NATIONALITY];
                                    $nationality_options = array();
                                    $nationality_options[''] = 'Select';
                                    foreach ($nationality as $item):
                                        $nationality_options[$item['parameter_id']] = $item['category_name'];
                                    endforeach;
                                    echo form_dropdown('nationality', $nationality_options, $profile['userdetails']['nationality'], 'id="nationality"');
                                    ?>
                                    <span id="nationality_err"></span>
                                </td>
                            
                                <td class="td_heading">Race:</td>
                                <?php
                                $race = $meta_result[Meta_Values::RACE];
                                $race_options[''] = 'Select';
                                foreach ($race as $item):
                                    $race_options[$item['parameter_id']] = $item['category_name'];
                                endforeach;
                                ?>
                                <td><?php echo form_dropdown('race', $race_options, $profile['userdetails']['race']); ?>
                                </td>
                                <td class="td_heading">Occupation:<span id="span_occupation" class="required">*</span></td>
                                <?php
                                $occupation = $meta_result[Meta_Values::DESIGNATION];
                                $occupation_options[''] = 'Select';
                                foreach ($occupation as $item):
                                    $occupation_options[$item['parameter_id']] = $item['category_name'];
                                endforeach;                    
                                ?>
                                <td colspan="2">
                                    <?php echo form_dropdown('occupation', $occupation_options, $profile['userdetails']['occupation_code'],'id="occupation"'); ?>
                                    <span id="occupation_err"></span>
                                </td>
                                
                            </tr>
                            <tr>
                                <td class="td_heading">Salary Range:<span id="span_sal_range"></span></td>
                                <?php
                                $sal_range = $meta_result[Meta_Values::SAL_RANGE];
                                $sal_range_options[''] = 'Select';
                                foreach ($sal_range as $item):
                                    $sal_range_options[$item['parameter_id']] = $item['category_name'];
                                endforeach;                    
                                ?>
                                <td>
                                    <?php echo form_dropdown('sal_range', $sal_range_options, $profile['userdetails']['salary_range'],'id="sal_range"'); ?>
                                    <span id="sal_range_err"></span>
                                </td>
                                <td class="td_heading" colspan="2">Highest Education Level:<span class="required">*</span></td>
                                <?php
                                $highest_educ_level = $meta_result[Meta_Values::HIGHEST_EDUC_LEVEL];
                                $highest_educ_level_options[''] = 'Select';
                                foreach ($highest_educ_level as $item):
                                    $highest_educ_level_options[$item['parameter_id']] = $item['category_name'];
                                endforeach;                     
                                ?>
                                <td colspan="4">
                                    <?php echo form_dropdown('highest_educ_level', $highest_educ_level_options, $profile['userdetails']['highest_educ_level'],'id="highest_educ_level" style="width:300px"'); ?>
                                    <span id="highest_educ_level_err"></span>
                                </td>
                            </tr>                            
                            <tr>
                                <td class="td_heading" colspan="2">Certificate Pickup Detail:</td>
                                <?php
                                $cerit_mail = array(
                                    'name' => 'certificate_pick_pref',
                                    'value' => 'cerit_mail',
                                    'checked' => ($profile['userdetails']['certificate_pick_pref'] === 'cerit_mail' ? TRUE : FALSE)
                                );
                                $cerit_self = array(
                                    'name' => 'certificate_pick_pref',
                                    'value' => 'cerit_self',
                                    'checked' => ($profile['userdetails']['certificate_pick_pref'] === 'cerit_self' ? TRUE : FALSE)
                                );
                                $cerit_post = array(
                                    'name' => 'certificate_pick_pref',
                                    'value' => 'cerit_post',
                                    'checked' => ($profile['userdetails']['certificate_pick_pref'] === 'cerit_post' ? TRUE : FALSE)
                                );
                                ?>
                                <td colspan="4">
                                    <?php echo form_radio($cerit_mail); ?> Mail to my personal email Id &nbsp;&nbsp; 
                                    <?php echo form_radio($cerit_self); ?> I will pickup myself &nbsp;&nbsp; 
                                    <?php echo form_radio($cerit_post); ?> Mail to my postal address     
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h2 class="sub_panel_heading_style">Address</h2>
                <div class="table-responsive">
                    <table class="table table-striped">

                        <tbody>
                            <tr>
                                <td class="td_heading">Building/Street:</td>
                                <td colspan="3">
                                    <?php
                                    $p_addr = array(
                                        'name' => 'pers_personal_address_bldg',
                                        'id' => 'pers_personal_address_bldg',
                                        'maxlength' => '255',
                                        'rows' => '1',
                                        'cols' => '80',
                                        'value' => $profile['userdetails']['personal_address_bldg'],
                                        'class' => 'upper_case'
                                    );
                                    echo form_textarea($p_addr);
                                    ?>                                    
                                </td>
                                <td class="td_heading">City:</td>
                                <td><?php
                                    $attr = array(
                                        'name' => 'pers_city',
                                        'id' => 'pers_city',
                                        'maxlength' => '50',
                                        'value' => $profile['userdetails']['personal_address_city'],
                                        'class' => 'upper_case alphabets',
                                        'autocomplete' =>"off"
                                    );
                                    echo form_input($attr);
                                    ?>
                                    <span id="pers_city_err"></span>
                                </td>
                            </tr>
                            <tr>

                                <td class="td_heading">Country:</td>
                                <td>   
                                    <?php
                                    $countries = $meta_result[Meta_Values::COUNTRIES];
                                    $country_options[''] = 'Select';
                                    foreach ($countries as $item):
                                        $country_options[$item['parameter_id']] = $item['category_name'];
                                    endforeach;

                                    $attr = 'id="pers_country"';
                                    $atr = 'id="current_pers_country"';

                                    echo form_dropdown('pers_country', $country_options, $profile['userdetails']['personal_address_country'], $attr);
                                    echo form_input(array('name' => 'current_pers_country', 'type'=>'hidden', 'id' =>'current_pers_country','value'=>$profile['userdetails']['personal_address_country']));
                                    ?> 
                                    <span id="pers_country_err"></span> 
                                </td> 
                                <td class="td_heading">State:</td>
                                <td>                                    
                                    <?php
                                        $states = ($profile['userdetails']['personal_address_country']) ? $this->user_model->get_states($profile['userdetails']['personal_address_country']) : 'Select';
                                        $state_options[''] = 'Select';
                                        foreach ($states as $item) {
                                            $state_options[$item->parameter_id] = $item->category_name;
                                        }
                                        //$attr = ($trainee[userdetails]['personal_address_state'])?get_catname_by_parm($trainee[userdetails]['personal_address_state']):'Select';
                                        $attr_js = 'id="pers_states"';
                                        echo form_dropdown('pers_states', $state_options, $profile['userdetails']['personal_address_state'], $attr_js);
                                        echo form_hidden('current_pers_states', $profile['userdetails']['personal_address_state'], 'current_pers_states');
                                    ?>
                                    <span id="pers_state_err"></span>
                                </td>
                                <td class="td_heading">Zip Code:</td>
                                <td colspan="3"><?php
                                    $attr = array(
                                        'name' => 'pers_zip_code',
                                        'id' => 'pers_zipcode',
                                        'maxlength' => '10',
                                        'value' => $profile['userdetails']['personal_address_zip'],
                                        'class' => 'alphanumeric upper_case',
                                        'autocomplete' =>"off"
                                    );
                                    echo form_input($attr);
                                    ?>
                                    <span id="pers_zipcode_err"></span>
                                </td>                                
                            </tr>
                            <tr>
                                <td class="td_heading">Upload Photo:</td>
                                <td>
                                    <input type="file" name="userfile" id="userfile" onchange="showimagepreview(this)" />
                                    <label id="image_err"></label>
                                </td>
                                <?php            
                               
                                if($profile['userdetails']['photo_upload_path']): ?> 
                                    <td  id="user_image_preview" class="td_heading">&nbsp;&nbsp;&nbsp;
                                        <img width="150px"  src="<?php echo admin_url()."//tmsadmin.xprienzhr.com/".$profile['userdetails']['photo_upload_path'];?>" id="imgprvw" border="0" />                
                                        <span id="removeimagebutton" onclick="remove_image();" class="glyphicon glyphicon-remove-circle"></span>
                                    </td> 
                                    <td colspan="4">
                                        <b>Use Previous Image:</b>&nbsp;&nbsp;&nbsp;
                                        <input type="radio" name="deleteimage" checked="checked" id="deleteimageyes" value="yes"/>Yes
                                        <input type="radio" name="deleteimage" id="deleteimageno" value="no"/>No
                                    </td>
                                <?php else:?>
                                    <td id="user_image_preview" colspan="4" class="td_heading">&nbsp;&nbsp;&nbsp;&nbsp;
                                        <img height="120px" width="120px" id="imgprvw" border="0" />
                                        &nbsp;&nbsp;&nbsp;
                                        <span style="display: none;" id="removeimagebutton" onclick="remove_image();" class="glyphicon glyphicon-remove-circle"></span>
                                    </td>     
                                <?php endif;?> 
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
                <h2 class="sub_panel_heading_style">Educational Details 
                    <span class="label label-default push_right black-btn" onclick="javascript:addmore('edu')"><span class="glyphicon glyphicon-plus glyphicon1"></span> Add More</span> 
                </h2>
                <div class="table-responsive">
                    <table id="edu_tbl" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Level</th>
                                <th>Year of Completion</th>
                                <th>Score/Grade</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>

                        <tbody id="addedu">
                            <?php
                            if (!$profile[edudetails]) {
                                $edulevel = $meta_result[Meta_Values::HIGHEST_EDUC_LEVEL];
                                $edulevel_options[''] = 'Select';
                                foreach ($edulevel as $item):
                                    $edulevel_options[$item['parameter_id']] = $item['category_name'];
                                endforeach;
                                $attr_edulevel = 'id="edu_level';
                                $edu_level = form_dropdown('edu_level[]', $edulevel_options, $attr_edulevel, 'id="edu_firstcolumn_0" class="edu_level" style="width:160px"');

                                $score_grade = array(
                                    'name' => 'edu_score_grade[]',
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
                                    'id' => 'edu_remarks',
                                    'maxlength' => '50',
                                    'value' => set_value('edu_remarks'),
                                    'style' => 'width:100%',
                                    'rows' => '1',
                                    'class' => 'upper_case'
                                );
                                ?>            
                                <tr id="edu_row_0">
                                    <td><div class="form-drop"><?php echo $edu_level; ?><span id="edu_err0"></span></div></td>
                                    <td><?php echo form_input($year_of_comp); ?>
                                        <span  id="yr_comp_err_0"></span>
                                    </td>
                                    <td><?php echo form_input($score_grade); ?></td>
                                    <td><?php echo form_textarea($edu_remarks); ?> <span style='display:none' class="pull-right remove_img remove2" onClick="javascript:remove_row('edu_remove_0');"></span> </td>
                                </tr>        
                                <?php
                            }
                            $edulevel = $meta_result[Meta_Values::HIGHEST_EDUC_LEVEL];
                            $edulevel_options[''] = 'Select';
                            foreach ($edulevel as $item):
                                $edulevel_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;

                            $i = 0;
                            foreach ($profile[edudetails] as $item):
                                ?>
                                <tr id="<?php echo 'edu_row_' . $i; ?>">
                                    <td>
                                        <?php
                                        $attr_edulevel = ' id="edu_firstcolumn_' . $i . '" rowno="' . $i . '" class="edu_level" ';
                                        $edu_level = form_dropdown('edu_level[]', $edulevel_options, $item[educ_level], $attr_edulevel);
                                        echo $edu_level;
                                        ?>

                                        <span id="<?php echo 'edu_err' . $i; ?>" />
                                    </td>
                                    <td>
                                        <?php
                                        $year_of_comp = array(
                                            'value' => ($item[educ_yr_completion]) ? $item[educ_yr_completion] : '',
                                            'name' => 'edu_year_of_comp[]',
                                            'id' => 'year_of_comp_' . $i,
                                            'maxlength' => '4',
                                            'placeholder' => 'yyyy',
                                        );
                                        echo form_input($year_of_comp);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $score_grade = array(
                                            'name' => 'edu_score_grade[]',
                                            'value' => ($item[educ_score]) ? $item[educ_score] : '',
                                            'maxlength' => '10',
                                        );
                                        echo form_input($score_grade);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $edu_remarks = array(
                                            'name' => 'edu_remarks[]',
                                            'value' => ($item[educ_remarks]) ? $item[educ_remarks] : '',
                                            'id' => 'edu_remarks',
                                            'maxlength' => '50',
                                            'style' => 'width:100%',
                                            'rows' => '1',
                                            'class' => 'upper_case'
                                        );
                                        echo form_textarea($edu_remarks);
                                        ?> 
                                        <span class="pull-right remove_img remove2" onClick="javascript:remove_row('<?php echo "edu_remove_" . $i; ?>');"></span>
                                    </td>                  
                                </tr>
                                <?php
                                $i++;
                            endforeach;                             
                            ?>
                        </tbody>
                    </table>
                    <span id="addedu_err"></span>
                </div>
                <br>
                <h2 class="sub_panel_heading_style">Other Certificates and Trainings 
                    <span class="label label-default push_right black-btn" onclick="javascript:addmore('other')"><span class="glyphicon glyphicon-plus glyphicon1"></span> Add More</span> 
                </h2>
                <div class="table-responsive">
                    <table id="other_tbl" class="table table-striped">
                        <thead>
                            <tr>
                                <th width="22%">Certificate Name</th>
                                <th width="25%">Year of Certification</th>
                                <th width="19%">Validity</th>
                                <th width="34%">Remarks</th>
                            </tr>
                        </thead>

                        <tbody id="addother">
                            <?php
                            if (!$profile[otherdetails]) {
                                $certi_name = array(
                                    'name' => 'oth_certi_name[]',
                                    'id' => 'other_firstcolumn_0',
                                    'class' => 'certi_name',
                                    'rowno' => '0',
                                    'maxlength' => '50',
                                    'value' => set_value('certi_name'),
                                   'oninput' => 'this.value=this.value.toUpperCase();'
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
                                    'id' => 'other_remarks',
                                    'maxlength' => '50',
                                    'value' => set_value('other_remarks'),
                                    'class' => 'upper_case',
                                    'style' => 'width:100%',
                                    'rows' => '1',
                                );
                                ?>
                                <tr  id="other_row_0">
                                    <td><?php echo form_input($certi_name); ?><span id="other_err0"></td>
                                    <td><?php echo form_input($year_of_certi); ?></td>
                                    <td><?php echo form_input($validity); ?></td>
                                    <td><?php echo form_textarea($other_remarks); ?> <span style='display:none' class="pull-right remove_img remove2" onClick="javascript:remove_row('other_remove_0');"></span> </td>
                                </tr>                    
                                <?php
                            }
                            $i = 0;
                            foreach ($profile[otherdetails] as $item):
                                ?>
                                <tr  id="<?php echo 'other_row_' . $i; ?>">
                                    <td>
                                        <?php
                                        $certi_name = array(
                                            'name' => 'oth_certi_name[]',
                                            'id' => 'other_firstcolumn_' . $i,
                                            'class' => 'certi_name',
                                            'rowno' => $i,
                                            'value' => $item[cert_name],
                                            'class' => 'upper_case'
                                        );
                                        echo form_input($certi_name);
                                        ?>
                                        <span id="<?php echo 'other_err' . $i; ?>" />
                                    </td>
                                    <td>
                                        <?php
                                        $year_of_certi = array(
                                            'name' => 'oth_year_of_certi[]',
                                            'value' => ($item[yr_completion]) ? $item[yr_completion] : '',
                                            'id' => 'year_of_certi_' . $i,
                                            'maxlength' => '4',
                                            'placeholder' => 'yyyy',
                                            'readonly' => true
                                        );
                                        echo form_input($year_of_certi);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $validity = array(
                                            'name' => 'oth_validity[]',
                                            'id' => 'validity_' . $i,
                                            'value' => ($item[valid_till]) ? date('d-m-Y', strtotime($item[valid_till])) : NULL,
                                            'id' => 'validity_' . $i,
                                            'placeholder' => 'dd-mm-yy',
                                            'maxlength' => '25',
                                            'readonly' => true
                                        );
                                        echo form_input($validity);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $other_remarks = array(
                                            'name' => 'oth_remarks[]',
                                            'id' => 'other_remarks',
                                            'value' => $item[oth_remarks],
                                            'maxlength' => '50',
                                            'class' => 'upper_case',
                                            'style' => 'width:100%',
                                            'rows' => '1',
                                        );
                                        echo form_textarea($other_remarks);
                                        ?>
                                        <span class="pull-right remove_img remove2" onClick="javascript:remove_row('<?php echo 'other_remove_' . $i; ?>');"></span> 
                                    </td>                        
                                </tr>
                                <?php
                                $i++;
                            endforeach;                             
                            ?>
                        </tbody>
                    </table>
                    <span id="addother_err"></span>
                </div>
                <br>

                <h2 class="sub_panel_heading_style">Work Experience 
                    <span class="label label-default push_right black-btn" onclick="javascript:addmore('work')"><span class="glyphicon glyphicon-plus glyphicon1"></span> Add More</span> 
                </h2>
                <div class="table-responsive">
                    <table id="work_tbl" class="table table-striped">
                        <thead>
                            <tr>
                                <th width="22%">Name of Organization</th>
                                <th width="25%">Employment From</th>
                                <th width="19%">Employment To</th>
                                <th width="34%">Designation</th>
                            </tr>
                        </thead>
                        <?php
                        $designation = $meta_result[Meta_Values::DESIGNATION];
                        $designation_options[''] = 'select';
                        foreach ($designation as $item):
                            $designation_options[$item['parameter_id']] = $item['category_name'];
                        endforeach;
                        $attr_designation = 'id="designation"';
                        $designation = form_dropdown('work_designation[]', $designation_options, set_value('work_designation'), $attr_designation);
                        ?>
                        <tbody id="addwork">
                            <?php
                            if (!$profile[workdetails]) {
                                $org_name = array(
                                    'name' => 'work_org_name[]',
                                    'id' => 'work_firstcolumn_0',
                                    'class' => 'org_name',
                                    'rowno' => '0',
                                    'maxlength' => '50',
                                    'value' => set_value('org_name'),
                                    'class' => 'upper_case'
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
                                ?>
                                <tr  id="work_row_0">
                                    <td><?php echo form_input($org_name); ?><span id="work_err0"></td>
                                    <td><?php echo form_input($empfrom); ?></td>
                                    <td><?php echo form_input($empto); ?></td>
                                    <td><?php echo $designation; ?> <span style='display:none' class="pull-right remove_img remove2" onClick="javascript:remove_row('work_remove_0');"></span> </td>
                                </tr>
                            <?php
                            }
                            $i = 0;
                            foreach ($profile[workdetails] as $item):
                                ?>
                                <tr id="<?php echo 'work_row_' . $i; ?>">
                                    <td>
                                        <?php
                                        $org_name = array(
                                            'name' => 'work_org_name[]',
                                            'id' => 'work_firstcolumn_' . $i,
                                            'class' => 'org_name',
                                            'rowno' => $i,
                                            'value' => $item[org_name],
                                            'maxlength' => '50',
                                            'class' => 'upper_case'
                                        );
                                        echo form_input($org_name);
                                        ?>
                                        <span id="<?php echo 'work_err' . $i; ?>" />
                                    </td>
                                    <td>
                                        <?php
                                        $empfrom = array(
                                            'name' => 'work_empfrom[]',
                                            'id' => "empfrom_datetimepicker_" . $i,
                                            'value' => ($item[emp_from_date] != NULL) ? date('d-m-Y', strtotime($item[emp_from_date])) : '',
                                            'id' => 'empfrom_datetimepicker_' . $i,
                                            'maxlength' => '25',
                                            'placeholder' => 'dd-mm-yy',
                                            'readonly' => true
                                        );
                                        echo form_input($empfrom);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $empto = array(
                                            'name' => 'work_empto[]',
                                            'id' => 'empto_datetimepicker_' . $i,
                                            'value' => ($item[emp_to_date] != NULL) ? date('d-m-Y', strtotime($item[emp_to_date])) : '',
                                            'maxlength' => '25',
                                            'placeholder' => 'dd-mm-yy',
                                            'readonly' => true
                                        );
                                        echo form_input($empto);
                                        ?>
                                    </td>
                                    <td>
                                <?php echo form_dropdown('work_designation[]', $designation_options, $item[designation], $attr_designation); ?>
                                        <span class="pull-right remove_img remove2" onClick="javascript:remove_row('<?php echo 'work_remove_' . $i; ?>');"></span> 
                                    </td>                    
                                </tr>
                            <?php
                            $i++;
                            endforeach;                            
                            ?>
                        </tbody>
                    </table>  
                    <span id="addwork_err"></span>  <br>
                    <span class="required required_i">* Required Fields</span>
                    <br>
                    <div class="throw_right">
                        <button class="btn btn-primary" type="submit">
                            <span class="glyphicon glyphicon-retweet"></span>&nbsp;Update
                        </button> &nbsp; &nbsp; 
                        <!--<button class="btn btn-primary" type="reset" id="reset_all"><span class="glyphicon glyphicon-refresh"></span>&nbsp;Reset</button>-->
                    </div>
                    <?php
                    // echo form_hidden('userid', $profile[userdetails]['user_id']);
                    // echo form_hidden('task', 'update');                
                    echo form_close();
                    ?>
                </div>           
            </div>
        </div>
    </div>    
</div>          
<script type="text/javascript">
    edituser_country_of_residence = '<?php echo $profile[userdetails]['country_of_residence']; ?>';
    tax_code_type = '<?php echo $profile[userdetails]['tax_code_type']; ?>';
    tax_code_type = (tax_code_type == 'SNG_3') ? 'OTHERS' : tax_code_type;
    $("#" + edituser_country_of_residence).show();
    $("#" + edituser_country_of_residence + "_ID").show();
    $("#SGP_" + tax_code_type).show();
    // for the add more array
    edu_cnt_array = [];
    other_cnt_array = [];
    work_cnt_array = [];
    selected_exec_array = [];

    $(document).ready(function() {
          var check = 0;
    $('#trainee_edit').submit(function() {
        check = 1;
        return validate();
    });
    $('#trainee_edit select,#trainee_form input').change(function() {
        if (check == 1) {
            return validate();
        }
    });
    //for salary range and occupation manadtory based on company.
    $('#assign_company').trigger("change");
        country_of_residence = $('#country_of_residence').val();
        other_type = $("#NRIC").val();
        if (country_of_residence) {
            $('#' + country_of_residence).show();
            $("#" + country_of_residence + "_ID").show();
            $("#SGP_" + other_type).show();
        }
   
        //for on page load date and time picker            

        var oth_cnt = $('#other_tbl tbody tr').size();
        var edu_cnt = $('#edu_tbl tbody  tr').size();
        var wrk_cnt = $('#work_tbl tbody tr').size();
        for (var i = 0; i < edu_cnt; i++) {
            $("#year_of_comp_" + i).datepicker({changeYear: true, maxDate: 0, dateFormat: 'yy', yearRange: '1945:' + (new Date).getFullYear()});
            edu_cnt_array.push(i);
            var selected_edu_level = $("#edu_firstcolumn_" + i).val();
            if (selected_edu_level != "")
                selected_exec_array.push(selected_edu_level);
        }
        for (var i = 0; i < oth_cnt; i++) {
            $("#year_of_certi_" + i).datepicker({changeYear: true, maxDate: 0, dateFormat: 'yy', yearRange: '1945:' + (new Date).getFullYear(),
                onSelect: function(selectedStartDate) {
                    var cnt = i - 1;
                    $("#validity_" + cnt).datepicker("option", {
                        minDate: '01-01-' + selectedStartDate,
                        maxDate: ''
                    });
                }
            });
            $("#validity_" + i).datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                changeYear: true,
                minDate: 0,
                maxDate: -1,
                yearRange: "-100:+100",
            });
            other_cnt_array.push(i);
        }
        for (var i = 0; i < wrk_cnt; i++) {
            $("#empfrom_datetimepicker_" + i).datepicker({dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,
                maxDate: 0,
                onSelect: function(selectedStartDate) {
                    var cnt = i - 1;
                    $("#empto_datetimepicker_" + cnt).datepicker("option", {
                        minDate: selectedStartDate,
                        maxDate: 0
                    });
                }
            });
            $("#empto_datetimepicker_" + i).datepicker({dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,
                yearRange: "-50:+50",
                minDate: 0,
                maxDate: -1,
            });
            work_cnt_array.push(i);
        }
    });
 
function valid_email_address(emailAddress) {
    var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
    // alert( pattern.test(emailAddress) );
    return pattern.test(emailAddress);
}
</script>
<script src="<?php echo base_url(); ?>assets/public_js/my_profile.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/public_js/addmore.js" type="text/javascript"></script>



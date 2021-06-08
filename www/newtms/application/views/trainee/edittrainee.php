
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
<style>
    .edu_level{
        width:210px;
    }
</style>
<div class="col-md-10">
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/trainee.png"/> Trainee - Edit/ Deactivate</h2>
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
    if ($trainee):
        
        $atr = 'id="trainee_edit" name="trainee_edit"';
        
        echo form_open_multipart("trainee/edit_trainee", $atr);
        ?>
        <input type="hidden" name="edit_user_id" id="edit_user_id" value="<?php echo $edit_user_id ?>" />
        
        <input type="hidden" name="taxcode" value="<?php echo $this->input->post('taxcode'); ?>" />
        <input type="hidden" name="trainee_name" value="<?php echo $this->input->post('trainee_name'); ?>" />
        <input type="hidden" name="search_radio" value="<?php echo $this->input->post('search_radio'); ?>" />
       
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/other_details.png"/> Access Detail</h2>
        <div id ='trainee_validation_div'>
            <div class="bs-example">
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
                                    echo form_dropdown('country_of_residence', $country_options, $trainee[userdetails]['country_of_residence'], $attr);
                                    ?>
                                    <span id="country_of_residence_err"></span>
                                </td>
                                <td class="td_heading" colspan="2" >
                                    
                                        <?php
                                        $attr = array('name' => 'PAN', 'class' => 'upper_case alphanumeric', 'id' => 'PAN', 'onblur' => 'javascript:isunique_taxcode(this.value,this.id);', 'maxlength' => '50');
                                       
                                        ?>
                                        <span id="PAN_err"></span>
                                    <SPAN id="SGP" style="">NRIC Type: <span class="required">* </span>             
                                        <?php
                                        $nrics = fetch_metavalues_by_category_id(Meta_Values::NRIC);
                                        $nris_options[''] = 'Select';
                                        foreach ($nrics as $item):
                                            $nris_options[$item['parameter_id']] = $item['category_name'];
                                        endforeach;
                                        $attr = 'id="NRIC"';
                                        echo form_dropdown('NRIC', $nris_options, $trainee[userdetails]['tax_code_type'], $attr);
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
                                        echo form_dropdown('NRIC_OTHER', $nric_other_options, $trainee[userdetails]['other_identi_type'], $attr);
                                        ?>
                                        <span id="NRIC_OTHER_err"></span>
                                    </SPAN>
                                    <SPAN id="SGP_ID" style="display:none;">
                                        <br /><br />
                                        <label id="SGP_ID_label"> NRIC Code : </label><span class="required">* </span>
                                        
                                        <?php
                                        form_hidden('NRIC_ID_MATCH', $trainee[userdetails]['tax_code']);
                                        $attr = array('name' => 'NRIC_ID', 'class' => 'upper_case alphanumeric', 'id' => 'NRIC_ID', 'onblur' => 'javascript:isunique_taxcode(this.value,this.id);', 'maxlength' => '50');
                                        echo form_input($attr, $trainee[userdetails]['tax_code']);
                                        ?>
                                        <span id="NRIC_ID_err"></span>
                                    </SPAN>

                                        <?php
                                        $attr = array('name' => 'SSN', 'class' => 'upper_case alphanumeric', 'id' => 'SSN', 'onblur' => 'javascript:isunique_taxcode(this.value,this.id);', 'maxlength' => '50');
                                        ?>    
                                        <span id="SSN_err"></span>
                                </td>
                            </tr>
                            <tr>
                                <td width="14%" class="td_heading">Username:<span class="required">*</span></td>
                                <td width="40%" colspan="1"><?php echo $trainee[userdetails]['user_name']; ?> <div id="show_user_name"> </div></td> 
                                <td class="td_heading">Activation Status:</td>                        
                                <td colspan="1">
                                    <?php
                                    if ($trainee[userdetails]['account_status'] == 'ACTIVE') {
                                        echo "<label style='color:green'>ACTIVE</label>";
                                    }
                                    if ($trainee[userdetails]['account_status'] == 'PENDACT') {
                                        echo "<label style='color:blue'>Pending Activation </label>";
                                    }
                                    ?>                        
                                </td>
                            </tr>       
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <h2 class="sub_panel_heading_style">
                <img src="<?php echo base_url(); ?>/assets/images/personal_details.png"/> Personal Details
                <span class="pull-right"> <img src="<?php echo base_url(); ?>/assets/images/personal_details.png"> <a href="#ex3" rel="modal:open" style="color: blue;">Individual Discount</a></span>
            </h2>
            <div class="table-responsive">
                <table class="table table-striped">      
                    <tbody>                
                        <tr>
                            <td class="td_heading">Name:<span class="required">*</span></td>
                            <?php
                            $fn = array(
                                'name' => 'pers_first_name',
                                'id' => 'pers_first_name',
                                'maxlength' => '100',
                                'class' => 'upper_case',
                                'value' => $trainee[userdetails]['first_name'],
                                'style' => 'width:250px',
                            );
                            ?>

                            <td colspan="5"><?php echo form_input($fn); ?><span id="pers_first_name_err"></span></td>                            
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
                                echo form_dropdown('nationality', $nationality_options, $trainee[userdetails]['nationality'], 'id="nationality" style="width:170px"');
                                ?>
                                <span id="nationality_err"></span>
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
                                echo form_dropdown('gender', $gender_options, $trainee[userdetails]['gender'], 'id="pers_gender"');
                                ?>
                                <span id="pers_gender_err"></span>
                            </td>
                            <td class="td_heading">Date of Birth:							
								<span class="required">*
							</td>						
                            <td> <?php
                                $dob_input = (empty($trainee[userdetails]['dob'])) ? '' : date('d-m-Y', strtotime($trainee[userdetails]['dob']));
                                $dob = array(
                                    'name' => 'personal_dob',
                                    'id' => 'pers_dob',
                                    'maxlength' => '10',
                                    'value' => $dob_input,

                                    'placeholder' => 'dd/mm/yyyy'
                                );
                                echo form_input($dob);
                                ?>
                                <span id="pers_dob_err"></span>
                            </td>
                            <td class="td_heading">Contact Number:<span class="required">*</span></td>
                            <?php
                            $contact_p = array(
                                'name' => 'pers_contact_phone',
                                'id' => 'pers_contact_number',
                                'maxlength' => '50',
                                
                                'value' => $trainee[userdetails]['contact_number'],
                                'style' => 'width:200px',
                            );
                            ?>
                            <td><?php echo form_input($contact_p); ?><span id="pers_contact_number_err"></span></td>

                        </tr>
                        <tr>
                            <?php
                            $contact_m = array(
                                'name' => 'pers_contact_mobile',
                                'id' => 'pers_alternate_contact_number',
                                'maxlength' => '50',
                                
                                'value' => $trainee[userdetails]['alternate_contact_number'],
                                'style' => 'width:220px',
                            );
                            ?>
                            <td class="td_heading">Alt. Contact #:</td>
                            <td colspan="2"><?php echo form_input($contact_m); ?><span id="pers_alternate_contact_number_err"></td> 
                            <td class="td_heading">Race:</td>
                            <?php
                            $race = fetch_metavalues_by_category_id(Meta_Values::RACE);
                            $race_options[''] = 'Select';
                            foreach ($race as $item):
                                $race_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;
                            $attr = 'id="race" style="width:150px"';
                            ?>
                            <td colspan="2">
                                <?php echo form_dropdown('race', $race_options, $trainee[userdetails]['race'], $attr); ?>
                            </td>
                        </tr>
                        <tr>
                            <?php 
                            if ($user->role_id == 'ADMN' || $user->role_id == 'CRSEMGR' || $user->role_id == 'SLEXEC') 
                            { 
                               
                             ?>
                                <td class="td_heading">Assign Trainee to Company:</td>
                                <?php
                                 $trainee_id_name=$trainee['company']['company_id']."/".$trainee['company']['company_name'];
                                
                                $compnies = getcompnies();
                                if ($user->role_id == 'ADMN' || $user->role_id == 'CRSEMGR' || $user->role_id == 'SLEXEC') 
                                {
                                    $company_options[''] = 'Select';
                                }
                                foreach ($compnies as $item):
                                    $id_name=$item['company_id']."/".$item['company_name'];
                                    $company_options[$id_name] = $item['company_name'];
                                   // echo "<br/>";
                                endforeach;
                              
                                ?>
                                <td colspan="5">
                                    <?php echo form_dropdown('assign_company', $company_options, 
                                            $trainee_id_name, 'id="assign_company" style="width:550px"'); ?>
                                </td>
                            <?php 
                            } 
                            else if ($trainee['company']['company_id'] != '') 
                            { 
                                 ?>
                                <td class="td_heading">Assign Trainee to Company:</td>
                                <?php
                                $compnies = getcompnies();
                                foreach ($compnies as $item) 
                                {
                                    if ($item['company_id'] == $trainee['company']['company_id'])
                                    {
                                         $id_name=$item['company_id']."/".$item['company_name'];
                                        $company_options[$item['company_id']] = $item['company_name'];
                                    }
                                }
                                ?>
                                <td colspan="5">
                                    <?php echo form_dropdown('assign_company', $company_options, $trainee['company']['company_id'],
                                            'id="assign_company"'); ?>
                                </td>
                            <?php 
                            } 
                            ?>    
                        </tr>
                        <!--added by pritam-->
                        <?php 
//                        echo "Cert Sne t TO(".$trainee[userdetails]['cert_sent_to'].")";
                                              
                        //f(!$trainee['company']['company_id'])
                        //{
                           //   echo "Company(".$trainee[userdetails]['comp_name'].")";
                       
                        ?>
                        
                        <tr id="cert_sent_t">
                            <?php 
                            if ($user->role_id == 'ADMN' || $user->role_id == 'CRSEMGR' || $user->role_id == 'SLEXEC') 
                            { 
                               
                                ?>
                                <td class="td_heading">Certificate Sent To:</td>
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
                                <td colspan="5">
                                    <?php echo form_dropdown('cert_sent_to', $company_options, 
                                            $trainee[userdetails]['cert_sent_to'], 'id="cert_sent_to" style="width:550px"');
                                   
                                    $data=array(
                                            'name'=> 'cert_sent_to_others',
                                            'id' => 'cert_sent_to_others',
                                            'value' => $trainee[userdetails]['cert_sent_to'],
                                            
                                        );
                                        echo form_input($data);
                                    
                                    ?>
                                    
                                </td>
                            <?php 
                               
                            } 
                            else if ($trainee[userdetails]['cert_sent_to']!= '') 
                            {
                                 ?>
                                <td class="td_heading">Certificate Sent To1:</td>
                                <?php
                               echo $flag=0;
                                $compnies = getcompnies();
                                foreach ($compnies as $item) 
                                {
                                    if ($item['company_id'] == $trainee[userdetails]['cert_sent_to'])
                                    {    
                                        $flag=1;
                                        $company_options[$item['company_id']] = $item['company_name'];
                                    }
                                }
                                ?>
                                <td colspan="5">
                                    <?php 
                                  
                                    if($flag==1){
                                    echo form_dropdown('cert_sent_to', $company_options, $trainee[userdetails]['cert_sent_to'],
                                    'id="cert_sent_to"'); }
                                    else
                                    {
                                        $data=array(
                                            'name'=> 'cert_sent_to_others',
                                            'id' => 'cert_sent_to_others',
                                            'value' => $trainee[userdetails]['cert_sent_to'],
                                            
                                        );
                                        echo form_input($data);
                                    }
                                    ?>
                                </td>
                            <?php 
                            
                            } 
                       // }
                           
                           ?>    
                        </tr>
                        
                        <!--end-->
                        <tr>                                        
                            <td class="td_heading">Salary Range:<span id="span_sal_range"></span></td>
                            <?php
                            $sal_range = fetch_metavalues_by_category_id(Meta_Values::SAL_RANGE);
                            $sal_range_options[''] = 'Select';
                            foreach ($sal_range as $item):
                                $sal_range_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;
                            $attr = 'id="sal_range"';
                            ?>
                            <td>
                                <?php echo form_dropdown('sal_range', $sal_range_options, $trainee[userdetails]['salary_range'], $attr); ?>
                                <span id="sal_range_err"></span>
                            </td>
                            <td class="td_heading">Designation:<span id="span_occupation"></span></td>
                            <?php
                            $occupation = fetch_metavalues_by_category_id(Meta_Values::OCCUPATION);
                            $occupation_options[''] = 'Select';
                            foreach ($occupation as $item):
                                $occupation_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;
                            ?>
                            <td colspan="3">
                                <?php echo form_dropdown('occupation', $occupation_options, $trainee[userdetails]['occupation_code'], 'id="occupation" style="width:250px"'); ?>
                                <span id="occupation_err"></span>
                            </td>
                        </tr>               
                        <tr>
                            <td class="td_heading">Email Activation:<span class="required">*</span></td>
                            <td colspan="5">
                                <?php
                                $bypassemail_info = $trainee[userdetails][acc_activation_type];
                                $bypass = array(
                                    'id' => 'bypassemail_1',
                                    'name' => 'bypassemail',
                                    'checked' => ($bypassemail_info == 'BPEMAC') ? TRUE : FALSE,
                                    'value' => 'BPEMAC',
                                    'class' => 'email',
                                );
                                $donotbypass = array(
                                    'id' => 'bypassemail_2',
                                    'name' => 'bypassemail',
                                    'checked' => ($bypassemail_info == 'EMACRQ') ? TRUE : FALSE,
                                    'value' => 'EMACRQ',
                                    'class' => 'email',
                                );
                                ?>
                                <?php
                                if ($trainee[userdetails]['account_status'] == 'PENDACT') {
                                    echo form_radio($bypass);
                                    ?>By-pass email activation &nbsp;&nbsp; 
                                    <?php
                                    echo form_radio($donotbypass);
                                    ?> Do not By-pass email activation
                                    <?php
                                } else { 
                                    echo ($bypassemail_info == 'BPEMAC') ? 'By-pass email activation' : 'Do not By-pass email activation';
                                }
                                $display_email = ($bypassemail_info == 'BPEMAC') ? 'none' : '';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="td_heading">Email Id:
                                <!--<span id="span_email_id" class="required" style="display:<?php //echo $display_email; ?>">*</span>-->
								<span id="span_email_id" class="required">*</span>
                            </td>
                            <?php
                            $email = array(
                                'name' => 'user_registered_email',
                                'id' => 'user_registered_email',
                                'maxlength' => '50',
                                'value' => $trainee[userdetails]['registered_email_id'],
                                'onblur' => 'javascript:isunique_email(this.value,this.id);',
                                'style' => 'width:250px',
                            );
                            $conf_email = array(
                                'name' => 'pers_conf_email',
                                'maxlength' => '50',
                                'id' => 'pers_conf_email',
                                'value' => $trainee[userdetails]['registered_email_id'],
                                'onblur' => 'javascript:confirm_email(this.value);',
                                'style' => 'width:250px',
                            );
                            ?>
                            <td colspan="2"><?php echo form_input($email); ?> <span id="user_registered_email_err"></span></td>
                            <td class="td_heading">Confirm Email Id:<span id="span_confirm_email_id" class="required" style="display:<?php echo $display_email; ?>">*</span></td>
                            <td colspan="2"> <?php echo form_input($conf_email); ?><span id="pers_conf_email_err"></span></td>
                            </txr>
                        <tr>
    <?php
    $alt_email = array(
        'name' => 'pers_alt_email',
        'maxlength' => '50',
        'id' => 'pers_alt_email',
        'value' => $trainee[userdetails]['alternate_email_id'],
        'onblur' => 'javascript:validate_alternate_email(this.value,this.id);',
        'style' => 'width:250px',
    );
    ?>
                            <td class="td_heading">Alternate Email Id:</td>
                            <td colspan="5">
    <?php echo form_input($alt_email); ?><span id="pers_alt_email_err"></span>
                            </td>

                        </tr>
                        <tr>
                            <td class="td_heading">Highest Education Level:<span class="required">*</span></td>
    <?php
    $highest_educ_level = fetch_metavalues_by_category_id(Meta_Values::HIGHEST_EDUC_LEVEL);
    $highest_educ_level_options[''] = 'Select';
    foreach ($highest_educ_level as $item):
        $highest_educ_level_options[$item['parameter_id']] = $item['category_name'];
    endforeach;
    ?>
                            <td colspan="5">
                            <?php echo form_dropdown('highest_educ_level', $highest_educ_level_options, $trainee[userdetails]['highest_educ_level'], 'id="highest_educ_level" style="width:900px;"'); ?>
                                <span id="highest_educ_level_err"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="td_heading">Certificate Pickup Detail:</td>
    <?php
    $pickup_info = $trainee[userdetails][certificate_pick_pref];
    $cerit_mail = array(
        'name' => 'certificate_pick_pref',
        'value' => 'cerit_mail',
        'checked' => ($pickup_info == 'cerit_mail') ? TRUE : FALSE
    );
    $cerit_self = array(
        'name' => 'certificate_pick_pref',
        'value' => 'cerit_self',
        'checked' => ($pickup_info == 'cerit_self') ? TRUE : FALSE
    );
    $cerit_post = array(
        'name' => 'certificate_pick_pref',
        'value' => 'cerit_post',
        'checked' => ($pickup_info == 'cerit_post') ? TRUE : FALSE
    );
    ?>
                            <td colspan="5"><?php echo form_radio($cerit_mail); ?> Mail to my personal email Id &nbsp;&nbsp; 
                            <?php echo form_radio($cerit_self); ?> I will pickup myself &nbsp;&nbsp; 
                                <?php echo form_radio($cerit_post); ?> Mail to my postal address    </td>		  
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
                            <td class="td_heading">Building/Street:
							<?php if($tenant_id=='T24'){ ?>
								<span class="required">*
							<?php } ?>
							</td>
								<?php
								$p_addr = array(
									'name' => 'pers_personal_address',
									'id' => 'pers_personal_address_bldg',
									'maxlength' => '255',
									'rows' => '1',
									'cols' => '70',
									'value' => $trainee[userdetails]['personal_address_bldg'],
									'class' => 'upper_case'
								);
								?>
                            <td colspan="3"><?php echo form_textarea($p_addr); ?>
								<span id="pers_personal_address_bldg_err"></span>
							</td>
                            <td class="td_heading">City:
							<?php if($tenant_id=='T24'){ ?>
								<span class="required">*
							<?php } ?>
							</td>
    <?php
    $city = array(
        'name' => 'pers_city',
        'id' => 'pers_city',
        'maxlength' => '50',
        'value' => $trainee[userdetails]['personal_address_city'],
        'class' => 'upper_case alphabets',
        'style' => 'width:250px',
    );
    ?>
                            <td><?php echo form_input($city); ?><span id="pers_city_err"></span></td>
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
    echo form_dropdown('pers_country', $country_options, $trainee[userdetails]['personal_address_country'], $attr);
    echo form_hidden('current_pers_country', $trainee[userdetails]['personal_address_country'], 'current_pers_country');
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
    $states = ($trainee[userdetails]['personal_address_country']) ? $this->traineemodel->get_states($trainee[userdetails]['personal_address_country']) : 'Select';
    $state_options[''] = 'Select';
    foreach ($states as $item) {
        $state_options[$item->parameter_id] = $item->category_name;
    }
    $attr_js = 'id="pers_states"';
    echo form_dropdown('pers_states', $state_options, $trainee[userdetails]['personal_address_state'], $attr_js);
    echo form_hidden('current_pers_states', $trainee[userdetails]['personal_address_state'], 'current_pers_states');
    ?>                        
                            <span id="pers_states_err"></span></td> 
                    
                    <td class="td_heading">Postal Code:
					<?php if($tenant_id=='T24'){ ?>
						<span class="required">*
					<?php } ?>
					</td>
    <?php
    $zip = array(
        'name' => 'personal_address_zip',
        'id' => 'pers_zipcode',
        'value' => $trainee[userdetails]['personal_address_zip'],
        'maxlength' => '10',
        'class' => 'alphanumeric upper_case'
    );
    ?>
                    <td><?php echo form_input($zip); ?><span id="pers_zipcode_err"></span></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Upload Photo:</td>
                        <td>
                            <input type="file" name="userfile" id="userfile" onchange="showimagepreview(this)" />
                            <label id="image_err"></label>
                        </td>
    <?php if ($trainee[userdetails]['photo_upload_path']): ?> 
                            <td  id="user_image_preview" class="td_heading">&nbsp;&nbsp;&nbsp;
                                <img width="150px"  src="<?php echo base_url() . $trainee[userdetails]['photo_upload_path']; ?>" id="imgprvw" border="0" />                
                                <span id="removeimagebutton" onclick="remove_image();" class="glyphicon glyphicon-remove-circle"></span>
                            </td> 
                            <td colspan="4">
                                <b>Use Previous Image:</b>&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="deleteimage" checked="checked" id="deleteimageyes" value="yes"/>Yes
                                <input type="radio" name="deleteimage" id="deleteimageno" value="no"/>No
                            </td>
    <?php else: ?>
                            <td id="user_image_preview" colspan="4" class="td_heading">&nbsp;&nbsp;&nbsp;&nbsp;
                                <img height="120px" width="120px" id="imgprvw" border="0" />
                                &nbsp;&nbsp;&nbsp;
                                <span style="display: none;" id="removeimagebutton" onclick="remove_image();" class="glyphicon glyphicon-remove-circle"></span>
                            </td>     
    <?php endif; ?> 
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
                        <th>Level</th>
                        <th>Year of Completion</th>
                        <th>Score/Grade</th>
                        <th>Remarks</th>
                    </tr>
                </thead>

                <tbody id="addedu">
    <?php
    if (!$trainee[edudetails]) {
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
    $edulevel = fetch_metavalues_by_category_id(Meta_Values::HIGHEST_EDUC_LEVEL);
    $edulevel_options[''] = 'Select';
    foreach ($edulevel as $item):
        $edulevel_options[$item['parameter_id']] = $item['category_name'];
    endforeach;

    $i = 0;
    foreach ($trainee[edudetails] as $item):
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
            'id' => 'edu_score_grade_'. $i,
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
            'id' => 'edu_remarks_'. $i,
            'maxlength' => '50',
            'style' => 'width:90%',
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
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/personal_details.png"/> Other Certificates and Trainings 
            <span class="label label-default push_right black-btn" onclick="javascript:addmore('other')"><span class="glyphicon glyphicon-plus glyphicon1"></span> Add More</span> 
        </h2>
        <div class="table-responsive">
            <table id="other_tbl" class="table table-striped">
                <thead>
                    <tr>
                        <th width="30%">Certificate Name</th>
                        <th width="15%">Year of Cert.</th>
                        <th width="15%">Validity</th>
                        <th width="40%">Remarks</th>
                    </tr>
                </thead>

                <tbody id="addother">
    <?php
    if (!$trainee[otherdetails]) {
        $certi_name = array(
            'name' => 'oth_certi_name[]',
            'id' => 'other_firstcolumn_0',
            'class' => 'certi_name',
            'rowno' => '0',
            'maxlength' => '50',
            'value' => set_value('certi_name'),
            'class' => 'upper_case',
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
                        <tr  id="other_row_0">
                            <td><?php echo form_input($certi_name); ?><span id="other_err0"></td>
                            <td><?php echo form_input($year_of_certi); ?></td>
                            <td><?php echo form_input($validity); ?></td>
                            <td><?php echo form_textarea($other_remarks); ?> <span style='display:none' class="pull-right remove_img remove2" onClick="javascript:remove_row('other_remove_0');"></span> </td>
                        </tr>                    
        <?php
    }
    $i = 0;
    foreach ($trainee[otherdetails] as $item):
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
            'value' => ($item[valid_till]) ? date('d-m-Y', strtotime($item[valid_till])) : '',
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
            'id' => 'other_remarks_'.$i,
            'value' => $item[oth_remarks],
            'maxlength' => '50',
            'class' => 'upper_case',
            'style' => 'width:90%',
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

        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/officail_details.png"/> Work Experience 
            <span class="label label-default push_right black-btn" onclick="javascript:addmore('work')"><span class="glyphicon glyphicon-plus glyphicon1"></span> Add More</span> 
        </h2>
        <div class="table-responsive">
            <table id="work_tbl" class="table table-striped">
                <thead>
                    <tr>
                        <th width="30%">Name of Organization</th>
                        <th width="15%">Employment From</th>
                        <th width="15%">Employment To</th>
                        <th width="40%">Designation</th>
                    </tr>
                </thead>
    <?php
    $designation = fetch_metavalues_by_category_id(Meta_Values::DESIGNATION);
    $designation_options[''] = 'Select';
    foreach ($designation as $item):
        $designation_options[$item['parameter_id']] = $item['category_name'];
    endforeach;
    $attr_designation = 'id="designation_0"';
    $designation = form_dropdown('work_designation[]', $designation_options, set_value('work_designation'), $attr_designation);
    ?>
                <tbody id="addwork">
                <?php
                if (!$trainee[workdetails]) {
                    $org_name = array(
                        'name' => 'work_org_name[]',
                        'id' => 'work_firstcolumn_0',
                        'class' => 'org_name',
                        'rowno' => '0',
                        'maxlength' => '50',
                        'value' => set_value('org_name'),
                        'class' => 'upper_case',
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
    foreach ($trainee[workdetails] as $item):
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
            'class' => 'upper_case',
            'style' => 'width:250px;'
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
            'value' => (!empty($item[emp_from_date])) ? date('d-m-Y', strtotime($item[emp_from_date])) : '',
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
            'value' => (!empty($item[emp_to_date])) ? date('d-m-Y', strtotime($item[emp_to_date])) : '',
            'maxlength' => '25',
            'placeholder' => 'dd-mm-yy',
            'readonly' => true
        );
        echo form_input($empto);
        ?>
                            </td>
                            <td>
        <?php 
        $attr_designation = 'id="designation_"'.$i;
        echo form_dropdown('work_designation[]', $designation_options, $item[designation], $attr_designation); ?>
                                <span class="pull-right remove_img remove2" onClick="javascript:remove_row('<?php echo 'work_remove_' . $i; ?>');"></span> 
                            </td>                    
                        </tr>
        <?php
        $i++;
    endforeach;
    ?>
                </tbody>
            </table>  
            <span id="addwork_err"></span>
        </div>
        </br>
    <?php
    $display = 'none';
    $display_contents = 'none';
    if ($trainee[userdetails]['account_status'] == 'PENDACT' && $bypassemail_info == 'BPEMAC') {
        $display = '';
    }
    if ($trainee[userdetails]['account_status'] == 'PENDACT' && $bypassemail_info == 'EMACRQ') {
        $display_contents = '';
    }
    ?>
        <span colspan="4" id="span_activate_user" style="display:<?php echo $display; ?>">
        <?php
        if ($trainee[userdetails]['account_status'] == 'PENDACT') {
            $now = array(
                'name' => 'activate_user',
                'checked' => ($trainee[userdetails]['account_status'] == 'ACTIVE') ? TRUE : FALSE,
                'value' => 'ACTIVE'
            );
            $later = array(
                'name' => 'activate_user',
                'value' => 'PENDACT',
                'checked' => ($trainee[userdetails]['account_status'] == 'PENDACT') ? TRUE : FALSE,
            );
            ?>
                <?php echo form_radio($now); ?>Activate Trainee Now &nbsp;&nbsp; 
                <?php echo form_radio($later); ?> Activate Trainee Later    
            <?php } ?>
        </span>
        <br>
        <span colspan="4">
            <i id="BPEMAC_content" style='display: <?php echo $display; ?>'>(On activation, mail will be sent to the trainee's email Id, with the username.)</i>
            <i id="EMACRQ_content" style="display:<?php echo $display_contents; ?>">
                (An activation mail will be sent to the trainee's email Id, with the username and password.
                Trainee will be required to click on the activation link to get the account activated.)
            </i>
        </span>
        <br>
        <span class="required required_i">* Required Fields</span>
        <br>
        <div class="throw_right">
            <button id="update_btn" class="btn btn-primary" type="submit">
                <span class="glyphicon glyphicon-retweet"></span>
                &nbsp;Update
            </button> &nbsp; &nbsp;  
            <?php
            if ($trainee[userdetails]['account_status'] == 'ACTIVE') 
            {
                if ($payment_status > 0) 
                {
                    $link = "#ex9";
                } 
                else 
                {
                    $link = "#ex8";
                }
                ?>
            <a class="small_text" rel="modal:open" href="<?php echo $link ?>">
                <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-remove-sign"></span>&nbsp;Deactivate</button>
            </a> &nbsp; &nbsp;
            <?php 
            } ?>
        </div>
    <?php if (empty($trainee['discountdetails'])) { ?>
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
                            foreach ($trainee['discountdetails'] as $row):
                                $k = $row['course_id'];
                                echo "<tr>"
                            . "<td>" . $row['crse_name'] . "</td>"
                            . "<td><input type='text' maxlength='10' id='indi_disc_" . $k . "' class='indi_disc' data-key='" . $k . "' value='" . number_format($row['discount_percent'], 2, '.', '') . "' name='indi_disc[" . $k . "]'>%<span id='indi_disc_" . $k . "_err'></span></td>"
                            . "<td>$ <input type='text' maxlength='10' id='indi_disc_amt_" . $k . "' class='indi_disc_amt' data-key='" . $k . "' value='" . number_format($row['discount_amount'], 2, '.', '') . "' name='indi_disc_amt[" . $k . "]'><span id='indi_disc_amt_" . $k . "_err'></span></td>"
                            . "</tr>";
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
                <br>
                <span id="popup_error_err"></span>
                <div class="popup_cance89">
                    <a rel="modal:close" href="#"><button type="button" class="btn btn-primary disc_save">Save</button></a>
                </div>
                </p>
            </div>
    <?php } ?>
        <?php
        echo form_hidden('userid', $trainee[userdetails]['user_id']);
        echo form_hidden('task', 'update');
        echo form_hidden('update_edu', implode(',', $update_edu));
        echo form_hidden('update_other', implode(',', $update_other));
        echo form_hidden('update_work', implode(',', $update_work));
        echo form_close();
        ?>                       


    <?php
    $form_attributes = array('name' => 'deactivate_trainee_form', 'id' => 'deactivate_trainee_form', "onsubmit" => "return(validate_deactivate_user());");
    echo form_open("trainee/edit_trainee", $form_attributes);
    ?>
        <div class="modal1_055" id="ex8" style="display:none;">
            <p>
            <h2 class="panel_heading_style">Deactivate Trainee</h2>         
            <strong> De-Activation Date:<span class="red">*</span> </strong>
            <label id="deactivation_date" class='error'></label>
            <span id="deactivation_date_err"></span>
            <br><br>
            <strong>Reason for De-Activation:<span class="red">*</span></strong> 
    <?php
    $d_reasons = fetch_metavalues_by_category_id(Meta_Values::DEACTIVATE_REASONS);
    $reasons_options[''] = 'Select';
    foreach ($d_reasons as $item):
        $reasons_options[$item['parameter_id']] = $item['category_name'];
    endforeach;
    $reasons_options['OTHERS'] = 'Others';
    $attr = 'id="reason_for_deactivation"';
    echo form_dropdown('reason_for_deactivation', $reasons_options, $this->input->post('reason_for_deactivation'), $attr);
    $company_name_data = array(
        'name' => 'hiddencompanyid',
        'id' => 'hiddencompanyid',
        'class' => 'hiddencompanyid',
        'type' => 'hidden',
        'value' => $trainee['company']['company_id']
    );
    echo form_input($company_name_data);
    ?> &nbsp; 
            <span id="reason_for_deactivation_err"></span>

            <span id="other_reason" style="display:none;">
    <?php
    $attr = array(
        'name' => 'other_reason_for_deactivation',
        'id' => 'other_reason_for_deactivation',
        'size' => 35,
        'style' => 'margin:5px 0 0 27.5%',
        'class' => 'upper_case'
    );
    echo form_input($attr);
    echo form_hidden('course_id_deactive', $course_data->course_id);
    ?>  
                <span id="other_reason_for_deactivation_err"></span>
            </span>
            <br><br>


            Are you sure you want to deactivate this User?
            <br>
            <span class="required_i red">*Required Field</span>

            <div class="popup_cancel9">
                <div rel="modal:close"><button class="btn btn-primary" type="submit">Save</button>&nbsp;&nbsp;<a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a>
                </div>
            </div>
        </div>
    <?php
    echo form_hidden('userid', $trainee[userdetails]['user_id']);
    echo form_hidden('task', 'deactivate');
    echo form_close();
endif;
?>
    <div class="modal1_055" id="ex9" style="display:none;">         
        <h2 class="panel_heading_style">Warning</h2>   
        <div  style="margin-top:7%">
            <p style="text-align: center;">This trainee has un-paid invoices. Please clear his payments, before deactivating the account.</p>
            <div class="popup_cancel9">
                <div rel="modal:close">
                    <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal1_055" id="ex11" style="display:none;">         
        <h2 class="panel_heading_style">Warning</h2>   
        <div  style="margin-top:7%">
            <p style="text-align: center;">
                We have found a trainee with similar NRIC/ FIN in our database.
                This user is not currently linked with your Training Organisation/ Institute.
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
<div class="modal1_055" id="ex22">         
    <h2 class="panel_heading_style">Warning</h2>   
    <div  style="margin-top:7%">
        <p style="text-align: center;" >
            The trainees whom you are about to register is part of the restricted list. Please acknowledge to continue !!!
        </p>
        <div class="popup_cancel9">
            <span rel="modal:close">
                <a href="#" rel="modal:close"><button class="btn btn-primary trainee_deatils_yes" type="button">Yes, I understand.</button></a>
            </span>
            
        </div>
    </div>
</div>

<!--//////////////////////////////////////////////////////-->
    <script type="text/javascript">
        $role_id = "<?php echo $this->session->userdata('userDetails')->role_id;?>";//added by shubhranshu
        $privilage = "<?php echo $privilage;?>"; //added by shubhranshu
        edituser_country_of_residence = '<?php echo $trainee[userdetails]['country_of_residence']; ?>';
        tax_code_type = '<?php echo $trainee[userdetails]['tax_code_type']; ?>';
        $("#" + edituser_country_of_residence).show();
        $("#" + edituser_country_of_residence + "_ID").show();
        $("#SGP_" + tax_code_type).show();
        other_identi_type = '<?php echo $trainee[userdetails]['other_identi_type']; ?>';         
        

        $("#deactivation_date").text($.datepicker.formatDate("dd/mm/yy", new Date()));
        edu_cnt_array = [];
        other_cnt_array = [];
        work_cnt_array = [];
        selected_exec_array = [];

        $(document).ready(function() {
            country_of_residence = $('#country_of_residence').val();
            other_type = $("#NRIC").val();
            other_type = (other_type == 'SNG_3') ? 'OTHERS' : other_type;
            if (country_of_residence) {
                $('#' + country_of_residence).show();
                $("#" + country_of_residence + "_ID").show();
                $("#SGP_" + other_type).show();
            }
            if(other_identi_type == 'NOTAXCODE') {
                $('#SGP_ID').hide();
            }
            $('#reset_form').click(function() {
                $(".error").text("").removeClass('error');
                $('#trainee_edit').each(function() {
                    this.reset();
                });
                reset_states();
                $('#country_of_residence').trigger('change');
            });

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
            
            for (var j = 0; j < oth_cnt; j++) {                
                $("#year_of_certi_" + j).datepicker({changeYear: true, maxDate: 0, dateFormat: 'yy', yearRange: '1945:' + (new Date).getFullYear(),
                    onSelect: function(selectedStartDate) {                         
                        var cnt_other = $(this).attr('id');
                        var validity_sub_str = cnt_other.substring(14);                        
                        $("#validity_" + validity_sub_str).datepicker("option", {
                            minDate: '01-01-' + selectedStartDate,
                            maxDate: ''
                        });
                    }
                });
                $("#validity_" + j).datepicker({
                    dateFormat: 'dd-mm-yy',
                    changeMonth: true,
                    changeYear: true,
                    minDate: '01-01-' + $("#year_of_certi_" + j).val(),
                    maxDate: "",
                    yearRange: "-100:+100",
                });
                other_cnt_array.push(j);
            }
           
            for (var i = 0; i < wrk_cnt; i++) {
                $("#empfrom_datetimepicker_" + i).datepicker({dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,
                    maxDate: 0,
                    onSelect: function(selectedStartDate) {
                        
                        var cnt_wrk = $(this).attr('id');
                        var wrk_sub_str = cnt_wrk.substring(23);                        
                        $("#empto_datetimepicker_" + wrk_sub_str).datepicker("option", {
                        
                            minDate: selectedStartDate,
                            maxDate: 0
                        });
                    }
                });
                $("#empto_datetimepicker_" + i).datepicker({dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,
                    yearRange: "-50:+50",
                    
                    minDate: $("#empfrom_datetimepicker_" + i).val(),
                    
                });
                work_cnt_array.push(i);
            }
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
                tbl_row = tbl_row.replace('validity_' + first_tr_id_number[2], 'validity_' + cnt);
                tbl_row = tbl_row.replace('class="hasDatepicker"', '');
                tbl_row = tbl_row.replace('empfrom_datetimepicker_' + first_tr_id_number[2], 'empfrom_datetimepicker_' + cnt);
                tbl_row = tbl_row.replace('class="hasDatepicker"', '');
                tbl_row = tbl_row.replace('empto_datetimepicker_' + first_tr_id_number[2], 'empto_datetimepicker_' + cnt);
                tbl_row = tbl_row.replace('class="hasDatepicker"', '');
                $('#' + e + '_tbl tr').last().after(tbl_row);
                $('#' + e + '_row_' + cnt).find('input:text').val('');
                $('#' + e + '_row_' + cnt).find('textarea').val('');
                $('#' + e + '_row_' + cnt).find('select').val('');
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
            for (var i = 0; i < cnt_array.length; i++) {
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

        $('.email').change(function() {

            if ($("#bypassemail_1").is(":checked")) {
                var email = $.trim($('#user_registered_email').val());
                if (email == '') {
                    //$("#user_registered_email_err").text("").removeClass('error');
                    //$("#user_registered_email").removeClass('error');
                    //$("#pers_conf_email_err").text("").removeClass('error');
                    //$("#pers_conf_email").removeClass('error');
                }
                $('#span_activate_user').css("display", "");
                $('#BPEMAC_content').css("display", "");
                $('#EMACRQ_content').css("display", "none");
                //$("#span_email_id").css("display", "none");
                //$("#span_confirm_email_id").css("display", "none");
            } else {
                $('#span_activate_user').css("display", "none");
                $('#BPEMAC_content').css("display", "none");
                $('#EMACRQ_content').css("display", "");
                //$("#span_email_id").css("display", "");
                //$("#span_confirm_email_id").css("display", "");
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
                    $("#cert_sent_to_others").hide();
                }
                else if (arr[1]== "OTHERS")
                {
                    $("#cert_sent_t").show();
                    $("#cert_sent_to_others").show();
                     $("#cert_sent_to").hide();
                     
                }
                else
                {
                     $("#cert_sent_to").hide();
                     $("#cert_sent_t").hide();
                      $("#cert_sent_to_others").hide();
                }
            } 
            else 
            {
                $("#cert_sent_to").hide();
                $("#cert_sent_t").hide();
                $("#cert_sent_to_others").hide();
            }
        });
        // end

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
                $('#NRIC_OTHER option[value=NOTAXCODE]').attr('selected','selected');////added by shubhranshu
                $("#SGP_ID").hide();
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
            $("#pers_dob").datepicker({
                dateFormat: 'dd-mm-yy',
                minDate: new Date(startYear, currenyMonth, CurrentDate),
                maxDate: new Date(endYear, currenyMonth, CurrentDate),
                changeMonth: true,
                changeYear: true,
                yearRange: '-110:+0'
            });
            $("#deactivation_date").datepicker({dateFormat: 'dd-mm-yy'});
        });

            var country_param = $('#pers_country').val();
            if (country_param) {
                $.post('<?php echo site_url(); ?>trainee/get_states_json', {country_param: country_param}, function(data) {
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

        $('#emp_country').change(function() {
            var country_param = $(this).val();
            if (country_param) {
                $.post('<?php echo site_url(); ?>Internal_User_Controller/get_states_json', {country_param: country_param}, function(data) {
                    json_data = $.parseJSON(data);
                    $emp_states_select = $("#emp_states");
                    $emp_states_select.html('<option value="">Select</option>');
                    $.each(json_data, function(i, item) {
                        $emp_states_select.append('<option value="' + item.parameter_id + '">' + item.category_name + '</option>');
                    });
                });
            }
        });

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
            } else {
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
                } else if (pan_error_text != '[code exists!]') {
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
            pers_first_name = $.trim($("#pers_first_name").val());
            if (pers_first_name == "") {
                $("#pers_first_name_err").text("[required]").addClass('error');
                $("#pers_first_name").addClass('error');
                retVal = false;
            } else {
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

			//if($tenant_id == 'T24') {
				pers_dob = $.trim($("#pers_dob").val());            
				if (valid_date_field(pers_dob) == false && pers_dob.trim().length > 0) {
					$("#pers_dob_err").text("[dd-mm-yy format]").addClass('error');
					$("#pers_dob").removeClass('error');
					retVal = false;
				} else if(pers_dob == "") {
					$("#pers_dob_err").text("[required]").addClass('error');
					$("#pers_dob").addClass('error');
				} else {
					$("#pers_dob_err").text("").removeClass('error');
					$("#pers_dob").removeClass('error');
				}
			//} else {
			//	pers_dob = $.trim($("#pers_dob").val());            
			//	if (valid_date_field(pers_dob) == false && pers_dob.trim().length > 0) {
			//		$("#pers_dob_err").text("[dd-mm-yy format]").addClass('error');
			//		$("#pers_dob").removeClass('error');
			//		retVal = false;
			//	} else {
			//		$("#pers_dob_err").text("").removeClass('error');
			//		$("#pers_dob").removeClass('error');
			//	}
			//}

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
                    retVal = false;
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

            user_registered_email = $.trim($("#user_registered_email").val());
            //if ($("#bypassemail_2").is(":checked")) {
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
            //} else {
            //    $("#user_registered_email_err").text("").removeClass('error');
            //    $("#user_registered_email").removeClass('error');
            //    $("#pers_conf_email_err").text("").removeClass('error');
            //    $("#pers_conf_email").removeClass('error');
            //}            

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
                $('#update_btn').css('display','none');
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
            remove_err('#popup_error');
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
            if($retval == true){
                var disc_perc = [];
                var disc_amt = [];
                $('.indi_disc').each(function(){
                    var same = $(this);
                    var key = same.data('key');
                    
                    if(same.val().trim() == '' || same.val().trim() == 0){
                        disc_perc[key] = '0.00';
                    } else {
                        disc_perc[key] = same.val();
                    }
                    disc_amt[key] = $('#indi_disc_amt_' + key).val();
                });
                $.ajax({
                    url: '<?php echo base_url(); ?>' + "trainee/update_userdiscount",
                    type: "post",
                    dataType: "json",
                    async: false,
                    data: {
                        disc_perc:disc_perc,
                        disc_amt:disc_amt,
                        userid:$('#userid').val()
                    },
                    success: function(data) {
                       if(data == 0){
                            disp_err('#popup_error', '[Something went wrong. Please try again.]');
                            $retval = false;  
                        }
                    }
                });
            }
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
            $('#trainee_edit').submit(function() {
                check = 1;
                 return validate(true);
            });
            $('#trainee_edit select,#trainee_form input').change(function() {
                if (check == 1) {
                    return validate(false);
                }
            });
            $('#assign_company').trigger("change");
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
        $(".search").change(function() {
            var search = $(this).val();
            if (search == 'tax_radio') {
                $("#tax_code").removeAttr('disabled');                
                $('#search_by_name_autocomplete').attr('disabled', 'disabled');
                $('#search_by_name_autocomplete').val('');
                $('#search_by_name_autocomplete').removeClass('error');
                $('#search_by_name_autocomplete_err').removeClass('error').text('');
            }
            else {
                $("#tax_code").attr('disabled','disabled');
                $('#tax_code').val('');
                $("#search_by_name_autocomplete").removeAttr('disabled');
                $('#tax_code').removeClass('error');
                $('#search_by_taxcode_autocomplete_err').removeClass('error').text('');
            }
        });
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
            return retVal;
        }
        function showother_reason(e) {
            $('#other_reason').css('visibility', 'visible');
        }
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
        var trainee_details;
        var primary_taxcode = '<?php echo $trainee[userdetails]['tax_code']; ?>';
        var trainee_auto_form_flag = true;
        /*  added by shubhranshu for client requirement on 21/03/2019 */
        function check_nric_restriction(){
            var $nric = $("#NRIC_ID").val(); 
            //alert($nric);
            $.ajax({
                    url: "check_nric_restriction",
                    type: "post",
                    dataType: "json",
                    async:false,
                    data: {tax_code: $nric,operation:'EDIT_TRAINEE'},
                    success: function(res) {
                        if (res == 1) {
                            ///added by shubhranshu
                            if($privilage == '0'){
                                if($role_id == 'ADMN'){
                                    if(res > 0){
                                        $('#ex22').modal();
                                    }
                                }
                            }else if($privilage == '1'){
                                if(res > 0){
                                        $('#ex22').modal();
                                    }
                            } ///added by shubhranshu
                        } 
                    }
                });
        }
        /*--------------------------------------------------------------*/
        
        function isunique_taxcode(e, id) {
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
            } else if(primary_taxcode != e) {
                var $nric = $.trim($('#NRIC').val());
                //added by shubhranshu for restriction check
               check_nric_restriction();  ///added by shubhranshu
                
                                                        
                var $country_of_residence = $.trim($('#country_of_residence').val());
                
                var user_id = $('#userid').val();
                $.ajax({
                    url: "check_taxcode",
                    type: "post",
                    dataType: "json",
                    data: {tax_code: e, user_id: user_id, country_of_residence: $country_of_residence, nric: $nric},
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
                        } else {
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
            } else {
                $("#" + id + "_err").text("").removeClass('error');
                $("#" + id).removeClass('error');
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
                    var edu_tbl_cnt = $('#edu_tbl tr').size();
                    var other_tbl_cnt =  $('#other_tbl tr').size();
                    var work_tbl_cnt = $('#work_tbl tr').size();
                    remove_table_tr(edu_tbl_cnt,"edu_row_");
                    remove_table_tr(other_tbl_cnt,"other_row_");
                    remove_table_tr(work_tbl_cnt,"work_row_");
                    populate_addmore('edu',trainee_details.edudetails);
                    populate_addmore('other',trainee_details.otherdetails);
                    populate_addmore('work',trainee_details.workdetails);
                }
                trainee_auto_form_flag = false;
           });
           $('.trainee_deatils_no').click(function(){                 
               $('#trainee_edit_search').submit();
           });
        });
        function remove_table_tr(tr_cnt, tr_id){            
            if(tr_cnt >1) {                
                var $j=1;                
                for($j=1;$j <= tr_cnt;$j++) {
                    $('#'+tr_id + $j).remove();
                }
            }             
        }
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
                    url: "check_email_id",
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
        function reset_states() {
            var curr_country = $("#current_pers_country").val();
            var curr_state = $("#current_pers_states").val();
            $.post(baseurl + "internal_user/get_states_json", {country_param: curr_country}, function(data) {
                var json_data = $.parseJSON(data);
                var $pers_states_select = $("#pers_states");
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
    <script type="text/javascript">
        function validate_search() {
            var $retVal = true;
            if ($("#name_radio").is(":checked") ) {         
                var search_val = $.trim($("#search_by_name_autocomplete").val());
                var $error_id = '#search_by_name_autocomplete_err';
                var $input_id = '#search_by_name_autocomplete';
                if (search_val == "") {
                    $($error_id).text("[required]").addClass('error');
                    $($input_id).addClass('error');
                    return false;
                } else if (search_val.indexOf('(') === -1) {                
                    $($error_id).text("[Select trainee from autofill-help]").addClass('error');
                    $($input_id).addClass('error');
                    return false;
                }
                
                $('#tax_code').removeClass('error');
                $('#search_by_taxcode_autocomplete_err').removeClass('error').text('');
                ///added by shubhranshu to prevent multiclick
                var self = $('#trainee_edit_search'),
                button = self.find('input[type="submit"],button');
                button.attr('disabled','disabled').html('Please Wait..');
            }
            if ($("#tax_radio").is(":checked") ) { 
                var search_val1 = $.trim($("#tax_code").val());
                var $error_id = '#search_by_taxcode_autocomplete_err';
                var $input_id = '#tax_code';   
                if (search_val1 == "") {
                    $($error_id).text("[required]").addClass('error');
                    $($input_id).addClass('error');
                    return false;
                } else if (search_val1.indexOf('(') === -1) {                
                    $($error_id).text("[Select trainee from autofill-help]").addClass('error');
                    $($input_id).addClass('error');
                    return false;
                }
                $('#tax_code').removeClass('error');
                $('#search_by_taxcode_autocomplete_err').removeClass('error').text('');
                ///added by shubhranshu to prevent multiclick
                var self = $('#trainee_edit_search'),
                button = self.find('input[type="submit"],button');
                button.attr('disabled','disabled').html('Please Wait..');
            }                
            return $retVal;
        }
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


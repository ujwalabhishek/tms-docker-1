
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
?>
<style>
    .edu_level{
        width:210px;
    }
</style>
<div class="col-md-2 col_2_style2">
    <ul class="ad">
        <li><a href="#" class="thumbnail"><img src="<?php echo base_url();?>/assets/images/ad1.jpg" style="display: block;" data-src="holder.js/100%x180" alt="100%x180"></a></li>
        <li><a href="#" class="thumbnail"><img src="<?php echo base_url();?>/assets/images/ad2.jpg" style="display: block;" data-src="holder.js/100%x180" alt="100%x180"></a></li>
    </ul>
</div>
<div class="col-md-10" style='font-size: 13px;'>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/trainee.png"/>Confirm Trainee Details:</h2>
    <div class="table-responsive">
        <?php echo validation_errors('<div class="error1">', '</div>'); ?> 
        <?php
        $form_attributes = array('name' => 'trainee_edit_search', 'id' => 'trainee_edit_search', "onsubmit" => "return(validate_search());");
        echo form_open("course_public/confirm_trainee_detail", $form_attributes);
        ?>
            <?php 
               $atr = 'id="trainee_form2" name="trainee_form2" style="font-size:16px;"';
              // echo form_open_multipart("course/enrol_once", $atr);
               echo form_open_multipart("course_public/confirm_trainee_detail", $atr);
              //  echo form_open_multipart("user/add_trainee1", $atr);
                $user_id;
               echo form_hidden('r_user_id', $user_id);
               if(!empty($user_id)){
                    echo form_hidden('loggedin', 1);
               }

               echo form_hidden('country_of_residence', 'SGP');
               echo form_hidden('course_id', $course_id);
               echo form_hidden('class_id', $class_id);
               echo form_hidden('registration', '1');

       ?>  
   <?php
   if($course_id!='' && $class_id!=''){

   ?>
   <div style="color:black;font-weight: bold; padding: 14px;text-align:center;width:100%" class="reg_tbl_div">                                
       <table class="table table-striped" style="">
                    <tbody>
                       <tr>
                           <td  class="td_heading">Class Name: 
                               <label class="label_font"><?php echo $class_details->class_name; ?></label>&nbsp;&nbsp;&nbsp;
                                  <a href="#ex12" rel="modal:open" class="small_text1"> <label class="label_font" style="cursor: pointer;">View Details</label>
                               </a>
                           </td>
                           <td class="td_heading">Unit Fees: <label class="label_font">&nbsp;&nbsp;$&nbsp;<?php echo number_format($class_details->class_fees, 2, '.', ''); ?>

                               </label></td>
                           <td class="td_heading">Discount@ : <label class="label_font">&nbsp;&nbsp;$
                               <?php echo number_format($class_details->class_discount, 2, '.', ''); ?>%</td>
                       </tr>

                       <tr style="display:none;">

                           <td class="td_heading" ><?php echo $gst_label; ?>:<label class="">$ <?php echo number_format($totalgst, 2, '.', ''); ?></label> </td>
                           <td colspan="2" class="td_heading">Net Fee: <label class=""><?php echo '$ ' . number_format($net_due, 2, '.', ''); ?></label></td>

                       </tr>  
                       <tr>

                        <td class="td_heading" ><?php echo $gst_label; ?>: <label class="label_font">$ <?php echo number_format($totalgst, 2, '.', ''); ?></label></td>
                        <td colspan="2" class="td_heading">Net Fee: <label class="label_font"><?php echo '$ ' . number_format($net_due, 2, '.', ''); ?></label></td>

                       </tr>  

                   </tbody>
       </table>
   </div>

   <div class="modalnew modal13" id="ex12" style="display:none;height:280px !important; min-height: 280px !important;">
         <h2 class="panel_heading_style" style="margin-bottom: -3px !important;">Class Details </h2>
                   <div class="class_desc_course">
                       <div class="table-responsive">
                           <table class="table table-striped">     
                               <tr>
                                   <td width="40%"><span class="crse_des">Course Name :</span></td>
                                   <td><?php echo   $course_details->crse_name;?></td>
                               </tr>
                               <tr>
                                   <td><span class="crse_des">Class Name :</span></td>
                                   <td><?php echo $class_details->class_name; ?></td>
                               </tr>
                               <tr>
                                   <td><span class="crse_des">Class Start Date and Time :</span></td>
                                   <td><?php echo date('d/m/Y h:i A', strtotime($class_details->class_start_datetime)); ?></td>
                               </tr>
                               <tr>
                                   <td><span class="crse_des">Class End Date and Time :</span></td>
                                   <td><?php echo  date('d/m/Y h:i A', strtotime($class_details->class_end_datetime)); ?></td>
                               </tr>
                               <tr>
                                   <td><span class="crse_des">Classroom Location :</span></td>
                                   <td><?php echo $classloc; ?></td>
                               </tr>


                           </table>
                       </div>                                
                   </div>
                   <div class="popup_cancel11">
                       <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Close</button></a>
                   </div>
               </div>  

   <?php } ?>
        
        <?php echo form_close(); ?>  
    </div>
    <br>
    <?php
    if ($trainee):
        
       
        $atr = array(
            'name' => 'trainee_edit',
            'id' => 'trainee_edit',
            'method'  => 'post',
            );
        echo form_open_multipart("course_public/confirm_trainee_detail", $atr);
        ?>
        <input type="hidden" name="edit_user_id" id="edit_user_id" value="<?php echo $this->input->post('user_id_popup'); ?>" />
        
        <input type="hidden" name="taxcode" value="<?php echo $this->input->post('taxcode'); ?>" />
        <input type="hidden" name="trainee_name" value="<?php echo $this->input->post('trainee_name'); ?>" />
        <input type="hidden" name="search_radio" value="<?php echo $this->input->post('search_radio'); ?>" />
         <input type="hidden" name="course_id" value="<?php echo $this->input->post('course_id'); ?>" />
          <input type="hidden" name="class_id" value="<?php echo $this->input->post('class_id'); ?>" />
        <input type="hidden" name="NRIC_ID_MATCH" value="<?php echo $this->input->post('taxcode_nric'); ?>" />
    
       
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/other_details.png"/> Access Detail</h2>
        <div id ='trainee_validation_div'>
            <div class="bs-example">
                <div class="table-responsive" style='overflow-x: initial;'>
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
                                        $attr = 'id="NRIC" disabled="true" style="background: #ebebe4;"';
                                        echo form_dropdown('NRIC', $nris_options, $trainee[userdetails]['tax_code_type'], $attr);
                                        ?>
                                        <input type='hidden' name='NRIC' value="<?php echo $trainee[userdetails]['tax_code_type'];?>">
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

                                        $attr = 'id="NRIC_OTHER" disabled="true" style="background: #ebebe4;"';
                                        echo form_dropdown('NRIC_OTHER', $nric_other_options, $trainee[userdetails]['other_identi_type'], $attr);
                                        ?>
                                        <input type='hidden' name='NRIC_OTHER' value="<?php echo $trainee[userdetails]['other_identi_type'];?>">
                                        <span id="NRIC_OTHER_err"></span>
                                    </SPAN>
                                    <SPAN id="SGP_ID" style="display:none;">
                                        <br /><br />
                                        <label id="SGP_ID_label"> NRIC Code : </label><span class="required">* </span>
                                        
                                        <?php
                                        
                                        $attr = array('name' => 'NRIC_ID', 'class' => 'upper_case alphanumeric', 'id' => 'NRIC_ID', 'onblur' => 'javascript:isunique_taxcode(this.value,this.id);', 'maxlength' => '50','disabled'=>'disabled');
                                        echo form_input($attr, $trainee[userdetails]['tax_code']);
                                        ?>
                                         <input type='hidden' name='NRIC_ID' value="<?php echo $trainee[userdetails]['tax_code'];?>">
                                        <span id="NRIC_ID_err"></span>
                                    </SPAN>

                                        <?php
                                        $attr = array('name' => 'SSN', 'class' => 'upper_case alphanumeric', 'id' => 'SSN', 'onblur' => 'javascript:isunique_taxcode(this.value,this.id);', 'maxlength' => '50');
                                        ?>    
                                        <span id="SSN_err"></span>
                                </td>
                            </tr>
                            <tr>
                                <td width="14%" class="td_heading">Username: <span class="required">*</span></td>
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
             
            </h2>
            <div class="table-responsive" style='overflow-x: initial;'>
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
                            <td class="td_heading">Date of Birth:</td>
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
                            <td class="td_heading">Email Id:
                                <span id="span_email_id" class="required" style="display:<?php echo $display_email; ?>">*</span>
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
                            <td class="td_heading">Highest Education Level:<span class="required">*</span></td>
                                    <?php
                                    $highest_educ_level = fetch_metavalues_by_category_id(Meta_Values::HIGHEST_EDUC_LEVEL);
                                    $highest_educ_level_options[''] = 'Select';
                                    foreach ($highest_educ_level as $item):
                                        $highest_educ_level_options[$item['parameter_id']] = $item['category_name'];
                                    endforeach;
                                    ?>
                            <td colspan="5">
                            <?php echo form_dropdown('highest_educ_level', $highest_educ_level_options, $trainee[userdetails]['highest_educ_level'], 'id="highest_educ_level" style="width:500px;"'); ?>
                                <span id="highest_educ_level_err"></span>
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
            <br>

        </div>
        <br>
        </br>

        <div class="throw_right">
            <button id="update_btn" class="btn btn-primary" type="submit">
                <span class="glyphicon glyphicon-retweet"></span>
                &nbsp;Update & Continue
            </button> &nbsp; &nbsp;  
            
            
          
            
        </div>
    
        <?php
        echo form_hidden('userid', $trainee[userdetails]['user_id']);
        echo form_hidden('task', 'update');
        echo form_hidden('update_edu', implode(',', $update_edu));
        echo form_hidden('update_other', implode(',', $update_other));
        echo form_hidden('update_work', implode(',', $update_work));
        echo form_close();
        ?>                       


    
   
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

     

<!--//////////////////////////////////////////////////////-->
    <script type="text/javascript">
        $baseurl = '<?php echo base_url(); ?>';
        $role_id = "<?php echo $this->session->userdata('userDetails')->role_id;?>";//added by shubhranshu
        $privilage = "<?php echo $privilage;?>"; //added by shubhranshu
        edituser_country_of_residence = '<?php echo $trainee[userdetails]['country_of_residence']; ?>';
        tax_code_type = '<?php echo $trainee[userdetails]['tax_code_type']; ?>';
        $("#" + edituser_country_of_residence).show();
        $("#" + edituser_country_of_residence + "_ID").show();
        $("#SGP_" + tax_code_type).show();
        other_identi_type = '<?php echo $trainee[userdetails]['other_identi_type']; ?>';         
        

        

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
           
            
           
           
           
        });

        
        

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

            pers_dob = $.trim($("#pers_dob").val());            
            if (valid_date_field(pers_dob) == false && pers_dob.trim().length > 0) {
                $("#pers_dob_err").text("[dd-mm-yy format]").addClass('error');
                $("#pers_dob").removeClass('error');
                retVal = false;
            } else {
                $("#pers_dob_err").text("").removeClass('error');
                $("#pers_dob").removeClass('error');
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
                    url: $baseurl+"user/check_email",
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


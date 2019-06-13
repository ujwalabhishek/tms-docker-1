<?php
$this->load->helper('common_helper');
$reacti_btn = '';
$reacti_popup = '';
if ($user_list_values->account_status == 'INACTIV' && $this->data['user']->role_id == 'ADMN'):
    //button
    $reacti_btn = '<a class="small_text" href="#ex897" rel="modal:open">
                        <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-saved"></span>&nbsp;
                            Reactivate
                        </button>
                    </a> &nbsp; &nbsp;';

    $form_attributes = array('name' => 'user_reactivate_form',
        'id' => 'user_reactivate_form',
        "onsubmit" => "return(validate_reactivate_user(true));",
        "onchange" => "if(validation == 1){ return(validate_reactivate_user(false))};");
    $form_tag = form_open("internal_user/reactivate_user", $form_attributes);
    $reactivate_reasons = fetch_metavalues_by_category_id(Meta_Values::USER_REACTIVATE_REASONS);
    $reasons_options = array('' => 'Select');
    foreach ($reactivate_reasons as $item):
        $reasons_options[$item['parameter_id']] = $item['category_name'];
    endforeach;
    $reasons_options['OTHERS'] = 'Others';
    $reasons_attr = 'id = "reason_for_reactivation" onchange=" return display_other_field(this.value);"';
    $reason_tag = form_dropdown('reason_for_reactivation', $reasons_options, $this->input->post('reason_for_reactivation'), $reasons_attr);
    $other_reason = array(
        'name' => 'other_reason_for_reactivation',
        'id' => 'other_reason_for_reactivation',
        'size' => 35,
        'style' => 'margin:5px 0 0 0; width:277px',
        'class' => 'upper_case',
    );
    $reacti_popup = $form_tag . '<div class="modal1_051" id="ex897" style="display:none;">
                        <p>
                        <h2 class="panel_heading_style">Reactivate Internal Staff</h2>  
                        <div class="table-responsive">
                            <table class="table table-striped">      
                                <tbody>
                                    <tr>
                                        <td class="td_heading"> Re-Activation Date:<span class="red">*</span> </td>
                                        <td> 
                                            <label id="reactivation_date" class="error">' . date('d/m/Y') . '</label>
                                        </td>
                                    </tr>            
                                    <tr>
                                        <td class="td_heading" width="30%">Reason for Re-Activation:<span class="red">*</span></td>
                                        <td>
                                            ' . $reason_tag . ' &nbsp; 
                                            <span id="reason_for_reactivation_err"></span>                
                                            <div id="other_reason" style="display:none;">
                                                ' . form_input($other_reason) . '
                                                <span id="other_reason_for_reactivation_err"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>       
                        <br>
                        Are you sure you want to reactivate this Staff?
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
                    </div>' . form_hidden('user_id', $user_list_values->user_id) . form_close();
    ?>
    <script type="text/javascript">
        var validation = 0;
        function valid_reactivate_reason(otherReason) {
            var pattern = new RegExp(/^[\sa-zA-Z0-9_,.-]+$/);
            return pattern.test(otherReason);
        }
        function display_other_field(value) {
            if (value == 'OTHERS') {
                $("#other_reason").show();
            } else {
                $("#other_reason").hide();
                $("#other_reason_for_reactivation").val('');
            }
        }
        function validate_reactivate_user(retVal) {
            validation = 1;
            reason_for_reactivation = $("#reason_for_reactivation").val();
            if (reason_for_reactivation == "") {
                $("#reason_for_reactivation_err").text("[required]").addClass('error');
                $("#reason_for_reactivation").addClass('error');
                retVal = false;
            } else {
                $("#reason_for_reactivation_err").text("").removeClass('error');
                $("#reason_for_reactivation").removeClass('error');
            }
            other_reason_for_reactivation = $("#other_reason_for_reactivation").val();
            if (reason_for_reactivation == "OTHERS") {
                if (other_reason_for_reactivation == "") {
                    $("#other_reason_for_reactivation_err").text("[required]").addClass('error');
                    $("#other_reason_for_reactivation").addClass('error');
                    retVal = false;
                } else if (valid_reactivate_reason(other_reason_for_reactivation) == false) {
                    $("#other_reason_for_reactivation_err").text("[invalid]").addClass('error');
                    $("#other_reason_for_reactivation").addClass('error');
                    retVal = false;
                } else {
                    $("#other_reason_for_reactivation_err").text("").removeClass('error');
                    $("#other_reason_for_reactivation").removeClass('error');
                }
            } else {
                $("#other_reason_for_reactivation_err").text("").removeClass('error');
            }
            if(retVal == true){
                $('.popup_cancel9 button[type=submit]').css('display','none');
            }
            return retVal;
        }
        
    </script>
    <?php
endif;
?>
<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<div class="col-md-10">
    <?php
    if ($this->session->flashdata('success')) {
        echo '<div class="success">' . $this->session->flashdata('success') . '</div>';
    }
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/internal_user.png"/>  Internal Staff - View Details</h2>
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/personal_details.png"/> Personal Details</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading" width="20%">Staff Name:</td>
                    <td colspan="3" width="80%"><label class="label_font"><?php echo $user_list_values->first_name; ?></label></td>
                    <td rowspan="5" align="center">
                        <div class="userphoto">
                            <?php if ($user_list_values->photo_upload_path): ?> 
                                <img src="<?php echo base_url() . $user_list_values->photo_upload_path; ?>"/> 
                            <?php else: ?>
                                <img src="<?php echo base_url(); ?>assets/images/photo.jpg"/> 
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Gender:</td>
                    <td><label class="label_font"><?php echo $user_list_values->gender; ?></label></td>
                    <td class="td_heading">Date of Birth:</td>
                    <td><label class="label_font"><?php echo ($user_list_values->dob == '0000-00-00')?'':formated_date($user_list_values->dob, '-'); ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Country of Residence:</td>
                    <td><label class="label_font"><?php echo $country_of_residence->category_name; ?></label></td>
                    <td>
                        <strong>NRIC/FIN No. Type:</strong> 
                    </td>
                    <td>
                        <?php echo get_catname_by_parm($user_list_values->tax_code_type) . $other_identi_type; ?>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">NRIC/FIN No.:</td>
                    <td><label class="label_font"><?php echo $user_list_values->tax_code; ?></label></td>
                    <td class="td_heading">Contact Number(P):</td>
                    <td><label class="label_font"><?php echo $user_list_values->contact_number; ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Contact Number(M):</td>
                    <td><label class="label_font"><?php echo $user_list_values->alternate_contact_number; ?></label></td>
                    <td class="td_heading">Email Id(P):</td>
                    <td><label class="label_font"><?php echo $user_list_values->registered_email_id; ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Alternate Email Id(P):</td>
                    <td colspan='4'><label class="label_font"><?php echo $user_list_values->alternate_email_id; ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Highest Education:</td>
                    <td colspan="2"><label class="label_font"><?php echo get_catname_by_parm($user_list_values->highest_educ_level); ?></label></td>
                    <td class="td_heading">Salary Range:</td>
                    <td><label class="label_font"><?php echo get_catname_by_parm($user_list_values->salary_range); ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Personal Address:</td>
                    <td colspan="4">
                        <label class="label_font" style="word-wrap: break-word; width:600px;"><?php echo $user_list_values->personal_address_bldg; ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">City:</td>
                    <td><label class="label_font"><?php echo $user_list_values->personal_address_city; ?></label></td>
                    <td class="td_heading">Country:</td>
                    <td><label class="label_font"><?php echo $user_personal_country->category_name; ?></label></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td class="td_heading">State:</td>
                    <td><label class="label_font"><?php echo $user_personal_state->category_name; ?></label></td>
                    <td class="td_heading">Postal Code:</td>
                    <td><label class="label_font"><?php echo $user_list_values->personal_address_zip; ?></label></td>
                    <td>&nbsp;</td>
                </tr>        
            </tbody>
        </table>
    </div>      
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/officail_details.png"/> Official Details</h2>          
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading" width="20%">Company Name:</td>
                    <td width="30%"><label class="label_font"><?php echo $user_list_values->company_name; ?></label></td>
                    <td class="td_heading" width="20%">Date of Joining:</td>
                    <td width="30%"><label class="label_font"><?php echo ($user_list_values->doj!='0000-00-00')?formated_date($user_list_values->doj, '-'):''; ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Designation:</td>
                    <td><label class="label_font"><?php echo ($user_list_values->designation=='OTHERS')?$user_list_values->designation_others:get_catname_by_parm($user_list_values->designation); ?></label></td>                    
                    <td class="td_heading">Email Id(O):</td>
                    <td><label class="label_font"><input type="text" id="view_edit_email" value="<?php echo $user_list_values->off_email_id; ?>" disabled="true"></label><span id="email_edit_btn" style="color:#428bca;cursor:pointer;" onclick="checkEdit()">Edit</span><div id="view_edit_email_err"></div></td>  <!--added by shubhranshu to check and update email on 4/12/2018--->
                </tr>
                <tr>
                    <td class="td_heading">Contact Number(O):</td>
                    <td colspan='3'><label class="label_font"><?php echo $user_list_values->off_contact_number; ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Office Address:</td>
                    <td colspan="3"><label class="label_font" style="word-wrap: break-word; width:600px;"><?php echo $user_list_values->off_address_bldg; ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">City:</td>
                    <td><label class="label_font"><?php echo $user_list_values->off_address_city; ?></label></td>
                    <td class="td_heading">Country:</td>
                    <td><label class="label_font"><?php echo $user_office_country->category_name; ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">State:</td>
                    <td><label class="label_font"><?php echo $user_office_state->category_name; ?></label></td>
                    <td class="td_heading">Postal Code:</td>
                    <td><label class="label_font"><?php echo $user_list_values->off_address_zip; ?></label></td>
                </tr>
            </tbody>
        </table>
    </div>
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/other_details.png"/>  Staff Other Details</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading" width="20%">Role:</td>
                    <td colspan="3"><label class="label_font"><?php echo $user_list_values->role_name; ?></label>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Username/ Login Name:</td>
                    <td colspan="3"><label class="label_font"><?php echo $user_list_values->user_name; ?></label>
                    <?php if ($this->data['user']->role_id == 'ADMN') { ?>
                        <!--&nbsp; &nbsp; &nbsp;<a title="Reset Password" href="<?php echo base_url() ?>internal_user/reset_password/<?php echo $user_list_values->user_id ?>">Reset Password</a>-->
                    &nbsp; &nbsp; &nbsp;<a title="Reset Password" href="<?php echo base_url() ?>internal_user/internal_user_reset_password/<?php echo $user_list_values->user_id ?>">Reset Password</a>
                            <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Account Status : </td>
                    <td colspan="3" class="td_heading">
                        <?php
                        $user_current_status = get_param_value($user_list_values->account_status);
                        if ($user_list_values->account_status == "PENDACT") {
                            echo '<div style="color:blue">' . $user_current_status->category_name . '</div>';
                        }
                        if ($user_list_values->account_status == "ACTIVE") {
                            echo "<font color='green'>" . $user_current_status->category_name . "</font>";
                        }
                        if ($user_list_values->account_status == "INACTIV") {
                            echo "<font color='red'>" . $user_current_status->category_name . "</font>";
                            $deactivation_details = '';
                            $deactivated_by = $this->internaluser->get_user_details($this->session->userdata('userDetails')->tenant_id, $user_list_values->deacti_by);
                            $deactivation_details = ' - Deactivated by ' . $deactivated_by->first_name . ' ' . $deactivated_by->last_name . '(' . $deactivated_by->user_name . ') on ' . date("d/m/Y", strtotime($user_list_values->acct_deacti_date_time)) . '.';
                            if ($user_list_values->deacti_reason == 'OTHERS') {
                                $deactivation_details .= ' Reason: ' . $user_list_values->deacti_reason_oth.'.';
                            } else {
                                $user_deactivation_reason = get_param_value($user_list_values->deacti_reason);
                                $deactivation_details .= ' Reason: ' . $user_deactivation_reason->category_name.'.';
                            }
                            echo "<font color='red'>" . $deactivation_details . "</font>";
                        }
                        ?>
                    </td>
                </tr>         
                <tr>
                    <td colspan="4">
                        <?php if ($user_list_values->account_status != "ACTIVE" && $user_list_values->account_status != "INACTIV") { ?>
                            <i>(If marked Yes, an activation mail will be sent to the users official email Id.)</i>
                        <?php } ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <div class="small_heading">        
        Training Attended Details as on <?php echo date('M j Y l'); ?>       
    </div>		  
    <div class="table-responsive">        
        <table class="table table-striped">
            <thead>
                <?php
                $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                $pageurl = $controllerurl;
                if (count($training_details) > 0) {
                    ?>
                    <tr>
                        <th width="17%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=crse.crse_name&o=" . $ancher; ?>">Training Name</a></th>
                        <th width="13%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=enrol.enrolled_on&o=" . $ancher; ?>">Enrollment Date</a></th>
                        <th width="13%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=enrol.enrolment_mode&o=" . $ancher; ?>">Enrollment Type</a></th>
                        <th width="14%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=cls.class_end_datetime&o=" . $ancher; ?>">Training End Date</a></th>
                        <th width="6%" class="th_header">Validity</th>
                        <th width="7%" class="th_header">Cert. Status</th>
                        <th width="7%" class="th_header">Att.<br/> Status</th>
                        <th width="30%" class="th_header">Action</th>
                    </tr>
                <?php } ?>
            </thead>
            <tbody>
                <?php
                $tenant_details = fetch_tenant_details($this->session->userdata('userDetails')->tenant_id);                
                if (count($training_details) > 0) {
                    foreach ($training_details as $item) :
                        $enrol_mode = ($item->enrolment_mode != '') ? get_catname_by_parm($item->enrolment_mode) : '';
                        if($enrol_mode == 'Company'){
                            $enrol_mode = '';
                        }
                        $company = '';
                        if($item->enrolment_mode == 'COMPSPON') {
                            if($item->company_id[0] == 'T' ){
                                $company =  $tenant_details->tenant_name;
                            } else {
                                $company =  $item->company_name;
                            }
                        }else if ($item->enrolment_mode == 'SELF'){
                            $company = '';
                        }
                        $class_end_datetime = date("Y/m/d", strtotime($item->class_end_datetime));
                        $class_end_datetime_str = strtotime($class_end_datetime);
                        $class_start_datetime = date("Y/m/d", strtotime($item->class_start_datetime));
                        $class_start_datetime_str = strtotime($class_start_datetime);
                        $cur_date = strtotime(date('Y/m/d'));
                        if ($item->crse_cert_validity != 0) {
                            $date = strtotime("+" . $item->crse_cert_validity . " days", strtotime($class_end_datetime));
                            $validity_date = strtotime(date('Y/m/d', $date));
                            $validity = date('d/m/Y', $date);
                        } else {
                            $validity = "Life Long";
                        }
                        if ($validity == "Life Long") {
                            $Status = 'ACTIVE';
                        } elseif ($cur_date <= $validity_date) {
                            $Status = 'ACTIVE';
                        } elseif ($cur_date > $validity_date) {
                            $Status = '<span class="blink">Renewal Due</span>';
                        }
                        $linkStr = '';
                        if ($user_list_values->account_status == 'PENDACT') {
                            $linkStr = '<span style="color:red;">Account not yet activated.</span>';
                        } else if ($user_list_values->account_status == 'INACTIV') {
                            $linkStr = get_links($item->enrolment_mode, $item->payment_status, $item->invoice_id, $item->user_id, $item->pymnt_due_id, $item->class_id, $this, $user_list_values->account_status);
                        } else {
                            $linkStr = '';
                            $classStatus = '';
                            if ($cur_date >= $class_end_datetime_str) {
                                if ($cur_date == $class_end_datetime_str) {
                                    $classStatus = 'IN_PROG';
                                } else if ($class_end_datetime_str < $cur_date ) {
                                    $classStatus = 'COMPLTD';
                                }
                                $linkStr = '';
                                if ($item->payment_status != 'PYNOTREQD' &&
                                        ($this->data['user']->role_id == 'ADMN' && $user_list_values->account_status == 'ACTIVE')
                                ) {
                                    $linkStr = '<a href="javascript:;" class="get_update" data-class="' . $item->class_id . '" data-user="' . $item->user_id . '">Update TG# </a>&nbsp;&nbsp;&nbsp;';
                                }
                                if ($this->data['user']->role_id == 'ADMN' ||
                                        $this->data['user']->role_id == 'CRSEMGR' ||
                                        $this->data['user']->role_id == 'TRAINER'
                                ) {
                                    $linkStr .= '<a id="training_update" href="#ex7" rel="modal:open" data-course="' . $item->course_id . '" data-class="' . $item->class_id . '" data-user="' . $item->user_id . '" class="training_update small_text1">Feedback</a>&nbsp;&nbsp;&nbsp;';
                                }
                                $linkStr .= '<a href="' . base_url() . 'trainee/print_loc/' . $item->class_id . '/' . $user_id . '">LOC</a>&nbsp;&nbsp;&nbsp;';
                            } else {
                                if ($item->payment_status != 'PYNOTREQD' &&
                                        $this->data['user']->role_id == 'ADMN') {
                                    $linkStr = '<a href="javascript:;" class="get_update" data-class="' . $item->class_id . '" data-user="' . $item->user_id . '">Update TG# </a>&nbsp;&nbsp;&nbsp;';
                                }
                                if ($class_start_datetime_str <= $cur_date && $class_end_datetime >= $cur_date) {
                                    $classStatus = 'IN_PROG';
                                }elseif ($class_start_datetime_str > $cur_date) {
                                    $classStatus = 'YET_TO_START';
                                }
                            }
                            /*   added $item->attn_stats by shubhranshu due to receipt issue for absent trainee      */
                            //    $linkStr .= get_links($item->enrolment_mode, $item->payment_status, $item->invoice_id, $item->user_id, $item->pymnt_due_id, $item->class_id, $this, $trainee['userdetails']['account_status'], $classStatus,$item->company_id,$assmnt_links);
						//$linkStr .= get_links($item->attn_stats,$item->enrolment_mode, $item->payment_status, $item->invoice_id, $item->user_id, $item->pymnt_due_id, $item->class_id, $this, $trainee['userdetails']['account_status'], $classStatus,$item->company_id,$assmnt_links);
                            /*--------------------------------------------------------------------------*/
                            $linkStr .= get_links($item->attn_stats,$item->enrolment_mode, $item->payment_status, $item->invoice_id, $item->user_id, $item->pymnt_due_id, $item->class_id, $this, $user_list_values->account_status, $classStatus);
                        }
                        ?>
                        <tr>                
                            <td><?php echo $item->crse_name . "-" . $item->class_name; ?></td>
                            <td><?php echo ($item->enrolled_on) ? date('d/m/Y', strtotime($item->enrolled_on)) : ''; ?></td>
                            <td><?php echo $enrol_mode . $company; ?></td>
                            <td><?php echo ($item->class_end_datetime) ? date('d/m/Y', strtotime($item->class_end_datetime)) : ''; ?></td>
                            <td><?php echo $validity; ?></td>
                            <td><?php echo $Status; ?></td>
                            <td><?php echo $item->att_status; ?></td>
                            <td><?php echo $linkStr; ?></td>                 
                        </tr>
        <?php
    endforeach;
} else {
    echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>No training attended details available.</label></td></tr>";
}
?>
            </tbody>      
        </table>    
    </div> 
    <div class="error">** <b>AB:</b> Absent</div>
    <div class="error">** <b>P:</b> Present</div>
    <div class="error">** <b>DNP:</b> Data Not Present</div>
    <div class="button_class">
        <?php echo $reacti_btn; ?>
        <a href="<?php echo site_url(); ?>internal_user">
            <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-step-backward"></span>&nbsp;Back</button>
        </a>
    </div>
</div>
<div style="clear:both;"></div><br>
<ul class="pagination pagination_style">
<?php echo $pagination; ?>
</ul>
<div class="modal1_0001" id="ex9" style="display:none;height:200px;min-height: 200px;">
    <h2 class="panel_heading_style">Update TG#</h2>
    <table class="table table-striped">
        <tbody>
<?php
$data = array(
    'id' => 'h_class',
    'type' => 'hidden',
    'name' => 'h_class',
);
echo form_input($data);
$data = array(
    'id' => 'h_user',
    'type' => 'hidden',
    'name' => 'h_user',
);
echo form_input($data);
?>
            <tr>
                <td class="td_heading">TG#:</td>
                <td><?php
            echo form_input('tg_number', $this->input->post('tg_number'), ' id="tg_number"');
?>
                    <span id="tg_number_err"></span></td>
            </tr>
        </tbody>
    </table>
    <span class="required required_i">* Required Fields</span>
    <div class="popup_cance89">
        <span href="#ex9" rel="modal:close"><button class="btn btn-primary subsidy_save" type="button">Save</button></span>
    </div>

</div>
<div style='clear: both'></div>
<div class="modal_3" id="ex8" style="display:none;">
    <p>  
    <h2 class="panel_heading_style">Payment Received Details</h2>  
    <table class="no_border_table">
        <tbody>  
            <tr>
                <td class="td_heading">Payment Made On:</td>
                <td><span class="r_recd_on"></span></td>
            </tr>
            <tr>
                <td class="td_heading">Mode of Payment:</td>
                <td><span class="r_mode"></span></td>
            </tr>
            <tr>
                <td class="td_heading">Class Fees:</td>
                <td><span class="r_class_fees"></span> SGD</td>
            </tr>
            <tr>
                <td class="td_heading"><span class="r_dis_label"></span> Discount @ <span class="r_dis_rate"></span>%:</td>
                <td><span class="r_dis_amount"></span> SGD</td>
            </tr>
            <tr>
                <td class="td_heading">Subsidy:</td>
                <td><span class="r_subsidy_amount"></span> SGD</td>
            </tr>
            <tr>
                <td class="td_heading">Amount Due:</td>
                <td><span class="r_after_gst"></span> SGD</td>
            </tr>
            <tr>
                <td class="td_heading">GST @ <span class="r_gst_rate"></span>% (<span class="r_gst_label"></span>):</td>
                <td><span class="r_total_gst"></span> SGD</td>
            </tr>
            <tr>
                <td class="td_heading">Net Due:</td>
                <td><span class="r_net_due"></span> SGD</td>
            </tr>
        </tbody>
    </table><br />
    <div class="popup_cance89">
        <a class="payment_recd_href" href="#"><button type="button" class="btn btn-primary">Print</button></a>
    </div>
</p>
</div>
<?php
$atr = 'id="trainer_feedback_form" name="trainer_feedback_form" ';
echo form_open("trainee/trainer_feedback/$item->user_id/$item->course_id/$item->class_id", $atr);
?>
<div class="modal1_050" id="ex7" style="display:none; height:535px;">
    <h2 class="panel_heading_style">Training Update</h2>    
    <table class="table table-striped">
        <tbody>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('CERTCOLDT'); ?>:</td>
                <td>
                    <?php
                    $collected_on = array(
                        'name' => 'CERTCOLDT',
                        'id' => 'collected_on',
                        'placeholder' => 'dd-mm-yyyy',
                        'readonly' => 'readonly',
                    );
                    echo form_input($collected_on);
                    ?>                    
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('SATSRATE'); ?>:</td>
                <td>                    
                    <?php
                    $satisfaction_rating = array('' => 'Select', '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5);
                    $satisfaction_rating_attr = 'id="satisfaction_rating"';
                    echo form_dropdown('SATSRATE', $satisfaction_rating, '', $satisfaction_rating_attr);
                    ?>   
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('CERTCOM1'); ?>:</td>
                <td>
                    <?php
                    $CERTCOM1_YES = array(
                        'name' => 'CERTCOM1',
                        'value' => 'Y',
                        'id' => 'CERTCOM1_YES'
                    );
                    $CERTCOM1_NO = array(
                        'name' => 'CERTCOM1',
                        'id' => 'CERTCOM1_NO',
                        'value' => 'N',
                    );
                    ?>              
                    <?php echo form_radio($CERTCOM1_YES); ?> Yes &nbsp;&nbsp;
                    <?php echo form_radio($CERTCOM1_NO); ?> No
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('APPKNLSKL'); ?>?</td>
                <td>                    
                                        <?php
                    $APPKNLSKL_YES = array(
                        'name' => 'APPKNLSKL',
                        'value' => 'Y',
                        'id' => 'APPKNLSKL_YES'
                    );
                    $APPKNLSKL_NO = array(
                        'name' => 'APPKNLSKL',
                        'id' => 'APPKNLSKL_NO',
                        'value' => 'N',
                    );
                    ?>              
                    <?php echo form_radio($APPKNLSKL_YES); ?> Yes &nbsp;&nbsp;
                    <?php echo form_radio($APPKNLSKL_NO); ?> No
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('EXPJOBSCP'); ?>?</td>
                <td>                    
                    <?php
                    $EXPJOBSCP_YES = array(
                        'name' => 'EXPJOBSCP',
                        'value' => 'Y',
                        'id' => 'EXPJOBSCP_YES'
                    );
                    $EXPJOBSCP_NO = array(
                        'name' => 'EXPJOBSCP',
                        'id' => 'EXPJOBSCP_NO',
                        'value' => 'N',
                    );
                    ?>              
                    <?php echo form_radio($EXPJOBSCP_YES); ?> Yes &nbsp;&nbsp;
                    <?php echo form_radio($EXPJOBSCP_NO); ?> No
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('RT3MNTHS'); ?>?</td>
                <td>                   
                                        <?php
                    $RT3MNTHS_YES = array(
                        'name' => 'RT3MNTHS',
                        'value' => 'Y',
                        'id' => 'RT3MNTHS_YES'
                    );
                    $RT3MNTHS_NO = array(
                        'name' => 'RT3MNTHS',
                        'id' => 'RT3MNTHS_NO',
                        'value' => 'N',
                    );
                    ?>              
                    <?php echo form_radio($RT3MNTHS_YES); ?> Yes &nbsp;&nbsp;
                    <?php echo form_radio($RT3MNTHS_NO); ?> No
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('DTCOMMEMP'); ?>:</td>
                <td>
                    <?php
                    $new_entrance = array(
                        'name' => 'DTCOMMEMP',
                        'id' => 'new_entrance',
                        'placeholder' => 'dd-mm-yyyy',
                        'readonly' => 'readonly',
                    );
                    echo form_input($new_entrance);
                    ?>
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('COMYTCOM'); ?>?</td>
                <td>                    
                    <?php
                    $COMYTCOM_C = array(
                        'name' => 'COMYTCOM',
                        'value' => 'C',
                        'id' => 'COMYTCOM_C',
                    );
                    $COMYTCOM_NYC = array(
                        'name' => 'COMYTCOM',
                        'id' => 'COMYTCOM_NYC',
                        'value' => 'NYC',
                    );
                    $COMYTCOM_EX = array(
                        'name' => 'COMYTCOM',
                        'id' => 'COMYTCOM_EX',
                        'value' => 'EX',
                    );
                    ?>              
                    <?php echo form_radio($COMYTCOM_C); ?> Competent <br/>
                    <?php echo form_radio($COMYTCOM_NYC); ?> Not Yet Competent <br/>
                    <?php echo form_radio($COMYTCOM_EX); ?> Exempted
                </td>
            </tr>
            <tr>
                <td colspan="2" class="td_heading">
                    <span style="vertical-align:top;"><?php echo get_catname_by_parm('COMMNTS'); ?>:</span>&nbsp;&nbsp;&nbsp;&nbsp;
                    <span>                        
                        <?php
                        $data = array(
                            'name' => 'COMMNTS',
                            'id' => 'COMMNTS',
                            'rows' => '1',
                            'cols' => '60',
                            'style' => 'width:70%',
                            'class' => 'upper_case',
                            'maxlength' => '250'
                        );

                        echo form_textarea($data);
                        ?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    <?php 
    echo form_hidden("viewuser","viewuser");
    ?>
    <div style="color: blue;">
        <span>1 - Strongly Disagree</span>&nbsp;&nbsp;
        <span>2 - Disagree</span>&nbsp;&nbsp;
        <span>3 - Neutral</span>&nbsp;&nbsp;
        <span>4 - Agree</span><br/>
        <span>5 - Strongly Agree</span>
    </div>
    <div class="popup_cance89">        
        <div class="popup_cancel9">
            <div rel="modal:close">
                <button class="btn btn-primary" type="submit">Save</button>&nbsp;&nbsp;
                <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a>
            </div>
        </div>
    </div>
</p>
</div>
<?php
echo form_close();
?>
<?php if (empty($discountdetails)) { ?>
    <div class="modal0000" id="ex144" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Alert Message</h2>
        There are no courses available.<br>
        <div class="popup_cancel popup_cancel001">
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
    </p>
    </div>
<?php } else { ?>
    <div class="modal_333" id="ex144" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Individual Discount by Course</h2>
        <div class="table-responsive payment_scroll" style="height: 300px;">
            <table style="width:100%; margin:0 auto;" class="table table-striped cpayment_received">
                <thead>
                    <tr>
                        <th width="70%">Course</th>
                        <th width="30%">Individual Discount</th>
                    </tr>
                </thead>
                <tbody>
    <?php
    foreach ($discountdetails as $k => $row):
        echo "<tr><td>" . $row['crse_name'] . "</td><td>" . number_format($row['discount_percent'], 2, '.', '') . "%</td></tr>";
    endforeach;
    ?>
                </tbody>
            </table>
        </div>
    </p>
    </div>
<?php } ?>
<div class="modal_333 modal_payment_recd" id="ex3" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Payment Received Details for '<span class="cp_invoice_company_name"></span>'</h2>
    <div class="table-responsive">
        <table class="table">
            <tbody>
                <tr>
                    <td class="td_heading">Course:</td>
                    <td><span class="cp_invoice_course_name"></span></td>
                    <td class="td_heading">Class:</td>
                    <td colspan="3"><span class="cp_invoice_class_name"></span></td>
                </tr>
                <tr>
                    <td class="td_heading">Invoice #:</td>
                    <td><span class="cp_invoice_id"></span></td>
                    <td class="td_heading">Invoice Dt:</td>
                    <td><span class="cp_invoice_dated"></span></td>
                    <td class="td_heading">Invoice Amount:</td>
                    <td>$<span class="cp_invoice_amount"></span> SGD</td>
                </tr>
                <tr>
                    <td class="td_heading"><span class="cp_invoice_discount_label"></span> Discount @<span class="cp_invoice_discount_rate"></span>%:</td>
                    <td>$<span class="cp_invoice_discount_amount"></span> SGD</td>
                    <td class="td_heading">Subsidy:</td>
                    <td>$<span class="cp_invoice_subsidy_amount"></span> SGD</td>
                    <td class="td_heading">GST @ (<span class="cp_invoice_gst_label"></span>)<span class="cp_invoice_gst_rate"></span>%:</td>
                    <td>$<span class="cp_invoice_total_gst"></span> SGD</td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <div class="table-responsive payment_scroll" style="height: 122px;">
        <table style="width:60%; margin:0 auto;" class="table table-striped cpayment_received">
            <thead>
                <tr>
                    <th>Payment Recd. On</th>
                    <th>Trainee Name</th>
                    <th>Amt. Recd.</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <br>
    <div class="popup_cance89">
        <a href="#" class="company_print_pdf"><button type="button" class="btn btn-primary">Print</button></a></div>
</p>
</div>
<div class="modal_020" id="ex6" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Payment Receipt</h2><br>
    <table width="100%">
        <tbody>
            <tr>
                <td rowspan="4"><img src="" class="logo" border="0" /></td>
                <td colspan="2"><span class="r_tenant_name"></span></td>
                <td class="td_heading"></td>
                <td><span class="r_invoice_no"></span></td>
            </tr>
            <tr>
                <td colspan="2"><span class="r_tenant_address"></span></td>
                <td class="td_heading"></td>
                <td><span class="r_invoice_recd"></span></td>
            </tr>
            <tr>
                <td colspan="4"><span class="r_tenant_phone"></span></td>
            </tr>
        </tbody>
    </table><br>
    <p><font style="font-size:14px; font-weight:bold;">Received</font> with thanks SGD <span class="r_invoice_amount_recd"></span> from <span class="r_invoice_trainee"></span> for <strong><i>'<span class="r_course"></span>-<span class="r_class"></span>-<span class="r_certilevel"></span>'</i></strong>. Mode of payment: <span class="r_invoice_mode"></span></p>
    <table class="table table-bordered">
        <tbody>
            <tr>
                <td class="td_heading">Class Start Date:</td>
                <td><span class="r_class_start"></span></td>
            </tr>
            <tr>
                <td class="td_heading">Location:</td>
                <td><span class="r_class_loc"></span></td>
            </tr>
            <tr>
                <td class="td_heading">Contact Details:</td>
                <td><span class="r_course_manager"></span>, <span class="r_tenant_phone"></span>, <span class="r_tenant_email"></span></td>
            </tr>
        </tbody>
    </table><br><br>
    <p><i>This is a computer generated receipt and doesn't require a seal or signature.</i></p>
    <div style="clear:both;"></div><br>
    <div class="popup_cance89">
        <a href="#" class="print_receipt"><button class="btn btn-primary" type="button">Print</button></a>
    </div>
</p>    
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/classtraineelist.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/view_trainee.js"></script>
<?php
 /// '$attn_status' added by shubhranshu to fix the issue for att status 26/11/2018
function get_links($attn_status,$enrolment_mode, $payment_status, $invoice_id, $user_id, $pymnt_due_id, $class_id, $view_trainee_data, $trainee_Status,$classStatus) {
    if ($payment_status == 'PYNOTREQD') {
        $tempLinkStr .= '<span style="color:red">Payment Not Required</span>';
    } else {
        $tempLinkStr = '';
        if ($view_trainee_data->data['user']->role_id != 'ADMN') {
            if ($trainee_Status == 'ACTIVE' && $classStatus != 'COMPLTD') {
                $tempLinkStr = '<a href="' . base_url() . 'class_trainee/booking_acknowledge_pdf/' . $user_id . '/' . $class_id . '">Booking ACK.</a>';
            }
            if ($enrolment_mode == 'SELF' && $payment_status == 'PAID') {
                $tempLinkStr .= '<a href="' . base_url() . 'class_trainee/export_payment_receipt/' . $pymnt_due_id . '">Receipt (Paid)</a>';
            } else if ($enrolment_mode == 'COMPSPON' && $payment_status == 'PAID') {
                $tempLinkStr .='<a href="' . base_url() . 'class_trainee/export_payment_received/' . $pymnt_due_id . '">Receipt (Paid)</a> &nbsp;&nbsp;';
            } else if ($enrolment_mode == 'COMPSPON' && $payment_status == 'PARTPAID') {
                $tempLinkStr .='<a href="' . base_url() . 'class_trainee/export_payment_received/' . $pymnt_due_id . '">Receipt (Part Paid)</a> &nbsp;&nbsp;';
            } else {
                $tempLinkStr .= '<span style="color:red">Not Paid</span>';
            }
        } else {
            if ($enrolment_mode == 'SELF' && $payment_status == 'PAID') {
                $tempLinkStr = '<a href="' . base_url() . 'class_trainee/export_payment_received/' . $pymnt_due_id . '">Paid</a> &nbsp;&nbsp;';
                /*   added by shubhranshu due to receipt issue for absent trainee      */
                if($attn_status==1){
                     $tempLinkStr .= '<a href="' . base_url() . 'class_trainee/export_payment_receipt/' . $pymnt_due_id . '">Receipt</a>';
                }else{
                    $tempLinkStr .= '<div><i>Receipt Not Available(Trainee is absent)</i></div>';
                }
                /*  -----------------------------------------------------------------    */
                //$tempLinkStr .= '<a href="' . base_url() . 'class_trainee/export_payment_receipt/' . $pymnt_due_id . '">Receipt</a>';
            } elseif ($enrolment_mode == 'SELF' && $payment_status == 'NOTPAID') {
                if ($trainee_Status == 'ACTIVE') {
                    $tempLinkStr = '<a href="' . base_url() . 'accounting/update_payment" style="color:red" >Not Paid</a> &nbsp;&nbsp;';
                    if($classStatus != 'COMPLTD')
                        $tempLinkStr .= '<a href="' . base_url() . 'class_trainee/booking_acknowledge_pdf/' . $user_id . '/' . $class_id . '">Booking ACK.</a>';
                } else {
                    $tempLinkStr = '<span style="color:red;" >Not Paid</span> &nbsp;&nbsp;';
                }
            } elseif ($enrolment_mode == 'COMPSPON' && $payment_status == 'PAID') {
                $tempLinkStr = '<a href="javascript:;" class="company_paid" data-invoice="' . $invoice_id . '" style="color:green;">Paid</a> &nbsp;&nbsp;';
                $tempLinkStr .='<a href="' . base_url() . 'class_trainee/export_payment_received/' . $pymnt_due_id . '">Received</a> &nbsp;&nbsp;';
                if ($trainee_Status == 'ACTIVE' && $classStatus != 'COMPLTD')
                    $tempLinkStr .='<a href="' . base_url() . 'class_trainee/booking_acknowledge_pdf/' . $user_id . '/' . $class_id . '">Booking ACK.</a>';
            } elseif ($enrolment_mode == 'COMPSPON' && ($payment_status == 'PARTPAID' || $payment_status == 'NOTPAID')) {
                $label = ($payment_status == 'NOTPAID') ? 'Not Paid' : 'Part Paid';
                $tempLinkStr = '<a href="' . base_url() . 'accounting/update_payment" style="color:red;">' . $label . '</a>&nbsp;&nbsp;';
                if ($trainee_Status == 'ACTIVE' && $classStatus != 'COMPLTD')
                    $tempLinkStr .='<a href="' . base_url() . 'class_trainee/booking_acknowledge_pdf/' . $user_id . '/' . $class_id . '">Booking ACK.</a>';
            }
        }
    }
    return $tempLinkStr;
}
?>
</div>
<?php echo $reacti_popup; ?>

<script>
    function checkEdit(){
            //alert();
            $('#view_edit_email').attr('disabled',false);
            $('#email_edit_btn').html("<span onclick='checkEmail();'>Save</span>");
        }
    function checkEmail(){
            $emaill=$('#view_edit_email').val();
            $.ajax({
                url: baseurl + "internal_user/check_email_status",
                type: "post",
                data: {'email' : $emaill,'usrid':<?php echo $user_list_values->user_id ?>},
                success: function(res) {
                    if (res == 1) {
                        window.email_id = 'exists';
                        $("#view_edit_email_err").text("[Email Id exists!]").addClass('error');
                    } else {
                        window.email_id = 'notexists';
                        $("#view_edit_email_err").removeClass('error');
                        $("#view_edit_email_err").text("Update Successful").addClass('success');
                        $('#email_edit_btn').html('');
                    }
                },
                error: function() {
                    return false;
                }
            });
        return false;
   
    }
</script>
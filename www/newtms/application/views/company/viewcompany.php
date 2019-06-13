<?php
$reacti_btn = '';
$reacti_popup = '';
if ($company_info[0]->comp_status == 'INACTIV'):
    $reacti_btn = '<a class="small_text" href="#ex8" rel="modal:open">
                        <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-saved"></span>&nbsp;
                            Reactivate
                        </button>
                    </a> &nbsp; &nbsp;';

    $form_attributes = array('name' => 'user_reactivate_form',
        'id' => 'user_reactivate_form',
        "onsubmit" => "return(validate_reactivate_user(true));",
        "onchange" => "if(validation == 1){ return(validate_reactivate_user(false))};");
    $form_tag = form_open("company/reactivate_company", $form_attributes);
    $reactivate_reasons = fetch_metavalues_by_category_id(Meta_Values::COMPANY_REACTIVATE_REASONS);
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
        'style' => 'margin:5px 0 0 0',
        'class' => 'upper_case',
    );
    $reacti_popup = $form_tag . '<div class="modal1_051" id="ex8" style="display:none;">
                        <p>
                        <h2 class="panel_heading_style">Reactivate Company</h2>  
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
                        Are you sure you want to reactivate this company?
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
                    </div>' . form_hidden('company_id', $company_info[0]->company_id). form_close();
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
            return retVal;
        }
    </script>
    <?php
endif;
?>
<div class="col-md-10">
    <?php
    if ($this->session->flashdata('success')) {
        echo '<div class="success">' . $this->session->flashdata('success') . '</div>';
    }
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/company.png" /> Company Detail 
        <?php if ($trainees_count > 0) { ?>
            <span class="label label-default pull-right white-btn">
                <a href="<?php echo site_url(); ?>company/trainees/<?php echo $company_id; ?>"><span class="glyphicon glyphicon-eye-open"></span> View Trainee/ Employee</a>
            </span>
        <?php } ?>
    </h2>
    <h2 class="sub_panel_heading_style">
        <img src="<?php echo base_url(); ?>/assets/images/company-detail.png" /> Company Details
        <span class="pull-right"> <img src="<?php echo base_url(); ?>/assets/images/personal_details.png"> <a href="#ex144" rel="modal:open" style="color: blue;">Company Discount</a></span>
    </h2>
    <div class="bs-example">
        <?php
        foreach ($company_info as $company) {
            ?>        
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td class="td_heading" width="15%">Company Name:</td>
                            <td><label class="label_font"><?php echo trim($company->company_name); ?></label></td>
                            <td class="td_heading" width="15%">Registration Number:</td>
                            <td><label class="label_font"><?php echo trim($company->comp_regist_num); ?></label></td>
                            <td class="td_heading" width="15%">Registration Type:</td>
                            <td><label class="label_font">
                                    <?php
                                    $business_type = ($company->business_type)?get_param_value(trim($company->business_type)):'';
                                    echo $business_type->category_name;
                                    ?>
                                </label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Size:</td>
                            <td><label class="label_font">
                                    <?php
                                    $business_size = ($company->business_size)?get_param_value(trim($company->business_size)):'';
                                    echo $business_size->category_name;
                                    ?>
                                </label></td>
                            <td class="td_heading">Phone Number:</td>
                            <td><label class="label_font"><?php echo trim($company->comp_phone); ?></label></td>
                            <td class="td_heading">Fax Number:</td>
                            <td><label class="label_font"><?php echo trim($company->comp_fax); ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Country:</td>
                            <td><label class="label_font">
                                    <?php
                                    $country = ($company->comp_cntry_scn)?get_param_value(trim($company->comp_cntry_scn)):'';
                                    echo $country->category_name;
                                    ?>
                                </label>
                                <div id="" style="width:50%; float:right;">
                                    <?php if ($company->comp_cntry_scn == 'SGP') { ?>
                                        <strong>SCN:</strong>
                                        <label class="label_font"><?php echo trim($company->comp_scn); ?></label>
                                    <?php } ?>
                                </div>  
                            </td>
                            <td class="td_heading">SME Type:</td>
                            <td colspan="3"><label class="label_font"><?php echo $sme_nonsme; ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Company Attn.:</td>
                            <td><label class="label_font"><?php echo $company->comp_attn; ?></label></td>
                            <td class="td_heading">Company Email:</td>
                            <td colspan="3"><label class="label_font"><?php echo $company->comp_email; ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Comments / Special Instructions:</td>
                            <td colspan="5"><label class="label_font"><?php echo trim($company->remarks); ?></label></td>          
                        </tr>
                    </tbody>
                </table>
            </div>
            <br>
            <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/address.png" /> Address</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td class="td_heading" width="20%">Street / Building:</td>
                            <td width="30%"><label class="label_font"><?php echo trim($company->comp_address); ?></label></td>
                            <td class="td_heading" width="20%">City:</td>
                            <td width="30%"><label class="label_font"><?php echo trim($company->comp_city); ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Country:</td>
                            <td><label class="label_font">
                                    <?php
                                    if ($company->comp_cntry != '') {
                                        $country = get_param_value(trim($company->comp_cntry));
                                        echo $country->category_name;
                                    }
                                    ?>
                                </label></td>
                            <td class="td_heading">State:</td>
                            <td><label class="label_font">
                                    <?php
                                    if ($company->comp_state != '') {
                                        $state = get_param_value(trim($company->comp_state));
                                        echo $state->category_name;
                                    }
                                    ?>
                                </label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Postal Code:</td>
                            <td colspan="5"><label class="label_font"><?php echo trim($company->comp_zip); ?></label></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php
        }
        ?>
        <br>
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/contact.png" /> Contact Details </h2>
        <div class="table-responsive">
            <?php
            if(empty($company_users_details)){
                echo '<table class="table table-striped"><tr class=danger><td style="color:red;text-align: center;"> No Company Users available. </td></tr></table>';
            }
            $i=1;
            foreach ($company_users_details as $values) {
                ?>            
                <table class="table table-striped" border="0">
                    <tbody>
                        <tr>
                            <td class="td_heading" width="15%"><?php echo ($i==1)?'Primary Contact Name':'Name';?>:</td>
                            <td colspan="3"><label class="label_font" width="15%"><?php echo $values->first_name; ?></label></td>                            
                            <td class="td_heading" width="15%">Gender:</td>
                            <td width="15%"><label class="label_font">
                                    <?php
                                    if ($values->gender != '') {
                                        $gender = get_param_value(trim($values->gender));
                                        echo $gender->category_name;
                                    }
                                    ?>
                                </label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Contact Number [O]:</td>
                            <td><label class="label_font"><?php echo $values->contact_number; ?></label></td>
                            <td class="td_heading">Mobile Number [O]:</td>
                            <td><label class="label_font"><?php echo $values->alternate_contact_number; ?></label></td>
                            <td class="td_heading"><!--Mobile Number [P]-->&nbsp;</td>
                            <td><label class="label_font">&nbsp;</label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Email Id 01:</td>
                            <td><label class="label_font"><?php echo $values->registered_email_id; ?></label></td>
                            <td class="td_heading">Email Id 02:</td>
                            <td><label class="label_font"><?php echo $values->alternate_email_id; ?></label></td>
                            <td class="td_heading">Username:</td>
                            <td><label class="label_font"><?php echo $values->user_name; ?></label></td>
                        </tr>
                        <tr>
                            <td><strong>Contact Status:</strong></td>
                            <td   colspan="4"><?php
                                $user_acct_status = ($values->user_acct_status)?get_param_value(trim($values->user_acct_status)):'';
                                if ($values->user_acct_status == 'INACTIV') {
                                    echo '<span class="red">' . $user_acct_status->category_name . '</span>';
                                    if ($values->deacti_reason == 'OTHERS') {
                                        $reason = $values->deacti_reason_oth;
                                    } else {
                                        $meta_details = ($values->deacti_reason)?get_param_value($values->deacti_reason):'';
                                        $reason = $meta_details->category_name;
                                    }
                                    $deactivated_by = $this->internaluser->get_user_details($tenant_id, $values->deacti_by);
                                    echo '<span> ( <strong>Deactivation Reason:</strong> ' . $reason . '. Deactivated by ' . $deactivated_by->user_name . ' on ' . $values->acct_deacti_date_time . ')</span>';
                                } else if ($values->user_acct_status == 'ACTIVE') {
                                    echo '<span class="green">' . $user_acct_status->category_name . '</span>';
                                } else if ($values->user_acct_status == 'PENDACT') {
                                    echo '<span class="blue">' . $user_acct_status->category_name . '</span>';
                                } else {
                                    echo $user_acct_status->category_name;
                                }
                                ?></td>
                            <td>
                                <?php if ($this->data['user']->role_id == 'ADMN') { ?>
                                    <a title="Reset Password" href="<?php echo base_url() ?>company/reset_password/<?php echo $values->user_id ?>/<?php echo $company_id; ?>">Reset Password</a>
                                <?php } ?>
                            </td>
                        </tr>
                        <TR><TD colspan="6">&nbsp;</TD></TR>
                </table> 
                <?php
            $i++;}
            ?>
            <strong>Company Status:</strong></td>
            <td >   <?php
                $company_acct_status = ($company->comp_status)?get_param_value(trim($company->comp_status)):'';
                if ($company->comp_status == 'INACTIV') {
                    echo '<span class="red">' . $company_acct_status->category_name . '</span>';
                    if ($company->deacti_reason == 'OTHERS') {
                        $reason = $company->deacti_reason_oth;
                    } else {
                        $meta_details = ($company->deacti_reason)?get_param_value(trim($company->deacti_reason)):'';
                        $reason = $meta_details->category_name;
                    }
                    $deactivated_by = $this->internaluser->get_user_details($tenant_id, $company->deacti_by);
                    echo '<span> ( <strong>Deactivation Reason: </strong>' . $reason . '. Deactivated by ' . $deactivated_by->user_name . ' on ' . $company->acct_deacti_date_time . ')</span>';
                } else if ($company->comp_status == 'ACTIVE') {
                    echo '<span class="green">' . $company_acct_status->category_name . '</span>';
                } else if ($company->comp_status == 'PENDACT') {
                    echo '<span class="blue">' . $company_acct_status->category_name . '</span>';
                } else {
                    echo $company_acct_status->category_name;
                }
                ?></td>
            <td colspan="4"></td>
            </tr>                    
            </tbody>
            </table>
        </div>
        <br>
    </div>
    <div class="button_class">
        <?php echo $reacti_btn; ?>
        <a href="<?php echo site_url(); ?>company">
            <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-step-backward"></span>&nbsp;Back</button>
        </a>
    </div>
</div>
<?php if (empty($company_discount)) { ?>
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
        <h2 class="panel_heading_style">Company Discount by Course</h2>
        <div class="table-responsive payment_scroll" style="height: 300px;">
            <table style="width:100%; margin:0 auto;" class="table table-striped cpayment_received">
                <thead>
                    <tr>
                        <th width="60%">Course</th>
                        <th width="20%">Discount %</th>
                        <th width="20%">Discount Amt. (SGD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($company_discount as $row) {
                        $k = $row['course_id'];
                        echo "<tr>
                                        <td>" . $row['crse_name'] . "</td>
                                        <td>" . number_format($row['Discount_Percent'], 2, '.', '') . " %</td>
                                        <td>$ " . number_format($row['Discount_Amount'], 2, '.', '') . "</td>
                                    </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </p>
    </div>
<?php } ?>
<?php echo $reacti_popup; ?>
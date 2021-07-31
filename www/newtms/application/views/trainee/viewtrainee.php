<?php
$this->load->helper('common_helper');


$reacti_btn = '';
$reacti_popup = '';
if ($trainee['userdetails']['account_status'] == 'INACTIV'):


    $reacti_btn = '<a class="small_text" href="#ex897" rel="modal:open">
                        <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-saved"></span>&nbsp;
                            Reactivate
                        </button>
                    </a> &nbsp; &nbsp;';





    $form_attributes = array('name' => 'user_reactivate_form',
        'id' => 'user_reactivate_form',
        "onsubmit" => "return(validate_reactivate_user(true));",
        "onchange" => "if(validation == 1){ return(validate_reactivate_user(false))};");
    $form_tag = form_open("trainee/reactivate_trainee", $form_attributes);
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
        'style' => 'margin:5px 0 0 0',
        'class' => 'upper_case',
    );
    $reacti_popup = $form_tag . '<div class="modal1_051" id="ex897" style="display:none;">
                        <p>
                        <h2 class="panel_heading_style">Reactivate Trainee</h2>  
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
                        Are you sure you want to reactivate this user?
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
                         
                    </div>' . form_hidden('user_id', $trainee['userdetails']['user_id']) .
            form_hidden('company_id', $trainee['company']['company_id'])
            . form_close();
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
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-eye-open"></span> View Trainee Details</h2>		  
    <div class="bs-example">
        <div class="table-responsive">          
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <td width="23%" class="td_heading">Country of Residence:<span class="required">*</span></td>
                        <td width="16%"><label class="label_font"><?php echo ($trainee[userdetails]['country_of_residence']) ? get_catname_by_parm($trainee[userdetails]['country_of_residence']) : ''; ?></label></td>
                        <?php if ($trainee[userdetails]['country_of_residence'] == 'SGP') { ?> 
                            <td colspan="2">               


                                <?php if ($trainee[userdetails]['tax_code_type'] != 'SNG_3') { ?> 


                                    <strong>NRIC Type:</strong> <?php echo ($trainee[userdetails]['tax_code_type']) ? get_catname_by_parm($trainee[userdetails]['tax_code_type']) : ''; ?>  &nbsp;&nbsp;&nbsp; 
                                    <strong>NRIC Code:</strong> <?php echo $trainee[userdetails]['tax_code']; ?>
                                <?php } else { ?>                                       
                                    <strong>OTHER:</strong> <?php echo ($trainee[userdetails]['other_identi_type']) ? get_catname_by_parm($trainee[userdetails]['other_identi_type']) : ''; ?>&nbsp;&nbsp;&nbsp;
                                    <strong>Code:</strong> <?php echo $trainee[userdetails]['other_identi_code']; ?>
                                <?php } ?>    
                            </td>
                        <?php } else { ?>
                            <td colspan="2">
                                <strong><?php echo $trainee[userdetails]['tax_code_type']; ?> No.:</strong>
                                <?php echo $trainee[userdetails]['tax_code']; ?>
                            </td>
                        <?php } ?>
                        <td class="td_heading">Username:<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $trainee[userdetails]['user_name']; ?></label></td> 
                    </tr>          
                    <tr>
                        <td class="td_heading">Activation Status:</td>


                        <td class="td_heading" colspan="4"> 
                            <label class="">
                                <?php
                                if ($trainee[userdetails]['account_status'] == 'ACTIVE') {
                                    echo "<font class='green-active'>Active</font>";
                                } else if ($trainee[userdetails]['account_status'] == 'INACTIV') {
                                    $deactivation_details = '';
                                    $deactivation_details = ' - Deactivated by ' . $deactivated_by->first_name . ' ' . $deactivated_by->last_name . '(' . $deactivated_by->user_name . ') on ' . date("d/m/Y", strtotime($trainee[userdetails]['acct_deacti_date_time'])) . '.';
                                    if ($trainee[userdetails]['deacti_reason'] == 'OTHERS') {
                                        $deactivation_details .= ' Reason: ' . $trainee[userdetails]['deacti_reason_oth'] . '.';
                                    } else {
                                        $user_deactivation_reason = get_param_value($trainee[userdetails]['deacti_reason']);
                                        $deactivation_details .= ' Reason: ' . $user_deactivation_reason->category_name . '.';
                                    }
                                    echo "<font color='red'>In-Active " . $deactivation_details . "</font>";
                                } else if ($trainee[userdetails]['account_status'] == 'PENDACT') {
                                    echo "<font color='blue'>Pending Activation</font>";
                                }
                                ?>
                            </label>
                        </td>
                        <td>


                            <?php if ($this->data['user']->role_id == 'ADMN') { ?>
                                <a title="Reset Password" href="<?php echo base_url() ?>trainee/reset_password/<?php echo $user_id ?>">Reset Password</a>
                            <?php } ?>
                        </td>
                    </tr>
                </tbody>
            </table>    
        </div>
    </div>
    <br>


    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/education.png"> Training History</h2>
    <div class="table-responsive d-table-scroll">
        <table class="table table-striped">
            <thead>
                <?php if (!empty($training_history)) { ?>
                    <tr>
                        <th width="25%">Course Name</th>
                        <?php if (TENANT_ID == 'T18') { ?>
                            <th width="25%">Enrollment Date</th>
                        <?php } else { ?>
                            <th width="25%">Course Date</th>
                        <?php } ?>
                        <th width="25%">Training Score</th>
                        <th width="25%">Company Name</th>
                        <th width="25%">Assessment Date</th>
                    </tr>
                    <?php
                } else {
                    echo "<tr class='danger'><td colspan='8' style='text-align:center;color:red'><label>No historical training data available.</label></td></tr>";
                }
                ?>
            </thead>
            <tbody>                
                <?php foreach ($training_history as $item): ?>
                    <tr>
                        <td>
                            <label class="label_font"><?php echo $item->course_name; ?></label>
                        </td>
                        <td>
                            <?php if (TENANT_ID == 'T18') { ?>
                                <label class="label_font"><?php echo $item->enrollment_date; ?></label>
                            <?php } else { ?>
                                <label class="label_font"><?php echo ($item->course_date && $item->course_date != '0000-00-00') ? date('d-m-Y', strtotime($item->course_date)) : ''; ?></label>    
                            <?php } ?> 
                        </td>
                        <td><label class="label_font"><?php echo get_catname_by_parm($item->training_score); ?></label></td>
                        <td><label class="label_font"><?php echo $item->company_name; ?></label></td>
                        <td><label class="label_font"><?php echo ($item->assessment_date && $item->assessment_date != '0000-00-00 00:00:00') ? date('d-m-Y', strtotime($item->assessment_date)) : ''; ?></label></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (TENANT_ID == 'T18') { ?>
                    <tr> <td colspan="6"> <center><a href="#ex145" rel="modal:open" style="color: blue;"><b>Module History</b></a></center></td></tr>
            <?php } ?> 
            </tbody>
        </table>  
    </div>
    <?php if (trim($trainee[userdetails]['history_remarks']) != '') { ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading" width="15%">Historical Remarks:</td>
                    <td width="85%">
                        <div class="table-responsive payment_scroll" style="height: 50px;min-height: 50px;"><?php echo $trainee[userdetails]['history_remarks']; ?></div>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php } ?>
    <br/>


    <h2 class="sub_panel_heading_style">
        <img src="<?php echo base_url(); ?>/assets/images/personal_details.png"> Personal Details
        <span class="pull-right"> <img src="<?php echo base_url(); ?>/assets/images/personal_details.png"> <a href="#ex144" rel="modal:open" style="color: blue;">Individual Discount</a></span>
    </h2>
    <div class="table-responsive">
        <table class="table table-striped">

            <tbody>

                <tr>
                    <td class="td_heading" width="15%">Name:<span class="required">*</span></td>
                    <td colspan="3"><label class="label_font"><?php echo $trainee[userdetails]['first_name']; ?></label></td>                    
                    <td class="td_heading">Nationality:<span class="required">*</span></td>
                    <td><label class="label_font">


                            <?php
                            $nationalityLabel = '';


                            $nationality = fetch_metavalues_by_category_id(Meta_Values::NATIONALITY);


                            foreach ($nationality as $item):


                                if ($item['parameter_id'] == $trainee[userdetails]['nationality']) {


                                    $nationalityLabel = $item['category_name'];


                                    break;
                                }


                            endforeach;
                            ?>    


                            <?php echo ($nationalityLabel); ?></label>


                    </td>                    
                    <td rowspan="3" align="center">


                        <div class="userphoto">


                            <?php if ($trainee[userdetails]['photo_upload_path']): ?> 
                                <img src="<?php echo base_url() . $trainee[userdetails]['photo_upload_path']; ?>"/> 
                            <?php else: ?>
                                <img src="<?php echo base_url(); ?>assets/images/photo.jpg"/> 
                            <?php endif; ?>
                        </div>
                    </td>

                </tr>        
                <tr>
                    <td class="td_heading" width="15%">Gender:<span class="required">*</span></td>
                    <td>
                        <label class="label_font"><?php echo ($trainee[userdetails]['gender']) ? get_catname_by_parm($trainee[userdetails]['gender']) : ''; ?></label>
                    </td>
                    <td class="td_heading">Date of Birth:</td>
                    <td><?php echo empty($trainee[userdetails]['dob']) ? '' : date('d-m-Y', strtotime($trainee[userdetails]['dob'])); ?></td>
                    <td class="td_heading">Contact Number:<span class="required">*</span></td>
                    <td><label class="label_font"><?php echo $trainee[userdetails]['contact_number']; ?></label></td>                    
                </tr>        
                <tr>
                    <td class="td_heading">Alt. Contact #:</td>
                    <td><label class="label_font"><?php echo $trainee[userdetails]['alternate_contact_number']; ?></label></td>
                    <td class="td_heading">Race:</td>
                    <td><label class="label_font"><?php echo ($trainee[userdetails]['race']) ? get_catname_by_parm($trainee[userdetails]['race']) : ''; ?></label></td>                    
                    <!--add by pritam-->
                    <?php
//echo "com".$trainee['company']['company_name'];
                    if ($trainee['company']['company_name'] == "NA") {
                        if ($trainee[userdetails]['company_name']) {
                            ?>
                            <td class="td_heading">Certificate Sent To:</td>
                            <td><label class="label_font"><?php echo $trainee[userdetails]['company_name']; ?></label></td> 
                            <?php
                        } else {
                            ?>
                            <td class="td_heading">Assign Trainee to Company:</td>
                            <td><label class="label_font"><?php echo $trainee['company']['company_name']; ?></label></td>
                            <?php
                        }
                    } else {
                        ?>
                        <td class="td_heading">Assign Trainee to Company:</td>
                        <td><label class="label_font"><?php echo $trainee['company']['company_name']; ?></label></td>
                        <?php
                    }
                    ?>
                    <!--end-->
                </tr>        
                <tr>
                    <td class="td_heading">Salary Range:</td>
                    <td>
                        <label class="label_font"><?php echo ($trainee[userdetails]['salary_range']) ? get_catname_by_parm($trainee[userdetails]['salary_range']) : ''; ?></label>
                    </td>
                    <td class="td_heading">Designation:</td>
                    <td colspan="4">
                        <label class="label_font"><?php echo ($trainee[userdetails]['occupation_code']) ? get_catname_by_parm($trainee[userdetails]['occupation_code']) : ''; ?></label>
                    </td>                    		                      
                </tr>
                <tr>


                    <td class="td_heading">Email Activation:<span class="required">*</span></td>
                    <td colspan="6">
                        <label class="label_font"><?php
                            if ($trainee[userdetails]['acc_activation_type'] == 'BPEMAC')
                                echo "By-pass email activation";
                            else
                                echo "Do not By-pass email activation";
                            ?>
                        </label>
                    </td>
                </tr>
                <tr>                    
                    <td class="td_heading">Email Id:</td>
                    <td><label class="label_font"><?php echo $trainee[userdetails]['registered_email_id']; ?></label></td>
                    <td class="td_heading">Confirm Email Id:</td>
                    <td><label class="label_font"><?php echo $trainee[userdetails]['registered_email_id']; ?></label></td>
                    <td class="td_heading">Alternate Email Id:</td>
                    <td colspan="2">
                        <label class="label_font"><?php echo $trainee[userdetails]['alternate_email_id']; ?></label>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading" colspan="2">Highest Education Level:<span class="required">*</span></td>
                    <td colspan="2">
                        <label class="label_font"><?php echo ($trainee[userdetails]['highest_educ_level']) ? get_catname_by_parm($trainee[userdetails]['highest_educ_level']) : ''; ?></label>
                    </td>
                    <td class="td_heading" colspan="1">Certificate Pickup Detail:</td>
                    <td colspan="2">
                        <label class="label_font">
                            <?php
                            if ($trainee[userdetails]['certificate_pick_pref'] == 'cerit_self')
                                echo "I will pickup myself";
                            else if ($trainee[userdetails]['certificate_pick_pref'] == 'cerit_mail')
                                echo "Mail to my personal email Id";
                            else if ($trainee[userdetails]['certificate_pick_pref'] == 'cerit_post')
                                echo "Mail to my postal address";
                            else
                                echo "NA";
                            ?>
                        </label>    
                    </td>

                </tr>
<!--                <tr>
                    <td class="td_heading">Additional Remarks:</td>
                    <td colspan="6">
                        <label class="label_font"><?php echo $trainee[userdetails]['additional_remarks']; ?>
                        </label>
                    </td>
                </tr>-->
            </tbody>
        </table>
    </div>
    <br>        
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/address.png"> Address</h2>
    <div class="table-responsive">
        <table class="table table-striped">

            <tbody>
                <tr>
                    <td class="td_heading" width="20%">Building/Street:</td>
                    <td colspan="3"><label class="label_font"><?php echo $trainee[userdetails]['personal_address_bldg']; ?></label></td>
                    <td class="td_heading" width="20%">City:</td>
                    <td><label class="label_font"><?php echo $trainee[userdetails]['personal_address_city']; ?></label></td>

                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Country:</td>
                    <td> <label class="label_font"><?php echo ($trainee[userdetails]['personal_address_country']) ? get_catname_by_parm($trainee[userdetails]['personal_address_country']) : ''; ?></label></td>
                    <td class="td_heading">State:</td>
                    <td> <label class="label_font"><?php echo ($trainee[userdetails]['personal_address_state']) ? get_catname_by_parm($trainee[userdetails]['personal_address_state']) : ''; ?></label></td>

                    <td class="td_heading">Postal Code:</td>
                    <td colspan="3"><label class="label_font"><?php echo $trainee[userdetails]['personal_address_zip']; ?></label></td>

                </tr>                
            </tbody>
        </table>
    </div>
    <br>
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/education.png"> Educational Details</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Level</th>
                    <th>Year of Completion</th>
                    <th>Score/Grade</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!$trainee[edudetails]) {
                    echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>No data available.</label></td></tr>";
                }
                ?>
                <?php foreach ($trainee[edudetails] as $item): ?>
                    <tr>
                        <td><label class="label_font"><?php echo ($item['educ_level']) ? get_catname_by_parm($item['educ_level']) : ''; ?></label>
                        </td>
                        <td>        
                            <label class="label_font"><?php echo ($item['educ_yr_completion']) ? $item['educ_yr_completion'] : ''; ?></label>    
                        </td>
                        <td><label class="label_font"><?php echo $item['educ_score']; ?></label></td>
                        <td><label class="label_font"><?php echo $item['educ_remarks']; ?></label></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="table-responsive">
        <br>
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/personal_details.png"> Other Certificates and Trainings</h2>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th width="22%">Certificate Name</th>
                    <th width="25%">Year of Certification</th>
                    <th width="19%">Validity</th>
                    <th width="34%">Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!$trainee[otherdetails]) {
                    echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>No data available.</label></td></tr>";
                }
                ?>
                <?php foreach ($trainee[otherdetails] as $item): ?>
                    <tr>
                        <td><label class="label_font"><?php echo $item['cert_name']; ?></label>
                        </td>
                        <td><label class="label_font"><?php echo ($item['yr_completion']) ? $item['yr_completion'] : ''; ?></label></td>
                        <td><label class="label_font"><?php echo ($item['valid_till']) ? date('d-m-Y', strtotime($item['valid_till'])) : ''; ?></label></td>
                        <td><label class="label_font"><?php echo $item['oth_remarks']; ?></label></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

    <div class="table-responsive"><br>
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/officail_details.png"> Work Experience</h2>

        <table class="table table-striped">
            <thead>

                <tr>
                    <th width="22%">Name of Organization</th>
                    <th width="25%">Employment From</th>
                    <th width="19%">Employment To</th>
                    <th width="34%">Designation/ Remarks</th>
                </tr>
            </thead>
            <tbody>      
                <?php
                if (!$trainee[workdetails]) {
                    echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>No data available.</label></td></tr>";
                }
                ?>
                <?php foreach ($trainee[workdetails] as $item): ?>
                    <tr>
                        <td><label class="label_font"><?php echo $item['org_name']; ?></label>
                        </td>
                        <td><label class="label_font"><?php echo ($item['emp_from_date']) ? date('d-m-Y', strtotime($item['emp_from_date'])) : ''; ?></label></td>
                        <td><label class="label_font"><?php echo ($item['emp_to_date']) ? date('d-m-Y', strtotime($item['emp_to_date'])) : ''; ?></label></td>
                        <td><label class="label_font"><?php echo ($item['designation']) ? get_catname_by_parm($item['designation']) : ''; ?></label></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>  
    </div>


    <br>
    <div class="small_heading">        
        Training Attended Details as on <?php echo date('M j Y l'); ?>       
    </div>		  
    <?php
//    echo count($training_details); echo"<br/>";
//                print_r($training_details);
    ?>
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
                if (count($training_details) > 0) {
                    foreach ($training_details as $item) :
                        $enrol_mode = ($item->enrolment_mode != '') ? get_catname_by_parm($item->enrolment_mode) : '';
                        $company = ($item->enrolment_mode == 'COMPSPON') ? ' ( ' . $item->company_name . ' )' : '';
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


                        if ($trainee['userdetails']['account_status'] == 'PENDACT') {


                            $linkStr = '<span style="color:red;">Account not yet activated.</span>';
                        } else if ($trainee['userdetails']['account_status'] == 'INACTIV') {


                            $linkStr = get_links($item->attn_stats, $item->enrolment_mode, $item->payment_status, $item->invoice_id, $item->user_id, $item->pymnt_due_id, $item->class_id, $this, $trainee['userdetails']['account_status'], $item->company_id, $assmnt_links);
                        } else {
                            $linkStr = '';
                            $classStatus = '';
                            if ($cur_date >= $class_end_datetime_str) {
                                if ($cur_date == $class_end_datetime_str) {
                                    $classStatus = 'IN_PROG';
                                } else if ($class_end_datetime_str < $cur_date) {
                                    $classStatus = 'COMPLTD';
                                }
                                $linkStr = '';
                                if ($item->payment_status != 'PYNOTREQD' &&
                                        (($this->data['user']->role_id == 'ADMN' || $this->data['user']->role_id == 'CRSEMGR') && $trainee['userdetails']['account_status'] == 'ACTIVE')) {
                                    $linkStr = '<a href="javascript:;" class="get_update" data-class="' . $item->class_id . '" data-user="' . $item->user_id . '">Update TG# </a>&nbsp;&nbsp;&nbsp;';
                                }
                                if ($this->data['user']->role_id == 'ADMN' || $this->data['user']->role_id == 'CRSEMGR') {
                                    $ci = &get_instance();
                                    $ci->load->model("classtraineemodel");
                                    $check_attendance = $ci->classtraineemodel->check_attendance_row($item->tenant_id, $item->course_id, $item->class_id);

                                    $feedback_status = TRUE;
                                    $crsemgr_array = explode(",", $item->crse_manager);
                                    if (!in_array($this->data['user']->user_id, $crsemgr_array) && $this->data['user']->role_id == 'CRSEMGR') {
                                        $feedback_status = FALSE;
                                    }
                                    if ($feedback_status == TRUE) {
                                        if ($classStatus == 'COMPLTD') {
                                            if ($check_attendance > 0) {
                                                $linkStr .= '<a id="training_update" href="#ex7" rel="modal:open" data-course="' .
                                                        $item->course_id . '" data-class="' . $item->class_id . '" data-user="' .
                                                        $item->user_id . '" data-payment="' . $item->pymnt_due_id . '" class="training_update small_text1">Feedback.</a>'
                                                        . '&nbsp;&nbsp;&nbsp;';
                                            } else {
                                                $linkStr.='<form action="' . base_url() . 'class_trainee/mark_attendance" method="post" name="maarkatt[]">'
                                                        . '<input type="hidden" name="course_id" value="' . $item->course_id . '" /><input type="hidden" name="class_id" value="' . $item->class_id . '" />'
                                                        . '<input type="submit" class="red" value ="Mark Attendance" /></form><br />';
                                            }
                                        }
                                    }
                                }
                                $check_competent = $this->classtraineemodel->check_competent($item->tenant_id, $item->course_id, $item->class_id, $item->user_id);

                                if ($check_competent > 0) {
                                    $wsq_courses_array = $this->config->item('wsq_courses'); // wsq courses modified by shubhranshu

                                    $tenant_array = array('T02', 'T12'); // xp and xp2 
                                    $linkStr .= '<a href="' . base_url() . 'trainee/print_loc/' . $item->class_id . '/' . $item->user_id . '">LOC</a><br/>';
                                    //////added by shubhranshu for wablab and everest TCS for all courses
                                    $tenant_id = $trainee[userdetails]['tenant_id'];

                                    if (($tenant_id == 'T20') || ($tenant_id == 'T17')) {
                                        $linkStr .= '<a href="' . base_url() . 'trainee/print_wsq_loc/' . $item->course_id . '/' . $item->class_id . '/' . $item->user_id . '">TCS</a><br/>';
                                    } else {
                                        if (in_array($item->course_id, $wsq_courses_array) && in_array($tenant_id, $tenant_array)) {
                                            $linkStr .= '<a href="' . base_url() . 'trainee/print_wsq_loc/' . $item->course_id . '/' . $item->class_id . '/' . $item->user_id . '">TCS</a><br/>';
                                        }
                                    }
                                }
                            } else {
                                if ($item->payment_status != 'PYNOTREQD' &&
                                        $this->data['user']->role_id == 'ADMN') {
                                    $linkStr = '<a href="javascript:;" class="get_update" data-class="' . $item->class_id . '" data-user="' . $item->user_id . '">Update TG# </a>&nbsp;&nbsp;&nbsp;';
                                }


                                if ($class_start_datetime_str <= $cur_date && $class_end_datetime >= $cur_date) {


                                    $classStatus = 'IN_PROG';
                                } elseif ($class_start_datetime_str > $cur_date) {


                                    $classStatus = 'YET_TO_START';
                                }
                            }


                            /*   added $item->attn_stats by shubhranshu due to receipt issue for absent trainee      */
                            //    $linkStr .= get_links($item->enrolment_mode, $item->payment_status, $item->invoice_id, $item->user_id, $item->pymnt_due_id, $item->class_id, $this, $trainee['userdetails']['account_status'], $classStatus,$item->company_id,$assmnt_links);
                            $linkStr .= get_links($item->attn_stats, $item->enrolment_mode, $item->payment_status, $item->invoice_id, $item->user_id, $item->pymnt_due_id, $item->class_id, $this, $trainee['userdetails']['account_status'], $classStatus, $item->company_id, $assmnt_links);
                            /* -------------------------------------------------------------------------- */
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


        <a href="<?php echo site_url(); ?>trainee">
            <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-step-backward"></span>&nbsp;Back</button>
        </a>
    </div>
</div>
<div style="clear:both;"></div><br>
<ul class="pagination pagination_style">
    <?php echo $pagination; ?>
</ul>





<?php echo $reacti_popup; ?>


<div class="modal" id="ex1" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Heading Goes Here...</h2>
    Detail Goes here.  <br>

    <div class="popup_cancel">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a></div></p>
</div>

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

    <center> <span id="skm" style="display:none"></span></center>
    <span id="tbl" style="display:none">

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


                        $COMYTCOM_ABS = array(
                            'name' => 'COMYTCOM',
                            'id' => 'COMYTCOM_ABS',
                            'value' => 'ABS',
                        );
                        $COMYTCOM_2NYC = array(
                            'name' => 'COMYTCOM',
                            'id' => 'COMYTCOM_2NYC',
                            'value' => '2NYC',
                        );
                        $COMYTCOM_ATTRITION = array(
                            'name' => 'COMYTCOM',
                            'id' => 'COMYTCOM_ATTRITION',
                            'value' => 'ATR',
                        );
                        ?>              
                        <?php echo form_radio($COMYTCOM_C); ?> Competent <br/>
                        <?php echo form_radio($COMYTCOM_NYC); ?> Not Yet Competent <br/>


                        <?php echo form_radio($COMYTCOM_EX); ?> Exempted<br/>                    
                        <?php echo form_radio($COMYTCOM_ABS); ?> Absent<br/>
                        <?php
                        if (TENANT_ID == 'T02') {/////below code was added by shubhranshu for xp for attrition option start-----
                            echo form_radio($COMYTCOM_ATTRITION);
                            echo "Attrition <br/>";
                        }
                        ?> 
                        <?php echo form_radio($COMYTCOM_2NYC); ?> Twice Not Competent


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
                    <button class="btn btn-primary" id="lock_att" type="submit">Save</button>&nbsp;&nbsp;
                    <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a>
                </div>
            </div>
        </div>
        </p>
        <div class="attendance_lock" style="display: none; height: 50px;text-align: center">                    
            <span style="color:red;"> <i>Can`t update the trainer feedback because class attendance is locked. To change it please contact to Administrator.</i>
            </span>
            <br/>                       
        </div>

    </span>
</div>
<?php
echo form_close();
?>
<?php if (empty($trainee['discountdetails'])) { ?>
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
                        <th width="60%">Course</th>
                        <th width="20%">Discount %</th>
                        <th width="20%">Discount Amt. (SGD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($trainee['discountdetails'] as $k => $row):
                        echo "<tr>"
                        . "<td>" . $row['crse_name'] . "</td>"
                        . "<td>" . number_format($row['discount_percent'], 2, '.', '') . "%</td>"
                        . "<td>$ " . number_format($row['discount_amount'], 2, '.', '') . "</td>"
                        . "</tr>";
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

<div class="modal_0221" id="ex145" style="display:none; height: 500px; overflow-y: auto;">
    <p>
        <h2 class="panel_heading_style">Trainee Module History</h2>
       
           <table class="table table-striped">
            <thead>
                
              <?php // print_r($training_module_history);
              if (!empty($training_module_history)) { ?>
            <tr>
                <th>Module Name</th>
                <th>Start date</th>
                <th>End date</th>
            </tr>
              <?php }else{
                  echo "<tr class='danger'><td colspan='8' style='text-align:center;color:red'><label>No Module historical training data available.</label></td></tr>";
              }?>
            </thead>
            <tbody>
                <?php foreach ($training_module_history as $training_module){?>
                <tr>
                    <td><label class="label_font"><?php echo $training_module->module_name; ?></label></td>
                    <td><label class="label_font"><?php echo $training_module->start_date; ?></label></td>
                    <td><label class="label_font"><?php echo $training_module->end_date; ?></label></td>
                    
                </tr>
                <?php } ?>
            </tbody>
            
        </table>
    </p>
 
</div>

<!--  Dialog for selecting the assessment report -->
<?php
$form_attributes = array('name' => 'select_assessment_rep_form', 'id' => 'select_assessment_rep_form', "onsubmit" => "return(false);");
echo form_open("", $form_attributes);
?>
<div class="modal1_077" id="assessment_form" style="display:none;">
    <p>

    <h2 class="panel_heading_style">View Assessment Forms</h2>   

    <strong>Click on the assessment template to view</strong>         

    <br><br>  
    <span id="assessment_links">
        <!--?php
                
        if(count($forms[0]) == 0) {
            
            echo "<br><strong><span class=\"required\">There are no assessment records for this traineee</span></strong><br><br><br>";
        }
        else {
        
            // Add the hyper links to the templates        
            $count = 1;
            foreach($forms as $link) {

                echo  $count . '.&nbsp;<a href="' . base_url() . 'uploads/files/filled_templates/template_' . 
                                    $link[0] . '_' . $user_id . '_1.png" target="_blank">' . 
                                    $link[2] . '</a><br><br>';
                $count += 1;
            }
        }
        
        ?-->
    </span>
    <br>


    <div class="popup_cancel9">
        <div rel="modal:close">
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a>
        </div>
    </div>
</div>
<?php
echo form_close();
?>

<!--  Dialog for selecting the assessment report END -->


<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/classtraineelist.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/view_trainee.js?0.000000001"></script>
<?php

// added by shubhranshu $attn_status due to receipt issue for viewtrainee absent trainee
function get_links($attn_status, $enrolment_mode, $payment_status, $invoice_id, $user_id, $pymnt_due_id, $class_id, $view_trainee_data, $trainee_Status, $classStatus, $company_id, $assmnt_links) {


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
                if ($attn_status == 1) {
                    $tempLinkStr .= '<a href="' . base_url() . 'class_trainee/export_payment_receipt/' . $pymnt_due_id . '">Receipt</a>';
                } else {
                    $tempLinkStr .= '<div><i>Receipt Not Available(Trainee is absent)</i></div>';
                }
                /*  -----------------------------------------------------------------    */

                //$tempLinkStr .= '<a href="' . base_url() . 'class_trainee/export_payment_receipt/' . $pymnt_due_id . '">Receipt</a>';
            } elseif ($enrolment_mode == 'SELF' && $payment_status == 'NOTPAID') {


                if ($trainee_Status == 'ACTIVE') {
                    $tempLinkStr = '<a href="' . base_url() . 'accounting/update_payment?invoice_id=' . $invoice_id . '&enrol_mode=individual" style="color:red" >Not Paid</a> &nbsp;&nbsp;';
                    if ($classStatus != 'COMPLTD') {
                        $tempLinkStr .= '<a href="' . base_url() . 'class_trainee/booking_acknowledge_pdf/' . $user_id . '/' . $class_id . '">Booking ACK.</a>';
                    }
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
                $tempLinkStr = '<a href="' . base_url() . 'accounting/update_payment?invoice_id=' . $invoice_id . '&enrol_mode=company&company_id=' . $company_id . '" style="color:red;">' . $label . '</a>&nbsp;&nbsp;';
                if ($trainee_Status == 'ACTIVE' && $classStatus != 'COMPLTD')
                    $tempLinkStr .='<a href="' . base_url() . 'class_trainee/booking_acknowledge_pdf/' . $user_id . '/' . $class_id . '">Booking ACK.</a>';
            }
        }
        //Added by Sutapa for Assessment Link in ADMIN role ONLY
        if ($classStatus == 'COMPLTD') {

            $assmt_data = $assmnt_links[$class_id];

            $link_data = "";
            foreach ($assmt_data as $link) {

                if (strlen($link_data) != 0)
                    $link_data .="~";

                if (strlen(trim($link[0])) > 0) {

                    $link_data .= base_url() . 'uploads/files/filled_templates/template_' .
                            $link[0] . '_' . $user_id . '.pdf' . "^" .
                            $link[2];
                }
            }

            $tempLinkStr .= '<a href="#" onClick="launch_form_dialog(\'' . $link_data . '\');return false;"> Assessment</a> &nbsp;&nbsp;';
        }
    }
    return $tempLinkStr;
}
?>

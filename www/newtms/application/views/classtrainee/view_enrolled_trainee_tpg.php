<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>

<div class="col-md-10 right-minheight">
    <?php echo validation_errors('<div class="error1">', '</div>'); ?> 
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> TPG Trainee Enrolled Details</h2>
    <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Enrollment Details</h2>
    <div class="table-responsive">
        <?php
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        ?>  
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td colspan="4">                            
                        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Trainee Details</h2>
                    </td>                           
                </tr>
                <tr class="change_span" style="">
                    <td>                    
                        <b>Enrollment Reference No. :<span class="required">*</span></b> 
                    </td>   
                    <td>
                        <input type="text" name="change_taxcode_autocomplete" id="nric" style="" value='<?php echo $referenceNumber; ?>' disabled="disabled"/>
                        <span id="nric_err"></span>
                    </td> 

                    <td>
                        <b>Status:<span class="required">*</span></b> 
                    </td>
                    <td> 
                        <input type="text" name="fullname" id="fullname" style="" value='<?php echo $status; ?>' disabled="disabled"/>
                        <span id="fullname_err"></span>
                    </td>
                </tr>

                <tr class="change_span" style="">
                    <td>                    
                        <b>NRIC/FIN No.:<span class="required">*</span></b> 
                    </td>   
                    <td>
                        <input type="text" name="change_taxcode_autocomplete" id="nric" style="" value='<?php echo $traineeId; ?>' disabled="disabled"/>
                        <span id="nric_err"></span>
                    </td> 

                    <td>
                        <b>Full Name.:<span class="required">*</span></b> 
                    </td>
                    <td> 
                        <input type="text" name="fullname" id="fullname" style="" value='<?php echo $traineeFullName; ?>' disabled="disabled"/>
                        <span id="fullname_err"></span>
                    </td>
                </tr>

                <tr class="change_span" style="">
                    <td>                    
                        <b>Trainee DOB.:<span class="required">*</span></b> 
                    </td>   
                    <td>
                        <input type="text" name="change_taxcode_autocomplete" id="dob" style="" value='<?php echo $traineeDateOfBirth; ?>' disabled="disabled"/>
                        <span id="dob_err"></span>
                    </td> 

                    <td>
                        <b>Trainee Cont. No.:<span class="required">*</span></b> 
                    </td>
                    <td> 
                        <input type="text" name="fullname" id="contactno" style="" value='<?php echo $traineeContactNumber; ?>' disabled="disabled"/>
                        <span id="contactno_err"></span>
                    </td>
                </tr>

                <tr class="change_span" style="">
                    <td>                    
                        <b>Trainee Email.:<span class="required">*</span></b> 
                    </td>   
                    <td>
                        <input type="text" name="change_taxcode_autocomplete" id="temail" style="" value='<?php echo $traineeEmailAddress; ?>' disabled="disabled"/>
                        <span id="temail_err"></span>
                    </td> 

                    <td>
                        <b>Trainee Type.:<span class="required">*</span></b> 
                    </td>
                    <td> 
                        <input type="text" name="fullname" id="ttype" style="" value='<?php echo $traineeIdType; ?>' disabled="disabled"/>
                        <span id="ttype_err"></span>
                    </td>
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Sponsorship Type:<span class="required">*</span></td>
                    <td>
                        <input type="text" name="tpuen" id="tpuen" style="" value='<?php echo $traineeSponsorshipType; ?>' disabled="disabled"/>
                        <span id="tpuen_err"></span>
                    </td>
                </tr>                
                <tr>
                    <td colspan="4">                            
                        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Training Partner Details</h2>
                    </td>                           
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Training Partner Code:<span class="required">*</span></td>
                    <td>
                        <input type="text" name="tpcode" id="tpcode" style="" value='<?php echo $trainingPartnerCode; ?>' disabled="disabled"/>
                        <span id="tpcode_err"></span>
                    </td>
                    <td class="td_heading" width="15%">Training Partner UEN:<span class="required">*</span></td>
                    <td>
                        <input type="text" name="tpuen" id="tpuen" style="" value='<?php echo $trainingPartnerUEN; ?>' disabled="disabled"/>
                        <span id="tpuen_err"></span>
                    </td>
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Training Partner Name:<span class="required">*</span></td>
                    <td>
                        <input type="text" name="tpuen" id="tpuen" style="" value='<?php echo $trainingPartnerName; ?>' disabled="disabled"/>
                        <span id="tpuen_err"></span>
                    </td>
                </tr>                
                <tr>
                    <td colspan="4">                            
                        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Course Details</h2>
                    </td>                           
                </tr>
                <tr class="new_span">
                    <td class="td_heading">Course Reference No.:<span class="required">*</span></td>
                    <td>                       
                        <input type="text" name="course" id="crefno" style="" value='<?php echo $courseReferenceNumber; ?>' disabled="disabled"/>
                        <span id="crefno_err"></span>
                    </td>
                    <td class="td_heading" width="15%">Course RunID:<span class="required">*</span></td>
                    <td>
                        <input type="text" name="courserunid" id="crunid" style="" value='<?php echo $courseRunId; ?>' disabled="disabled"/>
                        <span id="crunid_err"></span>
                    </td>
                </tr>                
                <tr class="new_span">
                    <td class="td_heading" width="15%">Course Title:<span class="required">*</span></td>
                    <td colspan="3">
                        <input type="text" name="discount_amount" id="discount_amount" value='<?php echo $courseTitle; ?>' disabled="disabled"/>
                        <span id="discount_amount_err"></span>
                    </td>
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Start Date:<span class="required">*</span></td>
                    <td>
                        <input type="text" name="collection_status" id="collection_status" value='<?php echo $courseStartDate; ?>' disabled="disabled"/>
                        <span id="discount_amount_err"></span>
                    </td>
                    <td class="td_heading" width="15%">End Date:<span class="required">*</span></td>
                    <td>
                        <input type="text" name="enrolment_date" id="enrolment_date" value='<?php echo $courseEndDate; ?>' disabled="disabled"/>
                        <span id="enrolment_date_err"></span>
                    </td>
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Discount Amount:<span class="required">*</span></td>
                    <td colspan="3">
                        <input type="text" name="collection_status" id="collection_status" value='<?php echo $feeDiscountAmount; ?>' disabled="disabled"/>

                        <span id="discount_amount_err"></span>
                    </td>
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Collection Status:<span class="required">*</span></td>
                    <td colspan="3">
                        <input type="text" name="enrolment_date" id="enrolment_date" value='<?php echo $feeCollectionStatus; ?>' disabled="disabled"/>
                        <span id="enrolment_date_err"></span>
                    </td>
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Enrollment Date:<span class="required">*</span></td>
                    <td colspan="3">
                        <input type="date" name="enrolment_date" id="enrolment_date" value='<?php echo $traineeEnrolmentDate; ?>' disabled="disabled"/>
                        <span id="enrolment_date_err"></span>
                    </td>
                </tr>
                <?php if($traineeSponsorshipType != "Individual") {?>
                <tr>
                    <td colspan="4">
                        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Employer Details</h2>
                    </td>
                </tr>
                <tr class="change_span" style="">
                    <td>                    
                        <b>Employer Name.:<span class="required">*</span></b> 
                    </td>   
                    <td>
                        <input type="text" name="tenant_name" id="ename" style="" value='<?php echo $employerName; ?>' disabled="disabled"/>
                        <span id="ename_err"></span>
                    </td> 

                    <td>
                        <b>Employer UEN.:<span class="required">*</span></b> 
                    </td>
                    <td> 
                        <input type="text" name="tenant_country" id="Ecountry" style="" value='<?php echo $employerUEN; ?>' disabled="disabled"/>
                        <span id="Ecountry_err"></span>
                    </td>
                </tr>
                <tr class="change_span" style="">
                    <td>                    
                        <b>Contact Name.:<span class="required">*</span></b> 
                    </td>   
                    <td>
                        <input type="text" name="tenant_email_id" id="tenant_email_id" style="" value='<?php echo $employerContactFullName; ?>' disabled="disabled"/>
                        <span id="tenant_email_id_err"></span>
                    </td> 

                    <td>
                        <b>Contact No.:<span class="required">*</span></b> 
                    </td>
                    <td> 
                        <input type="text" name="tenant_contact_num" id="tenant_contact_num" style="" value='<?php echo $employerContactNumber; ?>' disabled="disabled"/>
                        <span id="tenant_contact_num_err"></span>
                    </td>
                </tr>
                <tr class="change_span" style="">
                    <td>                    
                        <b>Employer Email.:<span class="required">*</span></b> 
                    </td>   
                    <td>
                        <input type="text" name="tenant_email_id" id="tenant_email_id" style="" value='<?php echo $employerEmailAddress; ?>' disabled="disabled"/>
                        <span id="tenant_email_id_err"></span>
                    </td>
                </tr>
                <?php } ?>
                <tr class="new_span">
                    <td colspan="4" class="no-bg">
                        <div class="push_right">
                            <a href="<?php echo base_url() . 'class_trainee'; ?>" class="small_text1"><span class="label label-default black-btn">Back</span></a>
                        </div>
                    </td>
                </tr>                
            </tbody>
        </table>        
    </div>
</div>
<!-- end abdulla -->
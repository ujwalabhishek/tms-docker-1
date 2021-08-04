<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/multiselectdropdown.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/multiselectdropdown.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/multidropdown.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.ui.timepicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery.ui.timepicker.css" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
    $role_id = "<?php echo $this->session->userdata('userDetails')->role_id; ?>";//added by shubhranshu
    $privilage = "<?php echo $privilage; ?>"; //added by shubhranshu
</script>
<div class="col-md-10 right-minheight">
    <?php echo validation_errors('<div class="error1">', '</div>'); ?> 
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> TPG Trainee Enrollment</h2>
    <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Enrollment Details</h2>
    <div class="table-responsive">
        <?php
        $atr = 'id="tpg_form" name="tpg_form" method="post" onkeypress="return event.keyCode != 13"';
        echo form_open("tp_gateway/response_trainee_enrolment_data_tpg", $atr);
        $tenant_id = $this->session->userdata('userDetails')->tenant_id; //added by shubhranshu
        $key = TPG_KEY."_".$tenant_id;
        $tpg_key = print $key;
        ?>  
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td colspan="4">                        
                        <textarea type="hidden" style="display:none;" id='tpg_data' name="tpg_data" ></textarea>
                        <input type="hidden" name="courseId" value="<?php echo $courseId; ?>" id="courseId">
                        <input type="hidden" name="classId" value="<?php echo $classId; ?>" id="classId">
                        <input type="hidden" name="userId" value="<?php echo $userId; ?>" id="userId">
                        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Trainee Details</h2>
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
                <tr>
                    <td colspan="4">                            
                        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Course Details</h2>
                    </td>                           
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Course Reference No.:<span class="required">*</span></td>
                    <td>                       
                        <input type="text" name="course" id="crefno" style="" value='<?php echo $courseReferenceNumber; ?>' disabled="disabled"/>
                        <span id="crefno_err"></span>
                    </td>               
                    <td class="td_heading" width="15%">Course RunID:<span class="required">*</span></td>
                    <td colspan="3">
                        <input type="text" name="courserunid" id="crunid" style="" value='<?php echo $courseRunId; ?>' disabled="disabled"/>
                        <span id="crunid_err"></span>
                    </td>
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Discount Amount:<span class="required">*</span></td>
                    <td>
                        <input type="number" name="discount_amount" id="discount_amount" value='<?php echo $feeDiscountAmount; ?>' disabled="disabled"/>
                        <span id="discount_amount_err"></span>
                    </td>                
                    <td class="td_heading" width="15%">Collection Status:<span class="required">*</span></td>
                    <td>
                        <input type="text" name="collection_status" id="collection_status" value='<?php echo $feeCollectionStatus; ?>' disabled="disabled"/>
                        <span id="discount_amount_err"></span>
                    </td>
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Enrollment Date:<span class="required">*</span></td>
                    <td colspan="3">
                        <input type="date" name="enrolment_date" id="enrolment_date" value='<?php echo $traineeEnrolmentDate; ?>' disabled="disabled"/>
                        <span id="enrolment_date_err"></span>
                    </td>
                </tr>
                <?php if($traineeSponsorshipType != "INDIVIDUAL") {?>
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
                        <input type="text" name="tenant_name" id="ename" style="" value='<?php echo $emploerFullName; ?>' disabled="disabled"/>
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
                        <b>Employer email.:<span class="required">*</span></b> 
                    </td>   
                    <td>
                        <input type="text" name="tenant_email_id" id="tenant_email_id" style="" value='<?php echo $employerEmailAddress; ?>' disabled="disabled"/>
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
                <?php } ?>
                <tr class="new_span">
                    <td colspan="4" class="no-bg">
                        <div class="push_right">
                            <button type="button" id='enrol_now_tpg' class="search_button btn btn-xs btn-primary no-mar">
                                <span class="glyphicon glyphicon-retweet"></span>
                                Enroll Now
                            </button>
                        </div>
                    </td>
                </tr>
                <tr class="change_span change_span1 remove_company_span move_company_span" style="display:none">
                    <td colspan="4" class="no-bg">
                        <div class="push_right">
                            <button id="search_enrolment" type="button" class="btn btn-xs btn-primary no-mar">
                                <span class="glyphicon glyphicon-retweet"></span>
                                Search
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
</div>
<!-- end abdulla -->
<script>
    $(document).ready(function () {
        function encrypt() {
            var tpgraw = '<?php echo $tpg_json_data; ?>';
            var key = '<?php echo $tpg_key; ?>';
            alert(key);
            var cipher = CryptoJS.AES.encrypt(
                    tpgraw,
                    CryptoJS.enc.Base64.parse(key), {
                iv: CryptoJS.enc.Utf8.parse('SSGAPIInitVector'),
                mode: CryptoJS.mode.CBC,
                keySize: 256 / 32,
                padding: CryptoJS.pad.Pkcs7
            });
            var encrypted = CryptoJS.enc.Base64.stringify(cipher.ciphertext);
            $('#tpg_data').val(encrypted);

        }

        $('#enrol_now_tpg').click(function () {
            encrypt();
            $('#tpg_form').submit();
        });
    });
</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/multiselectdropdown.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/multiselectdropdown.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/multidropdown.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.ui.timepicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery.ui.timepicker.css" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
    $role_id = "<?php echo $this->session->userdata('userDetails')->role_id;?>";//added by shubhranshu    
    $privilage = "<?php echo $privilage;?>"; //added by shubhranshu
</script>

<div class="col-md-10 right-minheight">
    <?php echo validation_errors('<div class="error1">', '</div>'); ?> 
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> TPG Trainee Enrollment</h2>
    <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Enrollment Details</h2>
    <div class="table-responsive">
        <?php
        $atr = 'id="tpg_form" name="tpg_form" method="post" onkeypress="return event.keyCode != 13"';
        echo form_open("tp_gateway/proceed_enrol_toTpg", $atr);
         $tenant_id = $this->session->userdata('userDetails')->tenant_id;//added by shubhranshu
         $tpg_key = $this->config->item(TPG_KEY_.$tenant_id);
        ?>  
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td colspan="4">
                        
                        <textarea type="hidden" style="display:none;" id='tpg_data' name="tpg_data" ></textarea>
                        <input type="hidden" name="privilage" value="<?php echo $privilage;?>"><!--added  by shubhranshu-->
                        <input type="hidden" id='restriction_flag' name="restriction_flag" value="">
                        <?php
                        $data = array(
                            'id' => 'enrollment_mode_new',
                            'class' => 'enrollment_type',
                            'name' => 'enrollment_mode',
                            'value' => 'new',
                            'checked' => TRUE
                        );
                        echo form_radio($data);
                        ?>&nbsp;&nbsp;<label style="color:blue;font-weight:bold">New enrollment(s)</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      
                           
                    
                    </td>                                       
                </tr>

                
                <tr class="change_span" style="">
                    <td>                    
                        <b>NRIC/FIN No.:<span class="required">*</span></b> 
                    </td>   
                    <td>
                        <input type="text" name="change_taxcode_autocomplete" id="nric" style="" value='<?php echo $trainee['tax_code']; ?>' disabled="disabled"/>
                         <span id="nric_err"></span>
                    </td> 

                    <td>
                        <b>Full Name.:<span class="required">*</span></b> 
                     </td>
                    <td> 
                        <input type="text" name="fullname" id="fullname" style="" value='<?php echo $trainee['first_name']; ?>' disabled="disabled"/>
                        <span id="fullname_err"></span>
                    </td>
                </tr>
                
                <tr class="change_span" style="">
                    <td>                    
                        <b>Trainee DOB.:<span class="required">*</span></b> 
                    </td>   
                    <td>
                        <input type="text" name="change_taxcode_autocomplete" id="dob" style="" value='<?php echo $trainee['dob']; ?>' disabled="disabled"/>
                         <span id="dob_err"></span>
                    </td> 

                    <td>
                        <b>Trainee Cont. No.:<span class="required">*</span></b> 
                     </td>
                    <td> 
                        <input type="text" name="fullname" id="contactno" style="" value='<?php echo $trainee['contact_number']; ?>' disabled="disabled"/>
                         <span id="contactno_err"></span>
                    </td>
                </tr>
       
                 <tr class="change_span" style="">
                    <td>                    
                        <b>Trainee Email.:<span class="required">*</span></b> 
                    </td>   
                    <td>
                        <input type="text" name="change_taxcode_autocomplete" id="temail" style="" value='<?php echo $trainee['registered_email_id']; ?>' disabled="disabled"/>
                         <span id="temail_err"></span>
                    </td> 

                    <td>
                        <b>Trainee Type.:<span class="required">*</span></b> 
                     </td>
                    <td> 
                        <input type="text" name="fullname" id="ttype" style="" value='NRIC' disabled="disabled"/>
                        <span id="ttype_err"></span>
                    </td>
                </tr>
                
               
                
              
                 <tr class="new_span">
                    <td class="td_heading" width="15%">Trainning Partner Code:<span class="required">*</span></td>
                    <td>
                        <input type="text" name="tpcode" id="tpcode" style="" value='<?php echo $_POST['tpcode']; ?>' disabled="disabled"/>
                       
                    <span id="tpcode_err"></span>
                    </td>
                    <td class="td_heading" width="15%">Trainning Partner UEN:<span class="required">*</span></td>
                      <td>
                          <input type="text" name="tpuen" id="tpuen" style="" value='<?php echo $_POST['tpuen']; ?>' disabled="disabled"/>
                          
                      <span id="tpuen_err"></span>
                      </td> 
                       
                        
                    
                </tr>
                <tr class="new_span">
                    <td class="td_heading">Select Course ReferenceNo:<span class="required">*</span></td>
                    <td colspan="3">
                        <?php
                        //$options = array();
                        //$options[''] = 'Select';
                       // $js = 'id="course" ';
                        //echo form_dropdown('course', $options, $this->input->post('course'), $js);
                        ?>
                         <input type="text" name="course" id="crefno" style="" value='<?php echo $_POST['course']; ?>' disabled="disabled"/>
                        <span id="crefno_err"></span>
                    </td>
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Select Course RunID:<span class="required">*</span></td>
                    <td colspan="3">
                        <?php
                        //$options = array();
                        //$options[''] = 'Select';
                        //$js = 'id="class" ';
                        //echo form_dropdown('class', $options, $this->input->post('class'), $js);
                        ?>
                        <input type="text" name="courserunid" id="crunid" style="" value='<?php echo $_POST['courserunid']; ?>' disabled="disabled"/>
                       
                        <span id="crunid_err"></span>
                    </td>
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Discount Amount:<span class="required">*</span></td>
                    <td colspan="3">
                       
                       <input type="number" name="discount_amount" id="discount_amount" value='<?php echo $_POST['discount_amount']?>' disabled="disabled"/>
                        <span id="discount_amount_err"></span>
                    </td>
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Collection Status:<span class="required">*</span></td>
                    <td colspan="3">
                       <input type="text" name="collection_status" id="collection_status" value='<?php echo $_POST['collection_status'];?>' disabled="disabled"/>
                     
                        <span id="discount_amount_err"></span>
                    </td>
                </tr>
                 <tr class="new_span">
                    <td class="td_heading" width="15%">Enrollment Date:<span class="required">*</span></td>
                    <td colspan="3">
                       
                      <input type="date" name="enrolment_date" id="enrolment_date" value='<?php echo $_POST['enrolment_date']?>' disabled="disabled"/>
                        <span id="enrolment_date_err"></span>
                    </td>
                </tr>
                
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
                        <input type="text" name="tenant_name" id="ename" style="" value='<?php echo $trainee['tenant_name']; ?>' disabled="disabled"/>
                         <span id="ename_err"></span>
                    </td> 

                    <td>
                        <b>Employer Address.:<span class="required">*</span></b> 
                     </td>
                    <td> 
                        <input type="text" name="tenant_address" id="eaddress" style="" value='<?php echo $trainee['tenant_address']; ?>' disabled="disabled"/>
                        <span id="eaddress_err"></span>
                    </td>
                </tr>
                <tr class="change_span" style="">
                    <td>                    
                        <b>Contact Person.:<span class="required">*</span></b> 
                    </td>   
                    <td>
                        <input type="text" name="contact_name" id="contact_name" style="" value='<?php echo $trainee['contact_name']; ?>' disabled="disabled"/>
                         <span id="contact_name_err"></span>
                    </td> 

                    <td>
                        <b>Cont. No.:<span class="required">*</span></b> 
                     </td>
                    <td> 
                        <input type="text" name="tenant_contact_num" id="tenant_contact_num" style="" value='<?php echo $trainee['tenant_contact_num']; ?>' disabled="disabled"/>
                        <span id="tenant_contact_num_err"></span>
                    </td>
                </tr>
                <tr class="change_span" style="">
                    <td>                    
                        <b>Employer email.:<span class="required">*</span></b> 
                    </td>   
                    <td>
                        <input type="text" name="tenant_email_id" id="tenant_email_id" style="" value='<?php echo $trainee['tenant_email_id']; ?>' disabled="disabled"/>
                         <span id="tenant_email_id_err"></span>
                    </td> 

                    <td>
                         <b>Employer Country.:<span class="required">*</span></b> 
                     </td>
                    <td> 
                         <input type="text" name="tenant_country" id="Ecountry" style="" value='<?php echo $trainee['tenant_country']; ?>' disabled="disabled"/>
                         <span id="Ecountry_err"></span>
                    </td>
                </tr>
                
                
                
                
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
    <div class="no_invoice_div" style="display: none; background-color: #f4fcff; height: 50px;text-align: center">            
        <span style="color:red;font-weight: bold">No invoice found for the company '<label id="company_name_2"></label>'. Would you want to generate a new Invoice? </span>                       
        <br/>
        <button type="button" class="btn btn-xs btn-primary no-mar yes_button">
            <span class="glyphicon glyphicon-retweet"></span>
            Yes
        </button>
        &nbsp;&nbsp;<button type="button" class="btn btn-xs btn-primary no-mar no_button">
            <span class="glyphicon glyphicon-retweet"></span>
            No
        </button>                
    </div> 
    
    <div class="attendance_lock" style="display: none; background-color: #f4fcff; height: 50px;text-align: center">                    
        <span style="color:red;font-weight: bold"> <i>Can`t change the trainee enrollment type because class attendance is locked. To change it please contact to Administrator.</i>
        </span>
        <br/>                       
    </div>
    <div class="cannot_change_div" style="display: none; background-color: #f4fcff; height: 50px;text-align: center">                    
        <span style="color:red;font-weight: bold">We cannot change the enrollment type as payment has been received for the invoice from this company for the class.</span>
        <br/>                       
    </div>
    <div class="not_company_user" style="display: none; background-color: #f4fcff; height: 50px;text-align: center">                    
        <span style="color:red;font-weight: bold">This trainee has not been assigned to any company. Please assign the trainee to a company before carrying out this operation.</span>
       
        <br/>                       
    </div>
    <div class="amountdetails_div" style="display: none;">
        <h2 class="sub_panel_heading_style">
            <span class="glyphicon glyphicon-circle-arrow-right"></span> Existing Invoice Details for '<label id="company_name_1"></label>'            
        </h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td  class="td_heading">
                            Invoice #: &nbsp;&nbsp;&nbsp;&nbsp;
                            <label class="label_font"><span class="invoice_no"></span></label>
                        </td>                                                
                        <td class="td_heading">
                            Invoice Date:&nbsp;&nbsp;&nbsp;&nbsp;
                            <label class="label_font"><span class="invoice_date"></span></label>
                        </td>                        
                        <td  class="td_heading" style="color:red">Total Invoice Amount:&nbsp;&nbsp;&nbsp;&nbsp;
                            <label class="label_font">$<span class="pay_total_invoice_amount"></span> SGD</label>
                        </td>                                                                       
                    </tr>
                    <tr>
                        <td  class="td_heading">Total Unit Fees:
                            &nbsp;&nbsp;&nbsp;&nbsp;<label class="label_font">$<span class="pay_class_fees"></span> SGD</label>
                        </td>  
                        <td class="td_heading">
                            <span class="pay_discount_label">
                            </span> Total Discount @ <span class="pay_discount_rate"></span>%:&nbsp;&nbsp;&nbsp;&nbsp;
                            <label class="label_font">$<span class="pay_discount_amount"></span> SGD</label>
                        </td>
                        <td class="td_heading">Total Subsidy Amount:&nbsp;&nbsp;&nbsp;&nbsp;
                            <label class="label_font">$<span class="pay_subsidy_amount"></span> SGD</label>
                        </td>
                    </tr>
                    <tr>                        
                        <td class="td_heading" colspan="3">Total GST @ <span class="pay_gst_rate">
                            </span>% (<span class="pay_gst_label"></span>):
                            &nbsp;&nbsp;&nbsp;&nbsp;$<label class="label_font"><span class="pay_total_gst"></span> SGD</label>
                        </td>
                    </tr>                                        
                </tbody>
            </table>
        </div>
    </div>
    <br/>
    <?php
    $atr = 'id="confirm_enrolment_change_form" name="confirm_enrolment_change_form" method="post" onsubmit="return(add_remove_invoice_validate());"';
    echo form_open("class_trainee/enrolment_type_change/mergeInvoice", $atr);
    ?>
    <div class="trainee_div" style="display: none;">
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/personal_details.png"/>
            List of employees already enrolled from the company in '<span id="couse_class_name"></span>'</h2> 
        <div class="trainee_alert"></div>
        <div class="table-responsive d-table-scroll add_company_invoice_div" style="width:100%;display: none">
            <table class="table table-striped add_company_invoice_table">
                <thead>                    
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <br/>
        <h2 class="sub_panel_heading_style add_company_invoice_heading" style="display: none"><img src="<?php echo base_url(); ?>/assets/images/personal_details.png"/>
            List of employees available for enrollment(s)</h2> 
        <span class='check_box_alert error' style="dispaly:none">[please select atleast one trainee]. </span>
        <div class="table-responsive d-table-scroll" style="width:70%;">
            <table class="table table-striped trainee_table">
                <thead>
                    <tr>                        
                        <th class="th_header" width="8%"></th>
                        <th class="th_header" width="40%">NRIC/FIN No.</th>
                        <th class="th_header" width="52%">Trainee Name</th>                        
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <?php
        $data = array(
            'id' => 'individual_user_id',
            'name' => 'individual_user_id',
            'type' => 'hidden'
        );
       
        echo form_input($data);
        $data = array(
            'id' => 'pymnt_due_id',
            'name' => 'pymnt_due_id',
            'type' => 'hidden'
        );
        
        echo form_input($data);
        $data = array(
            'id' => 'subsidy_amount',
            'name' => 'subsidy_amount',
            'type' => 'hidden'
        );
        echo form_input($data);
        $data = array(
            'id' => 'unit_fees',
            'name' => 'unit_fees',
            'type' => 'hidden'
        );
        echo form_input($data);
        $data = array(
            'id' => 'comp_pymnt_due_id',
            'name' => 'comp_pymnt_due_id',
            'type' => 'hidden'
        );
        echo form_input($data);
       
       
        $data = array(
            'id' => 'comp_invoice_id',
            'name' => 'comp_invoice_id',
            'type' => 'hidden'
        );
       
        echo form_input($data);
        
        $data = array(
            'id' => 'company_id',
            'class'=>'company_id',
            'name' => 'company_id',
            'type' => 'hidden'
        );
        
        echo form_input($data);
         
        $data = array(
            'id' => 'comp_gst_rule',
            'name' => 'comp_gst_rule',
            'type' => 'hidden'
        );
        echo form_input($data);
        $data = array(
            'id' => 'comp_gst_rate',
            'name' => 'comp_gst_rate',
            'type' => 'hidden'
        );
        echo form_input($data);
        
        $data = array(
            'id' => 'class_start_datetime',
            'name' => 'class_start_datetime',
            'type' => 'hidden'
        );
        echo form_input($data);
        
        $data = array(
            'id' => 'course_id',
            'name' => 'course_id',
            'class' =>'course_id',
            'type' => 'hidden'
        );
         
        echo form_input($data);
        $data = array(
            'id' => 'class_id',
            'name' => 'class_id',
            'class' =>'class_id',
            'type' => 'hidden'
        );
       
        echo form_input($data);
        echo "<br >";
       $data = array(
            'id' => 'to_comp_pymnt_due_id',
            'name' => 'to_comp_pymnt_due_id',
            'type' => 'hidden'
        );
        
        echo form_input($data);
        $data = array(
            'id' => 'to_comp_invoice_id',
            'name' => 'to_comp_invoice_id',
            'type' => 'hidden'
        );
       
        echo form_input($data);
        $data = array(
            'id' => 'to_company_id',
            'name' => 'to_company_id',
            'type' => 'hidden'
        );
        echo form_input($data);
        $data = array(
            'id' => 'to_course_id',
            'name' => 'to_course_id',
            'type' => 'hidden'
        );
        
        echo form_input($data);
        $data = array(
            'id' => 'to_class_id',
            'name' => 'to_class_id',
            'type' => 'hidden'
        );
      
        echo form_input($data);
        ?> 
     
        <div class="to_move_company_span" style="display:none">
            <div>
                <table class="table table-striped">
                    <tr>
                        <td colspan="2"><b>Select the Company Invoice:</b> 
                            <input type="text" name="to_move_company_autocomplete" id="to_move_company_autocomplete" style="width: 850px;" />

                            <span id="to_remove_company_select_err"></span>
                        </td>
                    </tr>
                </table>
            </div> 
            
        </div>
       
        <div class="push_right">
            <button type="submit" class="btn btn-xs btn-primary no-mar submit_button">
                <span class="glyphicon glyphicon-retweet"></span>
                Confirm Enrollment Change
            </button>
        </div>        
        <div class="lock_class_atten" style=" height: 50px;display:none;">                    
         <p class="error"><i>(***    Can`t Re-schedule in class whose attendance is locked. To Re-schedule it please contact to Administrator.)</i>
            </p>
            <br/>                       
    </div>    
    </div>    
<?php echo form_close(); ?>

<!-- added by pritam-->

    <div class="remvind_div" style="display: none;">
        <h2 class="sub_panel_heading_style">
            <span class="glyphicon glyphicon-circle-arrow-right"></span> Enrollment Details of '<span class="user_name"></span>'            
        </h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td  class="td_heading">
                            User Name : &nbsp;&nbsp;&nbsp;&nbsp;
                            <label class="label_font"><span class="user_name"></span></label>
                        </td> 
                         <td  class="td_heading">
                            Tax Code #: &nbsp;&nbsp;&nbsp;&nbsp;
                            <label class="label_font"><span class="tax_code"></span></label>
                        </td> 
                        <td class="td_heading">
                            Course name:&nbsp;&nbsp;&nbsp;&nbsp;
                            <label class="label_font"><span class="crse_name"></span>(<span class="course_id"></span>)</label>
                        </td>
                       
                        <td  class="td_heading">class name:&nbsp;&nbsp;&nbsp;&nbsp;
                            <label class="label_font"><span class="class_name"></span>(<span class="class_id"></span>)</label>
                        </td>
                         <td  class="td_heading">Invoice Id:&nbsp;&nbsp;&nbsp;&nbsp;
                            <label class="label_font"><span class="invoice_no"></span></label>
                        </td>   
<!--                         <td  class="td_heading">Payment Id:&nbsp;&nbsp;&nbsp;&nbsp;
                            <label class="label_font"><span class="pymnt_due_id"></span></label>
                        </td>   -->
                    </tr>
                    
                    
                </tbody>
            </table>
</div>
    </div>
    <br/>
    <?php
    $atr = 'id="remove_enrolment_change_form" name="" method="post" onsubmit = "return remove_button_disable()"';
    echo form_open("class_trainee/remove_ind_enrll", $atr);
    ?>
    <div class="remvind_div1" style="display: none;">
       <?php
         $data = array(
            'id' => 'tenant_id',
            'name' => 'tenant_id',
            'type' => 'hidden'
        );
        echo form_input($data);
        $data = array(
            'id' => 'user_id1',
            'name' => 'user_id1',
            'type' => 'hidden'
        );
        echo form_input($data);
         $data = array(
            'id' => 'user1',
            'name' => 'user1',
            'type' => 'hidden'
        );
        echo form_input($data);
        $data = array(
            'id' => 'pymnt_due_id1',
            'name' => 'pymnt_due_id1',
            'type' => 'hidden'
        );
        echo form_input($data);
       $data = array(
            'id' => 'invoice_no1',
            'name' => 'invoice_no1',
            'type' => 'hidden'
        );
        echo form_input($data);
        
        $data = array(
            'id' => 'course_id1',
            'name' => 'crouse_id1',
            'type' => 'hidden'
        );
        echo form_input($data);
        $data = array(
            'id' => 'class_id1',
            'name' => 'class_id1',
            'type' => 'hidden'
        );
        echo form_input($data);
     
        ?> 
        <div class="push_right">
            <button type="submit" class="btn btn-xs btn-primary no-mar">
                <span class="glyphicon glyphicon-retweet"></span>
                Remove From enrollment
            </button>
        </div>  
     </div>
        <?php echo form_close(); ?>
        <form action="<?php echo base_url();?>classes/edit_class" method="post" name ="my_form" id ="my_form" class="control_skm_err" style="display: none;">   
            <table class="table table-striped"> 
                 <tr class="danger">
                   <td style="color:red;text-align: center;">
                    <input type="hidden" class="class_id"  name="class_id"  />
                    <input type="hidden" class="course_id" name="course_id"  />
                    Please Schedule The Class First.
                     &nbsp;&nbsp;&nbsp;<input type="submit"  value ="Schedule Class" /></td>
                </tr>
            </table>
       </form>
</div>

<!-- end pritam -->

<!---------- /*  added by shubhranshu for client requirement on 21/03/2019 */-->
    <div class="modal1_055" id="ex111" style="display:none;">         
        <h2 class="panel_heading_style">Warning</h2>   
        <div  style="margin-top:7%">
            <p style="text-align: center;">
                The trainee whom you are about to register/enrol is part of the restricted list. Please acknowledge to continue !!!
            </p>
            <div class="popup_cancel9">
                <div rel="modal:close">
                    <a href="#" rel="modal:close"><button class="btn btn-primary trainee_deatils_yes" type="button">Ok</button></a>
                </div>
            </div>
        </div>
    </div>
<!---------- /*  added by shubhranshu for client requirement on 21/03/2019 */-->
<!---------- /*  added by shubhranshu for client requirement on 21/03/2019 */-->
    <div class="modal1_055" id="ex1111" style="display:none;">         
        <h2 class="panel_heading_style">Warning</h2>   
        <div  style="margin-top:7%">
            <p style="text-align: center;" >
                The trainees whom you are about to enrol is part of the restricted list. Please acknowledge to continue !!!
            </p>
            <div id="nric_list" style="font-weight:bold;color:red"></div>
            <div class="popup_cancel9">
                <div rel="modal:close">
                    <a href="#" rel="modal:close"><button class="btn btn-primary trainee_deatils_yes" type="button">Ok</button></a>
                </div>
            </div>
        </div>
    </div>
    <!---------- /*  added by shubhranshu for client requirement on 21/03/2019 */-->
<script>
    $(document).ready(function(){
        
    function encrypt() {
        var tpgraw = '<?php echo $tpg_data; ?>';
        var key = '<?php echo $tpg_key; ?>';
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
      
     $('#enrol_now_tpg').click(function(){
         encrypt();
         
         if(validation()){
             $('#tpg_form').submit();
         }
         
     });
     
        function validation(){
           $retval = true;
            $nric = $('#nric').val();
            if ($nric.length == 0) {
                disp_err('#nric');
                $retval = false;
            } else {
                remove_err('#nric');
            } 
            $fullname = $('#fullname').val();
            if ($fullname.length == 0) {
                disp_err('#fullname');
                $retval = false;
            } else {
                remove_err('#fullname');
            }
            $dob = $('#dob').val();
            if ($dob.length == 0) {
                disp_err('#dob');
                $retval = false;
            } else {
                remove_err('#dob');
            }

            $temail = $('#temail').val();
            if ($temail.length == 0) {
                disp_err('#temail');
                $retval = false;
            } else {
                remove_err('#temail');
            }

            $tpuen = $('#tpuen').val();
            if ($tpuen.length == 0) {
                disp_err('#tpuen');
                $retval = false;
            } else {
                remove_err('#tpuen');
            }

             $tpcode = $('#tpcode').val();
            if ($tpcode.length == 0) {
                disp_err('#tpcode');
                $retval = false;
            } else {
                remove_err('#tpcode');
            }

             $crefno = $('#crefno').val();
            if ($crefno.length == 0) {
                disp_err('#crefno');
                $retval = false;
            } else {
                remove_err('#crefno');
            }

            $crunid = $('#crunid').val();
            if ($crunid.length == 0) {
                disp_err('#crunid');
                $retval = false;
            } else {
                remove_err('#crunid');
            }

            $discount_amount = $('#discount_amount').val();
            if ($discount_amount.length == 0) {
                disp_err('#discount_amount');
                $retval = false;
            } else {
                remove_err('#discount_amount');
            }

             $collection_status = $('#collection_status').val();
            if ($collection_status.length == 0) {
                disp_err('#collection_status');
                $retval = false;
            } else {
                remove_err('#collection_status');
            }
            $enrolment_date = $('#enrolment_date').val();
            if ($enrolment_date.length == 0) {
                disp_err('#enrolment_date');
                $retval = false;
            } else {
                remove_err('#enrolment_date');
            }

            $tenant_email_id = $('#tenant_email_id').val();
            if ($tenant_email_id.length == 0) {
                disp_err('#tenant_email_id');
                $retval = false;
            } else {
                remove_err('#tenant_email_id');
            }

            $tenant_contact_num = $('#tenant_contact_num').val();
            if ($tenant_contact_num.length == 0) {
                disp_err('#tenant_contact_num');
                $retval = false;
            } else {
                remove_err('#tenant_contact_num');
            }
            
            $contact_name = $('#contact_name').val();
            if ($contact_name.length == 0) {
                disp_err('#contact_name');
                $retval = false;
            } else {
                remove_err('#contact_name');
            }


            return $retval;

        }
      
    });
    
</script>
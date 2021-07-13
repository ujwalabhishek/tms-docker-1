<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/multiselectdropdown.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/multiselectdropdown.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/multidropdown.js"></script><script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/addnewenroll.js"></script>
<div class="col-md-10 right-minheight">
    <?php echo validation_errors('<div class="error1">', '</div>'); ?> 
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Class Trainee Enrollment</h2>
    <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Enrollment Details</h2>
    <div class="table-responsive">
        <?php
        $atr = 'id="search_form" name="search_form" method="post" onkeypress="return event.keyCode != 13;"';
        echo form_open("class_trainee/enrollment_view_page", $atr);
        ?>  
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td colspan="4">
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
                        <?php
                        if($this->session->userdata('userDetails')->role_id == 'ADMN' || $this->session->userdata('userDetails')->role_id == 'CRSEMGR') {  //Modified by dummy 12-03-2015. Reason: Permission only for crsemgr.
                            $data = array(
                                'id' => 'enrollment_mode_change',
                                'class' => 'enrollment_type',
                                'name' => 'enrollment_mode',
                                'value' => 'change',
                                'checked' => FALSE
                            );
                            echo form_radio($data);
                            ?>
                            &nbsp;&nbsp;
                            <label style="color:blue;font-weight:bold">Change individual enrollment to company enrollment</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php
                            $data = array(
                                'id' => 'remove_company_invoice',
                                'class' => 'enrollment_type',
                                'name' => 'enrollment_mode',
                                'value' => 'remove_invoice',
                                'checked' => FALSE
                            );
                            echo form_radio($data);
                            ?>
                            &nbsp;&nbsp;<label style="color:blue;font-weight:bold">Remove enrollments from company invoice</label>
                        <?php } ?>
                    </td>                                       
                </tr>
                <?php if($this->session->userdata('userDetails')->role_id == 'ADMN' || $this->session->userdata('userDetails')->role_id == 'CRSEMGR') {  //Modified by dummy 12-03-2015. Reason: Permission only for crsemgr. ?>
                <tr>
                    <td colspan="4">
                        <?php
                        $data = array(
                            'id' => 'add_company_invoice',
                            'class' => 'enrollment_type',
                            'name' => 'enrollment_mode',
                            'value' => 'add_invoice',
                            'checked' => FALSE
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp;<label style="color:blue;font-weight:bold">Add enrollments to company invoice</label>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php
                        $data = array(
                                'id' => 'enrollment_mode_remvind',
                                'class' => 'enrollment_type',
                                'name' => 'enrollment_mode',
                                'value' => 'remvind',
                                'checked' => FALSE
                            );
                            echo form_radio($data);
                            ?>
                            &nbsp;&nbsp;
                            <label style="color:blue;font-weight:bold">Remove individual enrollment</label>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php
                            $data=array(
                                    'id'=>'move_enrollment',
                                    'class'=>'enrollment_type',
                                    'name'=>'enrollment_mode',
                                    'value'=>'move_invoice',
                                    'checked'=>FALSE
                                    );
                            echo form_radio($data);
                        ?>
                         &nbsp;&nbsp;
                        <label style="color:blue;font-weight: bold">Move Trainee from one company Invoice to other company invoice</label>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                <?php } ?>
                <tr class="change_span" style="display:none">
                    <td  colspan="2">
                        <b>Select Individual Enrolled:</b>
                    </td>
                </tr>
                <tr class="change_span" style="display:none">
                    <td  colspan="2" >
                        <select name="change_taxcode" id='change_taxcode' style="width:700px;">
                            <option value="">Please Select</option>
                        </select>                    
                        <b>Search by NRIC/FIN No.:</b> <input type="text" name="change_taxcode_autocomplete" id="change_taxcode_autocomplete" style="width: 700px;"/>
                        <span id="change_taxcode_err"></span>
                    </td> 
                </tr>
                <tr class="change_span1" style="display:none">
                    <td  colspan="2" >
                        <select name="change_taxcode1" id='change_taxcode1' style="width:700px;">
                            <option value="">Please Select</option>
                        </select>                    
                        <b>Search by NRIC/FIN No.:</b> <input type="text" name="change_taxcode_autocomplete1" id="change_taxcode_autocomplete1" style="width: 700px;"/>
                        <span id="change_taxcode_err"></span>
                    </td> 
                </tr>
                <tr class="remove_company_span" style="display:none">
                    <td  colspan="2">
                        <b>Select Company Invoices:</b>
                    </td>
                </tr>
                <tr class="remove_company_span" style="display:none">
                    <td  colspan="2">
                        <select name="remove_company_select" id='remove_company_select' style="width:700px;">
                            <option value="">Please Select</option>
                        </select>
                        <b>Search by Company Name:</b> <input type="text" name="remove_company_autocomplete" id="remove_company_autocomplete" style="width: 850px;"/>
                        <span id="remove_company_select_err"></span>
                    </td> 
                </tr>
                <tr class="move_company_span" style="display:none">
                    <td  colspan="2">
                        <select name="move_company_select" id='move_company_select' style="width:700px;">
                            <option value="">Please Select</option>
                        </select>
                        <b>Search by Company :</b> <input type="text" name="move_company_autocomplete" id="move_company_autocomplete" style="width: 850px;"/>
                        <span id="move_company_select_err"></span>
                    </td> 
                </tr>
                <tr class="new_span">
                    <td width="17%" class="td_heading">Enrollment Type:<span class="required">*</span></td>
                    <td colspan="3">
                        <?php
                        $options = array();
                        $options[''] = 'Select';
                        $options['individual'] = 'Individual';
                        $options['company'] = 'Company';
                        $val = '';
                        if ($this->data['user']->role_id == 'COMPACT') {
                            $options = array('' => 'Select', 'company' => 'Company');
                            $val = 'company';
                        }

                        $js = ' id="account_type"';
                        echo form_dropdown('account_type', $options, $val, $js);
                        ?>   
                        <span id="main_err"></span>
                    </td>
                </tr>
                <tr class="individual_tr new_span" style="display:none;">
                    <td class="td_heading">
                        <?php
                        $checked = TRUE;
                        $check = $this->input->post('search_select');
                        if ($check) {
                            $checked = ( $check == 1) ? TRUE : FALSE;
                        }
                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => 1,
                            'checked' => $checked
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp; NRIC/FIN No.:
                    </td>
                    <td width="32%">
                        <?php
                        $data = array(
                            'id' => 'taxcode',
                            'name' => 'taxcode',
                            'class'=>'upper_case',
                            'value' => $this->input->post('taxcode'),
                            'style' => 'width:200px',
                        );
                        echo form_input($data);
                        $data = array(
                            'id' => 'taxcode_id',
                            'name' => 'taxcode_id',
                            'value' => $this->input->post('taxcode_id'),
                            'type' => 'hidden'
                        );
                        echo form_input($data);
                        ?>
                        <br>
                        <span id="taxcode_err"></span>
                    </td>
                    <td width="15%" class="td_heading">
                        <?php
                        $checked = ($this->input->post('search_select') == 2) ? TRUE : FALSE;
                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => 2,
                            'checked' => $checked
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp; 
                        Trainee Name:</td>
                    <td><?php
                        $data = array(
                            'id' => 'trainee',
                            'name' => 'trainee',
                            'value' => $this->input->post('trainee'),
                            'style' => 'width:250px',
                        );
                        echo form_input($data);
                        $data = array(
                            'id' => 'trainee_id',
                            'name' => 'trainee_id',
                            'value' => $this->input->post('trainee_id'),
                            'type' => 'hidden'
                        );
                        echo form_input($data);
                        ?>
                        <br>
                        <span id="trainee_err"></span>
                    </td>
                </tr>
                <tr class="new_span">
                    <td class="td_heading">Select Course:<span class="required">*</span></td>
                    <td colspan="3">
                        <?php
                        $options = array();
                        $options[''] = 'Select';
                        $js = 'id="course" ';
                        echo form_dropdown('course', $options, $this->input->post('course'), $js);
                        ?>
                        <span id="course_err"></span>
                    </td>
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Select Class:<span class="required">*</span></td>
                    <td colspan="3">
                        <?php
                        $options = array();
                        $options[''] = 'Select';
                        $js = 'id="class" ';
                        echo form_dropdown('class', $options, $this->input->post('class'), $js);
                        ?>
                        <span id="class_err"></span>
                    </td>
                </tr>
                <tr class="company_tr" style="display:none;">
                    <td class="td_heading">Select Company:</td>
                    <td colspan="3">
                        <?php
                        $options = array();
                        $options[''] = 'Select';
                        $js = ' id="company" disabled="disabled"';
                        echo form_dropdown('company', $options, '', $js);
                        ?>   
                        <span id="company_err"></span>
                    </td>
                </tr>
                <tr class="company_tr" style="display:none;">
                    <td class="td_heading">Trainees:</td>
                    <td colspan="3">
                        <span  class="company_td">
                            <select id="control_n" disabled="disabled"><option value="">Select</option></select>
                        </span>
                        <span id="control_6_err"></span>
                    </td>
                </tr>
                 <tr class="add_inv1" style="display: none;">
                    <td colspan="3"><span><font color="red">[The Invoice for this company is already exist, Please enroll by using</font>  <a href="#" class="add_inv" style="display: none;" >Add enrollments to Company invoie</a><font color="red">.]</font></span></td>
                </tr>
                <tr class="new_span">
                    <td colspan="4" class="no-bg">
                        <div class="push_right">
                            <button type="submit" class="search_button btn btn-xs btn-primary no-mar">
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
    $atr = 'id="confirm_enrolment_change_form" name="" method="post"';
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
            <button type="submit" class="btn btn-xs btn-primary no-mar ">
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
<script>
    $(document).ready(function(){
       return enrollment_type_change(); 
    });
    
</script>
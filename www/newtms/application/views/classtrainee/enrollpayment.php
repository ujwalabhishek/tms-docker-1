<?php
$enroll_style = '';
$book_style = 'display: none;';
$ex5_height = 'height:300px;';
$discount_amount = ($classes->class_fees  * ($discount['discount_rate']/100) );
?>
<script>
    $gst_on = '<?php echo $courses->gst_on_off; ?>';
    $gst_subsidy_afterbefore = '<?php echo $courses->subsidy_after_before; ?>';
    $gst_feesdue = '<?php echo $feesdue; ?>';
    $amount_check = '<?php echo $feesdue; ?>';
    $gst_gstrate = '<?php echo $gstrate; ?>';
	
	$course_id = '<?php echo $courses->course_id;?>';//s_skm1 
    $trainee_age = '<?php echo $trainee_age->age?>';//s_skm2
    $trainee_id = '<?php echo $trainee_id; ?>';//s_skm3
    $course_duration = '<?php echo $course_duration; ?>';//s_skm4

    $company_unit_fees = '<?php echo $company_unit_fees; ?>';

    $class_fees = '<?php echo $classes->class_fees; ?>';
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
    
    $ori_comp_disc_amt = '<?php echo number_format($company_discount_amount, 2, '.', ''); ?>';
    $ori_discount_amount = '<?php echo number_format($discount_amount, 2, '.', ''); ?>';    
    $role_id = "<?php echo $this->session->userdata('userDetails')->role_id;?>";//added by shubhranshu
    $privilage = "<?php echo $privilage;?>"; //added by shubhranshu
    $restriction_flag = "<?php echo $restriction_flag;?>"; //added by shubhranshu
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/enrollpayment.js"></script>

<div class="col-md-10">
    <?php echo validation_errors('<div class="error1">', '</div>'); ?> 
    <?php
    $atr = 'id="enrollpaymentform" name="enrollpaymentform" method="post"';
    echo form_open("class_trainee/enrollment_view_page", $atr);
    ?>  
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-retweet"></span> Enrollment Payments</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <?php if ($this->input->post('account_type') == 'individual') { ?>
                    <tr>
                        <td width="17%" class="td_heading">Trainee Details: 
                        </td>
                        <td width="83%">
                            <?php
                           echo $trainee_name->first . ' ' . $trainee_name->last . ' ( NRIC/FIN No.: ' . $trainee_name->tax_code . ' )';
                           ?>
                        </td>
                    </tr>
                    <?php
                }
                if ($this->input->post('account_type') == 'company') {
                    $enroll_style = 'display:none;';
                    $book_style = '';
                    $ex5_height = 'height:300px;';
                    ?>
                    <tr>
                        <td width="17%" class="td_heading">Company Details: 
                        </td>
                        <td width="83%">
                            <?php
                            echo $company_details[0]->company_name;
                            ?>&nbsp;&nbsp;
                            <?php
                             $role_array = array("TRAINER", "COMPACT", "SLEXEC");
                            if ($pending_payments && !in_array($this->session->userdata('userDetails')->role_id, $role_array)):                            
                                echo "( <span class='red blink'>There are Pending Payments.</span> Click here to <a href='" . base_url() . "accounting/update_payment'>Update payment...</a> )";
                            endif;
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td width="17%" class="td_heading">Enrollment Type:<span class="required">*</span>
                    </td>
                    <td width="83%">
                        <?php
                        $data = array(
                            'name' => 'account_type',
                            'type' => 'hidden'
                        );
                        echo form_input($data, $this->input->post('account_type'), 'id="account_type"');
                        $data = array(
                            'name' => 'company',
                            'type' => 'hidden'
                        );
                        echo form_input($data, $this->input->post('company'), 'id="company"');
                        $data = array(
                            'name' => 'discount',
                            'type' => 'hidden'
                        );
                        echo form_input($data, $this->input->post('discount'), 'id="discount"');
                        echo array_to_input($this->input->post('control_6'), 'control_6');

// skm code start
                        if($direct == 1){ // direct check whether trainee direct comes from register and enroll process OR from enrollemnt process
                            // $direct == 1 means trainee direct comes from register and enroll process
                            $data = array(
                                'name' => 'course',
                                'type' => 'hidden',
                                'value' => $course
                            );
                            echo form_input($data, $this->input->post('course'), 'id="course"');
                            $data = array(
                                'name' => 'class',
                                'type' => 'hidden',
                                value => $class
                            );
                            echo form_input($data, $this->input->post('class'), 'id="class"');
                        }
                        else
                        {
                            $data = array(
                            'name' => 'course',
                            'type' => 'hidden'
                            );
                            echo form_input($data, $this->input->post('course'), 'id="course"');
                            $data = array(
                                'name' => 'class',
                                'type' => 'hidden'
                            );
                            echo form_input($data, $this->input->post('class'), 'id="class"');
                        }
//                        $data = array(
//                            'name' => 'course',
//                            'type' => 'hidden'
//                        );
//                        echo form_input($data, $this->input->post('course'), 'id="course"');
//                        $data = array(
//                            'name' => 'class',
//                            'type' => 'hidden'
//                        );
//                        echo form_input($data, $this->input->post('class'), 'id="class"');
                        $data = array(
                            'name' => 'search_select',
                            'type' => 'hidden'
                        );
                        echo form_input($data, $this->input->post('search_select'), 'id="search_select"');
                        $data = array(
                            'name' => 'taxcode_id',
                            'type' => 'hidden'
                        );
                        echo form_input($data, $this->input->post('taxcode_id'), 'id="taxcode_id"');
                        $data = array(
                            'name' => 'trainee_id',
                            'type' => 'hidden'
                        );
                        echo form_input($data, $this->input->post('trainee_id'), 'id="trainee_id"');
                        $data = array(
                            'name' => 'payment_enrol',
                            'type' => 'hidden'
                        );
                        echo form_input($data, $classes->class_pymnt_enrol, 'id="payment_enrol"');
                        $check = $this->input->post('enrollment_type');
                        if ($check) {
                            $checked = ( $check == 1) ? TRUE : FALSE;
                        }
                        $data = array(
                            'id' => 'enrollment_type',
                            'class' => 'enrollment_type',
                            'name' => 'enrollment_type',
                            'value' => 1,
                            'checked' => FALSE
                        );
                        echo form_radio($data);
                        ?> Re-take &nbsp;&nbsp; <?php
                        $checked = TRUE;
                        $check = $this->input->post('enrollment_type');
                        if ($check) {
                            $checked = ( $check == 2) ? TRUE : FALSE;
                        }
                        $data = array(
                            'id' => 'enrollment_type',
                            'class' => 'enrollment_type',
                            'name' => 'enrollment_type',
                            'value' => 2,
                            'checked' => FALSE
                        );
                        echo form_radio($data);
                        $check = $this->input->post('enrollment_type');
                        ?> First Attempt
                        <span id="enrollment_type_err"></span>
                    </td>
                </tr>
                <tr class="retake_span" style="<?php echo ($check == 1) ? '' : 'display:none;'; ?>">
                    <td>&nbsp;</td>
                    <td>
                        <span class="red retake_span" style="<?php
                        $check = $this->input->post('payment_retake');
                        echo ($check == 1) ? '' : 'display:none;';
                        ?>">
                                  <?php
                                  $check = $this->input->post('payment_retake');
                                  $checked = TRUE;
                                  if ($check) {
                                      $checked = ( $check == 1) ? TRUE : FALSE;
                                  }
                                  $data = array(
                                      'id' => 'payment_retake',
                                      'class' => 'payment_retake',
                                      'name' => 'payment_retake',
                                      'value' => 1,
                                      'checked' => $checked
                                  );
                                  echo form_radio($data);
                                  ?>
                            Payment required on re-take &nbsp;&nbsp; 
                            <?php
                            $check = $this->input->post('payment_retake');
                            if ($check) {
                                $checked = ( $check == 2) ? TRUE : FALSE;
                            }
                            $data = array(
                                'id' => 'payment_retake',
                                'class' => 'payment_retake',
                                'name' => 'payment_retake',
                                'value' => 2,
                                'checked' => $checked
                            );
                            echo form_radio($data);
                            ?> Bypass payment on re-take
                        </span></td>
                </tr>
                <?php
                 $this->session->userdata('userDetails')->user_id;
                 $sales_name=$this->session->userdata('userDetails')->first_name;
              
                $style = '';
                if (!empty($salesexec_check)) 
                {
                   
                    //$style = 'display:none';?>
<!--                    <tr style="<?php echo $style ?>">
                        <td colspan="2" class="td_heading">    
                            <div style="margin-top:0px;" >
                                <span class="td_heading">Enrolled by Sales Executive.:
                                    <?php
                                    $options[$row['user_id']] = $sales_name;
                                    $extra = 'id="salesexec"';
                                    $selected_sales =$this->session->userdata('userDetails')->user_id;
                                    echo form_dropdown('salesexec', $options, $selected_sales, $extra);
                                    ?>
                                    <span id="salesexec_err"></span>
                                </span>
                            </div>
                        </td>
                    </tr>-->
                <?php
                }
                else if (!empty($salesexec) && empty($salesexec_check)) 
                {
                   
                    ?>
                    <tr style="<?php echo $style ?>">
                        <td colspan="2" class="td_heading">    
                            <div style="margin-top:0px;" >
                                <span class="td_heading">Enrolled by Sales Executive.:
                                    <?php
                                    $options = array();
                                    if (empty($salesexec_check)) {
                                        $options[''] = 'Select';
                                    }
                                    foreach ($salesexec as $row) {
                                        $options[$row['user_id']] = $row['first_name'] . ' ' . $row['last_name'];
                                    }
                                    $extra = 'id="salesexec"';
                                    $selected_sales = ($this->input->post('salesexec'))? $this->input->post('salesexec'): $this->session->userdata('userDetails')->user_id;
                                    echo form_dropdown('salesexec', $options, $selected_sales, $extra);
                                    ?>
                                    <span id="salesexec_err"></span>
                                </span>
                            </div>
                        </td>
                    </tr>
                <?php 
                } ?>
            </tbody>
        </table>
    </div>
    <br>
    <div style="clear:both;"></div>
    <?php if ($this->input->post('account_type') == 'company') { ?>
        <div>
            <div class="comp_payment_due">
                <h2 class="sub_panel_heading_style">Payment Due for '<?php echo $courses->crse_name; ?> - <?php echo $classes->class_name; ?>'</h2>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td class="td_heading">Total Invoice Fees:</td>
                                <td><label class="label_font">$ <?php echo number_format($company_unit_fees, 2, '.', ''); ?> SGD</label></td>
                                <td class="td_heading">Total <span id="comp_disc_label"><?php echo $company_discount_label; ?></span> Discount <span class="box_bgcolor">@ <span id="comp_disc_rate"><?php echo number_format($company_discount_rate, 2, '.', ''); ?></span>%</span>:
                                    <input type="hidden" id="comp_disc_rate_hidden" value="<?php echo number_format($company_discount_rate, 4, '.', ''); ?>" />
                                </td>
                                <td><label class="label_font">$ <?php echo '<input type="text" name="comp_disc_amt" id="comp_disc_amt" value="' . number_format($company_discount_amount, 2, '.', '') . '"/>'; ?> SGD</label></td>
                            </tr>
                            <tr>
                                <td class="td_heading">Total Subsidy Amt.:</td>
                                <td><label class="label_font">$ <span class="company_subsidy_amount">0.00</span> SGD</label></td>
                                <td class="td_heading">GST Rate (<?php echo $gstlabel; ?>) @ <?php echo number_format($gstrate, 2, '.', ''); ?>%:</td>
                                <td><label class="label_font">$ <span class="company_gst_amount"><?php echo number_format($company_gst_total, 2, '.', ''); ?></span> SGD</label></td>
                            </tr>
                            <tr>
                                <td class="td_heading">Total Fees Due:</td>
                                <td colspan="3">
                                    <label class="label_font">$ <span class="company_net_due">
                                    <?php echo number_format($company_net_due, 2, '.', ''); ?></span> SGD
                                    <input type="hidden" class="company_net_due_hidden" value="<?php echo number_format($company_net_due, 4, '.', ''); ?>" />
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br>
            </div>
            <div class="table-responsive payment_scroll">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="20%" class="th_header">Trainee Name</th>
                            <th width="20%" class="th_header">TG#</th>
                            <th width="40%">Subsidy</th>
                            <th width="49%">Subsidy Recd. Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($trainees as $row) {
                            $options = array();                                    
                            $options[''] = 'Select';                               
                            foreach ($subsidy_type as $type) {
                                $options[$type->subsidy_id] = $type->subsidy_type;
                            }
                            $subsidy_type_attr = 'id="comp_subsidy_type" class="comp_subsidy_type" data-user="' . $row->user_id . '"';
                            echo '<tr>';
                            echo '<td>';
                            $data = array(
                                'class' => 'trainee_selected',
                                'name' => 'enrollment_type',
                                'value' => $row->user_id,
                                'checked' => $checked,
                                'style' => 'display:none;'
                            );
                            echo form_checkbox($data);
                            echo $row->first_name . ' ' . $row->last_name . '</td>
                            <td>
                            ' .
                            form_input('trainee_tg[' . $row->user_id . ']', '', ' class="trainee_tg"')
                            . '
                            </td>
                            <td> 
                            ' .
                            
                            form_dropdown('subsidy_type[' . $row->user_id . ']', $options,$this->input->post('subsidy_type'),$subsidy_type_attr)                            
                            . ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp
                            $ <label id="subsidy_amount_label_' . $row->user_id . '">0.00</label>
                            ' .
                            form_input('trainee_subsidy_amount[' . $row->user_id . ']', '', 'style="display:none" class="trainee_subsidy_amount" id="trainee_subsidy_amount_' . $row->user_id . '" data-user="' . $row->user_id . '"')
                            . 'SGD                            
                            <br>
                            <span id="trainee_subsidy_amount_' . $row->user_id . '_err"></span>
                            </td>
                            <td>' .
                            form_input('trainee_subsidy_date[' . $row->user_id . ']', '', ' class="trainee_subsidy_date" data-user="' . $row->user_id . '" id="subsidydate_' . $row->user_id . '" readonly="readonly"')
                            . '<span id="subsidydate_' . $row->user_id . '_err"></span></td>
                        </tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div style="clear:both;"></div>
        <br><br>
    <?php }
    ?>
    <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-book"></span> Course - Class Details</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="20%" class="td_heading">Course Name:</td>
                    <td width="30%" ><label class="label_font"><?php echo $courses->crse_name; ?></label></td>
                    <td width="20%" class="td_heading">Course Ref. Number:</td>
                    <td width="30%" ><label class="label_font"><?php echo $courses->reference_num; ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Course Competency Code:</td>
                    <td ><label class="label_font"><?php echo $courses->competency_code; ?></label></td>
                    <td class="td_heading">Cert. Code/ Level:</td>
                    <td ><label class="label_font" ><?php echo $courseLevel; ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Class Name:</td>
                    <td ><label class="label_font"><?php echo $classes->class_name; ?></label></td>
                    <td class="td_heading">No. of sessions per day:</td>
                    <td ><label class="label_font"><?php echo ($classes->class_session_day == 1) ? 'One Session' : 'Two Sessions'; ?></label></td>

                </tr>
                <tr>
                    <td class="td_heading">Classroom Duration:</td>
                    <td><label class="label_font"><?php echo $classes->total_classroom_duration; ?> hrs</label></td>
                    <td class="td_heading">Lab Duration:</td>
                    <td><label class="label_font"><?php echo $classes->total_lab_duration; ?> hrs</label></td>

                </tr>
                <tr>
                    <td class="td_heading">Start Date & Time:</td>
                    <td ><label class="label_font"><?php echo date('d/m/Y h:i A', strtotime($classes->class_start_datetime)); ?></label></td>
                    <td class="td_heading">End Date & Time:</td>
                    <td ><label class="label_font"><?php echo date('d/m/Y h:i A', strtotime($classes->class_end_datetime)); ?></label></td>          
                </tr>
                <tr>
                    <td class="td_heading">Class Language:</td>
                    <td><label class="label_font"><?php echo $ClassLang; ?></label></td>
                    <td class="td_heading">Class Fees:</td>
                    <td ><label class="label_font">$ <?php echo number_format($classes->class_fees, 2, '.', ''); ?> SGD</label></td>
                </tr>
                <tr>
                    <td class="td_heading">Total Seats:</td>
                    <td><label class="label_font"><?php echo $classes->total_seats; ?></label></td>
                    <td class="td_heading">Available Seats:</td>
                    <td><label class="label_font"><?php echo ($available < 0) ? 0 : $available; ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Course Manager:</td>
                    <td ><label class="label_font"><?php echo $course_manager; ?></label></td>          
                    <td class="td_heading">Trainer:</td>
                    <td ><label class="label_font"><?php echo $ClassTrainer; ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Assessor:</td>
                    <td>
                        <?php if (!empty($Assessor)) { ?>
                            <label class="label_font"><?php echo $Assessor; ?></label>
                        <?php } else { ?>
                            <label class="label_font">--</label>
                        <?php } ?>
                    </td>
                    <td class="td_heading">Training Aide:</td>
                    <td>
                        <?php if (!empty($TrainingAide)) { ?>
                            <label class="label_font"><?php echo $TrainingAide; ?></label>
                        <?php } else { ?>
                            <label class="label_font">--</label>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Location/Address:</td>
                    <td colspan="4"><label class="label_font"><?php echo $ClassLoc; ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Payment Mode:</td>
                    <td class="blue" colspan="4"><label class="label_font"><?php echo $ClassPay; ?></label></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="clear:both;"></div><br>
    <?php if ($this->input->post('account_type') == 'individual') {
        ?>
        <div style="width:100%;" class="retake_bypass_div">  
            <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-circle-arrow-up"></span> Total Fees Payable</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td class="td_heading" width="25%">Unit Fees:</td>
                            <td width="20%"><label class="label_font">$<?php echo number_format($classes->class_fees, 2, '.', ''); ?> SGD</label></td>
                            <td class="td_heading" width="30%"><?php echo $discount['discount_label']; ?> Discount <span class="box_bgcolor">@ <span id="disc_rate"><?php echo number_format($discount['discount_rate'], 2, '.', ''); ?></span>%</span>:
                            <?php 
                                echo '<input type="hidden" name="disc_rate_hidden" id="disc_rate_hidden" value="' . number_format($discount['discount_rate'], 4, '.', '') . '">';
                            ?>
                            </td>
                            <td width="25%"><label class="label_font">$ <?php
                                    $discount_amount = ($classes->class_fees * ($discount['discount_rate']/100));                                    
                                    echo '<input name="disc_rate" id="disc_amt" value="' . number_format($discount_amount, 2, '.', '') . '">';                                    
                                    ?> SGD</label>
                            </td>
                        </tr>
                        <tr>
                            <td class="td_heading">Subsidy Amt.:</td>
                            <td colspan=""><label class="label_font">$<span class="subsidy_amount">0.00</span> SGD</label></td>
                            <td class="td_heading">GST Rate (<?php echo $gstlabel; ?>) @ <?php echo number_format($gstrate, 2, '.', ''); ?>%:</td>
                            <td><label class="label_font">$ <span class="gst_amount"><?php echo number_format($gst_total, 2, '.', ''); ?></span> SGD</label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Total Fees Due:</td>
                            <td colspan="3"><label class="label_font">$<span class="net_due"><?php echo number_format($netdue, 2, '.', ''); ?></span> SGD</label></td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <br>
            <div class="retake_bypass_div">
                <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-circle-arrow-right"></span> Total Fees Receivable</h2>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <?php if ($classes->class_pymnt_enrol == 'PAENROL') { ?>
                                <tr>
                                    <td width="20%" class="td_heading">
                                        <?php
                                        $data = array(
                                            'id' => 'pay_time',
                                            'class' => 'pay_time',
                                            'name' => 'pay_time',
                                            'value' => 1,
                                            'checked' => true
                                        );
                                        echo form_radio($data);
                                        ?>
                                        Pay Now
                                    </td>
                                    <td class="td_heading" width="80%" colspan='3'><?php
                                        $data = array(
                                            'id' => 'pay_time',
                                            'class' => 'pay_time',
                                            'name' => 'pay_time',
                                            'value' => 2,
                                        );
                                        echo form_radio($data);
                                        ?> Pay Later</td>
                                </tr>
                            <?php } ?>
                            <tr class="rowdim">
                                <td class="td_heading" width="20%">TG#:</td>
                                <td  width="20%"><?php
                                    echo form_input('tg_number', $this->input->post('tg_number'), ' id="tg_number" maxlength="40"');
                                    ?></td>
                                <td class="td_heading"  width="20%">Subsidy Type.:</td> 
                                <td>
                                    <?php
                                    $options = array();                                    
                                    $options[''] = 'Select';                                    
                                    foreach ($subsidy_type as $row) {
                                        $options[$row->subsidy_id] = $row->subsidy_type;
                                    }
                                    echo form_dropdown('subsidy_type', $options,$this->input->post('subsidy_type'), ' id="subsidy_type"');
                                    ?>&nbsp;&nbsp; Subsidy Amt.: &nbsp;&nbsp; $ <label id="subsidy_amount_label">0.00</label><?php
                                    echo form_input('subsidy_amount', $this->input->post('subsidy_amount'), ' id="subsidy_amount" style="display:none"');
                                    ?> SGD
                                </td>
                            </tr>
                            <tr class="rowdim">
                                <td class="td_heading">Subsidy Recd. on:</td>
                                <td>
                                    <?php
                                    $recd_on = array(
                                        'name' => 'subsidy_recd_on',
                                        'id' => 'subsidy_recd_on',
                                        'readonly' => 'readonly',
                                        'value' => $this->input->post('subsidy_recd_on'),
                                    );
                                    echo form_input($recd_on);
                                    ?> 
                                    <span id="subsidy_recd_on_err"></span>
                                </td>
                                <td class="td_heading">Mode of Payment:<span class="required">*</span></td>
                                <td>
                                    <?php
//                                    $options = array(
//                                        '' => 'Select',
//                                        'CASH' => 'Cash',
//                                        'CHQ' => 'Cheque'
//                                    );
                                    if($tenant_id == 'T02' || $tenant_id == 'T12' || $tenant_id == 'T01')
                                    {$options = array(
                                        '' => 'Select',
                                        'CASH' => 'Cash',
                                        'CHQ' => 'Cheque',
                                        'NETS' => 'NETS'
                                    );
                                         
                                     }else{
                                         $options = array(
                                        '' => 'Select',
                                        'CASH' => 'Cash',
                                        'CHQ' => 'Cheque'
                                       
                                    );
                                     }
                                     
                                    $js = ' id="mode_of_payment"';
                                    echo form_dropdown('mode_of_payment', $options, '', $js);
                                    ?>  
                                    <span id="mode_of_payment_err"></span>
                                </td>
                            </tr>
                            <tr class="rowdim">
                                <td class="td_heading">Payment Received On:<span class="required">*</span></td>
                                <td>
                                    <?php
                                    $recd_on = array(
                                        'name' => 'recd_on',
                                        'id' => 'recd_on',
                                        'readonly' => 'readonly',
                                        'value' => $this->input->post('recd_on'),
                                    );
                                    echo form_input($recd_on);
                                    ?>
                                    &nbsp; <span id="recd_on_err"></span></td>
                                <td class="td_heading">Amount Received:<span class="required">*</span></td>
                                <td>$ <?php
                                    echo form_input('amount_rcd', $this->input->post('amount_rcd'), ' id="amount_rcd"');
                                    ?> SGD
                                    <span id="amount_rcd_err"></span>
                                </td>         
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <div id="row_dim" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td width="20%" class="td_heading">Cheque Number:<span class="required">*</span></td>
                                        <td width="20%"><?php
                                            echo form_input('chq_num', $this->input->post('chq_num'), ' id="chq_num"');
                                            ?>
                                            <span id="chq_num_err"></span>
                                        </td>         
                                        <td class="td_heading" width="20%">Cheque Date:<span class="required">*</span></td>
                                        <td>
                                            <?php
                                            $recd_on = array(
                                                'name' => 'chq_date',
                                                'id' => 'chq_date',
                                                'readonly' => 'readonly',
                                                'value' => $this->input->post('chq_date'),
                                            );
                                            echo form_input($recd_on);
                                            ?>
                                            <span id="chq_date_err"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="td_heading">Bank Name:<span class="required">*</span></td>
                                        <td colspan="3"><?php
                                            echo form_input('bank_name', $this->input->post('bank_name'), ' id="bank_name"');
                                            ?>
                                            <span id="bank_name_err"></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <span class="required required_i">* Required Fields</span>
        </div>
    <?php } ?>
    <div style="clear:both;"></div>
    <div class="push_right"><button class="btn btn-primary save_enroll" type="button" style="<?php echo $enroll_style; ?>"><span class="glyphicon glyphicon-saved"></span>&nbsp;Enroll Now</button> &nbsp; &nbsp; 
        <button class="btn btn-primary save_book" type="button" style="<?php echo $book_style; ?>"><span class="glyphicon glyphicon-saved"></span>&nbsp;Book Now</button> &nbsp; &nbsp; 
        <a href="<?php echo base_url() . 'class_trainee/add_new_enrol' ?>"><button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-step-backward"></span>&nbsp;Back</button></a>
    </div>
</div>
<div class="modal" id="ex1" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Heading Goes Here...</h2>
    Trainee Profile is displayed here…  <br>
    <div class="popup_cancel">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a></div></p>
</div>
<div class="modal_020" id="ex9" style="display:none;height: 450px;">
    <p>
    <h2 class="panel_heading_style">Payment Receipt</h2><br>
    <table width="100%">
        <tbody>
            <tr>
                <td rowspan="4"><img src="" class="logo" border="0" /></td>
                <td colspan="2"><span class="r_tenant_name"></span></td>
                <td class="td_heading"></td>
                <td><b>Receipt No.: </b><span class="r_invoice_no"></span></td>
            </tr>
            <tr>
                <td colspan="2"><span class="r_tenant_address"></span></td>
                <td class="td_heading"></td>
                <td><b>Receipt Date: </b><span class="r_invoice_recd"></span></td>
            </tr>
            <tr>
                <td colspan="4"><span class="r_tenant_phone"></span></td>
            </tr>
        </tbody>
    </table><br>
    <p><font style="font-size:14px; font-weight:bold;">Received</font> with thanks SGD <span class="r_invoice_amount_recd"></span> from <span class="r_invoice_trainee"></span> for <strong><i>'Course: <span class="r_course"></span>, Class: <span class="r_class"></span>, Certificate Code: <span class="r_certilevel"></span>'</i></strong>. Mode of payment: <span class="r_invoice_mode"></span>.</p>
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
                <td><span class="r_course_manager"></span> (<span class="r_tenant_phone"></span>, <span class="r_tenant_email"></span>)</td>
            </tr>
        </tbody>
    </table><br>
    <p style="color:red;"><i>This is a computer generated receipt and doesn't require a seal or signature.</i></p>
    <div style="clear:both;"></div>
    <div class="popup_cance89">
        <a href="javascript:;" class="payment_recd_href"><button class="btn btn-primary" type="button">Print</button></a></div>
</p>
</div>
<div class="modal_022" id="ex5" style="display:none;<?php echo $ex5_height; ?>">
    <h2 class="panel_heading_style">Booking Acknowledgment</h2>
    <span class="book_ack_text"></span>
    <div class="popup_cance89">
            <a href="#" class="book_ackn_print btn btn-primary">Print</a>
     </div>
</div>
<!----------------modal by ssp start----------------------->
<?php if ($this->session->userdata('userDetails')->tenant_id == 'T02' && $this->input->post('account_type') == 'individual'){$show='display:block';}else{$show='display:none';}?>
<div class="modal" id="ex1011" style="<?php //echo $show;?>;margin:auto;margin-top:20px;margin-bottom:20px;">
<p>
  <h2 class="panel_heading_style">Declaration</h2>
    <!--Section: Contact v.2-->
<section class="mb-4">

    <!--Section heading-->
    <!--<h2 class="h1-responsive font-weight-bold text-center my-4">Declaration Form</h2>-->
    <!--Section description-->
    <p class="text-center alert alert-danger">You must fill this form to continue for the enrollment,I consent for Xprienz to collect and use my personal data for the purposes of the company policy.</p>
  
    <div class="row">

        <!--Grid column-->
        <div class="col-md-12 mb-md-0 mb-5">
           
        
                <!--Grid row-->
                <div class="row">

                    <!--Grid column-->
                    <div class="col-md-12">
                        <div class="md-form mb-0">
                            <label for="name" class="">Your Name<span style='color:red'>*</span></label>
                            <input type="text" id="dec_name" class="form-control" value="<?php echo $trainee_name->first;?>" required>
                            
                        </div>
                    </div>
                    <!--Grid column-->
                </div>
                
                <div class="row">
                    <!--Grid column-->
                    <div class="col-md-6">
                        <div class="md-form mb-0">
                             <label for="email" class="">Your Email</label>
                             <input type="email" id="dec_email" class="form-control" pattern=".+@gmail.com" size="30" required>
                           
                        </div>
                    </div>
                    <!--Grid column-->
                    <!--Grid column-->
                    <div class="col-md-6">
                        <div class="md-form mb-0">
                             <label for="email" class="">Your Mobile<span style='color:red'>*</span></label>
                            <input type="tel" id="dec_mobile" class="form-control" name="phone" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required>
                           
                        </div>
                    </div>
                    <!--Grid column-->
                    
                </div>
                <!--Grid row-->

                <!--Grid row-->
                <div class="row">
                    <div class="col-md-6">
                        <div class="md-form mb-0">
                            <label for="subject" class="">Lesson Date:<span style='color:red'>*</span></label>
                            <input type="date" id="dec_les_time" class="form-control" required>
                            
                        </div>
                    </div>
                    <div class="col-md-6"></div>
                </div>
                <!--Grid row-->

                <!--Grid row-->
                <div class="row">

                    <!--Grid column-->
                    <div class="col-md-12">

                        <div class="md-form">
                            <label for="message">Do you have any relatives who returned from China on 15th January or later and is staying together? <span style='color:red'>*</span></label>
                            <div><input type="radio" value="1" class="" name='dec_res' id='dec_res' style='height: 1.7em;width: 20px;' checked><b style='padding:2px'>Yes</b></div>
                            <div><input type="radio" value="0" class="" name='dec_res' id='dec_res1' style='height: 1.7em;width: 20px;'><b style='padding:2px'>No</b> </div> 
                        </div>

                    </div>
                    <div class="col-md-12">

                        <div class="md-form">
                            <label for="message">Have you travelled overseas in the past 14 days? <span style='color:red'>*</span></label>
                            <input type="text" id="dec_overseas" placeholder='Please State' class="form-control" required>
                        </div>

                    </div>
                </div>
                
                 <div class="statuserr"></div>
                 <br>
                 <br>
                <!--Grid row-->
                 <div class="text-center">
                    <a href='#' class="btn btn-primary" id='declarations' type='button'>Submit & Continue</a></div>
                  
           
        </div>
        <!--Grid column-->
    </div>
         
</section>
<!--Section: Contact v.2-->

</p>
</div>
<!----------------modal by ssp end----------------------->
<?php
function array_to_input($array, $prefix = '') {
    if ((bool) count(array_filter(array_keys($array), 'is_string'))) {
        foreach ($array as $key => $value) {
            if (empty($prefix)) {
                $name = $key;
            } else {
                $name = $prefix . '[' . $key . ']';
            }
            if (is_array($value)) {
                array_to_input($value, $name);
            } else {
                ?>
                <input type="hidden" value="<?php echo $value ?>" name="<?php echo $name ?>">
                <?php
            }
        }
    } else {
        foreach ($array as $item) {
            if (is_array($item)) {
                array_to_input($item, $prefix . '[]');
            } else {
                ?>
                <input type="hidden" name="<?php echo $prefix ?>[]" value="<?php echo $item ?>">
                <?php
            }
        }
    }
}
?>

<!---------- /*  added by shubhranshu for client requirement on 21/03/2019 */-->
<style>
    #modalContainer {
	background-color:rgba(0, 0, 0, 0.3);
	position:absolute;
	width:100%;
	height:100%;
	top:0px;
	left:0px;
	z-index:10000;
	background-image:url(tp.png); /* required by MSIE to prevent actions on lower z-index elements */
}

#alertBox {
	position:relative;
	width:500px;
	min-height:100px;
	margin-top:192px;
	/*border:1px solid #666;*/
	background-color:#fff;
	background-repeat:no-repeat;
	background-position:20px 30px;
        border-radius:6px;
}

#modalContainer > #alertBox {
	position:fixed;
}

#alertBox h1 {
	margin:13px;
        text-align: center;
        background-color: #dfddec;
        color: #d61515;
	border-bottom:1px solid #dfddec;
	padding: 2px;
        font-size: 21px;
    font-weight: bold;
}

#alertBox p {
	font:verdana,arial;
        font-size:12px;
	height:50px;
	padding-left:5px;
	margin-left:55px;
}

#alertBox #closeBtn {
	display:block;
	position:relative;
	margin:5px auto;
	padding:7px;
	border:0 none;
	width:50px;
	/*font:0.7em verdana,arial;*/
	text-transform:uppercase;
	text-align:center;
	color:#FFF;
	background-color:#357EBD;
	border-radius: 3px;
	text-decoration:none;
}

/* unrelated styles */

#mContainer {
	position:relative;
	width:600px;
	margin:auto;
	padding:5px;
	border-top:2px solid #000;
	border-bottom:2px solid #000;
	font:0.7em verdana,arial;
}


code {
	font-size:1.2em;
	color:#069;
}

#credits {
	position:relative;
	margin:25px auto 0px auto;
	width:350px; 
	font:0.7em verdana;
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	height:90px;
	padding-top:4px;
}

#credits img {
	float:left;
	margin:5px 10px 5px 0px;
	border:1px solid #000000;
	width:80px;
	height:79px;
}

.important {
	background-color:#F5FCC8;
	padding:2px;
}

code span {
	color:green;
}
label {
  
    margin-top: 5px;
}
</style>
<script>
$(document).ready(function(){
       
       var ALERT_TITLE = "Warning!";
var ALERT_BUTTON_TEXT = "Ok";

if(document.getElementById) {
	window.alert = function(txt) {
		createCustomAlert(txt);
	}
}

function createCustomAlert(txt) {
	d = document;

	if(d.getElementById("modalContainer")) return;

	mObj = d.getElementsByTagName("body")[0].appendChild(d.createElement("div"));
	mObj.id = "modalContainer";
	mObj.style.height = d.documentElement.scrollHeight + "px";
	
	alertObj = mObj.appendChild(d.createElement("div"));
	alertObj.id = "alertBox";
	if(d.all && !window.opera) alertObj.style.top = document.documentElement.scrollTop + "px";
	alertObj.style.left = (d.documentElement.scrollWidth - alertObj.offsetWidth)/2 + "px";
	alertObj.style.visiblity="visible";

	h1 = alertObj.appendChild(d.createElement("h1"));
	h1.appendChild(d.createTextNode(ALERT_TITLE));

	msg = alertObj.appendChild(d.createElement("p"));
	//msg.appendChild(d.createTextNode(txt));
	msg.innerHTML = txt;

	btn = alertObj.appendChild(d.createElement("a"));
	btn.id = "closeBtn";
	btn.appendChild(d.createTextNode(ALERT_BUTTON_TEXT));
	btn.href = "#";
	btn.focus();
	btn.onclick = function() { removeCustomAlert();return false; }

	alertObj.style.display = "block";
	
}

function removeCustomAlert() {
	document.getElementsByTagName("body")[0].removeChild(document.getElementById("modalContainer"));
}
function ful(){
alert('Alert this pages');
}
        if($privilage == '0'){///added by shubhranshu
            if($role_id == 'ADMN'){///added by shubhranshu
                 if($restriction_flag == '1'){
                     alert('The trainees whom you are about to enrol is part of the restricted list. Please acknowledge to continue !!!'); 
                 }  ///added by shubhranshu
            }///added by shubhranshu
        }else {
            if($privilage == '1'){
                if($restriction_flag == '1'){
                    alert('The trainees whom you are about to enrol is part of the restricted list. Please acknowledge to continue !!!'); 
                 }
            }///added by shubhranshu
        }
        
     //added by shubhranshu on 30 jan 2020 new declaration for trainee enrol  
     <?php if ($this->session->userdata('userDetails')->tenant_id == 'T02' && $this->input->post('account_type') == 'individual'){?>
    $('#declarations').click(function(){
        $status = 1;
        if($('#dec_name').val()==''){
            $status=0;
        }
//        if($('#dec_email').val()==''){
//            $status=0;
//        }
        if($('#dec_mobile').val()==''){
            $status=0;
        }
        if($('#dec_overseas').val()==''){
            $status=0;
        }
        if($('#dec_les_time').val()==''){
            $status=0;
        }
        
        if($status == 1){
            $('#ex1011').hide();
            $('.statuserr').html('');
            $url = $siteurl + "class_trainee/save_declaration_trainee_data";
            $.ajax({
                url: $url,
                type: "post",
                dataType: "json",
                data: {
                    tax_code: '<?php echo $trainee_name->tax_code;?>',
                    type:'INDIVIDUAL',
                    name: $('#dec_name').val(),  
                    email: $('#dec_email').val(),
                    mobile: $('#dec_mobile').val(),
                    user_id:'<?php echo ($this->input->post('taxcode_id') ?? $this->input->post('trainee_id'));?>',
                    class_id:'<?php echo $this->input->post('class');?>',
                    res: $('input[name="dec_res"]:checked').val(),
                    lesson_timing: $('#dec_les_time').val(),
                    overseas: $('#dec_overseas').val()
                },
                success: function(data) {
                   if(data !='1'){
                      $('#ex1011').show();  
                  }
                }
            });
        }else{
             $('.statuserr').html('<span style="color:red">Please fill all the (*) Mark fields to Continue!</span>');
        }
    }); 
     <?php } ?>
       
    });
  
</script>

<!---------- /*  added by shubhranshu for client requirement on 21/03/2019 */-->
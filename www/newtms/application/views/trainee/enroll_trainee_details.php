<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/enroll_trainee_pay_now.js"></script>
<script>
    var $total_amount = <?php echo $result_array['net_fees_due']; ?>;    
    var $class_pymnt_enrol = '<?php echo $class_pymnt_enrol; ?>';    
    var baseurl = '<?php echo base_url(); ?>';
</script>
<div class="col-md-10 right-minheight">     
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Class Trainee Enrollment - Enroll New</h2>
    <h2 class="sub_panel_heading_style">
        <span class="glyphicon glyphicon-th-list"></span>
        Payment Details for
        ' <?php 
            echo ($trainee_data->gender == 'MALE') ? 'Mr. ' : 'Ms. '; 
           ?>
        <?php echo $trainee_data->first_name . ' ' . $trainee_data->last_name; ?>'            
    </h2>             
    <div>        
        <div id="pay_details" class="receipt-div col-md-12">                      

            <table class="table table-striped">
                <tbody>                    
                    <tr>
                        <td class="td_heading" width='25%;'>Unit Fees:</td>
                        <td width='25%;'><label class="label_font">$<?php echo number_format($result_array['unit_fees'], 2, '.', ''); ?></label></td>
                        <td class="td_heading" width='25%;'>Discount@ <span id="discount_rate"><?php echo number_format($result_array['discount_rate'], 2, '.', ''); ?></span>%:</td>
                        <td width='25%;'><span  id="discount_amount"><?php echo '$' . number_format($result_array['discount_amount'], 2, '.', ''); ?></span></td>
                    </tr>
                    <tr>
                        <td class="td_heading">
                            GST Amount:
                        </td>
                        <td>
                            $<span  id="gst_amount"><?php echo number_format($result_array['gst_amount'], 2, '.', ''); ?></span>
                            &nbsp;&nbsp;(@<span  id="gst_rate"><?php echo number_format($result_array['gst_rate'], 2, '.', '') ?></span>%)
                        </td>
                        <td class="td_heading">Net Fees Due:</td>
                        <td ><label class="label_font">$<span  id="net_fees_due"><?php echo number_format($result_array['net_fees_due'], 2, '.', ''); ?></span></label></td>
                    </tr>
              <?php
            $atr = 'id="enroll_pay_now_form" name="enroll_pay_now_form" method="post"';
            echo form_open("class_trainee/enroll_pay_now", $atr);
               
                 $this->session->userdata('userDetails')->user_id;
                 $sales_name=$this->session->userdata('userDetails')->first_name;
               
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
                        <td colspan="2"></td>
                    </tr>
                <?php 
                } ?>
                </tbody>
            </table>

            <?php
            $data = array(
                'id' => 'trainee_name',
                'name' => 'trainee_name',
                'value' => $trainee_data->first_name . ' ' . $trainee_data->last_name,
                'type' => 'hidden'
            );
            echo form_input($data);
            $data = array(
                'id' => 'company_id',
                'name' => 'company_id',
                'value' => $trainee_data->company_id,
                'type' => 'hidden'
            );
            echo form_input($data);
            $data = array(
                'id' => 'course_id',
                'name' => 'course_id',
                'value' => $course_id,
                'type' => 'hidden'
            );
            echo form_input($data);
            $data = array(
                'id' => 'class_id',
                'name' => 'class_id',
                'value' => $class_id,
                'type' => 'hidden'
            );
            echo form_input($data);
            
             if ($trainee_data->company_id != 0) {
                ?>
                <table class="table table-striped">
                    <tbody>
                        <tr>    
                            <td class="td_heading" style="color: red">
                                Enrollment Mode:<span class="required">*</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   
                                <!--added by shubhranshu due to privilage issue for company login on 29/11/2018-->
                                <!--if($salesexec_check || $this->session->userdata('userDetails')->role_id =='ADMN')-->
                                <?php
                                if($salesexec_check || $this->session->userdata('userDetails')->role_id =='ADMN'){
                               
                                $enroll_type_radio = array(
                                    'name' => 'enrolment_mode',
                                    'id' => 'enrolment_mode',
                                    'value' => 'SELF',
                                    'checked' => TRUE,
                                    'class' => 'enrolment_mode'
                                );
                                ?>
                                <?php echo form_radio($enroll_type_radio); ?>&nbsp;&nbsp;&nbsp;Individual &nbsp;&nbsp;&nbsp; <?php } ?>
                                <?php
                                $enroll_type_radio = array(
                                    'name' => 'enrolment_mode',
                                    'id' => 'enrolment_mode',
                                    'value' => 'COMPSPON',
                                    'checked' => FALSE,
                                    'class' => 'enrolment_mode'
                                );
                                ?>
                                <?php echo form_radio($enroll_type_radio); ?>&nbsp;&nbsp;&nbsp; Company
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php
            }
            
            if ($class_pymnt_enrol == 'PDENROL') {
                ?>
                <table class="table table-striped pdenrol_table">
                    <tbody>
                        <tr>
                            <td width="16%" class="td_heading">Mode of Payment:<span class="required">*</span></td>
                            <td width="45%">
                                <?php
//                                $type_options = array('' => 'Select', 'CASH' => 'Cash', 'CHQ' => 'Cheque', 'GIRO' => 'GIRO');
                                 if($tenant_id == 'T02' || $tenant_id == 'T12' || $tenant_id == 'T01'){
                                      //$type_options = array('' => 'Select', 'CASH' => 'Cash', 'NETS'=>'NETS', 'CHQ' => 'Cheque', 'GIRO' => 'GIRO','SFC_SELF'=>'SFC_SELF','SFC_ATO'=>'SFC_ATO');
                                     $type_options = array('' => 'Select', 'CASH' => 'Cash', 'NETS'=>'NETS', 'CHQ' => 'Cheque', 'GIRO' => 'GIRO');
                                 }  else {
                                        $type_options = array('' => 'Select', 'CASH' => 'Cash', 'NETS'=>'NETS', 'CHQ' => 'Cheque', 'GIRO' => 'GIRO');
                                       //$type_options = array('' => 'Select', 'CASH' => 'Cash', 'CHQ' => 'Cheque', 'GIRO' => 'GIRO','SFC_SELF'=>'SFC_SELF','SFC_ATO'=>'SFC_ATO');
                                 }
                              
                                $type_js = 'id="payment_type"';
                                echo form_dropdown('payment_type', $type_options, $this->input->get('payment_type'), $type_js);
                                $data = array(
                                    'id' => 'payment_due_id',
                                    'name' => 'payment_due_id',
                                    'value' => '',
                                    'type' => 'hidden'
                                );
                                echo form_input($data);
                                ?>
                                <span id="payment_type_err"></span>
                            </td>                        
                        </tr>
                        <tr class="sfc_clm" style="display: none;">
                            <td width="25%"> <span id="sfc_claim_err"></span></td>
                        </tr>
                    </tbody>
                </table>            
                <div id="row_dim" style="display:none;">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td width="16%" class="td_heading">Recd. On:<span class="required">*</span></td>
                                    <td width="19%">
                                        <?php
                                        $paid_on = array(
                                            'name' => 'paid_on',
                                            'id' => 'paid_on',
                                            'readonly' => 'readonly',
                                            'value' => $this->input->post('paid_on'),
                                        );
                                        echo form_input($paid_on);
                                        ?>
                                        <span id="paid_on_err"></span>
                                    </td>
                                    <td width="14%" class="td_heading">Cheque Number:<span class="required">*</span></td>
                                    <td width="18%">
                                        <?php
                                        $cheque_number = array(
                                            'name' => 'cheque_number',
                                            'id' => 'cheque_number',
                                            'value' => $this->input->post('cheque_number'),
                                            'maxlength' => 20,
                                            'class' =>'upper_case'
                                        );
                                        echo form_input($cheque_number);
                                        ?>
                                        <span id="cheque_number_err"></span>
                                    </td>
                                    <td width="14%" class="td_heading">Cheque Amount:<span class="required">*</span></td>
                                    <td width="19%">$ <?php
                                        $cheque_amount = array(
                                            'name' => 'cheque_amount',
                                            'id' => 'cheque_amount',
                                            'value' => $this->input->post('cheque_amount'),
                                        );
                                        echo form_input($cheque_amount);
                                        ?>
                                        <span id="cheque_amount_err"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td_heading">Cheque Date:<span class="required">*</span></td>
                                    <td>
                                        <?php
                                        $cheque_date = array(
                                            'name' => 'cheque_date',
                                            'id' => 'cheque_date',
                                            'readonly' => 'readonly',
                                            'value' => $this->input->post('cheque_date'),
                                        );
                                        echo form_input($cheque_date);
                                        ?>
                                        <span id="cheque_date_err"></span>
                                    </td>
                                    <td class="td_heading">Bank Drawn On:<span class="required">*</span></td>
                                    <td colspan="3">
                                        <?php
                                        $bank_name = array(
                                            'name' => 'bank_name',
                                            'id' => 'bank_name',
                                            'value' => $this->input->post('bank_name'),
                                            'maxlength' => 50,
                                            'class' => 'upper_case'
                                        );
                                        echo form_input($bank_name);
                                        ?>
                                        <span id="bank_name_err"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="row_dim3" style="display:none;">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td width="16%" class="td_heading">Recd. On:<span class="required">*</span></td>
                                    <td width="19%">
                                        <?php
                                        $cashpaid_on = array(
                                            'name' => 'cashpaid_on',
                                            'id' => 'cashpaid_on',
                                            'readonly' => 'readonly',
                                            'value' => $this->input->post('cashpaid_on'),
                                        );
                                        echo form_input($cashpaid_on);
                                        ?>
                                        <span id="cashpaid_on_err"></span>
                                    </td>
                                    <td width="14%" class="td_heading">Amount:<span class="required">*</span></td>
                                    <td width="51%">$ <?php
                                        $cash_amount = array(
                                            'name' => 'cash_amount',
                                            'id' => 'cash_amount',
                                            'value' => $this->input->post('cash_amount'),
                                        );
                                        echo form_input($cash_amount);
                                        ?>
                                        <span id="cash_amount_err"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="giro_div" style="display:none;">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td width="14%" class="td_heading">Transaction Date:<span class="required">*</span></td>
                                    <td width="18%">
                                        <?php
                                        $transc_on = array(
                                            'name' => 'transc_on',
                                            'id' => 'transc_on',
                                            'readonly' => 'readonly',
                                            'value' => $this->input->post('transc_on'),
                                        );
                                        echo form_input($transc_on);
                                        ?>
                                        <span id="transc_on_err"></span>
                                    </td>
                                    <td width="14%" class="td_heading">Bank Name:<span class="required">*</span></td>
                                    <td width="19%"><?php
                                        $gbank_name = array(
                                            'name' => 'gbank_name',
                                            'id' => 'gbank_name',
                                            'value' => $this->input->post('gbank_name'),
                                            'style' => 'width:200px',
                                            'maxlength' => '50',
                                            'class' => 'upper_case'
                                        );
                                        echo form_input($gbank_name);
                                        ?>
                                        <span id="gbank_name_err"></span>
                                    </td>
                                    <td width="14%" class="td_heading">GIRO Amount:<span class="required">*</span></td>
                                    <td width="19%">$ <?php
                                        $giro_amount = array(
                                            'name' => 'giro_amount',
                                            'id' => 'giro_amount',
                                            'value' => $this->input->post('giro_amount'),
                                        );
                                        echo form_input($giro_amount);
                                        ?>
                                        <span id="giro_amount_err"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>   
            
            
            
            <!-- SFC starts -->
        <div id="sfc_div" style="display:none;">
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td width="14%" class="td_heading">SFC Claimed On:<span class="required">*</span></td>
                            <td width="18%">
                                <?php
                                $sfcclaim_on = array(
                                    'name' => 'sfcclaim_on',
                                    'id' => 'sfcclaim_on',
                                    'readonly' => 'readonly',
                                    'value' => $this->input->post('sfcclaim_on'),
                                );
                                echo form_input($sfcclaim_on);
                                ?>
                                <span id="sfcclaim_on_err"></span>
                            </td>
                           <td width="14%" class="td_heading">SFC(SELF) Amount:<span class="required">*</span></td>
                            <td width="19%">$ <?php
                                $sfc_amount = array(
                                    'name' => 'sfc_amount',
                                    'id' => 'sfc_amount',
                                    'class'  =>'sfc_amount',
                                    'value' => $this->input->post('sfc_amount'),
                                );
                                echo form_input($sfc_amount);
                                ?>
                                <span id="sfc_amount_err"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="sfcato_div" style="display:none;">
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td width="14%" class="td_heading">SFC Claimed On:<span class="required">*</span></td>
                            <td width="18%">
                                <?php
                                $sfcatoclaim_on = array(
                                    'name' => 'sfcatoclaim_on',
                                    'id' => 'sfcatoclaim_on',
                                    'readonly' => 'readonly',
                                    'value' => $this->input->post('sfcatoclaim_on'),
                                );
                                echo form_input($sfcatoclaim_on);
                                ?>
                                <span id="sfcclaim_on_err"></span>
                            </td>
                           <td width="14%" class="td_heading">SFC(ATO) Amount:<span class="required">*</span></td>
                            <td width="19%">$ <?php
                                $sfcato_amount = array(
                                    'name' => 'sfcato_amount',
                                    'id' => 'sfcato_amount',
                                    'class'  =>'sfcato_amount',
                                    'value' => $this->input->post('sfcato_amount'),
                                );
                                echo form_input($sfcato_amount);
                                ?>
                                <span id="sfcato_amount_err"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- SFC Ends -->
        <br>
         <!-- other payment for SFC -->
    <div class="other_payment" id="other_payment" style="display:none;">
        <table class="table table-striped ">
            <tbody>
                <tr>
                    <td width="16%" class="td_heading">Other Mode of Payment:<span class="required">*</span></td>
                    <td width="84%">
                        <?php
                        if($tenant_id == 'T01' || $tenant_id == 'T02' || $tenant_id == 'T12')
                        {
                           $type_options1 = array('' => 'Select', 'CASH1' => 'Cash', 'CHQ1' => 'Cheque', 'GIRO1' => 'GIRO', 'NETS1'=> 'NETS'); 
                        }else{
                          $type_options1 = array('' => 'Select', 'CASH1' => 'Cash', 'CHQ1' => 'Cheque', 'GIRO1' => 'GIRO');  
                        }
                       // $type_options1 = array('' => 'Select', 'CASH1' => 'Cash', 'CHQ1' => 'Cheque', 'GIRO1' => 'GIRO');
                        $type_js = 'id="payment_type1"';
                        echo form_dropdown('payment_type1', $type_options1, $this->input->get('payment_type1'), $type_js);
                        $data = array(
                            'id' => 'payment_due_id1',
                            'name' => 'payment_due_id1',
                            'value' => '',
                            'type' => 'hidden'
                        );
                        echo form_input($data);
                        ?>
                        <span id="payment_type_err"></span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
          <div id="row_dim1" style="display:none;">
               <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td width="16%" class="td_heading">Cheque Recd. On:<span class="required">*</span></td>
                            <td width="19%">
                                <?php
                                $paid_on1 = array(
                                    'name' => 'paid_on1',
                                    'id' => 'paid_on1',
                                    'readonly' => 'readonly',
                                    'value' => $this->input->post('paid_on1'),
                                );
                                echo form_input($paid_on1);
                                ?>
                                <span id="paid_on1_err"></span>
                            </td>
                            <td width="14%" class="td_heading">Cheque Number:<span class="required">*</span></td>
                            <td width="18%">
                                <?php
                                $cheque_number1 = array(
                                    'name' => 'cheque_number1',
                                    'id' => 'cheque_number1',
                                    'value' => $this->input->post('cheque_number1'),
                                    'maxlength' => 20
                                );
                                echo form_input($cheque_number1);
                                ?>
                                <span id="cheque_number1_err"></span>
                            </td>
                            <td width="14%" class="td_heading">Cheque Amount:<span class="required">*</span></td>
                            <td width="19%">$ <?php
                                $cheque_amount1 = array(
                                    'name' => 'cheque_amount1',
                                    'id' => 'cheque_amount1',
                                    'value' => $this->input->post('cheque_amount1'),
                                );
                                echo form_input($cheque_amount1);
                                ?>
                                <span id="cheque_amount1_err"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="td_heading">Cheque Date:<span class="required">*</span></td>
                            <td>
                                <?php
                                $cheque_date1 = array(
                                    'name' => 'cheque_date1',
                                    'id' => 'cheque_date1',
                                    'readonly' => 'readonly',
                                    'value' => $this->input->post('cheque_date1'),
                                );
                                echo form_input($cheque_date1);
                                ?>
                                <span id="cheque_date1_err"></span>
                            </td>
                            <td class="td_heading">Bank Drawn On:<span class="required">*</span></td>
                            <td colspan="3">
                                <?php
                                $bank_name1 = array(
                                    'name' => 'bank_name1',
                                    'id' => 'bank_name1',
                                    'value' => $this->input->post('bank_name1'),
                                    'maxlength' => 50
                                );
                                echo form_input($bank_name1);
                                ?>
                                <span id="bank_name1_err"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="row_dim31" style="display:none;">
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td width="16%" class="td_heading">Cash Recd. On:<span class="required">*</span></td>
                            <td width="19%">
                                <?php
                                $cashpaid_on1 = array(
                                    'name' => 'cashpaid_on1',
                                    'id' => 'cashpaid_on1',
                                    'readonly' => 'readonly',
                                    'value' => $this->input->post('cashpaid_on1'),
                                );
                                echo form_input($cashpaid_on1);
                                ?>
                                <span id="cashpaid_on1_err"></span>
                            </td>
                            <td width="14%" class="td_heading">Cash Amount:<span class="required">*</span></td>
                            <td width="51%">$ <?php
                                $cash_amount1 = array(
                                    'name' => 'cash_amount1',
                                    'id' => 'cash_amount1',
                                    'value' => $this->input->post('cash_amount1'),
                                );
                                echo form_input($cash_amount1);
                                ?>
                                <span id="cash_amount1_err"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="giro_div1" style="display:none;">
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td width="14%" class="td_heading">Transaction Date:<span class="required">*</span></td>
                            <td width="18%">
                                <?php
                                $transc_on1 = array(
                                    'name' => 'transc_on1',
                                    'id' => 'transc_on1',
                                    'readonly' => 'readonly',
                                    'value' => $this->input->post('transc_on1'),
                                );
                                echo form_input($transc_on1);
                                ?>
                                <span id="transc_on1_err"></span>
                            </td>
                            <td width="14%" class="td_heading">Bank Name:<span class="required">*</span></td>
                            <td width="19%"><?php
                                $gbank_name1 = array(
                                    'name' => 'gbank_name1',
                                    'id' => 'gbank_name1',
                                    'value' => $this->input->post('gbank_name1'),
                                    'style' => 'width:200px',
                                    'maxlength' => '50',
                                );
                                echo form_input($gbank_name1);
                                ?>
                                <span id="gbank_name1_err"></span>
                            </td>
                            <td width="14%" class="td_heading">GIRO Amount:<span class="required">*</span></td>
                            <td width="19%">$ <?php
                                $giro_amount1 = array(
                                    'name' => 'giro_amount1',
                                    'id' => 'giro_amount1',
                                    'value' => $this->input->post('giro_amount1'),
                                );
                                echo form_input($giro_amount1);
                                ?>
                                <span id="giro_amount1_err"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <!-- ends other payment --> 
            
            
            
            <!---->
            
            
                <?php
            }           
            if ($class_pymnt_enrol == 'PDENROL') {
                ?>
                <br>
                <span class="required required_i">* Required Fields</span>
                <br/>
                <?php
                $data = array(
                    'id' => 'optype',
                    'name' => 'optype',
                    'value' => 'PAYNOW',
                    'type' => 'hidden'
                );
                echo form_input($data);
                ?>
                <span style="float:right;" id='declra'>                                        
                    <button type="submit" name="submit" value="enroll_now" class="pay_now btn btn-sm btn-info enroll_now_btn" style="float: right;"><strong>Pay Now</strong></button>                                                                    
                </span>
                <?php
            } else {
                ?>
                <br/>
                <?php
                $data = array(
                    'id' => 'optype',
                    'name' => 'optype',
                    'value' => 'PAYLATER',
                    'type' => 'hidden'
                );
                echo form_input($data);
                ?>
                <span style="float:right;" id='declra'> 
                        <button type="submit" name="submit" value="book_now" class="book_now btn btn-sm btn-info" style="float: right;margin-left: 10px;"><strong>Book Now</strong></button>
                         
                </span>
                <?php
            }
            echo form_close();
            ?>
                <!--added by shubhranshu to prevent trainee enrollment for paid company invoice-->
            <div id="paiddiv" style="display:none;padding: 10px;font-weight: bold;" class="text-center bg-danger">Invoice is already paid for this company, If you wants to enroll kindly refund the invoice
                <center><a href='<?php echo base_url(); ?>accounting/refund_payment' class='btn btn-sm btn-info'>Refund Now</a></center>
            </div>

           
            
            
        </div>

    </div> 
    
</div>

<!----------------modal by ssp start----------------------->
<?php if ($this->session->userdata('userDetails')->tenant_id == 'T02' && $trainee_data->company_id =='0'){$show='display:block';}else{$show='display:none';}?>
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
                            <input type="text" id="dec_name" class="form-control" value="<?php echo $trainee_data->first_name;?>" required>
                            
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
<script>
    $(document).ready(function(){
         $('#declra').show();
        $('.book_now').click(function(){
            $(this).hide();
        });
        
    //added by shubhranshu on 30 jan 2020 new declaration for trainee enrol 
   
    <?php if ($this->session->userdata('userDetails')->tenant_id == 'T02' && $trainee_data->company_id =='0'){?>
    //$('#declra').hide();
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
            $('#declra').show();
            $('.statuserr').html('');
            $siteurl = '<?php echo site_url(); ?>';
            $url = $siteurl + "class_trainee/save_declaration_trainee_data";
            $.ajax({
                url: $url,
                type: "post",
                dataType: "json",
                data: {
                    tax_code: '<?php echo $trainee_name->tax_code;?>',
                    type:'DIRECT_REG',
                    name: $('#dec_name').val(),  
                    email: $('#dec_email').val(),
                    mobile: $('#dec_mobile').val(),
                    user_id:'<?php echo $this->session->userdata('new_trainee_user_id');?>',
                    class_id:'<?php echo $class_id;?>',
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
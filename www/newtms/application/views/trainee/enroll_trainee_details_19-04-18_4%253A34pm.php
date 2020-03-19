<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/enroll_trainee_pay_now.js"></script>
<script>
    var $total_amount = <?php echo $result_array['net_fees_due']; ?>;    
    var $class_pymnt_enrol = '<?php echo $class_pymnt_enrol; ?>';    
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
                                <?php
                                $enroll_type_radio = array(
                                    'name' => 'enrolment_mode',
                                    'id' => 'enrolment_mode',
                                    'value' => 'SELF',
                                    'checked' => TRUE,
                                    'class' => 'enrolment_mode'
                                );
                                ?>
                                <?php echo form_radio($enroll_type_radio); ?>&nbsp;&nbsp;&nbsp;Individual &nbsp;&nbsp;&nbsp;
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
                                $type_options = array('' => 'Select', 'CASH' => 'Cash', 'CHQ' => 'Cheque', 'GIRO' => 'GIRO');
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
                <span style="float:right;">                                        
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
                <span style="float:right;"> 
                        <button type="submit" name="submit" value="book_now" class="book_now btn btn-sm btn-info" style="float: right;margin-left: 10px;"><strong>Book Now</strong></button>
                         
                </span>
                <?php
            }
            echo form_close();
            ?>                
        </div>
    </div>         
</div>
<script>
    $(document).ready(function(){
        $('.book_now').click(function(){
            $(this).hide();
        });
    });
</script>
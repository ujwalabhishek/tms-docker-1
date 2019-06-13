<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/updatepayment.js"></script>
<!--<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/updatepayment_all_tenant.js"></script>-->
<div class="col-md-10 right-minheight">
    <?php
    if ($this->session->flashdata('success')) {
        echo '<div class="success">' . $this->session->flashdata('success') . '</div>';
    }
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    ?>
    <?php echo validation_errors('<div class="error1">', '</div>'); ?> 
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-refresh"></span> Accounting - Update Payment</h2>
    <div class="table-responsive">
        <?php
        $atr = 'id="search_form" name="search_form" method="post"';
        echo form_open("class_trainee/updatepayment", $atr);
        ?>  
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading" width="20%">Select Account Type:</td>
                    <td colspan="3">
                        <?php
                        $options = array(
                            'individual' => 'Individual',
                            'company' => 'Company'
                        );
                        $js = 'id="account_type"';
                        echo form_dropdown('account_type', $options, '', $js);
                        ?>
                    </td>
                </tr>
                <tr class="row_dimm9">
                    <td class="td_heading">&nbsp;&nbsp;
                        <?php
                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => '1',
                            'checked' => true
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp;Invoice No./ Receipt No.:
                    </td>
                    <td colspan="3">
                        <?php
                        $options = array(
                            '' => 'Select',
                        );
                        foreach ($invoices as $row) {
                            $options[$row['value']] = $row['label'];
                        }
                        $js = 'id="invoice_id"';
                        echo form_dropdown('invoice_id', $options, '', $js);
                        ?>
                        <span id="invoice_err"></span>
                    </td>
                </tr>
                <tr class="row_dimm9">
                    <td class="td_heading" >
                        &nbsp;&nbsp;
                        <?php
                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => '2',
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp;NRIC/FIN No.:
                    </td>
                    <td>
                        <?php
                        $data = array(
                            'id' => 'taxcode',
                            'name' => 'taxcode',
                            'class' => 'upper_case',
                            'value' => $this->input->post('taxcode'),
                            'style' => 'width:200px',
                        );
                        echo form_input($data);
                        $data = array(
                            'id' => 'taxcode_id',
                            'name' => 'taxcode_id',
                            'type' => 'hidden',
                            'value' => $this->input->post('taxcode_id')
                        );
                        echo form_input($data);
                        ?>
                        <span id="taxcode_err"></span>
                    </td>
                    <td class="td_heading">&nbsp;&nbsp;
                        <?php
                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => '3',
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp;Trainee Name.:
                    </td>
                    <td ><?php
                        $data = array(
                            'id' => 'trainee',
                            'name' => 'trainee',
                            'class' => 'upper_case',
                            'value' => $this->input->post('trainee'),
                            'style' => 'width:300px',
                        );
                        echo form_input($data);
                        $data = array(
                            'id' => 'trainee_id',
                            'name' => 'trainee_id',
                            'type' => 'hidden',
                            'value' => $this->input->post('trainee_id')
                        );
                        echo form_input($data);
                        ?>
                        <span id="trainee_err"></span>
                    </td>
                </tr>
                <tr class="company_td" style="display: none;">
                    <td class="td_heading">Company :</td>
                    <td colspan="3">
                        <?php
                        $options = array();
                        $options[''] = 'Select';
                        foreach ($companies as $row) {
                            $options[$row->company_id] = $row->company_name;
                        }
                        
                        $tenant_details = fetch_tenant_details($this->session->userdata('userDetails')->tenant_id);
                        $options[$tenant_details->tenant_id] = $tenant_details->tenant_name;

                        $js = ' id="company" style="width:700px;"';
                        echo form_dropdown('company', $options, '', $js);
                        ?>   
                        <span id="company_err"></span>
                    </td>
                </tr>
                <tr class="company_td" style="display: none;">
                    <td class="td_heading" width="20%">Invoice No./ Receipt No.:</td>
                    <td colspan="3">
                        <?php
                        $options = array(
                            '' => 'Select',
                        );
                        foreach ($company_invoices as $row) {
                            $options[$row['value']] = $row['label'];
                        }
                        $js = 'id="company_invoice_id" style="width:250px;"';
                        echo form_dropdown('company_invoice_id', $options, '', $js);
                        ?>
                        <span id="company_invoice_err"></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4"> <div class="pull-right">
                            <button type="button" class="search_button btn btn-xs btn-primary no-mar">
                                <span class="glyphicon glyphicon-search"></span>
                                Search
                            </button>
                        </div></td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
    <br/>
    <div class="trainee_div" style="display: none;">
        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-user"></span> Select Trainee </h2> 
        <div class="trainee_alert"></div>
        <div class="table-responsive">
            <table class="table table-striped trainee_table">
                <thead>
                    <tr>
                        <th class="th_header">Sl#.</th>
                        <th class="th_header">Name</th>
                        <th class="th_header">NRIC/FIN No.</th>
                        <th class="th_header">Course / Class</th>
                        <th class="th_header">Amout Due</th>
                        <th class="th_header">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <br class="amountdetails_div" style="display: none;"/>
    <div style="clear:both;"></div><br class="amountdetails_div" style="display: none;">
    <div class="amountdetails_div" style="display: none;">
        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Amount Receivable Details 
            <span class="label label-default push_right black-btn">
                <a class="small_text1 view_invoice_blocked"><span class="glyphicon glyphicon-eye-open"></span> View/Print Invoice</a></span> &nbsp;&nbsp; 
            <span id="gyap" class="label label-default push_right black-btn">
                <a href="#ex3" rel="modal:open" class="small_text1 payment_recd_view"><span class="glyphicon glyphicon-circle-arrow-right"></span> Payments Received</a></span>
        </h2>
        <div style="clear:both;"></div>
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td class="td_heading" width="120px;">Course :</td>
                        <td ><label class="label_font"><span class="invoice_course_name"></span></label>
                        </td>
                        <td class="td_heading" width="165px;">Class :</td>
                        <td colspan="3"><label class="label_font"><span class="invoice_class_name"></span></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Class Fees:</td>
                        <td ><label class="label_font">$<span class="invoice_class_fees"></span></label>
                        </td>
                        <td class="td_heading"><span class="invoice_discount_label"></span> Discount @ <span class="invoice_discount_rate"></span>%:</td>
                        <td><label class="label_font">$<span class="invoice_discount_amount"></span></label></td>
                        <td class="td_heading">Subsidy:</td>
                        <td><label class="label_font">$<span class="invoice_subsidy_amount"></span></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Subsidy Type:</td>
                        <td><label class="label_font"><span class="invoice_subsidy_type"></span></label></td>
                        
                        <td class="td_heading">GST @ <span class="invoice_gst_rate"></span>% (<span class="invoice_gst_label"></span>):</td>
                        <td><label class="label_font">$<span class="invoice_total_gst"></span></label></td>
                        <td class="td_heading" style="color:blue;">Net Due:</td>
                        <td colspan="3"><label class="label_font" style="color:blue;">$<span class="invoice_net_due"></span></label></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <span class="red">**: All amounts are in SGD</span>
    </div>
    <?php
    $atr = 'id="updatepaymentform" name="updatepaymentform" method="post"';
    echo form_open("accounting/update_payment", $atr);
    ?>  
    <div class="companyamounts_display" style="display: none;">
        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Amount Receivable Details - <span style='color: blue;'>'<span class='comp_inv_data'></span>'</span>
            <div class="pull-right">  
                <span class="label label-default push_right black-btn"><a href="#ex13" rel="modal:open" class="small_text1 view_company_invoice"><span class="glyphicon glyphicon-eye-open"></span> View/Print Invoice</a></span>
                <span id="gyap" class="label label-default push_right black-btn  mar-right">
                    <a href="javascript:;" class="small_text1 company_payment_recd_view"> 
                        <span class="glyphicon glyphicon-circle-arrow-right"></span> Payments Received
                    </a>
                </span></div>
        </h2>
        <div style="clear:both;"></div>
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td class="td_heading">Course :</td>
                        <td width="19%"><label class="label_font"><span class="c_invoice_course_name"></span></label>
                        </td>
                        <td class="td_heading">Class :</td>
                        <td colspan="3"><label class="label_font"><span class="c_invoice_class_name"></span></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Total Invoice Fees:</td>
                        <td width="19%"><label class="label_font">$<span class="c_invoice_class_fees"></span></label>
                        </td>
                        <td class="td_heading">Total Discount @ <span class="c_invoice_discount_rate"></span>% (<span class="c_invoice_discount_label"></span>):</td>
                        <td><label class="label_font">$<span class="c_invoice_discount_amount"></span></label></td>
                        <td class="td_heading">Total Subsidy:</td>
                        <td><label class="label_font">$<span class="c_invoice_subsidy_amount"></span></label></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="td_heading">GST @ <span class="c_invoice_gst_rate"></span>% (<span class="c_invoice_gst_label"></span>):</td>
                        <td><label class="label_font">$<span class="c_invoice_total_gst"></span></label></td>
                        <td class="td_heading" style="color:black;">Orignal Due:</td>
                        <td colspan="2"><label class="label_font" style="color:black;">$<span class="c_invoice_net_due"></span></label></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="td_heading">Total Received:</td>
                        <td><label class="label_font">$<span class="c_total_received"></span></label></td>
                        <td class="td_heading error">Current Due:</td>
                        <td colspan="2"><label class="label_font error">$<span class="c_current_due"></span></label></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Payments Due - Trainee list</h2>
        <div class="table-responsive payment_scroll">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="th_header">Sl#.</th>
                        <th class="th_header">Trainee Name / (NRIC/FIN No.)</th>
                        <th class="th_header">Original Due</th>
                        <th class="th_header">Amt. Recd.</th>
                        <th class="th_header">Amt. Refnd.</th>
                        <th class="th_header">Amount Due</th>
                        <th class="th_header">Payment Type</th>
                        <th class="th_header">Status</th>
                    </tr>
                </thead>
                <tbody class="ctrainees_tbody">
                </tbody>
            </table>
        </div>
        <div class="green">**: Rescheduled</div>
        <div class="red">**: All amounts are in SGD</div>
    </div>
    <div style="clear:both;"></div><br>
    <div class="amountdetails_div companyamounts_display common_pay" style="display: none;">
        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Payment Details </h2>
       
        <table class="table table-striped ">
            <tbody>
                <tr>
                    <td width="16%" class="td_heading">Mode of Payment:<span class="required">*</span></td>
                        <td width="84%">

                            <?php

//                            $type_options = array('' => 'Select', 'CASH' => 'Cash', 'CHQ' => 'Cheque', 'GIRO' => 'GIRO','SFC_SELF'=>'SFC_SELF','SFC_ATO'=>'SFC_ATO');
                             if($tenant_id == 'T02' || $tenant_id == 'T12' || $tenant_id == 'T01')
                            {
                                $type_options = array('' => 'Select', 'CASH' => 'Cash', 'NETS'=>'NETS', 'CHQ' => 'Cheque', 'GIRO' => 'GIRO','SFC_SELF'=>'SFC_SELF','SFC_ATO'=>'SFC_ATO');
                            }else{
                                $type_options = array('' => 'Select', 'CASH' => 'Cash', 'CHQ' => 'Cheque', 'GIRO' => 'GIRO','SFC_SELF'=>'SFC_SELF','SFC_ATO'=>'SFC_ATO');
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
       
        
        <br>
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
                                    'maxlength' => 20
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
                                    'maxlength' => 50
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

                        $type_options1 = array('' => 'Select', 'CASH1' => 'Cash', 'CHQ1' => 'Cheque', 'GIRO1' => 'GIRO');
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
        <span class="required required_i">* Required Fields</span>
        <div class="button_class99">
            <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-retweet"></span>&nbsp;Update Payment Details</button> &nbsp; &nbsp; 
        </div>
    </div>
   
    <?php echo form_close(); ?>
</div>
<div class="modal_333 modal_payment_recd" id="ex3" style="display:none;">
    <h2 class="panel_heading_style">Payment Received Details for '<span class="p_invoice_company_name"></span>'</h2>
    <div class="table-responsive">
        <table class="table">
            <tbody>
                <tr>
                    <td class="td_heading">Course:</td>
                    <td><span class="p_invoice_course_name"></span></td>
                    <td class="td_heading">Class:</td>
                    <td colspan="3"><span class="p_invoice_class_name"></span></td>
                </tr>
                <tr>
                    <td class="td_heading">Invoice #:</td>
                    <td><span class="p_invoice_id"></span></td>
                    <td class="td_heading">Invoice Dt:</td>
                    <td><span class="p_invoice_dated"></span></td>
                    <td class="td_heading">Invoice Amount:</td>
                    <td>$<span class="p_invoice_amount"></span></td>
                </tr>
                <tr>
                    <td class="td_heading"><span class="p_invoice_discount_label"></span> Discount @<span class="p_invoice_discount_rate"></span>%:</td>
                    <td>$<span class="p_invoice_discount_amount"></span></td>
                    <td class="td_heading">Subsidy:</td>
                    <td>$<span class="p_invoice_subsidy_amount"></span></td>
                    <td class="td_heading">GST @ (<span class="p_invoice_gst_label"></span>)<span class="p_invoice_gst_rate"></span>%:</td>
                    <td>$<span class="p_invoice_total_gst"></span></td>
                </tr>
            </tbody>
        </table>
    </div><br>
    <div class="table-responsive payment_scroll" style="height: 122px;">
        <table style="width:60%; margin:0 auto;" class="table table-striped payment_received">
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
<div class="modal0000" id="ex12" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Alert Message</h2>
    No Payment received for Invoice '<span class='alert_invoice_id'></span>'
    <div class="popup_cancel popup_cancel001">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
</p>
</div>
<div class="modal-inv" id="ex13" style="display:none;width:25%">
    <p>
    <h2 class="panel_heading_style">Select Invoice Type</h2>
    <div>
        <?php
        $data = array('name' => 'select_invoice_print', 'class' => 'select_invoice_print');
        echo form_radio($data, 1, TRUE, $extra);
        echo '&nbsp; &nbsp; All'
        ?>
    </div>
    <div>
        <?php
        $data = array('name' => 'select_invoice_print', 'class' => 'select_invoice_print');
        echo form_radio($data, 2, FALSE, $extra);
        echo '&nbsp; &nbsp; With Subsidy';
        ?>
        <span id="with_subsidy_err"></span>
    </div>
    <div>
        <?php
        $data = array('name' => 'select_invoice_print', 'class' => 'select_invoice_print');
        echo form_radio($data, 3, FALSE, $extra);
        echo '&nbsp; &nbsp; Without Subsidy';
        ?>
        <span id="without_subsidy_err"></span>
    </div>
     <div>
        <?php
        $data = array('name' => 'select_invoice_print', 'class' => 'select_invoice_print');
        echo form_radio($data, 4, FALSE, $extra);
        echo '&nbsp; &nbsp; Foreigner Invoice';
        ?>
        <span id="foreigner_invoice_err"></span>
    </div>
    <div class="popup_cancel popup_cancel001">
        <span href="#" rel="modal:close"><button class="btn btn-primary print_company_invoice" type="button">Print</button></span></div>
</p>
</div>
<div class="modal0000" id="ex101" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Alert Message</h2>
    There is an excess payment of SGD <span class="excess_amount"></span>.<br>
    Do you wish to continue with the excess payment?<br>
    <div class="popup_cancel popup_cancel001" style="margin-left: 10px;">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">No</button></a></div>
    <div class="popup_cancel popup_cancel001">
        <a href="javascript:;" id="excess_ok"><button class="btn btn-primary" type="button">Yes</button></a></div>
</p>
</div>
<script>
    $(document).ready(function() {
        $(function() {
            $('.typee').change(function() {
                $('.row_dimm').hide();
                if (this.options[this.selectedIndex].value == 'parcel5') {
                    $('.row_dimm').show();
                    $('.table_new_style').css('margin-bottom', '0px');
                }
            });
        });
        $(function() {
            $('.typee').change(function() {
                $('.row_dimm9').hide();
                if (this.options[this.selectedIndex].value == 'parcel6') {
                    $('.row_dimm9').show();
                    $('.table_new_style').css('margin-bottom', '0px');
                }
            });
        });
        
        $np_inv_id = '<?php echo $this->input->get('invoice_id'); ?>';
        
        if ($np_inv_id.length > 0) {
            $np_acc_type = '<?php echo $this->input->get(enrol_mode); ?>';
            $('#account_type').val($np_acc_type);
            if ($np_acc_type == 'company') {
                $np_comp_id = '<?php echo $this->input->get(company_id); ?>';
                $('#account_type').trigger('change');
                $('#company').val($np_comp_id);
                $('#company').trigger('change');
                $('#company_invoice_id').val($np_inv_id);
            } else {
                $('#invoice_id').val($np_inv_id);
            }
            $('.search_button').trigger('click');
            if ($np_acc_type == 'individual') {
                $('.trainee_invoice').attr('checked', 'checked');
                $('.trainee_invoice').trigger('change');
            }
        }
        
    });
</script>
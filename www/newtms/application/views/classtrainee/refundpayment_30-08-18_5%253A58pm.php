<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/refundpayment.js"></script>
<div class="col-md-10">
    <?php
    if ($this->session->flashdata('success')) {
        echo '<div class="success">' . $this->session->flashdata('success') . '</div>';
    }
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    ?>
    <?php echo validation_errors('<div class="error1">', '</div>'); ?> 
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-circle-arrow-left"></span> Accounting - Refund Payment</h2>
    <?php
    $atr = 'id="search_form" name="search_form" method="post"';
    echo form_open("class_trainee/updatepayment", $atr);
    ?>
    <div class="table-responsive">  
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="20%" class="td_heading">Select Account Type:</td>
                    <td width="80%" colspan="2">
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
                        &nbsp;&nbsp;Invoice No./ Receipt No.:</td>
                    <td>
                        <?php
                        $options = array(
                            '' => 'Select',
                        );
                        foreach ($invoices as $row) {
                            $options[$row['value']] = $row['label'];
                        }
                        $js = 'id="invoice_id" style="width:115px;"';
                        echo form_dropdown('invoice_id', $options, '', $js);
                        ?>
                    </td>
                    <td colspan="">
                        <div class="row_dimm9 min-mar">
                            <table class="no_border_table">
                                <tbody>
                                    <tr>
                                        <td class="td_heading">&nbsp;&nbsp;
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
                                            <?php
                                            $data = array(
                                                'id' => 'taxcode',
                                                'name' => 'taxcode',
                                                'class'=>'upper_case',
                                                'value' => $this->input->post('taxcode')
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
                                            <?php
                                            $data = array(
                                                'id' => 'trainee',
                                                'name' => 'trainee',
                                                'class'=>'upper_case',
                                                'value' => $this->input->post('trainee')
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
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr class="row_dimm9">
                    <td class="no-bor"></td>
                    <td colspan="2"  class="no-bor">
                        <span id="invoice_err"></span>
                        <span id="taxcode_err"></span>
                        <span id="trainee_err"></span>
                    </td>
                </tr>
                <tr class="company_td" style="display: none;">
                    <td class="td_heading">Company :</td>
                    <td colspan="2">
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
                    <td><span class="td_heading">&nbsp;&nbsp;Invoice No./ Receipt No.:&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                    <td colspan="2">
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
                    <td colspan="3">
                        <div class="push_right">
                            <button type="button" class="search_button btn btn-xs btn-primary no-mar">
                                <span class="glyphicon glyphicon-search"></span>
                                Search
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php echo form_close(); ?>
    <div style="clear:both;"></div>
    <div class="trainee_div" style="display: none;">
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/personal_details.png"/> Select Trainee </h2> 
        <div class="trainee_alert"></div>
        <div class="table-responsive">
            <table class="table table-striped trainee_table">
                <thead>
                    <tr>
                        <th class="th_header">&nbsp;</th>
                        <th class="th_header">NRIC/FIN No.</th>
                        <th class="th_header">Trainee Name</th>
                        <th class="th_header">Course / Class</th>
                        <th class="th_header">Invoice Amt.</th>
                        <th class="th_header">Amt. Recd.</th>
                        <th class="th_header">Refund Amt.</th>
                        <th class="th_header">Refundable Amt.</th>
                        <th class="th_header">Status</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div class="amountdetails_div" style="display: none;">
        <br/>
        <h2 class="sub_panel_heading_style">
            <span class="glyphicon glyphicon-circle-arrow-right"></span> Payment Details
            <div class="pull-right">
                <a href="#ex31" rel="modal:open" class="label label-default push_right black-btn"><span class="glyphicon glyphicon-file"></span>Refund</a>
                <a href="#ex8" rel="modal:open" class="label label-default push_right black-btn mar-right"><span class="glyphicon glyphicon-circle-arrow-right"></span> 
                    Payment Recd.</a>
                <a class="label label-default push_right black-btn print_href mar-right"><span class="glyphicon glyphicon-eye-open"></span> View/Print Invoice</a>
            </div>
        </h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td  class="td_heading">Invoice Amount:</td>
                        <td ><label class="label_font">$<span class="pay_total_invoice_amount"></span></label></td>
                        <td colspan="2" class="td_heading">GST @ <span class="pay_gst_rate"></span>% (<span class="pay_gst_label"></span>):</td>
                        <td colspan="2">$<span class="pay_total_gst"></span></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Class Fees:</td>
                        <td><label class="label_font">$<span class="pay_class_fees"></span></label>
                        </td>
                        <td class="td_heading"><span class="pay_discount_label"></span> Discount @ <span class="pay_discount_rate"></span>%:</td>
                        <td><label class="label_font">$<span class="pay_discount_amount"></span></label></td>
                        <td class="td_heading">Subsidy Amount:</td>
                        <td><label class="label_font">$<span class="pay_subsidy_amount"></span></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Subsidy Type:</td>
                        <td colspan="5"><span class="pay_subsidy_type"></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="red">**: All amounts are in SGD</div>
    </div>
    <?php
    $atr = 'id="updatepaymentform" name="updatepaymentform" method="post"';
    echo form_open("accounting/refund_payment", $atr);
    ?>  
    <div class="companyamounts_display" style="display: none;">
        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Amt. Recd. / Refund Details<div class="pull-right">  
                <a class="label label-default push_right black-btn company_payment_refund_view" href="javascript:;"><span class="glyphicon glyphicon-file"></span>Refund</a>
                <span class="label label-default push_right black-btn  mar-right"><a href="#ex13" rel="modal:open" class="small_text1 view_company_invoice"><span class="glyphicon glyphicon-eye-open"></span> View/Print Invoice</a></span>
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
                        <td colspan="4"><label class="label_font">$<span class="c_invoice_total_gst"></span></label></td>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading" style="color:blue;">Total Invoice Amt.: </td>
                        <td style="color:blue;"><label class="label_font">$<span class="c_invoice_amount"></span></label>
                        <td class="td_heading" style="color:blue;">Total Amt. Recd.: </td>
                        <td colspan="3" style="color:blue;"><label class="label_font">$<span class="c_total_amt_recd"></span></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading error"><b> Total Pending Receivables: </b></td>
                        <td><label class="label_font error">$<span class="c_tot_pend_recv"></span></label></td>
                        <td class="td_heading error"><b>Total Refunded Amt.: </b></td>
                        <td colspan="3"><label class="label_font error">$<span class="c_refund_amt"></span></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading error"><b> Total Refundable Amt.: </b></td>
                        <td><label class="label_font error">$<span class="c_tot_refundable_amt"></span></label></td>
                        <td class="td_heading" style="color:blue;"><b>Over Payment Recd.: </b></td>
                        <td colspan="3"><label class="label_font" style="color:blue;">$<span class="c_over_recd"></span></label></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Trainee Refund Breakup Details</h2>
        <div class="table-responsive payment_scroll">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="th_header">Sl#.</th>
                        <th class="th_header">NRIC/FIN No.</th>
                        <th class="th_header" width="15%">Trainee Name</th>
                        <th class="th_header">Original Due</th>
                        <th class="th_header">Amt. Recd.</th>
                        <th class="th_header">Refund Amt.</th>
                        <th class="th_header">Refundable Amt.</th>
                        <th class="th_header">Refund Type</th>
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
    <div class="amountdetails_div  companyamounts_display common_pay" style="display: none;">
        <div>
            <div style="clear:both;"></div>
            <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-circle-arrow-left"></span> Refund Detail</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td class="td_heading">Mode of Payment:<span class="required">*</span></td>
                            <td>
                                <?php
//                                $type_options = array('' => 'Select', 'CASH' => 'Cash', 'CHQ' => 'Cheque','SFC_SELF'=>'SFC_SELF','SFC_ATO'=>'SFC_ATO');
                                 if($tenant_id == 'T02' || $tenant_id == 'T12' || $tenant_id == 'T01')
                                {
                                   $type_options = array('' => 'Select', 'CASH' => 'Cash','NETS'=>'NETS', 'CHQ' => 'Cheque','SFC_SELF'=>'SFC_SELF','SFC_ATO'=>'SFC_ATO'); 
                                }else{
                                    $type_options = array('' => 'Select', 'CASH' => 'Cash', 'CHQ' => 'Cheque','SFC_SELF'=>'SFC_SELF','SFC_ATO'=>'SFC_ATO');
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

                                $data = array(
                                    'id' => 'trainee_hidden_id',
                                    'name' => 'trainee_hidden_id',
                                    'value' => '',
                                    'type' => 'hidden'
                                );
                                echo form_input($data);

                                $data = array(
                                    'id' => 'company_hidden_id',
                                    'name' => 'company_hidden_id',
                                    'value' => '',
                                    'type' => 'hidden'
                                );
                                echo form_input($data);

                                $data = array(
                                    'id' => 'invoice_hidden_id',
                                    'name' => 'invoice_hidden_id',
                                    'value' => '',
                                    'type' => 'hidden'
                                );
                                echo form_input($data);

                                $data = array(
                                    'id' => 'account_hidden_id',
                                    'name' => 'account_hidden_id',
                                    'value' => '',
                                    'type' => 'hidden'
                                );
                                echo form_input($data);
                                ?>
                                <span id="payment_type_err"></span>
                            </td>
                            <td class="td_heading">Refund Date:<span class="required">*</span></td>
                            <td colspan="3">
                                <?php
                                $data = array(
                                    'id' => 'refund_date',
                                    'name' => 'refund_date',
                                    'readonly' => 'readonly'
                                );
                                echo form_input($data);
                                ?>
                                <span id="refund_date_err"></span>
                            </td>
                        </tr>
                        <tr class="sfc_clam" style="display:none;">
                            <td><span id="sfc_clm_err"></span>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br>
            <div id="row_dim" style="display:none;">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td width="16%" class="td_heading">Cheque Number:<span class="required">*</span></td>
                                <td width="33%">
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
                                <td width="23%" class="td_heading">Cheque Amount:<span class="required">*</span></td>
                                <td width="28%">$ <?php
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
                                <td width="16%" class="td_heading">Amount:<span class="required">*</span></td>
                                <td width="84%">$ <?php
                                    $cash_amount = array(
                                        'name' => 'cash_amount',
                                        'id' => 'cash_amount',
                                        'value' => $this->input->post('cash_amount'),
                                    );
                                    echo form_input($cash_amount);
                                    ?>
                                    <?php 
                                    echo form_hidden('refundable_total', 0, 'refundable_total'); 
                                    ?>
                                    <span id="cash_amount_err"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- start sfc self-->
            <style>
                .sfc_amount{
                    border-color: #transparent !important;
                    background-color:transparent;border: solid 0px #bcbcbc !important;
                }
                .sfcato_amount{
                    border-color: #transparent !important;
                    background-color:transparent;border: solid 0px #bcbcbc !important;
                }
            </style>
            <div id="row_dim4" style="display:none;">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
<!--                                <td width="16%" class="td_heading">SFC(SELF) claimed on:<span class="required">*</span></td>
                                <td width="84%">$ <?php
                                    $sfcclaim_on = array(
                                        'name' => 'sfcclaim_on',
                                        'id' => 'sfcclaim_on',
                                        'value' => $this->input->post('sfcclaim_on'),
                                    );
                                    echo form_input($sfcclaim_on);
                                    ?>
                                   
                                    <span id="sfcclaim_on_err"></span>
                                </td>-->
                                <td width="16%" class="td_heading">SFC(SELF) Amount Claimed:<span class="required">*</span></td>
                                <td width="84%">$ <?php
                                    $sfc_amount = array(
                                        'name' => 'sfc_amount',
                                        'id' => 'sfc_amount',
                                        'class' => 'sfc_amount',
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
             <!-- ends sfc -->
             <!-- start sfc ato-->
            <div id="row_dim5" style="display:none;">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td width="16%" class="td_heading">SFC(ATO) Amount:<span class="required">*</span></td>
                                <td width="84%">$ <?php
                                    $sfcato_amount = array(
                                        'name' => 'sfcato_amount',
                                        'id' => 'sfcato_amount',
                                        'class' => 'sfcato_amount',
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
            <!-- end sfc -->
              <br>
         <!-- other payment for SFC -->
    <div class="other_payment" id="other_payment" style="display:none;">
        <table class="table table-striped ">
            <tbody>
                <tr>
                    <td width="16%" class="td_heading">Other Mode of Refund:<span class="required">*</span></td>
                    <td width="84%">
                        <?php

                        $type_options1 = array('' => 'Select', 'CASH1' => 'Cash', 'CHQ1' => 'Cheque',);
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
                        <span id="payment_type1_err"></span>
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
<!--                            <td width="16%" class="td_heading">Recd. On:<span class="required">*</span></td>
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
                                <span id="paid_on_err"></span>
                            </td>-->
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
                                <span id="cheque_date_err"></span>
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
<!--                            <td width="16%" class="td_heading">Recd. On:<span class="required">*</span></td>
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
                                <span id="cashpaid_on_err"></span>
                            </td>-->
                            <td width="14%" class="td_heading">Amount:<span class="required">*</span></td>
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
        
    <!-- ends other payment -->
            <br>
            <span class="required required_i">* Required Fields</span>
            <div class="button_class99">
                <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-retweet"></span>&nbsp;Update</button> &nbsp; &nbsp; 
                <div class="modal1_0051" id="ex5" style="display:none;height:185px;width:50%;">
                    <p>
                    <h2 class="panel_heading_style">Refund Payment</h2>
                    <strong>Reason for Refund:<span class="required">*</span></strong> 
                    <?php
                    $options = array();
                    $options[''] = 'Select';
                    foreach ($refund_reason as $row) {
                        $options[$row['parameter_id']] = $row['category_name'];
                    }
                    $options['OTHERS'] = 'Others';
                    $js = 'id="refund_reason" ';
                    echo form_dropdown('refund_reason', $options, '', $js);
                    ?>
                    <span id="refund_reason_err"></span>
                    &nbsp; <div id="row_dim_new1" style="float:right; margin-right:10%;display:none;">
                        <?php
                        $data = array(
                            'name' => 'other_reason',
                            'id' => 'other_reason',
                            'style' => 'width:115%',
                            'maxlength' => 250,
                            'class' => 'upper_case'
                        );
                        echo form_input($data);
                        ?>
                        <span id="other_reason_err"></span>
                    </div>
                    <br><br>
                    Are you sure you want to pay this refund?
                    <br>
                    <span class="required required_i">* Required Fields</span>
                    <div class="popup_cancel9">
                        <input type="submit" class="btn btn-primary refund_save"  name="submit">&nbsp;&nbsp;
                        <a href="#" rel="modal:close">
                            <button class="btn btn-primary no_refund" type="button" >No</button>
                        </a>
                    </div>
                </p>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
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
                        <td>$<span class="cp_invoice_amount"></span></td>
                    </tr>
                    <tr>
                        <td class="td_heading"><span class="cp_invoice_discount_label"></span> Discount @<span class="cp_invoice_discount_rate"></span>%:</td>
                        <td>$<span class="cp_invoice_discount_amount"></span></td>
                        <td class="td_heading">Subsidy:</td>
                        <td>$<span class="cp_invoice_subsidy_amount"></span></td>
                        <td class="td_heading">GST @ (<span class="cp_invoice_gst_label"></span>)<span class="cp_invoice_gst_rate"></span>%:</td>
                        <td>$<span class="cp_invoice_total_gst"></span></td>
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
    <div class="modal_333 modal_payment_recd" id="ex32" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Refund Details for '<span class="cp_invoice_company_name"></span>'</h2>
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
                        <td>$<span class="cp_invoice_amount"></span></td>
                    </tr>
                    <tr>
                        <td class="td_heading"><span class="cp_invoice_discount_label"></span> Discount @<span class="cp_invoice_discount_rate"></span>%:</td>
                        <td>$<span class="cp_invoice_discount_amount"></span></td>
                        <td class="td_heading">Subsidy:</td>
                        <td>$<span class="cp_invoice_subsidy_amount"></span></td>
                        <td class="td_heading">GST @ (<span class="cp_invoice_gst_label"></span>)<span class="cp_invoice_gst_rate"></span>%:</td>
                        <td>$<span class="cp_invoice_total_gst"></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <div class="table-responsive payment_scroll" style="height: 122px;">
            <table style="width:60%; margin:0 auto;" class="table table-striped cpayment_refund">
                <thead>
                    <tr>
                        <th>Refund On</th>
                        <th>Refund Reason</th>
                        <th>Mode</th>
                        <th>Amt. Refund</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <br>
        <div class="popup_cance89">
            <a href="#" class="company_print_refund_pdf"><button type="button" class="btn btn-primary">Print</button></a></div>
        </p>
    </div>
    <div class="modal_333" id="ex8" style="display:none;height:300px;">
        <h2 class="panel_heading_style">Payment Received Details for '<span class="p_invoice_trainee_name"></span>'</h2>
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
                        <td class="td_heading">Subsidy Type:</td>
                        <td><span class="p_invoice_subsidy_type"></span></td>
                    </tr>
                    <tr>
                        <td class="td_heading">GST @ (<span class="p_invoice_gst_label"></span>)<span class="p_invoice_gst_rate"></span>%:</td>
                        <td colspan="5">$<span class="p_invoice_total_gst"></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <div class="table-responsive">
            <table style="width:60%; margin:0 auto;" class="table table-striped payment_received">
                <thead>
                    <tr>
                        <th>Payment Recd. On</th>
                        <th>Amt. Recd.</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="popup_cance89">
            <a href="#" class="print_pdf"><button type="button" class="btn btn-primary">Print</button></a></div>
        </p>
    </div>
    <div class="modal_333" id="ex31" style="display:none;height:350px;">
        <h2 class="panel_heading_style">Refund Details for '<span class="p_invoice_trainee_name"></span>'</h2>
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
                        <td class="td_heading">Subsidy Type:</td>
                        <td><span class="p_invoice_subsidy_type"></span></td>                        
                    </tr>
                    <tr>
                        <td class="td_heading">GST @ (<span class="p_invoice_gst_label"></span>)<span class="p_invoice_gst_rate"></span>%:</td>
                        <td colspan="5">$<span class="p_invoice_total_gst"></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <div class="table-responsive payment_scroll" style="height: 122px;">
            <table style="width:60%; margin:0 auto;" class="table table-striped payment_refund">
                <thead>
                    <tr>
                        <th>Refund On</th>
                        <th>Refund Reason</th>
                        <th>Mode</th>
                        <th>Amt. Refund</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="popup_cance89">
            <a href="#" class="print_refund_pdf"><button type="button" class="btn btn-primary">Print</button></a></div>
        </p>
    </div>
    <div class="modal_020" id="ex4" style="display:none;height: 450px;">
        <p>
        <h2 class="panel_heading_style">Payment Receipt</h2><br>
        <table width="100%">
            <tbody>
                <tr>
                    <td rowspan="4"><img src="" class="logo" border="0" /></td>
                    <td colspan="2"><span class="r_tenant_name"></span></td>
                    <td class="td_heading"></td>
                    <td><strong>Receipt No.:</strong> <span class="r_invoice_no"></span></td>
                </tr>
                <tr>
                    <td colspan="2"><span class="r_tenant_address"></span></td>
                    <td class="td_heading"></td>
                    <td><strong>Receipt Date:</strong> <span class="r_invoice_recd"></span></td>
                </tr>
                <tr>
                    <td colspan="4"><span class="r_tenant_phone"></span></td>
                </tr>
            </tbody>
        </table><br>
        <p><font style="font-size:14px; font-weight:bold;">Received</font> with thanks SGD <span class="r_invoice_amount_recd"></span> from <span class="r_invoice_trainee"></span> for <strong><i>'Course: <span class="r_course"></span>, Class: <span class="r_class"></span>, Certificate Code: <span class="r_certilevel"></span>'</i></strong>. Mode of payment: <span class="r_invoice_mode"></span></p>
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
        </table><br>
        <p style="color:red;"><i>This is a computer generated receipt and doesn't require a seal or signature.</i></p>
        <div style="clear:both;"></div>
        <div class="popup_cance89">
            <a href="#" class="print_receipt"><button class="btn btn-primary" type="button">Print</button></a></div>
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
        <div class="popup_cancel popup_cancel001">
            <span href="#" rel="modal:close"><button class="btn btn-primary print_company_invoice" type="button">Print</button></span></div>
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
    });
    </script>
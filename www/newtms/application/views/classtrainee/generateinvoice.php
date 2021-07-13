<?php
$role_check = $this->data['user']->role_id;
?>
<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/generateinvoice.js"></script>
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
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/class-trainee.png"/> Accounting - Generate Invoice</h2>
    <div class="table-responsive n-pos-rl">
        <?php
        $atr = 'id="search_form" name="search_form" method="post"';
        echo form_open("class_trainee/updatepayment", $atr);
        ?>  
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading" width="23%">Select Account Type:</td>
                    <td width="14%">
                        <?php
                        $options = array(
                            'individual' => 'Individual',
                            'company' => 'Company'
                        );
                        if ($this->data['user']->role_id == 'COMPACT') {
                            $options = array('company' => 'Company');
                            $val = '';
                        }
                        $js = 'id="account_type"';
                        echo form_dropdown('account_type', $options, '', $js);
                        ?>
                    </td>
                    <td width="63%">
                        <span class="td_heading" width="19%">Payment Status:&nbsp;&nbsp;</span>
                        <span width="14%">
                            <?php
                            $options = array(
                                '' => 'All',
                                'PAID' => 'Paid',
                                'NOTPAID' => 'Not Paid'
                                //'VOID' => 'Void'
                            );
                            $js = 'id="payment_status"';
                            echo form_dropdown('payment_status', $options, '', $js);
                            ?>
                        </span>
                        <span id="payment_status_err"></span>
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
                    <td style="display:none;">
                        <?php
                         $inv_options = array(
                            '' => 'Select',
                        );
                        foreach ($invoices as $row) {
                            $inv_options[$row['value']] = $row['label'];
                        }
                        $js = 'id="invoice_id" style="width:115px;"';
                        echo form_dropdown('invoice_id', $inv_options, '', $js);
                        ?>
                    </td>
                    <td> <!--shubhranshu-->
                        <?php
                         
                        $data = array(
                            'id' => 'invoice_no',
                            'name' => 'invoice_no',
                            'class'=>'upper_case',
                            'value' => $this->input->post('invoice_no')
                        );
                        echo form_input($data);
                        $data = array(
                            'id' => 'invoice_no_id',
                            'name' => 'invoice_no_id',
                            'type'=>'hidden',
                            'value' => $this->input->post('invoice_no_id')
                        );
                        echo form_input($data);
                        ?>
                        <div style='color:blue; font-size:10px;'>Enter minimum of 4 characters to search</div>
                    </td>
                    <!--shubhranshu-->
                    <td colspan="">
                        <div class="row_dimm9  min-mar">
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
                                        </td>
                                        <td>
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
                                            <div style='color:blue; font-size:10px;'>Enter minimum of 4 characters to search</div>
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
                                        <td>
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
                                            <div style='color:blue; font-size:10px;'>Enter minimum of 4 characters to search</div>
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
                        $js = ' id="company" style="width:700px"';
                        echo form_dropdown('company', $options, '', $js);
                        $data = array(
                            'id' => 'payment_due_id',
                            'name' => 'payment_due_id',
                            'value' => '',
                            'type' => 'hidden'
                        );
                        echo form_input($data);
                        ?>   
                        <span id="company_err"></span>
                    </td>
                </tr>
                <tr class="company_td" style="display: none;">
                    <td><span class="td_heading">&nbsp;&nbsp;Invoice No./ Receipt No.:</span></td>
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
                        <div class="push_right" id="btn_srch">
                            <button type="button" class="search_button btn btn-xs btn-primary no-mar">
                                <span class="glyphicon glyphicon-search"></span>
                                Search
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
    <div style="clear:both;"></div>
    <br>
    <div class="companyamounts_display" style="display: none;">
        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Payment Details1 <span style="color: blue;">'<span class="regen_invoice"></span>'</span><div class="pull-right">  
                <span class="label label-default push_right black-btn regen_but " style="display: none;"><a href="javascript:;" class="regen_inv"><span class="glyphicon glyphicon-eye-open"></span> Regenerate Invoice</a></span>
                <span class="label label-default push_right black-btn mar-right "><a href="#ex13" rel="modal:open" class="small_text1 view_company_invoice"><span class="glyphicon glyphicon-eye-open"></span> View/Print Invoice</a></span>
             
            </div>
        </h2>
        
<!--       <span class="trainee_total_invice_amyt"></span>+
       <span class="trainee_amont_refund"></span>+
        <span class="invoice_excexx_amt"></span>-
        <span class="total_paid"></span>-
        <span class="excexx_refund"></span>=
        <span class="trainee_test1"></span>
        <br />
        <span class="trainee_test"></span>=<span class="trainee_test1"></span>-->
       
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
                        <td width="19%"><label class="label_font">$<span class="c_invoice_class_fees"></span> SGD</label>
                        </td>
                        <td class="td_heading">Total Discount @ <span class="c_invoice_discount_rate"></span>% (<span class="c_invoice_discount_label"></span>):</td>
                        <td><label class="label_font">$<span class="c_invoice_discount_amount"></span> SGD</label></td>
                        <td class="td_heading">Total Subsidy:</td>
                        <td><label class="label_font">$<span class="c_invoice_subsidy_amount"></span> SGD</label></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="td_heading">GST @ <span class="c_invoice_gst_rate"></span>% (<span class="c_invoice_gst_label"></span>):</td>
                        <td colspan="4"><label class="label_font">$<span class="c_invoice_total_gst"></span> SGD</label></td>

                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading" style="color:blue;">Total Invoice Amt.: </td>
                        <td style="color:blue;"><label class="label_font">$<span class="c_invoice_amount"></span> SGD</label>
                        <td class="td_heading" style="color:blue;">Total Amt. Recd.: </td>
                        <td colspan="3" style="color:blue;"><label class="label_font">$<span class="c_total_amt_recd"></span> SGD</label></td>
                    </tr>
                    <tr>
                        <td class="td_heading error"><b> Total Pending Receivables.: </b></td>
                        <td><label class="label_font error">$<span class="c_tot_pend_recv"></span> SGD</label></td>
                        
                        <td class="td_heading error"><b>Total Refunded Amt.: </b></td>
                        <td colspan="3"><label class="label_font error">$<span class="c_refund_amt"></span> SGD</label></td>
                    </tr>
                    <tr>
                        <td class="td_heading error"><b> Total Refundable Amt.: </b></td>
                        <td><label class="label_font error">$<span class="c_tot_refundable_amt"></span> SGD</label></td>
                        <td class="td_heading" style="color:blue;"><b>Over Payment Recd.: </b></td>
                        <td colspan="3"><label class="label_font" style="color:blue;">$<span class="c_over_recd"></span> SGD</label></td>
                    </tr>
                    <?php
                    if ($role_check == 'ADMN') {
                        ?>
                        <tr>
                            <td class="td_heading">Invoice Sent On:</td>
                            <td colspan="5">
                                <?php
                                echo form_open("class_trainee/update_invoiced_on", $atr);
                                $invd_date = array(
                                    'name' => 'invd_date',
                                    'id' => 'invd_date',
                                    'style' => 'width:100px',
                                    'placeholder' => 'dd/mm/yyyy',
                                );
                                echo form_input($invd_date);

                                $inv_no_hidden = array(
                                    'name' => 'inv_no',
                                    'id' => 'inv_no',
                                    'type' => 'hidden'
                                );
                                echo form_input($inv_no_hidden);
                                ?>
                                <span id="invd_date_err"></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                <div class="push_right">
                                    <button type="submit" class="btn btn-primary update_company_inv" value="company" name="submit"><span class="glyphicon glyphicon-retweet"></span>&nbsp;Update</button>
                                </div>
                                <?php
                                echo form_close();
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (empty($total_invoice)) { ?>
        <div class="trainee_div main_error">
            <table class="table table-striped">
                <tr class="danger">
                    <td colspan="10" style="color:red;text-align: center;">There are no enrollments that required payment for an invoice to be created.</td>
                </tr>
            </table>
        </div>
    <?php } ?>
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
                        <th class="th_header">Status1</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <br>
    <div class="amountdetails_div" style="display: none;">
        <h2 class="sub_panel_heading_style">
            <span class="glyphicon glyphicon-circle-arrow-right"></span> Payment Details
            <div class="pull-right">
                <a class="label label-default black-btn print_href"><span class="glyphicon glyphicon-eye-open"></span> View/Print Invoice</a> &nbsp;&nbsp; 
                <a href="javascript:;" class="label label-default black-btn regen_indi_but regen_indi_inv"><span class="glyphicon glyphicon-eye-open"></span> Regenerate Invoice</a>
               <!-- for void  -->
               <?php
    $atr = 'id="" name="" method="post" class="void_form"';
    echo form_open("class_trainee/void_invoice", $atr);
    ?>
   
       <?php
         $data = array(
            'id' => 'tenant_id',
            'name' => 'tenant_id',
             'class' => 'tenant_id',
            'type' => 'hidden'
        );
        echo form_input($data);
        
        $data = array(
            'id' => 'pymnt_due_id',
            'name' => 'pymnt_due_id',
            'class' => 'pymnt_due_id',
            'type' => 'hidden'
        );
        echo form_input($data);
       $data = array(
            'id' => 'invoice_id',
            'name' => 'invoice_id',
            'class' => 'invoice_id',
            'type' => 'hidden'
        );
        echo form_input($data);
       ?> 
        <button type="submit" class="label label-default black-btn ind_void_invoice" style="display:none;">
                <span class="glyphicon glyphicon-retweet"></span>
                Void Invoice
        </button>
     
   
        <?php echo form_close(); ?>
   
               <!-- end void -->
            </div>
        </h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td  class="td_heading" style="color:blue;">Invoice Amount:</td>
                        <td  style="color:blue;"><label class="label_font">$<span class="pay_total_invoice_amount"></span> SGD</label></td>
                        <td colspan="2" class="td_heading">GST @ <span class="pay_gst_rate"></span>% (<span class="pay_gst_label"></span>):</td>
                        <td colspan="2">$<span class="pay_total_gst"></span> SGD</td>
                      
                        
                    </tr>
                    <tr>
                        <td class="td_heading">Class Fees:</td>
                        <td><label class="label_font">$<span class="pay_class_fees"></span> SGD</label>
                        </td>
                        <td class="td_heading"><span class="pay_discount_label"></span> Discount @ <span class="pay_discount_rate"></span>%:</td>
                        <td><label class="label_font">$<span class="pay_discount_amount"></span> SGD</label></td>
                        <td class="td_heading">Subsidy Amount:</td>
                        <td><label class="label_font">$<span class="pay_subsidy_amount"></span> SGD</label></td>
                        
                    </tr>
                    <tr>
                        <td class="td_heading">Subsidy Type:</td>
                        <td colspan="5"><label class="label_font"><span class="pay_subsidy_type"></span></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Invoice Sent On:</td>
                        <td colspan="5"><?php
                            echo form_open("class_trainee/update_invoiced_on", $atr);
                            $iinvd_date = array(
                                'name' => 'iinvd_date',
                                'id' => 'iinvd_date',
                                'style' => 'width:100px',
                                'placeholder' => 'dd/mm/yyyy',
                                'value' => $this->input->post('iinvd_date'),
                            );
                            echo form_input($iinvd_date);
                            $iinv_no_hidden = array(
                                'name' => 'iinv_no',
                                'id' => 'iinv_no',
                                'type' => 'hidden'
                            );
                            echo form_input($iinv_no_hidden);
                            ?>
                            <span id="iinvd_date_err"></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <div class="push_right">
                                <button type="submit" class="btn btn-primary update_indiv_inv" value="individual" name="submit"><span class="glyphicon glyphicon-retweet"></span>&nbsp;Update</button>
                            </div>
                            <?php
                            echo form_close();
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <input type='hidden' id="class_hidden_id"/>
</div>
<br>
<div style="clear:both;"></div>
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
<?php
$atr = 'id="regenerate_form" name="regenerate_form" method="post"';
echo form_open("class_trainee/regenerate_invoice", $atr);
?>  
<div class="modal-inv regen_div1" id="ex21" style="display:none;width:50%">
    <p>
    <h2 class="panel_heading_style">Regenerate Invoice '<span class='regen_invoice'></span>'</h2>
    <div>
        <div style="color:blue;">Please select regeneration reason: </div>
        <?php
        $data = array('name' => 'select_reinvoice[]', 'class' => 'select_reinvoice');
        echo form_checkbox($data, 1, FALSE, $extra);
        echo '&nbsp; &nbsp; Regenerate due to changes in subsidy'
        ?>
    </div>
    <div>
        <?php
        $data = array('name' => 'select_reinvoice[]', 'class' => 'select_reinvoice');
        echo form_checkbox($data, 2, FALSE, $extra);
        echo '&nbsp; &nbsp; Regenerate due to changes in discount';
        ?>
        <span id="select_reinvoice_err"></span>
    </div>
    <div class="popup_cancel popup_cancel001">
        <span href="#" rel="modal:close"><button class="btn btn-primary create_regen" type="button"> Regenerate Invoice</button></span></div>
</p>
</div>
<div class="modal_333" id="ex22" style="display:none;height:650px;">
    <h2 class="panel_heading_style">Regenerate Invoice '<span class='regen_invoice'></span>'</h2>
    <div class="table-responsive">
        <table class="table">
            <tbody>
                <tr>
                    <td class="td_heading">Regenerate Invoice Class Fees:</td>
                    <td>
                        <?php echo form_radio('inv_class_fee', 0, true); ?> Invoice Class Fees &nbsp; &nbsp; &nbsp;
                        <?php echo form_radio('inv_class_fee', 1, false); ?> Current Class Fees
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="regen_discount_div" style='display: none;'>
        <h2 class="sub_panel_heading_style">Change Discount</h2>
        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr>
                        <td class="td_heading">Discount Type:</td>
                        <td><span class="regen2_dis_type"></span></td>
                        <td class="td_heading">Discount Percentage:</td>
                        <td>$ <span class="regen2_dis_rate"></span>%</td>
                        <td>
                            <div class="pull-right"><a href="#" class="regen2_change">Change Discount</a></div>
                        </td>
                    </tr>
                    <tr class="regen2_change_div" style="display: none;">
                        <td class="td_heading">Discount Type:</td>
                        <td>
                            <?php
                            $options = array(
                                '' => 'Select',
                                'DISCOMP' => 'Company',
                                'DISCLASS' => 'Class'
                            );
                            echo form_dropdown('regen2_form_dis_type', $options, '', 'id="regen2_form_dis_type"');
                            ?>
                            <span id="regen2_form_dis_type_err"></span>
                        </td>
                        <td class="td_heading">Discount Percentage:</td>
                        <td colspan="2">
                            <?php
                            $dis_perc = array(
                                'name' => 'regen2_form_dis_perc',
                                'id' => 'regen2_form_dis_perc',
                            );
                            echo form_input($dis_perc);
                            $dis_type_hidden = array(
                                'name' => 'regen2_hid_dis_type',
                                'id' => 'regen2_hid_dis_type',
                                'type' => 'hidden'
                            );
                            $dis_perc_hidden = array(
                                'name' => 'regen2_hid_dis_perc',
                                'id' => 'regen2_hid_dis_perc',
                                'type' => 'hidden'
                            );
                            echo form_input($dis_type_hidden);
                            echo form_input($dis_perc_hidden);
                            ?>% OR
                            <span id="regen2_form_dis_perc_err"></span>
                        </td>
                    </tr>
                    <tr class="regen2_change_div" style="display: none;">
                        <td class="td_heading">Discount Amount:</td>
                        <td colspan="4">$
                            <?php
                            $dis_amt = array(
                                'name' => 'regen2_form_dis_amt',
                                'id' => 'regen2_form_dis_amt',
                            );
                            echo form_input($dis_amt);  
                            $dis_amt_hidden = array(
                                'name' => 'regen2_hid_dis_amt',
                                'id' => 'regen2_hid_dis_amt',
                                'type' => 'hidden'
                            );                            
                            echo form_input($dis_amt_hidden);
                            ?>SGD
                            <span id="regen2_form_dis_amt_err"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <br class='inbetween_div' />
    <div class='regen_subsidy_div' style='display: none;'>
        <h2 class="sub_panel_heading_style">Change Subsidy</h2>
        <div class="table-responsive payment_scroll" style="height: 135px;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="th_header">Sl#</th>
                        <th class="th_header">Trainee Name</th>
                        <th class="th_header">NRIC/FIN No.</th>
                        <th class="th_header">Subsidy Amt.</th>
                        <th class="th_header">Subsidy Recd. On</th>
                        <th class="th_header">Subsidy Type</th>
                    </tr>
                </thead>
                <tbody class="regen3_tbody"></tbody>
            </table>
        </div>
    </div>
    <span id="regen2_main_err"></span>
    <div class="popup_cance89">
        <span href="javascript:;" class=""><button type="submit" class="btn btn-primary regen2_but">Update</button></span>
    </div>
</p>
</div>
<input type='hidden' id="invoice_hidden_id" name="invoice_hidden_id"/>
<?php echo form_close(); ?>
<?php
$atr = 'id="regenerate_form" name="regenerate_form" method="post"';
echo form_open("class_trainee/regenerate_invoice", $atr);
?>  
<div class="modal-inv regen_div1" id="ex78" style="display:none;width:50%">
    <p>
    <h2 class="panel_heading_style">Regenerate Invoice '<span class='regen_indi_invoice'></span>'</h2>
    <div>
        <div style="color:blue;">Please select regeneration reason: </div>
        <?php
        $data = array('name' => 'select_indi_reinvoice[]', 'class' => 'select_indi_reinvoice');
        echo form_checkbox($data, 1, FALSE, $extra);
        echo '&nbsp; &nbsp; Regenerate due to changes in subsidy'
        ?>
    </div>
    <div>
        <?php
        $data = array('name' => 'select_indi_reinvoice[]', 'class' => 'select_indi_reinvoice');
        echo form_checkbox($data, 2, FALSE, $extra);
        echo '&nbsp; &nbsp; Regenerate due to changes in discount';
        ?>
        <span id="select_indi_reinvoice_err"></span>
    </div>
    <div class="popup_cancel popup_cancel001">
        <span href="#" rel="modal:close"><button class="btn btn-primary create_indi_regen" type="button"> Regenerate Invoice</button></span></div>
</p>
</div>
<div class="modal_333" id="ex79" style="display:none;height:650px;">
    <h2 class="panel_heading_style">Regenerate Invoice '<span class='regen_indi_invoice'></span>'</h2>
    <div class="table-responsive">
        <table class="table">
            <tbody>
                <tr>
                    <td class="td_heading">Regenerate Invoice Class Fees:</td>
                    <td>
                        <?php echo form_radio('inv_class_fee', 0, true); ?> Invoice Class Fees &nbsp; &nbsp; &nbsp;
                        <?php echo form_radio('inv_class_fee', 1, false); ?> Current Class Fees
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="regen_indi_discount_div" style='display: none;'>
        <h2 class="sub_panel_heading_style">Change Discount</h2>
        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr>
                        <td class="td_heading">Discount Type:</td>
                        <td><span class="regen2_indi_dis_type"></span></td>
                        <td class="td_heading">Discount Percentage:</td>
                        <td>$ <span class="regen2_indi_dis_rate"></span>%</td>
                        <td>
                            <div class="pull-right"><a href="#" class="regen2_indi_change">Change Discount</a></div>
                        </td>
                    </tr>
                    <tr class="regen2_indi_change_div" style="display: none;">
                        <td class="td_heading">Discount Type:</td>
                        <td>
                            <?php
                            $options = array(
                                '' => 'Select',
                                'DISINDVI' => 'Individual',
                                'DISCLASS' => 'Class'
                            );
                            echo form_dropdown('regen2_indi_form_dis_type', $options, '', 'id="regen2_indi_form_dis_type"');
                            ?>
                            <span id="regen2_indi_form_dis_type_err"></span>
                        </td>
                        <td class="td_heading">Discount Percentage:</td>
                        <td colspan="2">
                            <?php
                            $dis_perc = array(
                                'name' => 'regen2_indi_form_dis_perc',
                                'id' => 'regen2_indi_form_dis_perc',
                            );
                            echo form_input($dis_perc);
                            $dis_type_hidden = array(
                                'name' => 'regen2_indi_hid_dis_type',
                                'id' => 'regen2_indi_hid_dis_type',
                                'type' => 'hidden'
                            );
                            $dis_perc_hidden = array(
                                'name' => 'regen2_indi_hid_dis_perc',
                                'id' => 'regen2_indi_hid_dis_perc',
                                'type' => 'hidden'
                            );
                            echo form_input($dis_type_hidden);
                            echo form_input($dis_perc_hidden);
                            ?>% OR
                            <span id="regen2_indi_form_dis_perc_err"></span>
                        </td>
                    </tr>
                    <tr class="regen2_indi_change_div" style="display: none;">
                        <td class="td_heading">Discount Amount:</td>
                        <td colspan="4">$
                            <?php
                            $dis_amt = array(
                                'name' => 'regen2_indi_form_dis_amt',
                                'id' => 'regen2_indi_form_dis_amt',
                            );
                            echo form_input($dis_amt);  
                            $dis_amt_hidden = array(
                                'name' => 'regen2_indi_hid_dis_amt',
                                'id' => 'regen2_indi_hid_dis_amt',
                                'type' => 'hidden'
                            );                            
                            echo form_input($dis_amt_hidden);
                            ?>SGD
                            <span id="regen2_indi_form_dis_amt_err"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <br class='inbetween_indi_div' />
    <div class='regen_indi_subsidy_div' style='display: none;'>
        <h2 class="sub_panel_heading_style">Change Subsidy</h2>
        <div class="table-responsive payment_scroll" style="height: 135px;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="th_header">Sl#</th>
                        <th class="th_header">Trainee Name</th>
                        <th class="th_header">NRIC/FIN No.</th>
                        <th class="th_header">Subsidy Amt.</th>
                        <th class="th_header">Subsidy Recd. On</th>
                        <th class="th_header">Subsidy Type</th>
                    </tr>
                </thead>
                <tbody class="regen3_indi_tbody"></tbody>
            </table>
        </div>
    </div>
    <span id="regen2_indi_main_err"></span>
    <div class="popup_cance89">
        <span href="javascript:;" class=""><button type="submit" class="btn btn-primary regen2_indi_but">Update</button></span>
    </div>
</p>
</div>
<input type='hidden' id="invoice_indi_hidden_id" name="invoice_indi_hidden_id"/>
<input type='hidden' id="invoice_type" name="invoice_type" value="individual"/>
<?php echo form_close(); ?>
<div class="modal0000" id="ex111" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Alert Message</h2>
    <span class="error">Unable to update invoice sent on date. Please try again.</span><br>
    <div class="popup_cancel popup_cancel001">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
</p>
</div>
<div class="modal0000" id="ex101" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Alert Message</h2>
    Invoice sent on date updated successfully for the invoice.<br>
    <div class="popup_cancel popup_cancel001">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
</p>
</div>
<script>
    $(document).ready(function() {
        $(function() {
            $('.row_dim').hide();
            $('#type').change(function() {
                $('.row_dim').hide();
                if (this.options[this.selectedIndex].value == 'parcel') {
                    $('.row_dim').show();
                    $('.table_new_style').css('margin-bottom', '0px');
                    $('.col_10_height_other').css('height', 'auto');
                }
            });
        });
        $(function() {
            $('#type').change(function() {
                $('.row_dim1').hide();
                if (this.options[this.selectedIndex].value == 'parcel1') {
                    $('.row_dim1').show();
                    $('.table_new_style').css('margin-bottom', '0px');
                    $('.col_10_height_other').css('height', 'auto');
                }
            });
        });
        $(function() {
            $('#type').change(function() {
                $('.row_dim2').hide();
                if (this.options[this.selectedIndex].value == 'parcel2') {
                    $('.row_dim2').show();
                    $('.table_new_style').css('margin-bottom', '0px');
                    $('.col_10_height_other').css('height', 'auto');
                }
            });
        });
        $(function() {
            $('.row_dim3').show();
            $('#type').change(function() {
                $('.row_dim3').hide();
                if (this.options[this.selectedIndex].value == 'parcel3') {
                    $('.row_dim3').show();
                    $('.table_new_style').css('margin-bottom', '0px');
                    $('.col_10_height_other').css('height', 'auto');
                }
            });
        });
    });
</script>

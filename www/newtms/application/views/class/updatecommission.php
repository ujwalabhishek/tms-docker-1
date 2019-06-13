<script>
    $siteurl = '<?php echo site_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/updatecommission.js"></script>
<div class="col-md-10">
    <?php
    if ($this->session->flashdata('success')) {
        echo '<div class="success">' . $this->session->flashdata('success') . '</div>';
    }
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/comission.png"> Accounting - Update Commission Payment</h2>
    <div class="table-responsive">
        <?php
        $atr = 'id="search_form" name="search_form" method="get" onsubmit="return validateCommission();"';
        echo form_open("accounting/update_commission", $atr);
        ?>  
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="6%" class="td_heading">Search by Sales Executive:<span class="required">*</span></td>
                    <td width="20%" colspan="3">
                        <?php
                        $salesexec_options = array();
                        $salesexec_js = 'id="salesexec"';
                        $salesexec_options[''] = 'Select';
                        foreach ($salesexec as $v) {
                            $salesexec_options[$v->user_id] = $v->first_name . ' ' . $v->last_name;
                        }
                        echo form_dropdown('salesexec', $salesexec_options, $this->input->get('salesexec'), $class_js);
                        ?>
                    </td>
                    <td width="6%" align="right">
                        <button type="submit" value="Search" title="Search" class="btn btn-xs btn-primary no-mar"><span class="glyphicon glyphicon-search"></span> Search</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
    <div style="clear:both;"></div><br>
    <?php
    $sales_exec = $this->input->get('salesexec');
    if (!empty($sales_exec)) {
        ?>
        <?php if (!empty($comm_due)) { ?>
            <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/due.png"> Commission Payment Due <span class="label label-default push_right black-btn"><a href="#ex3" rel="modal:open">
                        <span class="glyphicon glyphicon-zoom-in"></span> View Payments</a></span></h2>
            <div style="clear:both;"></div>
            <?php
            $atr = 'id="updatecommissionform" name="updatecommissionform" method="post"';
            echo form_open("classes/updates_commission", $atr);
            ?>  
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="th_header" width="22%">Course</th>
                            <th class="th_header" width="15%">Period</th>
                            <th class="th_header" width="12%">Comm. Due</th>
                            <th class="th_header" width="12%">Comm. Paid</th>
                            <th class="th_header" width="20%">Comm. Payable</th>
                            <th class="th_header" width="20%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_due = 0;
                        $paid_total = 0;
                        $remain_total = 0;
                        $paid_arr = array('PAID' => 'Paid Full', 'NOTPAID' => 'Not Paid', 'PARTPAID' => 'Part Paid');
                        $paid_style = array('PAID' => 'color:green;', 'NOTPAID' => 'color:red;', 'PARTPAID' => 'color:blue;');
                        $year_arr = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September',
                            10 => 'October', 11 => 'November', 12 => 'December');
                        foreach ($comm_due as $row) {
                            $amount_paid = empty($row->amount_paid) ? 0 : $row->amount_paid;
                            $remain_amt = $row->comm_amount - $amount_paid;
                            $pdi = $row->pymnt_due_id;
                            if ($row->pymnt_status == 'NOTPAID') {
                                $payment_progress = '<span class="actual_pay_' . $pdi . '">$ ' . number_format($remain_amt, 2, '.', '') . ' SGD</span>
                                        <input type="hidden" value="' . number_format($remain_amt, 2, '.', '') . '" name="amount_check[' . $pdi . ']"/>
                                        <span style="display:none;" class="actual_payenter_' . $pdi . '">$<input type="text" value="' . number_format($remain_amt, 2, '.', '') . '" name="amount_paying[' . $pdi . ']" class="amount_paying"/> SGD</span>';

                                $paid_status = '<input type="radio" class="c_trainee" checked="checked" name="trainee[' . $pdi . ']" value="1">Full &nbsp; 
                                        <input type="radio" class="c_trainee" name="trainee[' . $pdi . ']" value="2">Part &nbsp; 
                                        <input type="radio" class="c_trainee" name="trainee[' . $pdi . ']" value="3">Dont Pay &nbsp; ';
                            } else if ($row->pymnt_status == 'PARTPAID') {
                                $payment_progress = '<span style="display:none;" class="actual_pay_' . $pdi . '">$ ' . number_format($remain_amt, 2, '.', '') . ' SGD</span>
                                        <input type="hidden" value="' . number_format($remain_amt, 2, '.', '') . '" name="amount_check[' . $pdi . ']"/>
                                        <span class="actual_payenter_' . $pdi . '">$<input type="text" value="' . number_format($remain_amt, 2, '.', '') . '" name="amount_paying[' . $pdi . ']" class="amount_paying"/> SGD</span>';

                                $paid_status = '<input type="radio" class="c_trainee" name="trainee[' . $pdi . ']" value="1">Full &nbsp; 
                                        <input type="radio" class="c_trainee" checked="checked"  name="trainee[' . $pdi . ']" value="2">Part &nbsp; 
                                        <input type="radio" class="c_trainee" name="trainee[' . $pdi . ']" value="3">Dont Pay &nbsp; ';
                            } else if ($row->pymnt_status == 'PAID') {
                                $payment_progress = '<span class="actual_pay_' . $pdi . '">$ ' . number_format($remain_amt, 2, '.', '') . ' SGD</span>
                                        <input type="hidden" value="' . number_format($remain_amt, 2, '.', '') . '" name="amount_check[' . $pdi . ']"/>
                                        <span style="display:none;" class="actual_payenter_' . $pdi . '">$<input type="text" value="' . number_format($remain_amt, 2, '.', '') . '" name="amount_paying[' . $pdi . ']" class="amount_paying"/> SGD</span>';

                                $paid_status = '<span style="color:green;">No Comm. Due.</span>';
                            }
                            echo "<tr>";
                            echo "<td>" . $row->crse_name . "</td>";
                            echo "<td>" . "<input name='trainee_selected[]' style='display:none;' class='trainee_selected' value='" . $pdi . "' checked='checked' type='checkbox' />" . $year_arr[$row->comm_period_mth] . " " . $row->comm_period_yr . "</td>";
                            echo "<td>$ " . number_format($row->comm_amount, 2, '.', '') . " SGD</td>";
                            echo "<td>$ " . number_format($amount_paid, 2, '.', '') . " SGD</td>";
                            echo "<td data-user='" . $pdi . "'>" . $payment_progress . "</td>";
                            echo "<td data-user='" . $pdi . "'>" . $paid_status . "</td>";
                            echo "</tr>";
                            $total_due +=$row->comm_amount;
                            $paid_total +=$amount_paid;
                            $remain_total += $remain_amt;
                        }
                        echo "<tr>
                            <td colspan='2' style='color:blue;' align='right'><b> Total Comm. Due:</b></td>
                            <td style='color:blue;'><b>$ " . number_format($total_due, 2, '.', '') . " SGD</b></td>
                            <td style='color:blue;'><b>$ " . number_format($paid_total, 2, '.', '') . " SGD</b></td>
                            <td style='color:blue;'><b>$ <span class='c_trainee_pay_total'>" . number_format($remain_total, 2, '.', '') . "</span> SGD</b></td>
                            <td>&nbsp;</td>
                        </tr>";
                        ?>
                    </tbody>
                </table>
            </div>
            <br>
            <div class="common_pay">
                <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/payment.png"> Payment Details </h2> 
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td width="16%" class="td_heading">Mode of Payment:<span class="required">*</span></td>
                            <td width="84%">
                                <?php
                                $type_options = array('' => 'Select', 'CASH' => 'Cash', 'CHQ' => 'Cheque');
                                $type_js = 'id="payment_type"';
                                echo form_dropdown('payment_type', $type_options, $this->input->get('payment_type'), $type_js);
                                ?>
                                <span id="payment_type_err"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <div id="row_dim" style="display:none;">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td width="16%" class="td_heading">Paid On:<span class="required">*</span></td>
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
                                        ?> SGD
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
                                    <td width="16%" class="td_heading">Paid On:<span class="required">*</span></td>
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
                                        ?> SGD
                                        <?php
                                        $salesexec = array(
                                            'name' => 'salesexec',
                                            'id' => 'salesexec',
                                            'value' => $this->input->get('salesexec'),
                                            'type' => 'hidden'
                                        );
                                        echo form_input($salesexec);
                                        ?>
                                        <span id="cash_amount_err"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div><br>
                <span class="required required_i">* Required Fields</span>
                <div class="button_class99">
                    <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-retweet"></span>&nbsp;Update</button> &nbsp; &nbsp; 
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
        <div style="clear:both;"></div><br>
        <hr>
        </div>
        <?php
    } else {
        ?>
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/due.png"> Commission Payment Due </h2>
        <div style="clear:both;"></div>
        <?php
        echo '<div class="error">There are no pending commission payments due for the selected sales executive \'' . $salesexec_options[$this->input->get('salesexec')] . '\'.</div>';
    }
}
?>
<div class="modal" id="ex1" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Heading Goes Here...</h2>
    Detail Goes here.  <br>
    <div class="popup_cancel">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a></div></p>
</div>
<div class="modal_3333" id="ex3" style="display:none;height:307px;">
    <p>
    <h2 class="panel_heading_style">Commission Paid Details for '<?php echo $salesexec_options[$this->input->get('salesexec')]; ?>' as on <?php echo date('d M, Y'); ?></h2>
    <div class="table-responsive">
        <div class="table-responsive payment_scroll" style="height: 153px;">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Course - Class</th>
                        <th>Due Month</th>
                        <th>Paid On</th>
                        <th>Mode</th>
                        <th>Amount Paid</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $year_arr = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September',
                        10 => 'October', 11 => 'November', 12 => 'December');
                    if (!empty($paid_details)) {
                        $total_paid = 0;
                        foreach ($paid_details as $k => $v) {
                            $cheque_details = ($v->mode_of_payment == 'CHQ') ? 'Chq:' . $v->cheque_number . ', Chq Dt:' . date('d/m/Y', strtotime($v->cheque_date)) : '';
                            $mode = ($v->category_name == 'Cash') ? $v->category_name : '';
                            echo '<tr>
                        <td>' . $v->course . '</td>
                        <td>' . $year_arr[$v->comm_period_mth] . ' ' . $v->comm_period_yr . '</td>
                        <td>' . date('d/m/Y', strtotime($v->paid_on)) . '</td>
                        <td>' . $mode . ' ' . $cheque_details . '</td>
                        <td>$' . number_format($v->amount_paid, 2, '.', '') . ' SGD</td>
                    </tr>';
                            $total_paid +=$v->amount_paid;
                        }
                        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align='right'><b>Total:</b></td><td>$ " . number_format($total_paid, 2, '.', '') . " SGD</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <br>
    <?php if (!empty($paid_details)) { ?>
        <div class="popup_cance89">
            <a href="<?php echo base_url() . 'classes/export_salesexecutive_commission/' . $sales_exec; ?>"><button type="button" class="btn btn-primary">Print</button></a></div>
        <?php } ?>
</p>
</div>

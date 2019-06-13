<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/reportpayments_due.js"></script>
<div class="col-md-10">
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Reports - Payment Due</h2>
    <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-search"></span> Search By</h2>

    <div class="table-responsive">
        <?php
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $salesexec = $this->input->get('salesexec');
        $atr = array('id' => 'paymrcvd_report_form', 'method' => 'get');
        echo form_open("reports_finance/payments_due", $atr);
        ?>
        <table class="table table-striped">

            <tbody>
                <tr>
                    <td class="td_heading">Sales Executive Name:</td>
                    <td colspan="5"><?php echo form_dropdown("salesexec", $executive, $this->input->get('salesexec'), 'id="salesexec"') ?><span id="salesexe_err"></span></td>
                </tr>
                <tr>
<!--                    <td class="td_heading">Payment Period From:</td>-->
                    <td class="td_heading">Class Start Date :</td>
                    <td><input type="text" name="start_date" id="start_date" class="date_picker" placeholder="dd/mm/yyyy" value="<?php echo $this->input->get('start_date'); ?>"><span id="start_date_err"></span></td>
                    <td class="td_heading">End Date :</td>
                    <td colspan="2"><input type="text" name="end_date"  id="end_date" class="date_picker" placeholder="dd/mm/yyyy" value="<?php echo $this->input->get('end_date'); ?>"><span id="end_date_err"></span></td>
                    <td align="center"><button type="submit" class="btn btn-xs btn-primary no-mar pull-right"><span class="glyphicon glyphicon-search"></span> Search</button></td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
    if (!empty($tabledata)) {
        if (empty($start_date) && empty($end_date)) {
            $period = ' for ' . date('F d Y, l');
        } else {
            $period = 'for the period';
            if (!empty($start_date))
                $period .= ' from ' . date('F d Y', DateTime::createFromFormat('d-m-Y', $start_date)->getTimestamp());
            if (!empty($end_date))
                $period .= ' to ' . date('F d Y', DateTime::createFromFormat('d-m-Y', $end_date)->getTimestamp());
        }
        if (!empty($salesexec)) {
            $period .= ' \'' . $executive[$salesexec] . '\'';
        }
        ?>
        <div class="panel-heading panel_headingstyle"><strong>Payments Due Report <?php echo $period ?></strong></div>
        <br>
       
        <div>
            <span style="float:left;color:blue;">NRIC/FIN No.:  Individual NRIC/ Company Registration Number</span>
            <span class="pull-right">
                <a href="<?php echo site_url('/reports_finance/payments_due_export_xls?' . $sort_link) ?>" class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export to XLS</span></a> &nbsp;&nbsp;
                <a href="<?php echo site_url('/reports_finance/payments_due_report_pdf?' . $sort_link) ?>" class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export to PDF</span></a>
            </span>
        </div>
       
        <br><br>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <?php
                     $ancher = (($sort_order == 'asc') ? 'desc' : 'asc')."&".$sort_link;
                    $pageurl = $controllerurl;
                    ?>
                    <tr>
                        <th width="5%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=inv.invoice_id&o=" . $ancher; ?>" >Inv #</a></th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=inv.inv_date&o=" . $ancher; ?>" >Inv Dt.</a></th>
                        <th width="12%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=inv.total_inv_amount&o=" . $ancher; ?>" >Inv Amt.</a></th>
                        <th width="10%" class="th_header text_move">Amt. Recd.</th>
                        <th width="10%" class="th_header text_move">Amt. Due</th>
                        <th width="10%" class="th_header text_move">Amt. Refd.</th>
                        <th width="20%" class="th_header text_move">Course - Class</th>
                         <th width="20%" class="th_header text_move">Clast Start Date - End Date</th>
                        <th width="25%" class="th_header text_move">Name</th>
                        <th width="15%" class="th_header text_move">NRIC/FIN No.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($tabledata as $data) {
                        if ($data->inv_type == 'INVINDV') {
                            $taxcode = $data->tax_code;
                            $name = $data->first_name . ' ' . $data->last_name;
                        } else {                                                       
                            if($data->company_id[0] == 'T') {
                                $tenant_details = fetch_tenant_details($data->company_id);
                                $name = $tenant_details->tenant_name. ' (Company)';
                                $taxcode = $tenant_details->tenant_name; 
                            } else {
                                $name = $data->company_name . ' (Company)';
                                $taxcode = $data->comp_regist_num; 
                            }
                        }
                        ?>
                        <tr>
                            <td><?php echo $data->invoice_id; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($data->inv_date)); ?></td>
                            <td>$ <?php echo $total_inv = number_format($data->total_inv_amount, 2, '.', ''); ?></td>
                            <td>$ <?php echo $total_recd = number_format($tabledatarecd[$data->invoice_id], 2, '.', ''); ?></td>
                            <td>$ <?php echo number_format($total_inv - $total_recd, 2, '.', ''); ?></td>
                            <td>$ <?php echo number_format($tabledatarefund[$data->invoice_id], 2, '.', ''); ?></td>
                            <td><?php echo $data->crse_name . ' - ' . $data->class_name; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($data->class_start_datetime)).' - '.date('d/m/Y', strtotime($data->class_end_datetime)); ?></td>
                            <td><?php echo $name; ?></td>
                            <td><?php echo $taxcode; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class='error'>*: All currency values are in <b>SGD</b>.</div>
    <?php } else { ?>
        <br>
        <table class="table table-striped">
            <tr class="danger">
                <td colspan="10" style="color:red;text-align: center;">There are no pending payment(s) available.</td>
            </tr>
        </table>
    <?php } ?>

    <ul class="pagination pagination_style">
        <?php echo $pagination; ?>
    </ul>
</div>

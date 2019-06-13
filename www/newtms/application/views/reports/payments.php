<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/reportpayments.js"></script>
<div class="col-md-10">
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Reports - Payments Received Report</h2>
    <h5 class="sub_panel_heading_style"><span class="glyphicon glyphicon-search"></span> Search By</h5>
    <div class="table-responsive">
        <?php
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $company = $this->input->get('company');
        $invoice_id = $this->input->get('invoice_id');
        $atr = array('id' => 'paymrcvd_report_form', 'method' => 'get');
        echo form_open("reports_finance/payments", $atr);
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">&nbsp;&nbsp;
                        <?php
                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => '4',
                            'checked' => true,
                            
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp;Company :
                    </td>
                    <td colspan="3">
                        <?php
                        $company_options = array();
                        $company_options[''] = 'Select';
                        foreach ($companies as $row) {
                            $company_options[$row->company_id] = $row->company_name;
                        }
                         $tenant_details = fetch_tenant_details($this->session->userdata('userDetails')->tenant_id);
                        $company_options[$tenant_details->tenant_id] = $tenant_details->tenant_name;
                        
                        $js = ' id="company" style="width:700px;"';
                        echo form_dropdown('company', $company_options, $this->input->get('company'), $js);
                        ?>   
                        <span id="company_err"></span>
                    </td>
                </tr>
                <tr>
                   
                    <td class="td_heading">&nbsp;&nbsp;
                        <?php
                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => '1',
                            'checked' => ($this->input->get('search_select') == 1) ? TRUE : False
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp;Invoice No.:
                    </td>
                    <td colspan="3">
                        <!--shubhranshu-->
                        <?php
//                        $options = array(
//                            '' => 'Select',
//                        );
//                        foreach ($invoices as $row) {
//                            $options[$row['value']] = $row['label'];
//                        }
//                        $js = 'id="invoice_id" style="width:250px;"';
//                        echo form_dropdown('invoice_id', $options, $this->input->get('invoice_id'), $js);

                        $data = array(
                            'id' => 'invoice_no',
                            'name' => 'invoice_no',
                            'class'=>'upper_case',
                            'value' => $this->input->get('invoice_no')
                        );
                        echo form_input($data);
                        $data = array(
                            'id' => 'invoice_no_id',
                            'name' => 'invoice_no_id',
                            'type'=>'hidden',
                            'value' => $this->input->get('invoice_no_id')
                        );
                        echo form_input($data);
                        ?><!--shubhranshu-->
                        <div style='color:blue; font-size:10px;'>Enter minimum of 5 characters to search</div>
                        <span id="invoice_id_err"></span>
                        
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">&nbsp;&nbsp;&nbsp;&nbsp;Payments Received From: &nbsp;&nbsp;&nbsp;</td>
                    <td><input type="text" name="start_date" id="start_date"  placeholder="dd-mm-yyyy" value="<?php echo $this->input->get('start_date'); ?>"></td>
                    <td class="td_heading">&nbsp;&nbsp;&nbsp;&nbsp;To: &nbsp;&nbsp;&nbsp;</td>
                    <td><input type="text" name="end_date"  id="end_date"  placeholder="dd-mm-yyyy" value="<?php echo $this->input->get('end_date'); ?>">
                        <div class="pull-right"><button type="submit" class="btn btn-xs btn-primary no-mar"><span class="glyphicon glyphicon-search"></span> Search</button></div>
                    </td>
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
        if (!empty($company)) {
            $period .= ' \'' . $company_options[$company] . '\'';
        }
        if (!empty($invoice_id)) {
            $period .= ' -  \' Invoice No.: ' . $invoice_id . '\'';
        }
        ?>
    <div id="validateion_err"></div>
        <div class="panel-heading panel_headingstyle"><strong>Payments Received Report <?php echo $period ?></strong></div>
        <br>
       
        <div>
            <span style="float:left;color:blue;">NRIC/FIN No.:  Individual NRIC/ Company Registration Number</span>
            <span class="pull-right">
                <a href="<?php echo site_url('/reports_finance/payments_export_xls?' . $sort_link) ?>" class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export to XLS</span></a> &nbsp;&nbsp;
                <a href="<?php echo site_url('/reports_finance/payments_received_reports_pdf?' . $sort_link) ?>" class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export to PDF</span></a>
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
                        <th width="5%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=ei.invoice_id&o=" . $ancher; ?>" >Inv #</a></th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=ei.inv_date&o=" . $ancher; ?>" >Inv Dt.</a></th>
                        <th width="12%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=ei.total_inv_amount&o=" . $ancher; ?>" >Inv Amt.</a></th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=epr.recd_on&o=" . $ancher; ?>" >Recd. On</a></th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=epr.amount_recd&o=" . $ancher; ?>" >Amt. Recd.</a></th>
                        <th width="20%" class="th_header text_move">Course - Class</th>
                        <th width="25%" class="th_header text_move">Name</th>
                        <th width="15%" class="th_header text_move">NRIC/FIN No.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php                     
                    foreach ($tabledata as $data) {
                        $k = $data->pymnt_due_id;                        
                        if ($data->inv_type == 'INVINDV') {
                            $taxcode = $tabledataextra[$k]->tax_code;
                            $name = $tabledataextra[$k]->first_name . ' ' . $tabledataextra[$k]->last_name;
                        } else {                            
                             if($data->company_id[0] == 'T') {
                                $tenant_details = fetch_tenant_details($data->company_id);
                                $name = $tenant_details->tenant_name. ' (Company)';
                                $taxcode = $tenant_details->tenant_name;
                            } else {
                                $name = $tabledataextra[$k]->company_name . ' (Company)';
                                $taxcode = $tabledataextra[$k]->comp_regist_num;
                            }
                        }
                        ?>
                        <tr>
                            <td><?php echo $data->invoice_id; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($data->inv_date)); ?></td>
                            <td>$ <?php echo number_format($data->total_inv_amount, 2, '.', ''); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($data->recd_on)); ?></td>
                            <td>$ <?php echo number_format($data->amount_recd, 2, '.', ''); ?></td>
                            <td><?php echo $tabledataextra[$k]->crse_name . ' - ' . $tabledataextra[$k]->class_name; ?></td>
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
                <td colspan="10" style="color:red;text-align: center;">There are no payment received report(s) available.</td>
            </tr>
        </table>
    <?php } ?>
    <ul class="pagination pagination_style">
        <?php echo $pagination; ?>
    </ul>
</div>



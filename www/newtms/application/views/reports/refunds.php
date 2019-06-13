<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/reportrefunds.js"></script>
<div class="col-md-10">
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Reports - Refunds Report</h2>
    <h5 class="sub_panel_heading_style"><span class="glyphicon glyphicon-search"></span> Search By</h5>
    <div class="table-responsive">
        <?php
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $company = $this->input->get('company');
        $invoice_id = $this->input->get('invoice_id');
        $atr = array('id' => 'paymrcvd_report_form', 'method' => 'get');
        echo form_open("reports_finance/refunds", $atr);
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
                            'checked' => true
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
                        <?php
                        $options = array(
                            '' => 'Select',
                        );
                        foreach ($invoices as $row) {
                            $options[$row['value']] = $row['label'];
                        }
                        $js = 'id="invoice_id" style="width:250px;"';
                        echo form_dropdown('invoice_id', $options, $this->input->get('invoice_id'), $js);
                        ?>
                        <span id="invoice_id_err"></span>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">&nbsp;&nbsp;&nbsp;&nbsp;From: &nbsp;&nbsp;&nbsp;</td><td><input type="text" name="start_date" id="start_date"  placeholder="dd-mm-yyyy" value="<?php echo $this->input->get('start_date'); ?>"></td>
                    <td class="td_heading">&nbsp;&nbsp;&nbsp;&nbsp;To: &nbsp;&nbsp;&nbsp;</td><td><input type="text" name="end_date"  id="end_date"  placeholder="dd-mm-yyyy" value="<?php echo $this->input->get('end_date'); ?>">
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
    <div id="valid_err"></div>
        <div class="panel-heading panel_headingstyle"><strong>Refunds Report <?php echo $period ?></strong></div>
        <br>
         
        <div>
            <span style="float:left;color:blue;">**NRIC/FIN No.:  Individual NRIC/ Company Registration Number</span>
            <span class="pull-right">
                <a href="<?php echo site_url('/reports_finance/refunds_export_xls?' . $sort_link) ?>" class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export to XLS</span></a> &nbsp;&nbsp;
                <a href="<?php echo site_url('/reports_finance/refund_report_pdf?' . $sort_link) ?>" class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export to PDF</span></a>
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
                        <th width="5%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=refnd.invoice_id&o=" . $ancher; ?>" >Inv #</a></th>
                        <th width="10%" class="th_header text_move">NRIC/FIN No.</th>
                        <th width="20%" class="th_header text_move">Name</th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=refnd.refund_on&o=" . $ancher; ?>" >Refund Dt.</a></th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=refnd.amount_refund&o=" . $ancher; ?>" >Refund Amt.</a></th>
                        <th width="30%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=refnd.refnd_reason&o=" . $ancher; ?>" >Reason</a></th>
                        <th width="15%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=usr.first_name&o=" . $ancher; ?>" >Refunded By</a></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($tabledata as $data) {
                        $k = $data->invoice_id;
                        if ($data->refund_type == 'INDV') {
                            $taxcode = $tableuser[$k]['taxcode'];
                            $name = $tableuser[$k]['name'];
                        } else {                            
                            if($data->company_id[0] == 'T') {
                                $tenant_details = fetch_tenant_details($data->company_id);
                                $name = $tenant_details->tenant_name. ' (Company)';
                                $taxcode = $tenant_details->tenant_name;
                            } else {
                                $taxcode = $data->comp_regist_num;
                                $name = $data->company_name . ' (Company)';
                            }
                        }
                        echo "<tr>
                                <td>" . $data->invoice_id . "</td>
                                <td>" . $taxcode . "</td>
                                <td>" . $name . "</td>
                                <td>" . date('d/m/Y', strtotime($data->refund_on)) . "</td>
                                <td>$ " . number_format($data->amount_refund, 2, '.', '') . "</td>
                                <td>" . $data->refnd_reason . "</td>
                                <td>" . $data->first_name . " " . $data->last_name . "</td>
                            </tr>";
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
                <td colspan="10" style="color:red;text-align: center;">No refunds found</td>
            </tr>
        </table> 
    <?php } ?>
    <br>
    <ul class="pagination pagination_style">
        <?php echo $pagination; ?>
    </ul>
</div>



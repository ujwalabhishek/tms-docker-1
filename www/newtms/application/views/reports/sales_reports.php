<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/invoice_audit_trail.js"></script>
<div class="col-md-10">
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Reports - Invoice Audit Trail</h2>
    <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-search"></span> Search By</h2>

    <div class="table-responsive">
        <?php
        $atr = array('id' => 'invoicelistform', 'method' => 'get');
        echo form_open("reports_finance/invoice_audit_trail", $atr);
        ?>
        <table class="table table-striped">

            <tbody>
                <tr>
                    <td class="td_heading">&nbsp;&nbsp;
                        <?php
                        $checked = TRUE;
                        $check = $this->input->get('search_select');
                        if ($check) {
                            $checked = ( $check == 1) ? TRUE : FALSE;
                        }
                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => 1,
                            'checked' => $checked
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp;Sales Executive:
                    </td>
                    <td colspan='5'><?php echo form_dropdown("sales_exec", $executive, $this->input->get('sales_exec'), 'id="sales_exec"') ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="td_heading">Invoice Dt. From:</td>
                    <td><?php
                        $start_date = array(
                            'name' => 'start_date',
                            'id' => 'start_date',
                            'style' => 'width:45%',
                            'value' => $this->input->get('start_date'),
                            'placeholder' => 'dd-mm-yyyy'
                        );
                        echo form_input($start_date);
                        ?>
                    </td>
                    <td class="td_heading">To:</td>
                    <td colspan="2">
                        <?php
                        $start_date = array(
                            'name' => 'end_date',
                            'id' => 'end_date',
                            'style' => 'width:45%',
                            'value' => $this->input->get('end_date'),
                            'placeholder' => 'dd-mm-yyyy'
                        );
                        echo form_input($start_date);
                        ?>
                    </td>
                </tr>
                    <tr>
                    <?php if ($this->data['user']->role_id != 'COMPACT') 
                    { ?>
                        <td class="td_heading">Company Name:</td>
                        <td colspan="3">
                        <?php
                        $company = array(

                            'name' => 'company_name',

                            'id' => 'company_name',

                            'value' => $this->input->get('company_name'),

                            'style'=>'width:200px;',

                            'class'=>'upper_case',

                            'autocomplete'=>'off'

                        );
                        echo form_input($company);

                       
                        ?>
                        <input type='hidden' name='company_id' id='company_id' value=''>
                        <span id="company_name_err"></span>
                        </td>
                    <?php 
                    } ?>
                        <td align="center">
                            <button type="submit" class="btn btn-xs btn-primary no-mar pull-right">
                                <span class="glyphicon glyphicon-search"></span> Search
                            </button>
                        </td>
                </tr>
                    
              
            </tbody>
        </table>
        <?php
        echo form_close();
        ?>
    </div>
    <?php
    $start_date = $this->input->get('start_date');
    $end_date = $this->input->get('end_date');
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
        ?>
        <div class="panel-heading panel_headingstyle"><strong>Invoice Audit Trail & Search Report <?php echo $period ?></strong></div>
        <br>
        <div>
            <span style="float:left;color:blue;">**NRIC/Comp Reg. No.:  Individual NRIC/ Company Registration Number</span>
            <span class="pull-right">
                <a href="<?php echo site_url('/reports_finance/invoice_audit_trail_export_XLS') . '?' . $_SERVER['QUERY_STRING']; ?>"  class="small_text1">
                    <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to XLS</span></a> &nbsp;&nbsp;
                <a href="<?php echo site_url('/reports_finance/invoice_audit_trail_export_PDF') . '?' . $_SERVER['QUERY_STRING']; ?>" class="small_text1">
                    <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to PDF</span></a>
            </span>
        </div>
        <br><br>
        <div class="table-responsive">
            <table class="table table-striped">
                <?php
                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                    $pageurl = $controllerurl . '?' . $sort_link;
                    ?>
                <thead>
                    <tr>
                        <th width="5%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "&f=invoice_id&o=" . $ancher; ?>" >Invoice #</a></th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "&f=inv_date&o=" . $ancher; ?>" >Invoice Dt.</a></th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "&f=inv_type&o=" . $ancher; ?>" >Invoice Type</a></th>
                        <th width="15%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "&f=tax_code&o=" . $ancher; ?>" >NRIC/Comp Reg. No.</a></th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "&f=total_inv_discnt&o=" . $ancher; ?>" >Discount</a></th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "&f=total_inv_subsdy&o=" . $ancher; ?>" >Subsidy</a></th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "&f=total_gst&o=" . $ancher; ?>" >GST</a></th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "&f=total_inv_amount&o=" . $ancher; ?>" >Net Amt.</a></th>
                        <th width="9%" class="th_header">Prev. Inv. Number</th>
                        <th width="9%" class="th_header">Next Inv. Number</th>
                    </tr>
                </thead>
                 <tbody>
                    <?php
                    foreach ($tabledata as $data) {
                        if ($data->invoice_generated_on == NULL || $data->invoice_generated_on == '0000-00-00 00:00:00') {
                            $invoiced_label = '<br><span style="color:red">** Not Invoiced</span>';
                        } else {
                            $int_date = $data->invoice_generated_on ? $data->invoice_generated_on : $data->inv_date;
                            $invoiced_label = '<br><span style="color:blue">** Invoiced (' . date('d/m/Y', strtotime($int_date)) . ')</span>';
                        }
                        $paid_arr = array('PAID' => 'Paid', 'PARTPAID' => 'Part Paid', 'NOTPAID' => 'Not Paid');
                        $paid_sty_arr = array('PAID' => 'color:green;', 'PARTPAID' => 'color:red;', 'NOTPAID' => 'color:red;');
                        if ($data->enrolment_mode == 'SELF') {
                            $taxcode = 'NRIC- '.$data->tax_code;
                            $name = $data->first_name . ' ' . $data->last_name;
                            $status = '<span style="' . $paid_sty_arr[$data->payment_status] . '">' . $paid_arr[$data->payment_status] . '</span>';
                            $prefix = "<a href='" . base_url() . 'class_trainee/export_generate_invoice/' . $data->pymnt_due_id . "'>";
                            $suffix = "</a>";
                            $inv_type = 'Individual';
                        } else {
                            // modified by dummy for internal staff enroll on 28 Nov 2014.
                                //if($data->inv_type == 'INVCOMALL') {
                                $name = $data->company_name;
                                //$tenant_details = fetch_tenant_details($data->company_id);
                                //$name = $tenant_details->tenant_name;
                                //$taxcode = $tenant_details->tenant_name;
                            //} else {
                                $inv_type = 'Company';
                                $taxcode = 'Reg No- '.$data->comp_regist_num;
                                $name = $data->company_name;
                            //}
                            $status = ($data->payment_status == 'PARTPAID') ? '<span style="color:red;">Part Paid/Not Paid</span>' : '<span style="color:green;">Paid</span>';
                            if($data->payment_status == 'NOTPAID'){
                                $status = '<span style="color:red;">Not Paid</span>';
                            }
                            $prefix = '<a href="javascript:;" class="company_pdf" data-invoice="' . $data->invoice_id . '" data-pdi="' . $data->pymnt_due_id . '">';
                            $suffix = '</a>';
                        }
                        $inv_type1 = $inv_type . ' (' . $name . ')';
//                      ?>
                        <tr>
                            <td><?php echo $data->invoice_id ?></a></td>
                            <td><?php echo date('d/m/Y', strtotime($data->inv_date)).'<br>'.$status; ?></td>
                            <td><?php echo $inv_type1 ?></td>
                            <td><?php echo $taxcode . $invoiced_label; ?></td>
                            <td>$ <?php echo number_format($data->total_inv_discnt, 2, '.', ''); ?></td>
                            <td>$ <?php echo number_format($data->total_inv_subsdy, 2, '.', ''); ?></td>
                            <td>$ <?php echo number_format($data->total_gst, 2, '.', ''); ?></td>
                            <td>$ <?php echo number_format($data->total_inv_amount, 2, '.', ''); ?></td>
                            <td><?php echo $data->invoice_id ?></td>
                            <td><?php echo $data->regen_inv_id ?></td>
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
                <td colspan="10" style="color:red;text-align: center;">There are no invoice audit trail available.</td>
            </tr>
        </table>
    <?php } ?>
    <br>
    <ul class="pagination pagination_style">
        <?php echo $pagination; ?>
    </ul>
</div>
<div class="modal-inv" id="ex13" style="display:none;width:25%">

    <div class="popup_cancel popup_cancel001">
        <span href="#" rel="modal:close"><button class="btn btn-primary print_company_invoice" type="button">Print</button></span></div>
</p>
</div>





























<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/reportsales.js"></script>
<div class="col-md-10">
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/comission.png"/>  Reports - Sales Commission</h2>
    <h5 class="sub_panel_heading_style"><span class="glyphicon glyphicon-search"></span> Search By</h5>

    <div class="table-responsive">
        <?php
        $sales_id = $this->input->get('sales_exec');
        $non_sales_id = $this->input->get('non_sales_exec');
        $atr = array('id' => 'sales_report_form', 'method' => 'get');
        echo form_open("reports_finance/sales", $atr);
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">&nbsp;&nbsp;
                        <?php
                        $checked = TRUE;
                        $check = $this->input->get('search_select');
                        if ($check) {
                            $checked = ( $check == 1) ? TRUE : FALSE;
                        }
                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => 1,
                            'checked' => $checked
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp;Sales Executive:
                    </td>
                    <td colspan='5'><?php echo form_dropdown("sales_exec", $executive, $this->input->get('sales_exec'), 'id="sales_exec"') ?>
                    </td>
                   <td class="td_heading">Invoice Dt. From:</td>
                    <td><?php
                        $start_date = array(
                            'name' => 'start_date',
                            'id' => 'start_date',
                            'style' => 'width:45%',
                            'value' => $this->input->get('start_date'),
                            'placeholder' => 'dd-mm-yyyy'
                        );
                        echo form_input($start_date);
                        ?>
                    </td>
                    <td class="td_heading">To:</td>
                    <td colspan="2">
                        <?php
                        $start_date = array(
                            'name' => 'end_date',
                            'id' => 'end_date',
                            'style' => 'width:45%',
                            'value' => $this->input->get('end_date'),
                            'placeholder' => 'dd-mm-yyyy'
                        );
                        echo form_input($start_date);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="10">&nbsp;</td>
                    <td align="right"><button type="submit" class="btn btn-xs btn-primary no-mar">
                            <span class="glyphicon glyphicon-search"></span> Search</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br /><div id="valid_err"></div><br />
    <?php if (!empty($tabledata)) { ?>
        <div class="panel-heading panel_headingstyle panel_fix"><strong>Sales Commission report for <?php echo date('F d Y, l'); ?> <?php echo!empty($sales_id) ? '\'' . $executive[$sales_id] . '\'' : ''; ?></strong></div>
        <div class="add_button space_style">
            <!--<a href="<?php echo site_url('/reports_finance/sales_export_xls?sales_exec=' . $this->input->get('sales_exec').'&non_sales_exec='.$this->input->get('non_sales_exec')); ?>" class="small_text1">-->
            <a href="<?php echo site_url('/reports_finance/sales_export_xls') . '?' . $_SERVER['QUERY_STRING']; ?>"  class="small_text1">
                <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export to XLS</span></a> &nbsp;&nbsp; 
<!--            <a href="<?php echo site_url('/reports_finance/report_sales_pdf?sales_exec=' . $this->input->get('sales_exec')) ?>" class="small_text1">-->
            <a href="<?php echo site_url('/reports_finance/report_sales_pdf') . '?' . $_SERVER['QUERY_STRING']; ?>"  class="small_text1">
                <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export to PDF</span></a>
        </div>
        <div style="clear:both;"></div>
        <div class="table-responsive">
            <table class="table table-striped" id="listview">
                <thead>
                    <tr>
                        <!-- <?php 
                            $tenant_id = $this->session->userdata('userDetails')->tenant_id;        
                          if($tenant_id=='T01'){?>
                           <th width="15%" class="th_header text_move">Sales Executive</th>
                       <?php }?>-->
                         <th width="15%" class="th_header text_move">Sales Executive</th>
                        <th width="15%" class="th_header text_move">Total Comm.</th>
                        <th width="15%" class="th_header text_move">Total Recd.</th>
                        <th width="15%" class="th_header text_move">Total Due</th>
                        <th width="15%" class="th_header text_move">Due Period</th>
                    </tr>
                </thead>
                <tbody>
                    <?php                     
                    foreach ($tabledata as $row) 
                    {
                        $total = number_format($row->comm_due_amount, 2, '.', '');
                        $paid = number_format($row->comm_paid_amount, 2, '.', '');
                        $due = number_format($total - $paid, 2, '.', '');
                    ?>
                        <tr>
                            <!--<?php if($tenant_id=='T01'){?>
                            <td>
                                <a href='javascript:;' class='sales_link' data-salesexec='"<?php echo $row->sales_exec_id ;?>"'>
                                <span class='name'><?php echo $row->first_name." ".$row->last_name;?></span></a>
                            </td>
                            <?php }?>-->
                            <td>
                                <a href='javascript:;' class='sales_link' data-salesexec='"<?php echo $row->sales_exec_id ;?>"'>
                                <span class='name'><?php echo $row->first_name." ".$row->last_name;?></span></a>
                            </td>
                            <td>$ <?php echo $total;?></td>
                            <td>$ <?php echo $paid ;?></td>
                            <td>$ <?php echo $due ;?></td>
                            <td><?php echo rtrim($perioddata[$row->sales_exec_id],", ");?></td>
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
                <td colspan="10" style="color:red;text-align: center;">There are no sales comm. due(s) available.</td>
            </tr>
        </table>
    <?php } ?>
    <div style="clear:both;"></div>
    <br/>
    <ul class="pagination pagination_style">
        <?php echo $pagination; ?>
    </ul>
    <div id="paging_holder" class="pagination_style"></div>
</div>
<div class="modal_3333" id="ex3" style="display:none;height:307px;">
    <p>
    <h2 class="panel_heading_style">Commission Paid Details for '<span class="s_name"></span>' as on <?php echo date('d M, Y'); ?></h2>
    <div class="table-responsive">
        <div class="table-responsive payment_scroll" style="height: 153px;">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Due Month</th>
                        <th>Paid On</th>
                        <th>Mode</th>
                        <th>Amount Paid</th>
                    </tr>
                </thead>
                <tbody class="sales_popup">
                </tbody>
            </table>
        </div>
    </div>
    <br>
    <div class="popup_cance89">
        <a href="javascript:;" class="print_pdf"><button type="button" class="btn btn-primary">Print</button></a></div>
</p>
</div>



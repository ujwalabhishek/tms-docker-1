<script>

    $siteurl = '<?php echo site_url(); ?>';

    $baseurl = '<?php echo base_url(); ?>';

</script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/invoice_list.js?v=1.0.0"></script>

<div class="col-md-10">

    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Reports - List & Search Invoice</h2>

    <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-search"></span> Search By</h2>



    <div class="table-responsive">

        <?php

        $atr = array('id' => 'invoicelistform', 'method' => 'get');

        echo form_open("reports_finance/invoice_list", $atr);

        ?>

        <table class="table table-striped">



            <tbody>

                <tr>

                    <td class="td_heading">Payment Status:</td>

                    <td colspan="<?php echo ($this->data['user']->role_id != 'COMPACT') ? 1 : 5 ; ?>"><?php echo form_dropdown("payment_status", array('' => 'All', 'PAID' => 'Paid', 'NOTPAID' => 'Part / Not Paid', 'INVD' => 'Invoiced', 'NOINVD' => 'Not Invoiced'), $this->input->get('payment_status'), 'id="payment_status"') ?></td>

                    <?php if ($this->data['user']->role_id != 'COMPACT') { ?>

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

                            echo form_hidden('company_id', $this->input->get('company_id'), $id='company_id');

                            ?>

                            <span id="company_name_err"></span>

                        </td>

                    <?php } ?>

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

                    <td align="center"><button type="submit" class="btn btn-xs btn-primary no-mar pull-right"><span class="glyphicon glyphicon-search"></span> Search</button></td>

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

        <div class="panel-heading panel_headingstyle"><strong>Invoice List & Search Report <?php echo $period ?></strong></div>

        <br>

        <div>

            <span style="float:left;color:blue;">**NRIC/FIN No.:  Individual NRIC/ Company Registration Number</span>
            <?php 
            // added by shubhranshu
                $start = $this->input->get("start_date");
                $company = $this->input->get("company_name");
                $end = $this->input->get("end_date");
             if(($start!="" && $end!='') || $company !='')
             {
             ?>
            <span class="pull-right" style='margin: 10px;'>

                <a href="<?php echo site_url('/reports_finance/invoice_list_export_xls') . '?' . $_SERVER['QUERY_STRING']; ?>"  class="small_text1">

                    <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to XLS</span></a> &nbsp;&nbsp;

                <a href="<?php echo site_url('/reports_finance/invoice_export_PDF') . '?' . $_SERVER['QUERY_STRING']; ?>" class="small_text1">

                    <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to PDF</span></a>
            </span>
             </div>

             <br><br>
             <?php }else{ ?>
                <span class="pull-right" style='margin: 10px;'>

                    <a href="javascript:void(0)" class="small_text1" id='displayText'>

                        <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to XLS</span></a> &nbsp;&nbsp;

                    <a href="javascript:void(0)" class="small_text1" id='displayText1'>

                        <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to PDF</span></a>
                        

                </span>
            <div id="alertmsg" style="padding:5px;clear:both;display:none" class='alert alert-danger'>Company name/Start & End Date Required to Download PDF/XLS.</div>
             </div>
            <?php } // added by shubhranshu end code?>
        

        

        <div class="table-responsive">

            <table class="table table-striped">

                <thead>

                    <?php

                     $ancher = (($sort_order == 'asc') ? 'desc' : 'asc')."&".$sort_link;

                    $pageurl = $controllerurl;

                    ?>

                    <tr>

                        <th width="5%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=ei.invoice_id&o=" . $ancher; ?>" >Inv #</a></th>

                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=ccl.class_start_datetime&o=" . $ancher; ?>" >Course Dt.</a></th>

                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=ei.inv_date&o=" . $ancher; ?>" >Inv Dt.</a></th>

                        <th width="15%" class="th_header text_move">NRIC/FIN No.</th>

                        <th width="20%" class="th_header text_move">Name</th>

                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=ei.total_inv_discnt&o=" . $ancher; ?>" >Discount</a></th>

                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=ei.total_inv_subsdy&o=" . $ancher; ?>" >Subsidy</a></th>

                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=ei.total_gst&o=" . $ancher; ?>" >GST</a></th>

                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=ei.total_inv_amount&o=" . $ancher; ?>" >Net Amt.</a></th>

                        <th width="9%" class="th_header">Status</th>

                    </tr>

                </thead>

                <tbody>

                    <?php

                    foreach ($tabledata as $data) {

                        if ($data->invoiced_on == NULL || $data->invoiced_on == '0000-00-00 00:00:00') {

                            $invoiced_label = '<br><span style="color:red">** Invoice Not Sent</span>';

                        } else {

                            $invoiced_label = '<br><span style="color:blue">** Invoice Sent (' . date('d/m/Y', strtotime($data->invoiced_on)) . ')</span>';

                        }

                        $paid_arr = array('PAID' => 'Paid', 'PARTPAID' => 'Part Paid', 'NOTPAID' => 'Not Paid');

                        $paid_sty_arr = array('PAID' => 'color:green;', 'PARTPAID' => 'color:red;', 'NOTPAID' => 'color:red;');

                        if ($data->enrolment_mode == 'SELF') {

                            $taxcode = $data->tax_code;

                            $name = $data->first_name . ' ' . $data->last_name;

                            $status = '<span style="' . $paid_sty_arr[$data->payment_status] . '">' . $paid_arr[$data->payment_status] . '</span>';

                            $prefix = "<a href='" . base_url() . 'class_trainee/export_generate_invoice/' . $data->pymnt_due_id . "'>";

                            $suffix = "</a>";

                        } else {

                            if($data->company_id[0] == 'T') {

                                $tenant_details = fetch_tenant_details($data->company_id);

                                $name = $tenant_details->tenant_name. ' (Company)';

                                $taxcode = $tenant_details->tenant_name;

                            } else {

                                $taxcode = $data->comp_regist_num;

                                $name = $data->company_name . ' (Company)';

                            }

                            $status = ($data->payment_status > 0) ? '<span style="color:red;">Part Paid/Not Paid</span>' : '<span style="color:green;">Paid</span>';

                            $prefix = '<a href="javascript:;" class="company_pdf" data-invoice="' . $data->invoice_id . '" data-pdi="' . $data->pymnt_due_id . '">';

                            $suffix = '</a>';

                        }

                        ?>

                        <tr>

                            <td><?php echo $prefix . $data->invoice_id . $suffix; ?></a></td>

                            <td><?php echo date('d/m/Y', strtotime($data->course_date)); ?></td>

                            <td><?php echo date('d/m/Y', strtotime($data->inv_date)); ?></td>

                            <td><?php echo $taxcode . $invoiced_label; ?></td>

                            <td><?php echo $name; ?></td>

                            <td>$ <?php echo number_format($data->total_inv_discnt, 2, '.', ''); ?></td>

                            <td>$ <?php echo number_format($data->total_inv_subsdy, 2, '.', ''); ?></td>

                            <td>$ <?php echo number_format($data->total_gst, 2, '.', ''); ?></td>

                            <td>$ <?php echo number_format($data->total_inv_amount, 2, '.', ''); ?></td>

                            <td><?php echo $status ?></td>

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

                <td colspan="10" style="color:red;text-align: center;">There are no invoice(s) available.</td>

            </tr>

        </table>

    <?php } ?>

    <br>

    <ul class="pagination pagination_style">

        <?php echo $pagination; ?>

    </ul>

</div>

<div class="modal-inv" id="ex13" style="display:none;width:25%">

    <p>

    <h2 class="panel_heading_style">Select Invoice Type</h2>

    <div>

        <input type="hidden" id="payment_due_id"/>

        <input type="hidden" id="company_invoice_id" />

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
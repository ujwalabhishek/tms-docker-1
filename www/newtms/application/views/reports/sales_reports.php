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



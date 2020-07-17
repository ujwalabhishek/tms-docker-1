<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/invoice_audit_trail.js"></script>
<div class="col-md-10">
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Reports - Sales Commission</h2>
    <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-search"></span> Search By</h2>

    <div class="table-responsive">
        <?php
        $atr = array('id' => 'invoicelistform', 'method' => 'post');
        echo form_open("reports_finance/sales_report", $atr);
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
                    <td colspan='5'><?php echo form_dropdown("sales_exec", $executive, $this->input->post('sales_exec'), 'id="sales_exec"') ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="td_heading">Enroll Dt. From:</td>
                    <td><?php
                        $start_date = array(
                            'name' => 'start_date',
                            'id' => 'start_date',
                            'style' => 'width:45%',
                            'value' => $this->input->post('start_date'),
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
                            'value' => $this->input->post('end_date'),
                            'placeholder' => 'dd-mm-yyyy'
                        );
                        echo form_input($start_date);
                        ?>
                    </td>
                </tr>
                   
                <tr>
                    <td colspan="4"><button type="submit" value="Search" class="btn btn-xs btn-primary no-mar pull-right" title="Search"><span class="glyphicon glyphicon-search"></span> Search</button></td>
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
    if (!empty($final_data)) {
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
        <div class="panel-heading panel_headingstyle"><strong>Sales Commission Search Report <?php echo $period ?></strong></div>
        <br>
        <div>
            
            <span class="pull-right">
                <a href="<?php echo site_url('/reports_finance/sales_report_export_xls') . '?sales_exec=' . $_POST['sales_exec'].'&start='.$_POST['start_date'].'&end='.$_POST['end_date']; ?>"  class="small_text1">
                    <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to XLS</span></a> &nbsp;&nbsp;
               
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
                        <th width="5%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "&f=invoice_id&o=" . $ancher; ?>" >SL NO #</a></th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "&f=inv_date&o=" . $ancher; ?>" >COURSE NAME.</a></th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "&f=inv_type&o=" . $ancher; ?>" >COURSE DATE</a></th>
                        <th width="15%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "&f=tax_code&o=" . $ancher; ?>" >TRAINING PROVIDER</a></th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "&f=total_inv_discnt&o=" . $ancher; ?>" >COURSE FEE</a></th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "&f=total_inv_subsdy&o=" . $ancher; ?>" >NO. OF PAX</a></th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "&f=total_gst&o=" . $ancher; ?>" >TOTAL SALES</a></th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "&f=total_inv_amount&o=" . $ancher; ?>" >TRAINEE NAME</a></th>
                        <th width="9%" class="th_header">NRIC NO</th>
                        <th width="9%" class="th_header">STATUS</th>
                    </tr>
                </thead>
                 <tbody>
                    <?php
                    $i=1;
                    foreach ($final_data as $dat) {
                        foreach ($dat as $data) {
                            
                        ?>
                        <tr rowspan='<?php echo count($dat)?>'>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $data->crse_name; ?></td>
                            <td><?php echo $data->class_start_datetime; ?></td>
                            <td><?php if($data->provider == 'T02'){echo 'Xprienz';}elseif($data->provider == 'T20'){echo "Wablab";}elseif($data->provider == 'T17'){echo "Everest";} ?></td>
                            <td>$ <?php echo number_format($data->coursefee, 2, '.', ''); ?></td>
                            <td> <?php echo count($dat); ?></td>
                            <td>$ <?php echo ($data->coursefee * count($dat)); ?></td>
                            <td><?php echo $data->first_name; ?></td>
                            <td><?php echo $data->tax_code; ?></td>
                            <td><?php echo $data->training_score ?? 'NA'; ?></td>
                        </tr>
                        <?php
                    }
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
                <td colspan="10" style="color:red;text-align: center;"> <?php echo $error;?></td>
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






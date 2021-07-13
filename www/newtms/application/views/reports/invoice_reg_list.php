<script>

    $siteurl = '<?php echo site_url(); ?>';

    $baseurl = '<?php echo base_url(); ?>';

</script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/invoice_reg_list.js?1.03565766666555"></script>

<div class="col-md-10">

    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Reports - Regenerated & Deleted Invoice</h2>

    <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-search"></span> Search By</h2>



    <div class="table-responsive">

        <?php

        $atr = array('id' => 'invoicelistform', 'method' => 'get');

        echo form_open("reports_finance/invoice_reg_list", $atr);

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
                        &nbsp;&nbsp;Current Invoice:
                    </td>

                    <td> <?php

                            $invoice = array(

                                'name' => 'invoice_name',

                                'id' => 'invoice_name',

                                'value' => $this->input->get('invoice_name'),

                                'style'=>'width:200px;',

                                'class'=>'upper_case',

                                'autocomplete'=>'off'

                            );

                            echo form_input($invoice);

                                echo form_hidden('invoice_id', $this->input->get('invoice_id'), $id='invoice_id');

                            ?>
                    <span id="invoice_id_err"></span>
                    <div style='color:blue; font-size:10px;'>Enter minimum of 5 characters to search</div>
                    </td>
                    
                      <td width="15%" class="td_heading">
                        <?php
                        $checked = ($this->input->get('search_select') == 2) ? TRUE : FALSE;
                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => 2,
                            'checked' => $checked
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp;Previous Invoice:
                    </td>

                    <td> <?php

                            $prev_invoice = array(

                                'name' => 'prev_invoice_name',

                                'id' => 'prev_invoice_name',

                                'value' => $this->input->get('prev_invoice_name'),

                                'style'=>'width:200px;',

                                'class'=>'upper_case',

                                'autocomplete'=>'off'

                            );

                            echo form_input($prev_invoice);

                            echo form_hidden('prev_invoice_id', $this->input->get('prev_invoice_id'), $id='prev_invoice_id');

                            ?>
                            <span id="prev_invoice_id_err"></span>
                            <div style='color:blue; font-size:10px;'>Enter minimum of 5 characters to search</div>
                    </td>
                    
 
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

<!--        <div class="panel-heading panel_headingstyle"><strong>Invoice List & Search Report <?php echo $period ?></strong></div>-->
    <div id="valid_err"></div>
        <br>
        <div>
            <span class="pull-right">
                <a href="<?php echo site_url('/reports/regenrated_deleted_export_xls') . '?' . $_SERVER['QUERY_STRING']; ?>" class="small_text1">
                <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to XLS</span></a> &nbsp;&nbsp;
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

                        <th width="5%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=ei.invoice_id&o=" . $ancher; ?>" >Previous Inv #</a></th>
                         
                         <th width="5%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=ei.invoice_id&o=" . $ancher; ?>" >Current Inv #</a></th>
                         <th> Previous Invoice Trainee List</th>
                         <th> Current Invoice Trainee List</th>
                        <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=ei.inv_date&o=" . $ancher; ?>" >Inv DT.</a></th>

                        <th width="15%" class="th_header text_move">Regenerated On / Deleted On</th>

                        <th width="20%" class="th_header text_move">Regenerated / Deleted By</th>
                        <th width="20%" class="th_header text_move">Due To</th>
                         <th width="20%" class="th_header text_move">Name </th>
                       
                        <th width="9%" class="th_header">Status</th>
                       
                        
                    </tr>

                </thead>

                <tbody>

                    <?php
                        foreach ($tabledata as $row) 
                        {
                            if($row->regen_inv_id=="0"){
                            $status="Deleted";
                            }else{
                                 $status="Regenerated";
                            }
                            ?>
                            <tr>
                            <td>
                                <a href="javascript:;" class="print_old_invoice" data-old_invoice_id="<?php echo $row->invoice_id;?>"
                                                                             data-pdi="<?php echo $row->pymnt_due_id;?>">
                                    <?php echo $row->invoice_id;?>
                                </a>
                            </td>
                          
                            <td><?php if($row->regen_inv_id=="0"){
                                 echo $row->regen_inv_id ;
                                 } else{
                              ?>
                               <a href="javascript:;" class="print_new_invoice" data-invoice="<?php echo $row->regen_inv_id ;?>" 
                                            data-pdi="<?php echo $row->pymnt_due_id;?>"><?php echo $row->regen_inv_id;?>
                            </a>
                           <?php }  ?>
                            </td>
                              <td align="center">
                            <a href="#ex145<?php echo $row->invoice_id;?>" rel="modal:open" style="color: black;">
                                <span class="glyphicon glyphicon-eye-open"></span>
                            </a>

                            <div class="modal_333" id="ex145<?php echo $row->invoice_id;?>" style="display:none;">
                                <p>
                                        <h2 class="panel_heading_style"> Previous Invoice Trainee List</h2>
                                        <div class="table-responsive payment_scroll" style="height: 300px;">
                                        <?php
                                      $ci = & get_instance();
                                       $ci->load->model('reports_model', 'reportsModel');
                                        $result = $ci->reportsModel->get_prev_invoice_trainee($row->invoice_id,$row->inv_type);
                                        if($row->inv_type=="INVINDV")
                                        {
                                             $data= $result['data'];
                                        }
//                                          
                                        ?>
                            <table style="width:100%; margin:0 auto;" class="table table-striped cpayment_received">
                            <thead>
                                <tr>
                                    <th width="30%">NRIC</th>
                                    <th width="50%">Trainee Name</th>
                                    <th width="20%">Payment Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if($row->inv_type=="INVCOMALL")
                                {
                                    foreach ($result as $rows) {
                                        echo"<tr>
                                                <td>" . $rows['tax_code'] . "</td>
                                                <td>" . $rows['first_name'] . $rows['last_name']."</td>
                                                <td>" . $rows['payment_status'] . "</td>
                                            </tr>";
                                    }
                                }
                                else{
                                    echo"<tr>
                                            <td>" . $data->tax_code . "</td>
                                            <td>" . $data->first_name . $data->last_name."</td>
                                            <td>" . $data->payment_status . "</td>
                                         </tr>";
                                }
//                                
                                ?>
                            </tbody>
                        </table>
                                        </div>
                                </p>
                            </div>
                        </td>
                            <td align="center">
                            <a href="#ex144<?php echo $row->regen_inv_id;?>" rel="modal:open" style="color: black;">
                                <span class="glyphicon glyphicon-eye-open"></span>
                            </a>

                            <div class="modal_333" id="ex144<?php echo $row->regen_inv_id;?>" style="display:none;">
                                <p>
                                        <h2 class="panel_heading_style">Current Invoice Trainee List</h2>
                                        <div class="table-responsive payment_scroll" style="height: 300px;">
                                            <?php 
                                            
                                            $res = $ci->reportsModel->get_invoice_trainee($row->regen_inv_id);
                                          if(!empty($res)) { 
                                            ?>
                        <table style="width:100%; margin:0 auto;" class="table table-striped cpayment_received">
                            <thead>
                                <tr>
                                    <th width="30%">NRIC</th>
                                    <th width="50%">Trainee Name</th>
                                    <th width="20%">Payment Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($res as $row1) {
                                    //$k = $row['course_id'];
                                    echo "<tr>
                                        <td>" . $row1->tax_code . "</td>
                                        <td>" . $row1->first_name . $row1->last_name."</td>
                                        <td>" . $row1->payment_status . "</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table><?php }else{?>
                          <p> No Trainee is available because Invoice is Deleted.   </p>                  
                        <?php }?>
                                        </div>
                                </p>
                            </div>
                        </td>
                            <td><?php echo date('d/m/Y', strtotime($row->inv_date)); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row->date_on)); ?></td>
                            <td><?php echo $row->user_name; ?></td>
                            <td><?php echo  $row->reg_due_to; ?></td>
                            <td><?php
                                    if($row->company_name!=""){
                                        echo $row->company_name;
                                        }
                                        else{
                                            echo $row->inv_type;
                                        }
                                ?>
                            </td>
                            <td><?php echo $status ?></td>
                            
                            
                        

                            </tr>
                        <?php 
                        }
                        ?>

                </tbody>

            </table>

        </div>

        
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
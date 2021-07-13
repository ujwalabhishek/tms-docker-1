
<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<div class="col-md-10">
    <?php
    $CI = & get_instance();
    $CI->load->model('reports_model');
    if ($this->session->flashdata('success_message')) {
        echo '<div class="success">' . $this->session->flashdata('success_message') . '!</div>';
    }
    if ($this->session->flashdata('error_message')) {
        echo '<div class="error1">' . $this->session->flashdata('error_message') . '!</div>';
    }
    ?>

    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/internal_user.png"/>  TMS REPORT - PAID/NOTPAID(COUNT/AMOUNT) MONTHLY</h2>
    <div class="table-responsive">
        
        <?php
//            print_r($tabledata);
        $atr = 'id="search_form" name="search_form" method="POST"';
//        echo form_open("internal_user/activity_log", $atr);
        echo form_open("reports_finance/tms_report", $atr);
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">                               
                        Select Any Year
                    </td>
                    <td>
                      <?php
                        $yearVal_options = array(
                            '' => '--Select Year--',
                            '2018' => '2018',
                            '2019' => '2019',
                            '2020' => '2020',
                            '2021' => '2021'
                        );
                         $attr = 'id="gYear" name="yearVal"';
                        echo form_dropdown('yearVal', $yearVal_options, $this->input->post('yearVal'), $attr);
                        ?>
                    </td>
                    <td class="td_heading">                               
                        Select Any Month
                    </td>
                    <td>
                 
                        
                        <?php
                        $monthVal_options = array(
                            '' => '--Select Month--',
                            '01' => 'Janaury',
                            '02' => 'February',
                            '03' => 'March',
                            '04' => 'April',
                            '05' => 'May',
                            '06' => 'June',
                            '07' => 'July',
                            '08' => 'August',
                            '09' => 'September',
                            '10' => 'October',
                            '11' => 'November',
                            '12' => 'December',
                        );
                         $attr = 'id="gMonth" name="monthVal"';
                        echo form_dropdown('monthVal', $monthVal_options, $this->input->post('monthVal'), $attr);
                        ?>
                    </td>
                    <td class="td_heading">                               
                        Payment Status
                    </td>
                    <td>
                 
                        <?php
                        $trainingStatus_options = array(
                            '' => '--Select Payment Status--',
                            '1' => 'Paid',
                            '2' => 'Not Paid'
                        );
                         $attr = 'id="payStatus" name="payStatus"';
                        echo form_dropdown('payStatus', $trainingStatus_options, $this->input->post('payStatus'), $attr);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">                               
                        Result Status
                    </td>
                    <td>
                    
                        <?php
                        $trainingStatus_options = array(
                            '' => '--Select Training Score--',
                            '1' => 'Competant',
                            '2' => 'Not Yet Competant',
                            '3' => 'Absentees',
                            '4' => 'Competant / Not Yet Competant'
                        );
                         $attr = 'id="tStatus" name="trainingStatus"';
                        echo form_dropdown('trainingStatus', $trainingStatus_options, $this->input->post('trainingStatus'), $attr);
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="push_right btn_srch">
            <button type="button" class="search_button btn btn-xs btn-primary no-mar">
                <span class="glyphicon glyphicon-search"></span>
                Search
            </button>

        </div>
        <?php echo form_close(); ?>

    </div>   
  
     
   

        <div class="bs-example" style='display:<?php echo ($_POST['mVal'] ? 'none': 'block');?>'>
            <div class="table-responsive">
                            <div class="add_button " style='margin-top: 6px;'>
                <?php if (count($result) > 0 && array_key_exists('EXP_XLS', $this->data['left_side_menu']['INTUSR'])) { ?>
                                           
                                            <a href="<?php echo site_url('/reports_finance/export_tms_report' . $export_url) ?>"  class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export All As XLS</span></a>
                <?php } ?>
                            </div>
                <div style="clear:both;"></div>
                <table id="listview" class="table table-striped">
                    <thead>
                        <tr>
                            <th width="5%">Tax Code</th>
                            <th width="5%">Invoice ID.</th>
                            <th width="10%">Trainee Name</th>
                            <th width="10%">Company Name</th>
                            <th width="5%">Class Fees</th>
                            <th width="5%">Discount</th>
                            <th width="5%">Subsidy</th>
                            <th width="5%">GST</th>
                            <th width="5%">Net Amt.</th>
                            <th width="5%">Mode</th>
                            <th width="5%">TG No.</th>
                            <th width="7%">Class Start Date</th>
                            <th width="7%">Class End Date</th>
                            <th width="5%">Class Name</th>
                            <th width="5%">Training Score</th>
                            <th width="5%">Payment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            echo "Total Count : ".count($result);
                            ?>
                        </tr>
                        <?php
                        $unpaidVal = 0;
                        $paidVal = 0;
                        foreach ($result as $data) {
                            ?>
                            <tr>
                                <td><?php echo $data->tax_code; ?></td>
                                <td><?php echo $data->invoice_id; ?></td>
                                <td width="8%"><?php echo $data->name; ?></td>
                                <td width="8%"><?php echo $data->company_name ?? "N/A"; ?></td>
                                <td>$ <?php echo $data->class_fees; ?></td>
                                <td>$ <?php echo $data->discount_rate ?? "N/A"; ?></td>
                                <td>$ <?php echo $data->subsidy_amount ?? "N/A"; ?></td>
                                <td>$ <?php echo $data->gst_amount ?? "N/A"; ?></td>
                                <td>$ <?php 
                                if($data->payment_status == "NOTPAID") {
                                    echo $data->total_amount_due; 
                                    $unpaidVal = $unpaidVal + $data->total_amount_due;
                                } else {
                                    if($data->enrolment_mode =='SELF'){
                                        $amount = $CI->reports_model->get_invoice_data_for_individual($data->invoice_id, $data->user_id);
                                        $unpaidVal = $unpaidVal + $amount;
                                        echo $amount;
                                    }else{
                                        $amount1= $CI->reports_model->get_invoice_data_for_comp($data->invoice_id, $data->user_id);
                                        $unpaidVal = $unpaidVal + $amount1;
                                        echo $amount1;
                                    }
                                    
                                }
                                
                                ?></td>
                                <td> <?php echo $data->mode_of_pymnt ?? "N/A"; ?></td>
                                <td><?php echo $data->tg_number ?? "N/A"; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($data->class_start_datetime)); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($data->class_end_datetime)); ?></td>
                                <td><?php echo $data->class_name; ?></td>
                                <td><?php echo $data->training_score; ?></td>
                                <td><?php echo $data->payment_status; ?></td>
                                
                            </tr>
                        <?php } 
                            
                        echo ($text ? $text : '').($unpaidVal ? $unpaidVal : '');
                        
                        ?>
                    </tbody>      
                </table>
            </div>
            <div style="clear:both;"></div><br>
            <ul class="pagination pagination_style">
                <?php
                echo $pagination;
//            echo $this->input->get('cls_name');
                ?>
            </ul>
        </div>
    
     <div class="container">
        
    </div>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/internal_user.png"/>  TMS TRAINEE - COUNT/AMOUNT(YEARLY)</h2>
    <h2 class="sub_panel_heading_style"><img src="<?php echo $baseurl;?>/assets/images/education.png"> <a href='#' id='collapse_data'>TMS Trainee Counts</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id='count_res'> <?php echo $text1 ? ($text1.' :: '.$amount1) : ''?></span></h2> 
    
    <div class="table-responsive" id='data_hide' style='display:none'>    
        
          
        <?php
        
// /           print_r($tabledata);
        $atr = 'id="search_form_count" name="search_form_count" method="POST"';
//        echo form_open("internal_user/activity_log", $atr);
        echo form_open("reports_finance/tms_report_count", $atr);
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">                               
                        Select Any Year
                    </td>
                    <td>
                      <?php
                        $y_options = array(
                            '' => '--Select Year--',
                            '2018' => '2018',
                            '2019' => '2019',
                            '2020' => '2020'
                        );
                         $attr = 'id="cYear" name="yVal"';
                        echo form_dropdown('yVal', $y_options, $this->input->post('yVal'), $attr);
                        ?>
                    </td>
                    <td class="td_heading">                               
                        Select Any Month
                    </td>
                    <td>
                 
                        
                        <?php
                        $m_options = array(
                            '' => '--Select Month--',
                            'ALL'  => 'ALL MONTHS',
                        );
                         $attr = 'id="cMonth" name="mVal"';
                        echo form_dropdown('mVal', $m_options, $this->input->post('mVal'), $attr);
                        ?>
                    </td>
                    <td class="td_heading">                               
                        Payment Status
                    </td>
                    <td>
                 
                        <?php
                        $t_options = array(
                            '' => '--Select Payment Status--',
                            '1' => 'Paid',
                            '2' => 'Not Paid',
                            '3' => 'PAID/UNPAID'
                        );
                         $attr = 'id="pStatus" name="pStatus"';
                        echo form_dropdown('pStatus', $t_options, $this->input->post('pStatus'), $attr);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">                               
                        Result Status
                    </td>
                    <td>
                    
                        <?php
                        $s_options = array(
                            '' => '--Select Training Score--',
                            '1' => 'Competant',
                            '2' => 'Not Yet Competant',
                            '3' => 'Absentees',
                            '4' => 'Competant / Not Yet Competant'
                        );
                         $attr = 'id="cStatus" name="tStatus"';
                        echo form_dropdown('tStatus', $s_options, $this->input->post('tStatus'), $attr);
                        ?>
                    </td>
                </tr>
                
            </tbody>
        </table>
        <div class="push_right btn_srch">
            <button type="button" class="search_button1 btn btn-xs btn-primary no-mar">
                <span class="glyphicon glyphicon-search"></span>
                Search
            </button>

        </div>
        <?php echo form_close();?>
        
    </div>  
    
    <script>
        $(".search_button").click(function () {
            ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
            $status = true;
            if($('#gYear').val() ==''){
                $status=false;
                $('#gYear').css('color','red');
            }else{
                $('#gYear').css('color','black');
            }
            if($('#gMonth').val() ==''){
                $status=false;
                $('#gMonth').css('color','red');
            }else{
                $('#gMonth').css('color','black');
            }
            if($('#payStatus').val() ==''){
                $status=false;
                $('#payStatus').css('color','red');
            }else{
                $('#payStatus').css('color','black');
            }
            if($('#tStatus').val() ==''){
                $status=false;
                $('#tStatus').css('color','red');
            }else{
                $('#tStatus').css('color','black');
            }    
            
            
            if($status){
                $('#search_form').submit();
                $('.search_button').attr('disabled', 'disabled').html('Please Wait..');
            }
            
            ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
        });
        
        $('#collapse_data').click(function(){
            $('#data_hide').toggle(500);
        });
        
        
        $(".search_button1").click(function () {
            ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
            $status = true;
            if($('#cYear').val() ==''){
                $status=false;
                $('#cYear').css('color','red');
            }else{
                $('#cYear').css('color','black');
            }
            if($('#cMonth').val() ==''){
                $status=false;
                $('#cMonth').css('color','red');
            }else{
                $('#cMonth').css('color','black');
            }
            if($('#pStatus').val() ==''){
                $status=false;
                $('#pStatus').css('color','red');
            }else{
                $('#pStatus').css('color','black');
            }
            if($('#cStatus').val() ==''){
                $status=false;
                $('#cStatus').css('color','red');
            }else{
                $('#cStatus').css('color','black');
            }    
            
            
            if($status){
                $('#search_form_count').submit();
               
            $('.search_button1').attr('disabled', 'disabled').html('Please Wait..');
            }
            
            ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
        });
        
       
        
        
    </script>
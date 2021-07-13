
<div style="clear:both;"></div>        

<div class="col-md-12"  style="min-height: 360px;">
    <br>
    <h2 class="panel_heading_style">My Payments and Invoices</h2>
    <div class="table-responsive">
        <?php //echo '<pre>';print_r($tabledata);?>
        <?php if (empty($tabledata)) { ?>
            <div class='error' style="text-align:center"><label class="no-result"><img src="<?php echo base_url(); ?>assets/images/no-result.png">You  have  not  done any  payments yet!</label></div>    
        <?php } else { ?>
            <table class="table table-striped">
                <thead>
                    <?php
                    $ci =&get_instance();
                    $ci->load->model('user_model');
                    $user_id = $this->session->userdata('userDetails')->user_id;
                    
                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                    $pageurl = $controllerurl;
                    ?>  
                    <tr>
                        <th width="18%" class=""><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=class_name&o=" . $ancher; ?>" >Training Details</a></th>
                        <th width="11%" class=""><a style="color:#000000;" href="#" >Enrolled By</a></th>
                        <th width="11%" class=""><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=enrolment_mode&o=" . $ancher; ?>" >Account Type</a></th>
                        <th width="8%" class=""><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=class_fees&o=" . $ancher; ?>" >Unit Fees<br>(SGD)</a></th>
                        <th width="8%" class=""><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=total_inv_discnt&o=" . $ancher; ?>" >Discount<br>(SGD)</a></th>
                        <th width="8%" class=""><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=total_inv_subsdy&o=" . $ancher; ?>" >Subsidy<br>(SGD)</a></th>
                        <th width="10%" class=""><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=total_inv_amount&o=" . $ancher; ?>" >Amount Due<br>(SGD)</a></th>
                        <th width="7%" class=""><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=total_gst&o=" . $ancher; ?>" >GST<br>(SGD)</a></th>
                        <th width="2%" class=""><a style="color:#000000;"  >SFC</a></th>
                        <th width="7%" class=""><a style="color:#000000;"  >Attendance</a></th>
                        <th width="8%" class=""><a style="color:#000000;" href="" >Net Due<br>(SGD)</a></th>
                        <th width="8%" class=""><a style="color:#000000;" href="" >PAID+SFC<br>(SGD)</a></th>
                        <th width="8%" class=""><a style="color:#000000;" href="" >OUTSTANDING AMT<br>(SGD)</a></th>
                        <th colspan="7">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($tabledata as $key => $data) 
                    {
                        if($data['att_status']==1 && $data['payment_status'] == 'PAID'){
                            //$total_paid_amnt += $data['total_amount_due']-$data['sfc_claimed'];
                            $total_paid_amnt += $data['total_paid_till_date']-$data['sfc_claimed'];
                        }
                        
                        if($data['total_amount_due'] == $data['total_paid_till_date']){
                            $status= 'PAID';
                        }
                        else if(($data['total_paid_till_date']!=0) && ($data['total_paid_till_date']<$data['total_amount_due'])){
                            $status= 'PART PAID';
                        }else if($data['total_paid_till_date']==0.00){
                            $status= 'NOT PAID';
                        }
                        if($data['att_status']==1 && ($status=='PART PAID' || $status=='NOT PAID')){
                            // $total_payable_amnt += $data['total_amount_due'];    
                             $total_payable_amnt += ($data['total_amount_due']-$data['total_paid_till_date']-$data['sfc_claimed']);    
                        }
                       
                        ?>
                        <tr>
                            <td><a href="#ex<?php echo $data['class_id']; ?>" rel="modal:open" class="small_text1"><?php echo $data['class_name']; ?>
                                </a>
<!--                                <span style="color:orange;">
                                    <?php echo (!empty($data['friend_id'])) ? '(' . $data['first_name'] . ')' : '' ?>
                                </span>-->
                            </td>
                            <td>
                                <?php 
                               
                                if(!empty($data['friend_id'])){
                                     echo $res = $ci->user_model->friend_name($user_id,$data['friend_id'],$data['class_id']);
                                }else{
                                   
                                     $res = $ci->user_model->refferal_friend_name($user_id,$data['class_id']);
                                    if($res=='')
                                    {
                                        echo 'SELF';
                                    }else{ echo $res; }
                                    
                                }
                               ?>
                                
                            </td>
                            <td><?php
                                if ($data['enrolment_mode'] != 'COMPSPON')
                                    echo "Individual";
                                else
                                    echo "Company";
                                ?></td>
                            <td><?php
                                echo number_format($data['class_fees'], 2, '.', '');
                                ?></td>
                            <td><?php
                                //echo number_format($data['total_inv_discnt'], 2, '.', '');
                                  echo number_format($data['discount_rate'], 2, '.', '');
                                ?></td>
                            <td><?php
                                //echo number_format($data['total_inv_subsdy'], 2, '.', '');
                                echo number_format($data['subsidy_amount'], 2, '.', '');
                                ?></td>
                            <td><?php
                                //$amount_due = $data['total_inv_amount'] - $data['total_gst'];
                                $amount_due = $data['total_amount_due']-$data['gst_amount'];
                                echo number_format($amount_due, 2, '.', '');
                                ?></td>
                            <td><?php
                                //echo number_format($data['total_gst'], 2, '.', '');
                                echo number_format($data['gst_amount'], 2, '.', '');
                                ?>
                            </td>
                            <td><?php echo number_format($data['sfc_claimed'],2,'.','');?></td>
                            <td><?php
                                if($data['att_status']==1){ echo "Present";}
                                else { echo "Absent";}
                                ?>
                            </td>
                            <td><?php
                                    if($data['att_status']==1){
                                    echo number_format(($data['total_amount_due']-$data['sfc_claimed']),2,'.','');}
                                    else{ echo "0.00";}
                                ?>
                            </td>
                            <td>
                                <?php echo number_format($data['total_paid_till_date'],2,'.','');
                                ?>
                            </td>
                             <td>
                                <?php echo abs(number_format(($data['total_paid_till_date']-($data['total_amount_due'])),2,'.',''));
                                ?>
                            </td>
<!--                             <td><?php
                                   
                                    echo number_format(($data['recieved_amount']),2,'.','');
                                    echo "/". number_format(($data['refund_amount']),2,'.','');
                                    echo "/".number_format(($data['total_paid_till_date']),2,'.','');
                                   
                                ?>
                            </td>-->

                                <?php
                                if ($data['payment_status'] == 'NOTPAID' && $data['enrolment_mode'] != 'COMPSPON') 
                                {
                                    // && ($data['enrolment_mode'] != 'COMPSPON' || $data['enrolment_mode'] != 'COMPANY')
                                    ?> 
                                <td colspan="2" width="22%"><?php  if($data['att_status']==1){ ?>
                                    <span class="red">UNPAID</span> 
                                     <?php if ($data['class_status'] != INACTIVE)  ?>
<!--                                    <a href="../user/enroll_now/?cls=<?php echo $data['class_id']; ?>&crs=<?php echo $data['course_id']; ?>
                                       &enrolto=<?php echo $data['user_id']; ?>&pay=update">(Pay Now)</a>-->

                                    &nbsp;&nbsp;&nbsp;&nbsp;  <a href="#gen_invoice" onclick="getinvoice(<?php echo $data[class_id] . ', ' . $data[course_id] . ', ' . $data[user_id]; ?>);" rel="modal:open" class="small_text1">Invoice</a>
                                    <?php }?>
                                    &nbsp;&nbsp; <a href="#show_ack" rel="modal:open" class="small_text1" onclick="getack(<?php echo $data[class_id] . ', ' . $data[course_id]. ', ' . $data[user_id]; ?>);">Booking ACK</a>
                                </td>
                                <?php 
                                
                                } 
                                else if ($data['payment_status'] == 'PAID') 
                                { ?>
                                   <td colspan="2" width="22%"> PAID &nbsp;&nbsp;&nbsp;&nbsp;
                                    <!--code modified, date: 03/05/2015, reason: hiding invoice for company-->
                                    <?php if ($data['enrolment_mode'] != 'COMPSPON') { 
                                        $class_id=$data[class_id];
                                        $course_id=$data[course_id];
                                        $user_id=$data[user_id];
                                        $invoice_id=$data['invoice_id'];
                                        $payment_status=$data['payment_status'];
                                     echo   "<a href='#gen_invoice' onclick='getinvoice(\"$class_id\",\"$course_id\",\"$user_id\",\"$invoice_id\",\"$payment_status\")' "
                                             . "rel='modal:open' class='small_text1'>Invoice</a>&nbsp;&nbsp;&nbsp;&nbsp";
                                           
                                           ?>
<!--                                        <a href="#gen_invoice" onclick="getinvoice(<?php echo $data[class_id] . ', ' . $data[course_id] . ', ' . $var; ?>);" rel="modal:open" class="small_text1">(<?php echo $data['invoice_id'];?>)Invoice</a>&nbsp;&nbsp;&nbsp;&nbsp;-->
                                    <?php } if($status=='PAID'){
                                    echo   "<a href='#ack_receipt' onclick='get_ack_receipt(\"$class_id\",\"$course_id\",\"$user_id\",\"$invoice_id\")' "
                                             . "rel='modal:open' class='small_text1'>Receipt</a>&nbsp;&nbsp;&nbsp;&nbsp";
                                    }
                                    ?>
                                   
<!--                                    <a href="#ack_receipt" 
                                       onclick="get_ack_receipt(<?php echo $data[class_id] . ', ' . $data[course_id]. ', ' . $data[user_id]; ?>)" 
                                       rel="modal:open" class="small_text1">Receipt</a></td>-->
                                <?php 
                                } 
                                else 
                                { ?>
                                    <td colspan="2"><a href="#show_ack" rel="modal:open" class="small_text1" onclick="getack(<?php echo $data[class_id] . ', ' . $data[course_id]. ', ' . $data[user_id]; ?>);">Booking Acknowledgment</a></td>
                            <?php 
                                } ?> 
                        </tr> 

                        <?php } ?>   
                        <tr>
                            <td colspan="11" align="right" class="td_heading">Total:</td>
                            <td colspan="" class="td_heading"><?php
                            echo "$ ".number_format($total_paid_amnt, 2, '.', '')
                            //added number format by sankar    
                            ?>
                            </td>

                            <td colspan="" class="td_heading"><?php
                                echo "$ ". number_format($total_payable_amnt, 2, '.', '')
                            //added number format by sankar    
                            ?>
                            </td>                        </tr>
<!--                        <tr>
                            <td colspan="12" align="right" class="td_heading">Total Payable Amount:</td>
                            <td colspan="4" class="td_heading"><?php
                            echo number_format($total_payable_amnt, 2, '.', '')
                            //added number format by sankar    
                            ?>
                            </td>
                        </tr>-->

                </tbody>

            </table>
                        <?php } ?>
    </div>  
    <br/>
    <ul class="pagination pagination_style"><?php echo $pagination; ?></ul>
</div>

<!-- popup div start -->
<?php foreach ($tabledata as $key => $data) { ?>
    <div class="modalnew modal13" id="ex<?php echo $data['class_id']; ?>" style="display:none;">
        <h2 class="panel_heading_style">Training Details</h2>
        <div class="class_desc_course">

            <table class="table table-striped">                                                                                 
                <tr>
                    <td width="40%"><span class="crse_des">Class Code. :</span></td>
                    <td><?php echo $data['class_id']; ?></td>
                </tr>
                <tr>
                    <td><span class="crse_des">Class Name :</span></td>
                    <td><?php echo $data['class_name']; ?></td>
                </tr>
                <tr>
                    <td><span class="crse_des">Class Start Date and Time :</span></td>
                    <td><?php echo date('d/m/Y h:i A', strtotime($data['class_start_datetime'])); ?></td>
                </tr>
                <tr>
                    <td><span class="crse_des">Class End Date and Time :</span></td>
                    <td><?php echo date('d/m/Y h:i A', strtotime($data['class_end_datetime'])); ?></td>
                </tr>
                <tr>
                    <td><span class="crse_des">Class Room Location :</span></td>
                    <td><?php echo $status_lookup_location[$data['classroom_location']]; ?></td>
                </tr>
                <tr>
                    <td><span class="crse_des">Class Language :</span></td>
                    <td><?php echo $status_lookup_language[$data['class_language']]; ?></td>
                </tr>
               <tr>
                    <td><span class="crse_des">Class Status: </span></td>
                    <td style="height:25px;">
                    <?php
                    $start =  strtotime($data['class_start_datetime']);
                    $end = strtotime($data['class_end_datetime']);
                    $cur_date = strtotime(date("Y-m-d H:i:s"));
                    if($status_lookup_class_status[$data['class_status']] == 'COMPLTD'){
                        echo 'Completed';
                    }
                    elseif ($start > $cur_date && $end > $cur_date)
                    {
                        echo 'Yet to Start';
                    }
                    else if ($start <= $cur_date && $end >= $cur_date)
                    {
                        echo 'In-Progress';
                    }
                    elseif ($end < $cur_date && $start < $cur_date)
                    {
                        echo 'Completed';
                    }
                    //$status_lookup_class_status[$data['class_status']];
                    
                    ?></td>
                </tr>
            </table>

        </div> 

        <div class="popup_cancel11">
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Close</button></a>
        </div>

    </div> 
<?php } ?>
<!--  main content ends here -->

<div class="modal_020" id="ack_receipt" style="display:none;">  
</div>
<div class="modal_99" id="gen_invoice" style="display:none;">
</div>

<div class="modal_022" id="show_ack" style="display:none;">

</div>
<!-- popup div end -->
<script>
    function getack(cls, crs, usr) {
        $.ajax({
            url: "generate_acknowledgement",
            type: "post",
            data: 'cls=' + cls + '&crs=' + crs+ '&usr=' + usr, success: function(res) {
                $("#show_ack").html(res);
            },
            error: function() {

                $("#show_ack").html('error!!!');
            }
        });
    }
    function getinvoice(cls, crs, usr,invoice,status) {
        
        $.ajax({
            url: "generate_invoice",
            type: "post",
            data: 'cls=' + cls + '&crs=' + crs + '&usr=' + usr + '&invoice='+invoice +'&status='+status, 
            success: function(res) {
                $("#gen_invoice").html(res);
            },
            error: function() {
                $("#gen_invoice").html('error!!!');
            }
        });
    }
    function get_ack_receipt(cls, crs, usr, invoice) {
      
        $.ajax({
            url: "generate_ack_receipt",
            type: "post",
            data: 'cls=' + cls + '&crs=' + crs+ '&usr=' + usr + '&invoice='+ invoice, 
            success: function(res) {
                $("#ack_receipt").html(res);
            },
            error: function() {
                $("#ack_receipt").html('error!!!');
            }
        });
    }
</script>
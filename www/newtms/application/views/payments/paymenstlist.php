 <div style="clear:both;"></div>        
        <div class="col-md-12"  style="min-height: 360px;">
        <br>
                <h2 class="panel_heading_style">My Payments and Invoices</h2>
		<div class="table-responsive">
                <?php if(empty($tabledata)){?>
                <div class='error' style="text-align:center"><label class="no-result"><img src="<?php echo base_url(); ?>assets/images/no-result.png">You  have  not  done any  payments yet!</label></div>    
                <?php }else{?>
                <table class="table table-striped">
                <thead>
                <?php
                 $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                 $pageurl = $controllerurl;
                 ?>  
                <tr>
                    <th width="18%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=class_name&o=" . $ancher; ?>" >Training Details</a></th>
                    <th width="11%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=enrolment_mode&o=" . $ancher; ?>" >Account Type</a></th>
                    <th width="8%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=class_fees&o=" . $ancher; ?>" >Unit Fees<br>(SGD)</a></th>
                    <th width="8%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=total_inv_discnt&o=" . $ancher; ?>" >Discount<br>(SGD)</a></th>
                    <th width="8%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=total_inv_subsdy&o=" . $ancher; ?>" >Subsidy<br>(SGD)</a></th>
                    <th width="10%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=total_inv_amount&o=" . $ancher; ?>" >Amount Due<br>(SGD)</a></th>
                    <th width="7%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=total_gst&o=" . $ancher; ?>" >GST<br>(SGD)</a></th>
                    <th width="8%" class="th_header"><a style="color:#000000;" href="" >Net Due<br>(SGD</a></th>
                    <th colspan="2">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                foreach ($tabledata as $key => $data) {
                $total_amount += $data['total_inv_amount']; 
                ?>
                <tr>
                    <td><a href="#ex<?php echo $data['class_id']; ?>" rel="modal:open" class="small_text1"><?php echo $data['class_name']; ?></a></td>
                    <td><?php if($data['enrolment_mode'] != 'COMPANY' || $data['enrolment_mode'] != 'COMPSPON') echo "Individual"; else echo 'Company'; ?></td>
                    <td><?php echo $data['class_fees']; ?></td>
                    <td><?php echo $data['total_inv_discnt']; ?></td>
                    <td><?php echo $data['total_inv_subsdy']; ?></td>
                    <td><?php echo $data['total_inv_amount'] - $data['total_gst']; ?></td>
                    <td><?php echo $data['total_gst']; ?></td>
                    <td><?php echo $data['total_inv_amount']; ?></td>
                    
                    <?php if(  ($data['payment_status'] == 'PENDING' || $data['payment_status'] == 'NOTPAID') && ($data['enrolment_mode'] != 'COMPSPON' || $data['enrolment_mode'] != 'COMPANY')   ){ ?>
                    <td colspan="2" width="22%">
                        <span class="red">Unpaid</span> <a href="../user/enroll_now/?cls=<?php echo $data['class_id'];?>&crs=<?php echo $data['course_id'];?>&pay=update">(Pay Now)</a>
                        &nbsp;&nbsp;&nbsp;&nbsp;  <a href="#gen_invoice" onclick="getinvoice(<?php echo $data[class_id].', '.$data[course_id];?>);" rel="modal:open" class="small_text1">Invoice</a>
                        &nbsp;&nbsp; <a href="#show_ack" rel="modal:open" class="small_text1" onclick="getack(<?php echo $data[class_id].', '.$data[course_id];?>);">Booking ACK</a>
                    </td>
                    <?php } else if($data['payment_status'] == 'PAID'){?>
                    <td colspan="2" width="22%">Paid &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="#gen_invoice" onclick="getinvoice(<?php echo $data[class_id].', '.$data[course_id];?>);" rel="modal:open" class="small_text1">Invoice</a>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="#ack_receipt" onclick="get_ack_receipt(<?php echo $data[class_id].', '.$data[course_id];?>)" rel="modal:open" class="small_text1">Receipt</a></td>
                    <?php } else { ?>
                    <td colspan="2"><a href="#show_ack" rel="modal:open" class="small_text1" onclick="getack(<?php echo $data[class_id].', '.$data[course_id];?>);">Booking Acknowledgment</a></td>
                    <?php  } ?> 
                </tr> 

                <?php } ?>   
                        <tr>
                            <td colspan="7" align="right" class="td_heading">Total Amount:</td>
      
                            <td colspan="4" class="td_heading"><?php echo $total_amount; ?></td>
                        </tr>
      
                </tbody>
      
    </table>
                    <?php } ?>
        </div>  
            <!--<ul class="pagination pagination_style"><?php echo $pagination; ?></ul>-->
       </div>
 
         <!-- popup div start -->
                    <div class="modalnew modal13" id="ex<?php echo $data['class_id']; ?>" style="display:none;">
                            <div class="class_desc_course">
                                <h2 class="panel_heading_style">Training Details</h2>
                                      <table class="table table-striped">                                                                                 
   
                                        <tr>
                                            <td width="40%"><span class="crse_des">Class Code :</span></td>
                                            <td><?php echo $data['class_id']; ?></td>
                                        </tr>
                                        <tr>
                                            <td><span class="crse_des">Class Name :</span></td>
                                            <td><?php echo $data['class_name']; ?></td>
                                        </tr>
                                        <tr>
                                            <td><span class="crse_des">Class Start Date and Time :</span></td>
                                            <td><?php echo date('d/m/Y h:i A', strtotime($data['class_start_datetime']));?></td>
                                        </tr>
                                        <tr>
                                            <td><span class="crse_des">Class End Date and Time :</span></td>
                                            <td><?php echo date('d/m/Y h:i A', strtotime($data['class_end_datetime']));?></td>
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
                                            <td><div class="table-content1"><?php echo $data['class_status']; ?></div></td>
                                        </tr>
                                    </table>
                                                   
                            </div> 

                            <div class="popup_cancel11">
                                <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a>
                            </div>

                    </div> 
 <!--  main content ends here -->

<div class="modal_020" id="ack_receipt" style="display:none;">  
</div>
<div class="modal_99" id="gen_invoice" style="display:none;">
</div>

<div class="modal_022" id="show_ack" style="display:none;">

</div>
<!-- popup div end -->
<script>
function getack(cls, crs){
                $.ajax({
                    url: "generate_acknowledgement",
                    type: "post",
                    data: 'cls=' + cls + '&crs=' + crs,
                    success: function(res) {
                            $("#show_ack").html(res);
                    },
                    error: function() {
                     
                        $("#show_ack").html('error!!!');
                    }
                });
}
function getinvoice(cls, crs){
                $.ajax({
                    url: "generate_invoice",
                    type: "post",
                    data: 'cls=' + cls + '&crs=' + crs,
                    success: function(res) {
                       $("#gen_invoice").html(res);
                    },
                    error: function() {
                       $("#gen_invoice").html('error!!!');
                    }
                });
}
function get_ack_receipt(cls, crs){
                $.ajax({
                    url: "generate_ack_receipt",
                    type: "post",
                    data: 'cls=' + cls + '&crs=' + crs,
                    success: function(res) {
                        $("#ack_receipt").html(res);
                    },
                    error: function() {
                        $("#ack_receipt").html('error!!!');
                    }
                });
}
</script>
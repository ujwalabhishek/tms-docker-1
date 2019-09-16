<div style="clear:both;"></div>      
<div class="col-md-12" style="min-height: 390px;">
    <br>
    <h2 class="panel_heading_style">Class Enrollment for <?php echo $enrl_det; ?></h2>
    <div class="table-responsive">
        <?php
        $applicable_discount = $details['class'][class_discount];
        $this->session->unset_userdata('enrol_to');
        $this->session->unset_userdata('payment_update');
        if ($discount != 0) {
            $applicable_discount == $discount;
        }
        $subsidry = $details[course][subsidry];
        $gst = $details[course][gst];
        if ($fee['discount_type'] == 'DISINDVI')
            $discount_type = 'Individual';
        else if ($fee['discount_type'] == 'DISCOMP')
            $discount_type = 'Company';
        else if ($fee['discount_type'] == 'DISCLASS')
            $discount_type = 'Class';
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">Course Name:</td>
                    <td><label class="label_font"><?php echo $details[course][name]; ?></label></td>
                    <td class="td_heading">Class Name:</td>
                    <td><label class="label_font"><?php echo $details['class'][class_name]; ?></label></td>
                    <td class="td_heading">Start Date & Time:</td>
                    <td><label class="label_font"><?php echo date('d/m/Y h:i A', strtotime($details['class'][class_start_datetime])); ?></label></td>
                </tr>

                <tr>
                    <td class="td_heading">End Date & Time:</td>
                    <td><label class="label_font"><?php echo date('d/m/Y h:i A', strtotime($details['class'][class_end_datetime])); ?></label></td>
                    <td class="td_heading">Duration:</td>
                    <td><label class="label_font"><?php echo $details['class'][total_classroom_duration]; ?> Hours</label></td>
                    <td class="td_heading">Class Type:</td>
                    <td><label class="label_font">Classroom and Assessment</label></td>
                </tr>
            </tbody>
        </table>
    </div>

    <h2 class="sub_panel_heading_style">Payment Details</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">Per Student Unit Fees:</td>
                    <td><label class="label_font">$<?php echo number_format($fee['class_fee'], 2, '.', ''); ?> SGD</label></td>
                    <td class="td_heading"><?php echo $discount_type; ?> Discount <span class="box_bgcolor">@ <span id="disc_rate"><?php echo $fee['discount']; ?></span>%</span>:</td>
                    <td class="td_heading"><label class="label_font"><?php echo '$' . number_format($fee['discount_amount'], 2, '.', ''); ?> SGD</label></td>
                    <td class="td_heading">Amount Due:</td>
                    <td><label class="label_font"><?php echo '$' . number_format($fee['feeafter_discount'], 2, '.', ''); ?>SGD</label></td>
                </tr>  

                <tr>
                    <td class="td_heading">GST Rate @<?php if ($fee['gstrate']) echo $fee['gstrate']; ?>%:</td>
                    <td><label class="label_font"><?php
                            if ($fee['gstamount']) {
                                echo '$' . $fee['gstamount'] . 'SGD';
                            } else {
                                $zero = '0.00';
                                echo '$' . $zero . ' SGD';
                            }
                            ?></label></td>
                    <td class="td_heading">Net Fees Due:</td>
                    <td colspan="3"><label class="label_font"><?php echo '$' . $total_fee = $fee['net_fee']; ?> SGD</label></td>
                </tr>    
            </tbody>
        </table>
    </div>
    <br/>
    <div class="table-responsive" style="margin-top:-20px;">
        <div style="clear:both;"></div>
        <table class="table table-striped">

            <tbody>
                <?php if (!$pay) { ?>
                    <tr>
                        <td width="22%" class="td_heading">Enrollment Type</td>
                        <td width="18%">
                            <input type="radio" onclick="javascript:yesnoCheck();" name="yesno" id="noCheck" <?php if (!$_GET['enrolto']) echo 'checked'; ?> >Self&nbsp;&nbsp;
                            <input type="radio" onclick="javascript:yesnoCheck();" name="yesno" id="yesCheck" <?php if ($_GET['enrolto']) echo 'checked'; ?> >Friend <strong>OR</strong> Family 
                        </td>
                        <td width="60%" colspan="2">
                            <div id="ifYes" <?php if (!$_GET['enrolto']) echo 'style="display:none"'; ?>>        
                                <?php
                                $referl_options = array();
                                $referl_options[''] = 'Select';
                                foreach ($details[refrals] as $key => $item):
                                    $referl_options[$item['user_id']] = $item['first_name'] . '' . $item['last_name'];
                                endforeach;
                                echo form_dropdown('enrol_user_id', $referl_options, $_GET['enrolto'], 'id="enrol_user_id" onchange="javascript:set_enrol_to(this);"');
                                ?>
                            </div>          
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="4"><h4><?php
                            $msg_style = 'style="text-align:center;"';
                            if ($error) {
                                echo '<div class="error" class="error" '.$msg_style.'>' . $error . '</div>';
                            }
                            if ($msg) {
                                echo '<div class="success" '.$msg_style.'>' . $msg . '</div>';
                            }
                            ?>
                            <div id="show_error" class="error" <?php echo $msg_style; ?>> </div></h4>
                    </td>
                </tr>     
            </tbody>
        </table>

    </div>
    <?php if ($enrolled['type']) {
        ?>
        <div class="button_class99">
            <?php if ($enrolled['type'] == PAY_AFTER_ENROL) {
                ?>
                <a href="#bookingack" rel="modal:open" class="small_text1"><button type="button" class="btn btn-primary">
                        <span class="glyphicon glyphicon-retweet"></span>&nbsp;Booking Acknowledgment</button></a> &nbsp; &nbsp;
            <?php } else if ($enrolled['type'] == PAY_DURING_ENROL) {
                ?>
                <a href="#ack_receipt" rel="modal:open" class="small_text1"><button type="button" class="btn btn-primary">
                        <span class="glyphicon glyphicon-retweet"></span>&nbsp; Receipt</button></a>
            <?php } ?>
        </div>
        <?php
    } else {
        ?>
        <div class="button_class99" style="margin-left: 1%;">
            <?php
            if ($_GET['enrolto'])
                $addurl = '&enrolto=' . $_GET['enrolto'];
            if ($pay)
                $addurl .= '&pay=update';
            echo form_open('payment/pay?sandbox=1' . $addurl);
            ?>
            <input type="hidden" name="action" value="process" />
            <input type="hidden" name="cmd" value="_xclick" />  
            <input type="hidden" name="course_name" value="<?php echo $details[course][name]; ?>" />
            <input type="hidden" name="class_name" value="<?php echo $details['class'][class_name]; ?>" />
            <input type="hidden" name="courseId" value="<?php echo $details[course][id]; ?>" />
            <input type="hidden" name="userId" value="<?php echo $enrol_to; ?>" />
            <input type="hidden" name="classId" value="<?php echo $details['class'][class_id]; ?>" />
            <input type="hidden" name="enrol_to" id="enrol_to_pay">
            <input type="hidden" name="amount" value="<?php echo $total_fee; ?>" />
            <button type="submit" onclick="return check_enrol();" name="processpayment" class="btn btn-primary"><span class="glyphicon glyphicon-retweet"></span>&nbsp;<?php
                if ($pay)
                    echo "Pay Now";
                else
                    echo "Pay and Enroll";
                ?> </button>&nbsp; &nbsp;
    <?php echo form_close(); ?>  
        </div>
        <div class="button_class99" style="margin-left: 1%;">
            <?php
            if (($details['class'][class_pymnt_enrol] == PAY_AFTER_ENROL) && (!$pay)):
                if ($_GET['enrolto'])
                    $enrolto = '&enrolto=' . $_GET['enrolto'];
                echo form_open('user/enroll_now/?cls=' . $details['class'][class_id] . '&crs=' . $details[course][id] . $enrolto);
                ?>
                <input type="hidden" name="action" value="enroll" />
                <input type="hidden" name="course_id" id="course_id" value="<?php echo $details[course][id]; ?>" />
                <input type="hidden" name="enrol_to" id="enrol_to" >
                <input type="hidden" name="class_fee" value="<?php echo $details['class'][class_fees]; ?>" />
                <input type="hidden" name="class_id" id="class_id" value="<?php echo $details['class'][class_id]; ?>" />
                <input type="hidden" name="amount" value="<?php echo $total_fee; ?>" />
                <input type="hidden" name="discount" value="<?php echo $applicable_discount; ?>" />
                <button type="submit" class="btn btn-primary small_text1"><span class="glyphicon glyphicon-retweet"></span>&nbsp;Enroll and Pay Later</button>
                <?php
                echo form_close();
            endif;
            ?>   
        </div>
<?php } ?>

    <div style="clear:both;"></div>
    <!-- popup div -->
    <div class="modal_022" id="bookingack" print_ack style="display:none;">
        <p>
        <h2 class="panel_heading_style">Booking Acknowledgment</h2>
        <form action="<?php echo base_url(); ?>payments/print_ack" method="post">
            <strong>Booking #: <!-- B001456 --> <?php echo date('M d Y'); ?> </strong><br /><br />
            Your seat has been temporarily booked. Please pay the class fees on or before the class start date.<br>
            <i>Temporary booking for <?php echo $enrolled['enrolled_to']; ?> for <?php echo $details[course][name] . ' - ' . $details['class'][class_name]; ?>.</i><br>
            <strong>Class start date:</strong> <?php echo date('M d Y, l', strtotime($details['class'][class_start_datetime])) . ' @ ' . date('h:i A', strtotime($details['class'][class_start_datetime])); ?><br>
            <strong>Location:</strong> <?php echo $details['class']['classroom_location']; ?><br>
            <strong>Contact Details:</strong> <?php echo $ack_data[crse_manager][first_name] . ' ' . $ack_data[crse_manager][last_name] . ', ' . $ack_data[crse_manager][contact_number] . ', ' . $ack_data[crse_manager][registered_email_id]; ?>
            <div style="clear:both;"></div><br>
            <input type="hidden" name="courseId" value="<?php echo $details[course][id]; ?>" />
            <input type="hidden" name="classId" value="<?php echo $details['class'][class_id]; ?>" />
            <input type="hidden" name="userId" value="<?php echo $enrol_to; ?>" />

            <div class="popup_cance89">
                <a href="#"><button class="btn btn-primary" type="submit" id="print_ack">Print</button></a>
                <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Close</button></a>
            </div>
        </form>
        </p>
    </div>

    <div class="modal_020" id="ack_receipt" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Payment Receipt</h2><br>
        <form action="<?php echo base_url(); ?>payments/print_receipt" method="post">
            <table width="100%">
                <tbody>
                    <tr>
                        <td rowspan="4"><img src="<?php echo base_url(); ?>logos/<?php echo $this->session->userdata('public_tenant_details')->Logo; ?>" border="0" /></td>
                        <td colspan="2"><?php echo $details['tanant']['tenant_name']; ?></td>
                        <td class="td_heading">Receipt No.:</td>
                        <td><?php echo date('Y').''.$ack_data['invoice_id'] ; ?></td>
                    </tr>
                    <tr>
                        <td colspan="2"><?php echo $details['tanant']['tenant_address'] . ' , ' . $details['tanant']['tenant_city']; ?></td>
                        <td class="td_heading">Receipt Date:</td>
                        <td><?php echo date('M d Y'); ?></td>
                    </tr>                    
                    <tr>
                        <td colspan="4"><?php echo $details['tanant']['tenant_contact_num']; ?></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <p><font style="font-size:14px; font-weight:bold;">Received</font> with thanks SGD <?php echo $total_fee; ?> from <?php echo $enrolled['enrolled_to']; ?> for <?php echo $details[course][name] . ' - ' . $details['class'][class_name]; ?>.. Mode of payment: Online Transfer.</p>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td class="td_heading">Class Start Date:</td>
                        <td><?php echo date('M d Y, l', strtotime($details['class'][class_start_datetime])) . ' @ ' . date('h:i A', strtotime($details['class'][class_start_datetime])); ?></td>
                    </tr>

                    <tr>
                        <td class="td_heading">Location:</td>
                        <td><?php echo $details['class']['classroom_location']; ?></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Contact Details:</td>
                        <td><?php echo $ack_data[crse_manager][first_name] . ' ' . $ack_data[crse_manager][last_name] . ', ' . $ack_data[crse_manager][contact_number] . ', ' . $ack_data[crse_manager][registered_email_id]; ?></td>
                    </tr>
                </tbody>
            </table>
            <br/>
            <p class="red"><i>This is a computer generated receipt and doesn't require a seal or signature.</i></p>
            <input type="hidden" name="courseId" value="<?php echo $details[course][id]; ?>" />
            <input type="hidden" name="classId" value="<?php echo $details['class'][class_id]; ?>" />
            <input type="hidden" name="userId" value="<?php echo $enrol_to; ?>" />

            <div style="clear:both;"></div><br>

            <div class="popup_cance89">
                <a href="#"><button id="print_ack_receipt" class="btn btn-primary" type="submit">Print</button></a></div>
        </form>
        </p>
    </div>
    <!-- pop up div end -->
</div>
<!-- code to reload page with friend enrollment id -->

<?php
$selfurl = str_replace('index.php/', '', $_SERVER['PHP_SELF']);
$sting = strchr($_SERVER['QUERY_STRING'], '&enrolto=');
$query_string = str_replace($sting, '', $_SERVER['QUERY_STRING']);
$loadurl = $selfurl . '?' . $query_string;
?>
<input type="hidden" id="cururl" value="<?php echo $loadurl; ?>">
<script type="text/javascript">
    function yesnoCheck() {
        if (document.getElementById('yesCheck').checked) {
            document.getElementById('ifYes').style.display = 'block';
        }
        else {
            document.getElementById('ifYes').style.display = 'none';
            var cururl = $('#cururl').val();
            window.location.href = cururl;
        }
    }
    function set_enrol_to(e) {
        var enrol_to = $('#enrol_user_id').val();
        var cururl = $('#cururl').val();
        window.location.href = cururl + '&enrolto=' + enrol_to;
    }
</script>

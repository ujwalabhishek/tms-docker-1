<?php
echo $this->load->view('common/refer_left_wrapper');
?>
<div class="ref_col ref_col_tax_code" style="min-height: 500px;">	
    <div class="container_row">
        <div class="col-md-12 min-pad">                              
            <div id ='trainee_validation_div'>
                <div class="bs-example">                    
                    <div class="table-responsive">
                        <div class="col-md-12 min-pad">
                            <?php
                            if (!empty($error_message)) {
                                echo 
 '<div style="color:#a94442;font-weight: bold;text-align:center;margin:1px 14px 20px 20px;padding:30px;background-color:#f2dede"><img src="' . base_url() . 'assets/images/no-result.png"> ' . $error_message . '</div>';
                            }
                            if (!empty($success_message)) {
                                echo '<div  style=" padding: 10px; width: 815px; text-align: center; margin: 0px auto;">
                                <div style="color:green;font-weight: bold;">
                                    ' . $success_message . '
                                </div>
                            </div>';
                            }
                            ?> 
                            <br/>

                            <?php
                            if (!empty($booking_ack)) {
                                echo $this->load->view('enrol/booking_ack_div', $booking_ack);
                            }
                            if (!empty($payment_receipt)) {
                                echo $this->load->view('enrol/receipt_div', $payment_receipt);
                            }
                            ?>
                        </div>                            
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>    
<?php
echo form_open('course/refer', 'id="back_to_refer_form"');
echo form_hidden('user_id', $this->session->userdata('refer_user_id'));
echo form_hidden('submit_but', $this->session->userdata('submit_status'));
echo form_close();
?>
<script>
    $(document).ready(function() {
        $('#back_to_refer').click(function() {
            $('#back_to_refer_form')[0].submit();
        });
    });
</script>
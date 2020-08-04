<?php echo $this->load->view('common/refer_left_wrapper'); ?>
<div style=" margin: 0px 0px 0px auto;">
    <?php
    if (!empty($success)) {
        echo '<div style="color:green;font-weight: bold; padding: 10px;text-align:center;">                                
                        ' . $success . '
                    </div>';
    }


    


     if (!empty($error_message)) {


        echo '<div style="color:red;font-weight: bold; padding: 10px;text-align:center;">                                
                        ' . $error_message . '
                    </div>';
    }
    ?>
    <div id="pay_details" class="receipt-div col-md-12"  style="height: 465px; margin: 0px 0 auto 60px;">
        <h2 class="panel_heading_style">Payment Details for 
            ' <?php echo ($trainee_data->gender == 'MALE') ? 'Mr. ' : 'Ms. '; ?>
            <?php echo $trainee_data->first_name . ' ' . $trainee_data->last_name; ?>'
            (NRIC/ FIN No.:<?php echo $trainee_data->tax_code; ?>)

        </h2>
        <h2 class="sub_panel_heading_style">Class Details</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td class="td_heading">Course Name:</td>
                        <td><label class="label_font"><?php echo $course_details->crse_name; ?></label></td>
                        <td class="td_heading">Class Name:</td>
                        <td><label class="label_font"><?php echo $class_details->class_name; ?></label></td>
                        <td class="td_heading">Class Type:</td>
                        <td ><label class="label_font"><?php echo $class_type; ?></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Start Date & Time:</td>
                        <td><label class="label_font"><?php echo date('d/m/Y h:i A', strtotime($class_details->class_start_datetime)); ?></label></td>


 


                        <td class="td_heading">End Date & Time:</td>
                        <td colspan="3"><label class="label_font"><?php echo date('d/m/Y h:i A', strtotime($class_details->class_end_datetime)); ?></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Classroom Venue:</td>
                        <td colspan="5"><?php echo $classloc;?></td>


 


                        


                    </tr>
                </tbody>
            </table>
        </div><br>
        <h2 class="sub_panel_heading_style">Payment Details</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>

                    <tr>
                        <td class="td_heading" width='100px;'>Unit Fees:</td>
                        <td><label class="label_font">$ &nbsp;<?php echo number_format($class_details->class_fees, 2, '.', ''); ?></label></td>
                        <td class="td_heading"  width='150px;'>Discount@ <?php echo number_format($class_details->class_discount, 2, '.', ''); ?>%: </td>
                        <td><?php echo '$ ' . number_format($discount_total, 2, '.', ''); ?></td>
                        <td class="td_heading" width="165px"><?php echo $gst_label; ?>: </td>
                        <td>$ <?php echo number_format($totalgst, 2, '.', ''); ?>


                            


                        </td>


 


                    </tr>  
                    <tr>
                        <td class="td_heading">Net Fees Due:</td>
                        <td colspan="5"><label class="label_font" style="color: blue"><?php echo '$ ' . number_format($net_due, 2, '.', ''); ?></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Additional Remarks:</td>
                        <td colspan="5">
                            <?php
                            $attr = array(
                                'name' => 'pers_additional_remarks',
                                'id' => 'pers_additional_remarks',
                                'rows' => '3',
                                'cols' => '95',
                                'value' => $additional_remarks,
                                'maxlength' => 500,
                            );
                            echo form_textarea($attr);
                            ?>  
                            <span id="pers_additional_remarks_err"></span>
                        </td>                                                                        
                    </tr>
                </tbody>
            </table>
        </div>
        <br/>
        <span style="float:right;">
            <?php $query_string = (SANDBOX ? '?sandbox=1' : ''); ?>
            <?php echo form_open("course_public/create_classenroll$query_string", "id='enroll_now'"); ?>
            <?php echo form_hidden('user_id', $user_id) ?>
            <?php echo form_hidden('additional_remarks', $additional_remarks) ?>
            <?php echo form_hidden('class_id', $class_details->class_id) ?>
            <?php echo form_hidden('course_id', $class_details->course_id) ?>
            <?php echo form_hidden('class_name', $class_details->class_name) ?>
            <?php echo form_hidden('class_fees', $class_details->class_fees) ?>
            <?php echo form_hidden('discount_rate', $class_details->class_discount) ?>
            <?php echo form_hidden('discount_amount', $discount_total) ?>
            <?php echo form_hidden('gst_rate', $gst_rate) ?>
            <?php echo form_hidden('gst_amount', $totalgst) ?>
            <?php echo form_hidden('gst_onoff', $course_details->gst_on_off) ?>
            <?php echo form_hidden('gst_rule', $course_details->subsidy_after_before) ?>
            <?php echo form_hidden('net_due', $net_due) ?>


            <?php echo form_hidden('cmd', '_xclick') ?>


            <?php if ($class_details->class_pymnt_enrol == PAY_A_ENROL) { ?>
                <button type="submit" name="submit" value="book_now" class="btn btn-sm btn-info" style="float: right;margin-left: 10px;"><strong>Book Now</strong></button>
            <?php }else{ ?>
                <center style='color:red'> Kindly Contact admin, If you wants to Book this class</center>
            <?php } ?>

<!--                <button type="submit" name="submit" value="pay_now" class="btn btn-sm btn-info" style="float: right;"><strong>Pay Now</strong></button>-->


            <?php echo form_close(); ?>
        </span>
    </div><br><br><br>
</div>

<!----------------modal by ssp start----------------------->
<?php if (TENANT_ID == 'T02'){$show='display:block';}else{$show='display:none';}?>
<div class="modal" id="ex1011" style="<?php //echo $show;?>;margin:auto;margin-top:20px;margin-bottom:20px;height: auto;left: 0px !important;">
<p>
  <h2 class="panel_heading_style">Declaration</h2>
    <!--Section: Contact v.2-->
<section class="mb-4">

    <!--Section heading-->
    <!--<h2 class="h1-responsive font-weight-bold text-center my-4">Declaration Form</h2>-->
    <!--Section description-->
    <p class="text-center alert alert-danger">You must fill this form to continue for the enrollment,I consent for Xprienz to collect and use my personal data for the purposes of the company policy.</p>
  
    <div class="row">

        <!--Grid column-->
        <div class="col-md-12 mb-md-0 mb-5">
           
        
                <!--Grid row-->
                <div class="row">

                    <!--Grid column-->
                    <div class="col-md-12">
                        <div class="md-form mb-0">
                            <label for="name" class="">Your Name<span style='color:red'>*</span></label>
                            <input type="text" id="dec_name" class="form-control" value="<?php echo $trainee_data->first_name;?>" required>
                            
                        </div>
                    </div>
                    <!--Grid column-->
                </div>
                
                <div class="row">
                    <!--Grid column-->
                    <div class="col-md-6">
                        <div class="md-form mb-0">
                             <label for="email" class="">Your Email</label>
                             <input type="email" id="dec_email" class="form-control" pattern=".+@gmail.com" size="30" required>
                           
                        </div>
                    </div>
                    <!--Grid column-->
                    <!--Grid column-->
                    <div class="col-md-6">
                        <div class="md-form mb-0">
                             <label for="email" class="">Your Mobile<span style='color:red'>*</span></label>
                            <input type="tel" id="dec_mobile" class="form-control" name="phone" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required>
                           
                        </div>
                    </div>
                    <!--Grid column-->
                    
                </div>
                <!--Grid row-->

                <!--Grid row-->
                <div class="row">
                    <div class="col-md-6">
                        <div class="md-form mb-0">
                            <label for="subject" class="">Lesson Date:<span style='color:red'>*</span></label>
                            <input type="date" id="dec_les_time" class="form-control" required>
                            
                        </div>
                    </div>
                    <div class="col-md-6"></div>
                </div>
                <!--Grid row-->

                <!--Grid row-->
                <div class="row">

                    <!--Grid column-->
                    <div class="col-md-12">

                        <div class="md-form">
                            <label for="message">Do you have any relatives who returned from China on 15th January or later and is staying together? <span style='color:red'>*</span></label>
                            <div><input type="radio" value="1" class="" name='dec_res' id='dec_res' style='height: 1.7em;width: 20px;' checked><b style='padding:2px'>Yes</b></div>
                            <div><input type="radio" value="0" class="" name='dec_res' id='dec_res1' style='height: 1.7em;width: 20px;'><b style='padding:2px'>No</b> </div> 
                        </div>

                    </div>
                    <div class="col-md-12">

                        <div class="md-form">
                            <label for="message">Have you travelled overseas in the past 14 days? <span style='color:red'>*</span></label>
                            <input type="text" id="dec_overseas" placeholder='Please State' class="form-control" required>
                        </div>

                    </div>
                </div>
                
                 <div class="statuserr"></div>
                 <br>
                 <br>
                <!--Grid row-->
                 <div class="text-center">
                    <a href='#' class="btn btn-primary" id='declarations' type='button'>Submit & Continue</a></div>
                  
           
        </div>
        <!--Grid column-->
    </div>
         
</section>
<!--Section: Contact v.2-->

</p>
</div>
<!----------------modal by ssp end----------------------->

<script type="text/javascript">
    $("#enroll_now").submit(function (event) {
        var addlRemark = $('#pers_additional_remarks').val();
        $( "input[name='additional_remarks']" ).val(addlRemark);
        return;
    });
    
    $(document).ready(function(){
     //added by shubhranshu on 30 jan 2020 new declaration for trainee enrol  
     <?php if (TENANT_ID == 'T02'){?>
    $('#declarations').click(function(){
        $status = 1;
        if($('#dec_name').val()==''){
            $status=0;
        }
//        if($('#dec_email').val()==''){
//            $status=0;
//        }
        if($('#dec_mobile').val()==''){
            $status=0;
        }
        if($('#dec_overseas').val()==''){
            $status=0;
        }
        if($('#dec_les_time').val()==''){
            $status=0;
        }
        
        if($status == 1){
            $('#ex1011').hide();
            $('.statuserr').html('');
            $siteurl = '<?php echo site_url(); ?>';
            $url = $siteurl + "class_trainee/save_declaration_trainee_data";
            $.ajax({
                url: $url,
                type: "post",
                dataType: "json",
                data: {
                    tax_code: '<?php echo $trainee_data->tax_code;?>',
                    type:'PUBLIC_PORTAL',
                    name: $('#dec_name').val(),  
                    email: $('#dec_email').val(),
                    mobile: $('#dec_mobile').val(),
                    user_id:'<?php echo $user_id;?>',
                    res: $('input[name="dec_res"]:checked').val(),
                     class_id:'<?php echo $class_details->class_id;?>',
                    lesson_timing: $('#dec_les_time').val(),
                    overseas: $('#dec_overseas').val()
                },
                success: function(data) {
                   if(data !='1'){
                      $('#ex1011').show();  
                  }
                }
            });
        }else{
             $('.statuserr').html('<span style="color:red">Please fill all the (*) Mark fields to Continue!</span>');
        }
    }); 
     <?php } ?>
 });
</script>

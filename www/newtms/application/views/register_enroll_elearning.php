<?php 
$this->load->helper('metavalues_helper');

$this->load->helper('common_helper');

echo $this->load->view('common/refer_left_wrapper');

//echo validation_errors('<div class="error1">', '</div>');
?>
<script>

    $siteurl = '<?php echo site_url(); ?>';

    $baseurl = '<?php echo base_url(); ?>';

</script>



<div class="col-md-10" style='font-size: 13px;'>  
    <h2 class="panel_heading_style">
        <span aria-hidden="true" class="glyphicon glyphicon-user"></span> 
          Check NRIC:

    </h2>
    <?php

    if (!empty($error_message)) {
                    echo '<div style="color:red;font-weight: bold;">' . $error_message . '</div>';
                }
    if ($this->session->flashdata('error')) {
                    echo '<div style="color:red;font-weight: bold;">
                            ' . $this->session->flashdata('error') . '
                        </div>';
                }
    ?>
<!--    <div class="tax_col">
</div>-->

    <?php 
            $atr = 'id="trainee_form2" name="trainee_form2"';
           // echo form_open_multipart("course/enrol_once", $atr);
            echo form_open_multipart("course_public/confirm_trainee_details", $atr);
           //  echo form_open_multipart("user/add_trainee1", $atr);
             $user_id;
            echo form_hidden('r_user_id', $user_id);
            if(!empty($user_id)){
                 echo form_hidden('loggedin', 1);
            }
            
            echo form_hidden('country_of_residence', 'SGP');
            echo form_hidden('course_id', $course_id);
            echo form_hidden('class_id', $class_id);
            echo form_hidden('registration', '1');
            
    ?>  
<?php
if($course_id!='' && $class_id!=''){
   
?>
<div style="color:black;font-weight: bold; padding: 6px;text-align:center;width:80%" class="reg_tbl_div">                                
    <table class="table table-striped" style="">
                 <tbody>
                    <tr>
                        <td  class="td_heading">Class Name: 
                            <label class="label_font"><?php echo $class_details->class_name; ?></label>&nbsp;&nbsp;&nbsp;
                               <a href="#ex12" rel="modal:open" class="small_text1"> <label class="label_font" style="cursor: pointer;">View Details</label>
                            </a>
                        </td>
                        <td class="td_heading">Unit Fees: <label class="label_font">&nbsp;&nbsp;$&nbsp;<?php echo number_format($class_details->class_fees, 2, '.', ''); ?>
                            
                            </label></td>
                        <td class="td_heading">Discount@ : <label class="label_font">&nbsp;&nbsp;$
                            <?php echo number_format($class_details->class_discount, 2, '.', ''); ?>%</td>
                    </tr>
                
                    <tr style="display:none;">
                     
                        <td class="td_heading" ><?php echo $gst_label; ?>:<label class="">$ <?php echo number_format($totalgst, 2, '.', ''); ?></label> </td>
                        <td colspan="2" class="td_heading">Net Fee: <label class=""><?php echo '$ ' . number_format($net_due, 2, '.', ''); ?></label></td>
                        
                    </tr>  
                    <tr>
                     
                     <td class="td_heading" ><?php echo $gst_label; ?>: <label class="label_font">$ <?php echo number_format($totalgst, 2, '.', ''); ?></label></td>
                     <td colspan="2" class="td_heading">Net Fee: <label class="label_font"><?php echo '$ ' . number_format($net_due, 2, '.', ''); ?></label></td>
                        
                    </tr>  
                   
                </tbody>
    </table>
</div>

<div class="modalnew modal13" id="ex12" style="display:none;height:280px !important; min-height: 280px !important;">
      <h2 class="panel_heading_style" style="margin-bottom: -3px !important;">Class Details </h2>
                <div class="class_desc_course">
                    <div class="table-responsive">
                        <table class="table table-striped">     
                            <tr>
                                <td width="40%"><span class="crse_des">Course Name :</span></td>
                                <td><?php echo   $course_details->crse_name;?></td>
                            </tr>
                            <tr>
                                <td><span class="crse_des">Class Name :</span></td>
                                <td><?php echo $class_details->class_name; ?></td>
                            </tr>
                            <tr>
                                <td><span class="crse_des">Class Start Date and Time :</span></td>
                                <td><?php echo date('d/m/Y h:i A', strtotime($class_details->class_start_datetime)); ?></td>
                            </tr>
                            <tr>
                                <td><span class="crse_des">Class End Date and Time :</span></td>
                                <td><?php echo  date('d/m/Y h:i A', strtotime($class_details->class_end_datetime)); ?></td>
                            </tr>
                            <tr>
                                <td><span class="crse_des">Classroom Location :</span></td>
                                <td><?php echo $classloc; ?></td>
                            </tr>
                            

                        </table>
                    </div>                                
                </div>
                <div class="popup_cancel11">
                    <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Close</button></a>
                </div>
            </div>  

<?php } ?>

    <div id ='trainee_validation_div' class='reg_tbl_div' style='width:80%'>
        <div class="bs-example">                    
                    <div class="table-responsive1">
                        
    
    
                        <table class="table table-striped" width="100%" >   


                            <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/personal_details.png"/>  Access Detail</h2>
                            
                             <tr>
                                <td class="td_heading" style='padding:15px;text-align: right;'>Please Enter NRIC:<span class="required">*</span></td>
                                <td style="text-align: left;">
                                  
                                    <?php                            
                                            $taxcode_nric = array(
                                                'name' => 'taxcode_nric',
                                                'id' => 'taxcode_nric',
                                                'value' => $this->input->post('taxcode_nric'),
                                                'maxlength' => '25',
                                                'class' => 'upper_case',
                                                //'onblur' => 'javascript:check_taxcode_nric(this.value,this.id);',
                                                'onkeypress' =>'return IsAlphaNumeric(event);',
                                                'style' => 'width: 328px;padding: 6px;border-radius: 5px;',

                                            );
                                             
                                 echo form_input($taxcode_nric);
                                    ?>
                                    <input type="hidden" id="course_id" name="course_id" value="<?php echo $course_id;?>">
                                    <input type="hidden" id="class_id" name="class_id" value="<?php echo $class_id;?>">
                                    
                                    <span id="error" style="color: Red; display: none"></span>
                                    <span id="nric_found"> </span>
                                    
                                <span id="r_try" style="color: red;"> </span>
                                <span id="nric_res"> </span>
                                <span id="taxcode_nric_err"></span>
                                    
                                </td>
                             
                            </tr>
                            <tr> <td colspan="2" class='text-center'><button type='button' class='btn btn-primary nric_submit'>Submit</button></td><tr>
                           


                        </table>
                          </div>
<!--           </div>               <br>-->
             </div>             
    </div>

<br/><br/>

<!----------------modal by ssp start----------------------->
<?php if (TENANT_ID == 'T02'){$show='display:none';}else{$show='display:none';}?>
<div class="modal" id="ex1011" style="<?php echo $show;?>;margin:auto;margin-top:20px;margin-bottom:20px;height: auto;left: 0px !important;">
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
                            <input type="text" id="dec_name" class="form-control" required>
                            
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



<div class="modal" id="modal_nric_found" style="display:hidden;margin:auto auto;height: 263px;left: 0px !important;">

  <h2 class="panel_heading_style">NRIC DETAILS</h2>
    <!--Section: Contact v.2-->
<section class="mb-4">
    <!--<form action='course_public/confirm_trainee_details' method='post'>-->
    <!--Section heading-->
    <!--<h2 class="h1-responsive font-weight-bold text-center my-4">Declaration Form</h2>-->
    <!--Section description-->
    <p class="text-center alert-success msg" style='padding: 10px;'></p>
    <br>
    <h4 class="text-center">Are you Sure! You want to Continue?</h4>
    <br>
    <div class="text-center"><button type='button' class='btn btn-primary yescls' style='padding: 3px 17px !important;font-size: 18px !important;'>Yes</button>&nbsp;&nbsp;&nbsp;<a href='#'class='btn btn-primary nocls' style='padding: 3px 17px !important;font-size: 18px !important;'>No</a></div>
    <input type='hidden' id='user_id_popup' name='user_id_popup' value=''>
    <input type='hidden' id='class_id_popup' name='class_id_popup' value='<?php echo $class_id;?>'>
    <input type='hidden' id='course_id_popup' name='course_id_popup' value='<?php echo $course_id;?>'>
    </form>    
</section>
<!--Section: Contact v.2-->


</div>



<!----------------modal by ssp end----------------------->

<!--<script src="<?php echo base_url(); ?>assets/js/validation_old.js" type="text/javascript"></script>-->
<script src="<?php echo base_url(); ?>assets/public_js/validation_register_enroll.js?v=2" type="text/javascript"></script>
<script>


    $("input:radio[name=relationship]").click(function() {


        var value = $(this).val();
        if (value == 'OTHERS') {
            $('#others_span').show();
        }
        else {
            $('#others_span').hide();


             $('#others').val('');


        }

    });

        var specialKeys = new Array();
        specialKeys.push(8); //Backspace
        specialKeys.push(9); //Tab
        specialKeys.push(46); //Delete
        specialKeys.push(36); //Home
        specialKeys.push(35); //End
        specialKeys.push(37); //Left
        specialKeys.push(39); //Right
        function IsAlphaNumeric(e) {
            var keyCode = e.keyCode == 0 ? e.charCode : e.keyCode;
            var ret = ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122) || 
                    (specialKeys.indexOf(e.keyCode) != -1 && e.charCode != e.keyCode));
            document.getElementById("error").style.display = ret ? "none" : "inline";
            return ret;
        }
        
    $(document).ready(function(){
        $('.nocls').click(function(){
             $('#modal_nric_found').hide();
             //document.trainee_form2.action = "<?php //echo base_url(); ?>"+"course_public/referral_credentials1";
             //window.location = "<?php //echo base_url(); ?>";
              //$('#trainee_form2').submit();
        });
        $('.yescls').click(function(){
             $('#modal_nric_found').hide();
             $('#trainee_form2').submit();
        });
       
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
            
            $url = $siteurl + "class_trainee/save_declaration_trainee_data";
            $.ajax({
                url: $url,
                type: "post",
                dataType: "json",
                data: {
                    tax_code: $('#taxcode_nric').val(),
                    type:'PUBLIC_PORTAL',
                    name: $('#dec_name').val(),  
                    email: $('#dec_email').val(),
                    mobile: $('#dec_mobile').val(),
                    user_id:'<?php echo $user_id;?>',
                    res: $('input[name="dec_res"]:checked').val(),
                    class_id:'<?php echo $class_id;?>',
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
 
 function blockSpecialCharacters(e) {
            let key = e.key;
            let keyCharCode = key.charCodeAt(0);

            // 0-9
            if(keyCharCode >= 48 && keyCharCode <= 57) {
                return key;
            }
            // A-Z
            if(keyCharCode >= 65 && keyCharCode <= 90) {
                return key;
            }
            // a-z
            if(keyCharCode >= 97 && keyCharCode <= 122) {
                return key;
            }

            return false;
    }

    $('#taxcode_nric').keypress(function(e) {
        blockSpecialCharacters(e);
    });
    </script>
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
    <h2 class="panel_heading_style"><span aria-hidden="true" class="glyphicon glyphicon-user"></span> Trainee Registration / Update:</h2>
    <?php
    if ($this->session->flashdata('error')) {
        echo $this->session->flashdata('error');
    }   
    ?>  

    <div class="panel panel-primary">
      <div class="panel-heading"><img src="<?php echo base_url(); ?>/assets/images/personal_details.png"/>  Access Detail</div>
      <div class="panel-body">
          <form class="form-horizontal" action="/action_page.php">
            <div class="form-group">
              <label class="control-label col-sm-2" for="email">Please Enter NRIC:<span class="required">*</span></label>
              <div class="col-sm-10">
                <?php 
                 $atr = 'id="trainee_form2" name="trainee_form2"';
                     echo form_open_multipart("course_public/confirm_trainee_detail", $atr);

                    $taxcode_nric = array(
                        'name' => 'taxcode_nric',
                        'id' => 'taxcode_nric',
                        'value' => $this->input->post('taxcode_nric'),
                        'maxlength' => '25',
                        'class' => 'upper_case',
                        'placeholder' => 'Enter Your NRIC',
                        //'onblur' => 'javascript:check_taxcode_nric(this.value,this.id);',
                        'onkeypress' =>'return IsAlphaNumeric(event);',
                        'style' => 'width: 328px;padding: 6px;border-radius: 5px;',

                    );

                    echo form_input($taxcode_nric);
                ?>
              </div>
            </div>
              <div><span id="error" style="color: Red; display: none"></span><span id="nric_found"> </span><span id="taxcode_nric_err"></span></div>
            
            <div class="form-group">        
                <div class="col-sm-offset-2 col-sm-10">
                  <button type="button" class="btn btn-default" id='declarations'>Submit & Continue</button>
                </div>
            </div>
        </form>
      </div>
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
    <h4 class="text-center suremsg">Are you Sure! You want to Continue?</h4>
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

<script src="<?php echo base_url(); ?>assets/public_js/validation_register_enroll.js?v=3" type="text/javascript"></script>
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
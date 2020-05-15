<?php 
$this->load->helper('metavalues_helper');

$this->load->helper('common_helper');

echo $this->load->view('common/refer_left_wrapper_public');

//echo validation_errors('<div class="error1">', '</div>');
?>
<script>

    $siteurl = '<?php echo site_url(); ?>';

    $baseurl = '<?php echo base_url(); ?>';

</script>
<style>
    .panel-primary>.panel-heading {
        background-color:#446cb3 !important;
    }
    .nric_submit{
            padding: 7px 59px !important;
    font-size: 13px !important;
    }
</style>


<div class="col-md-10 bodymain" style='font-size: 13px;'>
    <h2 class="panel_heading_style"><span aria-hidden="true" class="glyphicon glyphicon-user"></span> Trainee Registration / Update:</h2>
    <?php
    if ($this->session->flashdata('error')) {
        echo $this->session->flashdata('error');
    }   
    ?>  

    <div class="panel panel-primary">
      <div class="panel-heading"><span aria-hidden="true" class="glyphicon glyphicon-user"></span>  Access Detail</div>
      <div class="panel-body">
          <?php 
                $atr = 'id="trainee_form2" name="trainee_form2"';
                echo form_open_multipart("course_public/confirm_trainee_detail", $atr);

                ?>
            <div class="form-group text-center">
                <div class="col-sm-4"></div>
                
                <div class="col-sm-4">
                 <label type="email">PLEASE ENTER NRIC:<span class="required">*</span></label>
                <?php 

                    $taxcode_nric = array(
                        'name' => 'taxcode_nric',
                        'id' => 'taxcode_nric',
                        'value' => $this->input->post('taxcode_nric'),
                        'maxlength' => '25',
                        'class' => 'upper_case form-control',
                        'placeholder' => 'Enter Your NRIC',
                        //'onblur' => 'javascript:check_taxcode_nric(this.value,this.id);',
                        'onkeypress' =>'return IsAlphaNumeric(event);',
                        'style' => 'padding: 6px;border-radius: 5px;',

                    );

                    echo form_input($taxcode_nric);
                ?>
                </div>
                <div class="col-sm-4"></div>
            </div>
            
            <div class='row'>
                <div class='col-sm-12'><span id="taxcode_nric_err"></span><span id="error"></span><br></div>
            </div>
            <div class="form-group">        
                <div class="col-sm-12 text-center">
                  <button type="button" class="btn btn-primary nric_submit">Submit</button>
                </div>
            </div>
     
      </div>
    </div>

<!--<div class="modal" id="modal_nric_found" style="display:hidden;margin:auto auto;height: 274px;left: 0px !important;">

  <h2 class="panel_heading_style">NRIC DETAILS</h2>
    Section: Contact v.2
<section class="mb-4">
    <form action='course_public/confirm_trainee_details' method='post'>
    Section heading
    <h2 class="h1-responsive font-weight-bold text-center my-4">Declaration Form</h2>
    Section description
    <p class="text-center alert-success msg" style='padding: 10px;'></p>
    <br>
    <h5 class="text-center suremsg">Are you Sure! You want to Continue?</h5>
    <br>
    
    <div class="form-group">        
        <div class="col-sm-12 text-center">
          <button type='button' class='btn btn-primary yescls' style='padding: 3px 17px !important;font-size: 18px !important;'>Yes</button>&nbsp;&nbsp;&nbsp;<a href='#'class='btn btn-primary nocls' style='padding: 3px 17px !important;font-size: 18px !important;'>No</a>
        </div>
    </div>
    <input type='hidden' id='user_id_popup' name='user_id_popup' value=''>
    <input type='hidden' id='class_id_popup' name='class_id_popup' value='<?php echo $class_id;?>'>
    <input type='hidden' id='course_id_popup' name='course_id_popup' value='<?php echo $course_id;?>'>
    </form>    
</section>
Section: Contact v.2


</div>-->



<!----------------modal by ssp end----------------------->

<script src="<?php echo base_url(); ?>assets/public_js/validation_register_enroll.js?v=3.2" type="text/javascript"></script>
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
             $('#exampleModalCenter').modal('hide');
             //document.trainee_form2.action = "<?php //echo base_url(); ?>"+"course_public/referral_credentials1";
             //window.location = "<?php //echo base_url(); ?>";
              //$('#trainee_form2').submit();
        });
        $('.yescls').click(function(){
             $('#exampleModalCenter').modal('hide');
             $('#trainee_form2').submit();
        });
       
//        $('#exampleModalCenter').modal({
//            backdrop: 'static',
//            keyboard: false
//        });
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
    
   <!-- Button trigger modal -->
<input type="hidden" id='modal_nric_found' class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">


<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <form action='course_public/confirm_trainee_detail' method='post'>
      <div class="modal-header">
        <h5 class="modal-title panel_heading_style" id="exampleModalCenterTitle">NRIC DETAILS</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
               
        <p class="text-center alert-success msg" style='padding: 10px;'></p>
        <br>
        <h5 class="text-center suremsg">Are you Sure! You want to Continue?</h5>
        <br>


        <input type='hidden' id='user_id_popup' name='user_id_popup' value=''>
        <input type='hidden' id='class_id_popup' name='class_id_popup' value='<?php echo $class_id;?>'>
        <input type='hidden' id='course_id_popup' name='course_id_popup' value='<?php echo $course_id;?>'>
                    
            
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-lg btn-primary yescls" data-dismiss="modal">Continue</button>
        <button type="button" class="btn btn-lg btn-danger nocls">Save changes</button>
      </div>
    </form>
    </div>
  </div>
</div>
<?php echo validation_errors('<div class="error1">', '</div>'); ?>
<?php
$atr = 'id="deactivateUserForm" name="deactivateUserForm" onsubmit="return validate_deactivate_user();"';
echo form_open('internal_user/deactivate_user/'.$user_id, $atr);
?>
<div class="col-md-10 col_10_height_other">

<h2 class="panel_heading_style">Deactivate Internal User</h2>
<div>
  <p>
      <strong> De-Activation Date:<span class="red">*</span> </strong>
      <input type="date" style="line-height:17px" placeholder="dd/mm/yyyy" name="deactivation_date" id="deactivation_date" readonly="readonly">&nbsp;<span id="deactivation_date_err"></span>&nbsp;
    <br><br>
 <strong>Reason for De-Activation:<span class="red">*</span></strong> 
            <?php
            $d_reasons = fetch_metavalues_by_category_id(Meta_Values::DEACTIVATE_REASONS);
            
            $reasons_options[''] = 'Select';
            foreach ($d_reasons as $item):
                $reasons_options[$item['parameter_id']] = $item['category_name'];
            endforeach;
            $reasons_options['OTHERS'] = 'Others';
            
            $attr = 'id="reason_for_deactivation"';
            echo form_dropdown('reason_for_deactivation', $reasons_options, $this->input->post('reason_for_deactivation'), $attr);
            ?> 
            <span id="reason_for_deactivation_err"></span>

 &nbsp; 
  <br><br>
  <span>&nbsp;</span><span id="other_reason" style="display:none;">
     <?php
     $attr = array(
         'name'=>'other_reason_for_deactivation', 
         'id'=>'other_reason_for_deactivation',
         'size'=>40
         );
     echo form_input($attr);
     ?>
     <span id="other_reason_for_deactivation_err"></span>
 </span>
    <br><br>
  Are you sure you want to deactivate this User?  
  
  <br>
  <span class="required_i red">*Required Field</span>
  <div class="pull-right">
      <button class="btn btn-primary" type="submit" name="button_yes">Yes</button>&nbsp;&nbsp;<a href="<?php echo site_url(); ?>internal_user"><button class="btn btn-primary" type="button" name="button_no">No</button></a>
  </div>
  </p>
</div>
</div>
<?php
echo form_close();
?>
<script type="text/javascript">
    var d = new Date();        
    var currentYear = d.getFullYear(); 
    var currentMonth=d.getMonth();
    var CurrentDate=d.getDay();
    var startYear = currentYear - 65;
    var endYear = currentYear - 21;
    $(function() {          
      $( "#deactivation_date" ).datepicker({ 
          dateFormat: 'dd/mm/yy',
          changeMonth: true,
          changeYear: true,
          yearRange: '-100:+0'              
      });
    });
    
    $("#reason_for_deactivation").change(function(){
        reason_for_deactivation = $("#reason_for_deactivation").val();
        if(reason_for_deactivation=='OTHERS'){
            $("#other_reason").show();
        }
        else
        {
            $("#other_reason").hide();
        }
    });
    
function valid_deactivate_reason(userName) {
    var pattern = new RegExp(/^[\sa-zA-Z0-9_,.-]+$/);
    return pattern.test(userName);
}

 function validate_deactivate_user() {   
        var retVal=true;

        deactivation_date = $("#deactivation_date").val();
        reason_for_deactivation = $("#reason_for_deactivation").val();
        other_reason_for_deactivation = $("#other_reason_for_deactivation").val();
        
        if(deactivation_date == "") {
            $("#deactivation_date_err").text("[required]").addClass('error');
            retVal = false;
        }else{
           $("#deactivation_date_err").text("").removeClass('error'); 
        }
        if(reason_for_deactivation == "") {
            $("#reason_for_deactivation_err").text("[required]").addClass('error');
            retVal = false;
        }else{
           $("#reason_for_deactivation_err").text("").removeClass('error'); 
        }
        
        if(reason_for_deactivation == "OTHERS") {
            if(other_reason_for_deactivation == "") {
                $("#other_reason_for_deactivation_err").text("[required]").addClass('error');
                retVal = false;
            }else{
                if(valid_deactivate_reason(other_reason_for_deactivation)==false)
                {
                    $("#other_reason_for_deactivation_err").text("[invalid]").addClass('error');
                    retVal = false;                    
                }else{
                    $("#other_reason_for_deactivation_err").text("").removeClass('error'); 
                }
            }
        }else{
            $("#other_reason_for_deactivation_err").text("").removeClass('error');
        }        

        
        return retVal;
        
    } 
</script>
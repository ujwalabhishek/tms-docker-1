<form action="<?php echo site_url(); ?>settings/update_gst_rate" method="POST" id="gst_form" name="gst_form" onsubmit="return(myform_submit());" >
<div class="col-md-10 col_10_height_other">
    <h2 class="panel_heading_style"><img src="<?php  echo base_url(); ?>/assets/images/edit.png"> Settings - Edit GST Rate</h2>
    <div>
        <p>
            <table class="table table-striped">      
              <tbody>      
                <tr>            
                  <td class="td_heading">New Rate:<span class="required">*</span></td>
                  <td>
                      <input class='float_number' type="text" name="gst_rate" id="gst_rate" value="<?php echo $gst_active_value->gst_rate; ?>"> &nbsp; %
                      <span id="gst_rate_err"></span>
                  </td>          
                </tr>        
              </tbody>
            </table>
            <br>
            <span class="required required_i">* Required Fields</span>
            <div class="button_class">                
                <button class="btn btn-primary" type="submit" ><span class="glyphicon glyphicon-saved"></span>&nbsp;Save</button>
                <a href="<?php echo base_url()?>settings/gst_rates"><button class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-saved"></span>&nbsp;Cancel</button></a>
            </div>
        </p>
    </div>        
</div>
</form>
<script>

function valid_gst_rate(new_rate){
    var pattern = new RegExp(/^\s*[-+]?(\d*\.?\d+|\d+\.)(e[-+]?[0-9]+)?\s*$/i);
    return pattern.test(new_rate);
}   

function myform_submit(){    
    var new_rate=$("#gst_rate").val();
    var old_rate="<?php echo $gst_active_value->gst_rate; ?>";    
    if(new_rate==""){
        $("#gst_rate_err").text("[required]").addClass('error');
        return false;
    }else if(valid_gst_rate(new_rate) == false){
        $("#gst_rate_err").text("[invalid]").addClass('error');
        return false;
    }else if(new_rate==old_rate){
        $("#gst_rate_err").text("[no change]").addClass('error');
        return false;
    }else{
         ///////added by shubhranshu to prevent multiple clicks////////////////
        $('.button_class').html("<button class='btn btn-primary' type='submit'>Please Wait..</button>");
        var self = $('.button_class'),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
         ///////added by shubhranshu to prevent multiple clicks////////////////
        return true;
    }
}
 
    $(".float_number").keydown(function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            (e.keyCode == 65 && e.ctrlKey === true) || 
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
</script>
/**
 * This js file includes in deactivate company page
 */
$(function() {
        
        $("#company_deactivation_date").datepicker({dateFormat: 'dd/mm/yy'});
     
    });
    
    $("#company_reason_for_deactivation").change(function(){
        var reason_for_deactivation = $("#company_reason_for_deactivation").val();        
        if(reason_for_deactivation=='OTHERS'){
            $("#company_other_reason_for_deactivation").show();
        }
        else
        {
            $("#company_other_reason_for_deactivation").hide();
        }
    });    
    
    $(document).ready(function() { 

    });
    
    
    function validate_deactivate_company_form() {
        var retVal=true;

        deactivation_date = $("#company_deactivation_date").val();
        reason_for_deactivation = $("#company_reason_for_deactivation").val();
        other_reason_for_deactivation = $("#company_other_reason_for_deactivation").val();
        
        if(deactivation_date == "") {
            $("#company_deactivation_date_err").text("[required]").addClass('error');
            $("#company_deactivation_date").addClass('error');
            retVal = false;
        }else{
           $("#company_deactivation_date_err").text("").removeClass('error'); 
           $("#company_deactivation_date").removeClass('error');
        }
        if(reason_for_deactivation == "") {
            $("#company_reason_for_deactivation_err").text("[required]").addClass('error');
            $("#company_reason_for_deactivation").addClass('error');
            retVal = false;
        }else{
           $("#company_reason_for_deactivation_err").text("").removeClass('error'); 
           $("#company_reason_for_deactivation").removeClass('error');
        }
        
        if(reason_for_deactivation == "OTHERS") {
            if(other_reason_for_deactivation == "") {
                $("#company_other_reason_for_deactivation_err").text("[required]").addClass('error');
                $("#company_other_reason_for_deactivation").addClass('error');
                retVal = false;
            }else{
                if(valid_deactivate_reason(other_reason_for_deactivation)==false)
                {
                    $("#company_other_reason_for_deactivation_err").text("[invalid]").addClass('error');
                    $("#company_other_reason_for_deactivation").addClass('error');
                    retVal = false;                    
                }else{
                    $("#company_other_reason_for_deactivation_err").text("").removeClass('error');
                    $("#company_other_reason_for_deactivation").removeClass('error');
                }
            }
        }else{
            $("#company_other_reason_for_deactivation_err").text("").removeClass('error');
        }        

        return retVal;        
    }
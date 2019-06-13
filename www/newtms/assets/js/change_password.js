/** 
 * This js file included in change password page
 */
function validate(retVal){              
        var old_pwd=$.trim($('#old_password').val());
        if(old_pwd==""){
            $("#old_pwd_err").text("[required]").addClass('error');
            $("#old_pwd").addClass('error');
            retVal = false;
        }else{
            $("#old_pwd_err").text("").removeClass('error');
            $("#old_pwd").removeClass('error');
        }
        
        var new_pwd=$.trim($('#new_password').val());
        if(new_pwd==""){
            $("#new_pwd_err").text("[required]").addClass('error');
            $("#new_pwd").addClass('error');
            retVal = false;
        }else if(old_pwd==new_pwd){
            $("#new_pwd_err").text("[no change]").addClass('error');
            $("#new_pwd").addClass('error');
            retVal = false;
        }else{
            $("#new_pwd_err").text("").removeClass('error');
            $("#new_pwd").removeClass('error');
        }
        
        var new_pwd_confirm=$.trim($('#new_password_confirm').val());
        if(new_pwd_confirm==""){
            $("#new_pwd_confirm_err").text("[required]").addClass('error');
            $("#new_pwd_confirm").addClass('error');
            retVal = false;
        }else if(new_pwd != new_pwd_confirm){
            $("#new_pwd_confirm_err").text("[not matching]").addClass('error');
            $("#new_pwd_confirm").addClass('error');
            retVal = false;
        }else{
            $("#new_pwd_confirm_err").text("").removeClass('error');
            $("#new_pwd_confirm").removeClass('error');
        }
        if(retVal == true) {
            $('.submit_btn').hide();            
        }
        return retVal;
    }
    
    $(document).ready(function() {
        var check = 0;
        $('#change_pwd_form').submit(function() {
             check = 1;
              return validate(true);
        });
        $('#change_pwd_form select,#change_pwd_form input').change(function() {
            if (check == 1) {
                return validate(false);
            }
        });
    });
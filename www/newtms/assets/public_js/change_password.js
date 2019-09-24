function ispassword_exist(e,id) {
    e=$.trim(e);
    if(e =='') {
        $("#"+id+"_err").text("[required]").addClass('error');
        $("#"+id).addClass('error');
        return false;
    }else {
        $.ajax({
            url: baseurl + "settings_public/password_exist",
            type: "post",
            data: 'oldpassword='+e,
            success: function(res) {
                  if(res == 0) {
                  //  window.username = 'exists';                        
                    $("#"+id+"_err").text("[password not matching!]").addClass('error');
                    $("#"+id).addClass('error');
                    return false;
                }else{
                 //   window.username = 'notexists';
                    $("#"+id+"_err").text("").removeClass('error');
                    $("#"+id).removeClass('error');
                    return true;
                }
            },
            error:function(){
                return false;
            }
        });
    }        
}
function  password_matches(confirmpassword) {
   var userpassword=$('#new_password').val();
    if(userpassword !== confirmpassword){
        $("#confirm_password_err").text("[password does not matches]").addClass('error');
        $("#confirm_password").addClass('error');   
    }
    else{
        $("#confirm_password_err").text("").removeClass('error');
        $("#confirm_password").removeClass('error');  
    }
}
/*
* This function for validating change password.
* Author: Bineesh.
* Date: 28 Oct 2014.
*/
function validate() {
    var retVal=true;        
    var old_pwd=$.trim($('#old_password').val());
    var old_password_err = $('#old_password_err').text();
    if(old_pwd==""){
        $("#old_password_err").text("[required]").addClass('error');
        $("#old_password").addClass('error');
        retVal = false;
    }else if(old_password_err != '[password not matching!]'){
        $("#old_password_err").text("").removeClass('error');
        $("#old_password").removeClass('error');
    }

    var new_pwd=$.trim($('#new_password').val());
    if(new_pwd==""){
        $("#new_password_err").text("[required]").addClass('error');
        $("#new_password").addClass('error');
        retVal = false;
    }else if(old_pwd==new_pwd){
        $("#new_password_err").text("[same  as prevoius password]").addClass('error');
        $("#new_password").addClass('error');
        retVal = false;
    }else{
        $("#new_password_err").text("").removeClass('error');
        $("#new_password").removeClass('error');
    }

    var new_pwd_confirm=$.trim($('#confirm_password').val());
    if(new_pwd_confirm==""){
        $("#confirm_password_err").text("[required]").addClass('error');
        $("#confirm_password").addClass('error');
        retVal = false;
    }else if(new_pwd != new_pwd_confirm){
        $("#confirm_password_err").text("[password doesnot matches]").addClass('error');
        $("#confirm_password").addClass('error');
        retVal = false;
    }else{
        $("#confirm_password_err").text("").removeClass('error');
        $("#confirm_password").removeClass('error');
    }
    //final checking if any error span in document                       
    if($('#validation_div span').hasClass('error')) {                
        retVal = false;        
    }
    return retVal;
} 
 /*
* This function for triggering the validate
* Author: Bineesh.
* Date: 13/08/2014.
*/
$(document).ready(function() {
    var check = 0;
    $('#change_pwd_form').submit(function() {
        check = 1;
        return validate();
    });
    $('#change_pwd_form select,#change_pwd_form input').change(function() {
        if (check == 1) {
            return validate();
        }
    });
}); 
//function check_taxcode_nric(e,id) {
//    
//    e=$.trim(e);   
//    $taxcode = e;
//
//    $x = $("#NRIC").val();
//
//    if($x == 'SNG_2' && $taxcode!=''  || $x == 'SNG_1' && $taxcode!='' )
//    {   
////        alert("hi");
//        //var e = $taxcode;
//        $country_of_residence = 'SGP';
//        $nric = $x;
//        var uid = 'REFER_TRAINEE';
//       
//        $.ajax({
//                url: baseurl + "user/check_taxcode",
//                type: "post",
//                async: false,
//                data: {tax_code: e, country_of_residence: $country_of_residence, nric: $nric, uid: uid},
//                success: function(res) {
//                      
//                    if (res == 1) {
//                        window.username = 'exists';
////                        $("#" + id + "_err").text("[code exists!]").addClass('error');
////                        $("#" + id).addClass('error');
//                        $("#NRIC_err").text("[code exists!]").addClass('error');
//                        $("#NRIC").addClass('error');
//                        $("#NRIC_msg").hide(); // hide valid msg
//                        return false;
//                    } else if (res == 2) {
//                        $("#NRIC_err").text("[Invalid!]").addClass('error');
//                        $("#NRIC").addClass('error');
//                        $("#NRIC_msg").hide(); // hide valid msg
//                        return false;
//                    } else {
//                        window.username = 'notexists';
//                        $("#NRIC_err").text("").removeClass('error');
//                        $("#NRIC").removeClass('error');
//                        $("#NRIC_msg").show();
//                        $("#NRIC_msg").html("Valid");
//                        $('#try').hide(); // it will hide NRIC NOT FOUND msg
//                        return true;
//                    }
//                },
//                error: function() {
//                    return false;
//                }
//            });
//    }else{
////        alert("hello");
//        if(e =='' && id == 'taxcode_nric'){
//        $("#"+id+"_err").text("[required]").addClass('error');
//        $("#"+id).addClass('error');
//        return false;
//    }  else {
//        $.ajax({
//          
//            url: baseurl + "course/check_nric_no",
//            type: "post",
//            data: "taxcode_nric="+e,
//            async: false,
//            success: function(res){
//             
//                if(res == 1) {
//                   
//                    $("#admin_msg_err").hide();
//                                        $("#pers_dob_err").text("").removeClass('error');
//                                        $("#pers_dob").removeClass('error');
//                                        $("#e_email_err").text("").removeClass('error');
//                                        $("#e_email").removeClass('error');
//                                        $("#e_contact_no_err").text("").removeClass('error');
//                                        $("#e_contact_no").removeClass('error');
//
//                   
////                     $('#trainee_form').trigger("reset");
//                    $("#" + id + "_err").text("").removeClass('error');
//                    $("#" + id).removeClass('error');
//                    $('#try').hide();
//                    $('#nric_not_found1').hide();
//                    $('#nric_not_found2').hide();
//                  
//                    $('#nric_found').html('<input type="hidden" id="taxcode_found" name="taxcode_found" value="' + $taxcode + '">');
//                    $('#nric_res').html('<input type="hidden" id="res_found1" name="res_found1" value="' + res + '">');
//                    $('#existing_user').show();
//                    $('#success').show();
//                    $('#success').html('NRIC FOUND');
//                    $("#admin_msg_err").hide();
//                   
//return true;
//                }else{ 
//
//                       $('#nric_res').html('<input type="hidden" id="res_found1" name="res_found1" value="' + res + '">');
//                      $("#admin_msg_err").hide();
//                                        $("#pers_dob_err").text("").removeClass('error');
//                                        $("#pers_dob").removeClass('error');
//                                        $("#e_email_err").text("").removeClass('error');
//                                        $("#e_email").removeClass('error');
//                                        $("#e_contact_no_err").text("").removeClass('error');
//                                        $("#e_contact_no").removeClass('error');
//
//                        
//                        $("#" + id + "_err").text("").removeClass('error');
//                        $("#" + id).removeClass('error');
//
//                    $('#success').hide();
//                   // $('#nric_found').html('<input type="hidden" id="taxcode_found" name="taxcode_found" value="">');
//                    $('#nric_not_found1').show();
//                    $('#nric_not_found2').show();
//                    $('#existing_user').hide();
//                    $('#try').show();
//                    $('#try').html('NRIC NOT FOUND');
//                       $("#admin_msg_err").hide();
////                  
//            $('#taxcode_found').removeAttr('value');   
//               return true; }
//            },
//            error:function(){
//                return false;
//            }
//        });
//            } 
//        }
//           
//}

function check_taxcode_nric(e,id) {
    
    e=$.trim(e);   
    $taxcode = e;
    $x = $("#NRIC").val();
    if($x == 'SNG_2' && $taxcode!=''  || $x == 'SNG_1' && $taxcode!='' )
    {   
        $country_of_residence = 'SGP';
        $nric = $x;
        var uid = 'REFER_TRAINEE';
       
        $.ajax({
                url: baseurl + "user/check_taxcode",
                type: "post",
                async: false,
                data: {tax_code: e, country_of_residence: $country_of_residence, nric: $nric, uid: uid},
                success: function(res) {
                      
                    if (res == 1) {
                        window.username = 'exists';
                        $("#NRIC_err").text("[code exists!]").addClass('error');
                        $("#NRIC").addClass('error');
                        $("#NRIC_msg").hide(); // hide valid msg
                        return false;
                    } else if (res == 2) {
                        $("#NRIC_err").text("[Invalid!]").addClass('error');
                        $("#NRIC").addClass('error');
                        $("#NRIC_msg").hide(); // hide valid msg
                        return false;
                    } else {
                        window.username = 'notexists';
                        $("#NRIC_err").text("").removeClass('error');
                        $("#NRIC").removeClass('error');
                        $("#NRIC_msg").show();
                        $("#NRIC_msg").html("Valid");
                        $('#try').hide(); // it will hide NRIC NOT FOUND msg
                        return true;
                    }
                },
                error: function() {
                    return false;
                }
            });
    }else{
        if(e =='' && id == 'taxcode_nric'){
        $("#"+id+"_err").text("[required]").addClass('error');
        $("#"+id).addClass('error');
        return false;
    }  else {
          $course_id = $("#course_id").val();
          $class_id =  $("#class_id").val();
          $.ajax({
                url: baseurl + "course_public/check_nric_no_cc",
                type: "post",
                data: {taxcode_nric: e, course_id: $course_id, class_id: $class_id},
                async: false,
                success: function(res)
                {
                    if(res == 3) // if trainee exists but his status is not active
                    {
                        $flag =1;
                        var res =3;
                    }
                    else if(res == 2) // if trainee exists but enrolled in class
                    {
                        $flag = 1;
                        var res = 1;
                    }else if(res == 1) // if trainee exists but not enrolled in class
                    {
                        $flag = 0;
                        var res = 1;
                    }else               // if trainee not exists in db
                    {
                        var res = 0;
                    }

                    if(res == 3)
                    {
                        $("#user_exists_class_msg").html('Looks like this Person is already registered in the system but not active, please contact admin to active his/her account.');
                        $("#user_class_msg").show();
                        $('#nric_found_msg').hide();
                        $("#btn_dis").hide(); // it hide form submit button
                        $('#flag_row_hide').hide(); // it hide relationship row
                        $('#nric_not_found1').hide(); // it hides nric  
                        $("#realtion").hide();//hide relationship
                    }
                    else if(res == 1)
                    {
                        if($flag == 1)
                        {   
                           $("#user_exists_class_msg").html('This Person is already enrolled in this class. Click <a href='+$baseurl+'course_public/course_class_schedule/'+$course_id+'>here</a> to go back to the Class list.');
                           $("#user_class_msg").show();
                           $('#nric_found_msg').hide();
                           $('#flag_row_hide').hide(); // it hide relationship row
                           $("#btn_dis").hide(); // it hide form submit button
                            $("#realtion").hide();//hide relationship
                        }else{
                            $("#user_class_msg").hide();
                            $("#nric_found_user_msg").html('Looks like this Person is already registered in the system, please proceed enrollment in this class.');
                            $('#nric_found_msg').show();// when nric found then show user msg
                            $('#flag_row_hide').show(); // it show relationship row     
                            $("#btn_dis").show(); // it hide form submit button
                             $("#realtion").show();// show relationship
                        }

                                       $("#flag").show();
                                       $("#admin_msg_err").hide();
                                        $("#" + id + "_err").text("").removeClass('error');
                                        $("#" + id).removeClass('error');
                                        $('#try').hide();
                                        $('#nric_not_found1').hide();
                                        $('#nric_not_found2').hide();
                                        //$('#nric_found_msg').show();// when nric found then show user msg
                                        $('#nric_found').html('<input type="hidden" id="taxcode_found" name="taxcode_found" value="' + $taxcode + '">');
                                        $('#nric_res').html('<input type="hidden" id="res_found1" name="res_found1" value="' + res + '">');
    //                                    $('#existing_user').show();
                                        $('#success').show();
                                        $('#success').html('NRIC FOUND');
                                        $("#admin_msg_err").hide();


                                    return true;
                    }else{
                                    $("#flag").show();
                                    $("#user_class_msg").hide();
                                    $('#flag_row_hide').show(); // it show relationship row
                                    $('#trainee_validation_div1').show(); // it show rerrer porttion.

                                    $('#nric_res').html('<input type="hidden" id="res_found1" name="res_found1" value="' + res + '">');
                                    $("#admin_msg_err").hide();

                                    $("#" + id + "_err").text("").removeClass('error');
                                    $("#" + id).removeClass('error');

                                    $('#success').hide();
                                    $('#nric_found_msg').hide();// when nric not found then hide user msg

                                    $('#nric_not_found1').show();
                                    $('#nric_not_found2').show();
                                    $('#try').show();
                                    $('#try').html('NRIC NOT FOUND');
                                    $("#admin_msg_err").hide();
    //                  
                                    $('#taxcode_found').removeAttr('value');   
                                return true; 
                    }


                },
                error:function(){
                    return false;
                }
            });
            } 
        }
           
}


$("#country_of_residence").change(function() {

    country_of_residence = $('#country_of_residence').val();
    if (country_of_residence == "") {
        $("#country_of_residence > option").each(function() {
            if (this.value != "")
                $("#" + this.value).hide();
        });
    }

    $("#country_of_residence > option").each(function() {
        if (this.value == country_of_residence) {
            $("#" + country_of_residence).show();
        }
        else {
            if (this.value != "") {
                $("#" + this.value).hide();
                $("#" + this.value + "_OTHERS").hide();
                $("#" + this.value + "_ID").hide();
                remove_all_errors();
            }
        }
    });

    if (country_of_residence == "IND") {
        $("#PAN").show();
        $("#NRIC").hide();
        $("#SSN").hide();
        $("#SGP_ID").hide();
        $("#SGP_OTHERS").hide();
        $('#SSN_err').text('').removeClass('error');
        $('#SSN').removeClass('error');
        remove_nric_errors();
    }
    if (country_of_residence == "SGP") {
        $("#NRIC").show();
        $('#NRIC option:first-child').attr("selected", "selected");
        $("#PAN").hide();
        $("#SSN").hide();
        remove_ind_usa_errors();
    }
    if (country_of_residence == "USA") {
        $("#SSN").show();
        $("#PAN").hide();
        $("#NRIC").hide();
        $("#SGP_ID").hide();
        $("#SGP_OTHERS").hide();
        $('#PAN_err').text('').removeClass('error');
        $('#PAN').removeClass('error');
        remove_nric_errors();
    }
});
function remove_nric_errors() {
    $('#NRIC_err').text('').removeClass('error');
    $('#NRIC').removeClass('error');
    $('#NRIC_OTHER_err').text('').removeClass('error');
    $('#NRIC_OTHER').removeClass('error');
    $('#NRIC_ID_err').text('').removeClass('error');
    $('#NRIC_ID').removeClass('error');
}
function remove_ind_usa_errors() {
    $('#PAN_err').text('').removeClass('error');
    $('#PAN').removeClass('error');
    $('#SSN_err').text('').removeClass('error');
    $('#SSN').removeClass('error');
}
function remove_all_errors() {
    $('#NRIC_err').text('').removeClass('error');
    $('#NRIC').removeClass('error');
    $('#NRIC_OTHER_err').text('').removeClass('error');
    $('#NRIC_OTHER').removeClass('error');
    $('#NRIC_ID_err').text('').removeClass('error');
    $('#NRIC_ID').removeClass('error');
    $('#PAN_err').text('').removeClass('error');
    $('#PAN').removeClass('error');
    $('#SSN_err').text('').removeClass('error');
    $('#SSN').removeClass('error');
}


/*
 * This method for singapore contry of residence.
 */
$("#NRIC").change(function() {
    if (this.value == "") {
        
        $("#SGP_ID").hide();
        $("#SGP_OTHERS").hide();
    } else if (this.value == "SNG_3") {
        
        $("#SGP_OTHERS").show();
        $('#SGP_OTHERS option:first-child').attr("selected", "selected");
        $('#SGP_OTHERS_label').text('');
        $('#SGP_OTHERS_label').text('');
        $('#SGP_ID_label').text('');
        $('#SGP_ID_label').text('');
        
        $("#NRIC_err").text("").removeClass('error');
        $("#NRIC").removeClass('error');
        $("#NRIC_msg").hide();// it hides valid msg when nric or fin valid
   
    } else {
        
        $('#SGP_OTHERS_label').text('');
        $('#SGP_OTHERS_label').text('NRIC :');
        $('#SGP_ID_label').text('');
        $('#SGP_ID_label').text('NRIC Code :');
        $("#SGP_OTHERS").hide();
        $("#SGP_ID").show();
        
        $taxcode_nric = $("#taxcode_nric").val();
        //alert($taxcode_nric);
        $country_of_residence = 'SGP';
        $nric = $("#NRIC").val();
        var uid = 'REFER_TRAINEE';
       if($taxcode_nric!='')
       {
            $.ajax({
                url: baseurl + "user/check_taxcode",
                type: "post",
                async: false,
                data: {tax_code: $taxcode_nric, country_of_residence: $country_of_residence, nric: $nric, uid: uid},
                success: function(res) {
                      
                    if (res == 1) {
                        window.username = 'exists';
//                        $("#" + id + "_err").text("[code exists!]").addClass('error');
//                        $("#" + id).addClass('error');
                        $("#NRIC_err").text("[Already exists!]").addClass('error');
                        $("#NRIC").addClass('error');
                        $("#NRIC_msg").hide();
                        return false;
                    } else if (res == 2) {
                        $("#NRIC_err").text("[Invalid!]").addClass('error');
                        $("#NRIC").addClass('error');
                         $("#NRIC_msg").hide();
                        return false;
                    } else {
                        window.username = 'notexists';
                        $("#NRIC_err").text("").removeClass('error');
                        $("#NRIC").removeClass('error');
                        $("#NRIC_msg").show();
                        $("#NRIC_msg").html("Valid");
                        return true;
                    }
                },
                error: function() {
                    return false;
                }
            });
        }
       
    }
});
$("#NRIC_OTHER").change(function() {
    if (this.value == "") {
//        alert("1");
        $("#SGP_ID").hide();
    } else {
        $("#SGP_ID").show();
        $('#try').hide();
    }
});

$(function() {
    var d = new Date();
    var currentYear = d.getFullYear();
    var currenyMonth = d.getMonth();
    var CurrentDate = d.getDay();
    var startYear = currentYear - 90;
    var endYear = currentYear - 10;
    $(function() {
        $("#pers_dob").datepicker({
            dateFormat: 'dd-mm-yy',
            minDate: new Date(startYear, currenyMonth, CurrentDate),
            maxDate: new Date(endYear, currenyMonth, CurrentDate),
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });
        
        $("#dob").datepicker({
            dateFormat: 'dd-mm-yy',
            minDate: new Date(startYear, currenyMonth, CurrentDate),
            maxDate: new Date(endYear, currenyMonth, CurrentDate),
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });
    });
});
$(document).ready(function(){
     $("#admin_msg_err").hide();
     $('#country_of_residence option:nth(0)').attr("selected", "selected"); 
     
    $("#check").hide(); // it hide reeral nric
    $("#nnf").hide() // hide country code for referal
    $('#captcha').hide(); //  hide captcha msg 
    $(".exists").hide();
    $(".rnnf").hide(); // hide name,email,contact no when referal nric found
   
});
$('#pers_country').change(function() {
    var country_param = $(this).val();
    if (country_param) {
        $.post(baseurl + 'user/get_states_json', {country_param: country_param}, function(data) {
            json_data = $.parseJSON(data);
            $pers_states_select = $("#pers_states");
            $pers_states_select.html('<option value="">Select</option>');
            $.each(json_data, function(i, item) {
                $pers_states_select.append('<option value="' + item.parameter_id + '">' + item.category_name + '</option>');
            });
        });
    } else {
        $('#pers_states').find('option').remove().end().append('<option value="">Select</option>');
    }
});

function isunique_username(e, id) {
    e = $.trim(e);
    if (e == '') {
        $("#" + id + "_err").text("[required]").addClass('error');
        $("#" + id).addClass('error');
         $("#try").hide();
        return false;
    } else if (valid_user_name(e) == false) {
        $("#" + id + "_err").text("[invalid]").addClass('error');
        $("#" + id).addClass('error');
         $("#try").hide();
    } else {
        $.ajax({
            url: baseurl + "user/check_username",
            type: "post",
            data: 'username=' + e,
            async: false,
            success: function(res) {
                if (res == 1) {
                    $("#try").hide();
                    window.username = 'exists';
                    $("#" + id + "_err").text("[Username exists!]").addClass('error');
                    $("#" + id).addClass('error');
                    return false;
                } else {
                    $("#try").hide();
                    window.username = 'notexists';
                    $("#" + id + "_err").text("").removeClass('error');
                    $("#" + id).removeClass('error');
                    return true;
                }
            },
            error: function() {
                return false;
            }
        });
    }
}

//function check_referal_nric(e,id) {
// e=$.trim(e);   
// $taxcode = e;
//    if(e =='' && id == 'r_nric'){
//        $("#"+id+"_err").text("[required]").addClass('error');
//        $("#"+id).addClass('error');
//    }else{
//         $.ajax({
//          
//            url: baseurl + "course/check_referal_nric",
//            type: "post",
//            dataType: 'json',
//            async: false,
//            data: "taxcode_nric="+e,
//            success: function(res){
//                if(res !=0) {
//                  
//                    $('#referal_nric_not_found').hide();
//                    $('#referal_nric_found').show();
//                    $('#referal_nric_found').html('NRIC FOUND');
//                    
//                    $("#nnf").hide();
//                    $registered_email_id = res.registered_email_id;
//                    $contact_number = res.contact_number;
//                    $first_name = res.first_name;
//                    $('.exists').show();
//                    $('.rnnf').hide();
//                    $('.exists #r_ex1').html('<input type="text" id="r_name" name="r_name2" value="' + $first_name + '">');
//                    $('.exists #r_ex2').html('<input type="text" id="r_email" name="r_email2" value="' + $registered_email_id + '">');
//                    $('.exists #r_ex3').html('<input type="text"  id="r_contact" name="r_contact2" value="' + $contact_number + '">');
//                    $('.exists #r_ex4').html('<input type="text" id="r_res" name="r_res" value="1">');
//                    return true; 
//                }else{  
//                  
//                $('#referal_nric_found').hide();
//                $('#referal_nric_not_found').show();
//                $('#referal_nric_not_found').html('NRIC NOT FOUND');
//                $("#nnf").show() // show country code for referal when nric not found
//                $(".rnnf").show() // show name, eamil, contact number of referal when nric not found
//                $('.exists').hide();
////                $('.exists #r_ex1').hide();// hide name when nric not found.
////                $('.exists #r_ex2').hide();// hide email when nric not found.
////                $('.exists #r_ex3').hide();// hide contact when nric not found.
//                $('.rnnf #r_nw1').html('<input type="text" class="r_name" id="r_name" name="r_name" value="">');
//                $('.rnnf #r_nw2').html('<input type="text" id="r_email" name="r_email" value="">');
//                $('.rnnf #r_nw3').html('<input type="text" id="r_email" name="r_contact" value="">');  
//                $('.rnnf #r_nw4').html('<input type="text" id="r_res" name="r_res" value="0">');   
//                $("#" + id + "_err").text("").removeClass('error');
//                $("#" + id).removeClass('error');
//    
//               return true; }
//           
//            },
//            error:function(){
//                return false;
//            }
//        });
//    }
//           
//}
//}


if ($.trim($('#user_registered_email').val() == '')) {
    primary_email = $('#user_registered_email').val();
}
else {
    primary_email = $('#frnd_registered_email').val();
}

function isunique_email(e, id) {
    e = $.trim(e);

    if (e == '' && id == 'user_registered_email') {
        $("#" + id + "_err").text("[required]").addClass('error');
        $("#" + id).addClass('error');
    }
    else if (e == '' && id == 'frnd_registered_email') {
        $("#" + id + "_err").text("").removeClass('error');
        $("#" + id).removeClass('error');
    }
    else if (valid_email_address(e) == false) {
        $("#" + id + "_err").text("[Invalid]").addClass('error');
        $("#" + id).addClass('error');
    } else if (e != primary_email) {
        $.ajax({
            url: baseurl + "user/check_email",
            type: "post",
            data: 'email=' + e,
            async: false,
            success: function(res) {
                if (res == 1) {
                    window.email_id = 'exists';
                    $("#" + id + "_err").text("[Email Id exists!]").addClass('error');
                    $("#" + id).addClass('error');
                    return false;
                } else {
                    window.email_id = 'notexists';
                    $("#" + id + "_err").text("").removeClass('error');
                    $("#" + id).removeClass('error');
                }
            },
            error: function() {
                return false;
            }
        });
    } else {
        $("#" + id + "_err").text("").removeClass('error');
        $("#" + id).removeClass('error');
    }
    return false;
}

function check_captcha(e,id) {
    
    e=$.trim(e);   

    if(e =='' && id == 'captcha2'){
        $("#"+id+"_err").text("[required]").addClass('error');
        $("#"+id).addClass('error');
        return false;
    }  else {
        $.ajax({
          
            url: baseurl + "course_public/captcha_match1",
            type: "post",
            data: "captcha="+e,
            
            success: function(res){
               
                if(res == 1) {
                        $("#captcha2").removeClass('error');
                        $("#captcha2_err").text("").removeClass('error');
                        $('#captcha').show();
                        $('#captcha_found').html('<input type="hidden" id="captcha_found_data" name="captcha_found_data" value="1">');
                           
   
                }else{ 
                        $('#captcha').hide();
                        $("#captcha2_err").text("[Captcha Not Matched]").addClass('error');
                        $("#captcha2").addClass('error'); 
                        $('#captcha_found').html('<input type="hidden" id="captcha_found_data" name="captcha_found_data" value="0">');
                            //check_captcha1();
                            retVal = false;

                    }
            },
            error:function(){
                return false;
            }
        });
    }        
}
/*
 * This function for triggering the validate
 */
var val_check = 0; 
$(document).ready(function() {
    $("#admin_msg_err").hide();
    $("#sub2").hide();
    $('#existing_user').hide(); // when nric match then only it shows
    $('#nric_found_msg').hide();// when nric not found then hide user msg
    
  // STEP2 ST  for existing member
// $('.search_select[value="1"]').attr('checked', 'checked');
// $('.search_select').trigger('change');
// alert("trigger");
 //STEP2 ED   
    $('#trainee_form select,#trainee_form input').change(function() {
        if (val_check == 1) {
            return validate();
        }
    });
});

//STEP3 ST for existing member disabled input fields
//    $('#pers_dob').attr('disabled', 'disabled');
//    $('#e_email').attr('disabled', 'disabled');
//    $('#e_contact_no').attr('disabled', 'disabled');
//    
//    $('.search_select').change(function() {
//       
//        $val = $('.search_select:checked').val();
//        if ($val == 1) {
//            $div = $('#pers_dob');
//            $('#e_email').attr('disabled', 'disabled');
//            $('#e_email').attr('value', '');
//            $('#e_contact_no').attr('disabled', 'disabled');
//            $('#e_contact_no').attr('value', '');
//        } else if ($val == 2) {
//            $div = $('#e_email');  
//            $('#pers_dob').attr('disabled', 'disabled');
//            $('#pers_dob').attr('value', '');
//            $('#e_contact_no').attr('disabled', 'disabled');
//            $('#e_contact_no').attr('value', '');
//        } else if ($val == 3) {
//            $div = $('#e_contact_no');
//            $('#pers_dob').attr('disabled', 'disabled');
//            $('#pers_dob').attr('value', '');
//            $('#e_email').attr('disabled', 'disabled');
//            $('#e_email').attr('value', '');
//        }
//        $div.removeAttr('disabled');
//    });
//STEP3 ED

function validate($form_name) {
    val_check = 1;
    var retVal = true;
 
    //STEP5 ST check nirc have value or not
    var taxcode_found = null;
    var taxcode_found = $("#taxcode_found").val();
    var res_found1 = $("#res_found1").val();
//    alert(taxcode_found.length);
    //STEP5 ED
//   alert(res_found1);
    /* validatin for nirc check taxcode */
    var taxcode_nric = $("#taxcode_nric").val();
    if(taxcode_nric == "" )
    {
        $("#taxcode_nric_err").text("[required]").addClass('error');
        $("#taxcode_nric").addClass('error');
        retVal = false;
    }else{
        $("#taxcode_nric_err").text("").removeClass('error');
        $("#taxcode_nric").removeClass('error');
    }//end
    
    
   
    
    //STEP6 ST
    if(res_found1 == 1){
//alert("1");
       // validation for radio buttion when nric exists start
//        $val = $('.search_select:checked').val();
//        if($val == "1")
//        {    
//            var pers_dob = $.trim($("#pers_dob").val());
//            var taxcode_found = $("#taxcode_found").val();
//            if (valid_date_field(pers_dob) == false && pers_dob != "") {
//                $("#pers_dob_err").text("[dd-mm-yy format]").addClass('error');
//                $("#pers_dob").removeClass('error');
//                retVal = false;
//            }
//            else if(pers_dob == "")
//            {
//                $("#pers_dob_err").text("[required]").addClass('error');
//                $("#pers_dob").removeClass('error');
//                retVal = false;
//            }
//            else {
//                    $.ajax({
//                                url: baseurl + "user/check_existing_details",
//                                type: "post",
//                                data: {"nric":taxcode_found,"argument":pers_dob,"radio":$val},
//                                async:false,
//                                success: function(res){
//                                    if(res == 1)
//                                    {   
//                                        $("#admin_msg_err").hide();
//                                        $("#pers_dob_err").text("").removeClass('error');
//                                        $("#pers_dob").removeClass('error');
//
//                                    }else
//                                    {    
//                                       $("#pers_dob_err").text("[credatial not Found]").addClass('error');
//                                       $("#pers_dob").addClass('error'); 
//                                       $("#admin_msg_err").show();
//                                       retVal = false;
//                                     }
//                                },
//                                    error:function(){
//
//                                    }
//                            });
//                
//                    
//            }
//        }
//        else
//        {
//                 $("#pers_dob_err").text("").removeClass('error');
//                 $("#pers_dob").removeClass('error');
//        }
//
//        if($val == "2" && taxcode_found != null)
//        {   
////            
//            var e_email = $.trim($("#e_email").val());
//           
//            var taxcode_found = $("#taxcode_found").val();
//            if (e_email == "") {
//                $("#e_email_err").text("[required]").addClass('error');
//                $("#e_email").addClass('error');
//                retVal = false;
//            } else if (valid_email_address(e_email) == false && e_email!="") {
//                    $("#e_email_err").text("[invalid]").addClass('error');
//                    $("#e_email").addClass('error');
//                    retVal = false;
//                } else {
//                        $.ajax({
//                                url: baseurl + "user/check_existing_details",
//                                type: "post",
//                                data: {"nric":taxcode_found,"argument":e_email,"radio":$val},
//                                async:false,
//                                success: function(res){
//                                    if(res == 1)
//                                    {   
//                                        $("#admin_msg_err").hide();
//                                        $("#e_email_err").text("").removeClass('error');
//                                        $("#e_email").removeClass('error');
//
//                                    }else
//                                    {     
//                                       $("#e_email_err").text("[credatial not Found]").addClass('error');
//                                       $("#e_email").addClass('error'); 
//                                       $("#admin_msg_err").show();
//                                       retVal = false;
//                                     }
//                                },
//                                    error:function(){
//
//                                    }
//                            });
//                    
////                    $("#e_email").removeClass('error');
////                    $("#e_email_err").text("").removeClass('error');
//                        }
//            } 
//            else
//            {
//                $("#e_email").removeClass('error');
//                $("#e_email_err").text("").removeClass('error');
//            }
//
//        if($val == "3" && taxcode_found != null)
//        {    
//            var e_contact_no = $.trim($("#e_contact_no").val());
//            var taxcode_found = $("#taxcode_found").val();
//            if (e_contact_no == "") {
//                $("#e_contact_no_err").text("[required]").addClass('error');
//                $("#e_contact_no").addClass('error');
//                retVal = false;
//            } else {
//                if (valid_contact_number(e_contact_no) == false) {
//                    $("#e_contact_no_err").text("[invalid]").addClass('error');
//                    $("#e_contact_no").addClass('error');
//                    retVal = false;
//                } else {
//                        $.ajax({
//                                url: baseurl + "user/check_existing_details",
//                                type: "post",
//                                data: {"nric":taxcode_found,"argument":e_contact_no,"radio":$val},
//                                async:false,
//                                success: function(res){
//                                    if(res == 1)
//                                    {   
//                                        $("#admin_msg_err").hide();
//                                        $("#e_contact_no_err").text("").removeClass('error');
//                                        $("#e_contact_no").removeClass('error');
//
//                                    }else
//                                    {    
//                                       $("#e_contact_no_err").text("[credatial not Found]").addClass('error');
//                                       $("#e_contact_no").addClass('error'); 
//                                       $("#admin_msg_err").show();
//                                       retVal = false;
//                                     }
//                                },
//                                    error:function(){
//
//                                    }
//                            });
//                }
//            }
//        }else
//        {
//            $("#e_contact_no_err").text("").removeClass('error');
//            $("#e_contact_no").removeClass('error');
//        }   
        // end
       
    // validation for radio buttion when nric exists end
    
    
    }
    //STEP6 ED
    else {
        
            $("#admin_msg_err").hide();
//            {
            
            /* when NRIC TYPE blank in REFERANCE FORM start*/    
        var NRIC = $("#NRIC").val();
        if(NRIC == "")
        {
           $("#NRIC_err").text("[required]").addClass('error');
           $("#NRIC").addClass('error');
           retVal = false;
        }
        else
        {       
            if(NRIC == "SNG_3")
            {
                 var NRIC_OTHER = $("#NRIC_OTHER").val();
                 if(NRIC_OTHER == "")
                 {
                   $("#NRIC_OTHER_err").text("[required]").addClass('error');
                   $("#NRIC_OTHER").addClass('error'); 
                    retVal = false;
                 }else{
                       $("#NRIC_OTHER_err").text("").removeClass('error');
                       $("#NRIC_OTHER").removeClass('error');
                 }
            }         
            $("#NRIC_err").text("").removeClass('error');
            $("#NRIC").removeClass('error');
            $taxcode_nric = $("#taxcode_nric").val();
            check_taxcode_nric($taxcode_nric,'taxcode_nric');
        }
/* END */

    
    /* NRIC TAXCODE NOT FOUND then it vaidatie the which type of id USER provide start*/
    $r_res = $("#r_res").val();
    if($r_res == 0)
    {
        var NRIC = $("#NRIC").val();
        if(NRIC == "")
        {
           $("#NRIC_err").text("[required]").addClass('error');
           $("#NRIC").addClass('error');
           retVal = false;
        }else{       
               if(NRIC == "SNG_3")
               {
                    var NRIC_OTHER = $("#NRIC_OTHER").val();
                    if(NRIC_OTHER == "")
                    {
                      $("#NRIC_OTHER_err").text("[required]").addClass('error');
                      $("#NRIC_OTHER").addClass('error'); 
                       retVal = false;
                    }else{
                          $("#NRIC_OTHER_err").text("").removeClass('error');
                          $("#NRIC_OTHER").removeClass('error');
                    }
               }         
           $("#NRIC_err").text("").removeClass('error');
           $("#NRIC").removeClass('error');
           $taxcode_nric = $("#taxcode_nric").val();
           check_taxcode_nric($taxcode_nric,'taxcode_nric');

        }
    }
    /* END */

   
    
//    if (country_of_residence == "SGP") {
//
//        var NRIC = $.trim($("#NRIC").val());
//
////        if(NRIC.length == 0  || refer_friend !=1){
//              
//            var nric_error_text = $("#NRIC_ID_err").text();
//            var NRIC_ID = $.trim($("#NRIC_ID").val());
//
//            var NRIC_OTHER = $.trim($("#NRIC_OTHER").val());
//            if (NRIC == "") {
//
//                $("#NRIC_err").text("[required]").addClass('error');
//                $("#NRIC").addClass('error');
//                retVal = false;
//            }
//            else if (NRIC == "SNG_3") {
//                    if (NRIC_OTHER == "") {
//                        $("#NRIC_OTHER_err").text("[required]").addClass('error');
//                        $("#NRIC_OTHER").addClass('error');
//                        retVal = false;
//                    } else {
//                        $("#try").hide();
//                        $("#NRIC_OTHER_err").text("").removeClass('error');
//                        $("#NRIC_OTHER").removeClass('error');
//                    }
//                    if (NRIC_ID == "" && NRIC_OTHER != "NOTAXCODE") {
//                        $("#NRIC_err").text("").removeClass('error');
//                        $("#NRIC").removeClass('error');
//                        $("#NRIC_ID_err").text("[required]").addClass('error');
//                        $("#NRIC_ID").addClass('error');
//                        retVal = false;
//                    } else if (nric_error_text != '[code exists!]') {
//                        $("#NRIC_ID_err").text("").removeClass('error');
//                        $("#NRIC_ID").removeClass('error');
//                    }
//                } else if (NRIC_ID == "") {
//                    $("#NRIC_err").text("").removeClass('error');
//                    
//                    $("#NRIC").removeClass('error');
//                    $("#NRIC_ID_err").text("[required]").addClass('error');
//                    $("#NRIC_ID").addClass('error');
//                    retVal = false;
//                } else if (nric_error_text != '[code exists!]' && nric_error_text != '[Invalid!]') {
//                    $("#NRIC_err").text("").removeClass('error');
//                    $("#NRIC").removeClass('error');
//                    $("#NRIC_ID_err").text("").removeClass('error');
//                    $("#NRIC_ID").removeClass('error');
//                      $("#try").hide();
//                }
//                 else if (NRIC_ID == "" && refer_friend !=1) {
//                $("#NRIC_err").text("").removeClass('error');
//                $("#NRIC").removeClass('error');
//                $("#NRIC_ID_err").text("[required]").addClass('error');
//                $("#NRIC_ID").addClass('error');
//                retVal = false;
//            } else {
//                $("#try").hide();
//                $("#NRIC_err").text("").removeClass('error');
//                $("#NRIC").removeClass('error');
//                isunique_taxcode(NRIC_ID, 'NRIC_ID', uid = 'REFER_TRAINEE');
//            }
////        }
//
//    }
    
    
    
   

    var user_name = $.trim($("#user_name").val());
    if (user_name == "") {
        $("#user_name_err").text("[required]").addClass('error');
        $("#user_name").addClass('error');
        retVal = false;
    } else {
        if (valid_user_name(user_name) == false) {
            retVal = false;
            $("#user_name_err").text("[invalid]").addClass('error');
            $("#user_name").addClass('error');
        } else {
            javascript:isunique_username(user_name, 'user_name');
        }
    }

    var pers_first_name = $.trim($("#pers_first_name").val());
    if (pers_first_name == "") {
        $("#pers_first_name_err").text("[required]").addClass('error');
        $("#pers_first_name").addClass('error');
        retVal = false;
    } else {                                   
        $("#pers_first_name_err").text("").removeClass('error');
        $("#pers_first_name").removeClass('error');
    }
    var nationality = $.trim($("#nationality").val());
    if (nationality == "") {
        $("#nationality_err").text("[required]").addClass('error');
        $("#nationality").addClass('error');
        retVal = false;
    } else {
        $("#nationality_err").text("").removeClass('error');
        $("#nationality").removeClass('error');
    }

    var pers_gender = $.trim($("#pers_gender option:selected").val());
    if (pers_gender == "") {
        $("#pers_gender_err").text("[required]").addClass('error');
        $("#pers_gender").addClass('error');
        retVal = false;
    } else {
        $("#pers_gender_err").text("").removeClass('error');
        $("#pers_gender").removeClass('error');
    }
     
    var pers_contact_number = $.trim($("#pers_contact_number").val());
    if (pers_contact_number == "") {
        $("#pers_contact_number_err").text("[required]").addClass('error');
        $("#pers_contact_number").addClass('error');
        retVal = false;
    } else {
        if (valid_contact_number(pers_contact_number) == false) {
            $("#pers_contact_number_err").text("[invalid]").addClass('error');
            $("#pers_contact_number").addClass('error');
            retVal = false;
        } else {
            $("#pers_contact_number_err").text("").removeClass('error');
            $("#pers_contact_number").removeClass('error');
        }
    }

    var pers_alternate_contact_number = $.trim($("#pers_alternate_contact_number").val());
    if (pers_alternate_contact_number != "") {
        if (valid_contact_number(pers_alternate_contact_number) == false) {
            $("#pers_alternate_contact_number_err").text("[invalid]").addClass('error');
            $("#pers_alternate_contact_number").addClass('error');
            retVal = false;
        } else {
            $("#pers_alternate_contact_number_err").text("").removeClass('error');
            $("#pers_alternate_contact_number").removeClass('error');
        }
    } else {
        $("#pers_alternate_contact_number_err").text("").removeClass('error');
        $("#pers_alternate_contact_number").removeClass('error');
    }
    if ($("#user_registered_email").val() !== undefined) {
        var user_registered_email = $.trim($("#user_registered_email").val());

        if (user_registered_email == "") {
            $("#user_registered_email_err").text("[required]").addClass('error');
            $("#user_registered_email").addClass('error');
            $("#pers_conf_email_err").text("[required]").addClass('error');
            $("#pers_conf_email").addClass('error');
            retVal = false;
        } else if (valid_email_address(user_registered_email) == false) {
            $("#user_registered_email_err").text("[invalid]").addClass('error');
            $("#user_registered_email").addClass('error');
            retVal = false;
        } else {
            $("#user_registered_email_err").text("").removeClass('error');
            $("#user_registered_email").removeClass('error');
            $("#pers_conf_email_err").text("").removeClass('error');
            $("#pers_conf_email").removeClass('error');
        }
    }
    else {

        var user_registered_email = $.trim($("#frnd_registered_email").val());

        if (valid_email_address(user_registered_email) == false && user_registered_email != '') {
            $("#frnd_registered_email_err").text("[invalid]").addClass('error');
            $("#frnd_registered_email").addClass('error');
            retVal = false;
        } else {
            //isunique_email(user_registered_email, 'frnd_registered_email')
             $.ajax({
                                url: baseurl + "user/check_email",
                                type: "post",
                                data: 'email=' + user_registered_email,
                                async: false,
                                success: function(res) {
                                    if (res == 1) {
                  
                                        window.email_id = 'exists';
                                        $("frnd_registered_email_err").text("[Email Id exists!]").addClass('error');
                                        $("#frnd_registered_email").addClass('error');
                                        retVal = false;
                                    } else {
                    
                                        window.email_id = 'notexists';
                                        $("#frnd_registered_email_err").text("").removeClass('error');
                                        $("#frnd_registered_email").removeClass('error');
                                    }
                                },
                                error: function() {
                                    return false;
                                }
                            });

        }
    }
    if ($("#pers_conf_email").val() !== undefined) {
        var pers_conf_email = $.trim($("#pers_conf_email").val());
        var user_registered_email = $.trim($("#user_registered_email").val());
        if (pers_conf_email != user_registered_email) {
            if (valid_email_address(user_registered_email) == true) {
                if (pers_conf_email == '') {
                    $("#pers_conf_email_err").text("[required]").addClass('error');
                    $("#pers_conf_email").addClass('error');
                } else if (valid_email_address(pers_conf_email) == false) {
                    $("#pers_conf_email_err").text("[invalid]").addClass('error');
                    $("#pers_conf_email").addClass('error');
                } else {
                    $("#pers_conf_email_err").text("[Email does not match]").addClass('error');
                    $("#pers_conf_email").addClass('error');
                }
                retVal = false;
            }
        } else {
            $("#pers_conf_email").removeClass('error');
            $("#pers_conf_email_err").text("").removeClass('error');
        }
    } else {

        var frnd_conf_email = $.trim($("#frnd_conf_email").val());
        var registered_email = $.trim($("#frnd_registered_email").val());
        if (frnd_conf_email != registered_email) {
            if (valid_email_address(registered_email) == true) {
                if (frnd_conf_email == '') {
                    $("#frnd_conf_email_err").text("[required]").addClass('error');
                    $("#frnd_conf_email").addClass('error');
                } else if (valid_email_address(frnd_conf_email) == false) {
                    $("#frnd_conf_email_err").text("[invalid]").addClass('error');
                    $("#frnd_conf_email").addClass('error');
                } else {
                    $("#frnd_conf_email_err").text("[Email does not match]").addClass('error');
                    $("#frnd_conf_email").addClass('error');
                }
                retVal = false;
            }
        } else {
            $("#frnd_conf_email").removeClass('error');
            $("#frnd_conf_email_err").text("").removeClass('error');
        }
    }
    
    /* validation for email id when nric not found skm start */
       //end 
        
    
    var pers_alt_email = $.trim($("#pers_alt_email").val());
    if (pers_alt_email != "") {
        if (valid_email_address(pers_alt_email) == false) {
            $("#pers_alt_email_err").text("[invalid]").addClass('error');
            $("#pers_alt_email").addClass('error');
            retVal = false;
        } else {
            $("#pers_alt_email").removeClass('error');
            $("#pers_alt_email_err").text("").removeClass('error');
        }
    } else {
        $("#pers_alt_email_err").text("").removeClass('error');
    }
    var highest_educ_level = $.trim($("#highest_educ_level").val());
    if (highest_educ_level == "") {
        $("#highest_educ_level_err").text("[required]").addClass('error');
        $("#highest_educ_level").addClass('error');
        retVal = false;
    } else {
        $("#highest_educ_level_err").text("").removeClass('error');
        $("#highest_educ_level").removeClass('error');
    }

    var dob = $.trim($("#dob").val());
    if(dob == "")
    {   
        $("#dob_err").text("[dd-mm-yy format]").addClass('error');
        $("#dob").addClass('error');
        retVal = false;
        if(valid_date_field(dob) == false)
        {
            $("#dob_err").text("[dd-mm-yy format]").addClass('error');
            $("#dob").addClass('error');
            retVal = false;
        }else{
            $("#dob_err").text("").removeClass('error');
            $("#dob").removeClass('error');
        }
        
    }else
    {
        $("#dob_err").text("").removeClass('error');
        $("#dob").removeClass('error');
    }
   
    var occupation = $.trim($("#occupation").val());
    if (occupation == "") {
        $("#occupation_err").text("[required]").addClass('error');
        $("#occupation").addClass('error');
        retVal = false;
    } else {
        $("#occupation_err").text("").removeClass('error');
        $("#occupation").removeClass('error');
    }
    

//      /* NRIC TAXCODE NOT FOUND then it vaidatie the which type of id USER provide start*/
//    var r_NRIC = $("#r_NRIC").val();
//    if(r_NRIC == "")
//    {
//       $("#r_NRIC_err").text("[required]").addClass('error');
//       $("#r_NRIC").addClass('error');
//       retVal = false;
//    }else{       
//           if(r_NRIC == "SNG_3")
//           {
//                var r_NRIC_OTHER = $("#r_NRIC_OTHER").val();
//                if(r_NRIC_OTHER == "")
//                {
//                  $("#r_NRIC_OTHER_err").text("[required]").addClass('error');
//                  $("#r_NRIC_OTHER").addClass('error'); 
//                   retVal = false;
//                }else{
//                      $("#r_NRIC_OTHER_err").text("").removeClass('error');
//                      $("#r_NRIC_OTHER").removeClass('error');
//                      
//                }
//           }         
//       $("#r_NRIC_err").text("").removeClass('error');
//       $("#r_NRIC").removeClass('error');
//       $taxcode_nric = $("#r_nric").val();
//       check_referal_nric($taxcode_nric,'r_nric');
//       
//    }//end

   
   
    if ($('#captcha2').length > 0) {
            $captcha = $('#captcha2').val().trim();
            
            if ($captcha.length == 0) {
                $("#captcha2_err").text("[required]").addClass('error');
                $("#captcha2").addClass('error');
                retVal = false;
            } else {
            
                    var captcha_found_data = $("#captcha_found_data").val();
                    if(captcha_found_data !=1 || captcha_found_data == null)
                    {
                        $('#captcha').hide();
                        $("#captcha2_err").text("[Captcha Not Matched]").addClass('error');
                        $("#captcha2").addClass('error'); 
                    }else
                     {       $('#captcha').show()
                            $("#captcha2").removeClass('error');
                            $("#captcha2_err").text("").removeClass('error');
                    }
            }
        }
        
    if ($('#trainee_form span').hasClass('error')) {
        retVal = false;
    }
    if (retVal == true && $form_name == 'add_form') {
        $taxcode = $('#taxcode').val();
        $('#trainee_form').append('<input type="hidden" name="taxcode" value="' + $taxcode + '">');
        $yes_no = $('input[name="yes_no"]').val();
        $('#trainee_form').append('<input type="hidden" name="yes_no" value="' + $yes_no + '">');
    }    
    }
    
    // referal validatin starts
//    $r_res = $("#r_res").val();
//   
//    $checkbox_value =   $("#checkbox").val();
//  
//    if($r_res == "" && $checkbox_value == 1 )
//    {
//        var r_name = $.trim($("#r_name").val());
//        if (r_name == "") {
//            $("#r_name_err").text("[required]").addClass('error');
//            $("#r_name").addClass('error');
//            retVal = false;
//        } else {
//            $("#r_name_err").text("").removeClass('error');
//            $("#r_name").removeClass('error');
//        }
//    
//        var r_email = $.trim($("#r_email").val());
//    //    alert(r_email);
//        if (r_email == "") {
//            $("#r_email_err").text("[required]").addClass('error');
//            $("#r_email").addClass('error');
//            retVal = false;
//        }
//        else
//        {   
//            if(valid_email_address(r_email) == false){
//                $("#r_email_err").text("[invalid]").addClass('error');
//                $("#r_email").addClass('error');
//                retVal = false;
//            }else{
//                $("#r_email").removeClass('error');
//                $("#r_email_err").text("").removeClass('error');   
//            }
//
//        }
//    
//        var r_contact  = $.trim($("#r_contact").val());
//        if (r_contact == "") {
//           $("#r_contact_err").text("[required]").addClass('error');
//           $("#r_contact").addClass('error');
//           retVal = false;
//        } else {
//           if (valid_contact_number(r_contact) == false) {
//               $("#r_contact_err").text("[invalid]").addClass('error');
//               $("#r_contact").addClass('error');
//               retVal = false;
//           } else {
//               $("#r_contact_err").text("").removeClass('error');
//               $("#r_contact").removeClass('error');
//           }
//        }
//       
//    }
//    
//    
//    if($checkbox_value == 1 && $r_res == 0)
//    {   //alert("hi");
//        var r_name = $.trim($("#r_name").val());
//        alert("name = "+r_name);
//        if (r_name == "") 
//        {
//           // alert("1..");
//            $(".r_name_err").text("[required]").addClass('error');
////            $("#r_name1_err").html("error");
//            $("#r_name1").addClass('error');
//            retVal = false;
//        } 
//        else 
//        {
//            //alert("2");
//            $(".rnnf #r_nw1 #r_name_err").text("").removeClass('error');
//            $(".rnnf #r_nw1 #r_name").removeClass('error');
//        }
//    
//        var r_email = $.trim($("#r_email").val());
//    //    alert(r_email);
//        if (r_email == "") {
//            $("#r_email_err").text("[required]").addClass('error');
//            $("#r_email").addClass('error');
//            retVal = false;
//        }
//        else
//        {   
//            if(valid_email_address(r_email) == false){
//                $("#r_email_err").text("[invalid]").addClass('error');
//                $("#r_email").addClass('error');
//                retVal = false;
//            }else{
//                $("#r_email").removeClass('error');
//                $("#r_email_err").text("").removeClass('error');   
//            }
//
//        }
//    
//        var r_contact = $.trim($("#r_contact").val());
//        if (r_contact == "") {
//           $("#r_contact_err").text("[required]").addClass('error');
//           $("#r_contact").addClass('error');
//           retVal = false;
//        } else {
//           if (valid_contact_number(r_contact) == false) {
//               $("#r_contact_err").text("[invalid]").addClass('error');
//               $("#r_contact").addClass('error');
//               retVal = false;
//           } else {
//               $("#r_contact_err").text("").removeClass('error');
//               $("#r_contact").removeClass('error');
//           }
//        }
//    }
    //end
    
    

    return retVal;
}

function confirm_email(e, id) {

    if (id == 'frnd_conf_email') {
        c_email = $.trim($('#frnd_registered_email').val());
    }
    else {
        c_email = $.trim($('#user_registered_email').val());
    }


    e = $.trim(e);
    if (e == '' && c_email != '') {
        $("#" + id + "_err").text("[required]").addClass('error');
        $("#" + id + "_err").addClass('error');
    } else if (valid_email_address(e) == false && e != '') {
        $("#" + id + "_err").text("[invalid]").addClass('error');
        $("#" + id + "_err").addClass('error');
    } else if (c_email != e) {

        $("#" + id + "_err").text('[Email does not match]').addClass('error');
        $("#" + id + "_err").addClass('error');
    } else {
        $("#" + id + "_err").text('').removeClass('error');
        $("#" + id + "_err").removeClass('error');
    }
    return false;
}
function validate_alternate_email(e, id) {
    e = $.trim(e);
    if (e == '') {
        $("#" + id + "_err").text("").removeClass('error');
        $("#" + id).removeClass('error');
        return false;
    } else if (valid_email_address(e) == false) {
        $("#" + id + "_err").text("[invalid]").addClass('error');
        $("#" + id).addClass('error');
        return false;
    } else {
        $("#" + id + "_err").text("").removeClass('error');
        $("#" + id).removeClass('error');
        return true;
    }
}
function  validate_pers_contact_number(e, id) {
    e = $.trim(e);
    if (e == '') {
        $("#" + id + "_err").text("[required]").addClass('error');
        $("#" + id).addClass('error');
        return false;
    } else if (valid_contact_number(e) == false) {
        $("#" + id + "_err").text("[invalid]").addClass('error');
        $("#" + id).addClass('error');
        return false;
    } else {
        $("#" + id + "_err").text("").removeClass('error');
        $("#" + id).removeClass('error');
        return true;
    }
}
function validate_alternate_pers_contact_number(e, id) {
    e = $.trim(e);
    if (e == '') {
        $("#" + id + "_err").text("").removeClass('error');
        $("#" + id).removeClass('error');
        return false;
    } else if (valid_contact_number(e) == false) {
        $("#" + id + "_err").text("[invalid]").addClass('error');
        $("#" + id).addClass('error');
        return false;
    } else {
        $("#" + id + "_err").text("").removeClass('error');
        $("#" + id).removeClass('error');
        return true;
    }
}
function valid_contact_number(contactNum) {
    return /^\d+$/.test(contactNum.replace(/[,+\s]/g, '')); 
}
function valid_email_address(emailAddress) {
    var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
    return pattern.test(emailAddress);
}
function valid_user_name(userName) {
    var pattern = new RegExp(/^[a-zA-Z0-9_]+$/);
    return pattern.test(userName);
}
function valid_discount(discountValue) {
    var pattern = new RegExp(/^(([1-8][0-9]?|9[0-8]?)\.\d+|[1-9][0-9]?)$/);
    return pattern.test(discountValue);
}
function  valid_date_field(dateofjoin) {
    var pattern = /^\d{1,2}-\d{1,2}-\d{4}$/;
    return pattern.test(dateofjoin);
}
function valid_name($name) {
    var ck_name = /^[A-Za-z ]+$/;
    return ck_name.test($name);
}
function valid_zip($zip) {
    var ck_name = /^[A-Za-z0-9 ]+$/;
    return ck_name.test($zip);
}
/*
 * This method for allowing number value only
 */
$('.number').keydown(function(event) {
    if ($.inArray(event.keyCode, [46, 8, 9, 27, 13, 32, 188]) !== -1
            || ($.inArray(event.keyCode, [65, 67, 86]) !== -1 && event.ctrlKey === true)
            || (event.keyCode >= 35 && event.keyCode <= 39)) {
        return;
    } else {
        if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {
            event.preventDefault();
        }
    }
});
$(".float_number").keydown(function(e) {
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 173]) !== -1 ||
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                            (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
$(".alphabets").keydown(function(e) {
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 32]) !== -1 ||
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                            (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            if (((e.keyCode < 65 || e.keyCode > 90))) {
                e.preventDefault();
            }
        });

$(".alphanumeric").keydown(function(e) {
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 32]) !== -1 ||
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                            (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 90) && (e.keyCode < 96 || e.keyCode > 105))) {
                e.preventDefault();
            }
        });
function showimagepreview(input) {
    var ext = $('#userfile').val().split('.').pop().toLowerCase();
    if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
        $('#image_err').text('Invalid file!').addClass('error');
        $('#userfile').val('');
        $('#imgprvw').removeAttr('src');
        $('#removeimagebutton').hide();
        return false;
    }

    if (input.files && input.files[0]) {

        var size = input.files[0].size;
        if (size > 0) {
            var sizekb = size / 1024;
        } else {
            return false;
        }
        if (sizekb > 3075) {
            $('#userfile').val('');
            $('#imgprvw').removeAttr('src');
            $('#user_image_preview').hide();
            $('#image_err').text('Image size is too big. Please upload image which is not more than 3 MB in size.').addClass('error');
            $('#removeimagebutton').hide();
            return false;
        }

        var filerdr = new FileReader();
        filerdr.onload = function(e) {
            $('#image_err').text('').removeClass('error');
            $('#imgprvw').attr('src', e.target.result);
            $('#user_image_preview').show();
            $('#deleteimageyes').removeAttr('checked');
            $('#deleteimageno').attr('checked', 'checked');
            $('#imgprvw').attr('width', '120px');
            $('#imgprvw').attr('height', '100px');
        }
        filerdr.readAsDataURL(input.files[0]);
    }
    $('#removeimagebutton').show();
}
function remove_image() {
    $('#userfile').val('');
    $('#imgprvw').removeAttr('src');
    $('#user_image_preview').hide();
    $('#deleteimageyes').removeAttr('checked');
    $('#deleteimageno').attr('checked', 'checked');

}

$('.nric_submit').click(function(){
    e = $('#taxcode_nric').val();
    if(e == ''){
        $('#taxcode_nric_err').text('[Required!]').addClass('error');
    }else{
        $('#taxcode_nric_err').text('').removeClass('error');
       
    
        $course_id = $("#course_id").val();
        $class_id =  $("#class_id").val();
        e=$.trim(e);   
        $taxcode = e;
        $.ajax({
                    url: baseurl + "course_public/check_nric_no_public",
                    type: "post",
                    data: {taxcode_nric: e, course_id: $course_id, class_id: $class_id},
                    async: false,
                    success: function(res)
                    {
                         $('.yescls').show();
                         $('.nocls').html('Cancel');
                        if(res == 0) // if trainee exists but his status is not active
                        {
                           document.trainee_form2.action = baseurl+"course_public/register_trainee/";
                           $('#trainee_form2').submit();
                        }else if(res == 1){
                            var res = JSON.parse(res);
                            $('#modal_nric_found').click();
                            $('.msg').html("Oops! You are Already Enrolled for this class!");
                            $('.suremsg').html("Kindly Choose Another Class To Enrol!");
                            $('.yescls').hide();
                            $('.nocls').html('Proceed!');
                            $('#user_id_popup').val(res.user_id);
                            $(".nocls").attr("href", baseurl)

                        }else{
                            var res = JSON.parse(res);
                            $('#modal_nric_found').click();
                            $('.msg').html("Please Confirm The "+res.first_name+" & ("+res.tax_code+") Are Accurate & Belongs to you.");
                            $('.suremsg').html("Are you Sure! You want to Continue?");
                            $('#user_id_popup').val(res.user_id);
                        }

                    },
                    error:function(){
                        return false;
                    }
                });
                
    }
});
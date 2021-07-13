function check_taxcode_nric(e,id) {
    
    e=$.trim(e);   
    $taxcode = e;
//    alert($taxcode);
    $x = $("#NRIC").val();
    //alert($x);
    if($x == 'SNG_2' && $taxcode!=''  || $x == 'SNG_1' && $taxcode!='' )
    {   
//        alert("hi");
        //var e = $taxcode;
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
//                        $("#" + id + "_err").text("[code exists!]").addClass('error');
//                        $("#" + id).addClass('error');
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
//        alert("hello");
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
            success: function(res){
                
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
                    $('#trainee_validation_div1').hide(); // it hides rerrer porttion.
                    $('#nric_not_found1').hide(); // it hides nric
                    $('#nric_not_found2').hide(); // it hides FORM FOR REGISTRATION
                    
                }             
                else if(res == 1){
                                if($flag == 1)
                                {   
                                   $("#user_exists_class_msg").html('This Person is already enrolled in this class.Click <a href='+$baseurl+'course_public/course_class_schedule/'+$course_id+'>here</a> to go back to the Class list.');
                                   $("#user_class_msg").show();
                                   $('#nric_found_msg').hide();
                                   $('#flag_row_hide').hide(); // it hide relationship row
                                   $('#trainee_validation_div1').hide(); // it hides rerrer porttion.
                                   //$('#existing_user').hide(); // it hide existing user optional details.
                                  
                                   
                                }else{
                                   // $('#existing_user').show(); // it show existing user optional details.
                                    $("#user_class_msg").hide();
                                    $("#nric_found_user_msg").html('Looks like this Person  is already registered in the system, please proceed enrollment in this class.');
                                    $('#nric_found_msg').show();// when nric found then show user msg
                                    $('#flag_row_hide').show(); // it show relationship row
                                    $('#trainee_validation_div1').show(); // it show rerrer porttion.                                   
                                  
                                }
                                   
                                   $("#flag").show();
                                   $("#admin_msg_err").hide();
//                                   $("#pers_dob_err").text("").removeClass('error');
//                                   $("#pers_dob").removeClass('error');
//                                   $("#e_email_err").text("").removeClass('error');
//                                   $("#e_email").removeClass('error');
//                                   $("#e_contact_no_err").text("").removeClass('error');
//                                   $("#e_contact_no").removeClass('error');
                   
//                                  $('#trainee_form').trigger("reset");
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
//                                $("#pers_dob_err").text("").removeClass('error');
//                                $("#pers_dob").removeClass('error');
//                                $("#e_email_err").text("").removeClass('error');
//                                $("#e_email").removeClass('error');
//                                $("#e_contact_no_err").text("").removeClass('error');
//                                $("#e_contact_no").removeClass('error');
                        
                                $("#" + id + "_err").text("").removeClass('error');
                                $("#" + id).removeClass('error');

                                $('#success').hide();
                                $('#nric_found_msg').hide();// when nric not found then hide user msg
                               // $('#nric_found').html('<input type="hidden" id="taxcode_found" name="taxcode_found" value="">');
                                $('#nric_not_found1').show();
                                $('#nric_not_found2').show();
//                                $('#existing_user').hide();
                                $('#try').show();
                                $('#try').html('NRIC NOT FOUND');
                                $("#admin_msg_err").hide();
//                  
                                $('#taxcode_found').removeAttr('value');   
                            return true; }
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
                        $("#NRIC_err").text("[code exists!]").addClass('error');
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


$("#country_of_residence1").change(function() {

                country_of_residence1 = $('#country_of_residence1').val();
           
                if (country_of_residence1 == "") {
                    $("#country_of_residence1 > option").each(function() {
                        if (this.value != "")
                            $("#" + this.value).hide();
                    });
                }

                $("#country_of_residence1 > option").each(function() {
                    if (this.value == country_of_residence1) {

                        $("." + country_of_residence1).show();
                    }
                    else {
// 
                        if (this.value != "") {
                            $("#" + this.value).hide();
                            $("#" + this.value + "_OTHERS").hide();
                            $("#" + this.value + "_ID").hide();
                            r_remove_all_errors();
                        }
                    }
                });

            if (country_of_residence1 == "IND") {
        //      
                $("#r_PAN").show();
                $("#r_NRIC").hide();
                $("#r_SSN").hide();
                $("#r_SGP_ID").hide();
                $("#r_SGP_OTHERS").hide();
                $('#r_SSN_err').text('').removeClass('error');
                $('#r_SSN').removeClass('error');
                r_remove_nric_errors();
            }
            if (country_of_residence1 == "SGP") { 
//           
                $("#r_NRIC").show();
                $('#r_NRIC option:first-child').attr("selected", "selected");
                $("#r_PAN").hide();
                $("#r_SSN").hide();
                r_remove_ind_usa_errors();
            }
            if (country_of_residence1 == "USA") { 
        //     
                $("#r_SSN").show();
                $("#r_PAN").hide();
                $("#r_NRIC").hide();
                $("#r_SGP_ID").hide();
                $("#r_SGP_OTHERS").hide();
                $('#r_PAN_err').text('').removeClass('error');
                $('#r_PAN').removeClass('error');
                r_remove_nric_errors();
            }
});
function r_remove_nric_errors() {
//     
    $('#r_NRIC_err').text('').removeClass('error');
    $('#r_NRIC').removeClass('error');
    $('#r_NRIC_OTHER_err').text('').removeClass('error');
    $('#r_NRIC_OTHER').removeClass('error');
    $('#r_NRIC_ID_err').text('').removeClass('error');
    $('#r_NRIC_ID').removeClass('error');
}
function r_remove_ind_usa_errors() { 
//   
    $('#r_PAN_err').text('').removeClass('error');
    $('#r_PAN').removeClass('error');
    $('#r_SSN_err').text('').removeClass('error');
    $('#r_SSN').removeClass('error');
}
function r_remove_all_errors() {  
//   
    $('#r_NRIC_err').text('').removeClass('error');
    $('#r_NRIC').removeClass('error');
    $('#r_NRIC_OTHER_err').text('').removeClass('error');
    $('#r_NRIC_OTHER').removeClass('error');
    $('#r_NRIC_ID_err').text('').removeClass('error');
    $('#r_NRIC_ID').removeClass('error');
    $('#r_PAN_err').text('').removeClass('error');
    $('#r_PAN').removeClass('error');
    $('#r_SSN_err').text('').removeClass('error');
    $('#r_SSN').removeClass('error');
}
$("#r_NRIC").change(function() {
//  alert("change="+this.value);
    if (this.value == "") {
        
        $("#r_SGP_ID").hide();
        $("#r_SGP_OTHERS").hide();
    } else if (this.value == "SNG_3") {  
//      alert("SNG_3"+"1");
        $("#r_SGP_OTHERS").show();
        $('#r_SGP_OTHERS option:first-child').attr("selected", "selected");
        $('#r_SGP_OTHERS_label').text('');
        $('#r_SGP_OTHERS_label').text('');
        $('#r_SGP_ID_label').text('');
        $('#r_SGP_ID_label').text('');
        $('#referal_nric_not_found').hide();
        $("#r_NRIC_err").text("").removeClass('error');
        $("#r_NRIC").removeClass('error');
     
    } else { 
//        alert("2");
        $('#r_SGP_OTHERS_label').text('');
        $('#r_SGP_OTHERS_label').text('NRIC :');
        $('#r_SGP_ID_label').text('');
        $('#r_SGP_ID_label').text('NRIC Code :');
        $("#r_SGP_OTHERS").hide();
        $("#r_SGP_ID").show();
        $('#referal_nric_not_found').hide();
        $r_taxcode_nric = $("#r_nric").val();
//        alert("Referal = "+$r_taxcode_nric);
        $country_of_residence1 = 'SGP';
        $r_nric = $("#r_NRIC").val();
        var r_uid = 'REFER_TRAINEE';
        if($r_taxcode_nric!='')
       {
            $.ajax({
                url: baseurl + "user/check_taxcode",
                type: "post",
                async: false,
                data: {tax_code: $r_taxcode_nric, country_of_residence: $country_of_residence1, nric: $r_nric, uid: r_uid},
                success: function(res) {
                      
                    if (res == 1) {
                        window.username = 'exists';
                        $("#r_NRIC_err").text("[code exists!]").addClass('error');
                        $("#r_NRIC").addClass('error');
                        $("#r_NRIC_msg").hide();
                        return false;
                    } else if (res == 2) {
                        $("#r_NRIC_err").text("[Invalid!]").addClass('error');
                        $("#r_NRIC").addClass('error');
               
                        return false;
                    } else {
                        window.username = 'notexists';
                        $("#r_NRIC_err").text("").removeClass('error');
                        $("#r_NRIC").removeClass('error');
                       
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
$("#r_NRIC_OTHER").change(function() {  
    if (this.value == "") {
        $("#r_SGP_ID").hide();
        $('#referal_nric_not_found').hide();
    } else {
        $("#r_SGP_ID").show();
        $('#referal_nric_not_found').hide();
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
    $("#flag").hide();
    $("#user_class_msg").hide();

    $("#admin_msg_err").hide();
    $('#country_of_residence option:nth(0)').attr("selected", "selected"); 
    $('#nric_found_msg').hide();// when nric not found then hide user msg 
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

/* this is use for check box that decide that user want to provide his NRIC details start*/
$("#checkbox").change(function(){
  $checkbox_value =   $("#checkbox").val();
//  alert($checkbox_value);
  if(this.checked)
  { 
    
    $(".rnnf #r_nw4 #r_res").val('');
      
    $("#check").show(); // it show reeral nric
    $("#def1").hide(); // hide default name
    $("#def2").hide(); // hide default email
    $("#def3").hide(); // hide default contact
    $("#nnf").hide() // hide country code for referal
   // $(".rnnf").hide() // hide name,email,contact number code for referal when nric not found
    $(".exists").hide() // hide name,email,contact number code for referal when nric  found
    
    $('#r_name').val('');
    $('#r_email').val('');
    $('#r_contact').val('');
    $('#r_nric').val('');
    $('#r_NRIC').val('');
   // $('#r_NRIC_OTHER').hide();
    $('#referal_nric_found').hide();
       $('.exists #r_name').hide();
       $('.exists #r_email').hide();
       $('.exists #r_contact').hide();
    

    /* End */
  }
  else{
      
      $(".rnnf #r_nw4 #r_res").val('');
       $("#r_nric").val('');
      // alert("2");
       $("#check").hide();// it hide referal nric
       $("#def1").show(); // show default name
       $("#def2").show(); // show default email
       $("#def3").show(); // show default contact
       $("#nnf").hide() // hide country code for referal
       $(".rnnf").hide() // hide name,email,contact number code for referal when nric not found
//       $(".exists").hide() // hide name,email,contact number code for referal when nric found
       
       $('#r_name').val('');
       $('#r_email').val("");
       $('#r_contact').val('');
       $('#r_nric').val('');
       
        $('#r_NRIC').val('');
       // $('#r_NRIC_OTHER').hide();
       $('.exists').hide();
       $('.rnnf').hide();            
       $('.exists #r_ex1').hide();
       $('.exists #r_ex2').hide();
       $('.exists #r_ex3').hide();
       
 
  }
   
   
});//end


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

function check_referal_nric_code(e,id) {
 e=$.trim(e);   
 $taxcode = e;
 
 $y = $("#r_NRIC").val();
 if($y== 'SNG_2' && $taxcode!=''  || $y == 'SNG_1' && $taxcode!='' )
 {   
    $country_of_residence = 'SGP';
    $nric = $y;
    var uid = 'REFER_TRAINEE';
        $.ajax({
                url: baseurl + "user/check_taxcode",
                type: "post",
                async: false,
                data: {tax_code: e, country_of_residence: $country_of_residence, nric: $nric, uid: uid},
                success: function(res) {
                      
                    if (res == 1) {
                        window.username = 'exists';
//                        $("#" + id + "_err").text("[code exists!]").addClass('error');
//                        $("#" + id).addClass('error');
                        $("#r_NRIC_err").text("[code exists!]").addClass('error');
                        $("#r_NRIC").addClass('error');
                        $("#r_NRIC_msg").hide(); // hide valid msg
                        return false;
                    } else if (res == 2) {
                        $("#r_NRIC_err").text("[Invalid!]").addClass('error');
                        $("#r_NRIC").addClass('error');
                        $("#r_NRIC_msg").hide(); // hide valid msg
                        return false;
                    } else {
                        window.username = 'notexists';
                        $("#r_NRIC_err").text("").removeClass('error');
                        $("#r_NRIC").removeClass('error');
                        $("#r_NRIC_msg").show();
                        $("#r_NRIC_msg").html("Valid");
                        $('#try').hide(); // it will hide NRIC NOT FOUND msg
                        return true;
                    }
                },
                error: function() {
                    return false;
                }
            });
        
 }
 else
 {
    if(e =='' && id == 'r_nric')
    {
        $("#"+id+"_err").text("[required]").addClass('error');
        $("#"+id).addClass('error');
    }
    else
    {
        $.ajax({
                url: baseurl + "course_public/check_referal_nric",
                type: "post",
                dataType: 'json',
                async: false,
                data: "taxcode_nric="+e,
                success: function(res)
                {
                    if(res !=0) 
                    {
                        //alert("nric found");
                        //alert('ajax_result_ture='+res);
                         $("#r_NRIC_OTHER").val('');
                        $("#r_NRIC_ID").val('');

                        $('#referal_nric_not_found').hide();
                        $('#referal_nric_found').show();
                        $('#referal_nric_found').html('NRIC FOUND');
                        $("#nnf").hide();
                        $registered_email_id = res.registered_email_id;
                        $contact_number = res.contact_number;
                        $first_name = res.first_name;
                        $('.exists').show();
                        $('.exists #r_ex1').show();
                        $('.exists #r_ex2').show();
                        $('.exists #r_ex3').show();

                        $('#r_res_zero #r_res_zero').val('');
                        //$('#r_res_zero').hide();
                        $('.rnnf').hide();

                        $('.rnnf #r_nw1 #r_name').val('');
                        $('.rnnf #r_nw2 #r_email').val('');
                        $('.rnnf #r_nw3 #r_contact').val(''); 


//                        $('.exists #r_ex1').html('<input type="text" id="r_name" name="r_name2" value="' + $first_name + '" disabled>');
//                        $('.exists #r_ex2').html('<input type="text" id="r_email" name="r_email2" value="' + $registered_email_id + '" disabled>');
//                        $('.exists #r_ex3').html('<input type="text"  id="r_contact" name="r_contact2" value="' + $contact_number + '" disabled>');
//                        

                         $('.exists #r_ex1').html($first_name);
                        $('.exists #r_ex2').html($registered_email_id);
                        $('.exists #r_ex3').html($contact_number);                        
                        $('#r_res_one').html('<input type="hidden" id="r_res_one" name="r_res_one" value="1" >');
                       
                        return true; 
                    }
                    else
                    {  
    //                    alert('ajax_result_false='+res);
                        $('#r_res_one #r_res_one').val('');

                        $('.exists #r_ex1 #r_name').val();
                        $('.exists #r_ex2 #r_email').val();
                        $('.exists #r_ex3 #r_contact').val();

                        $('#referal_nric_found').hide();
                        $('#referal_nric_not_found').show();
                        $('#referal_nric_not_found').html('NRIC NOT FOUND');
                        $("#nnf").show() // show country code for referal when nric not found
                        $(".rnnf").show() // show name, eamil, contact number of referal when nric not found
                        $('.exists').hide();
                        $('#r_res_one #r_res').val('');
                        $('.rnnf #r_nw1').show;
                        $('.rnnf #r_nw2').show
                        $('.rnnf #r_nw3').show
                        $('#r_res_zero').html('<input type="hidden" id="r_res_zero" name="r_res_zero" value="'+ res +'">');   
                        $("#" + id + "_err").text("").removeClass('error');
                        $("#" + id).removeClass('error');
                        return true; 
                    }
            },
            error:function()
            {
                return false;
            }
        });
    }
        
 }
//    
// 
// 
//    

}

function check_referal_nric45(e,id) {
 e=$.trim(e);   
 $taxcode = e;
    if(e =='' && id == 'r_nric')
    {
        $("#"+id+"_err").text("[required]").addClass('error');
        $("#"+id).addClass('error');
    }
    else
    {
         $.ajax({
          
            url: baseurl + "course_public/check_referal_nric",
            type: "post",
            dataType: 'json',
            async: false,
            data: "taxcode_nric="+e,
            success: function(res)
            {
                if(res !=0) 
                {
                    //alert("nric found");
                    //alert('ajax_result_ture='+res);
                     $("#r_NRIC_OTHER").val('');
                    $("#r_NRIC_ID").val('');
                   
                    $('#referal_nric_not_found').hide();
                    $('#referal_nric_found').show();
                    $('#referal_nric_found').html('NRIC FOUND');
                    $("#nnf").hide();
                    $registered_email_id = res.registered_email_id;
                    $contact_number = res.contact_number;
                    $first_name = res.first_name;
                    $('.exists').show();
                    $('.exists #r_ex1').show();
                    $('.exists #r_ex2').show();
                    $('.exists #r_ex3').show();
                    
                    $('#r_res_zero #r_res_zero').val('');
                    //$('#r_res_zero').hide();
                    $('.rnnf').hide();
                    
                    $('.rnnf #r_nw1 #r_name').val('');
                    $('.rnnf #r_nw2 #r_email').val('');
                    $('.rnnf #r_nw3 #r_contact').val(''); 
                    
                    
//                    $('.exists #r_ex1').html('<input type="text" id="r_name" name="r_name2" value="' + $first_name + '">');
//                    $('.exists #r_ex2').html('<input type="text" id="r_email" name="r_email2" value="' + $registered_email_id + '">');
//                    $('.exists #r_ex3').html('<input type="text"  id="r_contact" name="r_contact2" value="' + $contact_number + '">');
//                    
                    $('.exists #r_ex1').html($first_name);
                    $('.exists #r_ex2').html($registered_email_id);
                    $('.exists #r_ex3').html($contact_number);
                    $('#r_res_one').html('<input type="hidden" id="r_res_one" name="r_res_one" value="1">');
                   
                    return true; 
                }
                else
                {  
//                    alert('ajax_result_false='+res);
                    $('#r_res_one #r_res_one').val('');
                    
                    $('.exists #r_ex1 #r_name').val();
                    $('.exists #r_ex2 #r_email').val();
                    $('.exists #r_ex3 #r_contact').val();
                    
                    $('#referal_nric_found').hide();
                    $('#referal_nric_not_found').show();
                    $('#referal_nric_not_found').html('NRIC NOT FOUND');
                    $("#nnf").show() // show country code for referal when nric not found
                    $(".rnnf").show() // show name, eamil, contact number of referal when nric not found
                    $('.exists').hide();
                    $('#r_res_one #r_res').val('');
                    $('.rnnf #r_nw1').show;
                    $('.rnnf #r_nw2').show
                    $('.rnnf #r_nw3').show
                    $('#r_res_zero').html('<input type="hidden" id="r_res_zero" name="r_res_zero" value="'+ res +'">');   
                    $("#" + id + "_err").text("").removeClass('error');
                    $("#" + id).removeClass('error');
                    return true; 
                }
            },
            error:function()
            {
                return false;
            }
        });
    }
           
}

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

function isunique_ref_email(e, id) {
    e = $.trim(e);

    if (e == '' && id == 'r_email') {
        $("#r_nw2 #r_email_err").text("[required]").addClass('error');
        $("#r_nw2 #r_email_err").addClass('error');
    }
    else if (e == '' && id == 'r_email') {
        $("#r_nw2 #r_email_err").text("").removeClass('error');
        $("#r_nw2 #r_email_err").removeClass('error');
    }
    else if (valid_email_address(e) == false) {
        $("#r_nw2 #r_email_err").text("[Invalid]").addClass('error');
        $("#r_nw2 #r_email_err").addClass('error');
    } else if (e != primary_email) {
        $.ajax({
            url: baseurl + "user/check_email",
            type: "post",
            data: 'email=' + e,
            async: false,
            success: function(res) {
              
                if (res == 1) {
                    window.email_id = 'exists';
                    $("#r_nw2 #r_email_err").text("[Email Id exists!]").addClass('error');
                    $("#r_nw2 #r_email_err").addClass('error');
                    return false;
                } else {
                    window.email_id = 'notexists';
                    $("#r_nw2 #r_email_err").text("").removeClass('error');
                    $("#r_nw2 #r_email_err").removeClass('error');
                    return true;
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
            async: false,
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
                            return false;
                            //retVal = false;

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
    
    
  // STEP2 ST  for existing member
// $('.search_select[value="2"]').attr('checked', 'checked');
// $('.search_select').trigger('change');
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

function validate($form_name) 
{
    val_check = 1;
    var retVal = true;
//    alert('form_submit='+retVal);
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
//            var e_email = $.trim($("#e_email").val());
//
//            var taxcode_found = $("#taxcode_found").val();
//            if (e_email != "") {
//                if (valid_email_address(e_email) == false) {
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
//                }
//            } else {
//                    $("#e_email_err").text("[required]").addClass('error');
//                    $("#e_email").addClass('error');
//                    }
//        }else
//        {
//            $("#e_email").removeClass('error');
//            $("#e_email_err").text("").removeClass('error');
//        }
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
                        retVal = false;
                    }else
                     {       $('#captcha').show()
                            $("#captcha2").removeClass('error');
                            $("#captcha2_err").text("").removeClass('error');
                    }
            }
        }
    
    }
    //STEP6 ED
    else {
        
            $("#admin_msg_err").hide();
//            $("#pers_dob_err").text("").removeClass('error');
//            $("#pers_dob").removeClass('error');
//            $("#e_email_err").text("").removeClass('error');
//            $("#e_email").removeClass('error');
//            $("#e_contact_no_err").text("").removeClass('error');
//            $("#e_contact_no").removeClass('error');

    
    /* NRIC TAXCODE NOT FOUND then it vaidatie the which type of id USER provide start*/
//    $r_res = $("#$r_res").val();
//    if($r_res == 0)
//    {
//        var NRIC = $("#NRIC").val();
//        if(NRIC == "")
//        {
//           $("#NRIC_err").text("[required]").addClass('error');
//           $("#NRIC").addClass('error');
//           retVal = false;
//        }else{       
//               if(NRIC == "SNG_3")
//               {
//                    var NRIC_OTHER = $("#NRIC_OTHER").val();
//                    if(NRIC_OTHER == "")
//                    {
//                      $("#NRIC_OTHER_err").text("[required]").addClass('error');
//                      $("#NRIC_OTHER").addClass('error'); 
//                       retVal = false;
//                    }else{
//                          $("#NRIC_OTHER_err").text("").removeClass('error');
//                          $("#NRIC_OTHER").removeClass('error');
//                    }
//               }         
//           $("#NRIC_err").text("").removeClass('error');
//           $("#NRIC").removeClass('error');
//           $taxcode_nric = $("#taxcode_nric").val();
//           check_taxcode_nric($taxcode_nric,'taxcode_nric');
//
//        }
//    }
    /* END */
    
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
           // $taxcode_nric = $("#taxcode_nric").val();
            //check_taxcode_nric($taxcode_nric,'taxcode_nric');
            
            $taxcode_nric = $("#taxcode_nric").val();
            $x = $("#NRIC").val();
            $country_of_residence = 'SGP';
            $nric = $x;
            var uid = 'REFER_TRAINEE';
//            alert("4");
            $.ajax({
                    url: baseurl + "user/check_taxcode",
                    type: "post",
                    async: false,
                    data: {tax_code: $taxcode_nric, country_of_residence: $country_of_residence, nric: $nric, uid: uid},
                    success: function(res)
                    {
                       
                        if (res == 1) {
                            window.username = 'exists';
                            $("#NRIC_err").text("[code exists!]").addClass('error');
                            $("#NRIC").addClass('error');
                           retVal = false;
                        } else if (res == 2) {
                                    //alert("false");
                            $("#NRIC_err").text("[Invalid!]").addClass('error');
                            $("#NRIC").addClass('error');
                            retVal = false;
//                            alert(retVal);
                        } else {
                            window.username = 'notexists';
                            $("#NRIC_err").text("").removeClass('error');
                            $("#NRIC").removeClass('error');

                        }
                    },
                });
 
            
        }
/* END */
        

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
            //javascript:isunique_username(user_name, 'user_name');
            $.ajax({
            url: baseurl + "user/check_username",
            type: "post",
            data: 'username=' + user_name,
            async: false,
            success: function(res) {
                if (res == 1) {
                    $("#try").hide();
                    window.username = 'exists';
                    $("#user_name_err").text("[Username exists!]").addClass('error');
                    $("#user_name").addClass('error');
                    retVal = false;
                } else {
                    $("#try").hide();
                    window.username = 'notexists';
                    $("#user_name_err").text("").removeClass('error');
                    $("#user_name").removeClass('error');
                    return true;
                }
            },
            error: function() {
                return false;
            }
        });
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
               $.ajax({
                                url: baseurl + "user/check_email",
                                type: "post",
                                data: 'email=' + user_registered_email,
                                async: false,
                                success: function(res) {
                                    if (res == 1) {
                    //                    alert("12");
                                        window.email_id = 'exists';
                                        $("frnd_registered_email_err").text("[Email Id exists!]").addClass('error');
                                        $("#frnd_registered_email").addClass('error');
                                        retVal = false;
                                    } else {
                    //                    alert("13");
                                        window.email_id = 'notexists';
                                        $("#frnd_registered_email_err").text("").removeClass('error');
                                        $("#frnd_registered_email").removeClass('error');
                                    }
                                },
                                error: function() {
                                   
                                    return false;
                                }
                            });
//               $(".rnnf #r_nw2 #r_email").removeClass('error');
//	       $(".rnnf #r_nw2 #r_email_err").text("").removeClass('error');   

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
//     alert("step2="+retVal);
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
    
//alert("step3="+retVal);

   
   
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
                        retVal = false;
                    }else
                     {       $('#captcha').show()
                            $("#captcha2").removeClass('error');
                            $("#captcha2_err").text("").removeClass('error');
                    }
            }
        }
//        alert("step4="+retVal);
        
//        if ($('#trainee_form span').hasClass('error')) {
//            retVal = false;
//        }
//        if (retVal == true && $form_name == 'add_form') {
//            $taxcode = $('#taxcode').val();
//            $('#trainee_form').append('<input type="hidden" name="taxcode" value="' + $taxcode + '">');
//            $yes_no = $('input[name="yes_no"]').val();
//            $('#trainee_form').append('<input type="hidden" name="yes_no" value="' + $yes_no + '">');
//        }    
    }
    
//    alert(retVal);
    
//     referal validatin starts
//    alert("before_checkbox="+retVal);
   if ($("#checkbox").prop('checked')==1)
   {
//       alert('checked.');
      
       $var = $('#r_nric').val().length;
        var r_nric = $('#r_nric').val();
        var taxcode_nric = $("#taxcode_nric").val();
        
       if (r_nric == "") {
            $("#r_nric_err").text("[required]").addClass('error');
            $("#r_nric").addClass('error');
            retVal = false;
        }
        else if(taxcode_nric == r_nric){
            $("#r_nric_err").text("[Reference & Referral NRIC same]").addClass('error');
            $("#r_nric").addClass('error');
            retVal = false;
        }
        else {
            $("#r_nric_err").text("").removeClass('error');
            $("#r_nric").removeClass('error');
        }
       
       
       
//       alert('nric_length='+$var);
       if($var >0)
       {
            $r_res = $("#r_res_zero #r_res_zero").val();
            $r_res1=$("#r_res_one #r_res_one").val();
//            alert('result1='+$r_res1);
            if($r_res1 != 1)
            {   
//                alert("checked and NRIC not found");

               
                 


        var r_NRIC = $("#r_NRIC").val();
        if(r_NRIC == "")
        {
           $("#r_NRIC_err").text("[required]").addClass('error');
           $("#r_NRIC").addClass('error');
           retVal = false;
        }
        else
        {       
            if(r_NRIC == "SNG_3")
            {
                 var r_NRIC_OTHER = $("#r_NRIC_OTHER").val();
                 if(r_NRIC_OTHER == "")
                 {
                   $("#r_NRIC_OTHER_err").text("[required]").addClass('error');
                   $("#r_NRIC_OTHER").addClass('error'); 
                    retVal = false;
                 }else{
                       $("#r_NRIC_OTHER_err").text("").removeClass('error');
                       $("#r_NRIC_OTHER").removeClass('error');
                 }
            }         
            $("#r_NRIC_err").text("").removeClass('error');
            $("#r_NRIC").removeClass('error');
//            $r_nric = $("#r_nric").val();
//            check_referal_nric($taxcode_nric,'r_nric');
//            $taxcode_nric = $("#r_nric").val();
//            check_referal_nric_code($taxcode_nric,'taxcode_nric');
            $r_taxcode_nric = $("#r_nric").val();
            $country_of_residence1 = 'SGP';
            $r_nric = $("#r_NRIC").val();
            var r_uid = 'REFER_TRAINEE';
            $.ajax({
                    url: baseurl + "user/check_taxcode",
                    type: "post",
                    async: false,
                    data: {tax_code: $r_taxcode_nric, country_of_residence: $country_of_residence1, nric: $r_nric, uid: r_uid},
                    success: function(res)
                    {
                       
                        if (res == 1) {
                            window.username = 'exists';
                            $("#r_NRIC_err").text("[code exists!]").addClass('error');
                            $("#r_NRIC").addClass('error');
                           retVal = false;
                        } else if (res == 2) {
                                    //alert("false");
                            $("#r_NRIC_err").text("[Invalid!]").addClass('error');
                            $("#r_NRIC").addClass('error');
                            retVal = false;
                           
                        } else {
                            window.username = 'notexists';
                            $("#r_NRIC_err").text("").removeClass('error');
                            $("#r_NRIC").removeClass('error');

                        }
                    },
                });

        }
                
                $("#def1 #r_name").val('');
                $("#def2 #r_email").val('');
                $("#def3 #r_contact").val('');   
                
                
                
                
                $("#r_name_err").text("").removeClass('error');
                $("#r_name").removeClass('error');
                
                $("#r_email").removeClass('error');
                $("#r_email_err").text("").removeClass('error');
                
                $("#r_contact_err").text("").removeClass('error');
                $("#r_contact").removeClass('error');
                
                var r_name = $.trim($(".rnnf #r_nw1 #r_name").val());
                if (r_name == "") {
                    $(".rnnf #r_nw1 #r_name_err").text("[required]").addClass('error');
                    $(".rnnf #r_nw1 #r_name").addClass('error');
                    retVal = false;
                } else {
                    $(".rnnf #r_nw1 #r_name_err").text("").removeClass('error');
                    $(".rnnf #r_nw1 #r_name").removeClass('error');
                }
                
                var user_registered_email = $.trim($("#frnd_registered_email").val());
                var r_email = $.trim($(".rnnf #r_nw2 #r_email").val());
                
                if (r_email == "") {
                    $(".rnnf #r_nw2 #r_email_err").text("[required]").addClass('error');
                    $(".rnnf #r_nw2 #r_email").addClass('error');
                    retVal = false;
                }
                else
                {   
                    if(valid_email_address(r_email) == false){
                        $(".rnnf #r_nw2 #r_email_err").text("[invalid]").addClass('error');
                        $(".rnnf #r_nw2 #r_email").addClass('error');
                        retVal = false;
                    }
                    else if(user_registered_email == r_email)
                    {
                        $(".rnnf #r_nw2 #r_email_err").text("[Reference & Referral Email id Same]").addClass('error');
                        $(".rnnf #r_nw2 #r_email").addClass('error');
                        retVal = false;
                    }
                    else{
                            $.ajax({
                                    url: baseurl + "user/check_email",
                                    type: "post",
                                    data: 'email=' + r_email,
                                    async: false,
                                    success: function(res) {
                                        if (res == 1) {
                        //                    alert("12");
                                            window.email_id = 'exists';
                                            $("frnd_registered_email_err").text("[Email Id exists!]").addClass('error');
                                            $("#frnd_registered_email").addClass('error');
                                            retVal = false;
                                        } else {
                        //                    alert("13");
                                            window.email_id = 'notexists';
                                            $("#frnd_registered_email_err").text("").removeClass('error');
                                            $("#frnd_registered_email").removeClass('error');
                                        }
                                    },
                                    error: function() {

                                        return false;
                                    }
                                });
                            
                       // $(".rnnf #r_nw2 #r_email").removeClass('error');
                       // $(".rnnf #r_nw2 #r_email_err").text("").removeClass('error');   
                    }
                }
                var r_contact  = $.trim($(".rnnf #r_nw3 #r_contact").val());
                if (r_contact == "") {
                    $(".rnnf #r_nw3 #r_contact_err").text("[required]").addClass('error');
                    $(".rnnf #r_nw3 #r_contact").addClass('error');
                    retVal = false;
                } 
                else 
                {
                    if (valid_contact_number(r_contact) == false) {
                       $(".rnnf #r_nw3 #r_contact_err").text("[invalid]").addClass('error');
                       $("#r_contact").addClass('error');
                       retVal = false;
                    } else {
                    $(".rnnf #r_nw3 #r_contact_err").text("").removeClass('error');
                    $(".rnnf #r_nw3 #r_contact").removeClass('error');
                }
                }
            }
            else
            {
//                alert("checked and NRIC found");
               
                $(".rnnf #r_nw1 #r_name").val('');
                $(".rnnf #r_nw2 #r_email").val('');
                $(".rnnf #r_nw3 #r_contact").val('');
//                alert(retVal);
                $("#def1 #r_name").val('');
                $("#def2 #r_email").val('');
                $("#def3 #r_contact").val('');   
                
                $("#def2 #r_name_err").text("").removeClass('error');
                $("#def2 #r_name").removeClass('error');
                
                $("#def3 #r_email").removeClass('error');
                $("#def3 #r_email_err").text("").removeClass('error');
                
                $("#r_contact_err").text("").removeClass('error');
                $("#r_contact").removeClass('error');
                
                $(".rnnf #r_nw1 #r_name_err").text("").removeClass('error');
                $(".rnnf #r_nw1 #r_name").removeClass('error');
                
                $(".rnnf #r_nw2 #r_email_err").text("[required]").addClass('error');
                $(".rnnf #r_nw2 #r_email").addClass('error');
                
                $(".rnnf #r_nw3 #r_contact_err").text("").removeClass('error');
                $(".rnnf #r_nw3 #r_contact").removeClass('error');
            }
//            alert(retVal);
        }
    }
   else
   { 
//        alert('unchecked..');
        $(".rnnf #r_nw1 #r_name").val('');
        $(".rnnf #r_nw2 #r_email").val('');
        $(".rnnf #r_nw3 #r_contact").val('');
        
        $(".rnnf #r_nw1 #r_name_err").text("").removeClass('error');
        $(".rnnf #r_nw1 #r_name").removeClass('error');
        
        $(".rnnf #r_nw2 #r_email_err").text("[required]").addClass('error');
        $(".rnnf #r_nw2 #r_email").addClass('error');
        
        $(".rnnf #r_nw3 #r_contact_err").text("").removeClass('error');
        $(".rnnf #r_nw3 #r_contact").removeClass('error');
        
        var r_name = $.trim($("#def1 #r_name").val());
        if (r_name == "") {
            $("#def1 #r_name_err").text("[required]").addClass('error');
            $("#def1 #r_name").addClass('error');
            retVal = false;
//            alert("name="+r_name+"res="+retVal);
        } else {
            $("#def1 #r_name_err").text("").removeClass('error');
            $("#def1 #r_name").removeClass('error');
//            alert("name1="+r_name+"res1="+retVal);
        }
    
        var r_email = $.trim($("#def2 #r_email").val());
        if (r_email == "") {
            $("#def2 #r_email_err").text("[required]").addClass('error');
            $("#def2 #r_email").addClass('error');
            retVal = false;
//               alert("emaail1="+r_email+"res1="+retVal);
        }
        else
        {   
            if(valid_email_address(r_email) == false){
                $("#def2 #r_email_err").text("[invalid]").addClass('error');
                $("#def2 #r_email").addClass('error');
                retVal = false;
              
            }else{
                $("#def2 #r_email").removeClass('error');
                $("#def2 #r_email_err").text("").removeClass('error'); 
//                alert("emaail="+r_email+"res="+retVal);
            }

        }
    
        var r_contact  = $.trim($("#def3 #r_contact").val());
        if (r_contact == "") 
        {
           $("#def3 #r_contact_err").text("[required]").addClass('error');
           $("#def3 #r_contact").addClass('error');
           retVal = false;
//            alert("contact1="+r_contact+"res1="+retVal);
        } else {
           if (valid_contact_number(r_contact) == false) {
               $("#def3 #r_contact_err").text("[invalid]").addClass('error');
               $("#def3 #r_contact").addClass('error');
               retVal = false;
           } else {
               $("#def3 #r_contact_err").text("").removeClass('error');
               $("#def3 #r_contact").removeClass('error');
//               alert("contact="+r_contact+"res="+retVal);
           }
        }
//        alert(retVal);
    }
//if ($('#trainee_form span').hasClass('error')) {
//        retVal = false;
//    }
    
//alert(retVal);
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
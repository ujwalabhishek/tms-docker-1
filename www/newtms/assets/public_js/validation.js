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
    } else {
        $('#SGP_OTHERS_label').text('');
        $('#SGP_OTHERS_label').text('NRIC :');
        $('#SGP_ID_label').text('');
        $('#SGP_ID_label').text('NRIC Code :');
        $("#SGP_OTHERS").hide();
        $("#SGP_ID").show();
    }
});
$("#NRIC_OTHER").change(function() {
    if (this.value == "") {
        $("#SGP_ID").hide();
    } else {
        $("#SGP_ID").show();
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
    });
});
$(document).ready(function(){
     $('#country_of_residence option:nth(0)').attr("selected", "selected"); 
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
        return false;
    } else if (valid_user_name(e) == false) {
        $("#" + id + "_err").text("[invalid]").addClass('error');
        $("#" + id).addClass('error');
         return false;
    } else {
        $.ajax({
            url: baseurl + "user/check_username",
            type: "post",
            data: 'username=' + e,
            async: false,
            success: function(res) {
                if (res == 1) {
                    window.username = 'exists';
                    $("#" + id + "_err").text("[Username exists!]").addClass('error');
                    $("#" + id).addClass('error');
                    return false;
                } else {
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

function isunique_taxcode(e, id, uid) {
    e = $.trim(e);
    if(e.length > 0  || refer_friend!=1){
        if (e == '') {
            $("#" + id + "_err").text("[required]").addClass('error');
            $("#" + id).addClass('error');
            return false;
        } else {
            var $country_of_residence = $.trim($('#country_of_residence').val());
            var $nric = $.trim($('#NRIC').val());

            $.ajax({
                url: baseurl + "user/check_taxcode",
                type: "post",
                async: false,
                data: {tax_code: e, country_of_residence: $country_of_residence, nric: $nric, uid: uid},
                success: function(res) {
                    if (res == 1) {
                        window.username = 'exists';
                        $("#" + id + "_err").text("[code exists!]").addClass('error');
                        $("#" + id).addClass('error');
                        return false;
                    } else if (res == 2) {
                        $("#" + id + "_err").text("[Invalid!]").addClass('error');
                        $("#" + id).addClass('error');
                        return false;
                    } else {
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
    }else {
        $("#" + id + "_err").text("").removeClass('error');
        $("#" + id).removeClass('error');
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
         return false;
    }
    else if (e == '' && id == 'frnd_registered_email') {
        $("#" + id + "_err").text("").removeClass('error');
        $("#" + id).removeClass('error');
    }
    else if (e == '' && id == 'r_email') { // referl email check start
        $("#" + id + "_err").text("").removeClass('error');
        $("#" + id).removeClass('error');
    }// referl email check end
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
/*
 * This function for triggering the validate
 */
var val_check = 0; 
$(document).ready(function() {
//    alert("hi");
    $('#trainee_form select,#trainee_form input').change(function() {
        if (val_check == 1) {
            return validate();
        }
    });
});

function validate($form_name) {
    val_check = 1;
    var retVal = true;
    var country_of_residence = $("#country_of_residence").val();
    if (country_of_residence == "") {
        $("#country_of_residence_err").text("[required]").addClass('error');
        $("#country_of_residence").addClass('error');
        retVal = false;
    }
    else {
        $("#country_of_residence_err").text("").removeClass('error');
        $("#country_of_residence").removeClass('error');
    }

    if (country_of_residence == "IND") {
        var PAN = $.trim($("#PAN").val());
        if(PAN.length > 0  || refer_friend!=1){
            var pan_error_text = $("#PAN_err").text();
            if (PAN == "") {
                $("#PAN_err").text("[required]").addClass('error');
                $("#PAN").addClass('error');
                retVal = false;
            } else {
                isunique_taxcode(PAN, 'PAN', uid = 'REFER_TRAINEE');
            }
        } else {
            $("#PAN_err").text("").removeClass('error');
            $("#PAN").removeClass('error');
        }
    }
    if (country_of_residence == "SGP") {
        var NRIC = $.trim($("#NRIC").val());
        if(NRIC.length > 0  || refer_friend !=1){
            var nric_error_text = $("#NRIC_ID_err").text();
            var NRIC_ID = $.trim($("#NRIC_ID").val());
            var NRIC_OTHER = $.trim($("#NRIC_OTHER").val());
            if (NRIC == "") {
                $("#NRIC_err").text("[required]").addClass('error');
                $("#NRIC").addClass('error');
                retVal = false;
            } else if (NRIC == "SNG_3") {
                if(NRIC_OTHER.length > 0  || refer_friend!=1){
                    if (NRIC_OTHER == "") {
                        $("#NRIC_OTHER_err").text("[required]").addClass('error');
                        $("#NRIC_OTHER").addClass('error');
                        retVal = false;
                    } else {
                        $("#NRIC_OTHER_err").text("").removeClass('error');
                        $("#NRIC_OTHER").removeClass('error');
                    }
                } else {
                    $("#NRIC_OTHER_err").text("").removeClass('error');
                    $("#NRIC_OTHER").removeClass('error');
                }
                if(NRIC_ID.length > 0  || refer_friend != 1){
                    if (NRIC_ID == "") {
                        $("#NRIC_err").text("").removeClass('error');
                        $("#NRIC").removeClass('error');
                        $("#NRIC_ID_err").text("[required]").addClass('error');
                        $("#NRIC_ID").addClass('error');
                        retVal = false;
                    } else {
                        $("#NRIC_err").text("").removeClass('error');
                        $("#NRIC").removeClass('error');
                        isunique_taxcode(NRIC_ID, 'NRIC_ID', uid = 'REFER_TRAINEE');
                    }
                } else {
                    $("#NRIC_ID_err").text("").removeClass('error');
                    $("#NRIC_ID").removeClass('error');
                }
            } else if (NRIC_ID == "" && refer_friend !=1) {
                $("#NRIC_err").text("").removeClass('error');
                $("#NRIC").removeClass('error');
                $("#NRIC_ID_err").text("[required]").addClass('error');
                $("#NRIC_ID").addClass('error');
                retVal = false;
            } else {
                $("#NRIC_err").text("").removeClass('error');
                $("#NRIC").removeClass('error');
                isunique_taxcode(NRIC_ID, 'NRIC_ID', uid = 'REFER_TRAINEE');
            }
        } else {
            $("#NRIC_err").text("").removeClass('error');
            $("#NRIC").removeClass('error');
            $("#NRIC_ID_err").text("").removeClass('error');
            $("#NRIC_ID").removeClass('error');
        }
    }
    if (country_of_residence == "USA") {
        var SSN = $.trim($("#SSN").val());
        if(SSN.length > 0  || refer_friend!=1){
            var ssn_error_text = $("#SSN_err").text();
            if (SSN == "") {
                $("#SSN_err").text("[required]").addClass('error');
                $("#SSN").addClass('error');
                retVal = false;
            } else {
                isunique_taxcode(SSN, 'SSN', uid = 'REFER_TRAINEE');
            }
        } else {
            $("#SSN_err").text("").removeClass('error');
            $("#SSN").removeClass('error');
        }
    }

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
   
   
//alert("step1"+retVal);
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

    var pers_dob = $.trim($("#pers_dob").val());
    if (valid_date_field(pers_dob) == false && pers_dob != '') {
        $("#pers_dob_err").text("[dd-mm-yy format]").addClass('error');
        $("#pers_dob").removeClass('error');
        retVal = false;
    }
    else {
        $("#pers_dob_err").text("").removeClass('error');
        $("#pers_dob").removeClass('error');
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
    
    /* validation for email id when nric not found skm start */
    var frnd_registered_email = $.trim($("#frnd_registered_email").val());
    if (frnd_registered_email != "")
    {
        if (valid_email_address(frnd_registered_email) == false) {
            $("#frnd_registered_email_err").text("[invalid]").addClass('error');
            $("#frnd_registered_email").addClass('error');
            retVal = false;
        } else {
            $("#frnd_registered_email").removeClass('error');
            $("#frnd_registered_email_err").text("").removeClass('error');
        }
    } else if (frnd_registered_email == "")
    {
        $("#frnd_registered_email_err").text("[required]").addClass('error');
        $("#frnd_registered_email").addClass('error');
        retVal = false;
    } else {
        $("#frnd_registered_email").removeClass('error');
        $("#frnd_registered_email_err").text("").removeClass('error');
    }//end ("step1"+retVal);

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
            isunique_email(user_registered_email, 'frnd_registered_email')

        }
    }
   
    /* validation for email id when nric not found skm start */
        var frnd_registered_email = $.trim($("#frnd_registered_email").val());
        if (frnd_registered_email != "") 
        {
            if (valid_email_address(frnd_registered_email) == false) {
                    $("#frnd_registered_email_err").text("[invalid]").addClass('error');
                    $("#frnd_registered_email").addClass('error');
                    retVal = false;
            }else{
                $("#frnd_registered_email").removeClass('error');
                $("#frnd_registered_email_err").text("").removeClass('error');
            }
        }else if(frnd_registered_email == "")
        {
                   $("#frnd_registered_email_err").text("[required]").addClass('error');
                   $("#frnd_registered_email").addClass('error');
                   retVal = false;
        }else{
                $("#frnd_registered_email").removeClass('error');
                $("#frnd_registered_email_err").text("").removeClass('error');
        }//end 
   
   
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
//alert("step2"+retVal);
    var occupation = $.trim($("#occupation").val());
    if (occupation == "") {
        $("#occupation_err").text("[required]").addClass('error');
        $("#occupation").addClass('error');
        retVal = false;
    } else {
        $("#occupation_err").text("").removeClass('error');
        $("#occupation").removeClass('error');
    }

    var pers_city = $.trim($("#pers_city").val());
    if (pers_city != '') {
        if (valid_name(pers_city) == false) {
            $("#pers_city_err").text("[invalid]").addClass('error');
            $("#pers_city").addClass('error');
            retVal = false;
        } else {
            $("#pers_city_err").text("").removeClass('error');
            $("#pers_city").removeClass('error');
        }
    } else {
        $("#pers_city_err").text("").removeClass('error');
        $("#pers_city").removeClass('error');
    }

    var pers_zipcode = $.trim($("#pers_zipcode").val());
    if (pers_zipcode != '') {
        if (valid_zip(pers_zipcode) == false) {
            $("#pers_zipcode_err").text("[invalid]").addClass('error');
            $("#pers_zipcode").addClass('error');
            retVal = false;
        } else {
            $("#pers_zipcode_err").text("").removeClass('error');
            $("#pers_zipcode").removeClass('error');
        }
    } else {
        $("#pers_zipcode_err").text("").removeClass('error');
        $("#pers_zipcode").removeClass('error');
    }
    if ($('#captcha2').length > 0) {
        $captcha = $('#captcha2').val().trim();
        if ($captcha.length == 0) {
            $("#captcha2_err").text("[required]").addClass('error');
            $("#captcha2").addClass('error');
            retVal = false;
        } else {
            $("#captcha2_err").text("").removeClass('error');
            $("#captcha2").removeClass('error');
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
// skm referl contact no start
function validate_r_contact_number(e, id) {
    e = $.trim(e);
//    alert(e);
//    alert("inner loop")
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
// skm referl contact no end
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
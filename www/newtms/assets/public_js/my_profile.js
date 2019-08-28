$('.email').change(function() {
    if ($("#bypassemail_1").is(":checked")) {
        var email = $.trim($('#user_registered_email').val());
        if (email == '') {
            $("#user_registered_email_err").text("").removeClass('error');
            $("#user_registered_email").removeClass('error');
            $("#pers_conf_email_err").text("").removeClass('error');
            $("#pers_conf_email").removeClass('error');
        }
        $('#span_activate_user').css("display", "");
        $('#BPEMAC_content').css("display", "");
        $('#EMACRQ_content').css("display", "none");
        $("#span_email_id").css("display", "none");
        $("#span_confirm_email_id").css("display", "none");
    } else {
        $('#span_activate_user').css("display", "none");
        $('#BPEMAC_content').css("display", "none");
        $('#EMACRQ_content').css("display", "");
        $("#span_email_id").css("display", "");
        $("#span_confirm_email_id").css("display", "");
    }
    return false;
});



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
 * for singapore contry of residence.
 * 
 */
$("#NRIC").change(function() {
    if (this.value == "") {
        $("#SGP_ID").hide();
        $("#SGP_OTHERS").hide();
    } else if (this.value == "SNG_3") {
        $("#SGP_OTHERS").show();
        $('#SGP_OTHERS option:first-child').attr("selected", "selected");
        // $('#SGP_OTHERS_label').text('');
        $('#SGP_OTHERS_label').text('');
        $('#SGP_ID_label').text('');
        $('#SGP_ID_label').text('');
    } else {
        $('#NRIC_OTHER_err').text('').removeClass('error');
        $('#NRIC_OTHER').removeClass('error');
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

$('#pers_country').change(function() {
    var country_param = $(this).val();
    if (country_param) {
        $.post(baseurl + 'user/get_states_json', {country_param: country_param}, function(data) {
            json_data = $.parseJSON(data);
            $pers_states_select = $("#pers_states");
            $pers_states_select.html('<option value="">Select</option>');
            $.each(json_data, function(i, item) {
                //alert(item.parameter_id+', '+item.category_name);
                $pers_states_select.append('<option value="' + item.parameter_id + '">' + item.category_name + '</option>');
            });
        });
    } else {
        $('#pers_states').find('option').remove().end().append('<option value="">Select</option>');
    }
});

function valid_contact_number(contactNum) {
    return /^\d+$/.test(contactNum.replace(/[,\s]/g, '')); //Allows space, numbers,comma
}
function valid_email_address(emailAddress) {
    var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
    // alert( pattern.test(emailAddress) );
    return pattern.test(emailAddress);
}
function valid_user_name(userName) {
    var pattern = new RegExp(/^[a-zA-Z0-9_]+$/);
    // alert( pattern.test(emailAddress) );
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

function validate() {
    var retVal = true;
    //Personal details
    /* none editable field in my profile start */
    
//    var country_of_residence = $("#country_of_residence").val();
//    if (country_of_residence == "") {
//        $("#country_of_residence_err").text("[required]").addClass('error');
//        $("#country_of_residence").addClass('error');
//        retVal = false;
//    }
//    else {
//        $("#country_of_residence_err").text("").removeClass('error');
//        $("#country_of_residence").removeClass('error');
//    }
//
//    if (country_of_residence == "IND") {
//        var PAN = $.trim($("#PAN").val());
//        var pan_error_text = $("#PAN_err").text();
//        if (PAN == "") {
//            $("#PAN_err").text("[required]").addClass('error');
//            $("#PAN").addClass('error');
//            retVal = false;
//        }
//        else if (pan_error_text != '[code exists!]') {
//            $("#PAN_err").text("").removeClass('error');
//            $("#PAN").removeClass('error');
//        }
//    }
//    if (country_of_residence == "SGP") {
//        var NRIC = $.trim($("#NRIC").val());
//        var nric_error_text = $("#NRIC_ID_err").text();
//        var NRIC_ID = $("#NRIC_ID").val();
//        var NRIC_OTHER = $("#NRIC_OTHER").val();
//        if (NRIC == "") {
//            $("#NRIC_err").text("[required]").addClass('error');
//            $("#NRIC").addClass('error');
//            retVal = false;
//        } else if (NRIC == "SNG_3") {
//            if (NRIC_OTHER == "") {
//                $("#NRIC_OTHER_err").text("[required]").addClass('error');
//                $("#NRIC_OTHER").addClass('error');
//                retVal = false;
//            } else {
//                $("#NRIC_OTHER_err").text("").removeClass('error');
//                $("#NRIC_OTHER").removeClass('error');
//            }
//            if (NRIC_ID == "") {
//                $("#NRIC_err").text("").removeClass('error');
//                $("#NRIC").removeClass('error');
//                $("#NRIC_ID_err").text("[required]").addClass('error');
//                $("#NRIC_ID").addClass('error');
//                retVal = false;
//            } else if (nric_error_text != '[code exists!]') {
//                $("#NRIC_ID_err").text("").removeClass('error');
//                $("#NRIC_ID").removeClass('error');
//            }
//        } else if (NRIC_ID == "") {
//            $("#NRIC_err").text("").removeClass('error');
//            $("#NRIC").removeClass('error');
//            $("#NRIC_ID_err").text("[required]").addClass('error');
//            $("#NRIC_ID").addClass('error');
//            retVal = false;
//        } else if (nric_error_text != '[code exists!]' && nric_error_text != '[Invalid!]') {
//            $("#NRIC_err").text("").removeClass('error');
//            $("#NRIC").removeClass('error');
//            $("#NRIC_ID_err").text("").removeClass('error');
//            $("#NRIC_ID").removeClass('error');
//        }
//    }
//    if (country_of_residence == "USA") {
//        var SSN = $.trim($("#SSN").val());
//        var ssn_error_text = $("#SSN_err").text();
//        if (SSN == "") {
//            $("#SSN_err").text("[required]").addClass('error');
//            $("#SSN").addClass('error');
//            retVal = false;
//        } else if (ssn_error_text != '[code exists!]') {
//            $("#SSN_err").text("").removeClass('error');
//            $("#SSN").removeClass('error');
//        }
//    } end

//    var pers_first_name = $.trim($("#pers_first_name").val());
//    if (pers_first_name == "") {
//        $("#pers_first_name_err").text("[required]").addClass('error');
//        $("#pers_first_name").addClass('error');
//        retVal = false;
//    } else if (valid_name(pers_first_name) == false) {
//        $("#pers_first_name_err").text("[invalid]").addClass('error');
//        $("#pers_first_name").addClass('error');
//    } else {
//        $("#pers_first_name_err").text("").removeClass('error');
//        $("#pers_first_name").removeClass('error');
//    }
    
    var pers_first_name = $.trim($("#pers_first_name").val());
    if (pers_first_name == "") {
        $("#pers_first_name_err").text("[required]").addClass('error');
        $("#pers_first_name").addClass('error');
        retVal = false;
    } else {
        $("#pers_first_name_err").text("").removeClass('error');
        $("#pers_first_name").removeClass('error');
    }
    
/* none editable field in my profile start */
//    var pers_second_name = $.trim($("#pers_second_name").val());
//    if(pers_second_name != ''){
//        if(valid_name(pers_second_name) == false) {
//            $("#pers_second_name_err").text("[invalid]").addClass('error');
//            $("#pers_second_name").addClass('error');
//        }else {
//            $("#pers_second_name_err").text("").removeClass('error');
//            $("#pers_second_name").removeClass('error');
//        }
//    }else {
//        $("#pers_second_name_err").text("").removeClass('error');
//        $("#pers_second_name").removeClass('error');
//    } //end

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
    if (valid_date_field(pers_dob) == false && pers_dob != "") {
        $("#pers_dob_err").text("[dd-mm-yy format]").addClass('error');
        $("#pers_dob").removeClass('error');
        retVal = false;
    }
    else {
        $("#pers_dob_err").text("").removeClass('error');
        $("#pers_dob").removeClass('error');
    }
/* none editable field in my profile start */
//    var pers_contact_number = $.trim($("#pers_contact_number").val());
//    if (pers_contact_number == "") {
//        $("#pers_contact_number_err").text("[required]").addClass('error');
//        $("#pers_contact_number").addClass('error');
//        retVal = false;
//    } else {
//        if (valid_contact_number(pers_contact_number) == false) {
//            $("#pers_contact_number_err").text("[invalid]").addClass('error');
//            $("#pers_contact_number").addClass('error');
//            retVal = false;
//        } else {
//            $("#pers_contact_number_err").text("").removeClass('error');
//            $("#pers_contact_number").removeClass('error');
//        }
//    }//end

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
/* none editable field in my profile start */
    /*email validations  starts*/
//    if ($("#user_registered_email").val() !== '') {
//        var user_registered_email = $.trim($("#user_registered_email").val());
//        if (valid_email_address(user_registered_email) == false) {
//            $("#user_registered_email_err").text("[invalid]").addClass('error');
//            $("#user_registered_email").addClass('error');
//            retVal = false;
//        } else {
//            $("#user_registered_email_err").text("").removeClass('error');
//            $("#user_registered_email").removeClass('error');
//            $("#pers_conf_email_err").text("").removeClass('error');
//            $("#pers_conf_email").removeClass('error');
//        }
//    }else{ $("#user_registered_email_err").text("").removeClass('error');
//            $("#user_registered_email").removeClass('error');
//            $("#pers_conf_email_err").text("").removeClass('error');
//            $("#pers_conf_email").removeClass('error');}

//    var pers_conf_email = $.trim($("#pers_conf_email").val());
//    var user_registered_email = $.trim($("#user_registered_email").val());
//    if (pers_conf_email != user_registered_email) {
//        if (valid_email_address(user_registered_email) == true) {
//            if (pers_conf_email == '') {
//                $("#pers_conf_email_err").text("[required]").addClass('error');
//                $("#pers_conf_email").addClass('error');
//            } else if (valid_email_address(pers_conf_email) == false) {
//                $("#pers_conf_email_err").text("[invalid]").addClass('error');
//                $("#pers_conf_email").addClass('error');
//            } else {
//                $("#pers_conf_email_err").text("[Email does not match]").addClass('error');
//                $("#pers_conf_email").addClass('error');
//            }
//            retVal = false;
//        }
//    } else {
//        $("#pers_conf_email").removeClass('error');
//        $("#pers_conf_email_err").text("").removeClass('error');
//    }//end

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
    //final checking if any error span in document                       
    if ($('#trainee_validation_div span').hasClass('error')) {
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
    $('#trainee_edit').submit(function() {
        check = 1;
        return validate();
    });
    $('#trainee_edit select,#trainee_form input').change(function() {
        if (check == 1) {
            return validate();
        }
    });
    //for salary range and occupation manadtory based on company.
    $('#assign_company').trigger("change");
});
/*
 * For showing the other reason for deactivation
 * Author: Bineesh
 * Date: 14/08/2014
 */

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
/*
 * This function used to check the uniquness of PAN id while focus out on the control.
 * Author: Bineesh.
 * Date: 25/08/2014.
 */
function isunique_taxcode(e, id) {
    e = $.trim(e);
    if (e == '') {
        $("#" + id + "_err").text("[required]").addClass('error');
        $("#" + id).addClass('error');
        return false;
    } else {
        var $country_of_residence = $.trim($('#country_of_residence').val());
        var $nric = $.trim($('#NRIC').val());
        $uid = ($('input[name="userid"][type="hidden"]').length > 0) ? $('input[name="userid"][type="hidden"]').val().trim() : '';
        $.ajax({
            url: baseurl + "user/check_taxcode",
            type: "post",
            data: {tax_code: e, country_of_residence: $country_of_residence, nric: $nric, uid: $uid},
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
}
if ($('#user_registered_email').val() == '') {
    primary_email = $('#user_registered_email').val();
} else {
    primary_email = $('#frnd_registered_email').val();
}

function isunique_email(e, id) {
    e = $.trim(e);
    if (e !== '') {
        if (valid_email_address(e) == false) {
            $("#" + id + "_err").text("[invalid]").addClass('error');
            $("#" + id).addClass('error');
        } else {
            $.ajax({
                url: baseurl + 'user/check_email',
                type: "post",
                data: 'email=' + e,
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
        }
    } else {
        $("#" + id + "_err").text('').removeClass('error');
        $("#" + id + "_err").removeClass('error');
    }
    return false;
}
/*Modified  for edit  trainee*/
function confirm_email(e, id) {

    c_email = $.trim($('#user_registered_email').val());

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
/*
 * This method for allowing number value only
 * Author Bineesh
 * Date: 12/08/2014
 */
$('.number').keydown(function(event) {
    // Allow special chars + arrows    
    if ($.inArray(event.keyCode, [46, 8, 9, 27, 13, 32, 188]) !== -1
            || ($.inArray(event.keyCode, [65, 67, 86]) !== -1 && event.ctrlKey === true)
            || (event.keyCode >= 35 && event.keyCode <= 39)) {
        return;
    } else {
        // If it's not a number stop the keypress
        if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {
            event.preventDefault();
        }
    }
});
$(".float_number").keydown(function(e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 173]) !== -1 ||
            // Allow: Ctrl+A
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                    // Allow: home, end, left, right
                            (e.keyCode >= 35 && e.keyCode <= 39)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

$(".alphabets").keydown(function(e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 32]) !== -1 ||
            // Allow: Ctrl+A
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                    // Allow: home, end, left, right
                            (e.keyCode >= 35 && e.keyCode <= 39)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if (((e.keyCode < 65 || e.keyCode > 90))) {
                e.preventDefault();
            }
        });
/*
 * Added by bineesh for zip code
 * Date : 10 sep 2014.
 */
$(".alphanumeric").keydown(function(e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 32]) !== -1 ||
            // Allow: Ctrl+A
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                    // Allow: home, end, left, right
                            (e.keyCode >= 35 && e.keyCode <= 39)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 90) && (e.keyCode < 96 || e.keyCode > 105))) {
                e.preventDefault();
            }
        });
// for resetting the states.
function reset_states() {
    var curr_country = $("#current_pers_country").val();
    var curr_state = $("#current_pers_states").val();
    $.post(baseurl + "internal_user/get_states_json", {country_param: curr_country}, function(data) {
        var json_data = $.parseJSON(data);
        var $pers_states_select = $("#pers_states");
        $pers_states_select.html('<option value="">Select</option>');
        var selected_attr_value = '';
        $.each(json_data, function(i, item) {
            if (item.parameter_id == curr_state) {
                selected_attr_value = 'selected="selected"';
            }
            else {
                selected_attr_value = '';
            }
            $pers_states_select.append('<option value="' + item.parameter_id + '" ' + selected_attr_value + '>' + item.category_name + '</option>');
        });

    });
}

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
            //$('#imgprvw').removeAttr('src');
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
//$("#userfile").replaceWith($("#userfile").clone(true));
    $('#imgprvw').removeAttr('src');
    $('#user_image_preview').hide();
    $('#deleteimageyes').removeAttr('checked');
    $('#deleteimageno').attr('checked', 'checked');

}
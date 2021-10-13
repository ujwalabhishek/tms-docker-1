/* 
 * This js included in edit company page.
 */
$(function() {
    i = $('#contact_details table').size();

    $('#addMore').click(function() {
        var ret1 = validate_company_edit_form();
        if (ret1 == true) {
            var memData = $('#contact_details_data').html();

            memData = memData.replace('id="email_01_"', 'id="email_01_' + i + '"');
            memData = memData.replace('/td_email_01_/g', 'td_email_01_' + i);

            memData = memData.replace('id="username_"', 'id="username_' + i + '"');
            memData = memData.replace('/td_username_/g', 'td_username_' + i);

            memData = memData.replace('id="fname_"', 'id="fname_' + i + '"');
            memData = memData.replace('id="lname_"', 'id="lname_' + i + '"');
            memData = memData.replace('id="contactno_"', 'id="contactno_' + i + '"');

            $("#contact_details").append(memData);
            i++;
        }
    });
});

$(document).ready(function() {
    $("#addr_country").change(function() {
        var pc = $(this).val();
        if (pc) {
            $.post(baseurl + "internal_user/get_states_json", {country_param: pc}, function(data) {
                json_data = $.parseJSON(data);
                $pers_states_select = $("#pers_states");
                $pers_states_select.html('<option value="">Select</option>');
                $.each(json_data, function(i, item) {
                    $pers_states_select.append('<option value="' + item.parameter_id + '">' + item.category_name + '</option>');
                });

            });
        } else {

        }
    });



    $("#country_of_residence").change(function() {
        var country_of_residence = $(this).val();
        if (country_of_residence == "SGP") {
            $("#SGP").show();
        }
        else {
            $("#SGP").hide();
        }
    });




    $("#remInput").click(function() {
        if ($('#contact_details table').size() == 1) {
            alert('One contact is required.');
            return false;
        }
    });



    $("#companyEditForm").validate({
        onkeyup: function(element, event) {
            company_contact_form_clear_errors();
        },
        rules: {
            "country_of_residence": "required",
            "business_s": "required",
            "business_type": "required",
            company_name: {
                required: true,
            },
            regno: {
                required: true,
            },
            phoneno: {
                required: true,
                accept: "[0-9-+.,()]+"
            },
            faxno: {
                valid_fax_number: true,
            },
            localdiscount: {
                number: true,
                min: 0,
                max: 100,
            },
            foreigndiscount: {
                number: true,
                min: 0,
                max: 100,
            },
            comp_attn: {
                required: {
                    depends: function() {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
            },
            comp_email: {
                required: {
                    depends: function() {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
                email: true,
            },
            zipcode: {
                valid_zipcode: true,
            },
            "fname[]": {
                required: {
                    depends: function() {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
            },
            "contactno[]": {
                required: {
                    depends: function() {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
                valid_contact_number: true,
            },
            "mobileno[]": {
                valid_mobile_number: {
                    depends: function() {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
            },
            "email_01[]": {
                required: {
                    depends: function() {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
                email: true,
            },
            "email_02[]": {
                email: {
                    depends: function() {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
            },
            "username[]": {
                required: {
                    depends: function() {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
                accept: "[a-zA-Z0-9.-_]+",
                check_contact_usernames: true,
            },
        },
        messages: {
            company_name: {
                required: "[required]",
            },
            regno: {
                required: "[required]",
            },
            phoneno: {
                required: "[required]",
                accept: "[invalid]"
            },
            faxno: {
                accept: "[invalid]"
            },
            localdiscount: {
                number: "[invalid]",
                min: "[invalid]",
                max: "[invalid]",
            },
            foreigndiscount: {
                number: "[invalid]",
                min: "[invalid]",
                max: "[invalid]",
            },
            comp_attn: {
                required: "[required]",
            },
            comp_email: {
                required: "[required]",
                email: "[invalid]",
            },
            zipcode: {
            },
            "country_of_residence": "[required]",
            "business_s": "[required]",
            "business_type": "[required]",
            "fname[]": {
                required: "[required]",
                accept: "[invalid]"
            },
            "lname[]": {
                required: "[required]",
                accept: "[invalid]"
            },
            "contactno[]": {
                required: "[required]",
                accept: "[invalid]"
            },
            "mobileno[]": {
                accept: "[invalid]"
            },
            "email_01[]": {
                required: "[required]",
                email: "[invalid]"
            },
            "email_02[]": {
                email: "[invalid]",
            },
            "username[]": {
                required: "[required]",
                accept: "[invalid]"
            },
        },
        
        submitHandler: function(form) {
            if ($('span').hasClass('error')) {
                return false;
            }
            else {
                //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 28/11/2018 AT 3:45PM/////////////
                var self = $("#companyEditForm"),
                button = self.find('input[type="submit"],button'),
                submitValue = button.data('submit-value');
                button.attr('disabled','disabled').val('Please Wait..');
                //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 28/11/2018 AT 3:45PM/////////////
                form.submit();
            }

        },

    });
    
    $("#companyFondaEditForm").validate({
        onkeyup: function(element, event) {
            company_contact_form_clear_errors();
        },
        rules: {
            "country_of_residence": "required",
            "business_s": "required",
            "business_type": "required",
            company_name: {
                required: true,
            },
            regno: {
                required: true,
            },
            phoneno: {
                required: true,
                accept: "[0-9-+.,()]+"
            },
            faxno: {
                valid_fax_number: true,
            },
            localdiscount: {
                number: true,
                min: 0,
                max: 100,
            },
            foreigndiscount: {
                number: true,
                min: 0,
                max: 100,
            },
            comp_attn: {
                required: {
                    depends: function() {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
            },
            comp_email: {
                required: {
                    depends: function() {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
                email: true,
            },
            zipcode: {
                valid_zipcode: true,
            },
            "fname[]": {
                required: {
                    depends: function() {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
            },
            "contactno[]": {
                required: {
                    depends: function() {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
                valid_contact_number: true,
            },
            "mobileno[]": {
                valid_mobile_number: {
                    depends: function() {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
            },
            "email_01[]": {
                required: {
                    depends: function() {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
                email: true,
            },
            "email_02[]": {
                email: {
                    depends: function() {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
            },
            "username[]": {
                required: {
                    depends: function() {
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
                accept: "[a-zA-Z0-9.-_]+",
                check_contact_usernames: true,
            },
        },
        messages: {
            company_name: {
                required: "[required]",
            },
            regno: {
                required: "[required]",
            },
            phoneno: {
                required: "[required]",
                accept: "[invalid]"
            },
            faxno: {
                accept: "[invalid]"
            },
            localdiscount: {
                number: "[invalid]",
                min: "[invalid]",
                max: "[invalid]",
            },
            foreigndiscount: {
                number: "[invalid]",
                min: "[invalid]",
                max: "[invalid]",
            },
            comp_attn: {
                required: "[required]",
            },
            comp_email: {
                required: "[required]",
                email: "[invalid]",
            },
            zipcode: {
            },
            "country_of_residence": "[required]",
            "business_s": "[required]",
            "business_type": "[required]",
            "fname[]": {
                required: "[required]",
                accept: "[invalid]"
            },
            "lname[]": {
                required: "[required]",
                accept: "[invalid]"
            },
            "contactno[]": {
                required: "[required]",
                accept: "[invalid]"
            },
            "mobileno[]": {
                accept: "[invalid]"
            },
            "email_01[]": {
                required: "[required]",
                email: "[invalid]"
            },
            "email_02[]": {
                email: "[invalid]",
            },
            "username[]": {
                required: "[required]",
                accept: "[invalid]"
            },
        },
        
        submitHandler: function(form) {
            if ($('span').hasClass('error')) {
                return false;
            }
            else {
                //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 28/11/2018 AT 3:45PM/////////////
                var self = $("#companyFondaEditForm"),
                button = self.find('input[type="submit"],button'),
                submitValue = button.data('submit-value');
                button.attr('disabled','disabled').val('Please Wait..');
                //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 28/11/2018 AT 3:45PM/////////////
                form.submit();
            }

        },

    });
    
    
    $("#reason_for_deactivation").change(function() {

        var reason_for_deactivation = $("#reason_for_deactivation").val();
        if (reason_for_deactivation == 'OTHERS') {
            $("#other_reason").show();
        }
        else
        {
            $("#other_reason").hide();
        }
    });


});


function validate_company_edit_form() {
    
    var r1;
    if($tenant_id != 'T25') {
        r1 = $("#companyEditForm").valid();
    } else {
        r1 = $("#companyFondaEditForm").valid();
    }


    if (r1 == true) {
        return true;
    } else {alert();
        return false;
    }

}



function company_contact_form_clear_errors() {
    $("div#contact_details select, div#contact_details input")
            .each(function() {
                $('#' + this.id + '_err').text("").removeClass('error');
                $('#' + this.id).removeClass('error');
            });

    return true;
}

function count_array_values(array, value) {
    var counter = 0;
    for (var i = 0; i < array.length; i++) {
        if (array[i] === value)
            counter++;
    }
    return counter;
}



$.validator.addMethod("unique_email_edit",
        function(value, element) {
            current_value = $("#" + element.id + "_current").val();
            if (current_value == 'undefined') {
                return isunique_email(value);
            }
            else
            {
                return isunique_email_edit(value, current_value);
            }
        },
        "[email id exists]"
        );

$.validator.addMethod("unique_username_edit",
        function(value, element) {
            current_value = $("#" + element.id + "_current").val();
            if (current_value == 'undefined') {
                return isunique_username(value);
            }
            else
            {
                return isunique_username_edit(value, current_value);
            }
        },
        "[username exists]"
        );

$.validator.addMethod("unique_regno_edit",
        function(value, element) {
            current_value = $("#" + element.id + "_current").val();
            return isunique_regno_edit(value, current_value);
        },
        "[registration number exists]"
        );

$.validator.addMethod("check_contact_usernames",
        function(value, element) {
            var contact_usernames = [];
            $('input[name="username[]"]').each(function() {
                if (this.id != 'username_') {
                    contact_usernames.push(this.value);
                }
            });

            for (var i = 0; i < contact_usernames.length; i++)
            {
                username_count = count_array_values(contact_usernames, value);
            }
            if (username_count > 1) {
                return false;
            }
            else {
                return true;
            }
        },
        "[duplicate username]"
        );

function reset_all() {
    $(".error").text("").removeClass('error');
    reset_states();
}


function set_deactivate_company_user_id(user_id) {
    $("#deactivate_user_id").val(user_id);
}
function unset_deactivate_company_user_id() {
    $("#deactivate_user_id").val("");
}

function validate_deactivate_company_user_form() {
    retVal = true;

    reason_for_deactivation = $("#reason_for_deactivation").val();
    other_reason_for_deactivation = $("#other_reason_for_deactivation").val();

    if (reason_for_deactivation == "") {
        $("#reason_for_deactivation_err").text("[required]").addClass('error');
        $("#reason_for_deactivation").addClass('error');
        retVal = false;
    } else {
        $("#reason_for_deactivation_err").text("").removeClass('error');
        $("#reason_for_deactivation").removeClass('error');
    }

    if (reason_for_deactivation == "OTHERS") {
        if (other_reason_for_deactivation == "") {
            $("#other_reason_for_deactivation_err").text("[required]").addClass('error');
            $("#other_reason_for_deactivation").addClass('error');
            retVal = false;
        } else {
            if (valid_deactivate_reason(other_reason_for_deactivation) == false)
            {
                $("#other_reason_for_deactivation_err").text("[invalid]").addClass('error');
                $("#other_reason_for_deactivation").addClass('error');
                retVal = false;
            } else {
                $("#other_reason_for_deactivation_err").text("").removeClass('error');
                $("#other_reason_for_deactivation").removeClass('error');
            }
        }
    } else {
        $("#other_reason_for_deactivation_err").text("").removeClass('error');
    }


    if (retVal == true) {
        reason_for_deactivation = $("#reason_for_deactivation").val();
        other_reason_for_deactivation = $("#other_reason_for_deactivation").val();

        $("#deactivate_reason").val(reason_for_deactivation);
        $("#deactivate_other_reason").val(other_reason_for_deactivation);
        //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 28/11/2018 AT 3:45PM/////////////
        var self = $("#companyEditForm"),
        button = self.find('input[type="submit"],button'),
        submitValue = button.data('submit-value');
        button.attr('disabled','disabled').val('Please Wait..');
                //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 28/11/2018 AT 3:45PM/////////////
        $("#companyEditForm").submit();
    }
}

function validate_deactivate_fonda_company_user_form() {
    retVal = true;

    reason_for_deactivation = $("#reason_for_deactivation").val();
    other_reason_for_deactivation = $("#other_reason_for_deactivation").val();

    if (reason_for_deactivation == "") {
        $("#reason_for_deactivation_err").text("[required]").addClass('error');
        $("#reason_for_deactivation").addClass('error');
        retVal = false;
    } else {
        $("#reason_for_deactivation_err").text("").removeClass('error');
        $("#reason_for_deactivation").removeClass('error');
    }

    if (reason_for_deactivation == "OTHERS") {
        if (other_reason_for_deactivation == "") {
            $("#other_reason_for_deactivation_err").text("[required]").addClass('error');
            $("#other_reason_for_deactivation").addClass('error');
            retVal = false;
        } else {
            if (valid_deactivate_reason(other_reason_for_deactivation) == false)
            {
                $("#other_reason_for_deactivation_err").text("[invalid]").addClass('error');
                $("#other_reason_for_deactivation").addClass('error');
                retVal = false;
            } else {
                $("#other_reason_for_deactivation_err").text("").removeClass('error');
                $("#other_reason_for_deactivation").removeClass('error');
            }
        }
    } else {
        $("#other_reason_for_deactivation_err").text("").removeClass('error');
    }


    if (retVal == true) {
        reason_for_deactivation = $("#reason_for_deactivation").val();
        other_reason_for_deactivation = $("#other_reason_for_deactivation").val();

        $("#deactivate_reason").val(reason_for_deactivation);
        $("#deactivate_other_reason").val(other_reason_for_deactivation);
        //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 28/11/2018 AT 3:45PM/////////////
        var self = $("#companyFondaEditForm"),
        button = self.find('input[type="submit"],button'),
        submitValue = button.data('submit-value');
        button.attr('disabled','disabled').val('Please Wait..');
                //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 28/11/2018 AT 3:45PM/////////////
        $("#companyFondaEditForm").submit();
    }
}


function check_registration_number(value, element_id, err_element_id) {
    if (value != '') {
        current_value = $("#" + element_id + "_current").val();
        var regnum_retval = isunique_regno_edit(value, current_value);
        if (regnum_retval == false) {
            $('#' + err_element_id).find('span').remove();
            $("#" + err_element_id).append('<span class="error">&nbsp;&nbsp;[registration number exists]</span>');
        }
        else {
            $('#' + err_element_id).find('span').remove();
        }
    }
    else {
        $('#' + err_element_id).find('span').remove();
    }
}




function check_contact_reg_email(value, element_id, err_element_id) {
    if (value != '') {
        current_value = $("#" + element_id + "_current").val();
        if (current_value == 'undefined') {
            var regnum_retval = isunique_email(value);
        }
        else
        {
            var regnum_retval = isunique_email_edit(value, current_value);
        }

        if (regnum_retval == false) {
            $('#' + err_element_id).find('span').remove();
            $("#" + err_element_id).append('<span class="error"><br />&nbsp;&nbsp;[email id exists]</span>');
        }
        else {
            $('#' + err_element_id).find('span').remove();
        }
    }
    else {
        $('#' + err_element_id).find('span').remove();
    }
}



function check_username(value, element_id, err_element_id) {
    if (value != '') {
        var regnum_retval = isunique_username(value);
        if (regnum_retval == false) {
            $('#' + err_element_id).find('span').remove();
            $("#" + err_element_id).append('<span class="error"><br />&nbsp;&nbsp;[username exists]</span>');
        }
        else {
            $('#' + err_element_id).find('span').remove();
        }
    }
    else {
        $('#' + err_element_id).find('span').remove();
    }
}


function reset_states() {
    curr_country = $("#current_company_country").val();
    curr_state = $("#current_pers_states").val();
    $.post(baseurl + "internal_user/get_states_json", {country_param: curr_country}, function(data) {
        json_data = $.parseJSON(data);
        $pers_states_select = $("#pers_states");
        $pers_states_select.html('<option value="">Select</option>');
        selected_attr_value = '';
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
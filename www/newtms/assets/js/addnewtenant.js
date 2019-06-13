/** 
 * This js file included in add new tenant page
 */
$(document).ready(function() {
    var check = 0;
    $('#country').change(function() {
        var country = $(this).val();
        $.ajax({
            url: baseurl + 'manage_tenant/get_states',
            type: 'post',
            data: {country: country},
            dataType: 'JSON',
            async: false,
            success: function(res) {
                var state = $('#state');
                state.html('<option value="">Select</option>');
                $.each(res, function(i, item) {
                    state.append('<option value="' + item['parameter_id'] + '">' + item['category_name'] + '</option>');
                });
            }
        })
    });
    function isunique_email(email) {
        var output;
        $.ajax({
            url: baseurl + 'manage_tenant/check_email',
            type: 'post',
            data: {email: email},
            async: false,
            success: function(res) {
                output = false;
                if (res == 0) {
                    output = true;
                }
            }
        })
        return output;
    }
    function isunique_name(name) {
        var output;
        $.ajax({
            url: baseurl + 'manage_tenant/check_tenant_name',
            type: 'post',
            data: {name: name},
            async: false,
            success: function(res) {
                output = false;
                if (res == 0) {
                    output = true;
                }
            }
        })
        return output;
    }
    $("#contact_num").keydown(function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    $("#acti_start_date, #acti_end_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "+0:+100",
    });
    $('#tenant_form').submit(function() {
        check = 1;
        return validate(true);
    });
    $('#tenant_form select,#tenant_form input,#tenant_form textarea').change(function() {
        if (check == 1) {
            return validate(false);
        }
    });
    function validate(retval) {
        var tenant_name = $('#tenant_name').val().trim();
        if (tenant_name.length == 0) {
            disp_err('#tenant_name');
            retval = false;
        } else if (isunique_name(tenant_name) == false) {
            disp_err('#tenant_name', '[name already exists.]');
            retval = false;
        } else {
            remove_err('#tenant_name');
        }
        var address = $('#address').val().trim();
        if (address.length == 0) {
            disp_err('#address');
            retval = false;
        } else {
            remove_err('#address');
        }
        var email = $('#email').val().trim();
        if (email.length == 0) {
            disp_err('#email');
            retval = false;
        } else if (valid_email_address(email) == false) {
            disp_err('#email', '[invalid]');
            retval = false;
        } else {
            remove_err('#email');
        }
        var logo = $('#userfile').val();
        if (logo.length == 0) {
            $('#image_err').addClass('error').html('[required]');
            retval = false;
        } else {
            $('#image_err').removeClass('error').html('');
        }
        
        var country = $('#country').val().trim();
        if (country.length == 0) {
            disp_err('#country');
            retval = false;
        } else {
            remove_err('#country');
        }
        
        var contact_num = $('#contact_num').val().trim();
        if (contact_num.length == 0) {
            disp_err('#contact_num');
            retval = false;
        } else {
            remove_err('#contact_num');
        }
        var acti_start_date = $('#acti_start_date').val().trim();
        if (acti_start_date.length == 0) {
            disp_err('#acti_start_date');
            retval = false;
        } else {
            remove_err('#acti_start_date');
        }
        var inv_name = $('#inv_name').val().trim();
        if (inv_name.length == 0) {
            disp_err('#inv_name');
            retval = false;
        } else {
            remove_err('#inv_name');
        }
        var currency = $('#currency').val().trim();
        if (currency.length == 0) {
            disp_err('#currency');
            retval = false;
        } else {
            remove_err('#currency');
        }
        var country_use = $('#country_use').val().trim();
        if (country_use.length == 0) {
            disp_err('#country_use');
            retval = false;
        } else {
            remove_err('#country_use');
        }
        var website = $('#website').val().trim();
        remove_err('#website');
        if (website.length > 0) {
             var pattern = /(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?/
            if (!pattern.test(website)) {
                disp_err('#website', '[invalid, eg: http://www.tms.com]');
                retval = false;
            }
        }
        var paypal_email = $('#paypal_email').val().trim();
        remove_err('#paypal_email');
        if (paypal_email.length > 0) {
            if (valid_email_address(paypal_email) == false) {
                disp_err('#paypal_email', '[invalid]');
                retval = false;
            }
        }
        var director_name = $('#director_name').val().trim();
        if (director_name.length == 0) {
            disp_err('#director_name');
            retval = false;
        } else {
            remove_err('#director_name');
        }
        var contact_name = $('#contact_name').val().trim();
        if (contact_name.length == 0) {
            disp_err('#contact_name');
            retval = false;
        } else {
            remove_err('#contact_name');
        }
        
        var copyright = $('#copyright').val().trim();
        if (copyright.length == 0) {
            disp_err('#copyright');
            retval = false;
        } else {
            remove_err('#copyright');
        }

        return retval;
    }
    function disp_err($id, $text) {
        $text = typeof $text !== 'undefined' ? $text : '[required]';
        $($id).addClass('error');
        $($id + '_err').addClass('error').addClass('error3').html($text);
    }
    function remove_err($id) {
        $($id).removeClass('error');
        $($id + '_err').removeClass('error').removeClass('error3').text('');
    }
});
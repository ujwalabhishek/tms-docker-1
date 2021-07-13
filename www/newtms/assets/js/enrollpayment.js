/* 
 * This js file included in enrollment page.
 */
$(document).ready(function() {
    $(document).on('click', '.close-modal,.jquery-modal', function(e) {
        $class = $('#class').val();
        $search_select = $('#search_select').val();
        if ($search_select == 1) {
            $user_id = $('#taxcode_id').val();
        } else {
            $user_id = $('#trainee_id').val();
        }
        $account_type = $('#account_type').val();
        if ($account_type == 'individual') {
            location.replace($baseurl + 'class_trainee/update_enroll_message/' + $user_id + '/' + $class);
        } else {
            $user_id = '';
            $('.trainee_selected:checked').each(function() {
                $val = $(this).val();
                $user_id += $val + '-';
            });
            location.replace($baseurl + 'class_trainee/update_enroll_message/' + $user_id + '/' + $class);
        }
    });
    $('.pay_time').change(function() {
        $val = $('.pay_time:checked').val();
        if ($val == 2) {
            $('.rowdim').hide();
            $('#row_dim').hide();
            $disc_amt = $('#disc_amt').val();
            $disc_rate_hidden = $('#disc_rate_hidden').val();            
            $subsidy_amount_value = $('#subsidy_amount').val();            
            $('.retake_bypass_div input[name!="pay_time"], .retake_bypass_div select[name!="subsidy_type"]').val('');
            $('#disc_amt').val($disc_amt);
            $('#subsidy_amount').val($subsidy_amount_value);            
            $('#disc_rate_hidden').val($disc_rate_hidden);
            $('#subsidy_per').trigger('change');
            $('.save_book').show();
            $('.save_enroll').hide();
        } else {
            $('.rowdim').show();
            $('.save_book').hide();
            $('.save_enroll').show();
        }
    });
    $('#subsidy_amount').change(function() {
        $subsidy = $(this).val();
        $disc_amt = $('#disc_amt').val();
        $.ajax({
            url: $baseurl + 'class_trainee/calculate_gst_for_subsidy',
            type: 'post',
            data: {gst_onoff: $gst_on, subsidy_after_before: $gst_subsidy_afterbefore, feesdue: $gst_feesdue,
                subsidy: $subsidy, gst_rate: $gst_gstrate, class_fees: $class_fees, discount_amount: $disc_amt},
            dataType: 'json',
            success: function(i) {
                if (i.label != '') {
                    label_alert = false;
                    $('#subsidy_amount').val('');
                    $('#subsidy_per').val('');
                    $('.subsidy_amount').html('0.00');
                    $('#subsidy_type').val('');
                    $('#subsidy_amount_label').text('0.00');
                    $('#disc_amt').trigger('change');
                    alert(i.label);
                } else {
                    $('.net_due').html(i.amount);
                    $('#subsidy_per').val(i.subsidy_per);
                    $subsidy = ($subsidy == '') ? 0 : $subsidy;
                    $('.subsidy_amount').html(parseFloat($subsidy).toFixed(2));
                    $('.gst_amount').html(i.gst_amount);
                    $amount_check = i.amount;

                }
            }
        });
    })
    $('.trainee_selected').change(function() {
        trigger_company_net_calculation('', '');
    });
    $('.trainee_subsidy_amount').change(function() {
        $user_selected = $(this).data('user');
        trigger_company_net_calculation($user_selected, '');
    });
    $('.trainee_subsidy_per').change(function() {
        $user_selected = $(this).data('user');
        trigger_company_net_calculation('', $user_selected);
    });
    function check_company_disable_div() {
        $enrol_val = $('.enrollment_type:checked').val();
        $val = $('.payment_retake:checked').val();
        if ($enrol_val == 1) {
            if ($val == 1) {
                $('.comp_payment_due').show();
                $('.trainee_selected:checked').each(function() {
                    $val = $(this).val();
                    $('.trainee_tg[name="trainee_tg[' + $val + ']"]').removeAttr('disabled');
                    $sub_amount = $('.trainee_subsidy_amount[name="trainee_subsidy_amount[' + $val + ']"]').removeAttr('disabled');
                    $sub_pers = $('.trainee_subsidy_per[name="trainee_subsidy_per[' + $val + ']"]').removeAttr('disabled');
                    $sub_date = $('.trainee_subsidy_date[name="trainee_subsidy_date[' + $val + ']"]').removeAttr('disabled');
                });
            } else if ($val == 2) {
                $('.comp_payment_due').hide();
                $('.trainee_selected:checked').each(function() {
                    $val = $(this).val();
                    $('.trainee_tg[name="trainee_tg[' + $val + ']"]').val('').attr('disabled', 'disabled');
                    $sub_amount = $('.trainee_subsidy_amount[name="trainee_subsidy_amount[' + $val + ']"]').val('').attr('disabled', 'disabled');
                    $sub_pers = $('.trainee_subsidy_per[name="trainee_subsidy_per[' + $val + ']"]').val('').attr('disabled', 'disabled');
                    $sub_date = $('.trainee_subsidy_date[name="trainee_subsidy_date[' + $val + ']"]').val('').attr('disabled', 'disabled');
                });
                trigger_company_net_calculation('', '');
            }
        }
        else if ($enrol_val == 2) {
            $('.comp_payment_due').show();
            $('.trainee_selected:checked').each(function() {
                $val = $(this).val();
                $('.trainee_tg[name="trainee_tg[' + $val + ']"]').removeAttr('disabled');
                $sub_amount = $('.trainee_subsidy_amount[name="trainee_subsidy_amount[' + $val + ']"]').removeAttr('disabled');
                $sub_pers = $('.trainee_subsidy_per[name="trainee_subsidy_per[' + $val + ']"]').removeAttr('disabled');
                $sub_date = $('.trainee_subsidy_date[name="trainee_subsidy_date[' + $val + ']"]').removeAttr('disabled');
            })
        }
    }
    $('#comp_disc_amt').change(function() {
        $disc_amt = $('#comp_disc_amt').val();
        if (parseFloat($disc_amt) >= $class_fees) { 
            alert("Discount amount should not  equal/greater than total invoice fees");
            $disc_rate = $('#comp_disc_rate_hidden').val();
            $disc_amt = (parseFloat($disc_rate) * parseFloat($class_fees)) / 100;
            $('#comp_disc_amt').val(parseFloat($disc_amt).toFixed(2));
            return false;
        } else {            
            trigger_company_net_calculation('', '');
        }

    });
    function trigger_company_net_calculation(amt, per) {        
        $discount_changed = 'N';
        $arr = get_company_data(amt, per);
        $disc_amt = $('#comp_disc_amt').val();
        if(parseFloat($disc_amt).toFixed(2) != parseFloat($ori_comp_disc_amt).toFixed(2)) {
            $discount_changed = 'Y';
        } 
        $.ajax({
            url: $baseurl + 'class_trainee/get_company_net_calculation',
            type: 'post',
            data: {company: $('#company').val(), data: $arr, discount: $('#comp_disc_amt').val(), 
                class: $('#class').val(), amt: amt, per: per,discount_changed : $discount_changed},
            dataType: 'json',
            beforeSend: function(i) {
            },
            success: function(i) {
                if (i.error.length == 0) {
                    $('#comp_disc_rate').html(parseFloat(i.discount_rate).toFixed(2));
                    $('#comp_disc_rate_hidden').val(parseFloat(i.discount_rate).toFixed(4));
                    $('.company_net_due').html(parseFloat(i.company_net).toFixed(2));
                    $('.company_subsidy_amount').html(parseFloat(i.company_subsidy).toFixed(2));
                    $('.company_gst_amount').html(parseFloat(i.company_gst).toFixed(2));
                    if ((i.percentage == '') && (i.amount == '')) {
                        if (amt != '') {
                            $('.trainee_subsidy_per[name="trainee_subsidy_per[' + amt + ']"]').val('');
                            $('.trainee_subsidy_amount[name="trainee_subsidy_amount[' + amt + ']"]').val('');
                            $('.trainee_subsidy_date[name="trainee_subsidy_date[' + amt + ']"]').val('');

                            remove_err('#trainee_subsidy_amount_' + amt);
                        }
                        if (per != '') {
                            $('.trainee_subsidy_per[name="trainee_subsidy_per[' + per + ']"]').val('');
                            $('.trainee_subsidy_amount[name="trainee_subsidy_amount[' + per + ']"]').val('');
                            $('.trainee_subsidy_date[name="trainee_subsidy_date[' + per + ']"]').val('');

                            remove_err('#trainee_subsidy_amount_' + amt);
                        }
                        return false;
                    }
                    if (amt != '') {
                        $('.trainee_subsidy_per[name="trainee_subsidy_per[' + amt + ']"]').val(parseFloat(i.percentage).toFixed(2));
                    }
                    if (per != '') {
                        $('.trainee_subsidy_amount[name="trainee_subsidy_amount[' + per + ']"]').val(parseFloat(i.amount).toFixed(2));
                    }
                } else {
                    alert('NEGATIVE Total Fees Due NOT ALLOWED. Please correct Discount AND/ OR Subsidy Amounts.');
                    $disc_rate = $('#comp_disc_rate_hidden').val();
                    $disc_amt = (parseFloat($disc_rate) * parseFloat($class_fees)) / 100;
                    $('#comp_disc_amt').val(parseFloat($disc_amt).toFixed(2));
                    $('#subsidy_amount_label_'+amt).text("0.00");
                    $('.comp_subsidy_type[name="subsidy_type['+amt+']"]').val("");                    
                    if (amt != '') {
                        $('.trainee_subsidy_per[name="trainee_subsidy_per[' + amt + ']"]').val('');
                        $('.trainee_subsidy_amount[name="trainee_subsidy_amount[' + amt + ']"]').val('');
                        $('.trainee_subsidy_date[name="trainee_subsidy_date[' + amt + ']"]').val('');
                    }
                    if (per != '') {
                        $('.trainee_subsidy_per[name="trainee_subsidy_per[' + per + ']"]').val('');
                        $('.trainee_subsidy_amount[name="trainee_subsidy_amount[' + per + ']"]').val('');
                        $('.trainee_subsidy_date[name="trainee_subsidy_date[' + per + ']"]').val('');
                    }
                    trigger_company_net_calculation('', '');
                    return false;
                }
            }
        });
    }
    $("#chq_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '+0:+100',
        onClose: function() {
            $(this).trigger("change");
        }
    });
    $("#recd_on, #subsidy_recd_on").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '+0:+100',
        maxDate: 0,
        onClose: function() {
            $(this).trigger("change");
        }
    });
    $('.trainee_subsidy_date').datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '+0:+100',
        maxDate: 0,
        onClose: function() {
            $(this).trigger("change");
        }
    });
    $('.reset_form').click(function() {
        window.location.replace('add_new_enrol');
    });
    $('#bank_name,#tg_number,#chq_num, .trainee_tg').keyup(function() {
        $(this).val(this.value.toUpperCase());
    });
    $('#mode_of_payment').change(function() {
        $val = $(this).val();
        if ($val == "CHQ") {
            $('#row_dim').show();
        } else {
            $('#row_dim').hide();
        }
        if (validate_check == 1) {
            return form_validate(false);
        }
    });
    $('#disc_amt').change(function() {
        $disc_amt = $('#disc_amt').val();
        if (parseFloat($disc_amt) >= $class_fees) {
            alert("Discount amount should be less than the class fees");
            $disc_rate = $('#disc_rate_hidden').val();
            $disc_amt = (parseFloat($disc_rate) * parseFloat($class_fees)) / 100;
            $('#disc_amt').val(parseFloat($disc_amt).toFixed(2));
            return false;
        } else {
            $subsidy_per = $('#subsidy_per').val();
            $subsidy = $('#subsidy_amount').val();
            $.ajax({
                url: $baseurl + 'class_trainee/calculate_discount_percentage',
                type: 'post',
                data: {gst_onoff: $gst_on, subsidy_after_before: $gst_subsidy_afterbefore, feesdue: $gst_feesdue,
                    subsidy: $subsidy, gst_rate: $gst_gstrate, class_fees: $class_fees, discount_amount: $disc_amt},
                dataType: 'json',
                success: function(i) {
                    if (i.label != '') {
                        label_alert = false;
                        alert(i.label);
                        $disc_rate = $('#disc_rate_hidden').val();
                        $disc_amt = (parseFloat($disc_rate) * parseFloat($class_fees)) / 100;
                        $('#disc_amt').val(parseFloat($disc_amt).toFixed(2));
                    } else {
                        $('.net_due').html(parseFloat(i.amount).toFixed(2));
                        $('#disc_rate').html(parseFloat(i.disc_rate).toFixed(2));
                        $('#disc_rate_hidden').val(parseFloat(i.disc_rate).toFixed(4));
                        $('.gst_amount').html(parseFloat(i.gst_amount).toFixed(2));
                        $('#subsidy_per').val(parseFloat(i.subsidy_per).toFixed(2));
                        $amount_check = i.amount;
                    }
                }
            });
        }

    });
    $('#subsidy_type').change(function() {
        $subsidy_type = $(this).val();
        $disc_amt = $('#disc_amt').val();        
        $.ajax({
            url: $baseurl + 'class_trainee/get_subsidy_amount',
            type: 'post',
            data: {subsidy_type:$subsidy_type},
            dataType: 'json',
            success: function(res) {
                res = res == 0?'0.00':res;
                $('#subsidy_amount_label').text(res);
                $('#subsidy_amount').val(res);
                $('#subsidy_amount').trigger('change');                
            }
        });
    });    
    $('.comp_subsidy_type').change(function() {
        $subsidy_type = $(this).val();        
        $user_selected = $(this).data('user');
        $.ajax({
            url: $baseurl + 'class_trainee/get_subsidy_amount',
            type: 'post',
            data: {subsidy_type:$subsidy_type},
            dataType: 'json',
            success: function(res) {
                res = res == 0?'0.00':res;
                $('#subsidy_amount_label_'+$user_selected).text(res);
                $('#trainee_subsidy_amount_'+$user_selected).val(res);
                $('#trainee_subsidy_amount_'+$user_selected).trigger('change');                
            }
        });
    });
    $("#subsidy_per,#subsidy_amount, #amount_rcd, #comp_disc_amt, #disc_amt, .trainee_subsidy_amount, .trainee_subsidy_per").keydown(function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    $('.enrollment_type').change(function() {
        $account_type = $('#account_type').val();
        $val = $('.enrollment_type:checked').val();
        if ($val == 1) {
            $('.retake_span').show();
        } else {
            $('.retake_span').hide();
        }
        if ($account_type == 'company') {
            check_company_disable_div();
        } else if ($account_type == 'individual') {
            check_retake_bypass_div();
        }
    });
    $('.payment_retake').change(function() {
        $account_type = $('#account_type').val();
        if ($account_type == 'company') {
            check_company_disable_div();
        } else if ($account_type == 'individual') {
            check_retake_bypass_div();
        }
    });
    var validate_check = 0;
    $('.save_enroll').click(function() {
        validate_check = 1;
        return form_validate(true);
    });
    $('.save_book').click(function() {
        validate_check = 1;
        return form_validate(true);
    });
    $('.col-md-10 input, .col-md-10 select').change(function() {
        if (validate_check == 1) {
            return form_validate(false);
        }
    });
});
function check_retake_bypass_div() {
    $enrol_val = $('.enrollment_type:checked').val();
    $val = $('.payment_retake:checked').val();
    if ($enrol_val == 1) {
        if ($val == 1) {
            $('.retake_bypass_div').show();
            $('.save_enroll').show();
            $('.save_book').hide();
        } else if ($val == 2) {
            $('.retake_bypass_div').hide();
            $('.save_enroll').hide();
            $('.save_book').show();
            $('.retake_bypass_div input[name!="pay_time"][name!="disc_rate"][name!="disc_rate_hidden"], .retake_bypass_div select').val('');
            $('#subsidy_per').trigger('change');
        }
    }
    else if ($enrol_val == 2) {
        $('.retake_bypass_div').show();
        $('.save_enroll').show();
        $('.save_book').hide();
    }
}
function company_data_validate($retval) {
    $('.trainee_selected:checked').each(function() {
        $val = $(this).val();
        $sub_amount = $('.trainee_subsidy_amount[name="trainee_subsidy_amount[' + $val + ']"]').val();
        $sub_pers = $('.trainee_subsidy_per[name="trainee_subsidy_per[' + $val + ']"]').val();
        $sub_date = $('.trainee_subsidy_date[name="trainee_subsidy_date[' + $val + ']"]').val();
        if (parseFloat($sub_amount) > 0) {
            if ($sub_date.length == 0) {
                $retval = false;
                disp_err('#subsidydate_' + $val);
            } else {
                remove_err('#subsidydate_' + $val);
            }
        }else {
                remove_err('#subsidydate_' + $val);
        }
        if ($sub_date.length > 0) {
            if (parseFloat($sub_amount) == 0 || $sub_amount == '') {                
                $retval = false;
                disp_err('#trainee_subsidy_amount_' + $val);
            } else {
                remove_err('#trainee_subsidy_amount_' + $val);
            }
        }


    })
    return $retval;
}
function get_company_data(amt, per) {
    $arr = [];
    $('.trainee_selected:checked').each(function() {
        $val = $(this).val();
        if (per == $val) {
            $sub_amount = '';
        } else {
            $sub_amount = $('.trainee_subsidy_amount[name="trainee_subsidy_amount[' + $val + ']"]').val();
        }
        if (amt == $val) {
            $sub_type = '';
        } else {
            $sub_type = $('.comp_subsidy_type[name="subsidy_type[' + $val + ']"]').val();
        }
        $sub_date = $('.trainee_subsidy_date[name="trainee_subsidy_date[' + $val + ']"]').val();
        $tg = $('.trainee_tg[name="trainee_tg[' + $val + ']"]').val();
        $data = {};
        $data.user_id = $val;
        $data.subsidy_date = $sub_date;
        $data.subsidy_type = $sub_type; 
        $data.subsidy_amount = $sub_amount;
        $data.tg = $tg;
        $arr.push($data);
    })
    return $arr;
}
function form_validate($retval) {
    $enroll_type = $('.enrollment_type:checked').length;
    if ($enroll_type == 0) {
        $retval = false;
        disp_err('#enrollment_type');
        $('html,body').animate({scrollTop: $('#enrollpaymentform').offset().top}, "slow");
    } else {
        remove_err('#enrollment_type');
    }
    $account_type = $('#account_type').val();
    if ($account_type == 'individual') {
        $enrollment_type = $('.enrollment_type:checked').val();
        if ($enrollment_type == 1) {
            $payment_enrol = $('#payment_enrol').val();
            $payment_retake = $('.payment_retake:checked').val();
            if (($payment_enrol == 'PAENROL') && ($payment_retake == 1) && ($('.pay_time:checked').val() == 1)) {
                return validate_required($retval, 0, '');
            } else if (($payment_enrol == 'PDENROL') && ($payment_retake == 1)) {
                return validate_required($retval, 0, '');
            }
            ///////added by shubhranshu to prevent multiple clicks////////////////
            if(validate_required($retval, 0, '')){
                $('.push_right').html('<button class="btn btn-primary" type="button">Please Wait..</button>');
                var self = $('.push_right'),
                button = self.find('input[type="button"],button');
                button.attr('disabled','disabled').html('Please Wait..');
            ///////added by shubhranshu to prevent multiple clicks////////////////
            }
            clear_required($retval, '');
        } else if ($enrollment_type == 2) {
            $payment_enrol = $('#payment_enrol').val();
            if (($payment_enrol == 'PDENROL') || ($('.pay_time:checked').val() == 1)) {
                return validate_required($retval, 0, '');
            }
            ///////added by shubhranshu to prevent multiple clicks////////////////
            if(validate_required($retval, 0, '')){
                $('.push_right').html('<button class="btn btn-primary" type="button">Please Wait..</button>');
                var self = $('.push_right'),
                button = self.find('input[type="button"],button');
                button.attr('disabled','disabled').html('Please Wait..');
            ///////added by shubhranshu to prevent multiple clicks////////////////
            }
            clear_required($retval, '');
        }
    } else {
        $retval = company_data_validate($retval);
        if ($retval == true) {
            $data = get_company_data('', '');
            ///////added by shubhranshu to prevent multiple clicks////////////////
            $('.push_right').html('<button class="btn btn-primary" type="button">Please Wait..</button>');
            var self = $('.push_right'),
            button = self.find('input[type="button"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            ///////added by shubhranshu to prevent multiple clicks////////////////
            trigger_company_ajax($data);
        } else {
            return $retval;
        }
    }
}
function clear_required($retval, $msg) {
    remove_err('#subsidy_recd_on');
    remove_err('#recd_on');
    remove_err('#amount_rcd');
    remove_err('#chq_num');
    remove_err('#chq_date');
    remove_err('#bank_name');
    if ($retval == true) {
        $mode_of_payment = $('#mode_of_payment').val();
        if ($mode_of_payment.length == 0) {
            remove_err('#mode_of_payment');
            trigger_ajax();
        } else {
            validate_required($retval, 1, $msg)
        }
    }
}
function validate_required($retval, $chk, $msg) {
    $subsidy_amount = $('#subsidy_amount').val();
      if (parseFloat($subsidy_amount) > 0) {
        $subsidy_recd_on = $('#subsidy_recd_on').val();
        if ($subsidy_recd_on.length == 0) {
            $retval = false;
            disp_err('#subsidy_recd_on');
        } else {
            remove_err('#subsidy_recd_on');
        }
    }else{
        remove_err('#subsidy_recd_on');
    }
    $mode_of_payment = $('#mode_of_payment').val();
    if ($mode_of_payment.length == 0) {
        $retval = false;
        disp_err('#mode_of_payment');
    } else {
        remove_err('#mode_of_payment');
    }
    if ($mode_of_payment == 'CASH' || $mode_of_payment == "NETS") {
        $recd_on = $('#recd_on').val();
        if ($recd_on.length == 0) {
            $retval = false;
            disp_err('#recd_on');
        } else {
            remove_err('#recd_on');
        }
        $amount_rcd = $.trim($('#amount_rcd').val());
        if ($amount_rcd.length == 0) {
            $retval = false;
            disp_err('#amount_rcd');
        } else if (isNaN($amount_rcd)) {
            disp_err('#amount_rcd', '[Invalid]');
            $retval = false;
        } else if (parseFloat($('.net_due').text()) != parseFloat($amount_rcd)) {
            $retval = false;
            disp_err('#amount_rcd', '[amount recd. not equal to amount due]');
        } else {
            remove_err('#amount_rcd');
        }
    } else if ($mode_of_payment == 'CHQ') {
        $recd_on = $('#recd_on').val();
        if ($recd_on.length == 0) {
            $retval = false;
            disp_err('#recd_on');
        } else {
            remove_err('#recd_on');
        }
        $amount_rcd = $.trim($('#amount_rcd').val());
        if ($amount_rcd.length == 0) {
            $retval = false;
            disp_err('#amount_rcd');
        } else if (isNaN($amount_rcd)) {
            disp_err('#amount_rcd', '[Invalid]');
            $retval = false;
        } else if (parseFloat($('.net_due').text()) != parseFloat($amount_rcd)) {
            $retval = false;
            disp_err('#amount_rcd', '[amount recd. not equal to amount due]');
        } else {
            remove_err('#amount_rcd');
        }
        $chq_num = $('#chq_num').val();
        if ($chq_num.length == 0) {
            $retval = false;
            disp_err('#chq_num');
        } else {
            remove_err('#chq_num');
        }
        $chq_date = $('#chq_date').val();
        if ($chq_date.length == 0) {
            $retval = false;
            disp_err('#chq_date');
        } else {
            remove_err('#chq_date');
        }
        $bank_name = $('#bank_name').val();
        if ($bank_name.length == 0) {
            $retval = false;
            disp_err('#bank_name');
        } else {
            remove_err('#bank_name');
        }
    }
    if ($retval == true) {
        return trigger_ajax();
    }
    return $retval;
}
function disp_err($id, $text) {
    $text = typeof $text !== 'undefined' ? $text : '[required]';
    $($id).addClass('error');
    $($id + '_err').addClass('error').addClass('error_text').html($text);
}
function remove_err($id) {
    $($id).removeClass('error');
    $($id + '_err').removeClass('error').text('');
}
function trigger_company_ajax(data) {
    $discount_changed = 'N';
    $arr = data;
    $payment_retake = $('.payment_retake:checked').val();
    $course = $('#course').val();
    $class = $('#class').val();
    $company = $('#company').val();
    $discount = $('#discount').val();
    $enrollment_type = $('.enrollment_type:checked').val();
    $salesexec = $('#salesexec').val();
    $check_user_id = '';
    $('.trainee_selected:checked').each(function() {
        $val = $(this).val();
        $check_user_id += $val;
    });
    if (($enrollment_type == 2) || (($enrollment_type == 1) && ($payment_retake == 1))) {
        if (parseFloat($('.company_net_due').html()) <= 0) {
            alert('Please ensure total fees due is greater than zero.');
            return false;
        }
    }
    if ($check_user_id.length == 0) {
        alert('Select atleast one trainee to enroll.');
        return false;
    } else {
        $disc_rate = $('#comp_disc_rate_hidden').val();
        $disc_amt = $('#comp_disc_amt').val();
        $disc_label = $('#comp_disc_label').html();
        if(parseFloat($disc_amt).toFixed(2) != parseFloat($ori_comp_disc_amt).toFixed(2)) {
            $discount_changed = 'Y';
        } 
       $.ajax({
            url: $baseurl + 'class_trainee/company_enrollment',
            type: 'post',
            data: {payment_retake: $payment_retake, company: $company, data: $arr, course: $course,
                class: $class, discount_rate: $disc_rate, discount_amount: $disc_amt, discount_label: $disc_label, 
				enrollment_type: $enrollment_type, salesexec: $salesexec,discount_changed:$discount_changed},
            dataType: 'json',
            beforeSend: function(i) {
            },
            success: function(i) {                
                if(i.status == false) {
                    var error_status = typeof i.error_status !== 'undefined' ? i.error_status : '';
                    location.replace($baseurl + 'class_trainee/transaction_fail?err='+error_status);                    
                } else {
                    $('.book_ack_text').html(i.data);
                    $('.book_ackn_print').attr('href', $baseurl + '/class_trainee/booking_acknowledge_company_pdf/' + i.trainee_id + '/' + $class + '/' + $company);
                    $('#ex5').modal();
                }
            }
        });
    }
}
function trigger_ajax() {
    $discount_changed = 'N';
    $course = $('#course').val();
    $class = $('#class').val();
    $search_select = $('#search_select').val();
    if ($search_select == 1) {
        $user_id = $('#taxcode_id').val();
    } else {
        $user_id = $('#trainee_id').val();
    }

    $enrollment_type = $('.enrollment_type:checked').val();
    $payment_retake = $('.payment_retake:checked').val();
    $disc_rate = $('#disc_rate_hidden').val();    
    $disc_amt = $('#disc_amt').val();
    $salesexec = $('#salesexec').val();
    $tg_number = $('#tg_number').val();
    $subsidy_amount = $('#subsidy_amount').val();
    $subsidy_type = $('#subsidy_type').val();
    $subsidy_recd_on = $('#subsidy_recd_on').val();
    $mode_of_payment = $('#mode_of_payment').val();
    $recd_on = $('#recd_on').val();
    $amount_rcd = $('#amount_rcd').val();
    $chq_num = $('#chq_num').val();
    $chq_date = $('#chq_date').val();
    $bank_name = $('#bank_name').val();
   if(parseFloat($disc_amt).toFixed(2) != parseFloat($ori_discount_amount).toFixed(2)) {
        $discount_changed = 'Y';
    }    
    if (($enrollment_type == 2) || (($enrollment_type == 1) && ($payment_retake == 1))) {
        if (parseFloat($('.net_due').html()) <= 0) {
            alert('Please ensure total fees due is greater than zero.');
            return false;
        }
    }
    $.ajax({
        url: $baseurl + 'class_trainee/individual_enrollment',
        type: 'post',
        dataType: 'json',
        data: {
            course: $course, class: $class, user_id: $user_id, enrollment_type: $enrollment_type,
            payment_retake: $payment_retake, disc_rate: $disc_rate, disc_amt: $disc_amt,
            salesexec: $salesexec, tg_number: $tg_number, subsidy_amount: $subsidy_amount,
            subsidy_type: $subsidy_type,
            subsidy_recd_on: $subsidy_recd_on, mode_of_payment: $mode_of_payment, recd_on: $recd_on,
            amount_rcd: $amount_rcd, chq_num: $chq_num, chq_date: $chq_date, bank_name: $bank_name,
            discount_changed:$discount_changed
        },
        success: function(i) {
            if (i.result == false) {
                var error_status = typeof i.error_status !== 'undefined' ? i.error_status : '';
                location.replace($baseurl + 'class_trainee/transaction_fail?err='+error_status);
            } else {
                if ($mode_of_payment == '') {
                    $('.book_ack_text').html(i.data);
                    $('.book_ackn_print').attr('href', $baseurl + '/class_trainee/booking_acknowledge_pdf/' + $user_id + '/' + $class);
                    $('#ex5').modal();
                } else {
                    $('.logo').attr('src', $baseurl + 'logos/' + i.data.tenant.Logo);
                    $('.r_tenant_name').html(i.data.tenant.tenant_name);
                    $('.r_tenant_address').html(i.data.tenant.tenant_address);
                    $('.r_tenant_phone').html('Phone: ' + i.data.tenant.tenant_contact_num);
                    $('.r_tenant_email').html('Email Id: ' + i.data.tenant.tenant_email_id);
                    $('.r_class').html(i.data.class.class_name);
                    $('.r_course').html(i.data.courses.crse_name);
                    $('.r_certilevel').html(i.data.courselevel);
                    $('.r_course_manager').html(i.data.coursemanager);
                    $('.r_class_loc').html(i.data.classloc);
                    $('.r_class_start').html(i.data.class_start);
                    $('.r_invoice_no').html(i.data.invoice.recd_on_year + '' + i.data.invoice.invoice_id);
                    $('.r_invoice_recd').html(i.data.invoice.recd_on);
                    var amount = parseFloat(i.data.invoice.amount_recd).toFixed(2);
                    $('.r_invoice_amount_recd').html(amount);
                    var chq_details ="";
                    if(i.data.invoice.mode_of_pymnt == 'Cheque') {
                        chq_details = " ( Cheque No: " + i.data.invoice.cheque_number+", Cheque Date: "+i.data.invoice.cheque_date+")";
                    }
                    $('.r_invoice_mode').html(i.data.invoice.mode_of_pymnt + chq_details);
                    $('.r_invoice_trainee').html(i.data.trainee);
					
					if(i.data.att_status == '1') {
						$('.payment_recd_href').attr('href', $baseurl + 'class_trainee/export_payment_receipt/' + i.data.invoice.pymnt_due_id);
					} else {
						$('.payment_recd_href').html('Receipt Not Available(Trainee is absent)');
					}
					
                    $('#ex9').modal();
                }
            }
        }
    });
}
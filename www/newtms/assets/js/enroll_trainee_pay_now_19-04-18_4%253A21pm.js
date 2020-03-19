/**
 * This js file includes in enrol_trainee_pay_now page
 */
$(document).ready(function() {
    var check = 0
    $('#enroll_pay_now_form').submit(function() {        
        check = 1;
        return form_validate(true);
    })
    $('button[type="reset"]').click(function() {
        $('.error_text').text('');
        $('.error').removeClass('error');
    });
    $('#enroll_pay_now_form select, #enroll_pay_now_form input').change(function() {
        if (check == 1) {
            return form_validate(false);
        }
    });
    
    $('#payment_type').change(function() {
        $('#row_dim3').hide();
        $('#row_dim').hide();    
        $('#giro_div').hide();
        $val = $(this).val();
        if ($val.length > 0) {
            if ($val == 'CASH') {
                $('#row_dim3').show();
            } else if ($val == 'CHQ') {
                $('#row_dim').show();
            } else if ($val == 'GIRO') {
                $('#giro_div').show();
            }
        }
    });
    $('.enrolment_mode').change(function() {
        $company = $('#company_id').val(); 
        $course_id = $('#course_id').val(); 
        $class_id = $('#class_id').val(); 
        $enrolment_mode = $(this).val();         
        if($class_pymnt_enrol == 'PDENROL') {
            if($enrolment_mode == 'COMPSPON') {
               $('#optype').val('PAYLATER'); 
               $('.enroll_now_btn').text('Book Now');
               $('.pdenrol_table').hide();
               $('#row_dim').hide();
               $('#row_dim3').hide();
               $('#giro_div').hide();
            } else {
               $('#optype').val('PAYNOW'); 
               $('.enroll_now_btn').text('Pay Now');
               $('.pdenrol_table').show();
            }
        }
        if ($enrolment_mode.length > 0) { 
            $url = baseurl + "trainee/re_calc_payment_details";
            $.ajax({
                url: $url,
                type: "post",
                dataType: "json",
                data: {
                    company: $company,enrolment_mode:$enrolment_mode,course_id:$course_id,class_id:$class_id
                },
                success: function(data) { 
                    $total_amount = data.net_fees_due;
                    $('#discount_rate').text(parseFloat(data.discount_rate).toFixed(2));
                    $('#discount_amount').text(parseFloat(data.discount_amount).toFixed(2));
                    $('#gst_amount').text(parseFloat(data.gst_amount).toFixed(2));
                    $('#gst_rate').text(parseFloat(data.gst_rate).toFixed(2));
                    $('#net_fees_due').text(parseFloat(data.net_fees_due).toFixed(2));
                }
            });
        }
    });
    $("#cashpaid_on, #paid_on, #transc_on").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
        maxDate: 0,
        onClose: function() {
            $(this).trigger("change");
        }
    });
    $("#cheque_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
        onClose: function() {
            $(this).trigger("change");
        }
    });
});
function disp_err($id, $text) {
    $text = typeof $text !== 'undefined' ? $text : '[required]';
    $($id).addClass('error');
    $($id + '_err').addClass('error').addClass('error3').text($text);
}
function remove_err($id) {
    $($id).removeClass('error');
    $($id + '_err').removeClass('error').removeClass('error3').text('');
}
function form_validate($retVal) { 
    $check_excess = 0;
    $chk_amount = parseFloat($total_amount);    
    $msg = '[net due and recd. amount not equal.]';
    $payment_type = $('#payment_type').val();
    $optype = $('#optype').val(); 
    var $enrolment_mode = $('.enrolment_mode:checked').val();        
    if($enrolment_mode == 'SELF' || (typeof $enrolment_mode  == "undefined")) {         
        if ($payment_type.length == 0) {
            disp_err('#payment_type');
            $retVal = false;
        } else if ($payment_type == 'CASH') {
            remove_err('#payment_type');
            $cashpaid_on = $('#cashpaid_on').val();
            if ($cashpaid_on.length == 0) {
                $retVal = false;
                disp_err('#cashpaid_on');
            } else {
                remove_err('#cashpaid_on');
            }
            $cash_amount = $.trim($('#cash_amount').val());
            $p_paid = parseFloat($cash_amount);
            if ($cash_amount.length == 0) {
                $retVal = false;
                disp_err('#cash_amount');
            } else if (isNaN($cash_amount)) {
                disp_err('#cash_amount', '[Invalid]');
                $retVal = false;
            } else {
                if ($check_excess == 1) {
                    if (($check_full == 2) && ($chk_amount != parseFloat($cash_amount))) {
                        $retVal = false;
                        disp_err('#cash_amount', $msg);
                    } else if (($check_full == 1) && ($chk_amount > parseFloat($cash_amount))) {
                        $retVal = false;
                        disp_err('#cash_amount', $msg);
                    } else {
                        remove_err('#cash_amount');
                    }
                } else {
                    if ($chk_amount != parseFloat($cash_amount)) {
                        $retVal = false;
                        disp_err('#cash_amount', $msg);
                    } else {
                        remove_err('#cash_amount');
                    }
                }
            }
        } else if ($payment_type == 'CHQ') {
            remove_err('#payment_type');
            $paid_on = $('#paid_on').val();
            if ($paid_on.length == 0) {
                $retVal = false;
                disp_err('#paid_on');
            } else {
                remove_err('#paid_on');
            }
            $cheque_number = $.trim($('#cheque_number').val());
            if ($cheque_number.length == 0) {
                $retVal = false;
                disp_err('#cheque_number');
            } else {
                remove_err('#cheque_number');
            }
            $cheque_amount = $.trim($('#cheque_amount').val());
            $p_paid = parseFloat($cheque_amount);
            if ($cheque_amount.length == 0) {
                $retVal = false;
                disp_err('#cheque_amount');
            } else if (isNaN($cheque_amount)) {
                disp_err('#cheque_amount', '[Invalid]');
                $retVal = false;
            } else {
                if ($check_excess == 1) {
                    if (($check_full == 2) && ($chk_amount != parseFloat($cheque_amount))) {
                        $retVal = false;
                        disp_err('#cheque_amount', $msg);
                    } else if (($check_full == 1) && ($chk_amount > parseFloat($cheque_amount))) {
                        $retVal = false;
                        disp_err('#cheque_amount', $msg);
                    } else {
                        remove_err('#cheque_amount');
                    }
                } else {
                    if ($chk_amount != parseFloat($cheque_amount)) {
                        $retVal = false;
                        disp_err('#cheque_amount', $msg);
                    } else {
                        remove_err('#cheque_amount');
                    }
                }
            }

            $cheque_date = $('#cheque_date').val();
            if ($cheque_date.length == 0) {
                $retVal = false;
                disp_err('#cheque_date');
            } else {
                remove_err('#cheque_date');
            }
            $bank_name = $.trim($('#bank_name').val());
            if ($bank_name.length == 0) {
                $retVal = false;
                disp_err('#bank_name');
            } else {
                remove_err('#bank_name');
            }
        }else if ($payment_type == 'GIRO') {        
            remove_err('#payment_type');
            $transc_on = $('#transc_on').val();
            if ($transc_on.length == 0) {
                $retVal = false;
                disp_err('#transc_on');
            } else {
                remove_err('#transc_on');
            }
            $gbank_name = $.trim($('#gbank_name').val());
            if ($gbank_name.length == 0) {
                $retVal = false;
                disp_err('#gbank_name');
            } else {
                remove_err('#gbank_name');
            }
            $giro_amount = $.trim($('#giro_amount').val());
            $p_paid = parseFloat($giro_amount);
            if ($giro_amount.length == 0) {
                $retVal = false;
                disp_err('#giro_amount');
            } else if (isNaN($giro_amount)) {
                disp_err('#giro_amount', '[Invalid]');
                $retVal = false;
            } else {
                if ($check_excess == 1) {
                    if (($check_full == 2) && ($chk_amount != parseFloat($giro_amount))) {
                        $retVal = false;
                        disp_err('#giro_amount', $msg);
                    } else if (($check_full == 1) && ($chk_amount > parseFloat($giro_amount))) {
                        $retVal = false;
                        disp_err('#giro_amount', $msg);
                    } else {
                        remove_err('#giro_amount');
                    }
                } else {
                    if ($chk_amount != parseFloat($giro_amount)) {
                        $retVal = false;
                        disp_err('#giro_amount', $msg);
                    } else {
                        remove_err('#giro_amount');
                    }
                }
            }
        }
        if ($retVal == true) {
             $('.pay_now').hide();
            if ($check_excess == 1 && $check_full == 1) {
                if ($chk_amount < parseFloat($p_paid)) {
                    $total_amount = parseFloat($p_paid - $chk_amount).toFixed(2);                
                    return false;
                }
            }
        }
    }
    return $retVal;
}

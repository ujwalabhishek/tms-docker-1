/**
 * This js file includes in update commission page
 */
$total_amt = 0;
$(document).ready(function() {
    $(".amount_paying").keydown(function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    $(document).on('change', '.amount_paying', function() {
        $user_id = $(this).parent().parent().data('user');
        $pay = $(this).val();
        $original_val = $('input[name="amount_check[' + $user_id + ']"]').val();
        $act_pay = $('.actual_pay_' + $user_id);
        $act_pay_enter = $('.actual_payenter_' + $user_id);
        if (isNaN($pay)) {
            alert('[Invalid]');
            $('input[name="amount_paying[' + $user_id + ']"]').val($original_val);
            $act_pay.show();
            $act_pay_enter.hide();
            $('input[name="trainee[' + $user_id + ']"][value="1"]').attr('checked', 'checked');
        } else if (parseFloat($original_val) < $pay) {
            alert('[Amount should be less than or equal to $' + $original_val + 'SGD]');
            $('input[name="amount_paying[' + $user_id + ']"]').val($original_val);
            $act_pay.show();
            $act_pay_enter.hide();
            $('input[name="trainee[' + $user_id + ']"][value="1"]').attr('checked', 'checked');
        } else if ($original_val == $pay) {
            $act_pay.show();
            $act_pay_enter.hide();
            $('input[name="trainee[' + $user_id + ']"][value="1"]').attr('checked', 'checked');
        }
        calculate_company_total();
    });
    $(document).on('change', '.c_trainee', function() {
        $user_id = $(this).parent().data('user');
        $checked_data = $('input[name="trainee[' + $user_id + ']"]:checked').val();
        $act_pay = $('.actual_pay_' + $user_id);
        $act_pay_enter = $('.actual_payenter_' + $user_id);
        if ($checked_data == 1) {
            $original_val = $('input[name="amount_check[' + $user_id + ']"]').val();
            $('input[name="amount_paying[' + $user_id + ']"]').val($original_val);
            $('.actual_pay_' + $user_id).removeAttr('style');
            $act_pay.show();
            $act_pay_enter.hide();
        } else if ($checked_data == 2) {
            $act_pay.hide();
            $act_pay_enter.show();
        } else if ($checked_data == 3) {
            $original_val = $('input[name="amount_check[' + $user_id + ']"]').val();
            $('input[name="amount_paying[' + $user_id + ']"]').val($original_val);
            $('.actual_pay_' + $user_id).css('text-decoration', 'line-through');
            $act_pay.show();
            $act_pay_enter.hide();
        }
        calculate_company_total();
    });
    function calculate_company_total() {
        $total = 0;
        $('.trainee_selected:checked').each(function() {
            $user_id = $(this).val();
            $check = $('input[name="trainee[' + $user_id + ']"]:checked').val();
            if ($check != 3) {
                $pay = $('input[name="amount_paying[' + $user_id + ']"]').val();
                $total = $total + +$pay;
            }
        });
        $total_amt = parseFloat($total).toFixed(2);
        $('.c_trainee_pay_total').html($total_amt);
        if (parseFloat($total_amt) > 0) {
            $('.common_pay').show();
        } else {
            $('.common_pay').hide();
        }
    }
    $("#cash_amount,#cheque_amount").keydown(function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    $('#bank_name').keyup(function() {
        $(this).val(this.value.toUpperCase());
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
    $("#cashpaid_on, #paid_on, #cheque_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
        maxDate: 0,
        onClose: function() {
            $(this).trigger("change");
        }
    });
    $('#payment_type').change(function() {
        $('#row_dim3').hide();
        $('#row_dim').hide();
        $val = $(this).val();
        if ($val.length > 0) {
            if ($val == 'CASH') {
                $('#row_dim3').show();
            } else if ($val == 'CHQ') {
                $('#row_dim').show();
            }
        }
    });
    var check = 0
    $('#updatecommissionform').submit(function() {
        check = 1;
        return form_validate(true);
    })
    $('button[type="reset"]').click(function() {
        $('.error_text').text('');
        $('.error').removeClass('error');
    });
    calculate_company_total();
});
function form_validate($retVal) {
    $payment_type = $('#payment_type').val();
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
        if ($cash_amount.length == 0) {
            $retVal = false;
            disp_err('#cash_amount');
        } else if (isNaN($cash_amount)) {
            disp_err('#cash_amount', '[Invalid]');
            $retVal = false;
        } else if (parseFloat($total_amt) != parseFloat($cash_amount)) {
            $retVal = false;
            disp_err('#cash_amount', '[commission due and paid amount not equal.]');
        } else {
            remove_err('#cash_amount');
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
        if ($cheque_amount.length == 0) {
            $retVal = false;
            disp_err('#cheque_amount');
        } else if (isNaN($cheque_amount)) {
            disp_err('#cheque_amount', '[Invalid]');
            $retVal = false;
        } else if (parseFloat($total_amt) != parseFloat($cheque_amount)) {
            $retVal = false;
            disp_err('#cheque_amount', '[commission due and paid amount not equal.]');
        } else {
            remove_err('#cheque_amount');
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
    }
    return $retVal;
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
///////added by shubhranshu to prevent multiple clicks////////////////
function validateCommission(){
    var self = $('#search_form'),
    button = self.find('input[type="submit"],button');
    button.attr('disabled','disabled').html('Please Wait..');
    return true;
}///////added by shubhranshu to prevent multiple clicks////////////////
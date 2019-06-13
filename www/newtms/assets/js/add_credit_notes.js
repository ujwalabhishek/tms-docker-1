/* 
 * This js file included in  credit_notes form
 */
function isunique_credit_number($value,$id) {
    var $value = $.trim($value);
    if ($value == '') {
        $("#" + $id + "_err").text("[required]").addClass('error');
        $("#" + $id).addClass('error');
        return false;
    } else {        
        $.ajax({
            url: baseurl+"accounting/unique_check_credit_number",
            type: "post",
            data: {credit_number: $value},
            success: function(res) {
                if (res > 0) {                    
                    $("#" + $id + "_err").text("[Credit number exists!]").addClass('error');
                    $("#" + $id).addClass('error');
                    return false;
                } else {                    
                    $("#" + $id + "_err").text("").removeClass('error');
                    $("#" + $id).removeClass('error');
                    return true;
                }
            },
            error: function() {
                return false;
            }
        });
    }
}
$(document).ready(function() {
    $("#credit_note_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,        
        maxDate: 0,
        yearRange: "-100:+100",
    });
    $("#ori_invoice_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,        
        maxDate: 0,
        yearRange: "-100:+100",
    });
    var check = 0;
    $('#creditForm').submit(function() {
        check = 1;
        return validate();
    });
    $('#creditForm input').change(function() {
        if (check == 1) {
            return validate();
        }
    });
    
    $(".float_number").keydown(function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            (e.keyCode == 65 && e.ctrlKey === true) || 
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });        
    
});
function validate() {
    var retVal = true;
    var $credit_note_number = $.trim($('#credit_note_number').val());
    var $credit_note_number_span = $.trim($('#credit_note_number_err').text());
    if($credit_note_number == "") {
        $("#credit_note_number_err").text("[required]").addClass('error');
        $("#credit_note_number").addClass('error');
        retVal = false;
    } else if($credit_note_number_span == '[Credit number exists!]') {
        retVal = false;
    }else {
        $("#credit_note_number_err").text("").removeClass('error');
        $("#credit_note_number").removeClass('error');
    }
    
    var $credit_note_date = $.trim($('#credit_note_date').val());
    if($credit_note_date == "") {
        $("#credit_note_date_err").text("[required]").addClass('error');
        $("#credit_note_date").addClass('error');
        retVal = false;
    } else {
        $("#credit_note_date_err").text("").removeClass('error');
        $("#credit_note_date").removeClass('error');
    }
    
    var $ori_invoice_number = $.trim($('#ori_invoice_number').val());
    if($ori_invoice_number == "") {
        $("#ori_invoice_number_err").text("[required]").addClass('error');
        $("#ori_invoice_number").addClass('error');
        retVal = false;
    } else {
        $("#ori_invoice_number_err").text("").removeClass('error');
        $("#ori_invoice_number").removeClass('error');
    }
    
    var $ori_invoice_date = $.trim($('#ori_invoice_date').val());
    if($ori_invoice_date == "") {
        $("#ori_invoice_date_err").text("[required]").addClass('error');
        $("#ori_invoice_date").addClass('error');
        retVal = false;
    } else {
        $("#ori_invoice_date_err").text("").removeClass('error');
        $("#ori_invoice_date").removeClass('error');
    }
    
    var $credit_note_amount = $.trim($('#credit_note_amount').val());
    if($credit_note_amount == "") {
        $("#credit_note_amount_err").text("[required]").addClass('error');
        $("#credit_note_amount").addClass('error');
        retVal = false;
    } else {
        $("#credit_note_amount_err").text("").removeClass('error');
        $("#credit_note_amount").removeClass('error');
    }
    
    if ($('.credit_note_div span').hasClass('error')) {
        retVal = false;
    }
    return retVal;
}


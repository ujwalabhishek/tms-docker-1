/* 
 * this js created to add all jquery function in invoice_audit_trail form
  * Date: 07 Apr 2015
 */
$(document).ready(function () {
    $("#start_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
        onSelect: function (selectedStartDate) {
            $("#end_date").datepicker("option", {
                minDate: selectedStartDate,
                maxDate: '',
            });
        },
    });
    $("#end_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
        minDate: 0,
        maxDate: -1
    });
    if ($('#start_date').val().length > 0) {
        $("#end_date").datepicker("option", {
            minDate: $('#start_date').val(),
            maxDate: '',
        });
    }
    
    //added by shubhranshu
    $("#invoice_no").autocomplete({
        source: function (request, response) {
            $('#invoice_id').val('');
            if (request.term.trim().length > 0) {
                $.ajax({
                    url: $siteurl + "reports_finance/get_invoice_audittrail_json",//modified by shubhranshu
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            } else {
                var d;
                response(d);
            }
        },
        select: function (event, ui) {
            var id = ui.item.key;
            $('#invoice_id').val(id);
             $('#invoice_no').val(id);
            validate(false);
        },
        minLength: 5
    });
    //added by pritam
    $("#company_name").autocomplete({
        source: function(request, response) {
            $('#company_id').val('');
            if (request.term.trim().length > 0) {
                $.ajax({
                    url: $siteurl + "reports_finance/get_company_json",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            } else {
                var d;
                response(d);
            }
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#company_id').val(id);
            validate(false);
        },
        minLength: 0
    });
    //

    var search_check = 0;
    $('#invoicelistform').submit(function () {
        search_check = 1; ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
         if(validate(true)){
           var self = $(this),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            return true;
        }else{
            return false;
        } ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
    });
    $('#invoicelistform input').change(function () {
        if (search_check == 1) {
            return validate(false);
        }
    });
    function validate(retval) {
        var invoice_no = $('#invoice_no').val().trim();
        var invoice_id = $('#invoice_id').val();
        if (invoice_no.length > 0 && invoice_id.length == 0) {
            disp_err('#invoice_no', 'Select from auto-help');
            retval = false;
        } else {
            remove_err('#invoice_no');
        }
        return retval;
    }
});
function disp_err($id, $text) {
    $text = typeof $text !== 'undefined' ? $text : '[required]';
    $($id).addClass('error');
    $($id + '_err').addClass('error').text($text);
}
function remove_err($id) {
    $($id).removeClass('error');
    $($id + '_err').removeClass('error').text('');
}


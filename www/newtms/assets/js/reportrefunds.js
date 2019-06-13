/**
 * This js file includes in reports payment refunds page
 */
$(document).ready(function() {
    $("#start_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
        onSelect: function(selectedStartDate) {
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
    $('.search_select').change(function() {
        $('#company').val('');
        $('#invoice_id').val('');
        $('#taxcode').val('');
        $('#taxcode_id').val('');
        $('#trainee').val('');
        $('#trainee_id').val('');
        keep_search_active();
    });
    function keep_search_active() {
        $('#company').attr('disabled', 'disabled');
        $('#invoice_id').attr('disabled', 'disabled');
        $('#taxcode').attr('disabled', 'disabled');
        $('#trainee').attr('disabled', 'disabled');
        $val = $('.search_select:checked').val();
        if ($val == 1) {
            $div = $('#invoice_id');
        } else if ($val == 2) {
            $div = $('#taxcode');
        } else if ($val == 3) {
            $div = $('#trainee');
        } else if ($val == 4) {
            $div = $('#company');
        }
        $div.removeAttr('disabled');
        if ($('#company option').length < 2) {
            $('#company').attr('disabled', 'disabled');
            $('.search_select[value="4"]').attr('disabled', 'disabled');
        }
    }
    keep_search_active();
    $("#taxcode").autocomplete({
        source: function(request, response) {
            $('#taxcode').val($('#taxcode').val().toUpperCase())
            $('#taxcode_id').val('');
            $.ajax({
                url: $siteurl + "class_trainee/get_paid_taxcode",
                type: "post",
                dataType: "json",
                data: {
                    q: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#taxcode_id').val(id);
        }
    });
    $("#trainee").autocomplete({
        source: function(request, response) {
            $('#trainee').val($('#trainee').val().toUpperCase())
            $('#trainee_id').val('');
            $.ajax({
                url: $siteurl + "class_trainee/get_paid_trainee",
                type: "post",
                dataType: "json",
                data: {
                    q: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#trainee_id').val(id);
        }
    });
    ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
    $('#paymrcvd_report_form').submit(function() {
        search_check = 1;
        ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
        if(validate(true)){
           var self = $(this),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            return true;
        }else{
            return false;
        } ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
    });
   
    function validate(retval) {
        var company_name = $('#company').val().trim();
        var invoice_id = $('#invoice_id').val();

        var end_date = $('#end_date').val();
        var start_date = $('#start_date').val();
        if (company_name == "" && invoice_id == "" && (start_date == "" || end_date== "")) {
            disp_err('#valid','Oops! Please select atleast one field to search');
            retval = false;
        } else {
           remove_err('#valid');
        }
        return retval;
    }
    function disp_err($id, $text) {
    $text = typeof $text !== 'undefined' ? $text : '[required]';
    $($id).addClass('error');
    $($id + '_err').addClass('error').text($text);
}
function remove_err($id) {
    $($id).removeClass('error');
    $($id + '_err').removeClass('error').text('');
}///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
});



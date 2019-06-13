/**
 * This js file includes in reports payment due page
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
     ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
    function validate(retval) {
        var company_name = $('#salesexec').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        if (company_name == "" && (start_date == "" || end_date== "")) {
            disp_err('#salesexe');
            disp_err('#start_date');
            disp_err('#end_date');
            retval = false;
        } else {
           remove_err('#salesexe');
            remove_err('#start_date');
            remove_err('#end_date');
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
    } ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
});


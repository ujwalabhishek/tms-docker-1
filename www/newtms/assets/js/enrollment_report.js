/**
 * This js file includes in enrollment report page
 */
$(document).ready(function () {
    $("#input_from_date").datepicker({
        dateFormat: CLIENT_DATE_FORMAT,
        onSelect: function (newDate, obj) {
            $('#input_to_date').datepicker('option', 'minDate', $.datepicker.parseDate(CLIENT_DATE_FORMAT, newDate));
        }
    });

    $("#input_to_date").datepicker({
        dateFormat: CLIENT_DATE_FORMAT
    });

    $(".th_header").click(function(){
        var sort = $(this).attr('sort');
        var action = pageurl + '?b=' + sort + '&o=' + ancher;
        var form = $('#search_form');
        form.attr("action", action);
    });
   ///////added by shubhranshu to prevent multiple clicks////////////////  ///////////////////////////////////////////////////////////////////
    $('#search_form').submit(function() {
        search_check = 1;

        if(validate(true)){
           var self = $(this),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            return true;
        }else{
            return false;
        }
    });
    
    function validate(retval) {
        var select_sales_id = $('#select_sales_id').val().trim();
        var select_non_sales_id = $('#select_non_sales_id').val().trim();
        var input_from_date = $('#input_from_date').val().trim();
        var input_to_date = $('#input_to_date').val().trim();
        if(select_sales_id == '' && select_non_sales_id == '' && (input_from_date == '' || input_to_date == '')){
                $('#search_error').addClass('error').text('Oops!..Please select atleast one filter to perform search operation');
                retval = false;
            }else{
                $('#search_error').removeClass('error').text('');
        }
        return retval;
    }
    ///////added by shubhranshu to vaildate search operation/////////////////////////////////////////////////////////////////////////////////
    $('#export_to_pdf_but').click(function(){
        $('input[name=export]').val("pdf");
        var form = $('#search_form');
        form.submit();
        $('input[name=export]').val("");
    });

    $('#export_to_xls_but').click(function(){
        $('input[name=export]').val("xls");
        var form = $('#search_form');
        form.attr("target", "_blank");
        form.submit();
        $('input[name=export]').val("");
    });
    $('.search_select').change(function() 
    {
        $('#select_sales_id').val('');
        $('#select_non_sales_id').val('');
        $('#select_sales_id').attr('disabled', 'disabled');
        $('#select_non_sales_id').attr('disabled', 'disabled');
        $val = $('.search_select:checked').val();
        if ($val == 1) {
            $div = $('#select_sales_id');
        } else {
            $div = $('#select_non_sales_id');
        }
        $div.removeAttr('disabled');
    });
    $('#select_sales_id').attr('disabled', 'disabled');
    $('#select_non_sales_id').attr('disabled', 'disabled');
    $val = $('.search_select:checked').val();
    if ($val == 1) {
        $div = $('#select_sales_id');
    } else {
        $div = $('#select_non_sales_id');
    }
    $div.removeAttr('disabled');




});
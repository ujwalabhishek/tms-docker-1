/**
 * This js file includes in sales report
 */
$(document).ready(function() {
    $year_arr = ['dummy', 'January', 'February', 'March', 'April',
        'May', 'June', 'July', 'August', 'September',
        'October', 'November', 'December'];
    $('.sales_link').click(function() {
        $sales = $(this).data('salesexec');
        $name = $(this).children('.name').html();
        $.ajax({
            url: $baseurl + "reports/get_sales_payment",
            type: "post",
            async: false,
            dataType: 'json',
            data: {
                sales: $sales,
            },
            success: function(res) {
                $div = $('.sales_popup');
                $div.html('');
                if (res != '') {
                    $total = 0;
                    $.each(res, function(i, item) {
                        $total = $total + parseFloat(item.amount_paid);
                        $month = $year_arr[item.comm_period_mth] + ' ' + item.comm_period_yr;
                        $html = '<tr><td>' + item.course + '</td><td>' + $month + '</td>\
                        <td>' + item.paid_on + '</td><td>' + item.category_name + '</td>\
                        <td>$ ' + parseFloat(item.amount_paid).toFixed(2) + ' SGD</td></tr>';
                        $div.append($html);
                    });
                    $html = '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td><b>Total:</b></td><td><b>$ ' + parseFloat($total).toFixed(2) + ' SGD</b></td></tr>';
                    $div.append($html);
                    $('.print_pdf').show();
                } else {
                    $('.print_pdf').hide();
                    $div.html('<tr class="error"><td colspan="5">There is no commission paid details available.</td></tr>');
                }
                $('.s_name').html($name);
                $('.print_pdf').attr('href', $baseurl + 'classes/export_salesexecutive_commission/' + $sales);
                $('#ex3').modal();
            }
        });
        $()
});
    $('.search_select').change(function() 
    {
            $('#sales_exec').val('');
        $('#non_sales_exec').val('');
        $('#sales_exec').attr('disabled', 'disabled');
        $('#non_sales_exec').attr('disabled', 'disabled');
        $val = $('.search_select:checked').val();
        if ($val == 1) {
            $div = $('#sales_exec');
        } else {
            $div = $('#non_sales_exec');
        }
        $div.removeAttr('disabled');
    });
    $('#sales_exec').attr('disabled', 'disabled');
    $('#non_sales_exec').attr('disabled', 'disabled');
    $val = $('.search_select:checked').val();
    if ($val == 1) {
        $div = $('#sales_exec');
    } else {
        $div = $('#non_sales_exec');
    }
    $div.removeAttr('disabled');
    
    ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
    $('#sales_report_form').submit(function() {
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
        var sales_exec = $('#sales_exec').val().trim();
        var non_sales_exec = $('#non_sales_exec').val();
        if (sales_exec == "" && non_sales_exec == "") {
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
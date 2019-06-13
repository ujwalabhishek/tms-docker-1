/** 
 * This js file included in booked seats page
 */
$(document).ready(function() {

    $('.company_paid').click(function() {
        $invoice = $(this).data('invoice');
        $.ajax({
            url: $baseurl + "class_trainee/get_company_invoice_payment_recd",
            type: "post",
            dataType: "json",
            data: {
                invoice: $invoice,
            },
            beforeSend: function() {
            },
            success: function(res) {
                var data = res.data;
                $('.cp_invoice_class_name').html(data.class_name);
                $('.cp_invoice_company_name').html(data.company_name);
                $('.cp_invoice_course_name').html(data.crse_name);
                $('.cp_invoice_id').html(data.invoice_id);
                $('.cp_invoice_dated').html(data.inv_date);
                $('.cp_invoice_amount').html(parseFloat(data.total_inv_amount).toFixed(2));
                $('.cp_invoice_discount_label').html(data.discount_label);
                $('.cp_invoice_discount_rate').html(parseFloat(data.discount_rate).toFixed(2));
                $('.cp_invoice_discount_amount').html(parseFloat(data.total_inv_discnt).toFixed(2));
                $('.cp_invoice_subsidy_amount').html(parseFloat(data.total_inv_subsdy).toFixed(2));
                $('.cp_invoice_gst_label').html(data.gst_label);
                $('.cp_invoice_gst_rate').html(parseFloat(data.gst_rate).toFixed(2));
                $('.cp_invoice_total_gst').html(parseFloat(data.total_gst).toFixed(2));

                var recd = res.recd;
                $payrcd_table = $('.cpayment_received tbody')
                $payrcd_table.empty();
                $total_recd = 0;
                if (recd != '') {
                    $.each(recd, function(i, item) {
                        $amount_recd = parseFloat(item.amount_recd).toFixed(2);
                        $gender = (item.gender == 'MALE') ? 'Mr. ' : 'Ms. ';
                        $name = $gender + item.first_name ;
                        $html = '<tr><td>' + item.recd_on + '</td><td>' + $name + '</td><td>$ ' + $amount_recd + ' SGD</td></tr>';
                        $payrcd_table.append($html);
                        $total_recd = $total_recd + +$amount_recd;
                    });
                    if (parseFloat(data.invoice_excess_amt) > 0) {
                        $amount_recd = parseFloat(data.invoice_excess_amt).toFixed(2);
                        $html = '<tr><td></td><td>Over Payment Recd.</td><td>$ ' + $amount_recd + ' SGD</td></tr>';
                        $payrcd_table.append($html);
                        $total_recd = $total_recd + +$amount_recd;
                    }
                    $html = '<tr><td>&nbsp;</td><td><strong>Total: </strong></td><td><strong>$ ' + parseFloat($total_recd).toFixed(2) + ' SGD</strong></td></tr>'
                    $payrcd_table.append($html);
                    $('.company_print_pdf').attr('href', $baseurl + 'class_trainee/export_payment_received/' + data.pymnt_due_id);
                } else {
                    $html = '<tr><td class="error" colspan="3">There is no payment available.</td></tr>';
                    $payrcd_table.append($html);
                }
                $('#ex3').modal();
            }
        });
    });

    $('.open_paid').click(function() {
        $class_id = $(this).data('class');
        $user_id = $(this).data('user');
        $.ajax({
            url: $siteurl + 'class_trainee/get_enroll_invoice_by_user_class',
            type: 'post',
            dataType: 'json',
            async: false,
            data: {'class_id': $class_id, 'user_id': $user_id},
            success: function(i) {
                var data = i.data;
               $('.p_invoice_class_name').html(data.class_name);
               $('.p_invoice_trainee_name').html(data.first_name);
               $('.p_invoice_course_name').html(data.crse_name);
                $('.p_invoice_id').html(data.invoice_id);
                $('.p_invoice_dated').html(data.inv_date);
                $('.p_invoice_amount').html(data.total_inv_amount);
                $('.p_invoice_discount_label').html(data.discount_label);
                $('.p_invoice_discount_rate').html(data.discount_rate);
                $('.p_invoice_discount_amount').html(data.total_inv_discnt);
                $('.p_invoice_subsidy_amount').html(data.total_inv_subsdy);
                $('.p_invoice_gst_label').html(data.gst_label);
                $('.p_invoice_gst_rate').html(data.gst_rate);
                $('.p_invoice_total_gst').html(data.total_gst);
                $('.p_invoice_total_fees').html(data.total_unit_fees);
                var recd = i.recd;
                $payrcd_table = $('.payment_received tbody')
                $payrcd_table.empty();
                if (recd != '') {
                    $.each(recd, function(i, item) {
                        $html = '<tr><td>' + item.recd_on + '</td><td>' + item.mode + '</td><td>' + item.amount + '</td></tr>';
                        $payrcd_table.append($html);
                    });
                } else {
                    $html = '<tr><td class="error" colspan="3">There is no payment available.</td></tr>';
                    $payrcd_table.append($html);
                }
                $('.print_pdf').attr('href', $baseurl + 'class_trainee/export_payment_received/' + data.pymnt_due_id);
            }
        });
    });
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
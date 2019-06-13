/**
 * This js file includes in refund payment page
 */
var $company_total = 0;
var $total_amount = 0;
$(document).ready(function() {
    $('#account_type').val('individual');
    $('#account_type').change(function() {
        $('.companyamounts_display').hide();
        $('.trainee_div').hide();
        $('.amountdetails_div').hide();
        $('#company_invoice').attr('disabled', 'disabled');
        $('#company_invoice_id').html('<option value="">Select</option>');
        $('#company_invoice_id').attr('disabled', 'disabled');
        $('.search_select[value="1"]').attr('checked', 'checked');
        $('.search_select').trigger('change');
        $('#company').val('');
        $('#company_invoice').val('');
        $('#company_invoice_id').val('');
        $val = $(this).val();
        if ($val == 'company') {
            $('.company_td').show();
            $('.row_dimm9').hide();
        } else {
            $('.company_td').hide();
            $('.row_dimm9').show();
        }
    });
    $('#company').change(function() {
        $company = $('#company').val();
        $('#company_invoice_id').html('<option value="">Select</option>');
        $('#company_invoice_id').attr('disabled', 'disabled');
         if ($company.length > 0) { 
            $url = $siteurl + "class_trainee/get_company_paid_invoice";
            $.ajax({
                url: $url,
                type: "post",
                dataType: "json",
                data: {
                    company: $company
                },
                success: function(data) {
                    if (data != '') {
                        $('#company_invoice_id').removeAttr('disabled');
                        remove_err('#company_invoice');
                        $.each(data, function(i, item) {
                            $('#company_invoice_id').append('<option value="' + item.key + '">' + item.label + '</option>');
                        });
                    } else {                        
                        disp_err('#company_invoice', '[There are no invoices.]');
                    }
                }
            });
        }
    });
    $('.print_company_invoice').click(function() {
        remove_err('#without_subsidy');
        remove_err('#with_subsidy');
        $val = $('.select_invoice_print:checked').val();
        $pymnt_due_id = $('#payment_due_id').val();
        $company_invoice_id = $('#company_invoice_id').val();
        if ($val == 1) {
            location.replace($baseurl + 'class_trainee/export_company_generate_invoice/' + $pymnt_due_id);
        } else if ($val == 2 || $val == 3) {
            $count = 0;
            $subsidy = ($val == 2) ? 1 : 0;
            $.ajax({
                url: $baseurl + "class_trainee/get_count_company_invoice_no_subsidy",
                type: "post",
                async: false,
                data: {
                    invoice: $company_invoice_id,
                    subsidy: $subsidy
                },
                success: function(res) {
                    $count = res;
                }
            });
            if ($count == 0) {
                if ($val == 3) {
                    disp_err('#without_subsidy', '[No trainees found in this invoice without subsidy.]');
                } else {
                    disp_err('#with_subsidy', '[No trainees found in this invoice with subsidy.]');
                }
            } else {
                location.replace($baseurl + 'class_trainee/gen_inv_pdf_basedon_subsidy/' + $company_invoice_id + '/' + $subsidy);
            }
        }
        return false;
    });

    $("#company_invoice").autocomplete({
        source: function(request, response) {
            $url = $siteurl + "class_trainee/get_company_paid_invoice";
            $company = $('#company').val();
            $('#company_invoice_id').val('');
            $.ajax({
                url: $url,
                type: "post",
                dataType: "json",
                data: {
                    q: request.term,
                    company: $company
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#company_invoice_id').val(id);
        }
    });

    $('#refund_reason').change(function() {
        $val = $(this).val();
        if ($val == 'OTHERS') {
            $('#row_dim_new1').show();
        } else {
            $('#other_reason').val('');
            $('#row_dim_new1').hide();
        }
    });
    $('.search_select').change(function() {
        $('#invoice').val('');
        $('#invoice_id').val('');
        $('#taxcode').val('');
        $('#taxcode_id').val('');
        $('#trainee').val('');
        $('#trainee_id').val('');
        keep_search_active();
    });
    $("#invoice").autocomplete({
        source: function(request, response) {
            $('#invoice_id').val('');
            $.ajax({
                url: $siteurl + "accounting/get_paid_invoice",
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
            $('#invoice_id').val(id);
        }
    });
    $("#taxcode").autocomplete({
        source: function(request, response) {
             $('#taxcode_id').val('');
            if (request.term.trim().length > 0) {
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
            } else {
                var d;
                response(d);
            }
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#taxcode_id').val(id);
            remove_all_err('#search_form');
        },
        minLength: 0
    });
    $("#trainee").autocomplete({
        source: function(request, response) {
            $('#trainee_id').val('');
            if (request.term.trim().length > 0) {
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
            } else {
                var d;
                response(d);
            }
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#trainee_id').val(id);
            remove_all_err('#search_form');
        },
        minLength: 0
    });
    $('.search_button').click(function() {
        $account_type = $('#account_type').val();
        $invoice_id = '';
        $taxcode_id = '';
        $trainee_id = '';
        $search_select = $('.search_select:checked').val();
        remove_err('#company');
        remove_err('#company_invoice');
        remove_err('#invoice');
        remove_err('#taxcode');
        remove_err('#trainee');
        if ($account_type == 'company') {
            $retval = true;
            $company = $('#company').val();
            if ($company.length == 0) {
                $retval = false;
                disp_err('#company');
            }
            $company_invoice = $('#company_invoice').val();
            $company_invoice_id = $('#company_invoice_id').val();
            if ($company_invoice_id.length == 0) {
                $retval = false;
                disp_err('#company_invoice');
            }
            if ($retval == true) {
                return trigger_companyajax($company_invoice_id, $company);
            }
            return false;
        } else {
            if ($search_select == 1) {
                $invoice = $('#invoice').val();
                $invoice_id = $('#invoice_id').val();
                if ($invoice_id.length == 0) {
                    disp_err('#invoice');
                } else {
                    trigger_ajax($invoice_id, $taxcode_id, $trainee_id);
                }
            } else if ($search_select == 2) {
                $taxcode = $('#taxcode').val();
                $taxcode_id = $('#taxcode_id').val();
                if ($taxcode.length == 0) {
                    disp_err('#taxcode');
                } else if ($taxcode_id.length == 0) {
                    disp_err('#taxcode', '[Select NRIC/FIN No. from auto-help]');
                } else {
                    trigger_ajax($invoice_id, $taxcode_id, $trainee_id);
                }
            } else if ($search_select == 3) {
                $trainee = $('#trainee').val();
                $trainee_id = $('#trainee_id').val();
                if ($trainee.length == 0) {
                    disp_err('#trainee');
                } else if ($trainee_id.length == 0) {
                    disp_err('#trainee', '[Select Trainee Name from auto-help]');
                } else {
                    trigger_ajax($invoice_id, $taxcode_id, $trainee_id);
                }
            }
        }
    });
    function trigger_companyajax($invoice_id, $company) {
        $.ajax({
            url: $baseurl + 'class_trainee/get_company_refundpayment',
            type: 'post',
            dataType: 'json',
            data: {
                invoice: $invoice_id,
                company: $company
            },
            beforeSend: function() {
                remove_err('#without_subsidy');
                remove_err('#with_subsidy');
            },
            success: function(data) {
                remove_all();
                check = 0;
                reset_all();
                $('.c_invoice_course_name').html(data.crse_name);
                $('.c_invoice_class_name').html(data.class_name);
                $('.c_invoice_class_fees').html(parseFloat(data.total_unit_fees).toFixed(2));
                $('.c_invoice_discount_amount').html(parseFloat(data.total_inv_discnt).toFixed(2));
                $('.c_invoice_subsidy_amount').html(parseFloat(data.total_inv_subsdy).toFixed(2)); 
                $('.c_invoice_discount_rate').html(parseFloat(data.discount_rate).toFixed(2));
                $('.c_invoice_discount_label').html(data.discount_label);
                $('.c_invoice_gst_label').html(data.gst_label);
                $('.c_invoice_gst_rate').html(parseFloat(data.gst_rate).toFixed(2));
                $('.c_invoice_total_gst').html(parseFloat(data.total_gst).toFixed(2));
                $('.c_refund_amt').html(parseFloat(data.amount_refund).toFixed(2));
                $('.c_invoice_amount').html(parseFloat(data.total_inv_amount).toFixed(2));
                $('.c_over_recd').html(parseFloat(data.invoice_excess_amt).toFixed(2));
                $('.company_payment_recd_view').attr('data-invoice', $invoice_id);
                $('#payment_due_id').val(data.pymnt_due_id);
                $('#trainee_hidden_id').val('');
                $('#company_hidden_id').val(data.company_id);
                $('#invoice_hidden_id').val(data.invoice_id);
                $('#account_hidden_id').val('company');
                $('.ctrainees_tbody').empty();
                var trainee = data.trainees;
                var cnt = 1;
                $company_total = 0
                $company_total_due = 0;
                $company_total_paid = 0;
                $company_total_refund = 0;
                $.each(trainee, function(i, item) {
                    $extra_name = '';
                    if (data.class_name != item.class_name) {
                        $extra_name = '<span class="green"> **</span><br><span class="green">(' + item.class_name + ')</span>';
                    }
                    $name = 'trainee[' + item.user_id + ']';
                    if (item.payment_status == 'NOTPAID') {
                        $status = '<span class="error">Not Paid</span>';
                    } else if (item.payment_status == 'PARTPAID') {
                        $status = '<span class="error">Paid Part</span>';
                    } else if (item.payment_status == 'PAID') {
                        $status = '<span style="color:green;">Paid Full</span>';
                    }
                    if (parseFloat(item.amount_remain) > 0) {
                        $payment_progress = '<span class="actual_pay_' + item.user_id + '">$ ' + parseFloat(item.amount_remain).toFixed(2) + '</span>\
                                        <input type="hidden" value="' + parseFloat(item.amount_remain).toFixed(2) + '" name="amount_check[' + item.user_id + ']"/>\
                                        <span style="display:none;" class="actual_payenter_' + item.user_id + '">$<input type="text" value="' + parseFloat(item.amount_remain).toFixed(2) + '" name="amount_paying[' + item.user_id + ']" class="amount_paying"/></span>';

                        $paid_status = '<input type="radio" class="c_trainee" checked="checked" name="' + $name + '" value="1">Full &nbsp; \
                                        <input type="radio" class="c_trainee" name="' + $name + '" value="2">Part &nbsp; \
                                        <input type="radio" class="c_trainee" name="' + $name + '" value="3">No Refund &nbsp; ';
                    } else {
                        $payment_progress = '<span class="actual_pay_' + item.user_id + '">$ ' + parseFloat(item.amount_remain).toFixed(2) + '</span>\
                                        <input type="hidden" value="' + parseFloat(item.amount_remain).toFixed(2) + '" name="amount_check[' + item.user_id + ']"/>\
                                        <span style="display:none;" class="actual_payenter_' + item.user_id + '">$<input type="text" value="' + parseFloat(item.amount_remain).toFixed(2) + '" name="amount_paying[' + item.user_id + ']" class="amount_paying"/></span>';
                        $paid_status = '<span style="color:green;">No Refund.</span>';
                    }
                    $html = '<tr>\
                                    <td>' + cnt + '<input name="trainee_selected[]" style="display:none;" class="trainee_selected" value="' + item.user_id + '" checked="checked" type="checkbox" /></td>\
                                    <td>' + item.tax_code + '</td>\
                                    <td>' + item.first  + $extra_name + '</td>\
                                    <td>$ ' + parseFloat(item.total_amount_due).toFixed(2) + '</td>\
                                    <td>$ ' + parseFloat(item.amount_paid).toFixed(2) + '</td>\
                                    <td>$ ' + parseFloat(item.amount_refund).toFixed(2) + '</td>\
                                    <td data-user="' + item.user_id + '">\
                                        ' + $payment_progress + '\
                                    </td>\
                                    <td data-user="' + item.user_id + '">' + $paid_status + '</td>\
                                    <td>' + $status + '<span></td>\
                                </tr>';
                    $('.ctrainees_tbody').append($html);
                    cnt++;
                    $company_total_due = $company_total_due + +item.total_amount_due;
                    $company_total_paid = $company_total_paid + +item.amount_paid;
                    $company_total_refund = $company_total_refund + +item.amount_refund;
                    $company_total = $company_total + +item.amount_remain;
                });
                if (cnt > 1) {
                    if (parseFloat(data.invoice_excess_amt) > 0) {
                        $name = 'trainee[0]';
                        if (parseFloat(data.excess_remain) > 0) {
                            $payment_progress = '<span class="actual_pay_0">$ ' + parseFloat(data.excess_remain).toFixed(2) + '</span>\
                                        <input type="hidden" value="' + parseFloat(data.excess_remain).toFixed(2) + '" name="amount_check[0]"/>\
                                        <span style="display:none;" class="actual_payenter_0">$<input type="text" value="' + parseFloat(data.excess_remain).toFixed(2) + '" name="amount_paying[0]" class="amount_paying"/></span>';

                            $paid_status = '<input type="radio" class="c_trainee" checked="checked" name="' + $name + '" value="1">Full &nbsp; \
                                        <input type="radio" class="c_trainee" name="' + $name + '" value="2">Part &nbsp; \
                                        <input type="radio" class="c_trainee" name="' + $name + '" value="3">No Refund &nbsp; ';
                        } else {
                            $payment_progress = '<span class="actual_pay_0">$ ' + parseFloat(data.excess_remain).toFixed(2) + '</span>\
                                        <input type="hidden" value="' + parseFloat(data.excess_remain).toFixed(2) + '" name="amount_check[0]"/>\
                                        <span style="display:none;" class="actual_payenter_0">$<input type="text" value="' + parseFloat(data.excess_remain).toFixed(2) + '" name="amount_paying[0]" class="amount_paying"/></span>';
                            $paid_status = '<span style="color:green;">No Refund.</span>';
                        }
                        $html = '<tr>\
                            <td>' + cnt + '<input name="trainee_selected[]" style="display:none;" class="trainee_selected" value="0" checked="checked" type="checkbox" /></td>\
                            <td></td>\
                            <td>Excess Payment Recd.</td>\
                            <td></td>\
                            <td>$ ' + parseFloat(data.invoice_excess_amt).toFixed(2) + '</td>\
                            <td>$ ' + parseFloat(data.excess_refunded).toFixed(2) + '</td>\
                            <td data-user="0">\
                                        ' + $payment_progress + '\
                                    </td>\
                            <td data-user="0">' + $paid_status + '</td>\
                            <td></td>\
                        </tr>';
                        $('.ctrainees_tbody').append($html)
                        $company_total = $company_total + +data.excess_remain;
                        $company_total_refund = $company_total_refund + +data.excess_refunded;
                    }
                    $company_total = parseFloat($company_total).toFixed(2);
                    $company_total_due = parseFloat($company_total_due).toFixed(2);
                    $company_total_paid = parseFloat($company_total_paid).toFixed(2);
                    $company_total_refund = parseFloat($company_total_refund).toFixed(2);
                    $total_paid = parseFloat($company_total_paid) + +parseFloat(data.invoice_excess_amt);
                    $total_paid = parseFloat($total_paid).toFixed(2);
                    $html = '<tr>\
                                <td colspan="2"></td>\
                                <td align="right"><strong>Total: </strong></td>\
                                <td><strong>$ ' + $company_total_due + '</strong></td>\
                                <td><strong>$ ' + $total_paid + '</strong></td>\
                                <td><strong>$ ' + $company_total_refund + '</strong></td>\
                                <td><strong>$ <span class="c_trainee_pay_total">' + $company_total + '</span></strong></td>\
                                <td></td>\
                                <td></td>\
                            </tr>';
                    $('.ctrainees_tbody').append($html);
                    $('.c_total_amt_recd').html($total_paid);
                    $('.c_tot_refundable_amt').html($company_total);
                    $pend_rec = parseFloat(data.total_inv_amount) + parseFloat(data.amount_refund) + parseFloat(data.invoice_excess_amt) - $total_paid - parseFloat(data.excess_refunded);
                    $pend_rec = $pend_rec < 0 ? 0 : $pend_rec;
                    $('.c_tot_pend_recv').html(parseFloat($pend_rec).toFixed(2));
                }
                $('.companyamounts_display').show();
                if (parseFloat($company_total) > 0) {
                    $('.common_pay').show();
                    $('.common_err').remove();
                } else {
                    $('.common_pay').hide();
                    $('.common_pay').after('<div class="common_err error">There are no funds available.</div>');
                }
            }
        })
    }
    $('.company_payment_refund_view').click(function() {
        $invoice = $('.company_payment_recd_view').attr('data-invoice');
        $.ajax({
            url: $baseurl + "class_trainee/get_company_invoice_payment_refund",
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

                var refund = res.refund;
                $payrcd_table = $('.cpayment_refund tbody')
                $payrcd_table.empty();
                if (refund != '') {
                    $t_refund = 0;
                    $.each(refund, function(i, item) {
                        $html = '<tr><td>' + item.refund_on + '</td><td>' + item.refnd_reason + '</td><td>' + item.mode_of_refund + '</td><td>$ ' + parseFloat(item.amount_refund).toFixed(2) + '</td></tr>';
                        $payrcd_table.append($html);
                        $t_refund = $t_refund + +item.amount_refund;
                    });
                    $html = '<tr><td>&nbsp;</td><td>&nbsp;</td><td><b>Total:</b></td><td><b>$ ' + parseFloat($t_refund).toFixed(2) + '</b></td></tr>';
                    $payrcd_table.append($html);
                    $('.company_print_refund_pdf').attr('href', $baseurl + 'class_trainee/export_payment_refund/' + data.pymnt_due_id);
                    $('.company_print_refund_pdf').show();
                } else {
                    $html = '<tr><td class="error" colspan="4">There are no refunds available.</td></tr>';
                    $payrcd_table.append($html);
                    $('.company_print_refund_pdf').hide();
                }
                $('#ex32').modal();
            }
        });
    });
    $('.company_payment_recd_view').click(function() {
        $invoice = $('.company_payment_recd_view').attr('data-invoice');
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
                        $name = $gender + item.first_name;
                        $html = '<tr><td>' + item.recd_on + '('+ $.trim(item.mode) +')</td><td>' + $name + '</td><td>$ ' + $amount_recd + '</td></tr>';
                        $payrcd_table.append($html);
                        $total_recd = $total_recd + +$amount_recd;
                    });
                    if (parseFloat(data.invoice_excess_amt) > 0) {
                        $amount_recd = parseFloat(data.invoice_excess_amt).toFixed(2);
                        $html = '<tr><td></td><td>Over Payment Recd.</td><td>$ ' + $amount_recd + '</td></tr>';
                        $payrcd_table.append($html);
                        $total_recd = $total_recd + +$amount_recd;
                    }
                    $html = '<tr><td>&nbsp;</td><td><strong>Total: </strong></td><td><strong>$ ' + parseFloat($total_recd).toFixed(2) + '</strong></td></tr>'
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
        $company_total = parseFloat($total).toFixed(2);
        $('.c_trainee_pay_total').html($company_total);
        if (parseFloat($company_total) > 0) {
            $('.common_pay').show();
        } else {
            $('.common_pay').hide();
        }
    }
    
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
    function trigger_ajax($invoice_id, $taxcode_id, $trainee_id) {
        $.ajax({
            url: $siteurl + "class_trainee/search_trainee_refundpayment",
            type: "post",
            dataType: "json",
            data: {
                'invoice_id': $invoice_id,
                'taxcode_id': $taxcode_id,
                'trainee_id': $trainee_id
            },
            beforeSend: function() {
                $('.trainee_table').hide();
                $('.trainee_table tbody').empty();
                $('.trainee_alert').empty();
            },
            success: function(res) {
                $('.trainee_table tbody').empty();
                
                $('.common_err').remove();
                $(".trainee_div").show();
                var data = res.data;
                if (data != '') {
                    $('.trainee_table').show();
                    $.each(data, function(i, item) {
                        $amount_refund = (item.amount_refund == null) ? 0 : item.amount_refund;
                        $refundable = item.amountrecd - $amount_refund;
                        $total_refundable = (parseFloat($refundable) <= 0) ? 0 : $refundable;
                       $html = '<tr>\
                                        <td><input type="radio" class="trainee_invoice"  name="trainee_invoice" value="' + item.payid + '"/></td>\
                                        <td>' + item.taxcode + '</td>\
                                        <td>' + item.first  + '</td>\
                                        <td>' + item.crse_name + ' - ' + item.class_name + '</td>\
                                        <td style="color:blue;">$ ' + parseFloat(item.amountdue).toFixed(2) + '</td>\
                                        <td>$ ' + parseFloat(item.amountrecd).toFixed(2) + '</td>\
                                        <td>$ ' + parseFloat($amount_refund).toFixed(2) + '</td>\
                                        <td style="color:blue;">$ ' + parseFloat($total_refundable).toFixed(2) + '</td>\
                                        <td><font color="red">Recd. Full<font></td>\
                                        </tr>';
                        $('.trainee_table tbody').append($html);
                    });
                } else {
                    $html = "<span class='error'>No Invoice Found for the Trainee '" + res.trainer + "'. Please contact Administrator.</span>";
                    $('.trainee_alert').html($html);
                }
                $('.amountdetails_div').hide();
            }
        });
    }
    $(document).on('change', '.trainee_invoice', function() {
        $payid = $('.trainee_invoice:checked').val();
        if ($payid.length > 0) {
            $.ajax({
                url: $siteurl + "class_trainee/get_enroll_invoice",
                type: "post",
                dataType: "json",
                data: {
                    'payid': $payid,
                },
                beforeSend: function() {
                },
                success: function(res) {
                    remove_all();
                    check = 0;
                    reset_all();
                    $('.amountdetails_div').show();
                    var data = res.data;
                    if (data != null) {
                        $('#trainee_hidden_id').val(data.user_id);
                        $('#company_hidden_id').val('');
                        $('#invoice_hidden_id').val(data.invoice_id);
                        $('#account_hidden_id').val('individual');
                        $('.print_href').attr('href', $baseurl + 'class_trainee/export_generate_invoice/' + data.pymnt_due_id);
                        $('.print_receipt').attr('href', $baseurl + 'class_trainee/export_payment_receipt/' + data.pymnt_due_id);
                        $('.pay_total_invoice_amount').html(data.total_inv_amount);
                        $('.pay_payment_recd_till_date').html(data.paid_rcd_till_date);
                        $('.pay_gst_rate').html(data.gst_rate);
                        $('.pay_total_gst').html(data.total_gst);
                        $('.pay_gst_label').html(data.gst_label);
                        $('.pay_discount_label').html(data.discount_label);
                        $('.pay_discount_rate').html(data.discount_rate);
                        $('.pay_discount_amount').html(data.total_inv_discnt);
                        $('.pay_class_fees').html(data.total_unit_fees);
                        $('.pay_subsidy_amount').html(data.total_inv_subsdy);
                        $('.pay_subsidy_type').html(res.subsidy_type_label); 
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
                        $('.p_invoice_subsidy_type').html(res.subsidy_type_label);
                        $('.p_invoice_subsidy_type').html(res.subsidy_type_label);
                        $('.p_invoice_gst_label').html(data.gst_label);
                        $('.p_invoice_gst_rate').html(data.gst_rate);
                        $('.p_invoice_total_gst').html(data.total_gst);
                        $('.p_invoice_total_fees').html(data.total_unit_fees);
                        $('.logo').attr('src', $baseurl + 'logos/' + res.tenant.Logo);
                        $('.r_tenant_name').html(res.tenant.tenant_name);
                        $('.r_tenant_address').html(res.tenant.tenant_address);
                        $('.r_tenant_phone').html(res.tenant.tenant_contact_num);
                        $('.r_tenant_email').html(res.tenant.tenant_email_id);
                        $('.r_class').html(data.class_name);
                        $('.r_course').html(data.crse_name);
                        $('.r_certilevel').html(data.courseLevel);
                        $('.r_course_manager').html(data.course_manager);
                        $('.r_class_loc').html(data.ClassLoc);
                        $('.r_class_start').html(data.class_start);
                        $('.r_invoice_no').html(res.invoice.recd_on_year + '' + res.invoice.invoice_id);
                        $('.r_invoice_recd').html(res.invoice.recd_on);
                        var amount = parseFloat(res.invoice.amount_recd).toFixed(2);
                        $('.r_invoice_amount_recd').html(amount);
                        $('.r_invoice_mode').html(res.invoice.mode_of_pymnt);
                        $('.r_invoice_trainee').html(res.trainee);
                        $total_amount = data.paid_rcd_till_date;
                        $('#refundable_total').val($total_amount);
                        
                        $mop=data.mode_of_pymnt;
                        if($mop=="SELF"){
                        $('#sfc_amount').val(data.sfc_claimed);
                        }
                        else if($mop=="ATO") {
                        $('#sfcato_amount').val(data.sfc_claimed);
                        }
                        
                        if (parseFloat($total_amount) == 0) {
                            $('.common_pay').hide();
                            $('.common_pay').after('<div class="common_err error">There are no funds available.</div>');
                        } else {
                            $('.common_pay').show();
                            $('.common_err').remove();
                        }
                        $('.print_pdf').attr('href', $baseurl + 'class_trainee/export_payment_received/' + data.pymnt_due_id);
                        $('.print_refund_pdf').attr('href', $baseurl + 'class_trainee/export_payment_refund/' + data.pymnt_due_id);
                    }
                    var recd = res.recd;
                    $payrcd_table = $('.payment_received tbody')
                    $payrcd_table.empty();
                    if (recd != '') {
                        $.each(recd, function(i, item) {
                            $html = '<tr><td>' + item.recd_on + '('+ $.trim(item.mode) +')</td><td>' + item.amount + '</td></tr>';
                            $payrcd_table.append($html);
                        });
                    } else {
                        $html = '<tr><td class="error" colspan="3">There is no payment available.</td></tr>';
                        $payrcd_table.append($html);
                    }
                    var refund = res.data.refund_details;
                    $payrcd_table = $('.payment_refund tbody')
                    $payrcd_table.empty();
                    if (refund != '') {
                        $cnt = 0;
                        $ti_refund = 0;
                        $.each(refund, function(i, item) {
                            $html = '<tr><td>' + item.refund_on + '</td><td>' + item.refnd_reason + '</td><td>' + item.mode_of_refund + '</td><td>$ ' + parseFloat(item.amount_refund).toFixed(2) + '</td></tr>';
                            $payrcd_table.append($html);
                            $ti_refund = $ti_refund + +item.amount_refund;
                            $cnt++;
                        });
                        if ($cnt > 1) {
                            $html = '<tr><td>&nbsp;</td><td>&nbsp;</td><td><b>Total: </b></td><td><b>$ ' + parseFloat($ti_refund).toFixed(2) + '</b></td></tr>';
                            $payrcd_table.append($html);
                            $('.print_refund_pdf').show();
                        }
                    } else {
                        $html = '<tr><td class="error" colspan="4">There are no refunds available.</td></tr>';
                        $payrcd_table.append($html);
                        $('.print_refund_pdf').hide();
                    }
                    $('#payment_due_id').val($payid);
                }
            });
        }
    });
    $('#bank_name, #cheque_number').keyup(function() {
        $(this).val(this.value.toUpperCase());
    });
    $("#cash_amount,#cheque_amount,#refund_amount").keydown(function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) || (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    var check = 0
    $('#updatepaymentform').submit(function() {
        check = 1;
        
        return form_validate(true);
    })
    $('button[type="reset"]').click(function() {
    
        $('.error_text').text('');
        $('.error').removeClass('error');
    });
    $('#updatepaymentform select, #updatepaymentform input').change(function() {
        if (check == 1) {
           
            return form_validate(false);
        }
    });
    $("#cheque_date,#cheque_date1").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
        onClose: function() {
            $(this).trigger("change");
        }
    });
    $("#refund_date").datepicker({
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
        $('#row_dim4').hide();
        $('#row_dim5').hide();
        $val = $(this).val();
        if ($val.length > 0) {
            if ($val == 'CASH') {
                $('#row_dim3').show();
                
                $('#row_dim31').hide();
                $('#row_dim1').hide();
                $('.other_payment').hide();
                  $('.sfc_clam').hide();
            } else if ($val == 'CHQ') {
                $('#row_dim').show();
                $('#row_dim31').hide();
                $('#row_dim1').hide();
                $('.other_payment').hide();
                  $('.sfc_clam').hide();
            }
            else if ($val == 'SFC_SELF') 
            {
                $comp=$('#account_type').val();
                if($comp=="company")
                {
                     $('.sfc_clam').show();
                    $msg="SFC is not for Company";
                    disp_err("#sfc_clm",$msg);
                    $('.other_payment').hide();
                    $('#row_dim4').hide();
                }
                else
                {
                    remove_err('#sfc_clm');
                    $check_amt= $total_amount;
                    $sfc_amount = $.trim($('#sfc_amount').val());
                    
                    if(parseFloat($check_amt) != parseFloat($sfc_amount))
                    {
                        if(isNaN(parseFloat($sfc_amount)))
                        {
                            $('.other_payment').hide();
                            $('#row_dim4').hide();
                              $('.sfc_clam').hide();
                               $('#row_dim').hide();
                                $('#row_dim3').hide();
                                
                                $('#row_dim1').hide();
                                $('#row_dim4').hide();
                                $('#row_dim31').hide();
                        }
                        else
                        {
                            $('#row_dim4').show();
                            $('.other_payment').show();
                            $('.sfc_clam').hide();
                        }
                    }
                    else
                    {
                        $('.other_payment').hide();
                        $('.sfc_clam').hide();
                    }
                  
                }
            }
            else if ($val == 'SFC_ATO') 
            {
                $comp=$('#account_type').val();
                if($comp=="company")
                {
                     $('.sfc_clam').show();
                    $msg="SFC is not for Company";
                    disp_err("#sfc_clm",$msg);
                }
                else
                {
                   
                    $sfcato_amount = $.trim($('#sfcato_amount').val());
                    $check_amt= $total_amount;
                   
                    if(parseFloat($check_amt) != parseFloat($sfcato_amount))
                    {
                        
                            if(isNaN(parseFloat($sfcato_amount)))
                            {
                               
                                $('#row_dim5').hide();
                                $('.other_payment').hide();
                                
                                $('#row_dim').hide();
                                $('#row_dim3').hide();
                                
                                $('#row_dim1').hide();
                                $('#row_dim4').hide();
                                $('#row_dim31').hide();
                               
                            }
                            else
                            {
                                
                                $('#row_dim5').show();
                                $('.other_payment').show();
                                $('.sfc_clam').hide();
                                
                            }
                    }
                    else
                    {
                       
                         $('#row_dim31').hide();
                         $('#row_dim1').hide();
                         $('.other_payment').hide();
                         $('.sfc_clam').hide();
                    } 
                }
            }
        }
    });
      //sfc
    $('#payment_type1').change(function() {
        $('#row_dim31').hide();
        $('#row_dim1').hide();
       
        $val = $(this).val();
        if ($val.length > 0) {
            if ($val == 'CASH1') {
                $('#row_dim31').show();
            } else if ($val == 'CHQ1') {
                $('#row_dim1').show();
            } 
          
        }
    });
    //ends sfc
    keep_search_active();
});
function keep_search_active() {
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
    }
    $div.removeAttr('disabled');
}
function form_validate($retVal) {
    
    $('.refund_save').val('Yes');
    $account_type = $('#account_type').val();
    if ($account_type == 'company') {
        $check_amt = $company_total;
    } else {
        $check_amt = $total_amount;
    }
    
    $payment_type = $('#payment_type').val();
    $payment_type1 = $('#payment_type1').val();
    if ($payment_type.length == 0) {
        disp_err('#payment_type');
        $retVal = false;
    }
   
    else if ($payment_type == 'SFC_SELF') 
    {
        remove_err('#payment_type');
        $sfc_amount = $.trim($('#sfc_amount').val());
        $remaining_amt=$check_amt -$sfc_amount;
        if ($sfc_amount.length == 0) {
            $retVal = false;
            disp_err('#sfc_amount');
        } else if (isNaN($sfc_amount)) {
            disp_err('#sfc_amount', '[Invalid]');
            $retVal = false;
        } 
        else 
        {
                if (parseFloat($check_amt) != parseFloat($sfc_amount)) 
                {
                   
                // $('#other_payment').show();
                   $retVal = false;
                  //disp_err('#sfc_amount', '[Cash amount greater than refund amount.]');
                } else {
                    remove_err('#sfc_amount');
                }
            
        }
    } 
    else if ($payment_type == 'SFC_ATO') 
    {
        remove_err('#payment_type');
        $sfcato_amount = $.trim($('#sfcato_amount').val());
        $remaining_amt=$check_amt -$sfcato_amount;
        if ($sfcato_amount.length == 0) {
            $retVal = false;
            disp_err('#sfcato_amount');
        } else if (isNaN($sfcato_amount)) {
            disp_err('#sfcato_amount', '[Invalid]');
            $retVal = false;
        } else {
             
                if (parseFloat($check_amt) != parseFloat($sfcato_amount)) {
                    $retVal = false;
                    //disp_err('#sfcato_amount', '[Cash amount greater than refund amount.]');
                } else {
                    remove_err('#sfcato_amount');
                }
            
        }
    } 
    else if ($payment_type == 'CASH') 
    {
       
        remove_err('#payment_type');
        $cash_amount = $.trim($('#cash_amount').val());
        if ($cash_amount.length == 0) {
            $retVal = false;
            disp_err('#cash_amount');
        } else if (isNaN($cash_amount)) {
            disp_err('#cash_amount', '[Invalid]');
            $retVal = false;
        } else {
            if ($account_type == 'company') {
                if (parseFloat($check_amt) != parseFloat($cash_amount)) {
                    $retVal = false;
                    disp_err('#cash_amount', '[Cash amount not equal to refund amount.]');
                } else {
                    remove_err('#cash_amount');
                }
            } else {
              
                if (parseFloat($check_amt) < parseFloat($cash_amount)) {
                     
                    $retVal = false;
                    disp_err('#cash_amount', '[Cash amount greater than refund amount.]');
                } else {
                     
                    remove_err('#cash_amount');
                }
            }
        }
    } 
    else if ($payment_type == 'CHQ') {
        remove_err('#payment_type');
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
        } else {
            if ($account_type == 'company') {
                if (parseFloat($check_amt) != parseFloat($cheque_amount)) {
                    $retVal = false;
                    disp_err('#cheque_amount', '[Cheque amount not equal to refund amount.]');
                } else {
                    remove_err('#cheque_amount');
                }
            } else {
                if (parseFloat($check_amt) < parseFloat($cheque_amount)) {
                    $retVal = false;
                    disp_err('#cheque_amount', '[Cheque amount greater than refund amount.]');
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
    }
    //starts other payment for SFC
    if ($payment_type1 == 'CASH1') 
    {
      
        remove_err('#payment_type1');
        $cash_amount1 = $.trim($('#cash_amount1').val());
        if ($cash_amount1.length == 0) {
            $retVal = false;
            disp_err('#cash_amount1');
        } else if (isNaN($cash_amount1)) {
            disp_err('#cash_amount1', '[Invalid]');
            $retVal = false;
        } else {
            
                if (parseFloat($remaining_amt) < parseFloat($cash_amount1)) 
                {
            
                    $retVal = false;
                    disp_err('#cash_amount1', '[Cash amount greater than refund amount.]');
                } else {
                     
                     $retVal=true;
                     
                    remove_err('#cash_amount1');
                    remove_err('#sfc_amount');
                }
            
        }
    } 
    else if ($payment_type1 == 'CHQ1') {
        
        remove_err('#payment_type1');
        $cheque_number1 = $.trim($('#cheque_number1').val());
        if ($cheque_number1.length == 0) {
            $retVal = false;
            disp_err('#cheque_number1');
        } else {
            remove_err('#cheque_number1');
        }
        $cheque_amount1 = $.trim($('#cheque_amount1').val());
        if ($cheque_amount1.length == 0) {
            $retVal = false;
            disp_err('#cheque_amount1');
        } else if (isNaN($cheque_amount1)) {
            disp_err('#cheque_amount1', '[Invalid]');
            $retVal = false;
        } else {
        
                if (parseFloat($remaining_amt) < parseFloat($cheque_amount1)) {
                    $retVal = false;
                    disp_err('#cheque_amount1', '[Cheque amount greater than refund amount.]');
                } else {
                     $retVal=true;
                    remove_err('#cheque_amount1');
                }
           
        }
        $cheque_date1 = $('#cheque_date1').val();
        if ($cheque_date1.length == 0) {
            $retVal = false;
            disp_err('#cheque_date1');
        } else {
            remove_err('#cheque_date1');
        }
        $bank_name1 = $.trim($('#bank_name1').val());
        if ($bank_name1.length == 0) {
            $retVal = false;
            disp_err('#bank_name1');
        } else {
            remove_err('#bank_name1');
        }
    }
    $refund_date = $('#refund_date').val();
    if ($refund_date.length == 0) {
        $retVal = false;
        disp_err('#refund_date');
    } else {
        remove_err('#refund_date');
    }
    //ends other payment for SFC
    $display_check = $('#ex5').css('display');
    if ($retVal == true && $display_check == 'none') {
        $('#ex5').modal();
        return false;
    }
    $refund_reason = $('#refund_reason').val();
    if ($refund_reason.length == 0) {
        $retVal = false;
        disp_err('#refund_reason');
    } else {
        remove_err('#refund_reason');
    }
    if ($refund_reason == 'OTHERS') {
        $other_reason = $('#other_reason').val();
        if ($other_reason.length == 0) {
            $retVal = false;
            disp_err('#other_reason');
        } else {
            remove_err('#other_reason');
        }
    }

    if($retVal == true){

       
        $('.refund_save[type=submit]').css('display','');
        $('.refund_save').attr("disabled","disabled");
        $('.no_refund').hide();
       // $('.refund_save').css("background-color","gray");
        $('.refund_save').val('Please Wait..');
    }
    return $retVal;
}
function disp_err($id, $text) {
    $text = typeof $text !== 'undefined' ? $text : '[required]';
    $($id).addClass('error');
    $($id + '_err').addClass('error').addClass('error3').text($text);
}
function remove_err($id) {
    $($id).removeClass('error');
    $($id + '_err').removeClass('error').removeClass('error3').text('');
}
function remove_all() {
    $('.error3').text('').removeClass('error3');
    $('.amountdetails_div .error').removeClass('error');
}
function reset_all() {
    $('.amountdetails_div input,.amountdetails_div select').val('');
    $('#payment_type').trigger('change');
    $('#payment_type1').trigger('change');
}
function remove_all_err(id){
     $('.error3').text('').removeClass('error3');
     $(id + ' .error').removeClass('error'); 
}

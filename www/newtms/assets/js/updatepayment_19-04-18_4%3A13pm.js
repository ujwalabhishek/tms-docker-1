/**
 * This js file includes in updatepayment page
 */
var $total_amount = 0;
var $company_total = 0;
var $total_inv_amount = 0;
$(document).ready(function() {
    $('#account_type').val('individual');
    $('#gyap').css('display', 'none');
    $('#taxcode').attr('disabled', 'disabled');
    $('#trainee').attr('disabled', 'disabled');
    $('#account_type').change(function() {
        $('.companyamounts_display').hide();
        $('.amountdetails_div').hide();
        $('.trainee_div').hide();
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
            $url = $siteurl + "class_trainee/get_company_notpaid_invoice";
            $.ajax({
                url: $url,
                type: "post",
                async: false,
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
    $('#excess_ok').click(function() {
        $('#updatepaymentform')[0].submit();
    });
    $("#invoice, #company_invoice").autocomplete({
        source: function(request, response) {
            if ($('#account_type').val() == 'company') {
                $url = $siteurl + "class_trainee/get_company_notpaid_invoice";
                $company = $('#company').val();
            } else {
                $url = $siteurl + "accounting/get_notpaid_invoice";
                $company = '';
            }
            if ($('#account_type').val() == 'company') {
                $('#company_invoice_id').val('');
            } else {
                $('#invoice_id').val('');
            }
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
            if ($('#account_type').val() == 'company') {
                $('#company_invoice_id').val(id);
            } else {
                $('#invoice_id').val(id);
            }
        }
    });
    $("#taxcode").autocomplete({
        source: function(request, response) {
            $('#taxcode').val($('#taxcode').val().toUpperCase())
            $('#taxcode_id').val('');
            $.ajax({
                url: $siteurl + "class_trainee/get_notpaid_taxcode",
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
                url: $siteurl + "class_trainee/get_notpaid_trainee",
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
    $('.search_select').change(function() {
        $val = $('.search_select:checked').val();
        $('#invoice').val('');
        $('#invoice_id').val('');
        $('#taxcode').val('');
        $('#taxcode_id').val('');
        $('#trainee').val('');
        $('#trainee_id').val('');
        $('#invoice').attr('disabled', 'disabled');
        $('#invoice_id').attr('disabled', 'disabled');
        $('#taxcode').attr('disabled', 'disabled');
        $('#trainee').attr('disabled', 'disabled');
        if ($val == 1) {
            $div = $('#invoice_id');
        } else if ($val == 2) {
            $div = $('#taxcode');
        } else if ($val == 3) {
            $div = $('#trainee');
        }
        $div.removeAttr('disabled');
    })
    $('.print_company_invoice').click(function() {
        remove_err('#without_subsidy');
        remove_err('#with_subsidy');
        remove_err('#foreigner_invoice');
        $val = $('.select_invoice_print:checked').val();
        $pymnt_due_id = $('#payment_due_id').val();
        $company_invoice_id = $('#company_invoice_id').val();
        if ($val == 1) {
            location.replace($baseurl + 'class_trainee/export_company_generate_invoice/' + $pymnt_due_id);
        } 
        else if ($val == 2 || $val == 3) 
        {
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
            if ($count == 0) 
            {
                if ($val == 3) 
                {
                    disp_err('#without_subsidy', '[No trainees found in this invoice without subsidy.]');
                } 
                else 
                {
                    disp_err('#with_subsidy', '[No trainees found in this invoice with subsidy.]');
                }
            } 
            else 
            {
                location.replace($baseurl + 'class_trainee/gen_inv_pdf_basedon_subsidy/' + $company_invoice_id + '/' + $subsidy);
            }
        }
        else if ($val == 4) 
        {
            $count = 0;
            $subsidy =  0;
            $.ajax({
                url: $baseurl + "class_trainee/get_count_company_invoice_foreigner",
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
            if ($count == 0) 
            {
               
                    disp_err('#foreigner_invoice', '[No Forneigner trainees found in this invoice.]');
            } 
            else 
            {
                //alert($company_invoice_id+"/"+$subsidy);
                location.replace($baseurl + 'class_trainee/gen_inv_pdf_basedon_forgeigner/' + $company_invoice_id + '/' + $subsidy);
            }
        }
        return false;
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
                $('.p_invoice_class_name').html(data.class_name);
                $('.p_invoice_company_name').html(data.company_name);
                $('.p_invoice_course_name').html(data.crse_name);
                $('.p_invoice_id').html(data.invoice_id);
                $('.p_invoice_dated').html(data.inv_date);
                $('.p_invoice_amount').html(parseFloat(data.total_inv_amount).toFixed(2));
                $('.p_invoice_discount_label').html(data.discount_label);
                $('.p_invoice_discount_rate').html(parseFloat(data.discount_rate).toFixed(2));
                $('.p_invoice_discount_amount').html(parseFloat(data.total_inv_discnt).toFixed(2));
                $('.p_invoice_subsidy_amount').html(parseFloat(data.total_inv_subsdy).toFixed(2));
                $('.p_invoice_gst_label').html(data.gst_label);
                $('.p_invoice_gst_rate').html(parseFloat(data.gst_rate).toFixed(2));
                $('.p_invoice_total_gst').html(parseFloat(data.total_gst).toFixed(2));

                var recd = res.recd;
                $payrcd_table = $('.payment_received tbody')
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
                    $html = '<tr><td>&nbsp;</td><td><strong>Total: </strong></td><td><strong>$ ' + parseFloat($total_recd).toFixed(2) + '</strong></td></tr>'
                    $payrcd_table.append($html);
                    $('.company_print_pdf').attr('href', $baseurl + 'class_trainee/export_payment_received/' + data.pymnt_due_id);
                    $('.company_print_pdf').show();
                } else {
                    $('.company_print_pdf').hide();
                    $html = '<tr><td class="error" colspan="3">There is no payment available.</td></tr>';
                    $payrcd_table.append($html);
                }
                $('#ex3').modal();
            }
        });
    });
    $('.search_button').click(function() {
        $account_type = $('#account_type').val();
        $invoice_id = '';
        $taxcode_id = '';
        $trainee_id = '';
        $search_select = $('.search_select:checked').val();
        remove_err('#company');
        remove_err('#invoice');
        remove_err('#company_invoice');
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
                } else if ($trainee.length == 0) {
                    disp_err('#trainee', '[Select Trainee Name from auto-help]');
                } else {
                    trigger_ajax($invoice_id, $taxcode_id, $trainee_id);
                }
            }
        }
    });
    function trigger_companyajax($invoice_id, $company) {
        $.ajax({
            url: $baseurl + 'class_trainee/get_company_updatepayment',
            type: 'post',
            dataType: 'json',
            data: {
                invoice: $invoice_id,
                company: $company
            },
            beforeSend: function() {

            },
            success: function(data) {
                remove_all();
                check = 0;
                reset_all();
                $('.comp_inv_data').html($('#company_invoice_id option[value="' + $invoice_id + '"]').html());
                $('.c_invoice_course_name').html(data.crse_name);
                $('.c_invoice_class_name').html(data.class_name);
                $('.c_invoice_class_fees').html(parseFloat(data.total_unit_fees).toFixed(2));
                $('.c_invoice_discount_amount').html(parseFloat(data.total_inv_discnt).toFixed(2));
                if(data.total_inv_subsdy!=null && data.total_inv_subsdy!='')
                 $('.c_invoice_subsidy_amount').html(parseFloat(data.total_inv_subsdy).toFixed(2)); 
                else
                 $('.c_invoice_subsidy_amount').html('0.00');
                 

                $('.c_invoice_discount_rate').html(parseFloat(data.discount_rate).toFixed(2));
                $('.c_invoice_discount_label').html(data.discount_label);
                $('.c_invoice_gst_label').html(data.gst_label);
                $('.c_invoice_gst_rate').html(parseFloat(data.gst_rate).toFixed(2));
                $('.c_invoice_total_gst').html(parseFloat(data.total_gst).toFixed(2));
                $total_inv_amount = parseFloat(data.total_inv_amount).toFixed(2);
                $('.c_invoice_net_due').html(parseFloat(data.total_inv_amount).toFixed(2));
                $('.company_payment_recd_view').attr('data-invoice', $invoice_id);
                $('#payment_due_id').val(data.pymnt_due_id);
                $('.ctrainees_tbody').empty();
                var trainee = data.trainees;
                var cnt = 1;
                $company_total = 0;
//                alert("company total1="+$company_total);
                $company_total_due = 0;
                $company_total_paid = 0;
                $.each(trainee, function(i, item) {
                    $extra_name = '';
                    if (data.class_name != item.class_name) {
                        $extra_name = '<span class="green"> **</span><br><span class="green">(' + item.class_name + ')</span>';
                    }
                    $name = 'trainee[' + item.user_id + ']';
                    if (item.payment_status == 'NOTPAID') {
                        $status = '<span class="error">Not Paid</span>';
                        $payment_progress = '<span class="actual_pay_' + item.user_id + '">$ ' + parseFloat(item.amount_remain).toFixed(2) + '</span>\
                                        <input type="hidden" value="' + parseFloat(item.amount_remain).toFixed(2) + '" name="amount_check[' + item.user_id + ']"/>\
                                        <span style="display:none;" class="actual_payenter_' + item.user_id + '">$<input type="text" value="' + parseFloat(item.amount_remain).toFixed(2) + '" name="amount_paying[' + item.user_id + ']" class="amount_paying"/></span>';

                        $paid_status = '<input type="radio" class="c_trainee" checked="checked" name="' + $name + '" value="1">Full &nbsp; \
                                        <input type="radio" class="c_trainee" name="' + $name + '" value="2">Part &nbsp; \
                                        <input type="radio" class="c_trainee" name="' + $name + '" value="3">No Payment &nbsp; ';
                    } else if (item.payment_status == 'PARTPAID') {
                        $status = '<span class="error">Paid Part</span>';
                        $payment_progress = '<span style="display:none;" class="actual_pay_' + item.user_id + '">$ ' + parseFloat(item.amount_remain).toFixed(2) + '</span>\
                                        <input type="hidden" value="' + parseFloat(item.amount_remain).toFixed(2) + '" name="amount_check[' + item.user_id + ']"/>\
                                        <span class="actual_payenter_' + item.user_id + '">$<input type="text" value="' + parseFloat(item.amount_remain).toFixed(2) + '" name="amount_paying[' + item.user_id + ']" class="amount_paying"/></span>';

                        $paid_status = '<input type="radio" class="c_trainee" name="' + $name + '" value="1">Full &nbsp; \
                                        <input type="radio" class="c_trainee" checked="checked"  name="' + $name + '" value="2">Part &nbsp; \
                                        <input type="radio" class="c_trainee" name="' + $name + '" value="3">No Payment &nbsp; ';
                    } else if (item.payment_status == 'PAID') {
                        $status = '<span style="color:green;">Paid Full</span>';
                        $payment_progress = '<span class="actual_pay_' + item.user_id + '">$ ' + parseFloat(item.amount_remain).toFixed(2) + '</span>\
                                        <input type="hidden" value="' + parseFloat(item.amount_remain).toFixed(2) + '" name="amount_check[' + item.user_id + ']"/>\
                                        <span style="display:none;" class="actual_payenter_' + item.user_id + '">$<input type="text" value="' + parseFloat(item.amount_remain).toFixed(2) + '" name="amount_paying[' + item.user_id + ']" class="amount_paying"/></span>';
                        $paid_status = '<span style="color:green;">No Payment Due.</span>';
                    }
                    $user_refunded = typeof data['user_refunded'][item.user_id] == 'undefined' ? 0 : data['user_refunded'][item.user_id];
                    $html = '<tr>\
                                    <td>' + cnt + '<input name="trainee_selected[]" style="display:none;" class="trainee_selected" value="' + item.user_id + '" checked="checked" type="checkbox" /></td>\
                                    <td>' + item.first + $extra_name + '<br/>(' + item.tax_code + ')</td>\
                                    <td>$ ' + parseFloat(item.total_amount_due).toFixed(2) + '</td>\
                                    <td>$ ' + parseFloat(item.amount_paid).toFixed(2) + '</td>\
                                    <td>$ ' + parseFloat($user_refunded).toFixed(2) + '</td>\
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
                    $company_total = $company_total + +item.amount_remain;
         
                });
                if (cnt > 1) {
                    $company_total = parseFloat($company_total).toFixed(2);
                    $company_total_due = parseFloat($company_total_due).toFixed(2);
                    $company_total_paid = parseFloat($company_total_paid).toFixed(2);
                    if ($company_total_due != $total_inv_amount) {
                        $company_total_due = $total_inv_amount;
                    }
                    $html = '<tr>\
                                <td colspan="2"></td>\
                                <td align="right"><strong>Total: </strong></td>\
                                <td><strong>$ ' + $company_total_due + '</strong></td>\
                                <td><strong>$ ' + $company_total_paid + '</strong></td>\
                                <td><strong><span style="color:blue;">$ <span class="c_trainee_pay_total">' + $company_total + '</span></span></strong></td>\
                                <td></td>\
                                <td></td>\
                            </tr>';
                    $('.ctrainees_tbody').append($html);
                    $('.c_total_received').html($company_total_paid);

                    $('.c_current_due').html(parseFloat($company_total).toFixed(2));
                }
                $('.companyamounts_display').show();
                if (parseFloat($company_total) > 0) {
                    $('.common_pay').show();
                } else {
                    $('.common_pay').hide();
                }
            }
        })
    }
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
            url: $siteurl + "class_trainee/search_trainee_updatepayment",
            type: "post",
            async: false,
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

                $(".trainee_div").show();
                var data = res.data;
                if (data != '') {
                    $('.trainee_table').show();
                    $.each(data, function(i, item) {
                        $html = '<tr>\
                                        <td><input type="radio" class="trainee_invoice"  name="trainee_invoice" value="' + item.payid + '"/></td>\
                                        <td>' + item.first + '</td>\
                                        <td>' + item.taxcode + '</td>\
                                        <td>' + item.crse_name + ' / ' + item.class_name + '</td>\
                                        <td><span style="color:blue;">$ ' + parseFloat(item.amountdue).toFixed(2) + '</span></td>\
                                        <td><span class="error">Pay in Full<span></td>\
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
            $('#payment_due_id').val($payid);
            $.ajax({
                url: $siteurl + "class_trainee/get_enroll_invoice",
                type: "post",
                dataType: "json",
                data: {
                    'payid': $payid,
                },
                beforeSend: function() {
                    $('.payment_recd_view').removeClass('payment_recd_banned');
                },
                success: function(res) {
                    remove_all();
                    check = 0;
                    reset_all();

                    if (res.label == 'inactive') {
                        $('.payment_recd_view').addClass('payment_recd_banned');
                    }
                    $('.amountdetails_div').show();
                    var data = res.data;
                    if (data != null) {
                        $('.alert_invoice_id').html(data.invoice_id);
                        $('.invoice_class_name').html(data.class_name);
                        $('.invoice_course_name').html(data.crse_name);
                        $('.invoice_class_fees').html(parseFloat(data.total_unit_fees).toFixed(2));
                        $('.invoice_discount_label').html(data.discount_label);
                        $('.invoice_discount_rate').html(parseFloat(data.discount_rate).toFixed(2));
                        $('.invoice_discount_amount').html(parseFloat(data.total_inv_discnt).toFixed(2));
                        $('.invoice_subsidy_amount').html(parseFloat(data.total_inv_subsdy).toFixed(2));
                        $('.invoice_subsidy_type').html(res.subsidy_type_label);
                        $('.invoice_gst_rate').html(parseFloat(data.gst_rate).toFixed(2));
                        $('.invoice_total_gst').html(parseFloat(data.total_gst).toFixed(2));
                        $('.invoice_net_due').html(parseFloat(data.total_inv_amount).toFixed(2));
                        $('.invoice_gst_label').html(data.gst_label);
                        $total_amount = data.total_inv_amount;
                        $('.print_pdf').attr('href', $baseurl + 'class_trainee/export_payment_received/' + data.pymnt_due_id);
                        $('.view_invoice_blocked').attr('href', $baseurl + 'class_trainee/export_generate_invoice/' + data.pymnt_due_id);
                        $('.invoice_footer_text').html(data.invoice_footer_text);
                    }
                    var recd = res.recd;
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
                }
            });
        }
    });
    $(".sfc_amount").keyup(function(){
       $chk_amount = parseFloat($total_amount);
       
        $sfc_amount = $.trim($('#sfc_amount').val());
        if ($chk_amount != parseFloat($sfc_amount)) {
            $('#other_payment').show();
        } 
        else {
           $('#other_payment').hide();
        }
    });
    $(".sfcato_amount").keyup(function(){
       $chk_amount = parseFloat($total_amount);
       
        $sfcato_amount = $.trim($('#sfcato_amount').val());
        if ($chk_amount != parseFloat($sfcato_amount)) {
            $('#other_payment').show();
        } 
        else {
           $('#other_payment').hide();
        }
    });

    $("#cash_amount,#cheque_amount,#sfc_amount,#sfcato_amount").keydown(function(e) {
       
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    
    $(document).on('click', '.payment_recd_banned', function() {
        $('#ex12').modal();
        return false;
    });
    $('.view_invoice_blocked').click(function() {
    });
    $('#bank_name,#cheque_number,#gbank_name').keyup(function() {
        var raw_text = jQuery(this).val();
        var return_text = raw_text.replace(/[^a-zA-Z0-9 _.]/g, '');
        $(this).val(return_text);
    });
    $('#bank_name,#cheque_number,#gbank_name').keyup(function() {
        $(this).val(this.value.toUpperCase());
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
    $("#cashpaid_on, #paid_on, #transc_on,#sfcclaim_on,#sfcatoclaim_on,#cashpaid_on1,#paid_on1, #transc_on1").datepicker({
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
        $('#giro_div').hide();
        $('#sfc_div').hide();
        $('#sfcato_div').hide();
        $val = $(this).val();
        if ($val.length > 0) {
            if ($val == 'CASH') {
                $('#row_dim3').show();
                $('#row_dim31').hide();
                $('#row_dim1').hide();
                $('#giro_div1').hide();
                $('.other_payment').hide();
                $('.sfc_clm').hide();
                 remove_err('#sfc_claim');
            } else if ($val == 'CHQ') {
                $('#row_dim').show();
                 $('#row_dim31').hide();
                $('#row_dim1').hide();
                $('#giro_div1').hide();
                $('.other_payment').hide();
                $('.sfc_clm').hide();
                 remove_err('#sfc_claim');
            } else if ($val == 'GIRO') {
                $('#giro_div').show();
                 $('#row_dim31').hide();
                $('#row_dim1').hide();
                $('#giro_div1').hide();
                $('.other_payment').hide();
                $('.sfc_clm').hide();
                 remove_err('#sfc_claim');
            }
            else if($val == 'SFC_SELF'){
               $comp= $('#account_type').val();
               
                if($comp=="company")
                {
                    $msg="SFC can not be claimed for company.";
                    disp_err('#sfc_claim', $msg);
                    $('#sfc_div').hide();
                    $('#row_dim31').hide();
                    $('#row_dim1').hide();
                    $('#giro_div1').hide();
                    $('.other_payment').hide();
                    $('.sfc_clm').show();
                }
                else
                {
                    remove_err('#sfc_claim');
                    $('#sfc_div').show();
                    $('#row_dim31').hide();
                    $('#row_dim1').hide();
                    $('#giro_div1').hide();
                    $('.other_payment').hide();
                    $('.sfc_clm').hide();
               }
            }
            else if($val == 'SFC_ATO')
            {
                  $comp= $('#account_type').val();
               
                if($comp=="company")
                {
                    $msg="SFC can not be claimed for company.";
                    disp_err('#sfc_claim', $msg);
                $('#sfcato_div').hide();
                $('#row_dim31').hide();
                $('#row_dim1').hide();
                $('#giro_div1').hide();
                $('.other_payment').hide();
                 $('.sfc_clm').show();
                }
                else
                {
                     remove_err('#sfc_claim');
                    $('#sfcato_div').show();
                    $('#row_dim31').hide();
                    $('#row_dim1').hide();
                    $('#giro_div1').hide();
                    $('.other_payment').hide();
                    $('.sfc_clm').hide();
                }
            }
                
        }
    });
    //sfc
    $('#payment_type1').change(function() {
        $('#row_dim31').hide();
        $('#row_dim1').hide();
        $('#giro_div1').hide();
       
        $val = $(this).val();
        if ($val.length > 0) {
            if ($val == 'CASH1') {
                $('#row_dim31').show();
            } else if ($val == 'CHQ1') {
                $('#row_dim1').show();
            } else if ($val == 'GIRO1') {
                $('#giro_div1').show();
            }
          
        }
    });
   
    //ends sfc
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
          //  return form_validate(false);
        }
    });
});
  
function form_validate($retVal) {
    $payment_type = $('#payment_type').val();
    $payment_type1 = $('#payment_type1').val();
    $account_type = $('#account_type').val();
    if ($account_type == 'company') {
        $chk_amount = parseFloat($company_total);
        $msg = '[Total recd. amount less than amount due OR there is a part payment with excess recd.]';
        $check_excess = 1;
        $check_full = 1;
        $('.trainee_selected:checked').each(function() {
            $user_id = $(this).val();
            $check = $('input[name="trainee[' + $user_id + ']"]:checked').val();
            if ($check == 3) {
                $check_full = 2;
            } else {
                $paying = $('input[name="amount_paying[' + $user_id + ']"]').val();
                $checking = $('input[name="amount_check[' + $user_id + ']"]').val();
                if (parseFloat($paying) != parseFloat($checking)) {
                    $check_full = 2;
                }
            }
        });
    } else {
        $chk_amount = parseFloat($total_amount);
        $msg = '[net due and recd. amount not equal.]';
        $check_excess = 0;
    }
    if ($payment_type.length == 0) {
        disp_err('#payment_type');
        $retVal = false;
    }
    
    
    else if ($payment_type == 'SFC_SELF') 
    {
        remove_err('#payment_type');
        $sfcclaim_on = $('#sfcclaim_on').val();
        if ($sfcclaim_on.length == 0) 
        {
            $retVal = false;
            disp_err('#sfcclaim_on');
        } 
        else 
        {
            remove_err('#sfcclaim_on');
        }
        $sfc_amount = $.trim($('#sfc_amount').val());
        
        $p_paid = parseFloat($sfc_amount);
       
        $remaining_amt=$chk_amount - parseFloat($sfc_amount);
        $remaining_amt=parseFloat($remaining_amt).toFixed(2);
       
        if ($sfc_amount.length == 0) 
        {
            $retVal = false;
            disp_err('#sfc_amount');
           
        } 
        else if (isNaN($sfc_amount)) 
        {
            disp_err('#sfc_amount', '[Invalid]');
            $retVal = false;
           
        } 
        else 
        {
            if ($check_excess == 1) 
            {   
                
                if (($check_full == 2) && ($chk_amount != parseFloat($sfc_amount))) 
                {
                    $('#row_dim').show();
                    $retVal = false;
                    disp_err('#sfc_amount', $msg);
                } 
                else if (($check_full == 1) && ($chk_amount > parseFloat($sfc_amount))) 
                {
                       $('#row_dim').show();
                    $retVal = false;
                    disp_err('#sfc_amount', $msg);
                } 
                else 
                {
                    remove_err('#sfc_amount');
                }
            } 
            else 
            {
                
                if ($chk_amount != parseFloat($sfc_amount)) 
                {
                    $('#other_payment').show();
                    $retVal = false;
                    //disp_err('#sfc_amount', $msg);
                } 
                else 
                {
                    
                     $('#other_payment').hide();
                    remove_err('#sfc_amount');
                }
            }
        }
    }
    else if ($payment_type == 'SFC_ATO') 
    {
        remove_err('#payment_type');
        $sfcatoclaim_on = $('#sfcatoclaim_on').val();
        if ($sfcatoclaim_on.length == 0) 
        {
            $retVal = false;
            disp_err('#sfcatoclaim_on');
        } 
        else 
        {
            remove_err('#sfcatoclaim_on');
        }
        $sfcato_amount = $.trim($('#sfcato_amount').val());
        
        $p_paid = parseFloat($sfcato_amount);
        $remaining_amt=$chk_amount - parseFloat($sfcato_amount);
        $remaining_amt=parseFloat($remaining_amt).toFixed(2);
        if ($sfcato_amount.length == 0) 
        {
            $retVal = false;
            disp_err('#sfcato_amount');
           
        } 
        else if (isNaN($sfcato_amount)) 
        {
            disp_err('#sfcato_amount', '[Invalid]');
            $retVal = false;
           
        } 
        else 
        {
            if ($check_excess == 1) 
            {   
                
                if (($check_full == 2) && ($chk_amount != parseFloat($sfcato_amount))) 
                {
                    
                    $retVal = false;
                    disp_err('#sfcato_amount', $msg);
                } 
                else if (($check_full == 1) && ($chk_amount > parseFloat($sfcato_amount))) 
                {
                      
                    $retVal = false;
                    disp_err('#sfcato_amount', $msg);
                } 
                else 
                {
                    remove_err('#sfcato_amount');
                }
            } 
            else 
            {
                
                if ($chk_amount != parseFloat($sfcato_amount)) 
                {
                    $('#other_payment').show();
                    $retVal = false;
                    //disp_err('#sfcato_amount', $msg);
                } 
                else 
                {
                   remove_err('#sfcato_amount');
                }
            }
        }
    }
    else if ($payment_type == 'CASH') {
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
    //payment options for SFC 
    if ($payment_type1 == 'CASH1') 
    {
       
        remove_err('#payment_type1');
        $cashpaid_on1 = $('#cashpaid_on1').val();
        if ($cashpaid_on1.length == 0) {
            $retVal = false;
            disp_err('#cashpaid_on1');
        } else {
            remove_err('#cashpaid_on1');
        }
        $cash_amount1 = $.trim($('#cash_amount1').val());
        $p_paid = parseFloat($cash_amount1);
        if ($cash_amount1.length == 0) {
           
            $retVal = false;
            disp_err('#cash_amount1');
        } else if (isNaN($cash_amount1)) {
            disp_err('#cash_amount1', '[Invalid]');
            $retVal = false;
        } 
        else 
        {
            
            if ($check_excess == 1) 
            {
                
                if (($check_full == 2) && ($chk_amount != parseFloat($cash_amount1))) 
                {
                    $retVal = false;
                    disp_err('#cash_amount1', $msg);
                } 
                else if (($check_full == 1) && ($chk_amount > parseFloat($cash_amount1))) 
                {
                    $retVal = false;
                    disp_err('#cash_amount1', $msg);
                } 
                else 
                {
                    remove_err('#cash_amount1');
                }
            } 
            else 
            {
                $net_amount=$remaining_amt+parseFloat($cash_amount1);
                $cash_amount1=parseFloat($cash_amount1).toFixed(2);
                //alert($remaining_amt+"="+$cash_amount1);
                if ($remaining_amt != $cash_amount1) 
                {
                   
                    $retVal = false;
                    disp_err('#cash_amount1', $msg);
                } 
                else 
                {
                    $retVal=true;
                    
                    remove_err('#cash_amount1');
                    remove_err('#sfc_amount');
                    
                    
                }
            }
        }
    }
     else if ($payment_type1 == 'CHQ1') {
        remove_err('#payment_type1');
        $paid_on1 = $('#paid_on1').val();
        if ($paid_on1.length == 0) {
            $retVal = false;
            disp_err('#paid_on1');
        } else {
            remove_err('#paid_on1');
        }
        $cheque_number1 = $.trim($('#cheque_number1').val());
        if ($cheque_number1.length == 0) {
            $retVal = false;
            disp_err('#cheque_number1');
        } else {
            remove_err('#cheque_number1');
        }
        $cheque_amount1 = $.trim($('#cheque_amount1').val());
        $p_paid = parseFloat($cheque_amount1);
        if ($cheque_amount1.length == 0) {
            $retVal = false;
            disp_err('#cheque_amount1');
        } else if (isNaN($cheque_amount1)) {
            disp_err('#cheque_amount1', '[Invalid]');
            $retVal = false;
        } else {
            if ($check_excess == 1) {
                if (($check_full == 2) && ($chk_amount != parseFloat($cheque_amount1))) {
                    $retVal = false;
                    disp_err('#cheque_amount1', $msg);
                } else if (($check_full == 1) && ($chk_amount > parseFloat($cheque_amount1))) {
                    $retVal = false;
                    disp_err('#cheque_amount1', $msg);
                } else {
                    remove_err('#cheque_amount1');
                }
            } else {
                $cheque_amount1=parseFloat($cheque_amount1).toFixed(2);
                if ($remaining_amt != $cheque_amount1) {
                    $retVal = false;
                    disp_err('#cheque_amount1', $msg);
                   
                } else {
                    $retVal=true;
                    remove_err('#cheque_amount1');
                   
                }
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
    
    
    else if ($payment_type1 == 'GIRO1') {
        remove_err('#payment_type1');
        $transc_on1 = $('#transc_on1').val();
        if ($transc_on1.length == 0) {
            $retVal = false;
            disp_err('#transc_on1');
        } else {
            remove_err('#transc_on1');
        }
        $gbank_name1 = $.trim($('#gbank_name1').val());
        if ($gbank_name1.length == 0) {
            $retVal = false;
            disp_err('#gbank_name1');
        } else {
            remove_err('#gbank_name1');
        }
        $giro_amount1 = $.trim($('#giro_amount1').val());
        $p_paid = parseFloat($giro_amount1);
        if ($giro_amount1.length == 0) {
            $retVal = false;
            disp_err('#giro_amount1');
        } else if (isNaN($giro_amount1)) {
            disp_err('#giro_amount1', '[Invalid]');
            $retVal = false;
        } else {
            if ($check_excess == 1) {
                if (($check_full == 2) && ($chk_amount != parseFloat($giro_amount1))) {
                    $retVal = false;
                    disp_err('#giro_amount1', $msg);
                } else if (($check_full == 1) && ($chk_amount > parseFloat($giro_amount1))) {
                    $retVal = false;
                    disp_err('#giro_amount1', $msg);
                } else {
                    remove_err('#giro_amount1');
                }
            } else {
                 $giro_amount1=parseFloat($giro_amount1).toFixed(2);
                if ($remaining_amt != $giro_amount1) {
                    $retVal = false;
                    disp_err('#giro_amount1', $msg);
                } else {
                     $retVal=true;
                    remove_err('#giro_amount1');
                   
                   
                }
            }
        }
    }
    //end other paymnet option 
    if ($retVal == true) 
    {
        if ($check_excess == 1 && $check_full == 1) {
            if ($chk_amount < parseFloat($p_paid)) {
                $total_amount = parseFloat($p_paid - $chk_amount).toFixed(2);
                $('.excess_amount').html($total_amount);
                $('#ex101').modal();
                return false;
            }
        }
    }
    if($retVal == true){
        $('.button_class99 button[type=submit]').css('display','none');
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
    $payment_due_id = $('#payment_due_id').val();
    $payment_due_id1 = $('#payment_due_id1').val();
    $('.amountdetails_div input,.amountdetails_div select').val('');
    $('#payment_due_id').val($payment_due_id);
    $('#payment_due_id1').val($payment_due_id1);
    $('#payment_type').trigger('change');
    $('#payment_type1').trigger('change');
}
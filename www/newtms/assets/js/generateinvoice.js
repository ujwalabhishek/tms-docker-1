/**
 * This js file includes in generate invoice page
 */
$iinvd_date_check = '';
$invd_date_check = '';
$company_id = '';
$class_id = '';
$total_unit_fees = '';
$total_indi_unit_fees='';
$gst_indi_subsidy_afterbefore='';
$gst_indi_on = '';
$total_trainees = '';
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
            $('#payment_status').html('<option value="">All</option><option value="PAID">Paid</option>\
            <option value="PARTPAID">Part Paid</option>\
            <option value="NOTPAID">Not Paid</option>');
            $('#company_invoice_id').html('<option value="">Select</option>');
            $('.company_td').show();
            $('.row_dimm9').hide();
        } else {
            $('#payment_status').html('<option value="">All</option><option value="PAID">Paid</option>\
            <option value="NOTPAID">Not Paid</option>');
            $('#payment_status').trigger('change');
            $('#invoice_id').html('<option value="">Select</option>');
            $('.company_td').hide();
            $('.row_dimm9').show();
        }
    });
    $("#invd_date, #iinvd_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
        maxDate: 0
    });
    $("#invd_date, #iinvd_date").change(function() {
        remove_err('#invd_date');
        remove_err('#iinvd_date');
    });
    $('#company').change(function() {
        $company = $('#company').val();
        $('#company_invoice_id').html('<option value="">Select</option>');
        $('#company_invoice_id').attr('disabled', 'disabled');
        if ($company.length > 0) { 
             $url = $siteurl + "class_trainee/get_company_all_invoice";
            $.ajax({
                url: $url,
                type: "post",
                dataType: "json",
                data: {
                    company: $company,
                    paid: $('#payment_status').val()
                },
                success: function(data) {
                    if (data != '') {
                        remove_err('#company_invoice');
                        $.each(data, function(i, item) {
                            $('#company_invoice_id').append('<option value="' + item.key + '">' + item.label + '</option>');
                        });
                        $('#company_invoice_id').removeAttr('disabled');
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
    $("#company_invoice").autocomplete({
        source: function(request, response) {
            $url = $siteurl + "class_trainee/get_company_all_invoice";
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
        },
        minLength:0
    });
    $('#taxcode').attr('disabled', 'disabled');
    $('#trainee').attr('disabled', 'disabled');
    $('.search_select').change(function() {
        $val = $('.search_select:checked').val();
        $('#invoice').val('');
        $('#invoice_id').val('');
        $('#invoice_no').val(''); ////added by shubhranshu///
        $('#invoice_no_id').val(''); ////added by shubhranshu///
        $('#taxcode').val('');
        $('#taxcode_id').val('');
        $('#trainee').val('');
        $('#trainee_id').val('');
        $('#invoice_id').attr('disabled', 'disabled');
        $('#invoice_no').attr('disabled', 'disabled');////added by shubhranshu///
        $('#taxcode').attr('disabled', 'disabled');
        $('#trainee').attr('disabled', 'disabled');
        if ($val == 1) {
            $div = $('#invoice_id');
            $div = $('#invoice_no');////added by shubhranshu///
        } else if ($val == 2) {
            $div = $('#taxcode');
        } else if ($val == 3) {
            $div = $('#trainee');
        }
        $div.removeAttr('disabled');
        remove_all_err('#search_form');
    });
    $("#invoice").autocomplete({
        source: function(request, response) {
            $('#invoice_id').val('');
            $.ajax({
                url: $siteurl + "accounting/get_invoice",
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
        },
        minLength:0
    });
    // added by shubhranshu////////////
    $("#invoice_no").autocomplete({
        source: function(request, response) {
//            $('#taxcode_id').val('');
            if (request.term.trim().length > 0) {
                $.ajax({
                    url: $siteurl + "class_trainee/get_invoice",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term,
                        paid: $('#payment_status').val(),
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            }else{
                var d;
                response(d);
            }
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#invoice_no').val(id);
            $('#invoice_no_id').val(id);
            remove_all_err('#search_form');
        },
        minLength:4
    });// added by shubhranshu////////////
    $("#taxcode").autocomplete({
        source: function(request, response) {
            $('#taxcode_id').val('');
            if (request.term.trim().length > 0) {
                $.ajax({
                    url: $siteurl + "class_trainee/get_taxcode",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term,
                        paid: $('#payment_status').val(),
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            }else{
                var d;
                response(d);
            }
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#taxcode_id').val(id);
            remove_all_err('#search_form');
        },
        minLength:0
    });
    $("#trainee").autocomplete({
        source: function(request, response) {
            $('#trainee_id').val('');
            if (request.term.trim().length > 0) {
                $.ajax({
                    url: $siteurl + "class_trainee/get_trainee",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term,
                        paid: $('#payment_status').val()
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            }else{
                var d;
                response(d);
            }
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#trainee_id').val(id);
            remove_all_err('#search_form');
        },
        minLength:0
    });
    $('#payment_status').change(function() {
        $('.companyamounts_display').hide();
        $('.trainee_div').hide();
        $('.amountdetails_div').hide();
        $('#invoice_id').val('');
        $('#taxcode').val('');
        $('#taxcode_id').val('');
        $('#trainee').val('');
        $('#trainee_id').val('');
        if ($('#account_type').val() == 'company') {
            $('#company').removeAttr('disabled');
           $('#company').val('');
            $('#company').trigger('change');
        } else {
            $val = $(this).val();
            $('#invoice_id').attr('disabled', 'disabled');
            $('#invoice_id').html('<option value="">Select</option>');
            $.ajax({
                url: $siteurl + "accounting/get_invoice",
                type: "post",
                dataType: "json",
                data: {
                    paid: $val
                },
                success: function(data) {
                    if (data != '') {
                        $('#invoice_id').removeAttr('disabled');
                        $.each(data, function(i, item) {
                            $('#invoice_id').append('<option value="' + item.value + '">' + item.label + '</option>');
                        });
                    }
                }
            });
        }
    });
    $('.search_button').click(function() {
        $account_type = $('#account_type').val();
        $invoice_id = '';
        $invoice_no_id = '';
        $taxcode_id = '';
        $trainee_id = '';
        $search_select = $('.search_select:checked').val();
        remove_err('#invoice');
        remove_err('#taxcode');
        remove_err('#trainee');
        remove_err('#company');
        remove_err('#company_invoice');
        $payment_status = $('#payment_status').val();
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
//                $invoice = $('#invoice').val();
//                $invoice_id = $('#invoice_id').val();
//                if ($invoice_id.length == 0) {
//                    disp_err('#invoice'); 
//                } else {
//                    trigger_ajax($invoice_id, $taxcode_id, $trainee_id);
//                }
                //added by shubhranshu for invoice serach on 19/04/2019
                $invoice_id=$invoice_no_id = $('#invoice_no_id').val();
                $invoice_no = $('#invoice_no').val();
                if ($invoice_no.length == 0) {
                    disp_err('#invoice');
                } else if ($invoice_no_id.length == 0) {
                    disp_err('#invoice', '[Select from auto-help]');
                } else {
                    trigger_ajax($invoice_id, $taxcode_id, $trainee_id);
                } //added by shubhranshu for invoice serach
            } else if ($search_select == 2) {
                $taxcode = $('#taxcode').val();
                $taxcode_id = $('#taxcode_id').val();
                if ($taxcode.length == 0) {
                    disp_err('#taxcode');
                } else if ($taxcode_id.length == 0) {
                    disp_err('#taxcode', '[Select from auto-help]');
                } else {
                    trigger_ajax($invoice_id, $taxcode_id, $trainee_id);
                }
            } else if ($search_select == 3) {
                $trainee = $('#trainee').val();
                $trainee_id = $('#trainee_id').val();
                if ($trainee.length == 0) {
                    disp_err('#trainee');
                } else if ($trainee_id.length == 0) {
                    disp_err('#trainee', '[Select from auto-help]');
                } else {
                    trigger_ajax($invoice_id, $taxcode_id, $trainee_id);
                }
            }
        }
    });
    function trigger_companyajax($invoice_id, $company) {
        ///////////////added by shubhranshu to prevent multiclicks/////////////
         var self = $("#btn_srch"),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            //////////////////////////////////////////////////////////////////////
        $.ajax({
            url: $baseurl + 'class_trainee/get_company_refundpayment',
            type: 'post',
            dataType: 'json',
            data: {
                invoice: $invoice_id,
                company: $company
            },
            beforeSend: function() {
                $('.success').empty();
                remove_err('#without_subsidy');
                remove_err('#with_subsidy');
                 remove_err('#foreigner_invoice');
            },
            success: function(data) {
                $class_id = data.class_id;
                $company_id = data.company_id;
                $total_unit_fees = data.total_unit_fees;
                $regen1_check = 0;
                $regen2_check = 0;
                $regen2_validate_check = 0;
                $('#invoice_hidden_id').val(data.invoice_id);
                $('#class_hidden_id').val(data.class_id);
                $('.regen_subsidy_div').hide();
                $('.regen_discount_div').hide();
                $('.regen_inv').removeAttr('data-inv');
                $('.regen_invoice').html($('#company_invoice_id option[value="' + data.invoice_id + '"]').text());
                $('.regen_but').hide();
                $('.select_reinvoice').removeAttr('checked');
                $('.regen2_dis_type').html(data.discount_label);
                $('.regen2_dis_amt').html(parseFloat(data.total_inv_discnt).toFixed(2));
                $('.regen2_dis_rate').html(parseFloat(data.discount_rate).toFixed(2));
                $('#regen2_hid_dis_type').val(data.discount_type);
                $('#regen2_hid_dis_perc').val(data.discount_rate);
                var inv_discnt = parseFloat(data.total_inv_discnt) / parseFloat(data.trainees.length);
                $total_trainees = data.trainees.length;
                $('#regen2_hid_dis_amt').val(inv_discnt);
                $('.regen2_change_div').hide();
                $('.regen2_change').show();
                $('#regen2_form_dis_type').val('');
                $('#regen2_form_dis_perc').val('');
                remove_err('#regen2_main');

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
                $('.companyamounts_display').show();
               
                var trainee = data.trainees;                
                var cnt = 1;   
                var subsidy_type = data.subsidy_type;                               
                $company_total = 0;
                $company_total_due = 0;
                $company_total_paid = 0;
                $company_total_refund = 0;
                $('.regen3_tbody').empty();
                $.each(trainee, function(i, item) 
                {
                    $subsidy_type_combo = '<option value="">Select</option>';
                    $.each(subsidy_type, function(i, row) {
                        if(row.subsidy_id == item.subsidy_type_id) {
                            $subsidy_type_combo += '<option value="'+row.subsidy_id+'" selected="selected">'+row.subsidy_type+'</option>';
                        } else {
                            $subsidy_type_combo += '<option value="'+row.subsidy_id+'">'+row.subsidy_type+'</option>';
                        }
                    }); 
                    $payment_progress = '<script>$(".amount_recd_paying").datepicker({\
                        dateFormat: "dd-mm-yy",\
                        changeMonth: true,\
                        changeYear: true,\
                        yearRange: "+0:+100",\
                        onClose: function() {\
                            $(this).trigger("change");\
                        }\
                    });</script>';
                    $name = 'trainee[' + item.user_id + ']';
                    $payment_progress += '<span class="actual_pay_recd_' + item.user_id + '">' + item.subsidy_recd_date + '</span>\
                                        <input type="hidden" value="' + item.subsidy_recd_date + '" name="amount_recd_check[' + item.user_id + ']"/>\
                                        <span style="display:none;" class="actual_pay_recdenter_' + item.user_id + '"><input type="text" value="' + item.subsidy_recd_date + '" name="amount_recd_paying[' + item.user_id + ']" class="amount_recd_paying"/></span>\n\
                                        <span id="amount_recd_paying_' + item.user_id + '_err"></span>';
                    $payment_recd = '<span class="actual_pay_' + item.user_id + '">$ ' + parseFloat(item.subsidy_amount).toFixed(2) + ' SGD</span>\
                                        <input type="hidden" value="' + parseFloat(item.subsidy_amount).toFixed(2) + '" name="amount_check[' + item.user_id + ']"/>\
                                        <span style="display:none;" class="actual_payenter_' + item.user_id + '"><select data-user="'+ item.user_id +'" name="subsidy_type[' + item.user_id + ']" class="subsidy_type" >'+$subsidy_type_combo+'</select> $ <label id="subsidy_type_label_' + item.user_id + '">' + parseFloat(item.subsidy_amount).toFixed(2) + '</label><input type="text" value="' + parseFloat(item.subsidy_amount).toFixed(2) + '" name="amount_paying[' + item.user_id + ']" class="amount_paying"/> SGD</span>\n\
                                        <span id="amount_paying_' + item.user_id + '_err"></span>';
                    $paid_status = '<input type="radio" class="c_trainee" name="' + $name + '" value="2">Change &nbsp; \
                                        <input type="radio" class="c_trainee" checked="checked" name="' + $name + '" value="1">Do Not Change &nbsp;';
                   $html = '<tr>\
                                    <td>' + cnt + '<input name="trainee_selected[]" style="display:none;" class="trainee_selected" value="' + item.user_id + '" checked="checked" type="checkbox" /></td>\
                                    <td>' + item.first + '</td>\
                                    <td>' + item.tax_code + '</td>\
                                    <td data-user="' + item.user_id + '">\
                                        ' + $payment_recd + '\
                                    </td>\
                                    <td data-user="' + item.user_id + '">\
                                        ' + $payment_progress + '\
                                    </td>\
                                    <td data-user="' + item.user_id + '">' + $paid_status + '</td>\
                                </tr>';
                    $('.regen3_tbody').append($html);
                    cnt++;
                });
                
                $.each(trainee, function(i, item) 
                {
                    cnt++;
                    $company_total_due = $company_total_due + +item.total_amount_due;
                    $company_total_paid = $company_total_paid + +item.amount_paid;
                    $company_total_refund = $company_total_refund + +item.amount_refund;
                    $company_total = $company_total + +item.amount_remain;
                });
                 
                if (cnt > 1) 
                {
                        
                    $company_total = $company_total + +data.excess_remain;
                    $company_total_refund = $company_total_refund + +data.excess_refunded;
                    $company_total = parseFloat($company_total).toFixed(2);
                    $company_total_due = parseFloat($company_total_due).toFixed(2);
                    $company_total_paid = parseFloat($company_total_paid).toFixed(2);
                    
                    $total_paid = parseFloat($company_total_paid) + +parseFloat(data.invoice_excess_amt);
                    $total_paid = parseFloat($total_paid).toFixed(2);
                    $pend_rec = parseFloat(data.total_inv_amount) + parseFloat(data.amount_refund) + parseFloat(data.invoice_excess_amt) - parseFloat(data.amount_recd) - parseFloat(data.excess_refunded);
//		    $pend_rec = parseFloat(data.total_inv_amount) + parseFloat(data.amount_refund) + parseFloat(data.invoice_excess_amt) - $total_paid - parseFloat(data.excess_refunded);
//                    alert("tot_inv_amt= "+(data.total_inv_amount) +"amount refund= "+(data.amount_refund) +"extra amount="+data.invoice_excess_amt +"tot_paid="+parseFloat(data.amount_recd)+ "extra refund="+(data.excess_refunded));
                    $pend_rec = $pend_rec < 0 ? 0 : $pend_rec;
	           
		    $inv_amt_zigg = parseFloat(data.total_inv_amount).toFixed(2);
                    $pend_rec1=  parseFloat($pend_rec).toFixed(2);
                    $('.trainee_total_invice_amyt').html(parseFloat(data.total_inv_amount));
                    $('.trainee_amont_refund').html(parseFloat(data.amount_refund));
                    $('.invoice_excexx_amt').html(parseFloat(data.invoice_excess_amt));
                    $('.total_paid').html($total_paid);
                    $('.excexx_refund').html(parseFloat(data.excess_refunded));
                    
                    $('.trainee_test').html(parseFloat($inv_amt_zigg));
                    $('.trainee_test1').html(parseFloat($pend_rec1));
//                    alert(parseFloat($total_paid) +"/"+ parseFloat($inv_amt_zigg) +"/"+ parseFloat($pend_rec1));
//                     if (parseFloat($total_paid) == 0 || (parseFloat($inv_amt_zigg) == parseFloat($pend_rec1)) || parseFloat($total_paid) != (parseFloat($inv_amt_zigg))) {
//                    
//                      if (parseFloat($total_paid) == 0 || (parseFloat($inv_amt_zigg) != parseFloat($pend_rec1)) ) {
//                        if (parseFloat($total_paid) == 0 && (parseFloat($total_paid)!= parseFloat($inv_amt_zigg)) || (parseFloat($inv_amt_zigg) != parseFloat($pend_rec1)) && (parseFloat($total_paid) == parseFloat($inv_amt_zigg)) ) {
                    if (parseFloat($total_paid) == 0 || (parseFloat($inv_amt_zigg) == parseFloat($pend_rec1)) ) { 

                        $('.regen_inv').attr('data-inv', data.invoice_id);
                        $('.regen_but').show();
                    }
                    
                    
                    if(parseFloat(data.total_inv_amount).toFixed(2)==0.00)
                    {   
                        $total_paid=0.00;
                        $company_total=0.00;
                        $pend_rec=0.00;
                        
                    }
//                    $('.c_total_amt_recd').html($total_paid);
                    $('.c_total_amt_recd').html(parseFloat(data.amount_recd).toFixed(2));
                    $('.c_tot_refundable_amt').html($company_total);
                    
                    $('.c_tot_pend_recv').html(parseFloat($pend_rec).toFixed(2));
                    
                }
                else
                {  
                       //below code added by shubhranshu
                  $pend_rec = parseFloat(data.total_inv_amount) + parseFloat(data.amount_refund) + parseFloat(data.invoice_excess_amt) - parseFloat(data.amount_recd) - parseFloat(data.excess_refunded);
                   ///////
                    if(parseFloat(data.total_inv_amount).toFixed(2)==0.00)
                    {  
                        $total_paid=0.00;
                        $company_total=0.00;
                        $pend_rec=0.00;
                        
                    }
                   
//                    $('.c_total_amt_recd').html($total_paid);
                    $('.c_total_amt_recd').html(parseFloat(data.amount_recd).toFixed(2));
                    $('.c_tot_refundable_amt').html($company_total);
                    
                    $('.c_tot_pend_recv').html(parseFloat($pend_rec).toFixed(2));
                }   
                
                $('#inv_no').val(data.invoice_id);
                $('#invd_date').val(data.invoiced_on);
                
                $invd_date_check = data.invoiced_on;
                
                $('#payment_due_id').val(data.pymnt_due_id);
                ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
                $(".search_button").removeAttr("Disabled");
                $(".search_button").html("<span class='glyphicon glyphicon-search'></span> Search");
                ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
            }
        })
        
    }

    $(document).on('change', '.c_trainee', function() {
        $user_id = $(this).parent().data('user');
        $checked_data = $('input[name="trainee[' + $user_id + ']"]:checked').val();
        $act_pay = $('.actual_pay_' + $user_id);
        $act_pay_enter = $('.actual_payenter_' + $user_id);
        $act_recdpay = $('.actual_pay_recd_' + $user_id);
        $act_recdpay_enter = $('.actual_pay_recdenter_' + $user_id);
        if ($checked_data == 1) {
            $original_val = $('input[name="amount_check[' + $user_id + ']"]').val();
            $('input[name="amount_paying[' + $user_id + ']"]').val($original_val);
            $('.actual_pay_' + $user_id).removeAttr('style');
            $act_pay.show();
            $act_pay_enter.hide();
            $original_recd_val = $('input[name="amount_recd_check[' + $user_id + ']"]').val();
            $('input[name="amount_recd_paying[' + $user_id + ']"]').val($original_recd_val);
            $('.actual_pay_recd_' + $user_id).removeAttr('style');
            $act_recdpay.show();
            $act_recdpay_enter.hide();
        } else if ($checked_data == 2) {
            $act_pay.hide();
            $act_pay_enter.show();
            $('input[name="amount_paying[' + $user_id + ']"]').hide();
            $act_recdpay.hide();
            $act_recdpay_enter.show();
        }
    });
    function trigger_ajax($invoice_id, $taxcode_id, $trainee_id) {
        ///////added by shubhranshu to prevent multiple clicks////////////////  //////////////////// 
        var self = $("#btn_srch"),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
        $.ajax({
            url: $siteurl + "class_trainee/search_trainee_invoice",
            type: "post",
            dataType: "json",
            data: {
                invoice_id: $invoice_id,
                taxcode_id: $taxcode_id,
                trainee_id: $trainee_id,
                paid: $('#payment_status').val()
            },
            beforeSend: function() {
                $('.trainee_table').hide();
                $('.trainee_table tbody').empty();
                $('.trainee_alert').empty();
                $('.success').empty();
            },
            success: function(res) {
                
                 $('.trainee_table tbody').empty();
                 
                $(".main_error").remove();
                $(".trainee_div").show();
                var data = res.data;
                if (data != '') {
                    $('.trainee_table').show();
                    $.each(data, function(i, item) {
                        $amount_refund = (item.amount_refund == null) ? 0 : item.amount_refund;
                        item.amountrecd = (item.amountrecd == null) ? 0 : item.amountrecd;
                        if(item.payment_status=="")
                        {
                            item.payment_status="VOIDED";
                        }
                        $('.pay_subsidy_amount1').html(item.payment_status);
                         $html = '<tr>\
                                        <td><input type="radio" class="trainee_invoice"  name="trainee_invoice" value="' + item.payid + '"/></td>\
                                        <td>' + item.taxcode + '</td>\
                                        <td>' + item.first + '</td>\
                                        <td>' + item.crse_name + ' - ' + item.class_name + '</td>\
                                        <td style="color:blue;">$ ' + parseFloat(item.amountdue).toFixed(2) + ' SGD</td>\
                                        <td>$ ' + parseFloat(item.amountrecd).toFixed(2) + ' SGD</td>\
                                        <td>$ ' + parseFloat($amount_refund).toFixed(2) + ' SGD</td>\
                                        <td style="color:blue;">$ ' + parseFloat((item.amountrecd - $amount_refund)).toFixed(2) + ' SGD</td>\
                                        <td><font color="red">' + item.payment_status + '<font></td>\
                                        </tr>';
                        $('.trainee_table tbody').append($html);
              });
                } else {
                $html = "<span class='error'>No Invoice Found for the Trainee '" + res.trainer + "'. Please contact Administrator.</span>";
                    $('.trainee_alert').html($html);

                }
                $('.amountdetails_div').hide();
                ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
                    $(".search_button").removeAttr("Disabled");
                $(".search_button").html("<span class='glyphicon glyphicon-search'></span> Search");
                ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
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
                },
                success: function(res) {
                    $('.amountdetails_div').show();
                    var data = res.data;
                    if (data != null) {
                        
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
                        
                        $('#iinv_no').val(data.invoice_id);
                        $('#iinvd_date').val(data.invoiced_on);
                        
                         $('.tenant_id').val(data.tenant_id);
                        $('.pymnt_due_id').val(data.pymnt_due_id);
                        $('.invoice_id').val(data.invoice_id);
                        
                        $('.pay_total_gst1').html(data.invoice_id);
                        $('.pay_total_gst11').html(data.pymnt_due_id);
                        
                        $iinvd_date_check = data.invoiced_on;
                        $gst_indi_subsidy_afterbefore = data.subsidy_after_before;
                        $gst_indi_on = data.gst_on_off ==true ? 1 : 0;
                        var regen_indi_inv =  $('.regen_indi_inv');
                        var subsidy_type = res.subsidy_type;
                        $subsidy_type_combo = '<option value="">Select</option>';
                        $.each(subsidy_type, function(i, item) {
                            if(data.subsidy_type_id == item.subsidy_id) {
                                $subsidy_type_combo += '<option value="'+item.subsidy_id+'" selected="selected">'+item.subsidy_type+'</option>';
                            } else {
                                $subsidy_type_combo += '<option value="'+item.subsidy_id+'">'+item.subsidy_type+'</option>';
                            }
                        }); 
                        
                        if(data.payment_status == 'NOTPAID') {
                            regen_indi_inv.attr('data-inv', data.invoice_id);
                            regen_indi_inv.show();
                            $('.remvind_div1').hide();
                            $('.ind_void_invoice').hide();
                            $('.pull-right').css('width', '');
                            $('.update_indiv_inv').show();
                        } else {
                            regen_indi_inv.removeAttr('data-inv');
                            regen_indi_inv.hide();
                            $('.remvind_div1').hide();
                            $('.ind_void_invoice').hide();
                            $('.pull-right').css('width', '');
                             $('.update_indiv_inv').show();
                            
                        }
                        ///////////////un-commented by shubhranshu on 06/12/2018 due to if the trainee absent regenerate button should be disabled/////////
                       if(data.att_status==0 && data.enrolment_mode!="COMPSPON")
                        {
//                            regen_indi_inv.removeAttr('data-inv');
                            regen_indi_inv.hide();
//                            
//                            $('.remvind_div1').show();
//                            $('.ind_void_invoice').show();
//                            $('.update_indiv_inv').hide();
//                            $('.pull-right').css('width', '242px');
//                            $('.void_form').css('margin-top','-19px');
//                            $('.void_form').css('margin-left','129px');
                        }//////////////////////////////////////////////////////////
                        $('.regen_indi_invoice').html(data.invoice_id + ' (Course: ' + data.crse_name + ', Class: '+ data.class_name +')');
                        $('.regen2_indi_dis_type').html(data.discount_label);
                        $('.regen2_indi_dis_rate').html(parseFloat(data.discount_rate).toFixed(2));
                        
                        $regen1_indi_check = 0;
                        $regen2_indi_check = 0;
                        $regen2_indi_validate_check = 0;
                        $('#invoice_indi_hidden_id').val(data.invoice_id);
                        $('.select_indi_reinvoice').removeAttr('checked');
                        $('#regen2_indi_hid_dis_type').val(data.discount_type);
                        $('#regen2_indi_hid_dis_perc').val(data.discount_rate_hidden);
                        $('#regen2_indi_hid_dis_amt').val(data.total_inv_discnt_hidden);
                        $('.regen2_indi_change_div').hide();
                        $('.regen2_indi_change').show();
                        $('#regen2_indi_form_dis_type').val('');
                        $('#regen2_indi_form_dis_perc').val('');
                        remove_err('#regen2_indi_main');
                        
                        $('.regen3_indi_tbody').empty();
                        $payment_progress = '<script>$(".amount_indi_recd_paying").datepicker({\
                            dateFormat: "dd-mm-yy",\
                            changeMonth: true,\
                            changeYear: true,\
                            yearRange: "+0:+100",\
                            onClose: function() {\
                                $(this).trigger("change");\
                            }\
                        });</script>';
                        $name = 'trainee_indi[' + data.user_id + ']';
                        $payment_progress += '<span class="actual_indi_pay_recd_' + data.user_id + '">' + data.subsidy_recd_date + '</span>\
                                            <input type="hidden" value="' + data.subsidy_recd_date + '" name="amount_indi_recd_check[' + data.user_id + ']"/>\
                                            <span style="display:none;" class="actual_indi_pay_recdenter_' + data.user_id + '"><input type="text" value="' + data.subsidy_recd_date + '" name="amount_indi_recd_paying[' + data.user_id + ']" class="amount_indi_recd_paying"/></span>\n\
                                            <span id="amount_indi_recd_paying_' + data.user_id + '_err"></span>';
                        $payment_recd = '<span class="actual_indi_pay_' + data.user_id + '">$ ' + parseFloat(data.total_inv_subsdy).toFixed(2) + ' SGD</span>\
                                            <input type="hidden" value="' + parseFloat(data.total_inv_subsdy).toFixed(2) + '" name="amount_indi_check[' + data.user_id + ']"/>\
                                            <span style="display:none;" class="actual_indi_payenter_' + data.user_id + '"><select data-user="'+ data.user_id +'" name="indi_subsidy_type[' + data.user_id + ']" class="indi_subsidy_type" >'+$subsidy_type_combo+'</select> $ <label id="indi_subsidy_type_label_' + data.user_id + '">' + parseFloat(data.total_inv_subsdy).toFixed(2) + '</label><input type="text" value="' + parseFloat(data.total_inv_subsdy).toFixed(2) + '" name="amount_indi_paying[' + data.user_id + ']" class="amount_indi_paying"/> SGD</span>\n\
                                            <span id="amount_indi_paying_' + data.user_id + '_err"></span>';
                        $paid_status = '<input type="radio" class="c_indi_trainee" name="' + $name + '" value="2">Change &nbsp; \
                                            <input type="radio" class="c_indi_trainee" checked="checked" name="' + $name + '" value="1">Do Not Change &nbsp;';
                       $html = '<tr>\
                                        <td>1<input name="trainee_indi_selected[]" style="display:none;" class="trainee_indi_selected" value="' + data.user_id + '" checked="checked" type="checkbox" /></td>\
                                        <td>' + data.first_name + '</td>\
                                        <td>' + data.tax_code + '</td>\
                                        <td data-user="' + data.user_id + '">\
                                            ' + $payment_recd + '\
                                        </td>\
                                        <td data-user="' + data.user_id + '">\
                                            ' + $payment_progress + '\
                                        </td>\
                                        <td data-user="' + data.user_id + '">' + $paid_status + '</td>\
                                    </tr>';
                        $('.regen3_indi_tbody').append($html);
                        $total_indi_unit_fees = data.total_inv_amount;
                        $('.print_href').attr('href', $baseurl + 'class_trainee/export_generate_invoice/' + data.pymnt_due_id);
                    }
                }
            });
        }
    }
    );
    
    $acc_type = $('#account_type').val();
    if ($acc_type == 'company') {

        $('#payment_status').html('<option value="">All</option><option value="PAID">Paid</option>\
            <option value="PARTPAID">Part Paid</option>\
            <option value="NOTPAID">Not Paid</option>');
        $('#company_invoice_id').html('<option value="">Select</option>');
        $('.company_td').show();
        $('.row_dimm9').hide();
    }


    $('.regen_inv').click(function() {

        $inv = $(this).attr('data-inv');
        if (typeof $inv !== typeof undefined && $inv !== false) {
            remove_err('#select_reinvoice');
            remove_err('#regen2_main');
            remove_all();
            $regen1_check = 0;
            $regen2_check = 0;
            $regen2_validate_check = 0;
            $('.regen2_change').show();
            $('.regen2_change_div').hide();
            $('#ex21').modal();
        } else {
            alert('something went wrong. Refresh the page and try again');
            return false;
        }
    });
        $('.regen_indi_inv').click(function() {

        $inv = $(this).attr('data-inv');
        if (typeof $inv !== typeof undefined && $inv !== false) {
            remove_err('#select_indi_reinvoice');
            remove_err('#regen2_indi_main');
            remove_all();
            $regen1_indi_check = 0;
            $regen2_indi_check = 0;
            $regen2_indi_validate_check = 0;
            $('.regen2_indi_change').show();
            $('.regen2_indi_change_div').hide();
            $('#ex78').modal();
        } else {
            alert('something went wrong. Refresh the page and try again');
            return false;
        }
    });
    
    $regen1_indi_check = 0;
    $('.create_indi_regen').click(function() {
        $regen1_indi_check = 1;
        regen1_indi_validate(true);
    });
    $('.select_reinvoice').change(function() {
        if ($regen1_indi_check == 1) {
            regen1_indi_validate(false);
        }
    });
    
    function regen1_indi_validate($retval) {
      
        $select_indi_reinvoice = $('.select_indi_reinvoice:checked').length;
        if ($select_indi_reinvoice == 0) {
            disp_err('#select_indi_reinvoice');
            $retval = false;
        } else {
            remove_err('#select_indi_reinvoice');
        }
        if ($retval == true) {
            if ($('.select_indi_reinvoice[value="2"]').attr('checked') == 'checked') {
                $('#ex79').css('height', '250px');
                $('.inbetween_indi_div').hide();
                $('.regen_indi_discount_div').show();
            } else {
                $('.regen_indi_discount_div').hide();
            }
            if ($('.select_indi_reinvoice[value="1"]').attr('checked') == 'checked') {
                $('#ex79').css('height', '300px');
                $('.inbetween_indi_div').hide();
                $('.regen_indi_subsidy_div').show();
            } else {
                $('.regen_indi_subsidy_div').hide();
            }
            if ($('.select_indi_reinvoice:checked').length == 2) {
                $('.inbetween_indi_div').show();
                $('#ex79').css('height', '650px');
            }

            $('#ex79').modal();

        }
    }
    
    $('.regen2_indi_change').click(function() {
        $regen2_indi_check = 1;
        $('.regen2_indi_change_div').show();
        $(this).hide();
    });
    $('.regen2_indi_but').click(function() {
        $regen2_indi_validate_check = 1;
        return regen2_indi_validate(true);
    });
    $(document).on('change', '#ex79 input[type="text"], #ex79 select', function() {
        if ($regen2_indi_validate_check == 1) {
            return regen2_indi_validate(false);
        }
    });
    function regen2_indi_validate($retval) {
        $regen2_indi_err = 0;
        if ($('.select_indi_reinvoice[value="2"]').attr('checked') == 'checked') {
            if ($regen2_indi_check == 0) {
                var def_indi_disc_type_obj = {
                  Individual:'DISINDVI',
                  Class: 'DISCLASS'
                };
                var default_indi_disc_label = $('.pay_discount_label').text();
                var default_indi_disc_amt = $('#regen2_indi_hid_dis_amt').val();
                var default_indi_disc_type = def_indi_disc_type_obj[default_indi_disc_label];                
                var default_indi_disc_perc = $('#regen2_indi_hid_dis_perc').val();  
                $('#regen2_indi_form_dis_type').val(default_indi_disc_type);
                $('#regen2_indi_form_dis_perc').val(default_indi_disc_perc);
                $('#regen2_indi_form_dis_amt').val(default_indi_disc_amt);
            } else {
                $regen2_indi_err = 0;
                remove_err('#regen2_indi_main');
                $regen2_indi_form_dis_type = $('#regen2_indi_form_dis_type').val();
                if ($regen2_indi_form_dis_type.length == 0) {
                    disp_err('#regen2_indi_form_dis_type');
                    $retval = false;
                } else {
                    remove_err('#regen2_indi_form_dis_type');
                }
                $regen2_indi_form_dis_perc = $('#regen2_indi_form_dis_perc').val();
                $regen2_indi_form_dis_amt = $('#regen2_indi_form_dis_amt').val();
                $regen2_indi_hid_dis_perc = $('#regen2_indi_hid_dis_perc').val();
                if ($regen2_indi_form_dis_perc.length == 0 && $regen2_indi_form_dis_amt.length ==0) {
                    disp_err('#regen2_indi_form_dis_perc');
                    $retval = false;
                } else if (parseFloat($regen2_indi_form_dis_perc) > 100) {
                    disp_err('#regen2_indi_form_dis_perc', '[Discount Percentage should not be greater than 100]');
                    $retval = false;
                } else {
                    remove_err('#regen2_indi_form_dis_perc');
                }
            }
        }
        if ($('.select_indi_reinvoice[value="1"]').attr('checked') == 'checked') {
            $regen2_indi_check_select = $('.c_indi_trainee[value="2"]:checked').length;
            if ($regen2_indi_check_select == 0) {
                if ($regen2_indi_err == 1) {
                    disp_err('#regen2_indi_main', '[Please change discount and subsidy details.]');
                    $retval = false;
                } else {
                    disp_err('#regen2_indi_main', '[Please change subsidy details.]');
                    $retval = false;
                }
            } else if ($regen2_indi_err != 1) {
                remove_err('#regen2_indi_main');
            }
            $('.trainee_indi_selected:checked').each(function() {
                $user_id = $(this).val();
                $check = $('input[name="trainee_indi[' + $user_id + ']"]:checked').val();
                if ($check == 2) {
                    $pay = $('input[name="amount_indi_paying[' + $user_id + ']"]').val();
                    $pay_check = $('input[name="amount_indi_check[' + $user_id + ']"]').val();
                    if ($pay.length == 0) {
                        disp_err('#amount_indi_paying_' + $user_id);
                        $retval = false;
                    } else if (parseFloat($pay) == parseFloat($pay_check)) {
                        disp_err('#amount_indi_paying_' + $user_id, '[previous and current subsidy amount is same.]');
                        $retval = false;
                    } else {
                        remove_err('#amount_indi_paying_' + $user_id);
                    }
                    if(parseFloat($pay) == 0){
                        $('input[name="amount_indi_recd_paying[' + $user_id + ']"]').val('');
                    }
                    $recd = $('input[name="amount_indi_recd_paying[' + $user_id + ']"]').val();
                    $recd_check = $('input[name="amount_indi_recd_check[' + $user_id + ']"]').val();
                    if ($recd.length == 0 && parseFloat($pay) > 0) {
                        disp_err('#amount_indi_recd_paying_' + $user_id);
                        $retval = false;
                    } 
//                    else if ($recd == $recd_check) {
//                        disp_err('#amount_indi_recd_paying_' + $user_id, '[previous and current subsidy date is same.]');
//                        $retval = false;
//                    } 
                    else {
                        remove_err('#amount_indi_recd_paying_' + $user_id);
                    }
                } else {
                    remove_err('#amount_indi_paying_' + $user_id);
                    remove_err('#amount_indi_recd_paying_' + $user_id);
                }
            });
        }
        return $retval;
    }
    $(document).on('change', '.c_indi_trainee', function() {
        $user_indi_id = $(this).parent().data('user');
        $checked_indi_data = $('input[name="trainee_indi[' + $user_indi_id + ']"]:checked').val();
        $act_indi_pay = $('.actual_indi_pay_' + $user_indi_id);
        $act_indi_pay_enter = $('.actual_indi_payenter_' + $user_indi_id);
        $act_indi_recdpay = $('.actual_indi_pay_recd_' + $user_indi_id);
        $act_indi_recdpay_enter = $('.actual_indi_pay_recdenter_' + $user_indi_id);
        if ($checked_indi_data == 1) {
            $original_indi_val = $('input[name="amount_indi_check[' + $user_indi_id + ']"]').val();
            $('input[name="amount_indi_paying[' + $user_indi_id + ']"]').val($original_indi_val);
            $('.actual_indi_pay_' + $user_indi_id).removeAttr('style');
            $act_indi_pay.show();
            $act_indi_pay_enter.hide();
            $original_indi_recd_val = $('input[name="amount_indi_recd_check[' + $user_indi_id + ']"]').val();
            $('input[name="amount_indi_recd_paying[' + $user_indi_id + ']"]').val($original_indi_recd_val);
            $('.actual_indi_pay_recd_' + $user_indi_id).removeAttr('style');
            $act_indi_recdpay.show();
            $act_indi_recdpay_enter.hide();
        } else if ($checked_indi_data == 2) {
            $act_indi_pay.hide();
            $act_indi_pay_enter.show();
            $('input[name="amount_indi_paying[' + $user_indi_id + ']"]').hide();
            $act_indi_recdpay.hide();
            $act_indi_recdpay_enter.show();
        }
    });
    $('#regen2_indi_form_dis_amt').change(function() {
        $disc_indi_amt = $('#regen2_indi_form_dis_amt').val();
        if (parseFloat($disc_indi_amt) >= $total_indi_unit_fees) {
            alert("Discount amount should not  equal/greater than invoice fees");
            $('#regen2_indi_form_dis_perc').val(0);
            $('#regen2_indi_form_dis_amt').val(0);
            return false;
        } else {            
           $('#regen2_indi_form_dis_perc').val('');
        }

    });
    $('#regen2_indi_form_dis_perc').change(function() {
        $('#regen2_indi_form_dis_amt').val('');
    });
    $(document).on('change', '.amount_indi_paying', function() {
        var $user_id = $(this).parent().parent().attr('data-user');
        $subsidy = $(this).val();
        $.ajax({
            url: $baseurl + 'class_trainee/calculate_discount_percentage',
            type: 'post',
           data: {
               gst_onoff: $gst_indi_on,
                subsidy_after_before: $gst_indi_subsidy_afterbefore, 
                feesdue: $('.pay_total_invoice_amount').text(),
                subsidy: $subsidy, 
                gst_rate: $('.pay_gst_rate').text(), 
                class_fees: $('.pay_class_fees').text(), 
                discount_amount: $('.pay_discount_amount').text()
            },
                dataType: 'json',
                success: function(i) {
                    if (i.label != '') {
                        label_alert = false;
                        alert(i.label);
                        $disc_rate = $('.amount_indi_paying').val('0');
                        $('.indi_subsidy_type').val('');
                        $('.amount_indi_recd_paying').val('');
                        $('.indi_subsidy_type').next('label').text('0.00');
                    }
                    if($subsidy == 0){
                        $('.amount_indi_recd_paying').val('');
                    }
                }
        });
    })
    $regen1_check = 0;
    $('.create_regen').click(function() {
        $regen1_check = 1;
        regen1_validate(true);
    });
    $('.select_reinvoice').change(function() {
        if ($regen1_check == 1) {
            regen1_validate(false);
        }
    });
    function regen1_validate($retval) {
      
        $select_reinvoice = $('.select_reinvoice:checked').length;
        if ($select_reinvoice == 0) {
            disp_err('#select_reinvoice');
            $retval = false;
        } else {
            remove_err('#select_reinvoice');
        }
        if ($retval == true) {
            if ($('.select_reinvoice[value="2"]').attr('checked') == 'checked') {
                $('#ex22').css('height', '250px');
                $('.inbetween_div').hide();
                $('.regen_discount_div').show();
            } else {
                $('.regen_discount_div').hide();
            }
            if ($('.select_reinvoice[value="1"]').attr('checked') == 'checked') {
                $('#ex22').css('height', '300px');
                $('.inbetween_div').hide();
                $('.regen_subsidy_div').show();
            } else {
                $('.regen_subsidy_div').hide();
            }
            if ($('.select_reinvoice:checked').length == 2) {
                $('.inbetween_div').show();
                $('#ex22').css('height', '650px');
            }

            $('#ex22').modal();

        }
    }


    function regen2_validate($retval) {

        $regen2_err = 0;
        if ($('.select_reinvoice[value="2"]').attr('checked') == 'checked') {
            if ($regen2_check == 0) {
                var def_disc_type_obj = {
                  Company:'DISCOMP',
                  Class: 'DISCLASS'
                };
                var default_disc_label = $('.regen2_dis_type').text();
                var default_disc_amt = $('#regen2_hid_dis_amt').val();
                var default_disc_type = def_disc_type_obj[default_disc_label];  
                var default_disc_perc = $('#regen2_hid_dis_perc').val();      
                $('#regen2_form_dis_type').val(default_disc_type);
                $('#regen2_form_dis_perc').val(default_disc_perc);
                $('#regen2_form_dis_amt').val(default_disc_amt);
            } else {
                $regen2_err = 0;
                remove_err('#regen2_main');
                $regen2_form_dis_type = $('#regen2_form_dis_type').val();
                if ($regen2_form_dis_type.length == 0) {
                    disp_err('#regen2_form_dis_type');
                    $retval = false;
                } else {
                    remove_err('#regen2_form_dis_type');
                }
                $regen2_form_dis_perc = $('#regen2_form_dis_perc').val();
                $regen2_hid_dis_perc = $('#regen2_hid_dis_perc').val();
                if ($regen2_form_dis_perc.length == 0) {
                    disp_err('#regen2_form_dis_perc');
                    $retval = false;
                } else if (parseFloat($regen2_form_dis_perc) > 100) {
                    disp_err('#regen2_form_dis_perc', '[Discount Percentage should not be greater than 100]');
                    $retval = false;
                } else {
                    remove_err('#regen2_form_dis_perc');
                }
            }
        }
        if ($('.select_reinvoice[value="1"]').attr('checked') == 'checked') {
            $regen2_check_select = $('.c_trainee[value="2"]:checked').length;
            if ($regen2_check_select == 0) {
                if ($regen2_err == 1) {
                    disp_err('#regen2_main', '[Please change discount and subsidy details.]');
                    $retval = false;
                } else {
                    disp_err('#regen2_main', '[Please change subsidy details.]');
                    $retval = false;
                }
            } else if ($regen2_err != 1) {
                remove_err('#regen2_main');
            }
            $('.trainee_selected:checked').each(function() {
                $user_id = $(this).val();
                $check = $('input[name="trainee[' + $user_id + ']"]:checked').val();
                if ($check == 2) {
                    $pay = $('input[name="amount_paying[' + $user_id + ']"]').val();
                    $pay_check = $('input[name="amount_check[' + $user_id + ']"]').val();
                    if ($pay.length == 0) {
                        disp_err('#amount_paying_' + $user_id);
                        $retval = false;
                    } else if (parseFloat($pay) == parseFloat($pay_check)) {
                        disp_err('#amount_paying_' + $user_id, '[previous and current subsidy amount is same.]');
                        $retval = false;
                    } else {
                        remove_err('#amount_paying_' + $user_id);
                    }
                    if(parseFloat($pay) == 0){
                        $('input[name="amount_recd_paying[' + $user_id + ']"]').val('');
                    }
                    $recd = $('input[name="amount_recd_paying[' + $user_id + ']"]').val();
                    $recd_check = $('input[name="amount_recd_check[' + $user_id + ']"]').val();
                    if ($recd.length == 0 && parseFloat($pay) > 0) {
                        disp_err('#amount_recd_paying_' + $user_id);
                        $retval = false;
                    } 
//                    else if ($recd == $recd_check) {
//                        disp_err('#amount_recd_paying_' + $user_id, '[previous and current subsidy date is same.]');
//                        $retval = false;
//                    } 
                    else {
                        remove_err('#amount_recd_paying_' + $user_id);
                    }
                } else {
                    remove_err('#amount_paying_' + $user_id);
                    remove_err('#amount_recd_paying_' + $user_id);
                }
            });
        }
        return $retval;
    }
    $(document).on('change', '.amount_paying', function() {
        var $user_id = $(this).parent().parent().attr('data-user');
        $subsidy = $(this).val();
        $.ajax({
            url: $baseurl + 'class_trainee/calculate_gst_get_class_for_subsidy',
            type: 'post',
            data: {subsidy: $subsidy, class: $('#class_hidden_id').val(), user: $user_id, },
         dataType: 'json',
            success: function(i) {
                if (i.label != '') {
                    label_alert = false;
                    $('input[name="amount_recd_paying[' + $user_id + ']"]').val('');
                    $('input[name="amount_paying[' + $user_id + ']"]').val('0');
                    $('select[name="subsidy_type[' + $user_id + ']"]').val('');
                    $('#subsidy_type_label_' + $user_id + '').text('0.00');
                    alert(i.label);
                } else {
                    if($subsidy.length > 0){
                    $('input[name="amount_paying[' + $user_id + ']"]').val(parseFloat($subsidy).toFixed(2));
                    }
                    if(parseFloat($subsidy) == 0){
                        $('input[name="amount_recd_paying[' + $user_id + ']"]').val('');
                    }

                }
            }
        });
    })
    $(document).on('keydown', '.amount_paying, .amount_indi_paying', function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    $("#regen2_form_dis_perc, #regen2_form_dis_amt, #regen2_indi_form_dis_perc, #regen2_indi_form_dis_amt").keydown(function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    $('.regen2_change').click(function() {
        $regen2_check = 1;
        $('.regen2_change_div').show();
        $(this).hide();
    });
    $('.regen2_but').click(function() {
        $regen2_validate_check = 1;
        return regen2_validate(true);
    });
    $(document).on('change', '#ex22 input[type="text"], #ex22 select', function() {
        if ($regen2_validate_check == 1) {
            return regen2_validate(false);
        }
    });
    
    $('#regen2_form_dis_amt').change(function() {
        $disc_amt = $('#regen2_form_dis_amt').val();
        if (parseFloat($disc_amt) >= $total_unit_fees) {
            alert("Discount amount should not  equal/greater than total invoice fees");
            $('#regen2_form_dis_perc').val(0);
            $('#regen2_form_dis_amt').val(0);
            return false;
        } else {            
            trigger_company_net_calculation('', '');
        }

    });
    $('#regen2_form_dis_perc').change(function() {
        $('#regen2_form_dis_amt').val('');
    });
    $(document).on('change', '.subsidy_type', function() {    
        $subsidy_type = $(this).val();        
        $user_selected = $(this).data('user');
        $.ajax({
            url: $baseurl + 'class_trainee/get_subsidy_amount',
            type: 'post',
            data: {subsidy_type:$subsidy_type},
            dataType: 'json',
            success: function(res) {
                res = res == 0?'0.00':res;
                $('#subsidy_type_label_'+$user_selected).text(res);
                $('#trainee_subsidy_amount_'+$user_selected).val(res);
                $('input[name="amount_paying[' + $user_selected + ']"]').val(res);
                $('input[name="amount_paying[' + $user_selected + ']"]').trigger('change');                
            }
        });
    });
    $(document).on('change', '.indi_subsidy_type', function() {    
        $subsidy_type = $(this).val();        
        $user_selected = $(this).data('user');
        $.ajax({
            url: $baseurl + 'class_trainee/get_subsidy_amount',
            type: 'post',
            data: {subsidy_type:$subsidy_type},
            dataType: 'json',
            success: function(res) {
                res = res == 0?'0.00':res;
                $('#indi_subsidy_type_label_'+$user_selected).text(res);
                $('input[name="amount_indi_paying[' + $user_selected + ']"]').val(res);
                $('input[name="amount_indi_paying[' + $user_selected + ']"]').trigger('change');                
            }
        });
    });

});
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
    $('#ex22 .error, #ex21 .error').removeClass('error');
}
function remove_all_err(id) {
    $('.error3').text('').removeClass('error3');
    $(id + ' .error').removeClass('error');
}
function trigger_company_net_calculation(amt, per) {        
        $arr = "";
        $disc_amt = $('#regen2_form_dis_amt').val();
        $discount_changed = 'Y';
        $.ajax({
            url: $baseurl + 'class_trainee/get_company_net_calculation',
            type: 'post',
            data: {company: $company_id, data: $arr, discount: $('#regen2_form_dis_amt').val(), 
                class: $class_id, amt: amt, per: per,discount_changed : $discount_changed},
            dataType: 'json',
            beforeSend: function(i) {
            },
            success: function(i) {
                if (i.error.length == 0) {
                    $('#regen2_form_dis_perc').val(parseFloat(i.discount_rate).toFixed(2));     
                    regen2_validate(false);
                } else {
                    alert('NEGATIVE Total Fees Due NOT ALLOWED. Please correct Discount AND/ OR Subsidy Amounts.');
                    $('#regen2_form_dis_perc').val(0);
                    $('#regen2_form_dis_amt').val(0);                    
                    return false;
                }
            }
        });
    }

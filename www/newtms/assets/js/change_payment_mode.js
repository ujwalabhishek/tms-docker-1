/**
 * This js file includes in change_payment_mode page
 */
$(document).ready(function() {
    $('#account_type').val('individual');    
    $('#gyap').css('display', 'none');    
    $('#trainee').attr('disabled', 'disabled');
    $('#account_type').change(function() {        
        $('.companyamounts_display').hide();
        $('.amountdetails_div').hide();
        $('.trainee_div').hide();
        $('.confirm_div').hide();
        $('#company_invoice').attr('disabled', 'disabled');
        $('#company_invoice_id').html('<option value="">Select</option>');
        $('#company_invoice_id').attr('disabled', 'disabled');
        $('.search_select[value="1"]').attr('checked', 'checked');
        $('.search_select').trigger('change');        
        $('#company').val('');        
        $val = $(this).val();
        if ($val == 'company') {
            $('.company_td').show();
            $('.row_dimm9').hide();
        } else {
            $('.company_td').hide();
            $('.row_dimm9').show();
        }
    });      
    $("#taxcode").autocomplete({
        source: function(request, response) {
            $('#taxcode_id').val('');
            if(request.term.trim().length>0){
                $.ajax({
                    url: $siteurl + "class_trainee/get_notpaid_notrequired_taxcode",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term,
                        type: 'taxcode'
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
        },
        minLength:4
    });
    $("#trainee").autocomplete({
        source: function(request, response) {
            $('#trainee_id').val('');
            if(request.term.trim().length>0){
                $.ajax({
                    url: $siteurl + "class_trainee/get_notpaid_notrequired_taxcode",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term,
                        type: 'name'
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
        },
        minLength:4
    });
    $("#company_name").autocomplete({
        source: function(request, response) {            
            if (request.term.trim().length > 0) {
                $.ajax({
                    url: $siteurl + "class_trainee/get_company_json",
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
            $('#company').val(id);
        },
        minLength:4
    });
    $('.search_select').change(function() {
        $('.trainee_div').hide();
        $('.amountdetails_div').hide();
        $('.confirm_div').hide();
        reset_change_payment_mode_individual();
        $val = $('.search_select:checked').val();              
        $('#taxcode').val('');
        $('#taxcode_id').val('');
        $('#trainee').val('');
        $('#trainee_id').val('');        
        $('#taxcode').attr('disabled', 'disabled');
        $('#trainee').attr('disabled', 'disabled');
        if ($val == 2) {
            $div = $('#taxcode');
        } else if ($val == 3) {
            $div = $('#trainee');
        }
        $div.removeAttr('disabled');
    });     
    $('.search_button').click(function() {
        $account_type = $('#account_type').val();
        $company = '';
        $taxcode_id = '';
        $trainee_id = '';
        $search_select = $('.search_select:checked').val();
        remove_err('#company');                
        remove_err('#taxcode');
        remove_err('#trainee');
        $('.trainee_div').hide();
        $('.amountdetails_div').hide();
        $('.confirm_div').hide();
        if ($account_type == 'company') {
            $retval = true;
            $company = $('#company').val();
            $company_name = $('#company_name').val();            
            if ($company.length == 0 || $company_name.length == 0) {
                $retval = false;
                disp_err('#company');
            }            
            if ($retval == true) {
                return trigger_ajax($company, $taxcode_id, $trainee_id);
            }
            return false;
        } else {
            if ($search_select == 2) {
                $taxcode = $('#taxcode').val();
                $taxcode_id = $('#taxcode_id').val();
                if ($taxcode.length == 0) {
                    disp_err('#taxcode');
                } else if ($taxcode_id.length == 0) {                    
                    disp_err('#taxcode', '[Select NRIC/FIN No. from auto-help]');
                } else {
                    trigger_ajax($company, $taxcode_id, $trainee_id);
                }
            } else if ($search_select == 3) {
                $trainee = $('#trainee').val();
                $trainee_id = $('#trainee_id').val();
                if ($trainee.length == 0) {
                    disp_err('#trainee');
                } else if ($trainee_id.length == 0) {
                    disp_err('#trainee', '[Select Trainee Name from auto-help]');
                } else {
                    trigger_ajax($company, $taxcode_id, $trainee_id);
                }
            }
        }
    });  
    
    function trigger_ajax($company, $taxcode_id, $trainee_id) {
         ///////added by shubhranshu to prevent multiple clicks////////////////
        $('.search_button').attr('disabled','disabled');
        $('.search_button').text('Please Wait..');
         ///////added by shubhranshu to prevent multiple clicks////////////////
        $.ajax({
            url: $siteurl + "class_trainee/search_trainee_change_pay_mode",
            type: "post",            
            //async: false,            
            dataType: "json",
            data: {                
                'taxcode_id': $taxcode_id,
                'trainee_id': $trainee_id,
                'company_id' : $company
            },
            beforeSend: function() {
                $('.border_table_div').hide();
                $('.trainee_table tbody').empty();
            },
            success: function(res) {                
                $(".border_table_div").show();
                $(".trainee_div").show();
                var account_type = $('#account_type').val();
                var company_name = $('#company_name').val();
                var heading = '';                
                var data = res.data;
                if (data != '') {
                    $('.border_table_div').show();
                    $('.trainee_table tbody').empty();
                    $.each(data, function(i, item) {
                        if(account_type == 'company')
                            heading = company_name;
                        else 
                            heading = item.first;
                        $('#trainee_name').html(heading);                        
                        var payment_status_text = '';
                        if(item.payment_status == 'NOTPAID')
                            payment_status_text = '<span style="color:blue">Payment required</span>';
                        else 
                            payment_status_text = '<span style="color:red;">Payment not required</span>';
                        var invoice_details = '';
                        if(item.invoice_id != null){
                            invoice_details = item.invoice_id +' / '+item.inv_date;
                        }
                        $html = '<tr>\
                                 <td><input type="radio" data-course="'+item.course_id+'" data-class="'+item.class_id+'" data-paymode="'+item.payment_status+'" class="trainee_invoice"  name="trainee_invoice" value="' + item.payid + '"/></td>\
                                 <td>' + item.crse_name + '</td>\
                                 <td>' + item.class_name + '</td>\
                                 <td>' + payment_status_text + '</td>\
                                <td>'+ invoice_details +'</td></tr>';                                                             
                        $('.trainee_table tbody').append($html);
                         ///////added by shubhranshu to prevent multiple clicks////////////////
                        $('.search_button').removeAttr('disabled');
                    $('.search_button').html('<span class="glyphicon glyphicon-search"></span>&nbsp;Search');
                     ///////added by shubhranshu to prevent multiple clicks////////////////
                    });
                } else {
                    if(account_type == 'company')
                        heading = company_name;
                    else 
                        heading = res.trainer;
                    $('#trainee_name').html(heading);
                    $html = "<tr><td colspan='5' style='text-align:center'><span class='error'>No Enrollment Found for '" + heading + "'.</span></tr>";
                    $('.trainee_table tbody').append($html); 
                     ///////added by shubhranshu to prevent multiple clicks////////////////
                    $('.search_button').removeAttr('disabled');
                    $('.search_button').html('<span class="glyphicon glyphicon-search"></span>&nbsp;Search');
                     ///////added by shubhranshu to prevent multiple clicks////////////////
                }                
            }
        });
    }
    function reset_change_payment_mode_individual() {
        $('#trainee_user_id').val('');
        $('#payment_due_id').val('');
        $('#payment_mode').val('');
        $('#course_id').val('');
        $('#class_id').val('');
        $('#company_id').val('');
    }
    $(document).on('change', '.trainee_invoice', function() {
        reset_change_payment_mode_individual();
         ///////added by shubhranshu to prevent multiple clicks////////////////
        $('.change_paymode').removeAttr('disabled');
        $('.change_paymode').html('<span class="glyphicon glyphicon-retweet"></span>&nbsp;Change Payment Mode');
         ///////added by shubhranshu to prevent multiple clicks////////////////
        $('.confirm_div').hide();
        $('.amountdetails_div').hide();
        $payid = $('.trainee_invoice:checked').val();
        if ($payid.length > 0) {
            $('.amountdetails_div').show();
        }
    });    
    $(document).on('click','.change_paymode', function() {
        reset_change_payment_mode_individual();   
         ///////added by shubhranshu to prevent multiple clicks////////////////
        $('.change_paymode').attr('disabled','disabled');
        $('.change_paymode').show();
        $('.change_paymode').text('Please Wait..');
         ///////added by shubhranshu to prevent multiple clicks////////////////
        var $paymode = $('.trainee_invoice:checked').data('paymode');
        var $paytext = '';
        if($paymode == 'NOTPAID')
            $paytext = 'Payment Not Required';
        else 
            $paytext = 'Payment Required';
        $('.confirm_div').show();
        $('#paymode').html($paytext);
    });
    $(document).on('click','.no_button', function() {
        $('.confirm_div').hide();
         ///////added by shubhranshu to prevent multiple clicks////////////////
         $('.change_paymode').removeAttr('disabled');
        $('.change_paymode').html('<span class="glyphicon glyphicon-retweet"></span>&nbsp;Change Payment Mode');
         ///////added by shubhranshu to prevent multiple clicks////////////////
    });
     $(document).on('click','.yes_button', function() {
          ///////added by shubhranshu to prevent multiple clicks////////////////
         $('.change_paymode').hide();
         var self = $('#change_payment_mode_individual'),
            button = self.find('input[type="button"],button');
            button.attr('disabled','disabled').html('Please Wait..');
             ///////added by shubhranshu to prevent multiple clicks////////////////
        var $pay_due_id = $('.trainee_invoice:checked').val();
        var $paymode = $('.trainee_invoice:checked').data('paymode');         
        var $course_id = $('.trainee_invoice:checked').data('course');
        var $class_id = $('.trainee_invoice:checked').data('class');        
        var $company_id = $('#company').val();        
        var $search_select = $('.search_select:checked').val();
        if ($search_select == 2) {
            var $trainee_user_id = $('#taxcode_id').val();
        } else if ($search_select == 3) {
            var $trainee_user_id = $('#trainee_id').val();
        }        
        $('#trainee_user_id').val($trainee_user_id);
        $('#payment_due_id').val($pay_due_id);
        $('#payment_mode').val($paymode);
        $('#course_id').val($course_id);
        $('#class_id').val($class_id);
        $('#company_id').val($company_id);
        $("#change_payment_mode_individual" ).submit();
        $(".yes_button").hide();
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


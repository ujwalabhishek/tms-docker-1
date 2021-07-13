/**
 * This js file includes in enrol_trainee_pay_now page
 */
$(document).ready(function() {
    var check = 0
    $('#enroll_pay_now_form').submit(function() {   
        //check = 1;
        // added by shubhranshu to prevent multiple click////////////////
        $account_type = $('#account_type').val();
        if ($account_type == 'company') {
            $check= form_validates(true);
        }else{
           $check= form_validate(true); 
        }
        
        if($check){
            $(".book_now").attr('disabled','disabled').html('Please Wait..');
            $(".pay_now").attr('disabled','disabled').html('Please Wait..');
            return true;
        }else{
            return false;
        }// added by shubhranshu to prevent multiple click////////////////
    })
    $('button[type="reset"]').click(function() {
        $('.error_text').text('');
        $('.error').removeClass('error');
    });
    $('#enroll_pay_now_form select, #enroll_pay_now_form input').change(function() {
        if (check == 1) {
            return form_validate(false);
        }
    });
    
//    $('#payment_type').change(function() {
//        $('#row_dim3').hide();
//        $('#row_dim').hide();    
//        $('#giro_div').hide();
//        $val = $(this).val();
//        if ($val.length > 0) {
//            if ($val == 'CASH' || $val == 'NETS') {
//                $('#row_dim3').show();
//            } else if ($val == 'CHQ') {
//                $('#row_dim').show();
//            } else if ($val == 'GIRO') {
//                $('#giro_div').show();
//            }
//        }
//    });

$('#payment_type').change(function() {
        $('#row_dim3').hide();
        $('#row_dim').hide();
        $('#giro_div').hide();
        $('#sfc_div').hide();
        $('#sfcato_div').hide();
        $val = $(this).val();
        if ($val.length > 0) {
            if ($val == 'CASH' || $val == 'NETS') {
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
            if ($val == 'CASH1' || $val == 'NETS1') {
                $('#row_dim31').show();
            } else if ($val == 'CHQ1') {
                $('#row_dim1').show();
            } else if ($val == 'GIRO1') {
                $('#giro_div1').show();
            }
          
        }
    });
    
    $('.enrolment_mode').change(function() {
        $('.book_now').hide();////ssp////////////////
        $company = $('#company_id').val(); 
        $course_id = $('#course_id').val(); 
        $class_id = $('#class_id').val(); 
        $enrolment_mode = $(this).val();   
         ///////added by shubhranshu to prevent enrollment for paid company invoice on 05/12/2018////////////////
        if($enrolment_mode == 'COMPSPON'){
            $.post(baseurl + "trainee/check_company_invoice_status", {comp_id: $company,crs_id: $course_id,cls_id: $class_id}, function(data) {
                json_data = $.parseJSON(data);
                if(json_data.payment_status == 'NOTPAID' || json_data.payment_status === undefined || json_data.payment_status === 'NULL'){
                     if($class_pymnt_enrol == 'PDENROL') {
                         $('.pay_now').show();
                     }else{
                         $('.book_now').show();
                     }
                     $('#paiddiv').hide();
                }else{
                    if($class_pymnt_enrol == 'PDENROL') {
                         $('.pay_now').hide();
                     }else{
                         $('.book_now').hide();
                     }
                    $('#paiddiv').show();
                }
            });
        }else{
             if($class_pymnt_enrol == 'PDENROL') {
                $('.pay_now').show();
            }else{
                $('.book_now').show();
            }
             $('#paiddiv').hide();
        }/////////////////////////////////////////////ssp//////////////////////////////////////////////////////////
        if($class_pymnt_enrol == 'PDENROL') {
            if($enrolment_mode == 'COMPSPON') {
               $('#optype').val('PAYLATER'); 
               $('.enroll_now_btn').text('Book Now');
               $('.pdenrol_table').hide();
               $('#row_dim').hide();
               $('#row_dim3').hide();
               $('#giro_div').hide();
            } else {
               
               $('#optype').val('PAYNOW'); 
               $('.enroll_now_btn').text('Pay Now');
               $('.pdenrol_table').show();
            }
        }
        if ($enrolment_mode.length > 0) { 
            $url = baseurl + "trainee/re_calc_payment_details";
            $.ajax({
                url: $url,
                type: "post",
                dataType: "json",
                data: {
                    company: $company,enrolment_mode:$enrolment_mode,course_id:$course_id,class_id:$class_id
                },
                success: function(data) { 
                    $total_amount = data.net_fees_due;
                    $('#discount_rate').text(parseFloat(data.discount_rate).toFixed(2));
                    $('#discount_amount').text(parseFloat(data.discount_amount).toFixed(2));
                    $('#gst_amount').text(parseFloat(data.gst_amount).toFixed(2));
                    $('#gst_rate').text(parseFloat(data.gst_rate).toFixed(2));
                    $('#net_fees_due').text(parseFloat(data.net_fees_due).toFixed(2));
                }
            });
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
    $("#cheque_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
        onClose: function() {
            $(this).trigger("change");
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
function form_validate($retVal) { 
    $check_excess = 0;
    $chk_amount = parseFloat($total_amount);    
    $msg = '[net due and recd. amount not equal.]';
    $payment_type = $('#payment_type').val();
    $optype = $('#optype').val(); 
    var $enrolment_mode = $('.enrolment_mode:checked').val();        
    if($enrolment_mode == 'SELF' || (typeof $enrolment_mode  == "undefined")) {         
        if ($payment_type.length == 0) {
            disp_err('#payment_type');
            $retVal = false;
        } else if ($payment_type == 'CASH' || $payment_type == 'NETS') {
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
        if ($retVal == true) {
             $('.pay_now').hide();
            if ($check_excess == 1 && $check_full == 1) {
                if ($chk_amount < parseFloat($p_paid)) {
                    $total_amount = parseFloat($p_paid - $chk_amount).toFixed(2);                
                    return false;
                }
            }
        }
    }
    return $retVal;
}

function form_validates($retVal) {
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
    else if ($payment_type == 'CASH'  || $payment_type == 'NETS') {
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
    if ($payment_type1 == 'CASH1' || $payment_type1 == 'NETS1') 
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
         // check sfc claimed on start
        if($payment_type =='SFC_ATO')
        {
            $sfcatoclaim_on = $('#sfcatoclaim_on').val();
            if ($sfcatoclaim_on.length == 0){
                $retVal = false;
                disp_err('#sfcatoclaim_on');
            } else {   
                remove_err('#sfcatoclaim_on');
            }
        }
        // end
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

/**
 * This js file includes in view trainee page
 */
function reset_form() {
    $('#collected_on').val('');
    $('#new_entrance').val('');
    $('#COMMNTS').text(''); 
    $('#trainer_feedback_form').find('input:radio').prop('checked', false);
   // $('#trainer_feedback_form #COMYTCOM_C').attr('checked', 'checked');
    $("#satisfaction_rating option:selected").removeAttr("selected");
}
function clean_data() {
    $('.r_tenant_name').html('');
    $('.r_tenant_address').html('');
    $('.r_tenant_phone').html('');
    $('.r_tenant_email').html('');
    $('.r_class').html('');
    $('.r_course').html('');
    $('.r_certilevel').html('');
    $('.r_course_manager').html('');
    $('.r_class_loc').html('');
    $('.r_class_start').html('');
    $('.r_invoice_no').html('');
    $('.r_invoice_recd').html('');
    $('.r_invoice_amount_recd').html('');
    $('.r_invoice_mode').html('');
    $('.r_invoice_trainee').html('');
    $('.print_receipt').attr('href', '#');
    return false;
}
$(document).ready(function() {
    $('#COMMNTS').keyup(function() {
        $('#COMMNTS').val($('#COMMNTS').val().toUpperCase())
    });
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
                        $html = '<tr><td>' + item.recd_on + '(' + item.mode + ')</td><td>' + $name + '</td><td>$ ' + $amount_recd + ' SGD</td></tr>'; 
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
        $('.training_update').click(function() {
        var $course = $(this).data('course');
        var $class = $(this).data('class');
        var $user = $(this).data('user');
        var $payment= $(this).data('payment');
        
        /* loader img skm start*/
        var ajax_image = "<img src='"+baseurl+"assets/images/q.gif' alt='Loading...' />";
        $('#tbl').hide();
        $('#skm').show();
        $('#skm').html(ajax_image);
        /* end*/
        
        reset_form();
        $('#trainer_feedback_form').attr("action", baseurl + "trainee/trainer_feedback/" + $user + "/" + $course + "/" + $class);
        $.ajax({
            type: 'post',
            url: baseurl + 'trainee/get_trainer_feedback',
            dataType: 'json',
            data: {course: $course, class: $class, user: $user,payment:$payment},
            success: function(res) {
                var trainer = res.trainer;
                var lock_status= res.class_lock;
                $.each(trainer, function(i, item) {                    
                    if (item.feedback_question_id == 'CERTCOLDT')
                        $('#collected_on').val(item.feedback_answer);
                    if (item.feedback_question_id == 'DTCOMMEMP')
                        $('#new_entrance').val(item.feedback_answer);
                    if (item.feedback_question_id == 'SATSRATE')
                        $('#satisfaction_rating option[value="' + item.feedback_answer + '"]').attr("selected", "selected");
                    if (item.feedback_question_id == 'COMMNTS')
                        $('#COMMNTS').text(item.feedback_answer);                    
                    if (item.feedback_question_id == 'APPKNLSKL') {
                        if (item.feedback_answer == 'Y') {
                            $('#APPKNLSKL_YES').prop('checked', true);
                        } else {
                            $('#APPKNLSKL_NO').prop('checked', true);
                        }
                    }
                    if (item.feedback_question_id == 'CERTCOM1') {
                        if (item.feedback_answer == 'Y') {
                            $('#CERTCOM1_YES').prop('checked', true);
                        } else {
                            $('#CERTCOM1_NO').prop('checked', true);
                        }
                    }
                    if (item.feedback_question_id == 'EXPJOBSCP') {
                        if (item.feedback_answer == 'Y') {
                            $('#EXPJOBSCP_YES').prop('checked', true);
                        } else {
                            $('#EXPJOBSCP_NO').prop('checked', true);
                        }
                    }
                    if (item.feedback_question_id == 'RT3MNTHS') {
                        if (item.feedback_answer == 'Y') {
                            $('#RT3MNTHS_YES').prop('checked', true);
                        } else {
                            $('#RT3MNTHS_NO').prop('checked', true);
                        }
                    }
                    if(item.training_score == 'C')
                    {
                        $('#COMYTCOM_C').prop('disabled', false);
                        $('#COMYTCOM_NYC').prop('disabled', false);     
                        $('#COMYTCOM_EX').prop('disabled', false);
                        $('#COMYTCOM_2NYC').prop('disabled', false);
                        $('#COMYTCOM_ABS').prop('disabled', true);
                    }
                   if(item.training_score == 'ABS')
                    {
                      
                        $('#COMYTCOM_C').prop('disabled', true);
                        $('#COMYTCOM_ABS').prop('disabled', false);     
                        $('#COMYTCOM_EX').prop('disabled', true);
                        $('#COMYTCOM_NYC').prop('disabled', true);
                        $('#COMYTCOM_2NYC').prop('disabled', true);
                       
                    }
                    if(item.training_score == 'ATR')
                    {
                      
                        $('#COMYTCOM_C').prop('disabled', true);
                        $('#COMYTCOM_ABS').prop('disabled', false);     
                        $('#COMYTCOM_EX').prop('disabled', true);
                        $('#COMYTCOM_NYC').prop('disabled', true);
                        $('#COMYTCOM_2NYC').prop('disabled', true);
                         $('#COMYTCOM_ATTRITION').prop('disabled', false);
                        $('#skm').hide();
                        $('#tbl').show();
                    }
                    
                    if (item.training_score == null){
                        
                        $('#COMYTCOM_C').prop('disabled', false);
                        $('#COMYTCOM_ABS').prop('disabled', false);     
                        $('#COMYTCOM_EX').prop('disabled', false);
                        $('#COMYTCOM_NYC').prop('disabled', false);
                        $('#COMYTCOM_2NYC').prop('disabled', false);
                       
                    }
                  
                    if (item.feedback_question_id == 'COMYTCOM') {
                        $('#COMYTCOM_C').prop('disabled', false);
                        $('#COMYTCOM_NYC').prop('disabled', false);     
                        $('#COMYTCOM_EX').prop('disabled', false);
                        $('#COMYTCOM_2NYC').prop('disabled', false);
                        $('#COMYTCOM_ABS').prop('disabled', true);
                        if (item.feedback_answer == 'C') {
                            $('#COMYTCOM_C').prop('checked', true);
                        } else if(item.feedback_answer == 'NYC'){
                            $('#COMYTCOM_NYC').prop('checked', true);                        
                        }else if(item.feedback_answer == 'EX'){
                            $('#COMYTCOM_EX').prop('checked', true);
                            
                        }else if(item.feedback_answer == 'ABS'){
                            $('#COMYTCOM_ABS').prop('checked', true);
                        }else if(item.feedback_answer == '2NYC') {
                            $('#COMYTCOM_2NYC').prop('checked', true);
                        }   
                        
                    }
                    
                    ///////below code was added by shubhranshu for xp for attrition option start-----
                if(item.training_score == "NYC" || item.training_score == "C" || item.training_score == "2NYC"){
                    $('#COMYTCOM_ATTRITION').prop('disabled', true);
                }else{
                    if((res.att_percentage <= 0.50) && (res.att_percentage !==null) && (res.att_percentage >= 0)){
                        $('#COMYTCOM_ATTRITION').prop('disabled', false);
                        $('#COMYTCOM_C').prop('disabled', true);
                        $('#COMYTCOM_NYC').prop('disabled', true);     
                        $('#COMYTCOM_EX').prop('disabled', true);
                        $('#COMYTCOM_2NYC').prop('disabled', true);
                        $('#COMYTCOM_ABS').prop('disabled', false);
                    }else if((res.att_percentage < 0.75) && (res.att_percentage !==null) && (res.att_percentage >= 0)){
                        $('#COMYTCOM_ATTRITION').prop('disabled', true);
                        $('#COMYTCOM_C').prop('disabled', true);
                        $('#COMYTCOM_NYC').prop('disabled', true);     
                        $('#COMYTCOM_EX').prop('disabled', true);
                        $('#COMYTCOM_2NYC').prop('disabled', true);
                        $('#COMYTCOM_ABS').prop('disabled', false);
                    }else if(res.att_percentage == null){
                        $('#COMYTCOM_ATTRITION').prop('disabled', false);
                        $('#COMYTCOM_C').prop('disabled', true);
                        $('#COMYTCOM_NYC').prop('disabled', true);     
                        $('#COMYTCOM_EX').prop('disabled', true);
                        $('#COMYTCOM_2NYC').prop('disabled', true);
                        $('#COMYTCOM_ABS').prop('disabled', false);
                    }else{
                        $('#COMYTCOM_ATTRITION').prop('disabled', true);
                    }
                }
                    ////below code was added by shubhranshu for xp for attrition option end-----
                });
                $.each(lock_status, function(i,item){
                        if(item.lock_status==1){
                            //   $('#lock_att').prop('disabled',true);
                            $('#COMYTCOM_C').prop('disabled', true);
                            $('#COMYTCOM_ABS').prop('disabled', true);     
                            $('#COMYTCOM_EX').prop('disabled', true);
                            $('#COMYTCOM_NYC').prop('disabled', true);
                            $('#COMYTCOM_2NYC').prop('disabled', true);
                            $('#new_entrance').prop('disabled', true);
                            $('#satisfaction_rating').prop('disabled', true);
                            $('#COMMNTS').prop('disabled', false); 
                            $('#APPKNLSKL_YES').prop('disabled', true);
                            $('#APPKNLSKL_NO').prop('disabled', true);
                            $('#CERTCOM1_YES').prop('disabled', true);
                            $('#CERTCOM1_NO').prop('disabled', true);
                            $('#EXPJOBSCP_YES').prop('disabled', true);
                            $('#EXPJOBSCP_NO').prop('disabled', true);
                            $('#RT3MNTHS_YES').prop('disabled', true);
                            $('#RT3MNTHS_NO').prop('disabled', true);
                            $('.attendance_lock').show();
                        }else{
                            $('#lock_att').prop('disabled',false);
                            $('.attendance_lock').hide();
            }
        });
            }
    });
    });
    $('.receipt').click(function() {
        var $payid = $(this).data('payid');
        if ($payid != '') {
            $.ajax({
                url: baseurl + "class_trainee/get_enroll_invoice",
                type: "post",
                dataType: "json",
                data: {
                    'payid': $payid,
                },
                beforeSend: function() {
                    clean_data();
                },
                success: function(res) {
                    var data = res.data;
                    if (data != null) {
                        $('.logo').attr('src', baseurl + 'logos/' + res.tenant.Logo);
                        $('.r_tenant_name').html(res.tenant.tenant_name);
                        $('.r_tenant_address').html(res.tenant.tenant_address);
                        $('.r_tenant_phone').html(res.tenant.tenant_contact_num);
                        $('.r_tenant_email').html(res.tenant.tenant_email_id);
                        $('.r_class').html(data.class_name);
                        $('.r_course').html(data.crse_name);
                        $('.r_certilevel').html(data.courselevel);
                        $('.r_course_manager').html(data.course_manager);
                        $('.r_class_loc').html(data.ClassLoc);
                        $('.r_class_start').html(data.class_start);
                        var result = res.invoice.recd_on.split("/");
                        $('.r_invoice_no').html('Receipt No.:' + result[2] + res.invoice.invoice_id);
                        $('.r_invoice_recd').html('Receipt Date:' + res.invoice.recd_on);
                        var amount = parseFloat(res.invoice.amount_recd).toFixed(2);
                        $('.r_invoice_amount_recd').html(amount);
                        $('.r_invoice_mode').html(res.invoice.mode_of_pymnt);
                        $('.r_invoice_trainee').html(res.trainee);
                        $total_amount = data.paid_rcd_till_date;
                        $('.print_receipt').attr('href', baseurl + 'class_trainee/export_payment_receipt/' + data.pymnt_due_id);
                    }
                }
            });
        }
        $('#ex6').modal();
    });
    $("#collected_on").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "+0:+100",
        minDate: 0
    });
    $("#new_entrance").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "+0:+100",
        minDate: 0
    });
});

/* FOR DMS */
function launch_form_dialog(link_text) {
 
    // Split the input and pupolate the hyper links    
    var data_to_set = "";
    
    if(link_text.trim().length == 0) {
        
        data_to_set = '<br><strong><span class=\"required\">There are no assessment records for this traineee</span></strong><br><br><br>';
    }
    else {
    
        var links = link_text.split("~");
        var index;
    
        for(index = 0; index < links.length; index++) {
       
            var values = links[index].split("^");
            data_to_set += (index+1) + '.&nbsp;<a href=\"' + values[0] + '\" target=\"_blank\">' + 
                                         values[1] + '</a><br><br>' + "\n";
        } 
    }
 
    // Set the links in the dialog
    $("#assessment_links").html(data_to_set);
    
    $("#assessment_form").modal('show');
}
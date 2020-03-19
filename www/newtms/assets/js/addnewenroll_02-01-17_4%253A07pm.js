/** 
 * This js file included in add_new_enroll page
 */
$(document).ready(function() {

    $('.enrollment_type').change(function() {
        $('.cannot_change_div').hide();
        $('.not_company_user').hide();
        return enrollment_type_change();
    });
    $('.no_button').click(function() {
        $('#enrollment_mode_new').prop('checked', true);
        $('.enrollment_type').trigger('change');
    });
    $('.yes_button').click(function() {
        $action = baseurl + "class_trainee/enrolment_type_change/newInvoice";
        $('#confirm_enrolment_change_form').attr('action', $action);
        $('#confirm_enrolment_change_form').submit();
    });
    $('#search_enrolment').click(function()     
    {
        $val = $('.enrollment_type:checked').val();
        $change_taxcode = '';
        $opt_type = "";
        $id = '';
        if ($val == 'change') 
        {
            $change_taxcode = $.trim($('#change_taxcode').val());
            $id = "#change_taxcode";
            $opt_type = $val;
        }
        else if ($val == 'remvind')
        {
            $change_taxcode = $.trim($('#change_taxcode1').val());
            $id = "#change_taxcode1";
            $opt_type = $val;
        }
        else if ($val == 'remove_invoice' || $val == 'add_invoice') 
        {
            $change_taxcode = $.trim($('#remove_company_select').val());
            $id = "#remove_company_select";
            $opt_type = $val;
        }
        else if($val=='move_invoice')
        {
            $change_taxcode=$.trim($('#move_company_select').val());
             $('.amountdetails_div').hide();
            $id="#move_company_select";
            $opt_type=$val;
        }
        if ($change_taxcode == '') 
        {
            $('.enrollment_container').hide();
            $('.trainee_div').hide();
            $('.amountdetails_div').hide();
            disp_err($id);
            return false;
        } 
        else 
        {
            remove_err($id);
            $('#company_name_1').text($($id).find('option:selected').attr("company_name"));
            $('#company_name_2').text($($id).find('option:selected').attr("company_name"));
            $couse_class_name = $($id).find('option:selected').attr("course_name") + " - " + $($id).find('option:selected').attr("class_name");
            $('#couse_class_name').text($couse_class_name);
            $payid = $($id).find('option:selected').attr("pymnt_due_id");
            $class = $($id).find('option:selected').attr("class_id");
            $course = $($id).find('option:selected').attr("course_id");
            $company_id = $($id).find('option:selected').attr("company_id");
            $subsidy_amount = $($id).find('option:selected').attr("subsidy_amount");
            $unit_fees = $($id).find('option:selected').attr("unit_fees");
            $individual_user_id = $($id).val();
            if ($payid.length > 0) 
            {
                $('#pymnt_due_id').val($payid);
                $('#course_id').val($course);
                $('#class_id').val($class);
                $('#individual_user_id').val($individual_user_id);
                $('#unit_fees').val($unit_fees);
				// skm code start   
				$.ajax({
                url: $baseurl + 'class_trainee/get_class_date',
                type: 'post',
                data:{"class_id":$class},
                success: function(data)
                {
                    if(data == 1)
                    {
                        $(".control_skm_err").hide();
						
						
                $.ajax({
                    url: $siteurl + "class_trainee/get_enroll_invoice_details",
                    type: "post",
                    dataType: "json",
                    data: {
                        'payid': $payid, 'course': $course, 'class': $class, 'company_id': $company_id,
                        'opt_type': $opt_type, 'user_id': $individual_user_id
                    },
                    beforeSend: function() 
                    {
                        $('.trainee_table').hide();
                        $('.trainee_table tbody').empty();
                        $('.add_company_invoice_div').hide();
                        $('.add_company_invoice_table tbody').empty();
                        $('.amountdetails_div').hide();
                        $('.no_invoice_div').hide();
                        $('.cannot_change_div').hide();
                        $('.not_company_user').hide();
                        $('.trainee_div').hide();
                        $('.attendance_lock').hide();
                        $('.remvind_div').hide();
                    },
                    success: function(res) 
                    {
                        $('.trainee_table').show();
                        $('.check_box_alert').hide();
                        $('.trainee_table tbody').empty();
                        var data = res.data;
                        var data1 = res.data1;
                        var trainees = res.trainees;
                        var error = res.error;
                        var lock= res.lock;
                        
                        if(lock == 1){
                            $('.attendance_lock').show();
                             return false;
                        }
                        else if (error.msg_status == "not_found") 
                        {
                            $('#company_name_2').text(error.company_name);
                            $('#company_id').val(error.company_id);
                            $('.no_invoice_div').show();
                            return false;
                        } 
                        else if (error.msg_status == "cannot_change") 
                        {
                            $('.cannot_change_div').show();
                            return false;
                        } 
                        else if (error.msg_status == "not_company_user") 
                        {
                            $('.not_company_user').show();
                        } 
                        //added by prit
                        else if (data1 != null && data1 != "") 
                        {
                             
                            $('.remvind_div').show();
                            $('.no_invoice_div').hide();
                           
                            
                            $('#course_id1').val(data1.course_id);
                            $('#class_id1').val(data1.class_id);
                            $('#pymnt_due_id1').val(data1.pymnt_due_id);
                            $('#invoice_no1').val(data1.invoice_id);
                            $('#user_id1').val(data1.user_id);
                            $('#user1').val(data1.user_name);
                            $('#tenant_id').val(data1.tenant_id);
                            
                            $('.course_id').html(data1.course_id);
                            $('.class_id').html(data1.class_id);
                            $('.crse_name').html(data1.crse_name);
                            $('.class_name').html(data1.class_name);
                            $('.pymnt_due_id').html(data1.pymnt_due_id);
                            $('.invoice_no').html(data1.invoice_id);
                            $('.user_name').html(data1.user_name);
                            $('.tax_code').html(data1.tax_code);
                            $('.tenant_id').html(data1.tenant_id);
                            
                            $('#company_id').val(data1.company_id);
                            $('#subsidy_amount').val(data1.subsidy_amount);
                            $('#company_name_1').text(data1.company_name);
                            $('.remvind_div1').show();
                        }
                        else if (data != null && data != "") 
                        {
                            $('.amountdetails_div').show();
                            $('.no_invoice_div').hide();
                            $('#comp_pymnt_due_id').val(data.pymnt_due_id);
                            $('#comp_invoice_id').val(data.invoice_id);
                            $('#comp_gst_rate').val(data.gst_rate);
                            
                            $('#class_start_datetime').val(data.class_start_datetime);
                            $('.class_start_datetime').html(data.class_start_datetime);
                            
                            $('#comp_gst_rule').val(data.gst_rule);
                            $('.invoice_no').html(data.invoice_id);
                            $('.pay_total_invoice_amount').html(data.total_inv_amount);
                            $('.pay_gst_rate').html(data.gst_rate);
                            $('.pay_total_gst').html(data.total_gst);
                            $('.pay_gst_label').html(data.gst_label);
                            $('.pay_discount_label').html(data.discount_label);
                            $('.pay_discount_rate').html(data.discount_rate);
                            $('.pay_discount_amount').html(data.total_inv_discnt);
                            $('.pay_class_fees').html(data.total_unit_fees);
                            $('.pay_subsidy_amount').html(data.total_inv_subsdy);
                            $('.invoice_date').html(data.inv_date);
                            $('#company_id').val(data.company_id);
                            $('#subsidy_amount').val(data.subsidy_amount);
                            $('#company_name_1').text(data.company_name);
                        }
                        if (trainees != "" && $val == 'add_invoice') 
                        {
                            var not_enrolled_trainees = res.not_enrolled_trainees;
                            $('.trainee_div').show();
                            $('.add_company_invoice_div').show();
                            $('.add_company_invoice_heading').show();
                            var $n = 1;
                            $html = '';
                            $.each(trainees, function(i, item) 
                            {
                                if ($n == 1) 
                                {
                                    $html += '<tr>';
                                }
                                $html += '<td>' + item.first_name + ' (' + item.tax_code + ')' + '</td>';
                                if ($n == 3) 
                                {
                                    $html += '</tr>';
                                    $n = 0;
                                }
                                $n++;
                            });
                            $('.add_company_invoice_table tbody').append($html);
                            if (not_enrolled_trainees != "") 
                            {
                                $.each(not_enrolled_trainees, function(j, data) 
                                {
                                    $html = '<tr>\n\
                                                <td><input type="checkbox" class="trainee_check_box"  name="checked_trainees[]" value="' + data.user_id + '"/></td>\n\
                                                <td>' + data.tax_code + '</td>\
                                                <td>' + data.first_name + '</td>\n\
                                            </tr>';
                                    $('.trainee_table tbody').append($html);
                                });
                                $('.submit_button').show();
                            } 
                            else 
                            {
                                $html = '<tr><td colspan="3" style="text-align:center;color:red">No employees available for enrollment(s)</td></tr>';
                                $('.trainee_table tbody').append($html);
                                $('.submit_button').hide();
                            }
                            $('.submit_button').text('Add Enrollment');
                            $action = baseurl + "class_trainee/add_to_company_enrollment";
                            $('#confirm_enrolment_change_form').attr('action', $action);
                        } 
                        else if (trainees != "") 
                        {
                            $('.trainee_div').show();
                            $('.add_company_invoice_div').hide();
                            $('.add_company_invoice_heading').hide();
                            $.each(trainees, function(i, item) 
                            {
                                var $sr_no = i + 1;
                                if ($val == 'change') 
                                {
                                    $html = '<tr>\n\
                                                <td>' + $sr_no + '</td>\n\
                                                <td>' + item.tax_code + '</td>\
                                                <td>' + item.first_name + '</td>\n\
                                            </tr>';
                                    $('.trainee_table tbody').append($html);
                                    $('.submit_button').text('Confirm Enrollment Change');
                                    $action = baseurl + "class_trainee/enrolment_type_change/mergeInvoice";
                                    $('#confirm_enrolment_change_form').attr('action', $action);
                                } 
                                else if ($val=='remove_invoice')
                                {
                                    $html = '<tr>\n\
                                                <td><input type="checkbox" class="trainee_check_box"  name="checked_trainees[]" value="' + item.user_id + '"/></td>\n\
                                                <td>' + item.tax_code + '</td>\
                                                <td>' + item.first_name + '</td>\n\
                                            </tr>';
                                    $('.trainee_table tbody').append($html);
                                    $('.submit_button').text('Remove Enrollment');
                                    $action = baseurl + "class_trainee/remove_company_enrollment";
                                    $('#confirm_enrolment_change_form').attr('action', $action);
                                }
                                else
                                {
                                    $html = '<tr>\n\
                                                <td><input type="checkbox" class="trainee_check_box"  name="checked_trainees[]" value="' + item.user_id + '"/></td>\n\
                                                <td>' + item.tax_code + '</td>\
                                                <td>' + item.first_name + '</td>\n\
                                            </tr>';
                                    $('.trainee_table tbody').append($html);
                                    
                                    $val = "to_move_invoice";
                                    get_select_box($val)
                                    $('#to_move_company_autocomplete').val('');
                                    $('.move_company_span').show();
                                     $('#search_enrolment').show();
                                    $('.to_move_company_span').show();
                                     $('#to_move_company_select').hide();
                                    $('.submit_button').text('Move Enrollment');
                                    $action=baseurl + "class_trainee/move_company_enrollment";
                                    $('#confirm_enrolment_change_form').attr('action',$action);
                                 }
                            });
                        } 
                        else 
                        {
                            $('.trainee_div').hide();
                        }

                    }
                });
				
				}
                    else
                    {
                        $('.amountdetails_div').hide();
                        $('.trainee_div').hide();
                        $(".control_skm_err").show();
                        $(".class_id").val($class);
                        $(".course_id").val($course);
                    }
                }
            })
				
				
				
            }
        }
    });
    
    $('#course').attr('disabled', 'disabled');
    $('#class').attr('disabled', 'disabled');
        $('.search_select').change(function() {
        disable_course_class();
        $('#trainee').attr('disabled', 'disabled');
        $('#taxcode').attr('disabled', 'disabled');
        $('#taxcode').val('');
        $('#taxcode_id').val('');
        $('#trainee').val('');
        $('#trainee_id').val('');
        $val = $('.search_select:checked').val();
        if ($val == 1) {
            $div = $('#taxcode');
        } else if ($val == 2) {
            $div = $('#trainee');
        }
        $div.removeAttr('disabled');
    });
    $('#account_type').change(function() {
        $('#course').attr('disabled', 'disabled');
        $('#class').attr('disabled', 'disabled');
        $('#course').val('');
        $('#class').val('');
        $val = $(this).val();
        $('.search_button').show();
        $('.company_tr').hide();
        $('.individual_tr').hide();
        if ($val == 'individual') {
            $('.individual_tr').show();
            $('.search_button').html('<span class="glyphicon glyphicon-retweet"></span> Enroll Now');
        } else {
            $('#course').removeAttr('disabled');
            $('#taxcode').val('');
            $('#taxcode_id').val('');
            $('#trainee').val('');
            $('#trainee_id').val('');
            $('.company_tr').show();
            $('.search_button').html('<span class="glyphicon glyphicon-retweet"></span> Book Now');
            $('#class').trigger('change');
        }
    });
    $('#class').change(function() 
    {
        $account_type = $('#account_type').val();
        if ($account_type == 'company') 
        {
            $(".search_button").hide();
            $(".company_tr").hide();
            $(".control_skm_err").hide();
            
            $course_id = $('#course').val();
            $class_id = $('#class').val();
            $.ajax({
                url: $baseurl + 'class_trainee/get_class_date',
                type: 'post',
                data:{"class_id":$class_id},
                success: function(data)
                {
                    
                    if(data == 1)
                    {   //individual , 
                        $(".company_tr").show();
                        $(".search_button").show();
                        $(".control_skm_err").hide();
                        $('#company').val('');
                        if ($('#class').val().length > 0) {
                            $('#company').removeAttr('disabled');
                        } else {
                            $('#company').attr('disabled', 'disabled');
                        }
                        $('.company_td').empty().html('<select id="control_n" disabled="disabled"><option value="">Select</option></select>').show();
                        remove_err('#control_6');
                    }
                    if($class_id>0 )
                    {
                        if(data == 0){
                        $(".company_tr").hide();
                        $(".search_button").hide();
                        $(".control_skm_err").show();
                        $(".class_id").val($class_id);
                        $(".course_id").val($course_id);
                          $('.add_inv1').hide();
                      }
                    }
                }
            });
           
        }
        else if ($account_type == 'individual') 
        {
            $(".company_tr").hide();
            $(".search_button").hide();
            $course_id = $('#course').val();
            $class_id = $('#class').val();
            $.ajax({
                url: $baseurl + 'class_trainee/get_class_date',
                type: 'post',
                data:{"class_id":$class_id},
                success: function(data)
                {
                    //alert(data);
                    if(data == 1)
                    {   $(".control_skm_err").hide();
                         $(".search_button").show();
                        var cls = $('#class').val();
                        $.ajax({
                            url: $siteurl + "class_trainee/clean_orphan_data",
                            type: "post",
                            dataType: "json",
                            data: {
                                class: cls,
                            },
                            success: function(data) {

                            }
                        });
                    }
                    else
                    {
                        //alert("no");
                        $(".company_tr").hide();
                        $(".search_button").hide();
                        $(".control_skm_err").show();
                        $(".class_id").val($class_id);
                        $(".course_id").val($course_id);
                        $('.add_inv1').hide();
                    }
                }
            });
            
            
           
        }
        
    });
    $("#taxcode").autocomplete({
        source: function(request, response) {
            $('#taxcode_id').val('');
            disable_course_class();
            if (request.term.trim().length > 0) {
                $.ajax({
                    url: $siteurl + "class_trainee/get_alltaxcode",
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
            $('#course').removeAttr('disabled');
        },
        minLength:0
    });
    var check = 0;
    $('#search_form').submit(function() {
        check = 1;
        return form_validate(true);
    });
    $(document).on('change', '#search_form select,#search_form input', function() {
        if (check == 1) {
            return form_validate(false);
        }
    });
    
    $('#company').change(function() {
        $company = $('#company').val();
        $course = $('#course').val();
        $class = $('#class').val();
        $('#trainee').val($('#trainee').val().toUpperCase());
        $('#trainee_id').val('');
        $('.company_td').empty();
        $('.company_td').hide();
        $('.add_inv1').hide();
        $('.company_td').html('<select multiple="multiple" class="control_6"  id="control_6" name="company_trainee"><option value="">Select</option></select>');
        if ($company.length == 0) {
            return $('#class').change();
        }
        $.ajax({
            url: $siteurl + "class_trainee/get_companytrainees",
            type: "post",
            dataType: "json",
            data: {
                company: $company,
                class: $class,
                course: $course
            },
            success: function(data) {
                if (data != '') 
                {
                    if(data.pymnt_due_id > 0)
                    {
                       
                     //   disp_err('#control_6', '[The Invoice for this company is already exist, Please enroll by using Add enrollments to Company invoie.]');
                        $('.search_button').hide();
                        
                        $('.add_inv1').show();
                        $('.add_inv').show();
                          remove_err('#control_6');
                    }
                    else
                    {
                        $.each(data, function(i, item) 
                        {
                             $('.control_6').append('<option value="' + item.user_id + '">' + item.first_name + '( NRIC/FIN No. ' + item.tax_code + ' )</option>');
                        });
                        $('.company_td').show();
                        $("#control_6").multiSelect({oneOrMoreSelected: '*'});
                        remove_err('#control_6');
                        $('.search_button').show();
                    }
                } 
                else 
                {
                    disp_err('#control_6', '[There are no unassigned trainees available.]');
                    $('.search_button').hide();
                }
            }
        });
    });
    
  $('.add_inv').click(function(){
        $('.add_inv1').hide();
        //$('#search_enrolment').show();
        $("#add_company_invoice").prop("checked", true);
        $val='add_invoice';
        get_select_box($val);
        if ($val == 'add_invoice') 
        {
            $('#remove_company_autocomplete').val('');
            $('.change_span').hide();
            $('.change_span1').hide();
            $('.remvind_div').hide();
            $('.remvind_div1').hide();
             $('.to_move_company_span').hide();
            $('.move_company_span').hide();
            $('.remove_company_span').show();
            $('.enrollment_container').hide();
            $('.amountdetails_div').hide();
            $('.trainee_div').hide();
            $('.no_invoice_div').hide();
            $('.new_span').hide();
            $('.company_tr').hide();
        }
  });
    $("#trainee").autocomplete({
        source: function(request, response) {
            $('#trainee').val($('#trainee').val().toUpperCase())
            $('#trainee_id').val('');
            disable_course_class();
            if (request.term.trim().length > 0) {
                $.ajax({
                    url: $siteurl + "class_trainee/get_alltrainee",
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
            $('#course').removeAttr('disabled');
        },
        minLength: 0
    });
    $('#course').change(function() {
        $course = $('#course').val();
        $taxcode_id = '';
        $trainee_id = '';
        $search_select = $('.search_select:checked').val();
        if ($search_select == 1) {
            $taxcode_id = $('#taxcode_id').val();
        } else {
            $trainee_id = $('#trainee_id').val();
        }
        if ($course.length == 0) {
            $('#class').html('<option value="">Select</option>');
            $('#class').attr('disabled', 'disabled');
            $('#class').trigger('change');
            return false;
        }
        $.ajax({
            url: $baseurl + 'class_trainee/get_trainee_classes',
            type: 'post',
            dataType: 'json',
            data: {'course': $course, 'trainee_id': $trainee_id, 'taxcode_id': $taxcode_id},
            async: false,
            beforeSend: function() {
                $('#class').html('<option value="">Select</option>');
            },
            success: function(i) {
                if (i != '') {
                    $.each(i, function(e, item) {
                      //  alert(item.lock_status);
                        if(item.lock_status==1){
                        $('#class').append('<option value="' + item.class_id + '" disabled>' + item.class_name + ' ( Class Id: ' + item.class_id + ') - Locked</option>');
                        }else{
                             $('#class').append('<option value="' + item.class_id + '">' + item.class_name + ' ( Class Id: ' + item.class_id + ')</option>');
                        }
                    });
                    $('#class').removeAttr('disabled');
                } else {
                    $('#class').attr('disabled', 'disabled');
                }
            }
        });
        $('#class').trigger('change');
    });
    $('.search_select').trigger('change');
    if ($('#account_type').val() == 'company') {
        $('#course').removeAttr('disabled');
        $('#taxcode').val('');
        $('#taxcode_id').val('');
        $('#trainee').val('');
        $('#trainee_id').val('');
        $('.company_tr').show();
        $('.search_button').html('<span class="glyphicon glyphicon-retweet"></span> Book Now');
        $('#class').trigger('change');
    }
});




function form_validate($retval) {
    $val = $('.enrollment_type:checked').val();
   if ($val == "change" || $val == "remvind") 
    {
        return true;
    }
    $account_type = $('#account_type').val();
    remove_err('#main');
    if ($account_type == 'individual') {
        remove_err('#taxcode');
        remove_err('#trainee');
        $search_select = $('.search_select:checked').val();
        if ($search_select == 1) {
            $taxcode = $('#taxcode').val();
            $taxcode_id = $('#taxcode_id').val();
            if ($taxcode.length == 0) {
                $retval = false;
                disp_err('#taxcode');
            } else if ($taxcode_id.length == 0) {
                $retval = false;
                disp_err('#taxcode', '[Select NRIC/FIN No. from auto-help]');
            } else {
                remove_err('#taxcode');
            }
        } else if ($search_select == 2) {
            $trainee = $('#trainee').val();
            $trainee_id = $('#trainee_id').val();
            if ($trainee.length == 0) {
                $retval = false;
                disp_err('#trainee');
            } else if ($trainee_id.length == 0) {
                $retval = false;
                disp_err('#trainee', '[Select Trainee Name from auto-help]');
            } else {
                remove_err('#trainee');
            }
        }
    } else {
        $company = $('#company').val();
        if ($company.length == 0) {
            $retval = false;
            disp_err('#company');
        } else {
            remove_err('#company');
        }
        $company_trainee = '';
        $('input[name="control_6[]"]:checked').each(function() {
            $company_trainee += $(this).val();
        })
        if ($company_trainee.length == 0) {
            disp_err('#control_6');
            $retval = false;
        } else {
            remove_err('#control_6');
        }
    }
    $course = $('#course').val();
    if ($course.length == 0) {
        $retval = false;
        disp_err('#course');
    } else {
        remove_err('#course');
    }
    $class = $('#class').val();
    if ($class.length == 0) {
        $retval = false;
        disp_err('#class');
    } else {
        remove_err('#class');
    }
    if ($account_type == 'company' && $retval == true) {
        $.ajax({
            url: $siteurl + "class_trainee/get_class_booked_count",
            type: "post",
            data: {class: $('#class').val()},
            async: false,
            success: function(res) {
                if (res != 'any') {
                    $user_count = $("input[name='control_6[]']:checked").length;
                    if (parseInt($user_count) > parseInt(res)) {
                        alert('Total seats being enrolled, exceeds total available seats. Total seats available: ' + res + '. Please reduce the number of enrollments and try again.');
                        $retval = false;
                    }
                }
            }
        });
    }
    return $retval;
}
function disp_err($id, $text) {
    $text = typeof $text !== 'undefined' ? $text : '[required]';
    $($id).addClass('error');
    $($id + '_err').addClass('error').html($text);
}
function remove_err($id) {
    $($id).removeClass('error');
    $($id + '_err').removeClass('error').text('');
}
function disable_course_class() {
    $('#course').attr('disabled', 'disabled');
    $('#class').html('<option value="">Select</option>');
    $('#class').attr('disabled', 'disabled');
    $('#course').val('');
    $('#class').val('');
}

function enrollment_type_change() 
{
    $val = $('.enrollment_type:checked').val();
    get_select_box($val);
    if ($val == "change") 
    {        
        $('#change_taxcode_autocomplete').val('');
        $('.attendance_lock').hide();
        $('.remove_company_span').hide();
        $('.add_inv1').hide();
        $('.move_company_span').hide();
        $('.change_span1').hide();
        $('.change_span').show();
        $('.remvind_div').hide();
        $('.remvind_div1').hide();
        $('.new_span').hide();
        $('.company_tr').hide();
        $('.amountdetails_div').hide();
        $('.trainee_div').hide();
         $(".control_skm_err").hide();
        $action = baseurl + "class_trainee/change_individual_enrolment";
        $('#search_form').attr('action', $action);
    }
     //added by prit
    else if ($val == "remvind") 
    {        
        $('#change_taxcode_autocomplete1').val('');
        $('.attendance_lock').hide();
        $('.remove_company_span').hide();
         $('.add_inv1').hide();
        $('.move_company_span').hide();
        $('.change_span').hide();
        $('.change_span1').show();
        $('.remvind_div').hide();
        $('.remvind_div1').hide();
        $('.new_span').hide();
        $('.company_tr').hide();
        $('.amountdetails_div').hide();
        $('.trainee_div').hide();
         $(".control_skm_err").hide();
        $('.no_invoice_div').hide();
        $action = baseurl + "class_trainee/change_individual_enrolment";
        $('#search_form').attr('action', $action);
    } 
    else if ($val == 'new') 
    {
        $('#account_type').val('individual');
        $('.attendance_lock').hide();
        $('.search_button').html('<span class="glyphicon glyphicon-retweet"></span> Enroll Now');
        $('.change_span').hide();
         $('.add_inv1').hide();
        $('.change_span1').hide();
        $('.remvind_div').hide();
        $('.remvind_div1').hide();
        $('.to_move_company_span').hide();
        $('.enrollment_container').hide();
        $('.amountdetails_div').hide();
        $('.trainee_div').hide();
         $(".control_skm_err").hide();
        $('.no_invoice_div').hide();
        $('.remove_company_span').hide();
        $('.move_company_span').hide();
        $('.new_span').show();
        $action = baseurl + "class_trainee/enrollment_view_page";
        $('#search_form').attr('action', $action);
        
        $('#account_type').trigger("change");
    } 
    else if ($val == 'remove_invoice' || $val == 'add_invoice') 
    {
        $('#remove_company_autocomplete').val('');
        $('.attendance_lock').hide();
        $('.change_span').hide();
         $('.add_inv1').hide();
        $('.change_span1').hide();
        $('.remvind_div').hide();
        $('.remvind_div1').hide();
         $('.to_move_company_span').hide();
        $('.move_company_span').hide();
        $('.remove_company_span').show();
        $('.enrollment_container').hide();
        $('.amountdetails_div').hide();
        $('.trainee_div').hide();
         $(".control_skm_err").hide();
        $('.no_invoice_div').hide();
        $('.new_span').hide();
        $('.company_tr').hide();
    }
    else if($val=='move_invoice')
    {
        $('#move_company_autocomplete').val('');
        $('.attendance_lock').hide();
        $('.change_span').hide();
         $('.add_inv1').hide();
        $('.change_span1').hide();
        $('.remvind_div').hide();
        $('.remvind_div1').hide();
        $('.remove_company_span').hide();
        $('.move_company_span').show();
        $('.to_move_company_span').show();
         $('#to_move_company_select').hide();
        $('.enrollment_container').hide();
        $('.amountdetails_div').hide();
        $('.trainee_div').hide();
         $(".control_skm_err").hide();
        $('.no_invoice_div').hide();
        $('.new_span').hide();
        $('.company_tr').hide();
    }
}
function get_select_box($type) 
{
    $('#search_enrolment').hide();
    $select = '<option value="">Select</option>';
    $.ajax({
        url: baseurl + 'class_trainee/get_select_box',
        data: {type: $type},
        type: "post",
        dataType: "json",
        beforeSend: function() 
        {
            $('#change_taxcode').hide();
            $('#change_taxcode1').hide();
            $('#remove_company_select').hide();
            $('#move_company_select').hide();
            $('#to_move_company_select').hide();
            $('.change_taxcode_err').remove();
            $('.remove_company_err').remove();
            $('#search_enrolment').hide();
        },
        success: function(data) 
        {
            if ($type == "change") 
            {
                var change_individual = data.change_individual;
                $('#change_taxcode').html($select);
                if (change_individual != "") {
                    $.each(change_individual, function(i, $item) {
                       
                     
                        $value = '<option value="' + $item.user_id + '" pymnt_due_id="' + $item.pymnt_due_id + '" \
                                    course_id="' + $item.course_id + '"\
                                    class_id="' + $item.class_id + '" company_name="' + $item.company_name + '"\
                                    course_name="' + $item.crse_name + '" class_name="' + $item.class_name + '"\                                   \n\
                                    unit_fees ="' + $item.class_fees + '" >\
                                    (' + $item.tax_code + ') ' + $item.first_name + '\
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Class: ' + $item.crse_name + ' - ' + $item.class_name + '&nbsp;&nbsp;&nbsp;&nbsp;\
                                     Duration: ' + $item.class_start_datetime + ' to ' + $item.class_end_datetime + '\
                                    &nbsp;&nbsp;&nbsp;&nbsp;Enrolled On: ' + $item.enrolled_on + '\
                           </option>';
                       
                        $('#change_taxcode').append($value);
                    });
                    $('#change_taxcode').hide();
                    $('#change_taxcode_autocomplete').show();
                    $('#change_taxcode_autocomplete').prev('b').show();
                    $('#search_enrolment').show();
                } else {
                    $('#change_taxcode_autocomplete').hide();
                    $('#change_taxcode_autocomplete').prev('b').hide();
                    $('#change_taxcode').before('<span class="red change_taxcode_err" style="margin-left: 250px;font-weight: bold">There are currently no Individual invoices available.</span>');
                }
            } 
            
             //add by pritam
            else if ($type == "remvind") 
            {
                var change_individual = data.change_individual;
                $('#change_taxcode1').html($select);
                if (change_individual != "") 
                {
                    $.each(change_individual, function(i, $item) 
                    {
                        $value = '<option value="' + $item.user_id + '" pymnt_due_id="' + $item.pymnt_due_id + '" \
                                    course_id="' + $item.course_id + '"\
                                    class_id="' + $item.class_id + '" company_name="' + $item.company_name + '"\
                                    course_name="' + $item.crse_name + '" class_name="' + $item.class_name + '"\                                   \n\
                                    unit_fees ="' + $item.class_fees + '">\
                                    (' + $item.tax_code + ') ' + $item.first_name + '\
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Class: ' + $item.crse_name + ' - ' + $item.class_name + '&nbsp;&nbsp;&nbsp;&nbsp;\
                                     Duration: ' + $item.class_start_datetime + ' to ' + $item.class_end_datetime + '\
                                    &nbsp;&nbsp;&nbsp;&nbsp;Enrolled On: ' + $item.enrolled_on + '\
                           </option>';
                        $('#change_taxcode1').append($value);
                    });
                    $('#change_taxcode1').hide();
                    $('#change_taxcode_autocomplete1').show();
                    $('#change_taxcode_autocomplete1').prev('b').show();
                    $('#search_enrolment').show();
                    
                } 
                else 
                {
                    $('#change_taxcode_autocomplete1').hide();
                    $('#change_taxcode_autocomplete1').prev('b').hide();
                    $('#change_taxcode1').before('<span class="red change_taxcode_err" style="margin-left: 250px;font-weight: bold">There are currently no Individual invoices available..</span>');
                }
            } 
            
            else if ($type == "remove_invoice" || $type == "add_invoice") 
            {
                $('#remove_company_select').html($select);
                var company_not_paid_invoice = data.company_not_paid_invoice;
                if (company_not_paid_invoice != "" && company_not_paid_invoice!= null) 
                {
                    $.each(company_not_paid_invoice, function(i, $item) 
                    {
                        $value ='<option value="' + $item.company_id + '" pymnt_due_id="' + $item.pymnt_due_id + '"\
                                   company_id="' + $item.company_id + '" course_id="' + $item.course_id + '"\
                                   class_id="' + $item.class_id + '" company_name="' + $item.company_name + '"\
                                   course_name="' + $item.crse_name + '" class_name="' + $item.class_name + '" \
                                   unit_fees ="' + $item.class_fees + '">\
                                   Company Name: ' + $item.company_name + '&nbsp;&nbsp;&nbsp;Invoice Id: ' + $item.invoice_id + '&nbsp;&nbsp;&nbsp;Invoice Date: ' + $item.inv_date + '\
                                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Class: ' + $item.crse_name + ' - ' + $item.class_name + '&nbsp;&nbsp;&nbsp;&nbsp;\
                                   Duration: ' + $item.class_start_datetime + ' to ' + $item.class_end_datetime + '\
                                </option>';
                        $('#remove_company_select').append($value);
                    });
                    $('#remove_company_select').hide();
                    $('#remove_company_autocomplete').show();
                    $('#remove_company_autocomplete').prev('b').show();
                    //$('#search_enrolment').show(); 
                } 
                else 
                {
                    $('#remove_company_autocomplete').hide();
                    $('#remove_company_autocomplete').prev('b').hide();
                    $('#remove_company_select').before('<span class="red remove_company_err" style="margin-left: 250px;font-weight: bold">There are currently no Company invoices available.</span>');
                }
            } 
            //add by pritam
            else if ($type == "move_invoice") 
            {
                $('#move_company_select').html($select);
                var company_not_paid_invoice = data.company_not_paid_invoice;
                if (company_not_paid_invoice != "" && company_not_paid_invoice!= null) 
                {
                    $.each(company_not_paid_invoice, function(i, $item) 
                    {
                        $value ='<option value="' + $item.company_id + '" pymnt_due_id="' + $item.pymnt_due_id + '"\
                                   company_id="' + $item.company_id + '" course_id="' + $item.course_id + '"\
                                   class_id="' + $item.class_id + '" company_name="' + $item.company_name + '"\
                                   course_name="' + $item.crse_name + '" class_name="' + $item.class_name + '" \
                                   unit_fees ="' + $item.class_fees + '">\
                                   Company Name: ' + $item.company_name + '&nbsp;&nbsp;&nbsp;Invoice Id: ' + $item.invoice_id + '&nbsp;&nbsp;&nbsp;Invoice Date: ' + $item.inv_date + '\
                                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Class: ' + $item.crse_name + ' - ' + $item.class_name + '&nbsp;&nbsp;&nbsp;&nbsp;\
                                   Duration: ' + $item.class_start_datetime + ' to ' + $item.class_end_datetime + '\
                                </option>';
                        $('#move_company_select').append($value);
                    });
                    $('#move_company_select').hide();
                    $('#move_company_autocomplete').show();
                    $('#move_company_autocomplete').prev('b').show();
                    $('#search_enrolment').show();
                } 
                else 
                {
                    $('#move_company_autocomplete').hide();
                    $('#move_company_autocomplete').prev('b').hide();
                    $('#move_company_select').before('<span class="red remove_company_err" style="margin-left: 250px;font-weight: bold">There are currently no Company invoices available.</span>');
                }
            }
            else if ($type == "to_move_invoice") 
            {
                $('#to_move_company_select').html($select);
                var company_not_paid_invoice = data.company_not_paid_invoice;
                if (company_not_paid_invoice != "" && company_not_paid_invoice!= null) 
                {
                    $.each(company_not_paid_invoice, function(i, $item) 
                    {
                        $value ='<option value="' + $item.company_id + '" pymnt_due_id="' + $item.pymnt_due_id + '"\
                                   company_id="' + $item.company_id + '" course_id="' + $item.course_id + '"\
                                   class_id="' + $item.class_id + '" company_name="' + $item.company_name + '"\
                                   course_name="' + $item.crse_name + '" class_name="' + $item.class_name + '" \
                                   unit_fees ="' + $item.class_fees + '">\
                                   Company Name: ' + $item.company_name + '&nbsp;&nbsp;&nbsp;Invoice Id: ' + $item.invoice_id + '&nbsp;&nbsp;&nbsp;Invoice Date: ' + $item.inv_date + '\
                                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Class: ' + $item.crse_name + ' - ' + $item.class_name + '&nbsp;&nbsp;&nbsp;&nbsp;\
                                   Duration: ' + $item.class_start_datetime + ' to ' + $item.class_end_datetime + '\
                                </option>';
                        $('#move_company_select').append($value);
                    });
                    $('#to_move_company_select').hide();
                    $('#to_move_company_select').show();
                    $('#to_move_company_select').prev('b').show();
                    $('#search_enrolment').show();
                } 
                else 
                {
                    $('#to_move_company_select').hide();
                    $('#to_move_company_select').prev('b').hide();
                    $('#to_move_company_select').before('<span class="red remove_company_err" style="margin-left: 250px;font-weight: bold">There are currently no Company invoices available.</span>');
                }
            }
            
            else if ($type == "new") 
            {
                var courses = data.courses;
                $('#course').html($select);
                $.each(courses, function(i, $item) 
                {
                    $value = '<option value="' + i + '">' + $item + '</option>'
                    $('#course').append($value);
                });
                var companies = data.companies;
                $('#company').html($select);
                $.each(companies, function(i, $item) 
                {
                    $value = '<option value="' + $item.company_id + '">' + $item.company_name + '</option>'
                    $('#company').append($value);
                });
            }
        }
    });
}

function add_remove_invoice_validate() {
    var $button_text = $('.submit_button').text();
    if ($button_text == 'Remove Enrollment' || $button_text == 'Add Enrollment' || $button_text == 'Move Enrollment') {
        if ($(".trainee_check_box:checked").length > 0) {
            $('.check_box_alert').hide();
            return true;
        } else {
            $('.check_box_alert').show();
            return false;
        }
    } else {
        return true;
    }
}
$(document).ready(function(){
   $("#change_taxcode_autocomplete").autocomplete({
        source: function(request, response) {
            $url = $siteurl + "class_trainee/get_select_box";
            $('#change_taxcode').val('');
            if(request.term.length > 0){
                $.ajax({
                    url: $url,
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term,
                        type: 'change'
                    },
                    success: function(result) {
                        var data = [];
                        $.each(result.change_individual, function(i, $item) {
                            data.push({
                                key_userid:$item.user_id ,
                                key_pymnt_due_id:$item.pymnt_due_id ,
                                label:'(' + $item.tax_code + ') ' + $item.first_name
                                         + '   Class: ' + $item.crse_name + ' - ' + $item.class_name
                                         + '   Duration: ' + $item.class_start_datetime + ' to ' + $item.class_end_datetime
                                         + '   Enrolled On: ' + $item.enrolled_on,
                            });
                        });
                        var d = jQuery.parseJSON(data);
                        response(data);
                    }
                });
            }else{
                var d;
                response(d);
            }
        },
        select: function(event, ui) {
            var value = ui.item.key_userid;
            var pymnt_due_id = ui.item.key_pymnt_due_id;
            $('#change_taxcode option[value="'+value+'"][pymnt_due_id="'+pymnt_due_id+'"]').attr('selected','selected');
        },
        minLength:0
    });
    //tremove add by pritam
    $("#change_taxcode_autocomplete1").autocomplete({
        source: function(request, response) 
        {
            $url = $siteurl + "class_trainee/get_select_box";
            $('#change_taxcode1').val('');
            if(request.term.length > 0){
                $.ajax({
                    url: $url,
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term,
                        type: 'remvind'
                    },
                    success: function(result) 
                    {
                        var data = [];
                        $.each(result.change_individual, function(i, $item) 
                        {
                            data.push({
                                key_userid:$item.user_id ,
                                key_pymnt_due_id:$item.pymnt_due_id ,
                                label:'(' + $item.tax_code + ') ' + $item.first_name
                                         + '   Class: ' + $item.crse_name + ' - ' + $item.class_name
                                         + '   Duration: ' + $item.class_start_datetime + ' to ' + $item.class_end_datetime
                                         + '   Enrolled On: ' + $item.enrolled_on,
                            });
                        });
                        
                        var d = jQuery.parseJSON(data);
                        response(data);
                    }
                });
            }
            else
            {
                var d;
                response(d);
            }
        },
        select: function(event, ui) {
            var value = ui.item.key_userid;
            var pymnt_due_id = ui.item.key_pymnt_due_id;
            $('#change_taxcode1 option[value="'+value+'"][pymnt_due_id="'+pymnt_due_id+'"]').attr('selected','selected');
        },
        minLength:0
    });
    $("#move_company_autocomplete").autocomplete({
        source: function(request, response) 
        {
            $enrollment_type_value = $('.enrollment_type:checked').val();
            $url = $siteurl + "class_trainee/get_select_box";
            $('#remove_company_select').val('');
            
            if(request.term.length > 0)
            {
                $.ajax({
                    url: $url,
                    type: "post",
                    dataType: "json",
                    data: 
                    {
                        q: request.term,
                       
                        type: $enrollment_type_value
                    },
                    success: function(result) 
                    {
                        var data = [];
                        if (result.company_not_paid_invoice !== null) 
                        {
                            $.each(result.company_not_paid_invoice, function(i, $item) 
                            {
                                data.push({
                                    key_company_id:$item.company_id ,
                                    key_pymnt_due_id:$item.pymnt_due_id ,
                                    label:$item.company_name + '   Invoice Id: ' + $item.invoice_id + '   Invoice Date: ' + $item.inv_date +
                                            ' Class: ' + $item.crse_name + ' - ' + $item.class_name + 
                                            ' Duration: ' + $item.class_start_datetime + ' to ' + $item.class_end_datetime
                                });
                            });
                        }
                        var d = jQuery.parseJSON(data);
                        response(data);
                    }
                });
            }
            else
            {
                var d;
                response(d);
            }
        },
        select: function(event, ui) 
        {
            var value = ui.item.key_company_id;
            var pymnt_due_id = ui.item.key_pymnt_due_id;
            $('#move_company_select option[value="'+value+'"][pymnt_due_id="'+pymnt_due_id+'"]').attr('selected','selected');
        },
        minLength:0
    });
        $("#to_move_company_autocomplete").autocomplete({
        source: function(request, response) 
        {
            $enrollment_type_value = "to_move_invoice";
            $url = $siteurl + "class_trainee/get_select_box";
            $('#remove_company_select').val('');
            $company_id=$('.company_id').val();
            $course_id=$('.course_id').val();
            $class_id=$('.class_id').val();
           // alert($class_id);
            if(request.term.length > 0)
            {
                $.ajax({
                    url: $url,
                    type: "post",
                    dataType: "json",
                    data: 
                    {
                        q: request.term,
                        company_id:$company_id,
                        course_id:$course_id,
                        class_id:$class_id,
                        type: $enrollment_type_value
                    },
                    success: function(result) 
                    {
                        var data = [];
                        if (result.company_not_paid_invoice !== null) 
                        {
                            $.each(result.company_not_paid_invoice, function(i, $item) 
                            {
                                data.push({
                                    key_company_id:$item.company_id ,
                                    key_pymnt_due_id:$item.pymnt_due_id ,
                                    key_invoice_id:$item.invoice_id ,
                                    key_course_id:$item.course_id ,
                                    key_class_id:$item.class_id ,
                                    key_lock_id : $item.lock_status,
                                    label:$item.company_name + 'Invoice Id: ' + $item.invoice_id + '   Invoice Date: ' + $item.inv_date +
                                            ' Class: ' + $item.crse_name + '(' + $item.course_id + ') - ' + $item.class_name + '(' + $item.class_id + ')' +
                                            ' Duration: ' + $item.class_start_datetime + ' to ' + $item.class_end_datetime
                                    
                                });
                                
                            });
                            
                        }
                        var d = jQuery.parseJSON(data);
                        response(data);
                    }
                });
            }
            else
            {
                var d;
                response(d);
            }
        },
        select: function(event, ui) 
        {
            var value = ui.item.key_company_id;
            var pymnt_due_id = ui.item.key_pymnt_due_id;
            $('#to_move_company_select option[value="'+value+'"][pymnt_due_id="'+pymnt_due_id+'"]').attr('selected','selected');
            $('#to_comp_pymnt_due_id').val(ui.item.key_pymnt_due_id);
            $('#to_comp_invoice_id').val(ui.item.key_invoice_id);
            $('#to_company_id').val(ui.item.key_company_id);
            $('#to_course_id').val(ui.item.key_course_id);
            $('#to_class_id').val(ui.item.key_class_id);
            if(ui.item.key_lock_id==1){
            $('.submit_button').prop('disabled',true);
            $('.lock_class_atten').show();}
            
        },
        minLength:0
    });
    //end
    $("#remove_company_autocomplete").autocomplete({
        source: function(request, response) 
        {
             $('#search_enrolment').hide();
            $enrollment_type_value = $('.enrollment_type:checked').val();
            $url = $siteurl + "class_trainee/get_select_box";
            $('#remove_company_select').val('');
            if(request.term.length > 0){
                $.ajax({
                    url: $url,
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term,
                        type: $enrollment_type_value
                    },
                    success: function(result) {
                        var data = [];
                        if (result.company_not_paid_invoice !== null) {
                            $.each(result.company_not_paid_invoice, function(i, $item) {
                                data.push({
                                    key_company_id:$item.company_id ,
                                    key_pymnt_due_id:$item.pymnt_due_id ,
                                   
                                    label:$item.company_name + '   Invoice Id: ' + $item.invoice_id + '   Invoice Date: ' + $item.inv_date +
                                            ' Class: ' + $item.crse_name + ' - ' + $item.class_name + 
                                            ' Duration: ' + $item.class_start_datetime + ' to ' + $item.class_end_datetime
                                });
                            }); 
                        }
                        var d = jQuery.parseJSON(data);
                        response(data);
                    }
                });
            }else{
                var d;
                response(d);
            }
        },
        select: function(event, ui) {
            var value = ui.item.key_company_id;
            var pymnt_due_id = ui.item.key_pymnt_due_id;
             $('#search_enrolment').show(); 
            $('#remove_company_select option[value="'+value+'"][pymnt_due_id="'+pymnt_due_id+'"]').attr('selected','selected');
            
        },
        minLength:0
    });
});

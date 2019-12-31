/**
 * This js file includes in  reschedule page
 */
$(document).ready(function() {
    $('#type').change(function() {
        $('.multiSelectOptions').css({'width': '143px'});
        $('.multiSelect span').css({'width': '143px'});
        $('#row_dim1').hide();
        $('#row_dim2').hide();
        $('#row_dim3').hide();
      	$('#row_dim4').hide(); 
        $('#row_dim').hide();
        $('.row_main').hide();
        
        $('.cannot_change_div').hide();
        $val = $(this).val();
        if ($val.length > 0) {
            $('#tax_code').val('');
            $('#taxcode_id').val('');

            $('#course_id').val('');
            $('#course_id').trigger('change');

            $('#company').val('');
            $('#trainee_name').val('');
            $('#trainee_id').val('');

            $('#taxcode_alert').remove();
            if ($val == 1) {
                $('#row_dim1').show();
            } else if ($val == 2) {
                $('#row_dim').show();
            } else if ($val == 3) {
                $('#row_dim3').show();
            }else if ($val == 4) { 
                $('#row_dim4').show();
            }
        }
    });
   $('.active_class').change(function() 
    {
        $(".reschedule_class").prop("checked", false);
        $active_course = $('.active_class:checked').attr('course_id');
        $active_lock_status = $('.active_class_lock_status').val();
        $i=0;
        $('.reschedule_class').each(function() 
        {
            $re_class = $('.reschedule_class_lock_status_'+$i).val();
            $reschedule_lock_status = $('.reschedule_class_lock_status_'+$active_course).val();
            if ($(this).hasClass('course_' + $active_course)) 
            {
                $reschedule_lock_status = $('reschedule_class').val();
                if($re_class==0){
                    $(this).removeAttr('disabled');
                }
            } 
            else 
            {
                $(this).attr('disabled', 'disabled');
            }
            $i++;
            
        });
    });
    $('#course_active_class').change(function() {
        $trainee = $('#control_6');
        $traineen = $('#control_n');
        $class_id = $('#course_active_class').val();
        $('#course_reschedule_class option').removeAttr('style');
        $('#course_reschedule_class option[value="' + $class_id + '"]').attr('style', 'display:none;');
        $.ajax({
            type: 'post',
            url: $siteurl + '/class_trainee/get_trainee_related_json',
            data: {class_id: $class_id},
            dataType: "json",
            beforeSend: function() {
                $trainee.siblings('.multiSelectOptions').remove();
                $trainee.remove();
                $traineen.remove();
            },
            success: function(res) {
                $('#course_reschedule_class').removeAttr('disabled');
                if (res.trainee == null) {
                    disp_err('#control_6', '[No trainee available to re-schedule to.]');
                    $('.re_schedule').hide();
                } else {
                    $('.re_schedule').show();
                    remove_err('#control_6');
                    $('#control_6_err').before('<select multiple="multiple" style="width:78%;" class="control_6" id="control_6" name="control_6[]"></select>');
                    $.each(res.trainee, function(i, item) {
                        $('.control_6').append('<option value="' + item.key + '">' + item.label + '</option>');
                    });
                    $("#control_6").multiSelect({oneOrMoreSelected: '*'});
                }

            }
        });
        $course_id = $('#course_id').val();
        $active_class = $('#course_active_class');
        $.ajax({
            url: $siteurl + "class_trainee/get_reschedule_course_details",
            type: "post",
            dataType: "json",
            data: {"course_id": $course_id, active_class: $active_class.val()},
            beforeSend: function() {

            },
            success: function(res) {
                $('#course_reschedule_class').remove();
                if (res.reschedule == null) {
                    disp_err('#course_reschedule_class', '[No classes available to re-schedule to.]');
                    $('.re_schedule').hide();
                    $('.trainee_tr').hide();
                } else {
                    remove_err('#course_reschedule_class');
                    $('.re_schedule').show();
                    $('.trainee_tr').show();
                    $('.multiSelect span').css({'width': '580px'});
                    $('.multiSelectOptions').css({'width': '580px'});
                    $('#course_reschedule_class_err').before('<select id="course_reschedule_class" name="course_reschedule_class"></select>');
                    $reschedule_class = $('#course_reschedule_class');
                    $reschedule_class.html('<option value="">Select</option>');
                    $.each(res.reschedule, function(i, item) {
                            var data=item.key;
                            var arr = data.split(',');
                            var class_id= arr[0];
                            var lock_status= arr[1];
                            if(lock_status==1){
                                $reschedule_class.append('<option value="' + class_id + '" disabled>' + item.value + '  locked</option>');
                            }else{
                                $reschedule_class.append('<option value="' + class_id + '">' + item.value + '</option>');
                            }
                    });
                }
            }
        });
    });
    $(document).on('change', '#course_reschedule_class', function() {
        $class_id = $('#course_reschedule_class').val();
        $.ajax({
            type: 'post',
            url: $siteurl + '/class_trainee/get_trainee_related_json',
            data: {class_id: $class_id},
            dataType: "json",
            beforeSend: function() {
            },
            success: function(res) {
                $('input[name="control_6[]"]').removeAttr('disabled');
                $('.multiSelectOptions label').removeAttr('style');
                $('.err_span').remove();
                if (res != '') {
                    $.each(res.trainee, function(i, item) {
                        $('input[name="control_6[]"][value="' + item.key + '"]').attr('disabled', 'disabled').removeAttr('checked');
                        $('input[name="control_6[]"][value="' + item.key + '"]').parent().css('display', 'none');
                    });
                    $pre_length = $('input[name="control_6[]"]').not(':disabled').length;
                    if (parseInt($pre_length) == 0) {
                        $('.multiSelectOptions').prepend('<span class="error err_span">There are no trainees to reschedule for this class</span>')
                        $('.multiSelectOptions label').css('display', 'none');
                    }
                }
            }
        });
    });
    $search_check = 0;
    $('.search_button').click(function() {
        $search_check = 1;
        return search_validate(true);
    });
    $('#tax_code, #trainee_name, #company').change(function() {
        if ($search_check == 1) {
            return search_validate(false);
        }
    });
    $('.re_schedule').click(function() {
        $retval = true;
        var trainee_id_notpaid ="";
        var trainee_active_class ="";
        $type = $('#type').val();
        if ($type.length > 0) {
            if ($type == 1) {
                $tax_code = $.trim($('#tax_code').val());
                trainee_id_notpaid = $taxcode_id = $('#taxcode_id').val();
                if ($tax_code.length == 0) {
                    $retval = false;
                    disp_err('#tax_code');
                } else if ($taxcode_id.length == 0) {
                    $retval = false;
                    disp_err('#tax_code', '[Select NRIC/FIN No. from auto-help]');
                } else {
                    remove_err('#tax_code');
                }
                $active_class = '';
                $('input.active_class:checked').each(function() {
                    $active_class += $(this).val();
                })
                trainee_active_class = $active_class;
                if ($active_class.length == 0) {
                    $retval = false;
                    disp_err('#active_class');
                } else {
                    remove_err('#active_class');
                }
                $reschedule_class = '';
                $('input.reschedule_class:checked').each(function() {
                    $reschedule_class += $(this).val();
                })
                if ($reschedule_class.length == 0) {
                    $retval = false;
                    disp_err('#reschedule_class');
                } else {
                    remove_err('#reschedule_class');
                }
            } else if ($type == 3) {
                $company = $('#company').val();
                if ($company.length == 0) {
                    $retval = false;
                    disp_err('#company');
                } else {
                    remove_err('#company');
                }
                $trainee_name = $.trim($('#trainee_name').val());
                trainee_id_notpaid = $trainee_id = $('#trainee_id').val();
                if ($trainee_name.length == 0) {
                    $retval = false;
                    disp_err('#trainee_name');
                } else if ($trainee_id.length == 0) {
                    $retval = false;
                    disp_err('#trainee_name', '[Select trainee name from auto-help]');
                } else {
                    remove_err('#trainee_name');
                }
                $active_class = '';                
                $('input.active_class:checked').each(function() {
                    $active_class += $(this).val();                    
                })
                trainee_active_class = $active_class;
                if ($active_class.length == 0) {
                    $retval = false;
                    disp_err('#active_class');
                } else {
                    remove_err('#active_class');
                }
                $reschedule_class = '';
                $('input.reschedule_class:checked').each(function() {
                    $reschedule_class += $(this).val();
                })
                if ($reschedule_class.length == 0) {
                    $retval = false;
                    disp_err('#reschedule_class');
                } else {
                    remove_err('#reschedule_class');
                }
            } else if ($type == 4) { 
                $tax_code = $.trim($('#trainee_name_serach').val());
                trainee_id_notpaid = $taxcode_id = $('#taxcode_user_id').val();
                if ($tax_code.length == 0) {
                    $retval = false;
                    disp_err('#trainee_name_serach');
                } else if ($taxcode_id.length == 0) {
                    $retval = false;
                    disp_err('#trainee_name_serach', '[Select NRIC/FIN No. from auto-help]');
                } else {
                    remove_err('#trainee_name_serach');
                }
                $active_class = '';
                $('input.active_class:checked').each(function() {
                    $active_class += $(this).val();
                })
                trainee_active_class = $active_class;
                if ($active_class.length == 0) {
                    $retval = false;
                    disp_err('#active_class');
                } else {
                    remove_err('#active_class');
                }
                $reschedule_class = '';
                $('input.reschedule_class:checked').each(function() {
                    $reschedule_class += $(this).val();
                })
                if ($reschedule_class.length == 0) {
                    $retval = false;
                    disp_err('#reschedule_class');
                } else {
                    remove_err('#reschedule_class');
                }
            } else {
                $course_id = $('#course_id').val();
                if ($course_id.length == 0) {
                    $retval = false;
                    disp_err('#course_id');
                } else {
                    remove_err('#course_id');
                }
                trainee_active_class = $course_active_class = $('#course_active_class').val();
                if ($course_active_class.length == 0) {
                    $retval = false;
                    disp_err('#course_active_class');
                } else {
                    remove_err('#course_active_class');
                }
                $course_reschedule_class = $('#course_reschedule_class').val();
                if ($course_reschedule_class.length == 0) {
                    $retval = false;
                    disp_err('#course_reschedule_class');
                } else if ($course_reschedule_class == $course_active_class) {
                    $retval = false;
                    disp_err('#course_reschedule_class', '[Reschedule From and To class are equal]');
                } else {
                    remove_err('#course_reschedule_class');
                }
                $trainee = '';
                $('input[name="control_6[]"]:checked').each(function() {
                    $trainee += $(this).val();
                    trainee_id_notpaid += $(this).val()+',';
                })
                if ($trainee.length == 0) {
                    $retval = false;
                    disp_err('#control_6');
                } else {
                    remove_err('#control_6');
                }
                $.ajax({
                    url: $siteurl + "class_trainee/get_class_booked_count",
                    type: "post",
                    data: {class: $('#course_reschedule_class').val()},
                    async: false,
                    success: function(res) {
                        if (res != 'any') {
                            $user_count = $("input[name='control_6[]']:checked").length;
                            if (parseInt($user_count) > parseInt(res)) {
                                alert('Total seats being rescheduled, exceeds total available seats. Total seats available: ' + res + '. Please reduce the number of reschedules and try again.');
                                $retval = false;
                            }
                        }
                    }
                });
            }
            if($retval == true) {                  
                $retval = check_selected_trainee_status(trainee_id_notpaid, trainee_active_class );                
            }
            if($retval == false) {
                $('.row_main').hide();
            }
            return $retval;
        }
    });
    $('.save').click(function() {
        $retval = true;
        $reschedule_reason = $('#reschedule_reason').val();
        if ($reschedule_reason.length == 0) {
            $retval = false;
            disp_err('#reschedule_reason');
        } else if ($reschedule_reason == 'OTHERS') {
            $other_reason = $.trim($('#other_reason').val());
            if ($other_reason.length == 0) {
                $retval = false;
                disp_err('#other_reason');
            } else {
                remove_err('#other_reason');
                remove_err('#reschedule_reason');
            }
        } else {
            remove_err('#reschedule_reason');
        }
        if($retval == true) {
            $(this).hide();
        }
        return $retval;
    });
    $('#reschedule_reason').change(function() {
        if ($(this).val() == 'OTHERS') {
            $('#other_reason').fadeIn();
        } else {
            $('#other_reason').fadeOut();
            $('#other_reason').val('');
        }
    });
    $('#course_id').change(function() {
        $('.row_main').hide();
        $('#course_reschedule_class').attr('disabled', 'disabled');
        $trainee = $('#control_6');
        $traineen = $('#control_n');
        $trainee.siblings('.multiSelectOptions').remove();
        $trainee.remove();
        $traineen.remove();
        $('#control_6_err').before('<select disabled="disabled" id="control_n"><option>select</option></select>');
        $course_id = $('#course_id').val();
        $active_class = $('#course_active_class');
        $reschedule_class = $('#course_reschedule_class');
        $.ajax({
            url: $siteurl + "class_trainee/get_reschedule_course_details",
            type: "post",
            dataType: "json",
            data: {"course_id": $course_id},
            beforeSend: function() {
                $active_class.html('<option value="">Select</option>');
                $reschedule_class.html('<option value="">Select</option>');
            },
            success: function(res) {
                $active_class.removeAttr('disabled');
                $.each(res.active, function(i, item) {
                    var data=item.key;
                    var arr = data.split(',');
                    var class_id= arr[0];
                    var lock_status= arr[1];
                    if(lock_status==1){
                        $active_class.append('<option value="' + class_id + '" disabled>' + item.value + '  locked</option>');
                    }else{
                        $active_class.append('<option value="' + class_id + '">' + item.value + '</option>');}
                });
                $.each(res.reschedule, function(i, item) {
                });
            }
        });
    });
    $("#tax_code").autocomplete({
        source: function(request, response) {
            $('#tax_code').val($('#tax_code').val().toUpperCase())
            $('#taxcode_id').val('');
            $.ajax({
                url: $siteurl + "class_trainee/get_all_trainees",
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
        },
        minLength: 4
    });

    $("#trainee_name_serach").autocomplete({
        source: function(request, response) {
            $('#trainee_name_serach').val($('#trainee_name_serach').val().toUpperCase())
            $('#taxcode_user_id').val('');
            $.ajax({
                url: $siteurl + "class_trainee/get_all_trainee_names",
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
            $('#taxcode_user_id').val(id);
        },
        minLength: 4
    });
    $("#trainee_name").autocomplete({
        source: function(request, response) {
            $company_id = $('#company').val();
            if ($company_id.length == 0) {
                return false;
            }
            $('#trainee_name').val($('#trainee_name').val().toUpperCase())
            $('#trainee_id').val('');
            $.ajax({
                url: $siteurl + "class_trainee/get_all_companytrainees",
                type: "post",
                dataType: "json",
                data: {
                    company_id: $company_id,
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
        },
        minLength: 4
    });
    if ($('#course_id').val().length == 0) {
        $('#course_active_class').attr('disabled', 'disabled');
        $('#course_reschedule_class').attr('disabled', 'disabled');
    } else {
        $('#course_reschedule_class').trigger('change');
    }
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
function search_validate($retval) {
    $type_check = $('#type').val();
    if ($type_check == 1) {
        $tax_code = $.trim($('#tax_code').val());
        $taxcode_id = $('#taxcode_id').val();
        if ($tax_code.length == 0) {
            $retval = false;
            disp_err('#tax_code');
        } else if ($taxcode_id.length == 0) {
            $retval = false;
            disp_err('#tax_code', '[select NRIC/FIN No. from autofill-help]');
        } else {
            remove_err('#tax_code');
        }
    } else if ($type_check == 3) {
        $company = $('#company').val();
        if ($company.length == 0) {
            $retval = false;
            disp_err('#company');
        } else {
            remove_err('#company');
        }
        $trainee_name = $.trim($('#trainee_name').val());
        $trainee_id = $('#trainee_id').val();
        if ($trainee_name.length == 0) {
            $retval = false;
            disp_err('#trainee_name');
        } else if ($trainee_id.length == 0) {
            $retval = false;
            disp_err('#trainee_name', '[select trainee name from autofill-help]');
        } else {
            remove_err('#trainee_name');
        }
    } else if ($type_check == 4) { 
        $trainee_name_serach = $.trim($('#trainee_name_serach').val());
        $taxcode_user_id = $('#taxcode_user_id').val();
        if ($trainee_name_serach.length == 0) {
            $retval = false;
            disp_err('#trainee_name_serach');
        } else if ($taxcode_user_id.length == 0) {
            $retval = false;
            disp_err('#trainee_name_serach', '[select trainee from autofill-help]');
        } else {
            remove_err('#trainee_name_serach');
        }
    }
    return $retval;
}

function check_selected_trainee_status(trainee_id, class_id) {
    var status; 
    $.ajax({
        url: $siteurl + "class_trainee/check_reschedule_status",
        type: "post",
        data: {
            class_id: class_id,            
            trainee_id : trainee_id
        },
        async: false,
        dataType: "json",
        success: function(res) {            
            if (res.status == 'PAID') {
                $('.cannot_change_div').show();
                status = false;
            }else {
                $('.cannot_change_div').hide();
                status = true;
            }
        }
    });
    return status;
}
///////added by shubhranshu to prevent multiple clicks////////////////
function disable_button(){
//   var self = $('#search_form'),
//    button = self.find('input[type="submit"],button');
//    button.attr('disabled','disabled').html('Please Wait..');
    return true;
}///////added by shubhranshu to prevent multiple clicks////////////////
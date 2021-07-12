/* 
 * This js file included in  add_new_class form
 */
$(document).ready(function() {
     $start_date_check = check_start_or_today();
    $('.form_reset').click(function() {
        $('#AddclassForm')[0].reset();
        $('.error_text').text('');
        $('.error').removeClass('error');
        $('.schld_div div table tbody tr').remove();
        $('.def_schld_div div table tbody tr').remove();
        $('.schld_div div table').after('<div class="error error_text">No Schedule available</div>');
        $('.def_schld_div div table').after('<div class="error error_text">No Schedule available</div>');
    });
    $('#schld_start_time').timepicker({
        showLeadingZero: false,
        onSelect: tpStartSelect,
    });
    $('#start_time').timepicker({
        showLeadingZero: false,
    });
    $('#end_time').timepicker({
        showLeadingZero: false,
    });
    $('#schld_end_time').timepicker({
        showLeadingZero: false,
    });
    $('#def_schld_start_time').timepicker({
        showLeadingZero: false,
        onSelect: deftpStartSelect,
    });
    $('#def_schld_end_time').timepicker({
        showLeadingZero: false,
    }); 
    var check = 0;
    $('#AddclassForm').submit(function() {
        check = 1;
        //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM////////////////////////////////////
        if(validate()){
            var self = $(this),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            return true;
        }else{
           return false; 
        }
       //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM///////////////////////////////
    });    
    $(document).on('change', '#AddclassForm select,#AddclassForm input,#AddclassForm textarea', function() {
        if (check == 1) {
            return validate();
        }
    });
    $(document).on('change', '#ex4 input', function() {
        reminder_popup_validate();
    });
    $(document).on('change', '#ex1 input, #ex1 select', function() {
        schld_form_validate();
    });
    $(document).on('change', '#ex3 input, #ex3 select', function() {
        def_schld_form_validate(false);
    });    
    $("#fees,#class_discount,#cls_duration,#lab_duration,#class_assmnt_duration").keydown(function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });    
    $("#total_seats,#minimum_students,#reminder1,#reminder2,#reminder3").keydown(function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105) || (e.keyCode == 190)) {
            e.preventDefault();
        }
    });    
    $("#schld_end_time,#schld_start_time").keydown(function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) || (e.keyCode == 59 && e.shiftKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105) || (e.keyCode == 190)) {
            e.preventDefault();
        }
    });    
    $('.def_save').click(function() {
        return def_schld_form_validate(true);
    });
   
    $('.schld_save').click(function() {
        return schld_form_validate(true);
    });
    
    $(document).on('click', '.schld_edit', function() {
        $class = $(this).parent().parent().attr('class');
        $parent = $('.' + $class);
        $('#schld_date').val($parent.children('.schlded_date').val());
        $('#schld_session_type').val($parent.children('.schlded_session_type').val());
        $('#schld_start_time').val($parent.children('.schlded_start_time').val());
        $('#schld_end_time').val($parent.children('.schlded_end_time').val());
        $('.schld_save').attr('data-edit', $class);
        schld_form_validate(false);
    });
    
    $(document).on('click', '.schld_delete', function() {
        $parent_tr = $(this).parent().parent();
        $count = $parent_tr.attr('data-count');
        $('.schld_alert_yes').attr('data-count', $count);
    });
   
    $(document).on('click', '.def_schld_delete', function() {
        $('.schld_alert_yes').attr('data-count', 'default-assessment');
    });
   
    $('.schld_alert_no').click(function() {
        if ($('.schld_alert_yes').is("[data-count]")) {
            $('.schld_alert_yes').removeAttr('data-count');
        }
    });
    
    $('.schld_alert_yes').click(function() {
        if ($(this).is("[data-count]")) {
            $count = $(this).attr('data-count');
            if ($count == 'default-assessment') {
                default_data_clear();

                $('.def_schld_div .table-responsive table tbody tr').remove();
            } else {
                $date = $('.schld_tr' + $count).attr('data-date');
                $('.schld_tr' + $count).remove();
                if ($('tr').hasClass('schlddate_' + $date)) {
                    $rowspan_td = $('.schlddate_' + $date + ':first td:first');
                    $rowspan = $rowspan_td.attr('rowspan');
                    $rowspan_td.attr('rowspan', --($rowspan));
                    $delete = '<a href="#ex9" rel="modal:open" class="small_text delete_color schld_delete"><input type="button" value="Delete" style="color:#000000; text-decoration:none;" /></a>';
                    $('.schlddate_' + $date + ':last td:last').html($delete);
                }
                $('.schld_alert_yes').removeAttr('data-count');
            }
        }
    });
    $('.sessions_perday').click(function() {
        $session_chk = $('.sessions_perday:checked').val();
        if ($session_chk == 1) {
            $option = '<option value="">Select</option>\
            <option value="S1">Session 1</option>';
        } else {
            $option = '<option value="">Select</option>\
            <option value="S1">Session 1</option>\
            <option value="BRK">Break</option>\
            <option value="S2">Session 2</option>';
        }
        $('#schld_session_type').html($option);
    })
    
    $('.add_schld_form').click(function() {
        return schld_form_clear();
    });
    
    $(document).on('click', '#ex1 .schld_cancel,#ex1 .close-modal', function() {
        return schld_form_clear();
    });
    $('.reminder_cancel').click(function() {
        $('#reminder1').val('');
        remove_err('#reminder1');
        $('#reminder2').val('');
        remove_err('#reminder2');
        $('#reminder3').val('');
        remove_err('#reminder3');
    });
    $('.def_schld_remove').click(function() {
        default_data_clear();
        remove_all();

        $('.def_schld_div .table-responsive table tbody tr').remove();
    });
    
    $(document).on('click', '.alert_message', function() {
        $('#ex11').modal();
        return false;
    });
//    $('.alert_message1').click(function() {
//        $('#ex12').modal();
//        return false;
//    });
   
    $('.close_reminder_popup').click(function() {
        return reminder_popup_validate();
    });
    
    $('#cls_venue').change(function() {
        $val = $(this).val();
        $div = $('.clsven_oth_span');
        if ($val == 'OTH') {
            $div.show();
        } else {
            $('#classroom_venue_oth').val('');
            $div.hide();
        }
    });
    $('#lab_venue').change(function() {
        $val = $(this).val();
        $div = $('.labven_oth_span');
        if ($val == 'OTH') {
            $div.show();
        } else {
            $('#lab_venue_oth').val('');
            $div.hide();
        }
    });
    $('#def_schld_venue').change(function() {
        $val = $(this).val();
        $div = $('.defven_oth_span');
        if ($val == 'OTH') {
            $div.show();
        } else {
            $('#def_venue_oth').val('');
            $div.hide();
        }
    });
    $('#class_course').change(function() {
        $lang = $('#languages');
        $salesexec = $('#control_4');
        if ($('#class_course').val().length > 0) {
            $('.class_display_none').fadeIn('100');
            $('.multiSelectOptions').css({'width': '143px'});
        } else {
            $('.class_display_none').fadeOut('100');
        }
        $.ajax({
            type: 'post',
            url: $siteurl + 'classes/get_course_related_json',
            data: {course_id: $('#class_course').val()},
            dataType: "json",
            beforeSend: function() {
                $lang.html('<option value="">Select</option>');
                $salesexec.siblings('.multiSelectOptions').remove();
                $salesexec.remove();
            },
            success: function(res) {
                $course_duration = parseFloat(res.course_duration);
                $.each(res.languages, function(i, item) {
                    $lang.append('<option value="' + item.key + '">' + item.value + '</option>');
                });
                $('#control_4_err').before('<select multiple="multiple" style="width:78%;" class="control_4" id="control_4" name="control_4[]"></select>');
                $.each(res.salesexec, function(i, item) {
                    $('.control_4').append('<option value="' + item.key + '" selected="selected">' + item.value + '</option>');
                });
                $("#control_4").multiSelect({oneOrMoreSelected: '*'});
                $('#control_3').children('span').text('Select Option');
                $('#control_3').next('.multiSelectOptions').children('label.selectAll').children('input').removeAttr('checked');
                var checkbox_3 = $('input[name="control_3[]"][type="checkbox"]');
                checkbox_3.removeAttr('checked').parent().removeAttr('class');
                var text = '';
                $.each(res.course_manager, function(i,item) {
                    var select_input = $('input[name="control_3[]"][type="checkbox"][value="'+ item +'"]');
                    text += select_input.parent().text();
                    text +=', ';
                    select_input.attr('checked',true).parent().attr('class','checked');
                });
                text = text.slice(0,-2);                
                $('#control_3').children('span').text(text);
                $('#control_3').attr('title',text);   
                $('#crs_admin_email').val(res.crse_admin_email);  
                $('#crse_ref_no').val(res.crse_ref_no); 
            }
        });
    });
    
    $("#ass_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '-50:+50',
        minDate: $start_date_check,
        maxDate: $('#end_date').val(),
                onClose: function() {
                    $(this).trigger("change");
                }
    });
    $('#ass_start_time').timepicker({
        showLeadingZero: false,
        onSelect: asstpStartSelect,
    });
    $('#ass_end_time').timepicker({
        showLeadingZero: false,
    }); 
    $("#start_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+100",
        minDate: ($js_role == "ADMN")? '' : 0,
        onSelect: function(selectedStartDate) {
            selectedStartDate = check_start_or_today();
            $("#end_date").datepicker("option", {
                minDate: selectedStartDate,
                maxDate: '',
            });
            $("#schld_date").datepicker("option", {
                minDate: selectedStartDate,
                maxDate: $('#end_date').val()
            });
            $("#def_date").datepicker("option", {
                minDate: selectedStartDate,
                maxDate: $('#end_date').val()
            });
        },
        onClose: function() {
            $(this).trigger("change");
        }
    });
    $("#end_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+100",
        minDate: 0,
        maxDate: -1,
        onSelect: function(selectedEndDate) {
            selectedStartDate = check_start_or_today();
            $("#coll_date").datepicker("option", {
                minDate: selectedEndDate,
                maxDate: ''
            });
            $("#schld_date").datepicker("option", {
                minDate: selectedStartDate,
                maxDate: selectedEndDate,
            });
            $("#def_date").datepicker("option", {
                minDate: selectedStartDate,
                maxDate: selectedEndDate,
            });
            $('.add_schld_form').removeClass('alert_message');
            $('.add_def_schld_form').removeClass('alert_message');
        },
        onClose: function() {
            $(this).trigger("change");
        }
    });
    $('.add_def_schld_form').click(function() {
        if ($(this).hasClass('alert_message')) {

        } else {
            $('input[name="control_9[]"]').attr('disabled', 'disabled').removeAttr('checked');
            $('#control_9').next('.multiSelectOptions').children('label').css('display', 'none');
            $('#control_9').next('.multiSelectOptions').children('.selectAll').removeAttr('style');
            $('input[name="control_7[]"]:checked').each(function(i) {
                $val = $(this).val();
                $('input[name="control_9[]"][value="' + $val + '"]').removeAttr('disabled');
                $('input[name="control_9[]"][value="' + $val + '"]').parent().removeAttr('style');
            });
            $length = $('input[name="control_9[]"]').not(':disabled').length;
            if (parseInt($length) == 0) {
                $('#ex231').modal();
                return false;
            }
        }
    });
    $("#coll_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+100",
        minDate: 0,
        maxDate: -1,
        onClose: function() {
            $(this).trigger("change");
        }
    });
    $("#schld_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '-50:+100',
        minDate: 0,
        maxDate: -1,
        onClose: function() {
            $(this).trigger("change");
        }
    });
    $("#def_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '-50:+100',
        minDate: 0,
        maxDate: -1,
        onClose: function() {
            $(this).trigger("change");
        }
    });
});

 function convertDate(str) {
        var parts = str.split("-");
        return new Date(parts[1] + "/" + parts[0] + "/" + parts[2]);
     }
function validate() {
    $retval = true;
    $class_course = $('#class_course').val();
    if ($class_course.length == 0) {
        disp_err('#class_course');
        $retval = false;
    } else {
        remove_err('#class_course');
    }
    $class = $('#class_name').val().trim();
    if ($class.length > 0) {
        remove_err('#class_name');
        $.ajax({
            type: 'post',
            url: $siteurl + 'classes/check_classname_unique',
            data: {class_name: $class},
            async: false,
            success: function(res) {
                if (res == 1) {
                    disp_err('#class_name', '[Class name exists!]');
                    $retval = false;
                }
            }
        });
    }
    $start_date = $('#start_date').val();
    if ($start_date.length == 0) {
        disp_err('#start_date');
        $retval = false;
    } else {
        remove_err('#start_date');
    }
    $start_time = $('#start_time').val();
    if ($start_time.length == 0) {
        disp_err('#start_time');
        $retval = false;
    } else {
        remove_err('#start_time');
    }
    $end_date = $('#end_date').val();
    if ($end_date.length == 0) {
        disp_err('#end_date');
        $retval = false;
    } else {
        remove_err('#end_date');
    }
    $end_time = $('#end_time').val();
    if ($end_time.length == 0) {
        disp_err('#end_time');
        $retval = false;
    } else if ($start_date == $end_date) {
        if (parseInt($start_time.replace(':', '')) == parseInt($end_time.replace(':', ''))) {
            disp_err('#end_time', '[start and end date-time are equal.]');
            $retval = false;
        } else if (parseInt($start_time.replace(':', '')) > parseInt($end_time.replace(':', ''))) {
            disp_err('#end_time', '[start date-time is greater than end date-time.]');
            $retval = false;
        } else {
            remove_err('#end_time');
        }
    } else {
        remove_err('#end_time');
    }
    
    if ($start_date.length != 0) {
        var cstart_date = convertDate($start_date);
        var cend_date = convertDate($end_date);
        $timeDiff = Math.abs(cend_date.getTime() - cstart_date.getTime());
        $diffDays = Math.ceil($timeDiff / (1000 * 3600 * 24));
        if(($diffDays > 0) && ($('.schlded_date').val() === undefined))
        {
                $(".marketing #dis-error").html('<span id="dis-error" class="error">You must create atleast one class Schedule.</span>');
                $(".marketing #dis-error").focus();
                $retval = false;
          
        }else {
        $("#dis-error").html('');
        }
    } 

    $total_seats = $.trim($('#total_seats').val());
    if ($total_seats.length == 0) {
        disp_err('#total_seats');
        $retval = false;
    } else if (valid_number($total_seats) == false) {
        disp_err('#total_seats', '[invalid]');
        $retval = false;
    } else if (parseInt($total_seats) == 0) {
        disp_err('#total_seats', '[cannot be zero]');
        $retval = false;
    } else {
        remove_err('#total_seats');
    }
    $cls_duration = $.trim($('#cls_duration').val());
    $lab_duration = $.trim($('#lab_duration').val());
    $assmnt_duration = $.trim($('#class_assmnt_duration').val());
    $cls_dur = ((isNaN($cls_duration)) == true || $cls_duration.length == 0) ? 0 : parseFloat($cls_duration);
    $lab_dur = ((isNaN($lab_duration)) == true || $lab_duration.length == 0) ? 0 : parseFloat($lab_duration);
    $ass_dur = ((isNaN($assmnt_duration)) == true || $assmnt_duration.length == 0) ? 0 : parseFloat($assmnt_duration);
    $total_dur = $cls_dur + +$lab_dur + +$ass_dur;

    if ($cls_duration.length == 0 && $lab_duration.length == 0 && $assmnt_duration.length == 0) {
        disp_err('#cls_duration', '[classroom, assmnt and lab duration hours cannot be left empty.]');
        $retval = false;
    } else if ($total_dur <= 0) {
        disp_err('#cls_duration', '[classroom, assmnt and lab duration hours cannot be zero.]');
        $retval = false;
    } else if ($cls_duration.length > 0 && isNaN($cls_duration) == true) {
        disp_err('#cls_duration', '[Invalid classroom duration hours.]');
        $retval = false;
    } else if ($lab_duration.length > 0 && isNaN($lab_duration) == true) {
        disp_err('#cls_duration', '[Invalid lab duration hours.]');
        $retval = false;
    } else if ($assmnt_duration.length > 0 && isNaN($assmnt_duration) == true) {
        disp_err('#cls_duration', '[Invalid assmnt. duration hours.]');
        $retval = false;
    } else if (parseFloat($course_duration) < parseFloat($total_dur)) {
        disp_err('#cls_duration', '[total course duration hours: ' + $course_duration + ']');
        $retval = false;
    } else {
        remove_err('#cls_duration');
    }

    $minimum_students = $.trim($('#minimum_students').val());
    if (parseInt($minimum_students) == 0) {
        disp_err('#minimum_students', '[Minimum Students should not be zero]');
        $retval = false;
    } else if ($minimum_students.length > 0 && parseInt($minimum_students) > parseInt($total_seats)) {
        disp_err('#minimum_students', '[Minimum Students <b>< =</b> Total Seats]');
        $retval = false;
    } else {
        remove_err('#minimum_students');
    }
    $crs_admin_email = $.trim($('#crs_admin_email').val());
    if(!valid_email_address($crs_admin_email)){
        disp_err('#crs_admin_email', '[Invalid Email Address]');
        $retval = false;
    }else {
        remove_err('#crs_admin_email');
    }
    $venue_floor=$('#venue_floor').val();
        if ($venue_floor == null || $venue_floor == '') {
            $("#venue_floor_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#venue_floor_err").text("").removeClass('error');
        }
        $venue_building=$('#venue_building').val();
        if ($venue_building == null || $venue_building == '') {
            $("#venue_building_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#venue_building_err").text("").removeClass('error');
        }
        $venue_unit=$('#venue_unit').val();
        if ($venue_unit == null || $venue_unit == '') {
            $("#venue_unit_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#venue_unit_err").text("").removeClass('error');
        }
        $venue_postalcode=$('#venue_postalcode').val();
        if ($venue_postalcode == null || $venue_postalcode == '') {
            $("#venue_postalcode_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#venue_postalcode_err").text("").removeClass('error');
        }
        
        $venue_room = $('#venue_room').val();
        if ($venue_room == null || $venue_room == '') {
            $("#venue_room_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#venue_room_err").text("").removeClass('error');
        }
        
        $venue_block = $('#venue_block').val();
        if ($venue_block == null || $venue_block == '') {
            $("#venue_block_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#venue_block_err").text("").removeClass('error');
        }
        
        $venue_street = $('#venue_street').val();
        if ($venue_street == null || $venue_street == '') {
            $("#venue_street_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#venue_street_err").text("").removeClass('error');
        }
    $fees = $.trim($('#fees').val());
    if ($fees.length == 0) {
        disp_err('#fees');
        $retval = false;
    } else if (isNaN($fees)) {
        disp_err('#fees', '[Invalid]');
        $retval = false;
    } else if (parseInt($fees) == 0) {
        disp_err('#fees', '[cannot be Zero]');
        $retval = false;
    } else {
        remove_err('#fees');
    }
    $class_discount = $.trim($('#class_discount').val());
    if ($class_discount.length > 0) {
        if (parseInt($class_discount) >= 100) {
            disp_err('#class_discount', '[should below 100]');
            $retval = false;
        } else if (isNaN($class_discount)) {
            disp_err('#class_discount', '[Invalid]');
            $retval = false;
        } else {
            remove_err('#class_discount');
        }
    }
    $languages = $('#languages').val();
    if ($languages.length == 0) {
        disp_err('#languages');
        $retval = false;
    } else {
        remove_err('#languages');
    }
    $modeoftraining = $('#modeoftraining').val();
    if ($modeoftraining.length == 0) {
        disp_err('#modeoftraining');
        $retval = false;
    } else {
        remove_err('#modeoftraining');
    }
    $survey_language= $('#survey_language').val();
    if ($survey_language.length == 0) {
        disp_err('#survey_language');
        $retval = false;
    } else {
        remove_err('#survey_language');
    }
    $cls_venue = $('#cls_venue').val();
    if ($cls_venue.length == 0) {
        disp_err('#cls_venue');
        $retval = false;
    } else if ($cls_venue == 'OTH' && $('#classroom_venue_oth').val().trim().length == 0) {
        disp_err('.classroom_venue_oth', '[Classroom venue others is required]');
        $retval = false;
    } else {
        remove_err('.classroom_venue_oth');
        remove_err('#cls_venue');
    }
    $lab_venue = $('#lab_venue').val();
    if ($lab_venue == 'OTH' && $('#lab_venue_oth').val().trim().length == 0) {
        disp_err('.lab_venue_oth', '[Lab venue others is required]');
        $retval = false;
    } else {
        remove_err('.lab_venue_oth');
    }
    $cls_trainer = '';
    $('input[name="control_5[]"]:checked').each(function() {
        $cls_trainer += $(this).val();
    })
    if ($cls_trainer.length == 0) {
        disp_err('#control_5');
        $retval = false;
    } else {
        remove_err('#control_5');
    }
    $schlded_date = $('.schlded_date').val();

    if($schlded_date === undefined && $js_tenant == 'T02'){
        $(".marketing #dis-error").html('<span id="dis-error" class="error">You must create atleast one class Schedule.</span>');
        $retval = false;
    } else {
         $(".marketing #dis-error").html('');
    }
    
    return $retval;
}

function disp_err($id, $text) {
    $text = typeof $text !== 'undefined' ? $text : '[required]';
    $($id).addClass('error');
    $($id + '_err').addClass('error').addClass('error3').html($text);
}

function remove_err($id) {
    $($id).removeClass('error');
    $($id + '_err').removeClass('error').removeClass('error3').text('');
}
function remove_all() {
    $('.error3').text('').removeClass('error3');
    $('#ex2 .error, #ex3 .error').removeClass('error');
}

function valid_number(duration) {
    return /^\d+$/.test(duration.replace(/[\s]/g, ''));
}
function reminder_popup_validate() {
    $ret_Val = true;
    $reminder1 = $.trim($('#reminder1').val());
    $reminder2 = $.trim($('#reminder2').val());
    $reminder3 = $.trim($('#reminder3').val());
    if ($reminder2.length > 0 || $reminder3.length > 0) {
        if (parseInt($reminder1) <= parseInt($reminder2) || $reminder1.length == 0) {
            disp_err('#reminder2', '[invalid]');
            $ret_Val = false;
        } else {
            remove_err('#reminder2');
        }
        if (parseInt($reminder2) <= parseInt($reminder3) || $reminder2.length == 0 || $reminder1.length == 0) {
            disp_err('#reminder3', '[invalid]');
            $ret_Val = false;
        } else {
            remove_err('#reminder3');
        }
    }
    if ($ret_Val == false && $('#ex4').css('display') == 'none') {
        $('.small_text1').trigger('click');
    }
    return $ret_Val;
}
function schld_form_clear() {
    remove_err('#schld_date');
    remove_err('#schld_session_type');
    remove_err('#schld_start_time');
    remove_err('#schld_end_time');
    $('#schld_date').val('');
    $('#schld_session_type').val('');
    $('#schld_start_time').val('');
    $('#schld_end_time').val('');
    $('.schld_save').removeAttr('data-edit');
}

function default_data_clear() {
    $('#def_date').val('');
    $('#def_schld_start_time').val('');
    $('#def_schld_end_time').val('');
    $('#control_9').children('span').text('Select Option');
    $('#control_9').next('.multiSelectOptions').children('label.selectAll').children('input').removeAttr('checked');
    $('input[name="control_9[]"]').removeAttr('checked');
    $('#def_schld_venue').val('');
    $('#def_venue_oth').val('');
    $('.defven_oth_span').hide();
}

function def_schld_attach() {
    $def_date = $('#def_date').val();
    $def_schld_start_time = $('#def_schld_start_time').val();
    $def_schld_end_time = $('#def_schld_end_time').val();
    $assessor = '';
    $assessortext = '';
    $('input[name="control_9[]"]:checked').each(function() {
        $assessor += $(this).val() + ',';
        $assessortext += $(this).parent().text() + ',';
    })
    $assessortext = $assessortext.replace(/,+$/, '');
    $def_schld_venue = $('#def_schld_venue').val();
    $def_schld_venue_oth = $('#def_venue_oth').val();
    $def_schld_venue_text = $('#def_schld_venue option[value="' + $def_schld_venue + '"]').text();
    if ($def_schld_venue_oth) {
        $def_schld_venue_text += "(" + $def_schld_venue_oth + ")";
    }
    $html = '<tr>\
                <input type="hidden" value="' + $def_date + '" name="def_schlded_date" class="def_schlded_date">\
                <input type="hidden" value="' + $def_schld_start_time + '" name="def_schlded_start_time" class="def_schlded_start_time">\
                <input type="hidden" value="' + $def_schld_end_time + '" name="def_schlded_end_time" class="def_schlded_end_time">\
                <input type="hidden" value="' + $assessor + '" name="def_schlded_assessor" class="def_schlded_assessor">\
                <input type="hidden" value="' + $def_schld_venue + '" name="def_schlded_venue" class="def_schlded_venue">\
               <input type="hidden" value="' + $def_schld_venue_oth + '" name="def_schlded_venue_oth" class="def_schlded_venue_oth">\
 			   <td class="a_button">\
                    <a class="small_text" rel="modal:open" href="#ex3">\
                    <input type="button" value="E" style="color:#000000; text-decoration:none;" />\
                    </a><br>\
                    <a href="#ex9" rel="modal:open" class="small_text delete_color def_schld_delete">\
                    <input type="button" value="D" style="color:#000000; text-decoration:none;" />\
                    </a>\
                </td>\
                <td>' + $def_date + '</td>\
                <td>All</td>\
                <td>' + $assessortext + '</td>\
                <td>' + $def_schld_start_time + ' - ' + $def_schld_end_time + '</td>\
                <td>' + $def_schld_venue_text + '</td>\
            </tr>';
    $('.def_schld_div .table-responsive table tbody').html($html);
}

$schld_count = 1;
function schld_attach() {
    $schld_date = $('#schld_date').val();
    $schld_session_type = $('#schld_session_type').val();
    $schld_session_type_text = $('#schld_session_type option[value="' + $schld_session_type + '"]').text();
    $schld_start_time = $('#schld_start_time').val();
    $schld_end_time = $('#schld_end_time').val();
    $class_chk = 'schlddate_' + $schld_date;
    if ($('tr').hasClass($class_chk)) {
        $date_inc = 'tr.' + $class_chk + ':first td:first';
        $rowspan = $($date_inc).attr('rowspan');
        $($date_inc).attr('rowspan', ++($rowspan));
        $date_td = '';
    } else {
        $date_td = '<td rowspan="1">' + $schld_date + '</td>';
    }
    $html = '<tr class="schld_tr' + $schld_count + ' schlddate_' + $schld_date + '" data-session="' + $schld_session_type + '" data-date="' + $schld_date + '" data-count="' + $schld_count + '">\
                <input type="hidden" value="' + $schld_date + '" name="schlded_date[]" class="schlded_date">\
                <input type="hidden" value="' + $schld_session_type + '" name="schlded_session_type[]" class="schlded_session_type">\
                <input type="hidden" value="' + $schld_start_time + '" name="schlded_start_time[]" class="schlded_start_time">\
                <input type="hidden" value="' + $schld_end_time + '" name="schlded_end_time[]" class="schlded_end_time">\
                ' + $date_td + '\
                <td width="20%">' + $schld_session_type_text + '</td>\
                <td>' + $schld_start_time + '</td>\
                <td>' + $schld_end_time + '</td>\
                <td class="a_button">\
                    <a href="#ex9" rel="modal:open" class="small_text delete_color schld_delete"><input type="button" value="Delete" style="color:#000000; text-decoration:none;" /></a>\
                </td>\
            </tr>';
    if ($('tr').hasClass($class_chk)) {
        $('tr.' + $class_chk + '[data-date="' + $schld_date + '"]').each(function() {
            $(this).children('td:last').html('');
        });
        $('tr.' + $class_chk + ':last').after($html);
    } else {
        $('.schld_div .table-responsive table tbody').append($html);
    }
    $('#schld_date').val('');
    $('#schld_session_type').val('');
    $('#schld_start_time').val('');
    $('#schld_end_time').val('');
    $schld_count++;
}
function schld_form_validate($retVal) {
    $schld_date = $('#schld_date').val();
    if ($schld_date.length == 0) {
        disp_err('#schld_date');
        $retVal = false;
    } else {
        remove_err('#schld_date');
    }
    $schld_session_type = $('#schld_session_type').val();
    if ($schld_session_type.length == 0) {
        disp_err('#schld_session_type');
        $retVal = false;
    } else {
        if ($('tr').hasClass('schlddate_' + $schld_date)) {
            if ($('tr.schlddate_' + $schld_date).is('[data-session="' + $schld_session_type + '"]')) {
                disp_err('#schld_session_type', '[Session exists]');
                $retVal = false;
            } else if ($('tr.schlddate_' + $schld_date).is('[data-session="S2"]') && $schld_session_type == 'BRK') {
                disp_err('#schld_session_type', '[Delete Session2]');
                $retVal = false;
            } else {
                remove_err('#schld_session_type');
            }
        } else if ($schld_session_type != 'S1') {
            disp_err('#schld_session_type', '[Add Session1]');
            $retVal = false;
        } else {
            remove_err('#schld_session_type');
        }
    }
    $schld_start_time = $.trim($('#schld_start_time').val());
    if ($schld_start_time.length == 0) {
        disp_err('#schld_start_time');
        $retVal = false;
    } else {
        if ($('tr').hasClass('schlddate_' + $schld_date)) {
            $max_rtime = $max_time = $('tr.schlddate_' + $schld_date + ':last td:last').prev('td').html();
            if (parseInt($max_time.replace(':', '')) > parseInt($schld_start_time.replace(':', ''))) {
                disp_err('#schld_start_time', '[Start from ' + $max_rtime + ']');
                $retVal = false;
            } else {
                remove_err('#schld_start_time');
            }
        } else {
            remove_err('#schld_start_time');
        }
    }
    $schld_end_time = $.trim($('#schld_end_time').val());
    if ($schld_end_time.length == 0) {
        disp_err('#schld_end_time');
        $retVal = false;
    } else if (parseInt($schld_start_time.replace(':', '')) >= parseInt($schld_end_time.replace(':', ''))) {
        disp_err('#schld_end_time', '[cannot be same or less than start time]');
        $retVal = false;
    } else {
        remove_err('#schld_end_time');
    }
    if ($retVal == true) {
        $('.schld_div .error').remove();
        schld_attach();
    }
    return $retVal;
}
function def_schld_form_validate($retVal) {
    $def_date = $('#def_date').val();
    if ($def_date.length == 0) {
        disp_err('#def_date');
        $retVal = false;
    } else {
        remove_err('#def_date');
    }
    $def_start_time = $('#def_schld_start_time').val();
    if ($def_start_time.length == 0) {
        disp_err('#def_schld_start_time');
        $retVal = false;
    } else {
        if ($('tr').hasClass('schlddate_' + $def_date)) {
            $max_rtime = $max_time = $('tr.schlddate_' + $def_date + ':last td:last').prev('td').html();
            if (parseInt($max_time.replace(':', '')) > parseInt($def_start_time.replace(':', ''))) {
                disp_err('#def_schld_start_time', '[Start from ' + $max_rtime + ']');
                $retVal = false;
            } else {
                remove_err('#def_schld_start_time');
            }
        } else {
            remove_err('#def_schld_start_time');
        }
    }
    $def_end_time = $('#def_schld_end_time').val();
    if ($def_end_time.length == 0) {
        disp_err('#def_schld_end_time');
        $retVal = false;
    } else if (parseInt($def_start_time.replace(':', '')) >= parseInt($def_end_time.replace(':', ''))) {
        disp_err('#def_schld_end_time', '[cannot be same or less than start time]');
        $retVal = false;
    } else {
        remove_err('#def_schld_end_time');
    }
    $assessor = '';
    $('input[name="control_9[]"]:checked').each(function() {
        $assessor += $(this).val();
    })
    if ($assessor.length == 0) {
        disp_err('#control_9');
        $retVal = false;
    } else {
        remove_err('#control_9');
    }
    $def_venue = $('#def_schld_venue').val();
    $def_venue_oth = $('#def_venue_oth').val();
    if ($def_venue.length == 0) {
        disp_err('#def_schld_venue');
        $retVal = false;
    } else if ($def_venue == 'OTH' && $def_venue_oth.trim().length == 0) {
        disp_err('#def_venue_oth');
        $retVal = false;
    } else {
        remove_err('#def_venue_oth');
        remove_err('#def_schld_venue');
    }
    if ($retVal == true) {
        $('.def_schld_div .error').remove();
        def_schld_attach();
    }
    return $retVal;
}
function tpStartSelect(time, endTimePickerInst) {
    $('#schld_end_time').timepicker('option', {
        minTime: {
            hour: endTimePickerInst.hours,
            minute: endTimePickerInst.minutes
        }
    });
}
function deftpStartSelect(time, endTimePickerInst) {
    $('#def_schld_end_time').timepicker('option', {
        minTime: {
            hour: endTimePickerInst.hours,
            minute: endTimePickerInst.minutes
        }
    });
}
function check_start_or_today() {
    $start_date = $('#start_date').val();
    $start_date_timestamp = parseDate($start_date);
    $current_date_timestamp = new Date();
     return $('#start_date').val();
     
    if ($start_date_timestamp > $current_date_timestamp) {
        return $('#start_date').val();
    }
    else {

        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; 
        var yyyy = today.getFullYear();
        $today_date_check = dd + '-' + mm + '-' + yyyy;
        return $today_date_check;
    }
}
function parseDate(s) {
    s = typeof s !== 'undefined' ? s : '';
    if (s.length > 0) {
        var b = s.split('-');
        return new Date(b[2], --b[1], b[0]);
    }
}
$(document).ready(function() {
    $(document).on('click', '*[href="#ex3"]', function() {
        scroll_to_top();
    });
    $(document).on('click', '*[href="#ex1"]', function() {
        scroll_to_top();
    });
    $('.ass_save').click(function() {
        return ass_form_validate(true);
    });
    $(document).on('click', '.ass_delete', function() {
        $val = $(this).children('input').data('del');
        $('.schld_alert_yes').attr('data-count', $val);
        $('.schld_alert_yes').attr('data-check', 'ass');
    });
    
    $(document).on('click', '.ass_edit', function() {
        $edit_id = $(this).children('input').data('edit');
        $div = $('.ass_tr' + $edit_id);
        $('#ass_date').val($div.children('.assmnt_date').val());
        $('#ass_start_time').val($div.children('.assmnt_start_time').val());
        $('#ass_end_time').val($div.children('.assmnt_end_time').val());
        $('input[name="control_2[]"]').removeAttr('disabled');
        $('#control_2').next('.multiSelectOptions label').removeAttr('style');
        $('#control_2').next('.multiSelectOptions').children('label').children('input').removeAttr('checked');
        $('.err_span').remove();
        $('input[name="checking_trainee[]"]').each(function(i) {
            $val = $(this).val();
            $('input[name="control_2[]"][value="' + $val + '"]').attr('disabled', 'disabled').removeAttr('checked');
            $('input[name="control_2[]"][value="' + $val + '"]').parent().css('display', 'none');
        });
        $('.ass_tr' + $edit_id + ' input[name="checking_trainee[]"]').each(function(i) {
            $val = $(this).val();
            $('input[name="control_2[]"][value="' + $val + '"]').removeAttr('disabled').attr('checked', 'checked');
            $('input[name="control_2[]"][value="' + $val + '"]').parent().removeAttr('style')
        })
        $('#ass_editid').val($edit_id);
        $('#control_8').next('.multiSelectOptions').children('label').children('input').removeAttr('checked');
        $assessor = $('.ass_tr' + $edit_id + ' .assmnt_assessor').val();
        $('.ass_tr' + $edit_id + ' input[name="checking_assessor[]"]').each(function(i) {
            $val = $(this).val();
            $('input[name="control_8[]"][value="' + $val + '"]').attr('checked', 'checked');
        })
        $('input[name="control_8[]"]').attr('disabled', 'disabled');
        $('#control_8').next('.multiSelectOptions').children('label').css('display', 'none');
        $('#control_8').next('.multiSelectOptions').children('.selectAll').removeAttr('style');
        $('input[name="control_7[]"]:checked').each(function(i) {
            $val = $(this).val();
            $('input[name="control_8[]"][value="' + $val + '"]').removeAttr('disabled');
            $('input[name="control_8[]"][value="' + $val + '"]').parent().removeAttr('style');
        });
        $('input[name="control_8[]"]:disabled').each(function(i) {
            $(this).removeAttr('checked');
        });
        $length = $('input[name="control_8[]"]').not(':disabled').length;
        if (parseInt($length) == 0) {
            $('#ex231').modal();
            return false;
        }
        $('#ass_venue').val($div.children('.ass_venue').val());
        if ($div.children('.ass_venue').val() == 'OTH') {
            $('.assven_oth_span').show();
            $('#ass_venue_oth').val($div.children('.ass_venue_oth').val());
        }
    });
})
function scroll_to_top() {
    $('html,body').animate({scrollTop: $('body').offset().top}, "fast");
}
function asstpStartSelect(time, endTimePickerInst) {
    $('#ass_end_time').timepicker('option', {
        minTime: {
            hour: endTimePickerInst.hours,
            minute: endTimePickerInst.minutes
        }
    });
}
function ass_form_validate($retVal) {

    $ass_date = $('#ass_date').val();
    if ($ass_date.length == 0) {
        disp_err('#ass_date');
        $retVal = false;
    } else {
        remove_err('#ass_date');
    }
    $ass_start_time = $.trim($('#ass_start_time').val());
    if ($ass_start_time.length == 0) {
        disp_err('#ass_start_time');
        $retVal = false;
    } else {
        remove_err('#ass_start_time');
    }
    $ass_end_time = $.trim($('#ass_end_time').val());
    if ($ass_end_time.length == 0) {
        disp_err('#ass_end_time');
        $retVal = false;
    } else if (parseInt($ass_start_time.replace(':', '')) >= parseInt($ass_end_time.replace(':', ''))) {
        disp_err('#ass_end_time', '[cannot be same or less than start time]');
        $retVal = false;
    } else {
        remove_err('#ass_end_time');
    }
    $assessor = '';
    $('input[name="control_8[]"]:checked').each(function() {
        $assessor += $(this).val();
    })
    if ($assessor.length == 0) {
        disp_err('#control_8');
        $retVal = false;
    } else {
        remove_err('#control_8');
    }
    
    $ass_venue = $('#ass_venue').val();
	$ass_venue_oth = $('#ass_venue_oth').val();
    if ($ass_venue.length == 0) {
        disp_err('#ass_venue');
        $retVal = false;
    } else if ($ass_venue == 'OTH' && $ass_venue_oth.trim().length == 0) {
        disp_err('#ass_venue_oth');
        $retVal = false;
    } else {
        remove_err('#ass_venue_oth');
        remove_err('#ass_venue');
    }
    if ($retVal == true) {
        ass_attach();
    }
    return $retVal;
}
$cnt = 100;
function ass_attach() {
    if ($('#ass_editid').val().length > 0) {
        $count = $('#ass_editid').val();
    } else {
        $count = $cnt;
    }
    $ass_date = $('#ass_date').val();
    $ass_start_time = $('#ass_start_time').val();
    $ass_end_time = $('#ass_end_time').val();
    $assessor = '';
    $assessortext = '';
    $assessor_hidden = '';
    $('input[name="control_8[]"]:checked').each(function() {
        $assessor += $(this).val() + ',';
        $assessor_hidden += '<input type="hidden" value="' + $(this).val() + '" name="checking_assessor[]">';
        $assessortext += $(this).parent().text() + ',';
    })
    
    $assessortext = $assessortext.replace(/,+$/, '');
   
    $ass_venue = $('#ass_venue').val();
    $ass_venue_text = $('#ass_venue option[value="' + $ass_venue + '"]').text();
    $ass_venue_oth = $('#ass_venue_oth').val();
    if ($ass_venue == 'OTH') {
        $ass_venue_text += "(" + $ass_venue_oth + ")";
    }
    $html1 = '<tr class="ass_tr' + $count + '">';
    $html2 = $assessor_hidden + '\
                <input type="hidden" value="' + $ass_date + '" name="assmnt_date[]" class="assmnt_date">\
                <input type="hidden" value="' + $ass_start_time + '" name="assmnt_start_time[]" class="assmnt_start_time">\
                <input type="hidden" value="' + $ass_end_time + '" name="assmnt_end_time[]" class="assmnt_end_time">\
                <input type="hidden" value="' + $assessor + '" name="assmnt_assessor[]" class="assmnt_assessor">\
                <input type="hidden" value="' + $ass_venue + '" name="ass_venue[]" class="ass_venue">\
                <input type="hidden" value="' + $ass_venue_oth + '" name="ass_venue_oth[]" class="ass_venue_oth">\
                 <td class="a_button">\
                    <a href="#ex2" rel="modal:open" class="small_text ass_edit">\
                        <input type="button" data-edit="' + $count + '" value="E" style="color:#000000; text-decoration:none;" />\
                    </a><br>\
                    <a href="#ex9" rel="modal:open" class="small_text delete_color ass_delete">\
                        <input type="button" data-del="' + $count + '" value="D" style="color:#000000; text-decoration:none;" />\
                    </a>\
                </td>\
                <td>' + $ass_date + '</td>\
                <td>' + $assessortext + '</td>\
                <td>' + $ass_start_time + ' - ' + $ass_end_time + '</td>\
                <td>' + $ass_venue_text + '</td>';
    $html3 = '</tr>';
   
    $cnt++;
    $('.def_schld_div .table-responsive table tbody').append($html1 + $html2 + $html3);
    
    assmnt_data_clear();
}
function assmnt_data_clear() {
    $('#ass_date').val('');
    $('#ass_start_time').val('');
    $('#ass_end_time').val('');
    $('#control_8').children('span').text('Select Option');
    $('#control_8').next('.multiSelectOptions').children('label.selectAll').children('input').removeAttr('checked');
    $('input[name="control_8[]"]').removeAttr('checked');
    $('input[name="control_2[]"]').removeAttr('checked');
    $('#control_2').children('span').text('Select Option');
    $('#control_2').next('.multiSelectOptions').children('label.selectAll').children('input').removeAttr('checked');
    $('#ass_venue').val('');
    $('.assven_oth_span').hide();
    $('#ass_venue_oth').val('');
}


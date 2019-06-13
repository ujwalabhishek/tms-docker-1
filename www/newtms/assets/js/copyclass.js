/* 
 * This js file included in copyclass page
 */
$(document).ready(function() {
    $("#course_name").autocomplete({
        source: function(request, response) {
            $('#course_name').val($('#course_name').val().toUpperCase())
            $('#course_id').val('');
            $('#class_id').attr('disabled', 'disabled');
            $('#class_id').html('<option value="">Select</option>');
            $.ajax({
                url: $siteurl + "classes/get_courses_json",
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
            var id = ui.item.id;
            get_class_id(id);
            if (id.length != 0) {
                $('#class_id').removeAttr('disabled');
            } else {
                $('#class_id').attr('disabled', 'disabled');
            }
            if ($search_check == 1) {
                search_validate();
            }
        },
         minLength: 4
    });
    $('#copy_form').submit(function() {
        //////////////////////////////////added by shubhranshu to prevent multi click////////////////////////////////////////////////
        check = 1;
        if(copy_validate(true)){
            $('.popup_cance89').html('<button class="btn btn-primary" type="submit">Update</button>');
            var self = $(this),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            return true;
        }else{
            return false;
        }   
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    });
    $('#copy_form input, #copy_form select').change(function() {
        return copy_validate(false);
    });
    $search_check = 0;
    $('#search_form').submit(function() {
        $search_check = 1;
         //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM////////////////////////////////////
        if(search_validate()){
            var self = $(this),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            return true;
        }else{
           return false; 
        }
       //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM///////////////////////////////
       
    })
    $('#search_form select').change(function() {
        if ($search_check == 1) {
            return search_validate();
        }
    })
    $("#start_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '-100:+0',
        yearRange: "-100:+100",
                onSelect: function(selectedStartDate) {
                    $("#end_date").datepicker("option", {
                        minDate: selectedStartDate,
                        maxDate: '',
                    });
                },
        onClose: function() {
            $(this).trigger("change");
        }
    });
    $('#start_time').timepicker({
        showLeadingZero: false,
    });
    $('#end_time').timepicker({
        showLeadingZero: false,
    });
    $("#end_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '-100:+0',
        minDate: 0,
        maxDate: -1,
        yearRange: "-50:+100",
                onClose: function() {
                    $(this).trigger("change");
                }
    });
    $('.deactivate_cancel').click(function(){
       $('#class_name').val('');
       remove_err('#class_name');
       $('#start_date').val('');
       remove_err('#start_date');
       $('#start_time').val('');
       remove_err('#start_time');
       $('#end_date').val('');
       remove_err('#end_date');
       $('#end_time').val('');
       remove_err('#end_time');
       $('#copy_reason').val('');
       remove_err('#copy_reason');
       $('#other_reason').val('');
       remove_err('#other_reason');
       $('#other_reason').hide();
    });
    $('#copy_reason').change(function() {
        if ($(this).val() == 'OTHERS') {
            $('#other_reason').fadeIn();
        } else {
            $('#other_reason').fadeOut();
            $('#other_reason').val('');
        }
    });
    $('#class_name').keyup(function() {
        $(this).val(this.value.toUpperCase());
    });
});
function copy_validate($retVal) {
    $class = $('#class_name').val().trim();
    if ($class.length > 0) {
        remove_err('#class_name');
        $.ajax({
            type: 'post',
            url: $siteurl + '/classes/check_classname_unique',
            data: {class_name:$class},
            async: false,
            success: function(res) {
                if(res == 1){
                    disp_err('#class_name','[Class name exists!]');
                    $retVal = false;
                }
            }
        });
    }
    $start_date = $('#start_date').val();
    if ($start_date.length == 0) {
        disp_err('#start_date');
        $retVal = false;
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
        $retVal = false;
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
    $reason = $('#copy_reason').val();
    $other_reason = $.trim($('#other_reason').val());
    if ($reason.length == 0) {
        disp_err('#copy_reason');
        $retVal = false;
    } else if ($reason == 'OTHERS' && $other_reason.length == 0) {
        disp_err('#copy_reason', '[Other Reason is required]');
        $retVal = false;
    } else {
        remove_err('#copy_reason');
    }
    return $retVal;
}
function search_validate() {
    $retVal = true;
    $course_name = $.trim($('#course_name').val());
    $course_id = $.trim($('#course_id').val());
    if ($course_name.length == 0) {
        disp_err('#course_name');
        $retVal = false;
    } else if ($course_id.length == 0) {
        disp_err('#course_name', 'Pick Course from Autocomplete to get Classes');
        $retVal = false;
    } else {
        remove_err('#course_name');
    }
    $class_id = $('#class_id').val();
    if ($class_id.length == 0) {
        disp_err('#class_id');
        $retVal = false;
    } else {
        remove_err('#class_id');
    }
    return $retVal;
}
function disp_err($id, $text) {
    $text = typeof $text !== 'undefined' ? $text : '[required]';
    $($id).addClass('error');
    $($id + '_err').addClass('error').text($text);
}
function remove_err($id) {
    $($id).removeClass('error');
    $($id + '_err').removeClass('error').text('');
}
function get_class_id(id) {
    $('#course_id').val(id);
    $class = $('#class_id');
    $.ajax({
        type: 'post',
        url: $siteurl + 'classes/get_course_copy_classes_json',
        data: {course_id: id},
        dataType: "json",
        beforeSend: function() {
            $class.html('<option value="">Select</option>');
        },
        success: function(res) {
            $.each(res, function(i, item) {
                $class.append('<option value="' + item.key + '">' + item.value + '</option>');
            });
        }
    });
}
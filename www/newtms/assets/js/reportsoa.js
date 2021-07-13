/**
 * This js file includes in soa reports.
 */
$check_search = 0;
$(document).ready(function() {
    $("#start_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
        maxDate: -1,
        onSelect: function(selectedStartDate) {
            $("#end_date").datepicker("option", {
                minDate: selectedStartDate,
                maxDate: -1,
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
        yearRange: "-50:+50",
        minDate: 0,
        maxDate: -1,
        onClose: function() {
            $(this).trigger("change");
        }
    });
    $('#generateby').change(function() {
        $val = $(this).val();
        $('.error1').empty();
        remove_all();
        $('.generate1').hide();
        $('.generate2').hide();
        $('.search_but').hide();
        if ($val == 1) {
            $('#courseId').val('');
            $('#classId').html('<option value="">Select</option>').attr('disabled', 'disabled');
            $('.generate1').show();
            $('.search_but').show();
        } else if ($val == 2) {
            reset_calendar();
            $('.generate2').show();
            $('.search_but').show();
        }
    });
    ///////below code added by shubhranshu to prevent disable of button
    $('#classId').change(function() {
         $('.print_soa_report').html("Print");
         $('.print_soa_report').prop("disabled", false);
         $('.submit_but').prop("disabled", false);
         $('.submit_but').html("<span class='glyphicon glyphicon-search'></span>Export");
    });
    ////////code end//////////////////////////
    $('#courseId').change(function() {
        $('#classId').attr('disabled', 'disabled');
        $('#status').val('');
        $('#trainee').val('');
        $('#trainee_id').val('');
        $('.print_soa_report').prop("disabled", false);//added by shubhranshu to prevent disable of button
        $('.print_soa_report').html("Print");//added by shubhranshu to prevent disable of button
         $('.submit_but').prop("disabled", false);///added by shubhranshu to prevent disable of button
         $('.submit_but').html("<span class='glyphicon glyphicon-search'></span>Export");///added by shubhranshu to prevent disable of button
        $courseId = $('#courseId').val();
        if ($courseId.length > 0) {
            $.ajax({
                url: $baseurl + 'reports/get_completed_classes_for_course',
                data: {'courseId': $courseId},
                type: 'get',
                dataType: 'json',
                beforeSend: function(i) {
                    $('#classId').html('<option value="">Select</option>');
                },
                success: function(res) {
                    var data = res.data;
                    if (data != '') {
                        $.each(data, function(i, item) {
                            $('#classId').append('<option value="' + item.class_id + '">' + item.class_name + '</option>')
                        });
                        remove_err('#classId');
                        $('.search_but').show();
                        $('#classId').removeAttr('disabled');
                    } else {
                        disp_err('#classId', '[There are no completed classes.]');
                        $('.search_but').hide();
                    }
                }
            });
        }
    });

    function reset_calendar() {
        $("#start_date").datepicker("option", {
            minDate: '',
            maxDate: -1,
        });
        $("#end_date").datepicker("option", {
            minDate: 0,
            maxDate: -1,
        });
        $('#start_date').val('');
        $('#end_date').val('');
    }
    $('#soa_report_form input, #soa_report_form select[name != "generateby"][name != "courseId"]').change(function() {
        if ($check_search == 1) {
            return form_validation(false);
        }
    });
    $('#soa_report_form').submit(function() {
        $check_search = 1;
        return form_validation(true); // added by shubhranshu for multiple request

    });
    $('.print_soa_report').click(function() {
        $val = $('.select_soa_print:checked');
        if ($val.length == 0) {
            disp_err('#xls');
        } else {
            remove_err('#xls');
            var self = $('.popup_cancel001'),
            button = self.find('input[type="button"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            $('#soa_report_form')[0].submit();
        }
    });
    // below code added by shubhranshu for dynamic button search request
    $('.select_soa_print').click(function(){
        $('.print_soa_report').prop("disabled", false);
         $('.print_soa_report').html("Print");
         $('.submit_but').prop("disabled", false);
         $('.submit_but').html("<span class='glyphicon glyphicon-search'></span>Export");
    });// below code added by shubhranshu for dynamic button search request
});
function form_validation($retval) {
    $course = $('#courseId').val();
    $class = $('#classId').val();
    $start_date = $('#start_date').val();
    $end_date = $('#end_date').val();
    $generatedby = $('#generateby').val();
    if ($generatedby == 1) {
        if ($course.length == 0 || $course == 0) {
            disp_err('#courseId');
            $retval = false;
        } else {
            remove_err('#courseId');
        }
        if ($class.length == 0 || $class == 0) {
            disp_err('#classId');
            $retval = false;
        } else {
            remove_err('#classId');
        }
    } else if ($generatedby == 2) {
        if ($start_date.length == 0 || $start_date == 0) {
            disp_err('#start_date');
            $retval = false;
        } else {
            remove_err('#start_date');
        }
    }
    if ($retval == true) {
        $.ajax({
            type: 'post',
            url: $siteurl + 'reports/soa_report_json',
            data: {
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                generateby: $('#generateby').val(),
                courseId: $('#courseId').val(),
                classId: $('#classId').val(),
            },
            async: false,
            beforeSend: function() {

            },
            success: function(res) {
                if (res == 0) {
                    $retval = false;
                    $('#ex11').modal();
                }
            }
        });
    }
    if ($retval == true) {
        ///////added by shubhranshu to prevent multiple clicks////////////////
        var self = $('.search_but'),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            ///////added by shubhranshu to prevent multiple clicks////////////////
        $('#ex13').modal();
        return false;
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
    $('.error').removeClass('error');
}
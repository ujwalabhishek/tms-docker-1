/**
 * This js file includes in reports Certificates page
 */
$(document).ready(function() {
   
    $("#start_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
        onSelect: function(selectedStartDate) {
            $("#end_date").datepicker("option", {
                minDate: selectedStartDate,
                maxDate: $max_date,
            });
        },
    });
    $("#end_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
        minDate: 0,
        maxDate: -1
    });
    if ($('#start_date').val().length > 0) {
        $("#end_date").datepicker("option", {
            minDate: $('#start_date').val(),
            maxDate: '',
        });
    }
    $('#courseId').change(function() {
        $('#classId').attr('disabled', 'disabled');
        $('#status').val('');
        $('#trainee').val('');
        $('#trainee_id').val('');
        $courseId = $('#courseId').val();
        $max_date = '';
        reset_calendar();
        $.ajax({
            url: $baseurl + 'reports/get_classes_for_certificate_course',
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
                    $('#classId').removeAttr('disabled');
                }
            }
        });
    });
    $('#classId').change(function() {
        $max_date = '';
        reset_calendar();
        $class_id = $(this).val();
        if ($class_id.length > 0) {
            $.ajax({
                url: $baseurl + 'reports/get_class_details',
                data: {'classId': $class_id},
                type: 'get',
                dataType: 'json',
                success: function(res) {
                    $max_date = res.end_date;
                    $("#start_date").datepicker("option", {
                        minDate: res.start_date,
                        maxDate: res.end_date,
                    });
                    $("#end_date").datepicker("option", {
                        minDate: res.start_date,
                        maxDate: res.end_date,
                    });
                }
            })
        }
        $('#status').val('');
        $('#trainee').val('');
        $('#trainee_id').val('');
    });

    $('.search_select').change(function() {
        $val = $('.search_select:checked').val();
        $('#status').val('');
	$('#invoice').val('');
        $('#trainee').val('');
        $('#trainee_id').val('');
	$('#invoice_id').val('');
        $('#status').attr('disabled', 'disabled');
	$('#invoice').attr('disabled', 'disabled');
        $('#trainee').attr('disabled', 'disabled');
        if ($val == 1) {
            $div = $('#trainee');
        } else if ($val == 2) {
            $div = $('#invoice');
	   $div1 = $('#status');
           $div1.removeAttr('disabled');
        }
        $div.removeAttr('disabled');
    });


    $("#trainee").autocomplete({
        source: function(request, response) {
             $('#trainee_id').val('');
            if (request.term.trim().length > 0) {
                $course_id = $('#courseId').val();
                $class_id = $('#classId').val();
                $.ajax({
                    url: $siteurl + "reports/get_all_trainee",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term,
                        class_id: $class_id,
                        course_id: $course_id
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
        },
        minLength: 4
    });

    if ($('.search_select:checked').val() == 1) {
        $div = $('#invoice');
         $div = $('#status');
    } else {
        $div = $('#trainee');
    }
    $div.attr('disabled', 'disabled');
    // skm start
    $("#invoice").autocomplete({
        source: function(request, response) {
             $('#invoice_id').val('');
            if (request.term.trim().length > 0) {
               // $invoice_id = $('#invoice_id').val();
                
                //$class_id = $('#classId').val();
                $.ajax({
                    url: $siteurl + "reports/get_all_invoices",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term,
                        
                    },
                    success: function(data) {
                        response(data);
                       //alert("data");
                    }
                });
            } else {
                var d;
                response(d);
            }
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#invoice_id').val(id);
        },
        minLength: 4
    });
    // skm end
    
    function reset_calendar() {
        $("#start_date").datepicker("option", {
            minDate: '',
            maxDate: '',
        });
        $("#end_date").datepicker("option", {
            minDate: 0,
            maxDate: -1,
        });
        $('#start_date').val('');
        $('#end_date').val('');
    }
    if ($get_enddate.length > 0 && $get_startdate.length > 0) {
        $("#start_date").datepicker("option", {
            minDate: $get_startdate,
            maxDate: $get_enddate,
        });
        if ($check_startdate.length > 0) {
            $get_startdate = $check_startdate;
        }
        $("#end_date").datepicker("option", {
            minDate: $get_startdate,
            maxDate: $get_enddate,
        });
    }
    var search_check = 0;
    $('#wda_report_form').submit(function() {
        search_check = 1;
        ///////added by shubhranshu to prevent multiple clicks////////////////
        if(validate(true)){
           var self = $(this),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            return true;
        }else{
            return false;
        }
        ///////added by shubhranshu to prevent multiple clicks////////////////

    });
    
    // added by shubhranshu

        $( "#displayText" ).click(function() {
          $( "#alertmsg" ).show();
        });
         $( "#displayText1" ).click(function() {
          $( "#alertmsg" ).show();
        });
    
    $('#wda_report_form input').change(function() {
        if (search_check == 1) {
            return validate(false);
        }
    });
    function validate(retval) {
        var trainee = $('#trainee').val().trim();
        var trainee_id = $('#trainee_id').val();
        var courseid = $('#courseId').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        if (trainee.length > 0 && trainee_id.length == 0) {
            disp_err('#trainee', 'Select from auto-help');
            retval = false;
        } else {
            remove_err('#trainee');
        }
        
        ///////added by shubhranshu to vaildate search operation////////////////
        if(trainee == '' && courseid == '' && (start_date == '' || end_date == '')){
                
                $('#search_error').addClass('error').text('Oops!Please select atleast one filter to perform search operation');
                retval = false;
            }else{
                $('#search_error').removeClass('error').text('');
        }///////added by shubhranshu to vaildate search operation////////////////
        check_remove_id();///////added by shubhranshu//
        return retval;
    }
    /////////////added by shubhranshu///////////////////////
    function check_remove_id(){
        
        $trainee = $('#trainee').val();
        if($trainee == ''){
           $('#trainee_id').val(''); 
        }
        
    }/////////////////////////////////////////////////////////////////////////////////////
    
    function disp_err($id, $text) {
        $text = typeof $text !== 'undefined' ? $text : '[required]';
        $($id).addClass('error');
        $($id + '_err').addClass('error').addClass('error3').html($text);
    }
    function remove_err($id) {
        $($id).removeClass('error');
        $($id + '_err').removeClass('error').removeClass('error3').text('');
    }
});
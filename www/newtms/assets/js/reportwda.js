/**
 * This js file includes in WDA reports page
 */

$(document).ready(function() {
    $( "#displayText" ).click(function() {
          $( "#alertmsg" ).show();
        });
         $( "#displayText1" ).click(function() {
          $( "#alertmsg" ).show();
        });
    $("#start_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
        onSelect: function(selectedStartDate) {
            $("#end_date").datepicker("option", {
                minDate: selectedStartDate,
                maxDate: '',
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
        $('#taxcode').val('');
        $('#taxcode_id').val('');
        $('#trainee').val('');
        $('#trainee_id').val('');
        $courseId = $('#courseId').val();
        $.ajax({
            url: $baseurl + 'reports/get_classes_for_course',
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
        $('#taxcode').val('');
        $('#taxcode_id').val('');
        $('#trainee').val('');
        $('#trainee_id').val('');
    });
    $('.search_select').change(function() {
        $val = $('.search_select:checked').val();
        $('#taxcode').val('');
        $('#taxcode_id').val('');
        $('#trainee').val('');
        $('#trainee_id').val('');
        $('#taxcode').attr('disabled', 'disabled');
        $('#trainee').attr('disabled', 'disabled');
        if ($val == 1) {
            $div = $('#trainee');
        } else if ($val == 2) {
            $div = $('#taxcode');
        }
        $div.removeAttr('disabled');
    });

    $("#taxcode").autocomplete({
        source: function(request, response) {
            $('#taxcode').val($('#taxcode').val().toUpperCase())
            $('#taxcode_id').val('');
            if ($('#taxcode').val().length > 0) {
                $course_id = $('#courseId').val();
                $class_id = $('#classId').val();
                $.ajax({
                    url: $siteurl + "reports/get_all_taxcode",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term,
                        course_id: $course_id,
                        class_id: $class_id
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            }
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#taxcode_id').val(id);
        }
    });

    $("#trainee").autocomplete({
        source: function(request, response) {
            $('#trainee').val($('#trainee').val().toUpperCase())
            $('#trainee_id').val('');
            if ($('#trainee').val().length > 0) {
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
            }
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#trainee_id').val(id);
        }
    });
    if ($('.search_select:checked').val() == 1) {
        $div = $('#taxcode');
    } else {
        $div = $('#trainee');
    }
    $div.attr('disabled', 'disabled');
    

    
    ///////added by shubhranshu to prevent multiple clicks////////////////  ///////////////////////////////////////////////////////////////////
    $('#wda_report_form').submit(function() {
        search_check = 1;

        if(validate(true)){
           var self = $(this),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            return true;
        }else{
            return false;
        }
    });
    
    function validate(retval) {
        var courseId = $('#courseId').val().trim();
        var trainee = $('#trainee').val().trim();
        var taxcode = $('#taxcode').val().trim();
        var start_date = $('#start_date').val().trim();
        var end_date = $('#end_date').val().trim();
        if(courseId == '' && trainee == '' && taxcode == ''&& (start_date == '' || end_date == '')){
                $('#search_error').addClass('error').text('Oops!..Please select atleast one filter to perform search operation');
                retval = false;
            }else{
                $('#search_error').removeClass('error').text('');
        }
        check_remove_id();///////added by shubhranshu
        return retval;
    }
    /////////////added by shubhranshu///////////////////////
    function check_remove_id(){
        
        $taxcode = $('#taxcode').val();
        $trainee_name = $('#trainee').val();
        
        if($taxcode == ''){
           $('#taxcode_id').val(''); 
        }
        if($trainee_name == ''){
           $('#trainee_id').val(''); 
        }
    }/////////////////////////////////////////////////////////////////////////////////////
    
    ///////added by shubhranshu to vaildate search operation///////////////////////////////////////////////////////////
});
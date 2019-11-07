/**
 * This js file includes in reports Certificates page
 */

$(document).ready(function() {
    $("#start_date").datepicker({
        dateFormat: 'dd/mm/yy',
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
  
// shubhranshu

        $( "#displayText" ).click(function() {
          $( "#alertmsg" ).show();
        });
         $( "#displayText1" ).click(function() {
          $( "#alertmsg" ).show();
        });

      $('#trainee').attr('disabled', 'disabled');
    $('.search_select').change(function() {
        $('#taxcode').val('');
        $('#taxcode_id').val('');
        $('#trainee').val('');
        $('#trainee_id').val('');
        $('#taxcode').attr('disabled', 'disabled');
        $('#trainee').attr('disabled', 'disabled');
        $val = $('.search_select:checked').val();
        if ($val == 1) {
            $div = $('#taxcode');
        } else {
            $div = $('#trainee');
        }
        $div.removeAttr('disabled');
    });


        for(i=1;i<=count;i++)
        {

            $('#cert_col_'+i).datepicker({
                
                dateFormat: 'dd-mm-yy',
                changeMonth : true,
                changeYear : true,
                yearRange :"-50:+50"
            });
        }
       
     for(i=1;i<=count;i++)
     {
         (function(i){
            $('#cert_col_'+i).change(function()
            {

                   // $cert_col=$(this).val();
                    $cert_col=$('#cert_col_'+ i).val();
                    $course_id=$('#course_'+i).val();
                    $class_id=$('#class_'+i).val();
                    $user_id=$('#user_'+i).val();
                    //alert("hi"+$cert_col1);
                  $.ajax({
                       url: $baseurl + 'reports/update_coll_on',
                       data:{'cert_col': $cert_col,'course_id':$course_id,'class_id':$class_id,'user_id':$user_id},
                       type:'POST',
                      // url: site_url('reports/update_coll_on'),
                       success:function(result)
                       {
                           $('#cert_col_'+i).html(result);
                       }
                   });
               });
           })(i);
       }
        
        //end
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
    $('#courseId').change(function() 
    {
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

    
    // trainee autocomplate 
    $("#trainee").autocomplete({
        source: function(request, response) {
             $('#trainee_id').val('');
            if (request.term.trim().length > 0) {
                $course_id = $('#courseId').val();
                $class_id = $('#classId').val();
                $comp_id = $('#company_id').val();
                $.ajax({
                    url: $siteurl + "reports/get_all_trainee",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term,
                        class_id: $class_id,
                        course_id: $course_id,
                        comp_id:$comp_id
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
        minLength: 0
    });
     // trainee autocomplate 
    //added by pritam
    
     $("#taxcode").autocomplete({
        source: function(request, response) {
            $('#taxcode_id').val('');
           // disable_course_class();
            if (request.term.trim().length > 0) 
           {
                $course_id = $('#courseId').val();
                $class_id = $('#classId').val();
                $comp_id = $('#company_id').val();
                $.ajax({
                    url: $siteurl + "reports/get_all_taxcode",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term,
                        class_id: $class_id,
                        course_id: $course_id,
                        comp_id: $comp_id
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
           // $('#course').removeAttr('disabled');
        },
        minLength:0
    });
     $("#company_name").autocomplete({
        source: function(request, response) {
            $('#company_id').val('');
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
            $('#company_id').val(id);
        },
        minLength:0
    });
    $('#taxcode').attr('disabled', 'disabled');
    $('#trainee').attr('disabled', 'disabled');
    $val = $('.search_select:checked').val();
    if ($val == 1) {
        $div = $('#taxcode');
    } else {
        $div = $('#trainee');
    }
    $div.removeAttr('disabled');
    
    
    // ends
      
//    $val = $('.search_select:checked').val();
//    if ($val == 1) 
//    {
//        $div = $('#taxcode');
//    } 
//    else if ($val == 2) 
//    {
//            $div = $('#trainee');
//    }
//    $div.attr('disabled', 'disabled');

   
    
    
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
    $('#wda_report_form input').change(function() {
        if (search_check == 1) {
            return validate(false);
        }
    });
    function validate(retval) {
        var trainee = $('#trainee').val().trim();
        var taxcode = $('#taxcode').val().trim();
        var courseid = $('#courseId').val().trim();
        var trainee_id = $('#trainee_id').val();
        var company = $('#company_name').val();
        if (trainee.length > 0 && trainee_id.length == 0) {
            disp_err('#trainee', 'Select from auto-help');
            retval = false;
        } else {
            remove_err('#trainee');
        }
        ///////added by shubhranshu to vaildate search operation////////////////
        if(trainee == '' && courseid == '' && taxcode == '' && company == ''){
                $('#company_name_err').addClass('error').text('Oops!..Please select atleast one filter to perform search operation');
                retval = false;
            }else{
                $('#company_name_err').removeClass('error').text('');
        }///////added by shubhranshu to vaildate search operation////////////////
        check_remove_id();/////////added by shubhranshu//
        return retval;
    }
    /////////////added by shubhranshu///////////////////////
    function check_remove_id(){
        
        $taxcode = $('#taxcode').val();
        $trainee= $('#trainee').val();
        $company= $('#company_name').val();
        
        if($taxcode == ''){
           $('#taxcode_id').val(''); 
        }
        if($trainee == ''){
           $('#trainee_id').val(''); 
        }
        if($company == ''){
           $('#company_id').val(''); 
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
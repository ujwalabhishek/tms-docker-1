/**
 * This js file includes in  trainee_feedback report page
 */
function reset_form() {
    $('#collected_on').val('');
    $('#new_entrance').val('');
    $('#COMMNTS').text(''); 
    $('#trainer_feedback_form').find('input:radio').prop('checked', false);
    //$('#trainer_feedback_form #COMYTCOM_C').attr('checked', 'checked');
    $("#satisfaction_rating option:selected").removeAttr("selected");
}
function reset_trainee_form() {    
    $('#feedbackForm option').attr('selected', false);
    $('#remarks').val('');
}
$(document).ready(function() {
    $('.view_feedback').click(function(event) {
        event.preventDefault();
        var user_id = $(this).attr("user_id");
        $trainee_name = $('.trainee_name_' + user_id).text();
        $.get("view_trainee_feedback", {
            user_id: user_id,
            class_id: class_id,
            course_id: course_id,
            trainee_name: $trainee_name
        }, function(html) {
            $(html).appendTo('body').modal();
        });
    });
    $('.export_feedback').click(function() {
        $url = $(this).attr('href');
        $result = '';
        $.ajax({
            type: 'get',
            url: $url + '&json=1',
            data: {},
            async: false,
            success: function(res) {
                $result = res;
                if (res == 'false') {
                    $('#ex231').modal();
                    return false;
                }
            }
        });
        if($result == 'false'){
            return false;
        }
    });
    $('.view_trainer_feedback').click(function(event) {
        event.preventDefault();
        var user_id = $(this).attr("user_id");
        $trainee_name = $('.trainee_name_' + user_id).text();
        $.get("view_trainer_feedback", {
            user_id: user_id,
            class_id: class_id,
            course_id: course_id,
            trainee_name: $trainee_name
        }, function(html) {
            $(html).appendTo('body').modal();
        });
    });
    $('#select_course_id').change(function() {
        var courseId = $('#select_course_id').val();
        var classSelect = $('#select_class_id');
        $.ajax({
            type: 'get',
            url: SITE_URL + 'reports/get_classes_for_certificate_course',
            data: {courseId: courseId},
            dataType: "json",
            beforeSend: function() {
                classSelect.html('<option value="">Select</option>');
            },
            success: function(res) {
                if (res.data != '') {
                    classSelect.removeAttr('disabled');
                } else {
                    classSelect.html('<option value="">Select</option>');
                    classSelect.attr('disabled', 'disabled');
                }
                $.each(res.data, function(i, item) {
                    classSelect.append('<option value="' + item.class_id + '">' + item.class_name + '</option>');
                });
            }
        });
    });
   $('.training_update').click(function() {
        var $course = $(this).data('course');
        var $class =  $(this).data('class');
        var $user =   $(this).data('user');
        var $payment= $(this).data('payment');
        
        /* loader img skm start*/
        var ajax_image = "<img src='"+baseurl+"assets/images/q.gif' alt='Loading...' />";
        $('#tbl').hide();
        $('#trainee_fdbk').hide();
        $('#skm').show();
        $('#ssp').show();
        $('#skm').html(ajax_image);
        $('#ssp').html(ajax_image);
        /* end*/
        
        reset_form();
        $('#traineefeedbackForm').attr("action", baseurl + "class_trainee/trainee_feedback/" + $user + "/" + $course + "/" + $class);
        $('#trainer_feedback_form').attr("action", baseurl + "class_trainee/trainer_feedback/" + $user + "/" + $course + "/" + $class);
        $.ajax({
            
            type: 'post',
            url: baseurl + 'trainee/get_trainer_feedback',
            dataType: 'json',
            data: {course: $course, class: $class, user: $user,payment:$payment},
            
            success: function(res) {  
               
                var trainer = res.trainer;
                var trainee = res.trainee;
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
                        $('#skm').hide();
                         $('#tbl').show();
                          $('#ssp').hide();
                        $('#trainee_fdbk').show();
                    }
                    if(item.training_score == 'ABS')
                    {
                      
                        $('#COMYTCOM_C').prop('disabled', true);
                        $('#COMYTCOM_ABS').prop('disabled', false);     
                        $('#COMYTCOM_EX').prop('disabled', true);
                        $('#COMYTCOM_NYC').prop('disabled', true);
                        $('#COMYTCOM_2NYC').prop('disabled', true);
                        $('#skm').hide();
                         $('#tbl').show();
                          $('#ssp').hide();
                        $('#trainee_fdbk').show();
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
                          $('#ssp').hide();
                        $('#trainee_fdbk').show();
                    }
                    if (item.training_score == null){
                        
                        $('#COMYTCOM_C').prop('disabled', false);
                        $('#COMYTCOM_ABS').prop('disabled', false);     
                        $('#COMYTCOM_EX').prop('disabled', false);
                        $('#COMYTCOM_NYC').prop('disabled', false);
                        $('#COMYTCOM_2NYC').prop('disabled', false);
                        $('#skm').hide();
                         $('#tbl').show();
                          $('#ssp').hide();
                        $('#trainee_fdbk').show();
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
                         $('#skm').hide();
                         $('#tbl').show();
                          $('#ssp').hide();
                        $('#trainee_fdbk').show();
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
                $('#rating').val('');
                $.each(trainee, function(i, item) {
                    //$('#rating option[value="' + item.trainee_feedback_rating + '"]').attr("selected", "selected"); 
                    $('#rating').val(item.trainee_feedback_rating);
                    $('#'+item.feedback_question_id+' option[value="' + item.feedback_answer + '"]').attr("selected", "selected");
                    $('#remarks').text(item.other_remarks_trainee);
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
    
    $('.trainee_update').click(function() {        
        var $user = $(this).data('user');
        reset_trainee_form();
        $('#trainee_id').val($user);        
    }); 
    $("#collected_on").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",        
    });
    $("#new_entrance").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "+0:+100",
        minDate: 0
    });
});
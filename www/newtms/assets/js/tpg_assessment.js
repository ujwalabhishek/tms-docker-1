
$(document).ready(function () {

    $('#course').change(function () {
        $class = $('#class');
        $.ajax({
            type: 'post',
            url: $baseurl + 'classes/get_course_classes_json',
            data: {course_id: $('#course').val()},
            dataType: "json",
            beforeSend: function () {
                $class.html('<option value="">Select</option>');
            },
            success: function (res) {
                if (res != '') {
                    $class.html('<option value="">All</option>');
                    $class.removeAttr('disabled');
                } else {
                    $class.html('<option value="">Select</option>');
                    $class.attr('disabled', 'disabled');
                }
                $.each(res, function (i, item) {
                    $class.append('<option value="' + item.key + '">' + item.value + '</option>');
                });
            }
        });
    });
    
    $('#class').change(function () {
        $nric= $('#nric');
        $.ajax({
            type: 'post',
            url: $baseurl + 'classes/get_Trainee_For_Assessments_json',
            data: {course_id: $('#course').val(),class_id: $('#class').val()},
            dataType: "json",
            beforeSend: function () {
                $nric.html('<option value="">Select</option>');
            },
            success: function (res) {
                if (res != '') {
                    $nric.html('<option value="">All</option>');
                    $nric.removeAttr('disabled');
                } else {
                    $nric.html('<option value="">Select</option>');
                    $nric.attr('disabled', 'disabled');
                }
                $.each(res, function (i, item) {
                    $nric.append('<option value="' + item.key + '">' + item.value + '</option>');
                });
            }
        });
    });
    
    //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM////////////////////////////////////
    $('#search_form').on('submit',function() {
        form_check = 1;
        //alert("form click");
        var status=form_validate(true);//alert(status);
        if(status){
        var self = $(this),
        button = self.find('input[type="submit"],button'),
        submitValue = button.data('submit-value');
        button.attr('disabled','disabled').html('Please Wait..');
        $nric_id= ($('#nric>option:selected').text() == 'All') ? '' : $('#nric>option:selected').text();
        $('#nric_id').val($nric_id);
        return true;
       }else{
           return false;
       }
    }); //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM///////////////
    function form_validate($retval) {
        var crse = $('#course').val();
        var clas = $('#class').val();
        if (crse ==null || crse == '') {
           
            disp_err('#course', '[Required]');
            $retval = false;
        } else {
            remove_err('#course');
       
        }
        
        if (clas == null || clas == 0) {
            disp_err('#class', '[Required]');
            $retval = false;
        } else {
             remove_err('#class');
            
        }
        
        return $retval;
    }
    

    $('[href="#view_assessment"]').click(function(){

        $.ajax({
            type: 'post',
            url: $baseurl + 'tp_gateway/view_assessment',
            data: {refNo: $(this).attr('data-refNo') },
            dataType: "json",
            beforeSend: function () {
                
            },
            success: function (res) {
                json_data = $.parseJSON(res);
                if (json_data != '') {
                    $('#ass_ref_no').html(res.data.referenceNumber);
                    $('#tp_uen').html(res.data.trainingPartner.uen);
                    $('#tp_name').html(res.data.trainingPartner.name);
                    $('#crs_ref_no').html(res.data.course.referenceNumber);
                    $('#crs_name').html(res.data.course.title);
                    $('#crs_run_id').html(res.data.course.run.id);
                    $('#crs_run_start_date').html(res.data.course.run.startDate);
                    $('#crs_run_end_date').html(res.data.course.run.endDate);
                    $('#trainee_id_type').html(res.data.trainee.idType.type);
                    $('#trainee_id').html(res.data.trainee.id);
                    $('#fullname').html(res.data.trainee.fullName);
                    $('#result').html(res.data.result);
                    $('#score').html(res.data.score);
                    $('#grade').html(res.data.grade);
                    $('#ass_date').html(res.data.assessmentDate);
                    $('#skill_code').html(res.data.skillCode);
                    //$('#enrol_no').html(res.data.enrolment.referenceNumber);
                    $('#created_on').html(res.meta.createdOn);
                    $('#updated_on').html(res.meta.updatedOn);
                    
                    //if(json_data.status == 200){
                        
                   // }else{
                       // $('#viewsection').hide();
                    //}
                  // $.each(json_data.data.sessions, function(i, item) {
                   //    $('#ssg_sess').append('<tr><td>'+(i+1)+'</td><td>'+item.id+'</td><td>'+item.startDate+'</td><td>'+item.endDate+'</td><td>'+item.startTime+'</td><td>'+item.endTime+'</td><td>'+item.attendanceTaken+'</td></tr>');
                   // });
                }else{
                     $('#ass_ref_no').html('');
                    $('#tp_uen').html('');
                    $('#tp_name').html('');
                    $('#crs_ref_no').html('');
                    $('#crs_name').html('');
                    $('#crs_run_id').html('');
                    $('#crs_run_start_date').html('');
                    $('#crs_run_end_date').html('');
                    $('#trainee_id_type').html('');
                    $('#trainee_id').html('');
                    $('#fullname').html('');
                    $('#result').html('');
                    $('#score').html('');
                    $('#grade').html('');
                    $('#ass_date').html('');
                    $('#skill_code').html('');
                    //$('#enrol_no').html(res.data.enrolment.referenceNumber);
                    $('#created_on').html('');
                    $('#updated_on').html('');
                }
            }
        });
    });
    
    
    $('[href="#update_void_assessment"]').click(function(){
        $('#btnarea').show();
        $('#status_msg').html('');
        $.ajax({
            type: 'post',
            url: $baseurl + 'tp_gateway/view_assessment',
            data: {refNo: $(this).attr('data-refno')},
            dataType: "json",
            beforeSend: function () {
                
            },
            success: function (res) {
                json_data = $.parseJSON(res);
                if (json_data != '' && res.status == 200) {
                    $('#fullname1').html(res.data.trainee.fullName);
                    $('#result1').html(res.data.result);
                    if(res.data.tms_result == "C"){
                        $resu = 'Pass';
                    }else if(res.data.tms_result == "EX"){
                        $resu = 'Exempt';
                    }else{
                         $resu = 'Fail';
                    }
                    $('#tms_result').html($resu);
                    $('#score1').html(res.data.score);
                    $('#grade1').html(res.data.grade);
                    $('#tms_score').html(res.data.tms_score);
                    $('#tms_grade').html(res.data.tms_grade);
                    $('#ass_date1').val(res.data.assessmentDate);
                    $('#skill_code1').html(res.data.skillCode);
                    $('#tms_skill_code').html(res.data.tms_skill_code);
                    $('#tms_fullname').html(res.data.trainee.tms_fullname);
                    $('#assmt_ref_no1').html(res.data.referenceNumber);//$('#update_assessment').data('refNo');
                    //if(json_data.status == 200){
                        
                   // }else{
                       // $('#viewsection').hide();
                    //}
                  // $.each(json_data.data.sessions, function(i, item) {
                   //    $('#ssg_sess').append('<tr><td>'+(i+1)+'</td><td>'+item.id+'</td><td>'+item.startDate+'</td><td>'+item.endDate+'</td><td>'+item.startTime+'</td><td>'+item.endTime+'</td><td>'+item.attendanceTaken+'</td></tr>');
                   // });
                }
            }
        });
    });
    
    
    $('#updateAseessment').click(function () {
       
       $fullname1=$('#tms_fullname').html();
       $result1=$('#tms_result').html();
       $score1=$('#tms_score').html();
       $grade1=$('#tms_grade').html();
       $ass_date1=$('#ass_date1').val();
       $skill_code1=$('#tms_skill_code').html();
       $action=$('#action').val();
       $assmt_ref_no1=$('#assmt_ref_no1').html();
       $courseid=$('#update_assessment').data('courseid');
       $userid=$('#update_assessment').data('userid');
       $classid=$('#update_assessment').data('classid');
        $.ajax({
            type: 'post',
            url: $baseurl + 'tp_gateway/update_assessment',
            data: {course_id:$courseid,class_id:$classid,user_id:$userid,fullname: $fullname1,result:$result1,score:$score1,grade:$grade1,assessment_date:$ass_date1,skillcode:$skill_code1,action:$action,assmt_ref_no:$assmt_ref_no1},
            dataType: "json",
            beforeSend: function () {
                
            },
            success: function (res) {
                json_data = $.parseJSON(res);
                if (json_data != '' && res.status == 200) {
                   $('#status_msg').html("<div class='alert alert-success text-center'>Assessment Record Updated Successfully With Reference ID: "+res.data.assessment.referenceNumber+"</div>");
                    $('#btnarea').hide();
                }else{
                    $('#status_msg').html("<div class='alert alert-warning text-center'>"+res.error.message+"</div>");
                    $.each(res.error.details, function(i, msg) {
                        $('#status_msg').append("<div class='alert alert-danger text-center'>"+msg.field+" : "+msg.message+"</div>");
                    });
                }
            }
        });
   });
   
    
    
});





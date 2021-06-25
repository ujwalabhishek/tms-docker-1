
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
        return true;
       }else{
           return false;
       }
    }); //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM///////////////
    function form_validate($retval) {
        var crse = $('#course').val();
        var clas = $('#class').val();
        if (crse.length > 0 && crse.length == 0) {
            $('#course').css('border', 'solid 1px red');
            $retval = false;
        } else {
            $('#course').css('border', 'solid 1px #bcbcbc');
        }
        
        if (clas.length > 0 && clas.length == 0) {
            $('#class').css('border', 'solid 1px red');
            $retval = false;
        } else {
            $('#class').css('border', 'solid 1px #bcbcbc');
        }
        
        return $retval;
    }
    
    $('#click_assessment').click(function () {
   
        $.ajax({
            type: 'post',
            url: $baseurl + 'tp_gateway/view_assessment',
            data: {referenceNo: 'ASM-2103-000037'},//$('#click_assessment').data('refNo') },
            dataType: "json",
            beforeSend: function () {
                
            },
            success: function (res) {
                json_data = $.parseJSON(res);
                if (json_data != '' && res.status == 200) {
                    $('#ass_ref_no').html(res.data.referenceNumber);
                    $('#tp_uen').html(res.data.trainingPartner.uen);
                    $('#tp_name').html(res.data.trainingPartner.name);
                    $('#crs_ref_no').html(res.data.course.referenceNumber);
                    $('#crs_ref_no').html(res.data.course.title);
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
                    $('#enrol_no').html(res.data.enrolment.referenceNumber);
                    $('#created_on').html(res.data.meta.createdOn);
                    $('#updated_on').html(res.data.meta.updatedOn);
                    
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
});



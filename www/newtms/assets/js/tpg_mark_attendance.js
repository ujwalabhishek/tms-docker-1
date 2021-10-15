
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
            url: $baseurl + 'class_trainee/get_enrolled_trainee',
            data: {course_id: $('#course').val(),class_id: $('#class').val()},
            dataType: "json",
            beforeSend: function () {
                $nric.html('<option value="">Select</option>');
            },
            success: function (res) {
                if (res != '') {
                    $nric.html('<option value="">All</option>');
                    $nric.removeAttr('disabled');
                    $('#nric_count').val(res.nric_count);
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
        var nric = $('#nric').val();
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
        
         if (nric == null || nric == 0) {
            disp_err('#nric', '[Required]');
            $retval = false;
        } else {
             remove_err('#nric');
            
        }
        
        return $retval;
    }
    
    $('[href="#view_session_attn"]').click(function(){

        $.ajax({
            type: 'post',
            url: $baseurl + 'tp_gateway/retrieve_course_sess_att',
            data: {referenceNo: $(this).attr('data-refno') },
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
    

});





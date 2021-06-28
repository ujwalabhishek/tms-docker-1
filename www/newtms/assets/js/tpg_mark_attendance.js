
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

});





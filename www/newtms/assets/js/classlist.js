/*  
 * This js file included in classlist page
 */
$(document).ready(function() {
    $('#course_id').change(function() {
        $class = $('#class_id');
        $.ajax({
            type: 'post',
            url: $siteurl + '/classes/get_course_copy_classes_json',
            data: {course_id: $('#course_id').val()},
            dataType: "json",
            beforeSend: function() {
                $class.html('<option value="">Select</option>');
            },
            success: function(res) {
                if (res != '') {
                    $class.html('<option value="">All</option>');
                    $class.removeAttr('disabled');
                } else {
                    $class.html('<option value="">Select</option>');
                    $class.attr('disabled', 'disabled');
                }
                $.each(res, function(i, item) {
                    $class.append('<option value="' + item.key + '">' + item.value + '</option>');
                });
            }
        });
    });
    var check = 0;
    $('#search_form').submit(function() {
        check = 1;     
        //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM////////////////////////////////////
        if(search_form_validate(true)){
            var self = $(this),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            return true;
        }else{
           return false; 
        }
       //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM///////////////////////////////

            
    });
    $('#course_id').change(function() {        
        if (check == 1) {            
            return search_form_validate(false);
        }
    });
});
function search_form_validate(ret_val) {    
    var course_id = $("#course_id").val();    
    if(course_id == "") {
        $("#course_id_err").text("[required]").addClass('error');
        $("#course_id").addClass('error');
        ret_val = false;
    } else {
        $("#course_id_err").text("").removeClass('error');
        $("#course_id").removeClass('error');
    }
    return ret_val;
}
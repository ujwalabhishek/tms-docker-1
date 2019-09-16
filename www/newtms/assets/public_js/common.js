$(document).ready(function() {
    var host = "<?php print base_url(); ?>"
    jQuery(".inputsearchbox_course").autocomplete({
        source: function(request, response) {
            jQuery.get(baseurl + "index.php/course_public/get_course_list_autocomplete", {
                query: request.term,
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
            });
        },
        minLength: 1
    });
    $("#NRIC_OTHER option[value='NOTAXCODE']").remove();

});
function validate_search() {
    var course_values = document.getElementById("course_name").value;
    if (course_values == "") {
        $("#crse_id").show();
        $("#crse_id").text("[required]").addClass("error");
        document.getElementById("course_name").focus();
        return false;
    } else {
        return true;
    }
}
$(function() {
    $("#datepicker").datepicker({minDate: 0, dateFormat: 'yy-mm-dd',
        onSelect: function(date) {
            $.ajax({
                url: baseurl + "course_public/check_course_class_schedule",
                type: "post",
                data: 'date='+date,
                success: function(res) {
                        if(res == 1) {
                        window.location.href = baseurl+'course_public/classes_list_by_date/' + date;
                    }else {                        
                       $('#alert').modal();
                        return false;
                    }
                },
                error:function() {
                    return false;
                }
            });          
        }
    });
});

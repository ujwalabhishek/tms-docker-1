$(document).ready(function() {

 $('#submit_btn').attr('disabled', 'disabled');
//$('#skm_btn').hide();
 
    if($( "#select_course_id option:selected" ).val() == 0){
        $('#select_class_id').attr('disabled', 'disabled');
    }
    
      $('#course_id').change(function() {
        $('#cls_id').attr('disabled', 'disabled');
        $cls_id = $('#cls_id');
        $.ajax({
            type: 'post',
            url: $baseurl + 'reports_finance/get_course_class_name_json',
            data: {course_id: $('#course_id').val()},
            dataType: "json",
            beforeSend: function() {
                $cls_id.html('<option value="">All</option>');
            },
            success: function(res) {
                if (res != '') {
                    $cls_id.html('<option value="">All</option>');
//                    $cls_name.removeAttr('disabled');
                } else {
                    $cls_id.html('<option value="">All</option>');
//                    $cls_name.attr('disabled', 'disabled');
                }
                $('#cls_id').removeAttr('disabled');
                $.each(res, function(i, item) {
                    $cls_id.append('<option value="' + item.key + '">' + item.value + '</option>');
                });
                 
            }
        });
    });
    
//    $course_id = $('#course_id').val();
//    $class_id = $('#cls_id').val();
//    alert($class_id+"asd");

  $('#cls_id').change(function() {
//$('#submit_btn').show();
 $('#submit_btn').removeAttr('disabled');
  });

      
//    $('#submit_btn').submit(function(){
//        
//        alert("skm inter");
//        var courceVal = $("#course_id").val();
//        var classVal = $("#cls_id").val();
//        if (courceVal > 0 && classVal > 0) {
//            $("#submit_btn").html('');
//            $('#search_form').submit();
//        } else {
//            alert("skm");
//            $("#submit_btn").html('Please select course and class.');
//            return false;
//        }
//        return false;
//    });
    
});

///////added by shubhranshu to prevent multiple clicks////////////////
function disable_button(){
   var self = $('#search_form'),
    button = self.find('input[type="submit"],button');
    button.attr('disabled','disabled').html('Please Wait..');
    return true;
}///////added by shubhranshu to prevent multiple clicks////////////////
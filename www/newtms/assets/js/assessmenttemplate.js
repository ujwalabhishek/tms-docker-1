/* 
 * This js file is used by Assessment Template Use Case
 */
$(document).ready(function() {
    //alert('hi');
     $('#assmnt_course_name').val('');
     $('#template_name').val('');
     $('#assmnt_upload').val('');
     remove_err('#assmnt_course_name');
     remove_err('#template_name');
     remove_err('#assmnt_upload');
});
    function validate_assessment_template(){  
      //  alert('hi 1');
        var retVal=true;
        var template_name = $("#template_name").val(); 
        var course_name = $("#assmnt_course_name").val();
        $assmnt_upload = $('#assmnt_upload').val();
    
        if(course_name == "") {
            $("#assmnt_course_name_err").text("[required]").addClass('error');
            $("#assmnt_course_name").addClass('error');
            retVal = false;
        }else {
            remove_err('#assmnt_course_name');
        }
        if(template_name == "") {
            $("#template_name_err").text("[required]").addClass('error');
            $("#template_name").addClass('error');
            retVal = false;
        }else {
            remove_err('#template_name');
        }
        var ext = $('#assmnt_upload').val().split('.').pop().toLowerCase();
       // alert('hi '+ext);
        if ($assmnt_upload.length == 0) {
            $("#assmnt_upload_err").text("[required]").addClass('error');
            $("#assmnt_upload").addClass('error');
            retVal = false;
        } else if ($.inArray(ext, ['pdf']) == -1) {
            $("#assmnt_upload_err").text("[please upload only PDF]").addClass('error');
            $("#assmnt_upload").addClass('error');
            retVal = false;
        }else {
            remove_err('#assmnt_upload');
        }
        if($('span').hasClass('error')){            
            retVal = false;
        }
        return retVal;        
    }
    function remove_err($id) {
        $($id).removeClass('error');
        $($id + '_err').removeClass('error').text('');
    }
    
    function validate_assessment_template_update() {
        
        var retVal=true;
        var template_name = $("#up_template_name").val();         
        $assmnt_upload = $('#up_assmnt_upload').val();
        
        if(template_name == "") {
            
            $("#up_template_name_err").text("[required]").addClass('error');
            $("#up_template_name").addClass('error');
            retVal = false;
        }
        else {
            remove_err('#up_template_name');
        }                
      
        if ($assmnt_upload.length != 0) {
            
            var ext = $('#up_assmnt_upload').val().split('.').pop().toLowerCase();
            
            if ($.inArray(ext, ['pdf']) == -1) {
            
                $("#up_assmnt_upload_err").text("[please upload only PDF]").addClass('error');
                $("#up_assmnt_upload").addClass('error');
                retVal = false;
            }
            else {
                remove_err('#up_assmnt_upload');
            }
        }
        else {
            remove_err('#up_assmnt_upload');
        } 
        
        return retVal;
    }
    
    function launch_assmt_deactivate(template_id, template_name) {           
            
        $("#de_template_id").text(template_id);
        $("#de_template_name").text(template_name);
        
        $("#sel_template_id").val(template_id)
        
        $("#deactivate_form").modal('show');
    }
    
    function launch_assmt_update(template_id, template_name) {           
            
        var courseName = $("#course_name option:selected").text();
        
        $("#up_template_id").text(template_id);
        $("#up_course_name").text(courseName);
        
        $("#up_template_name").val(template_name);
        
        $("#sel_up_template_id").val(template_id)
        
        $("#updateassmnttemplatePopup").modal('show');
    }
    
    function launch_assmt_new(sel_id) {
        
        $("#assmnt_course_name").val(sel_id);
        $("#addnewassmnttemplatePopup").modal('show');
    }
    
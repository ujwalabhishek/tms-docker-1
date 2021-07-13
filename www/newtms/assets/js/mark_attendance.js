/* 
 * This js file includes in mark_attendance page
 */
$( document ).ready(function() {

    if($( "#select_course_id option:selected" ).val() == 0){
        $('#select_class_id').attr('disabled', 'disabled');
    }

    var postData = [];
    var form = $('#update_form');
    $('.scroll table input[type=checkbox]').each(function(key, item){
        $(item).click(function (){
            var isChecked = $(item).is(':checked');
            var paramName = $(this).attr('name');

            var newVal = isChecked ? '1' : '0';
            var hidden = $(form).find("[name='" + paramName + "']");
            if (hidden.length != 0){
                hidden.val(newVal);
            } else {
                $(form).append('<input type="hidden" name="' + paramName + '" value="' + newVal + '"/>');
            }
        });
    });
    /////added by shubhranshu to mark all the attendance
    $('#checkall1').click(function(){
     if($('#checkall1').is(":checked")){
            $('input[type=checkbox]').each(function() { this.checked = true; 
            //this.attr('checked','checked');
        }); 
        }else{
             $('input[type=checkbox]').each(function() { this.checked = false; 
             //this.attr('checked','');
         }); 
        }
       
    });
    $('#checkall').click(function(){
     if($('#checkall1').is(":checked")){
            $('input[type=checkbox]').each(function() { this.checked = true; 
            //this.attr('checked','checked');
        }); 
        }else{
             $('input[type=checkbox]').each(function() { this.checked = false; 
             //this.attr('checked','');
         }); 
        }
       
    });
     /////added by shubhranshu to mark all the attendance
    
    $('#prev_week_but').click(function(){
        $('input[name=week]').val("1");
        $('#search_form').submit();
    });

    $('#next_week_but').click(function(){
        $('input[name=week]').val("2");
        $('#search_form').submit();
    });

    $('#export_to_pdf_but').click(function(){
        $('input[name=export]').val("pdf");
        var form = $('#search_form');
        form.submit();
        $('input[name=export]').val("");
    });

    $('#export_to_pdf_week_but').click(function(){
    });
    $('#export_to_pdf_week_xls').click(function(){
        $('input[name=export]').val("xls_week");
        var form = $('#search_form');
        form.submit();
        $('input[name=export]').val("");
    });

    $('#export_to_xls_but').click(function(){
        $('input[name=export]').val("xls");
        var form = $('#search_form');
        form.submit();
        $('input[name=export]').val("");
    });
    $('#lock_attendance').click(function(){
        $('input[name=export1]').val("lock"); 
        var form = $('#search_form');
        form.submit();
        $('input[name=export1]').val('');
    });
     $('#unlock_attendance').click(function(){
        $('input[name=export1]').val("unlock"); 
        var form = $('#search_form');
        form.submit();
        $('input[name=export1]').val('');
    });


    $("#reset_button").click(function(){
        var form = $('#search_form');
        form.submit();
    });

    $(".sort_header").click(function(){
        var sort = $(this).attr('sort');
        var action = SITE_URL + '/class_trainee/mark_attendance?b=' + sort + '&o=' + ancher;
        var form = $('#search_form');
        form.attr("action", action);
    });

    $('#select_course_id').change(function() {
        var courseId = $('#select_course_id').val();
        var classSelect = $('#select_class_id');
        $.ajax({
            type: 'post',
            url: SITE_URL + 'classes/get_course_classes_json',
            data: {course_id: courseId, mark_attendance:"mark_attendance"},
            dataType: "json",
            beforeSend: function() {
                classSelect.html('<option value="">Please Select</option>');
            },
            success: function(res) {
                if (res != '') {
                    classSelect.removeAttr('disabled');
                } else {
                    classSelect.html('<option value="">Please Select</option>');
                    classSelect.attr('disabled', 'disabled');
                }
                $.each(res, function(i, item) {
                    classSelect.append('<option value="' + item.key + '">' + item.value + '</option>');
                });
            }
        });
    });
   /*.. $('#lock_attendance').click( function(){
        var courseId = $('#select_course_id').val();
        var classId  = $('#select_class_id').val();
        $.ajax({
                    type: 'post',
                    url: SITE_URL + 'class_trainee/lock_class_attendance',
                    data : {course_id: courseId , class_id: classId},
                    success : function(res){
    
                        if(res==1){
                              $('#lock_attendance').hide();
                             $('#unlock_attendance').show();
                            ('#lock_message').show();
                        }else
                        {
                           
                           
                            ('#lock_message').hide('');
                        }
                    }
                });
    });
    $('#unlock_attendance').click( function()
    {
        var courseId = $('#select_course_id').val();
        var classId  = $('#select_class_id').val();
        $.ajax({
                    type: 'post',
                    url: SITE_URL + 'class_trainee/unlock_class_attendance',
                    data : {course_id: courseId , class_id: classId},
                    success : function(res){
                       
                        if(res == 1){
                           
                            $('#lock_attendance').show();
                            $('#unlock_attendance').hide();
                            ('#lock_message').html('<span></span>Class atendance is successfully locked');
                        }
                        else{
                           
                           
                            ('#lock_message').html('<span></span>Sorry! seems some probelm with attendance locking');
                        }
                    }
                });
    });
    ..*/
    $('#select_class_id').change(function() {        
        $('#week_start').val($.datepicker.formatDate("dd/mm/yy", new Date()));        
    });

    $('#update_button').click(function(){
        var form = $('#update_form');
        form.find("[name='course_id']").val($('#select_course_id').val());
        form.find("[name='class_id']").val($('#select_class_id').val());
        form.find("[name='subsidy']").val($('#subsidy').val());
//        $(".overlay").show();
//        $("input[name*=mark_attendance][type=checkbox]").each(function () {
//                $(this).trigger('click').change();});
         /////added by shubhranshu to prevent multiple clicks/////////////////////////
        var self = $('#update_form'),
        button = self.find('input[type="submit"],button');
        button.attr('disabled','disabled').html('Please Wait..');
         /////added by shubhranshu to prevent multiple clicks/////////////////////////
        form[0].submit();
    });
    
    /* for acctivity log of mark attendance skm start */
    $(document).ready(function(){
        $('.act').click(function(){
            var courseId = $('#select_course_id').val();
            var classId  = $('#select_class_id').val();
            $.ajax({
                        type: 'post',
                        url: SITE_URL + 'class_trainee/mark_att_log',
                        data : {course_id: courseId , class_id: classId},
                        success : function(res){

                        }
                    });          
        });
    });
    /* skm end */

    $('#markAttendanceSubmit').click(function(){
        var courceVal = $("#select_course_id").val();
        var classVal = $("#select_class_id").val();
        if (courceVal > 0 && classVal > 0) {
            $("#markAttendanceSubmitErrorForm").html('');
            /////added by shubhranshu to prevent multiple clicks/////////////////////////
            var self = $('#search_form'),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
             /////added by shubhranshu to prevent multiple clicks/////////////////////////
            $('#search_form').submit();
        } else {
            $("#markAttendanceSubmitErrorForm").html('Please select course and class.');
            return false;
        }
        return false;
    });

});
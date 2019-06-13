/**
 * This js file includes in bulk enrollment page
 */
$(document).ready(function() {
    var check = 0;
    $('#bulkenrollment').submit(function() {
        check = 1;
        var status=form_validate(true);//alert(status);
        if(status){
            var self = $(this),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait While Uploading..');
            return true;
       }else{
           return false;
       }
    });
    $('#bulkenrollment input, #bulkenrollment select').change(function() {
        if (check == 1) {
            return form_validate(false);
        }
    });
    $('#course').change(function() {
        $course = $('#course').val();
        $taxcode_id = '';
        $trainee_id = '';
        if ($course.length == 0) {
            $('#class').html('<option value="">Select</option>');
            return false;
        }
        $.ajax({
            url: $baseurl + 'class_trainee/get_trainee_classes',
            type: 'post',
            dataType: 'json',
            data: {'course': $course, 'trainee_id': $trainee_id, 'taxcode_id': $taxcode_id},
            beforeSend: function() {
                $('#class').html('<option value="">Select</option>');
            },
            success: function(i) {
                if (i != '') {
                    $.each(i, function(e, item) {
                        $('#class').append('<option value="' + item.class_id + '">' + item.class_name + '</option>');
                    });
                }
            }
        });
    });

    $('#class').change(function() {
        $class = $('#class').val();
        $course = $('#course').val();
        $.ajax({
            url: $baseurl + 'class_trainee/get_class_salesexec',
            type: 'post',
            dataType: 'json',
            data: {'class': $class, course: $course},
            beforeSend: function() {
                $select = '<option value="">Select</option>';
                if ($salesexec_check == 1) {
                    $select = '';
                }
                $('#salesexec').html($select);
            },
            success: function(i) {
                if (i != '') {
                    $.each(i, function(e, item) {
                        if($login_user_id == item.user_id ) {
                            $('#salesexec').append('<option value="' + item.user_id + '" selected>' + item.first_name + '</option>');
                        } else {
                            $('#salesexec').append('<option value="' + item.user_id + '">' + item.first_name + '</option>');
                        }    
                    });
                }
            }
        });
    });

    function form_validate($retval) {
        $company = $('#company').val();
        if ($company.length == 0) {
            disp_err('#company');
            $retval = false;
        } else {
            remove_err('#company');
        }
        $course = $('#course').val();
        if ($course.length == 0) {
            disp_err('#course');
            $retval = false;
        } else {
            remove_err('#course');
        }
        $class = $('#class').val();
        if ($class.length == 0) {
            disp_err('#class');
            $retval = false;
        } else {
            remove_err('#class');
        }
        $upload = $('#upload').val();
        var ext = $('#upload').val().split('.').pop().toLowerCase();
        if ($upload.length == 0) {
            disp_err('#upload');
            $retval = false;
        } else if ($.inArray(ext, ['xls', 'xlsx']) == -1) {
            disp_err('#upload', '[Please upload only XLS.]');
            $('#upload').val('');
            $retval = false;
        } else {
            remove_err('#upload');
        }
        return $retval;
    }
    $('#filter_status').change(function() {
        $val = $(this).val();
        if ($val == 'Success') {
            $('tr.danger').hide();
            $('tr.nodanger').show();
            $('.export_but').attr('href', $export_url + $filesa);
        } else if ($val == 'Failure') {
            $('tr.danger').show();
            $('tr.nodanger').hide();
            $('.export_but').attr('href', $export_url + $filesb);
        } else {
            $('tr.danger').show();
            $('tr.nodanger').show();
            $('.export_but').attr('href', $export_url + $files);
        }
    })
    function disp_err($id, $text) {
        $text = typeof $text !== 'undefined' ? $text : '[required]';
        $($id).addClass('error');
        $($id + '_err').addClass('error').addClass('error_text').html($text);
    }
    function remove_err($id) {
        $($id).removeClass('error');
        $($id + '_err').removeClass('error').text('');
    }
});
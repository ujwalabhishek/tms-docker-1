/**
 * This js file includes in view tenant page
 */
$(document).ready(function() {
    $('#reason_for_deactivation').change(function() {
        $reason = $(this).val();
        if ($reason == 'OTHERS') {
            $('#row_dim_new1').fadeIn();
        } else {
            $('#row_dim_new1').fadeOut();
            $('#other_reason_for_deactivation').val('');
        }
    });
    $('#deactivate_form').submit(function() {
        return deactivate_validate(true);
    });
    $('#deactivate_form input, #deactivate_form select').change(function() {
        return deactivate_validate(false);
    });

    function deactivate_validate($retVal) {
        $reason = $('#reason_for_deactivation').val();
        if ($reason.length == 0) {
            disp_err('#reason_for_deactivation');
            $retVal = false;
        } else {
            remove_err('#reason_for_deactivation');
        }
        if ($reason == 'OTHERS' && $.trim($('#other_reason_for_deactivation').val()).length == 0) {
            disp_err('#other_reason_for_deactivation');
            $retVal = false;
        } else {
            remove_err('#other_reason_for_deactivation');
        }

        return $retVal;
    }
    function disp_err($id, $text) {
        $text = typeof $text !== 'undefined' ? $text : '[required]';
        $($id).addClass('error');
        $($id + '_err').addClass('error').addClass('error3').html($text);
    }
    function remove_err($id) {
        $($id).removeClass('error');
        $($id + '_err').removeClass('error').removeClass('error3').text('');
    }
});
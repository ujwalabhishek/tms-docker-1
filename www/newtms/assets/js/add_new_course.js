/* 
 * This js file included in add_new_course form
 */
$(document).ready(function() {
    var cnt = $('#sales_exec_tbl tr').size();
    cnt_array = [0];
    selected_exec_array = new Array();
    $("#sales_exec_addmore").click(function() {
        retVal = true;
        $("#sales_exec_tbl_err").text("").removeClass('error');
        retVal = validate_sales_executive();
        if (retVal == false) {
            return false;
        } else {
            var first_tr_id = $('#sales_exec_tbl').closest('table').find(' tbody tr:first').attr('id');
            var first_tr_id_number = first_tr_id.split('_');
            var last_tr_id = $('#sales_exec_tbl').closest('table').find(' tbody tr:last').attr('id');
            var last_tr_id_number = last_tr_id.split('_');

            var selected_sales_executives = $("#sales_executives_" + last_tr_id_number[3]).val();
            if (selected_sales_executives !== "") {
                selected_exec_array_push(selected_sales_executives);
            }
            var option_count = $('#sales_executives_' + first_tr_id_number[3] + ' option').length;
            option_count = option_count - 1;
            var tr_count = $('#sales_exec_tbl tr').size();
            if (tr_count == option_count) {
                $('#sales_exec_tbl_err').text("[There are no more sales executives to add.]").addClass('error');
                return false;
            } else {
                $("#sales_executives_" + last_tr_id_number[3] + " > option:not(:selected)").attr('disabled', true);
                $("#sales_exec_tbl_err").text("").removeClass('error');
                sales_exec_tbl_row = '<tr id="sales_exec_tblrow_' + cnt + '">' + $("#" + first_tr_id).html() + '</tr>';
                sales_exec_tbl_row = sales_exec_tbl_row.replace('sales_executives_' + first_tr_id_number[3], 'sales_executives_' + cnt);
                sales_exec_tbl_row = sales_exec_tbl_row.replace('sales_exec_commission_rates_' + first_tr_id_number[3], 'sales_exec_commission_rates_' + cnt);

                sales_exec_tbl_row = sales_exec_tbl_row.replace('sales_exec_remove_btn_' + first_tr_id_number[3], 'sales_exec_remove_btn_' + cnt);
                sales_exec_tbl_row = sales_exec_tbl_row.replace('remove_sales_exec_row(' + first_tr_id_number[3] + ')', 'remove_sales_exec_row(' + cnt + ')');
                sales_exec_tbl_row = sales_exec_tbl_row.replace('sales_exec_remove_btn_' + first_tr_id_number[3], 'sales_exec_remove_btn_' + cnt);
                sales_exec_tbl_row = sales_exec_tbl_row.replace('remove_sales_exec_row(' + first_tr_id_number[3] + ')', 'remove_sales_exec_row(' + cnt + ')');


                sales_exec_tbl_row = sales_exec_tbl_row.replace('sales_executives_err_' + first_tr_id_number[3], 'sales_executives_err_' + cnt);
                sales_exec_tbl_row = sales_exec_tbl_row.replace('sales_exec_commission_rates_err_' + first_tr_id_number[3], 'sales_exec_commission_rates_err_' + cnt);

                $('#sales_exec_tbl tr').last().after(sales_exec_tbl_row);
                cnt_array.push(cnt);
                removeBasedOnValue('sales_executives_' + cnt);
                cnt++;
            }
        }
        if ($('#sales_exec_tbl tr').size() > 1) {
            $('.remove2').show();
        }
    });

    $("#validity_yes").click(function() {
        $('#show_me').show();
    });
    $("#validity_no").click(function() {
        $('#show_me').hide();
    });
    $(".alphanumeric").keydown(function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 32]) !== -1 ||
                        (e.keyCode == 65 && e.ctrlKey === true) ||
                                (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return;
                }
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 90) && (e.keyCode < 96 || e.keyCode > 105))) {
                    e.preventDefault();
                }
            });
    $(".alphabets").keydown(function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 32]) !== -1 ||
                        (e.keyCode == 65 && e.ctrlKey === true) ||
                                (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return;
                }
                if (((e.keyCode < 65 || e.keyCode > 90))) {
                    e.preventDefault();
                }
            });

});
function removeBasedOnValue(id) {
    var current_val = $("#" + id).val();
    $("#" + id + " option").removeAttr('disabled');
    for (i = 0; i < selected_exec_array.length; i++) {
        if (current_val != selected_exec_array[i]) {
            $("#" + id + " > option[value='" + selected_exec_array[i] + "']").attr('disabled', 'disabled');
        }
    }

}

function validate_sales_executive() {

    var stat_val = true;
    for (i = 0; i < cnt_array.length; i++) {
        var last_selected_sales_exec = $("#sales_executives_" + cnt_array[i] + " option:selected").val();
        var last_selected_commission_rate = $.trim($("#sales_exec_commission_rates_" + cnt_array[i]).val());
        if (last_selected_commission_rate < 0 || last_selected_commission_rate > 99) {
            $('#sales_exec_commission_rates_err_' + cnt_array[i]).text("[Invalid range(0-99)]").addClass('error');
            $("#sales_exec_commission_rates_" + cnt_array[i]).addClass('error');
            stat_val = false;
        } else if (last_selected_commission_rate == "") {
            $('#sales_exec_commission_rates_err_' + cnt_array[i]).text("[required]").addClass('error');
            $("#sales_exec_commission_rates_" + cnt_array[i]).addClass('error');
            stat_val = false;
        } else {
            $("#sales_exec_commission_rates_err_" + cnt_array[i]).text("").removeClass('error');
            $("#sales_exec_commission_rates_" + cnt_array[i]).removeClass('error');

        }
        if (last_selected_sales_exec == "") {
            $('#sales_executives_err_' + cnt_array[i]).text("[required]").addClass('error');
            $("#sales_executives_" + cnt_array[i]).addClass('error');
            stat_val = false;
        } else {
            $("#sales_executives_err_" + cnt_array[i]).text("").removeClass('error');
            $("#sales_executives_" + cnt_array[i]).removeClass('error');
        }
    }
    return stat_val;
}

function remove_sales_exec_row(rowId) {
    if ($('#sales_exec_tbl tr').size() == 2) {
        var first_tr_id = $('#sales_exec_tbl').closest('table').find(' tbody tr:first').attr('id');
        var first_tr_id_number = first_tr_id.split('_');
        var id = 'sales_executives_' + first_tr_id_number[3];
        $("#" + id + " option").attr('disabled', false);
        $('.remove2').hide();
    }
    if ($('#sales_exec_tbl tr').size() > 1) {
        selected_exec_array_pop(rowId);
        array_pop(rowId);
        $('#sales_exec_tblrow_' + rowId).remove();
        $("#sales_exec_tbl_err").text("").removeClass('error');
    } else {
        $('#sales_exec_tbl_err').text("[at least one Sales Executive required.]").addClass('error');
    }
    var last_tr_id = $('#sales_exec_tbl').closest('table').find(' tbody tr:last').attr('id');
    var last_tr_id_number = last_tr_id.split('_');
    removeBasedOnValue('sales_executives_' + last_tr_id_number[3]);
}

function selected_exec_array_pop(removeItem) {
    var id = 'sales_executives_' + removeItem;
    var value = $("#" + id + " option:selected").val();
    var i = selected_exec_array.indexOf(value);
    if (i != -1) {
        selected_exec_array.splice(i, 1);
    }
}
function selected_exec_array_push(removeItem) {
    var value = removeItem;
    var i = selected_exec_array.indexOf(value);
    if (i == -1) {
        selected_exec_array.push(value);
    }
}

function array_pop(removeItem) {
    var i = cnt_array.indexOf(removeItem);
    if (i != -1) {
        cnt_array.splice(i, 1);
    }
}

function isunique_course(e, id) {
    e = $.trim(e);
    if (e == '') {
        $("#" + id + "_err").text("[required]").addClass('error');
        $("#" + id).addClass('error');
        return false;
    } else {
        $.ajax({
            url: "check_course_name",
            type: "post",
            data: 'course_name=' + e,
            success: function(res) {
                if (res == 1) {
                    window.email_id = 'exists';
                    $("#" + id + "_err").text("[Couse Name exists!]").addClass('error');
                    $("#" + id).addClass('error');
                    return false;
                } else {
                    window.email_id = 'notexists';
                    $("#" + id + "_err").text("").removeClass('error');
                    $("#" + id).removeClass('error');
                    return true;
                }
            },
            error: function() {
                return false;
            }
        });
    }
}

function validate() {

    retVal = true;
    language = $.trim($('#language').val());
    if (language == null || language == '' || language == 'multiselect-all') {
        $("#language_err").text("[required]").addClass('error');
        $("#language").addClass('error');
        retVal = false;
    } else {
        $("#language_err").text("").removeClass('error');
        $("#language").removeClass('error');
    }
    course_manager = $.trim($('#course_manager').val());
    if (course_manager == null || course_manager == '' || course_manager == 'multiselect-all') {
        $("#course_manager_err").text("[required]").addClass('error');
        $("#course_manager").addClass('error');
        retVal = false;
    } else {
        $("#course_manager_err").text("").removeClass('error');
        $("#course_manager").removeClass('error');
    }
    var course_description = $.trim($('#course_description').val());
    if (course_description == "") {
        $('#course_description_err').text("[required]").addClass('error');
        $("#course_description").addClass('error');
        retVal = false;
    } else {
        $('#course_description_err').text("").removeClass('error');
        $("#course_description").removeClass('error');
    }
    course_name = $.trim($('#course_name').val());
    if (course_name == "") {
        $("#course_name_err").text("[required]").addClass('error');
        $("#course_name").addClass('error');
        retVal = false;
    }
    course_types = $.trim($('#course_types').val());
    if (course_types == "") {
        $("#course_type_err").text("[required]").addClass('error');
        $("#course_types").addClass('error');
        retVal = false;
    } else {
        $("#course_type_err").text("").removeClass('error');
        $("#course_types").removeClass('error');
    }
    class_types = $.trim($('#class_types').val());
    if (class_types == "") {
        $("#class_type_err").text("[required]").addClass('error');
        $("#class_types").addClass('error');
        retVal = false;
    } else {
        $("#class_type_err").text("").removeClass('error');
        $("#class_types").removeClass('error');
    }
    course_duration = $.trim($('#course_duration').val());
    if (course_duration == "") {
        $("#course_duration_err").text("[required]").addClass('error');
        $("#course_duration").addClass('error');
        retVal = false;
    } else {
        $("#course_duration_err").text("").removeClass('error');
        $("#course_duration").removeClass('error');
    }
    course_reference_num = $.trim($('#course_reference_num').val());
    if (course_reference_num == "") {
        $("#course_reference_num_err").text("[required]").addClass('error');
        $("#course_reference_num").addClass('error');
        retVal = false;
    } else {
        $("#course_reference_num_err").text("").removeClass('error');
        $("#course_reference_num").removeClass('error');
    }
    external_reference_num = $.trim($('#external_reference_number').val());
    if (external_reference_num == "") {
        $("#external_reference_number_err").text("[required]").addClass('error');
        $("#external_reference_number").addClass('error');
        retVal = false;
    } else {
        $("#external_reference_number_err").text("").removeClass('error');
        $("#external_reference_number").removeClass('error');
    }
    crse_admin_email = $.trim($('#crse_admin_email').val());
    if (crse_admin_email == "") {
        $("#crse_admin_email_err").text("[required]").addClass('error');
        $("#crse_admin_email").addClass('error');
        retVal = false;
    } else {
        $("#crse_admin_email_err").text("").removeClass('error');
        $("#crse_admin_email").removeClass('error');
    }
    course_competency_code = $.trim($('#course_competency_code').val());
    if (course_competency_code == "") {
        $("#course_competency_code_err").text("[required]").addClass('error');
        $("#course_competency_code").addClass('error');
        retVal = false;
    } else {
        $("#course_competency_code_err").text("").removeClass('error');
        $("#course_competency_code").removeClass('error');
    }
      //default commission rate
    default_commission_rate = $.trim($('#default_commission_rate').val());
    if (default_commission_rate == "") {
        $("#default_commission_rate_err").text("[required]").addClass('error');
        $("#default_commission_rate").addClass('error');
        retVal = false;
    } else {
        $("#default_commission_rate_err").text("").removeClass('error');
        $("#default_commission_rate").removeClass('error');
    }
    
    
    certification_code = $.trim($('#certification_code').val());
    if (certification_code == "") {
        $("#certification_code_err").text("[required]").addClass('error');
        $("#certification_code").addClass('error');
        retVal = false;
    } else {
        $("#certification_code_err").text("").removeClass('error');
        $("#certification_code").removeClass('error');
    }
    validity = $.trim($("#validity").val());
    if ($("#validity_yes").is(":checked") && validity == "") {
        $("#validity_err").text("[required]").addClass('error');
        $("#validity").addClass('error');
        retVal = false;
    } else if ($("#validity_yes").is(":checked") && valid_number(validity) == false) {
        $("#validity_err").text("[invalid]").addClass('error');
        $("#validity").addClass('error');
        retVal = false;
    } else {
        $("#validity_err").text("").removeClass('error');
        $("#validity").removeClass('error');
    }
    var validationStatus = validate_sales_executive();
    if (validationStatus == false) {
        retVal = false;
    }
    if ($('span').hasClass('error')) {
        retVal = false;
    }
    return retVal;
}

function reset_all() {
    $("#pre_requisite").multiselect("clearSelection");
    $("#pre_requisite").multiselect('refresh');
    $("#language").multiselect("clearSelection");
    $("#language").multiselect('refresh');
    $("#course_manager").multiselect("clearSelection");
    $("#course_manager").multiselect('refresh');
    $(".error").text("").removeClass('error');
    $("#sales_exec_tbl").find("tr:gt(0)").remove();
    $('.remove2').hide();
}

$(document).ready(function() {
    var check = 0;
    $('#addNewCourseForm').submit(function() {
        check = 1;
        if(validate()){
            var self = $(this),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            return true;
        }else{
            return false;
        }
        
    });
    $('#addNewCourseForm select,#addNewCourseForm input,#addNewCourseForm textarea').change(function() {
        if (check == 1) {
            return validate();
        }
    });
});

function remove_zip_file() {
    $('#zip_file').val('');
    $('#remove_upload_span').css('display', 'none');
}
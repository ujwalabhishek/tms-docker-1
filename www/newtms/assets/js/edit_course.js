/* 
 * This js included in edit course page.
 */
function validate_search() {
    var search_course_name = $.trim($("#search_course_name").val());
    if (search_course_name == "") {
        $("#search_course_name_err").text("[required]").addClass('error');
        $("#search_course_name").addClass('error');
        return false;
    } else if (search_course_name.indexOf('(') === -1) {
        $("#search_course_name_err").text("[select course from autofill]").addClass('error');
        $("#search_course_name").addClass('error');
        return false;
    } else {

        return true;
    }
}


$(document).ready(function () {
    //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 03/01/2019 AT 3:45PM////////////////////////////////////
    $('#search_form').on('submit', function () {
        if (validate_search()) {
            var self = $(this),
                    button = self.find('input[type="submit"],button');
            button.attr('disabled', 'disabled').html('Please Wait..');
            return true;
        } else {
            return false;
        }

    }); //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 03/01/2019 AT 3:45PM///////////////////////////////
    var cnt = $('#sales_exec_tbl tr').size();
    cnt_array = [];
    selected_exec_array = new Array();
    for (var i = 0; i < cnt; i++) {
        cnt_array.push(i);
        var selected_sales_executives = $("#sales_executives_" + i).val();
        if (selected_sales_executives != "")
            selected_exec_array.push(selected_sales_executives);
    }
    $("#sales_exec_addmore").click(function () {
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
            var selected_sales_executives = $("#sales_executives_" + last_tr_id_number[3] + " option:selected").val();
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
                $('#sales_exec_tblrow_' + cnt).find('input:text').val('');
                $('#sales_exec_tblrow_' + cnt).find('select').val('');
                removeBasedOnValue('sales_executives_' + cnt);
                cnt++;
            }
        }
        if ($('#sales_exec_tbl tr').size() > 1) {
            $('.remove2').show();
        }
    });

    $("#validity_yes").click(function () {
        $('#show_me').show();
    });
    $("#validity_no").click(function () {
        $('#show_me').hide();
    });
    $(".alphanumeric").keydown(function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 32]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 90) && (e.keyCode < 96 || e.keyCode > 105))) {
            e.preventDefault();
        }
    });
    $(".alphabets").keydown(function (e) {
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
        var last_selected_sales_exec = $.trim($('#sales_executives_' + cnt_array[i] + " option:selected").val());
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
function check_enrolled(sales_id) {
    var course_id = $('#course_id').val();
    if (sales_id != '') {
        $.ajax({
            url: baseurl + "course/check_enrolled",
            type: "post",
            async: false,
            data: {'sales_id': sales_id, 'course_id': course_id},
            success: function (res) {
                if (res == 1) {
                    $('#sales_exec_tbl_err').text("[Sales Executive has done Enrolments.]").addClass('error');
                    result = false;
                } else {
                    $('#sales_exec_tbl_err').text("").removeClass('error');
                    result = true;
                }
            },
            error: function () {
                result = false;
            }
        });
    }
}
function remove_zip_file(course_id) {
    $('#zip_file').val('');
    $('#zip_file_err').text('').removeClass('error');
    if (course_id != '') {
        $.ajax({
            url: baseurl + "course/remove_zip_file",
            type: "post",
            async: false,
            data: {'course_id': course_id},
            success: function (res) {
                $('#span_course_content').css('display', 'none');
                $('#zip_file_err').text('File has been removed.').addClass('error');
                remove_zip_file_result = true;
            },
            error: function () {
                remove_zip_file_result = false;
            }
        });
    }
}

function remove_sales_exec_row(rowId) {
    var id = 'sales_executives_' + rowId;
    var value = $("#" + id + " option:selected").val();
    if (value != '') {
        result = false;
        check_enrolled(value);
    } else {
        result = true;
    }
    if (result == true) {
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
        result = false;
    }
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

function validate() {
    var retVal = true;
    var language = $.trim($('#language option:selected').val());
    if (language == null || language == '') {
        $("#language_err").text("[required]").addClass('error');
        $("#language").addClass('error');
        retVal = false;
    } else {
        $("#language_err").text("").removeClass('error');
        $("#language").removeClass('error');
    }
    var course_manager = $.trim($('#course_manager').val());
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
    var course_name = $.trim($('#course_name').val());
    if (course_name == "") {
        $("#course_name_err").text("[required]").addClass('error');
        $("#course_name").addClass('error');
        retVal = false;
    }
    var course_types = $.trim($('#course_types').val());
    if (course_types == "") {
        $("#course_type_err").text("[required]").addClass('error');
        $("#course_types").addClass('error');
        retVal = false;
    } else {
        $("#course_type_err").text("").removeClass('error');
        $("#course_types").removeClass('error');
    }
    var class_types = $.trim($('#class_types').val());
    if (class_types == "") {
        $("#class_type_err").text("[required]").addClass('error');
        $("#class_types").addClass('error');
        retVal = false;
    } else {
        $("#class_type_err").text("").removeClass('error');
        $("#class_types").removeClass('error');
    }
    var course_duration = $.trim($('#course_duration').val());
    if (course_duration == "") {
        $("#course_duration_err").text("[required]").addClass('error');
        $("#course_duration").addClass('error');
        retVal = false;
    } else {
        $("#course_duration_err").text("").removeClass('error');
        $("#course_duration").removeClass('error');
    }
    var course_reference_num = $.trim($('#course_reference_num').val());
    if (course_reference_num == "") {
        $("#course_reference_num_err").text("[required]").addClass('error');
        $("#course_reference_num").addClass('error');
        retVal = false;
    } else {
        $("#course_reference_num_err").text("").removeClass('error');
        $("#course_reference_num").removeClass('error');
    }
    var tpg_course = $('#tpg_course').val();
    if (tpg_course == '1') {
        var external_reference_num = $.trim($('#external_reference_number').val());
        if (external_reference_num == "") {
            $("#external_reference_number_err").text("[required]").addClass('error');
            $("#external_reference_number").addClass('error');
            retVal = false;
        } else {
            $("#external_reference_number_err").text("").removeClass('error');
            $("#external_reference_number").removeClass('error');
        }
        
        var crse_admin_email = $.trim($('#crse_admin_email').val());
        if (crse_admin_email == "") {
            $("#crse_admin_email_err").text("[required]").addClass('error');
            $("#crse_admin_email").addClass('error');
            retVal = false;
        } else {
            $("#crse_admin_email_err").text("").removeClass('error');
            $("#crse_admin_email").removeClass('error');
        }
    }
    var course_competency_code = $.trim($('#course_competency_code').val());
    if (course_competency_code == "") {
        $("#course_competency_code_err").text("[required]").addClass('error');
        $("#course_competency_code").addClass('error');
        retVal = false;
    } else {
        $("#course_competency_code_err").text("").removeClass('error');
        $("#course_competency_code").removeClass('error');
    }

    var default_commission_rate = $.trim($('#default_commission_rate').val());
    if (default_commission_rate == "") {
        $("#default_commission_rate_err").text("[required]").addClass('error');
        $("#default_commission_rate").addClass('error');
        retVal = false;
    } else {
        $("#default_commission_rate_err").text("").removeClass('error');
        $("#default_commission_rate").removeClass('error');
    }

    var certification_codes = $.trim($('#certification_code').val());
    if (certification_codes == "") {
        $("#certification_code_err").text("[required]").addClass('error');
        $("#certification_codes").addClass('error');
        retVal = false;
    } else {
        $("#certification_code_err").text("").removeClass('error');
        $("#certification_codes").removeClass('error');
    }
    var validity = $.trim($("#validity").val());
    if ($("#validity_yes").is(":checked") && (validity == "" || validity == '0')) {
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
    if (retVal == true) {
        $('select[name="languages[]"] option:disabled').removeAttr('disabled');
    }
    return retVal;
}

function isunique_course(e, id) {
    var course_id = $("#course_id").val();
    e = $.trim(e);
    if (e == '') {
        $("#" + id + "_err").text("[required]").addClass('error');
        $("#" + id).addClass('error');
        return false;
    } else if (course_id != '') {
        $.ajax({
            url: "check_course_name",
            type: "post",
            data: {'course_name': e, 'course_id': course_id},
            success: function (res) {
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
            error: function () {
                return false;
            }
        });
    } else {
        $("#" + id + "_err").text("").removeClass('error');
        $("#" + id).removeClass('error');
    }
    return false;
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
    $('#editCourseForm').each(function () {
        this.reset();
    });
}

$(document).ready(function () {
    var check = 0;
    $('#addNewCourseForm').submit(function () {
        check = 1;
        return validate();
    });
    //////////////////////////////////added by shubhranshu to prevent multi click////////////////////////////////////////////////
    $('#editCourseForm').submit(function () {
        check = 1;
        if (validate()) {
            $('#updatebtn').hide()
            $('#deactivatebtn').hide()
            $('.button_class99').html('<button class="btn btn-primary" type="submit">Update</button>');
            var self = $(this),
                    button = self.find('input[type="submit"],button');
            button.attr('disabled', 'disabled').html('Please Wait..');
            return true;
        } else {
            return false;
        }

    });///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $('#addNewCourseForm select,#addNewCourseForm input,#addNewCourseForm textarea').change(function () {
        if (check == 1) {
            return validate();
        }
    });
});
$(function () {
    $("#deactivation_date").text($.datepicker.formatDate("dd/mm/yy", new Date()));
});

function valid_deactivate_reason(userName) {
    var pattern = new RegExp(/^[\sa-zA-Z0-9_,.-]+$/);
    return pattern.test(userName);
}
function validate_deactivate_user() {
    var retVal = true;
    reason_for_deactivation = $("#reason_for_deactivation").val();
    other_reason_for_deactivation = $("#other_reason_for_deactivation").val();
    if (reason_for_deactivation == "") {
        $("#reason_for_deactivation_err").text("[required]").addClass('error');
        $("#reason_for_deactivation").addClass('error');
        retVal = false;
    } else {
        $("#reason_for_deactivation_err").text("").removeClass('error');
        $("#reason_for_deactivation").removeClass('error');
    }

    if (reason_for_deactivation == "OTHERS") {
        if (other_reason_for_deactivation == "") {
            $("#other_reason_for_deactivation_err").text("[required]").addClass('error');
            $("#other_reason_for_deactivation").addClass('error');
            retVal = false;
        } else {
            if (valid_deactivate_reason(other_reason_for_deactivation) == false)
            {
                $("#other_reason_for_deactivation_err").text("[invalid]").addClass('error');
                $("#other_reason_for_deactivation").addClass('error');
                retVal = false;
            } else {
                $("#other_reason_for_deactivation_err").text("").removeClass('error');
                $("#other_reason_for_deactivation").removeClass('error');
            }
        }
    } else {
        $("#other_reason_for_deactivation_err").text("").removeClass('error');
    }
    return retVal;
}
$(document).ready(function () {
    $('.deactive').click(function () {
        var course_id = $('#course_id').val();
        $.ajax({
            url: baseurl + "course/get_active_class",
            type: "post",
            dataType: "json",
            async: false,
            data: {
                'course_id': course_id,
            },
            beforeSend: function () {
            },
            success: function (res) {
                var running_class = res.running;
                var yet_to_start_class = res.yet_to_start;
                if (running_class != 0 && yet_to_start_class != 0) {
                    $('#deacive_contents').html('');
                    $('#deacive_contents').html('<h3 style="color:red;font-family:arial;font-size:14px;font-weight:bold">Course cannot be De-Activated as there are classes in in-progress state and/or yet to start!.</h3>');
                }
                else if (running_class != 0) {
                    $('#deacive_contents').html('');
                    $('#deacive_contents').html('<h3 style="color:red;font-family:arial;font-size:14px;font-weight:bold">Course cannot be De-Activated as there are classes in in-progress state!.</h3>');
                }
                else if (yet_to_start_class != 0) {
                    $('#deacive_contents').html('');
                    $('#deacive_contents').html('<h3 style="color:red;font-family:arial;font-size:14px;font-weight:bold">Course cannot be De-Activated as there are classes yet to start!.</h3>');
                }
            }
        });
        $('#ex9').modal();
    });
});
var tms = tms || {};

tms.paging = function(totalPages, pageNumber, callback)
{
    if (totalPages < 2)
        return null;
    if (!pageNumber)
        pageNumber = 1;

    var links = [];
    for (var page = 1; page <= totalPages; page++)
    {
        links.push($('<li>', {'class': page == pageNumber ? 'active' : ''}).append($('<a href="#" onclick="' + callback + '(' + page + ');">' + page + '</a>')));
    }

    return $('<ul>', {'class': 'pagination'}).append(
            $('<li>', {'class': pageNumber == 1 ? 'disabled' : ''}).append(pageNumber == 1 ? $('<a href="#">&laquo;</a>') : $('<a href="#" onclick="' + callback + '(' + (pageNumber - 1) + ');">&laquo;</a>')),
            links,
            $('<li>', {'class': pageNumber == totalPages ? 'disabled' : ''}).append(pageNumber == totalPages ? $('<a href="#">&raquo;</a>') : $('<a href="#" onclick="' + callback + '(' + (1 * pageNumber + 1) + ');">&raquo;</a>'))
            );
};

tms.utils = {
    formatDateSingapore: function(d) {
        if (!d)
            return '';

        var dd = d.getDate();
        if (dd < 10)
            dd = '0' + dd;

        var mm = d.getMonth() + 1;
        if (mm < 10)
            mm = '0' + mm;


        var yy = d.getFullYear();
        if (yy < 10)
            yy = '0' + yy;

        return dd + '/' + mm + '/' + yy;
    },
    formatDateMysql: function(d) {
        if (!d)
            return '';

        var dd = d.getDate();
        if (dd < 10)
            dd = '0' + dd;

        var mm = d.getMonth() + 1;
        if (mm < 10)
            mm = '0' + mm;

        var yy = d.getFullYear();

        return  yy + '-' + mm + '-' + dd;
    }

};




$(document).ready(function() {

    jQuery("#search_user_firstname").autocomplete({
        source: function(request, response) {
            $('#search_user_id').val('');
            jQuery.get(baseurl + "internal_user/get_user_list_autocomplete", {
                query: request.term,
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
            });
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#search_user_id').val(id);
        },
        minLength: 4
    });

    $("#edit_search_company_name").autocomplete({
        source: function(request, response) {
            $.ajax({
                type: "GET",
                url: baseurl + "company/get_company_list_autocomplete",
                dataType: "json",
                data: {name_startsWith: request.term, mode: 'edit'},
                contentType: "application/json; charset=utf-8",
                success: function(data) {
                    response($.map(data, function(item) {
                        return {
                            value: item.company_id,
                            label: item.company_name + ' (' + item.comp_regist_num + ')',
                        }
                    }));
                }
            });
        },
        select: function(event, ui) {
            $("#edit_search_company_name").val(ui.item.label);
            $("#edit_search_company_id").val(ui.item.value);
            return false;
        },
        minLength:4
    });
    $("#list_search_company_name").autocomplete({
        source: function(request, response) {
            $("#list_search_company_id").val('');
            if(request.term.trim().length > 3){
                $.ajax({
                    type: "GET",
                    url: baseurl + "company/get_company_list_autocomplete",
                    dataType: "json",
                    data: {name_startsWith: request.term},
                    contentType: "application/json; charset=utf-8",
                    success: function(data) {
                        response($.map(data, function(item) {
                            return {
                                value: item.company_id,
                                label: item.company_name + ' (' + item.comp_regist_num + ')',
                            }
                        }));
                    }
                });
            }else{
                var d;
                response(d);
            }
        },
        select: function(event, ui) {
            $("#list_search_company_name").val(ui.item.label);
            $("#list_search_company_id").val(ui.item.value);
            return false;
        },
        minLength:4
    });
    jQuery("#course_code").autocomplete({
        source: function(request, response) {
            jQuery.get(baseurl + "course/get_course_list_autocomplete", {
                query: request.term,
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
                
            });
        },select: function(event, ui) {
            $('#course_code_id').val(ui.item.value);
            $('#course_code').val(ui.item.value);
            return false;
        },
        minLength: 3
    });
    jQuery("#course_code_ssg_api").autocomplete({
        source: function(request, response) {
            jQuery.get(baseurl + "ssgapi_course/get_course_list_autocomplete", {
                query: request.term,
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
                
            });
        },select: function(event, ui) {
            $('#course_code_id').val(ui.item.key);
            $('#course_code_ssg_api').val(ui.item.value);
            return false;
        },
        minLength: 3
    });
    jQuery("#first_last_name").autocomplete({
        source: function(request, response) {
            $('#user_id').val('');
            jQuery.get(baseurl + "internal_user/get_internal_user_autocomplete", {
                query: request.term,
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
            });
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#user_id').val(id);
        },
        minLength: 4
    });
    jQuery("#search_course_name").autocomplete({
        source: function(request, response) {
            jQuery.get(baseurl + "course/get_course_name_list_autocomplete", {
                query: request.term,
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
            });
        },
        minLength: 4
    });
    jQuery("#copy_course_name").autocomplete({
        source: function(request, response) {
            jQuery.get(baseurl + "course/get_copy_course_name_list_autocomplete", {
                query: request.term,
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
            });
        },
        minLength: 4
    });
    jQuery("#couse_name").autocomplete({
        source: function(request, response) {
            jQuery.get(baseurl + "course/get_course_name_autocomplete", {
                query: request.term,
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
            });
        },
        minLength: 4
    });
    jQuery("#couse_name_copy").autocomplete({
        source: function(request, response) {
            jQuery.get(baseurl + "course/get_course_name_copy_autocomplete", {
                query: request.term,
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
            });
        },
        minLength: 4
    });

    jQuery("#taxcode").autocomplete({
        source: function(request, response) {
            jQuery.get(baseurl + "trainee/get_trainees_by_taxcode_autocomplete", {
                query: request.term,
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
            });
        },
        minLength: 4
    });

    jQuery("#search_by_name_autocomplete").autocomplete({
        source: function(request, response) {
            $('#user_id').val('');
            jQuery.get(baseurl + "trainee/get_trainees_by_name_autocomplete", {
                query: request.term,
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
            });
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#user_id').val(id);
        },
        minLength: 4
    });

    jQuery("#tax_code").autocomplete({
        source: function(request, response) {
            $('#user_id').val('');
            jQuery.get(baseurl + "trainee/get_trainees_by_taxcode", {
                query: request.term,
                company_id: $('#off_company_name').val(),
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
            });
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#user_id').val(id);
            $('#tax_id').val(id);
        },
        minLength: 4
    });

    jQuery("#trainee_name_list").autocomplete({
        source: function(request, response) {
            $('#user_id').val('');
            jQuery.get(baseurl + "trainee/get_trainees_by_taxcode_autocomplete", {
                query: request.term,
                company_id: $('#off_company_name').val(),
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
            });
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#user_id').val(id);
        },
        minLength: 4,
    });
    $("#search_company_trainee_name").autocomplete({
        source: function(request, response) {
            company_id = $("#company_id").val();
            $.ajax({
                type: "GET",
                url: baseurl + "company/get_company_trainee_list_autocomplete",
                dataType: "json",
                data: {name_startsWith: request.term, company_id: company_id},
                contentType: "application/json; charset=utf-8",
                success: function(data) {
                    response($.map(data, function(item) {
                        if (item.tax_code_type == 'OTHERS') {
                            tax_code = item.other_identi_code;
                        } else {
                            tax_code = item.tax_code;
                        }
                        return {
                            label: item.first_name + ' (' + tax_code + ')',
                        }
                    }));
                }
            });
        },
        select: function(event, ui) {
            $("#search_company_trainee_name").val(ui.item.label);
            return false;
        }
    });
    $("#search_company_trainee_taxcode").autocomplete({
        source: function(request, response) {
            company_id = $("#company_id").val();
            $.ajax({
                type: "GET",
                url: baseurl + "company/get_company_trainee_taxcode_autocomplete",
                dataType: "json",
                data: {name_startsWith: request.term, company_id: company_id},
                contentType: "application/json; charset=utf-8",
                success: function(data) {
                    response($.map(data, function(item) {
                        return {
                            label: item.tax_code + ' (' + item.first_name + ')',
                        }
                    }));
                }
            });
        },
        select: function(event, ui) {
            $("#search_company_trainee_taxcode").val(ui.item.label);
            return false;
        }
    });


});
function valid_contact_number(contactNum) {
    return /^\d+$/.test(contactNum.replace(/[\s]/g, '')); 
}
function valid_email_address(emailAddress) {
    var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
    return pattern.test(emailAddress);
}
function valid_user_name(userName) {
    var n = userName.indexOf(" "); 
    return (n == -1)?true:false;
}
unique_username = '';
function isunique_username(e) {
    $.ajax({
        url: "check_username",
        type: "post",
        data: 'username=' + e,
        async: false,
        success: function(res) {
            unique_username = res;
        },
        error: function() {
        }
    });

    if (unique_username >= 1)
        return false;
    else
        return true;
}
unique_username_edit = '';
function isunique_username_edit(val, curr_val) {
    $.ajax({
        url: "check_username_edit",
        type: "post",
        data: 'username=' + val + '&curr_username=' + curr_val,
        async: false,
        success: function(res) {
            unique_username_edit = res;
        },
        error: function() {
        }
    });

    if (unique_username_edit >= 1)
        return false;
    else
        return true;
}
unique_email = '';
function isunique_email(e) {

    $.ajax({
        url: "check_email",
        type: "post",
        data: 'email=' + e,
        async: false,
        success: function(res) {
            unique_email = res;
        },
        error: function() {
        }
    });

    if (unique_email >= 1)
        return false;
    else
        return true;
}
unique_email_edit = '';
function isunique_email_edit(val, curr_val) {
    $.ajax({
        url: "check_email_edit",
        type: "post",
        data: 'email=' + val + '&curr_email=' + curr_val,
        async: false,
        success: function(res) {
            unique_email_edit = res;
        },
        error: function() {
        }
    });

    if (unique_email_edit >= 1)
        return false;
    else
        return true;
}
unique_regno = '';
function isunique_regno(e) {
    $.ajax({
        url: "check_registration_number",
        type: "post",
        data: 'reg_num=' + e,
        async: false,
        success: function(res) {
            unique_regno = res;
        },
        error: function() {
        }
    });

    if (unique_regno >= 1)
        return false;
    else
        return true;
}
unique_regno_edit = '';
function isunique_regno_edit(val, curr_val) {

    $.ajax({
        url: "check_registration_number_edit",
        type: "post",
        data: 'reg_num=' + val + '&curr_reg_num=' + curr_val,
        async: false,
        success: function(res) {
            unique_regno_edit = res;
        },
        error: function() {
        }
    });

    if (unique_regno_edit >= 1)
        return false;
    else
        return true;
}
function getDomainURL() {
    var url = location.href;  
    var baseURL = url.substring(0, url.indexOf('/', 14));
    if (baseURL.indexOf('http://localhost') != -1) {
         var url = location.href; 
        var pathname = location.pathname; 
        var index1 = url.indexOf(pathname);
        var index2 = url.indexOf("/", index1 + 1);
        var baseLocalUrl = url.substr(0, index2);
        return baseLocalUrl + "/";
    }
    else {
        return baseURL + "/";
    }

}
function getBaseURL() {
    var l = window.location;
    var base_url = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1];

    base_url = base_url + "/";
    return base_url;
}
function valid_deactivate_reason(reason) {
    var pattern = new RegExp(/^[\sa-zA-Z0-9_,.-]+$/);
    return pattern.test(reason);
}
$(document).ready(function() {
    $.validator.addMethod("valid_company_name",
            function(value, element) {
                var pattern = new RegExp(/^[\sa-zA-Z0-9]+$/);
                return pattern.test(value);
            },
            "[invalid]"
            );

    $.validator.addMethod("valid_contact_number",
            function(value, element) {
                var pattern = new RegExp(/^[,0-9]+$/);
                return pattern.test(value);
            },
            "[invalid]"
            );

    $.validator.addMethod("valid_mobile_number",
            function(value, element) {
                if (value.trim() == '') {
                    return true;
                }
                else {
                    var pattern = new RegExp(/^[,0-9]+$/);
                    return pattern.test(value);
                }
            },
            "[invalid]"
            );


    $.validator.addMethod("valid_fax_number",
            function(value, element) {
                if (value == '') {
                    return true;
                }
                else {
                    var pattern = new RegExp(/^[\sa-zA-Z0-9-]+$/);
                    return pattern.test(value);
                }
            },
            "[invalid]"
            );

    $.validator.addMethod("valid_float_number",
            function(value, element) {
                if (value == '' || value == 0) {
                    return true;
                }
                else {
                    var pattern = new RegExp(/^([0-9]{1,2})+\.+([0-9]{1,2})+$/);
                    return pattern.test(value);
                }
            },
            "[invalid]"
            );


    $.validator.addMethod("valid_zipcode",
            function(value, element) {
                if (value == '') {
                    return true;
                }
                else {
                    var pattern = new RegExp(/^[a-zA-Z0-9]+$/);
                    return pattern.test(value);
                }
            },
            "[invalid]"
            );


});

function showimagepreview(input) {
    var ext = $('#userfile').val().split('.').pop().toLowerCase();
    if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
        $('#image_err').text('Invalid file!').addClass('error');
        $('#userfile').val('');
        $('#imgprvw').removeAttr('src');
        $('#removeimagebutton').hide();
        return false;
    }

    if (input.files && input.files[0]) {

        var size = input.files[0].size;
        if (size > 0) {
            var sizekb = size / 1024;
        } else {
            return false;
        }
        if (sizekb > 3075) {
            $('#userfile').val('');
            $('#imgprvw').removeAttr('src');
            $('#user_image_preview').hide();
            $('#image_err').text('Image size is too big. Please upload image which is not more than 3 MB in size.').addClass('error');
            $('#removeimagebutton').hide();
            return false;
        }

        var filerdr = new FileReader();
        filerdr.onload = function(e) {
            $('#image_err').text('').removeClass('error');
            $('#imgprvw').attr('src', e.target.result);
            $('#user_image_preview').show();
            $('#deleteimageyes').removeAttr('checked');
            $('#deleteimageno').attr('checked', 'checked');
            $('#imgprvw').attr('width', '120px');
            $('#imgprvw').attr('height', '100px');
        }
        filerdr.readAsDataURL(input.files[0]);
    }
    $('#removeimagebutton').show();
}
function remove_image() {
    $('#userfile').val('');
    $('#imgprvw').removeAttr('src');
    $('#user_image_preview').hide();
    $('#deleteimageyes').removeAttr('checked');
    $('#deleteimageno').attr('checked', 'checked');

}


function validate_course_file(input) {
    var ext = $('#zip_file').val().split('.').pop().toLowerCase();
    if ($.inArray(ext, ['zip', 'tar']) == -1) {
        $('#remove_upload_span').css('display', 'none');
        $('#zip_file_err').text('Please upload only zip files!').addClass('error');
        $('#zip_file').val('');
        return false;
    }

    if (input.files && input.files[0]) {

        var size = input.files[0].size;
        if (size > 0) {
            var sizekb = size / 1024;
        } else {
            return false;
        }
        if (sizekb > 26700) {
            $('#zip_file').val('');
            $('#zip_file_err').text('File size is too big. Please upload file which is not more than 20 MB in size.').addClass('error');
            $('#remove_upload_span').css('display', 'none');
            $('#removeimagebutton').hide();
            return false;
        } else {
            $('#zip_file_err').text('').removeClass('error');
            $('#remove_upload_span').css('display', '');
        }
    }
}
function validate_trainee_bulk(input) {
    var ext = $('#xls_file').val().split('.').pop().toLowerCase();
    if ($.inArray(ext, ['xls', 'xlsx']) == -1) {
        $('#xls_file_err').text('Please upload excel file!').addClass('error');
        $('#xls_file').val('');
        return false;
    } else {
        $('#xls_file_err').text('').removeClass('error');
    }
}
//////////////////////////////////////added by shubhranshu on 27/11/2018/////////////////////////////

function exportValidate(){
        if(form_validates()){
            return true;
        }else{
            return false;
        }
    }
function disp_err($id, $text) {
    $text = typeof $text !== 'undefined' ? $text : '[required]';
    $($id).addClass('error');
    $($id + '_err').addClass('error').addClass('error_text').html($text);
}
function remove_err($id) {
    $($id).removeClass('error');
    $($id + '_err').removeClass('error').text('');
}
    /////////////////////added by shubhranshu for search validate on ///////////////////////////
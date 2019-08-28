edu_cnt_array = [0];
other_cnt_array = [0];
work_cnt_array = [0];      
    $("#empfrom_datetimepicker_0").datepicker({dateFormat: 'dd-mm-yy',  changeMonth: true, changeYear: true,
        maxDate: 0,
        onSelect: function(selectedStartDate) {                    
            $("#empto_datetimepicker_0").datepicker("option", {
                minDate: selectedStartDate,     
                maxDate:0
            });
        }
    });
    $("#empto_datetimepicker_0").datepicker({dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,
        yearRange: "-50:+50",
        minDate: 0,
        maxDate: -1,
    });

    $("#year_of_comp_0").datepicker({changeYear: true,maxDate: 0,dateFormat: 'yy',yearRange: '1945:'+(new Date).getFullYear()});
    $("#year_of_certi_0").datepicker({changeYear: true,maxDate: 0,dateFormat: 'yy',yearRange: '1945:'+(new Date).getFullYear(),
        onSelect: function(selectedStartDate) {              
                    $("#validity_0").datepicker("option", {                               
                       minDate: '01-01-'+selectedStartDate,
                       maxDate:''
                    });
                }
        });
    $("#validity_0").datepicker({
        dateFormat: 'dd-mm-yy', 
        changeMonth: true, 
        changeYear: true,            
        minDate: 0,
        maxDate: -1,  
        yearRange: "-100:+100",
    });
function addmore(e) {                        
    selected_exec_array = [];
    retVal = true;
    $("#" + e + "_err").text("").removeClass('error');

    retVal = true;
    if (e == 'edu') {                  
        retVal = addmore_validate(e,edu_cnt_array);
    }
    if (e == 'other'){                
        retVal = addmore_validate(e,other_cnt_array);
    }
    if (e == 'work'){                
        retVal = addmore_validate(e,work_cnt_array);
    }            
    if (retVal == false) {
        return false;
    } else {

        var first_tr_id = $('#' + e + '_tbl').closest('table').find(' tbody tr:first').attr('id');
        var first_tr_id_number = first_tr_id.split('_');

        var last_tr_id = $('#' + e + '_tbl').closest('table').find(' tbody tr:last').attr('id');
        var last_tr_id_number = last_tr_id.split('_');
        var cnt = last_tr_id_number[2];
        cnt++;                
        $("#" + e + "_firstcolumn_" + last_tr_id_number[2] + " > option:not(:selected)").attr('disabled', true);
        $("#" + e + "_tbl_err").text("").removeClass('error');
        tbl_row = '<tr id="' + e + '_row_' + cnt + '">' + $("#" + first_tr_id).html() + '</tr>';              
        tbl_row = tbl_row.replace(e + '_firstcolumn_' + first_tr_id_number[2], e + '_firstcolumn_' + cnt);
        tbl_row = tbl_row.replace("remove_row('" + e + "_remove_" + first_tr_id_number[2] + "')", "remove_row('" + e + "_remove_" + cnt + "')");
        tbl_row = tbl_row.replace(e + '_err' + first_tr_id_number[2], e + '_err' + cnt);
        tbl_row = tbl_row.replace('year_of_comp_' + first_tr_id_number[2], 'year_of_comp_' + cnt);
        tbl_row = tbl_row.replace('year_of_certi_' + first_tr_id_number[2], 'year_of_certi_' + cnt);
        tbl_row = tbl_row.replace('validity_' + first_tr_id_number[2], 'validity_' + cnt);
        tbl_row = tbl_row.replace('class="hasDatepicker"', '');
        tbl_row = tbl_row.replace('empfrom_datetimepicker_' + first_tr_id_number[2], 'empfrom_datetimepicker_' + cnt);
        tbl_row = tbl_row.replace('class="hasDatepicker"', '');
        tbl_row = tbl_row.replace('empto_datetimepicker_' + first_tr_id_number[2], 'empto_datetimepicker_' + cnt);
        tbl_row = tbl_row.replace('class="hasDatepicker"', '');                
        $('#' + e + '_tbl tr').last().after(tbl_row);
        $('#' +  e + '_firstcolumn_' + cnt).val($('#'+ e + '_firstcolumn_' + cnt +' option:first').val()); 

        if (e == 'edu') { 
            edu_cnt_array.push(cnt);
            removeBasedOnValue('edu_firstcolumn_' + cnt);
        }
        if (e == 'other') {
            other_cnt_array.push(cnt);
        }
        if (e == 'work') {
            work_cnt_array.push(cnt);
        }
        $("#year_of_comp_" + cnt).datepicker({changeYear: true,maxDate: 0,dateFormat: 'yy',yearRange: '1945:'+(new Date).getFullYear()});
        $("#year_of_certi_" + cnt).datepicker({
            changeMonth: true, 
            changeYear: true,
            maxDate: 0,
            dateFormat: 'yy',
            yearRange: "-100:+100",
            onSelect: function(selectedStartDate) {                       
                $("#validity_" + cnt).datepicker("option", {                           
                   minDate: '01-01-'+selectedStartDate,  
                   maxDate: '', 
                });
            }
        });
        $("#validity_" + cnt).datepicker({
            dateFormat: 'dd-mm-yy', 
            changeMonth: true, 
            changeYear: true, 
            maxDate: -1,
            minDate: 0,
            yearRange: "-100:+100",
        });
        $("#empfrom_datetimepicker_" + cnt).datepicker({
            changeMonth: true, 
            changeYear: true, 
            dateFormat: 'dd-mm-yy', 
            maxDate: 0,
            onSelect: function(selectedStartDate) {                          
                $("#empto_datetimepicker_" + cnt).datepicker("option", {
                    minDate: selectedStartDate,     
                    maxDate:0                        
                });
            }
        });
        $("#empto_datetimepicker_" + cnt).datepicker({
            dateFormat: 'dd-mm-yy', 
            changeMonth: true, 
            changeYear: true,  
            maxDate: -1, 
            minDate: 0, 
            yearRange: "-50:+50" ,                    
        });                               
    }
    if ($('#' + e + '_tbl tr').size() > 1) {
        $('#' + e + '_tbl .remove2').show();
    }
}
function removeBasedOnValue(id) { 
    var current_val = $("#" + id).val();
    $("#" + id + " option").removeAttr('disabled');           
    for (i = 0; i < selected_exec_array.length; i++) {
        if(current_val != selected_exec_array[i] ) {
            $("#" + id + " > option[value='" + selected_exec_array[i] + "']").attr('disabled', true);
        }
    }            
}
function addmore_validate(e,cnt_array) {            
    var stat_val = true;            
    for (i = 0; i < cnt_array.length; i++) {
        if(e == 'edu')
            var last_row_value = $.trim($( "#" + e + '_firstcolumn_' + cnt_array[i] +" option:selected" ).val());
        else
            var last_row_value = $.trim($("#" + e + "_firstcolumn_" + cnt_array[i]).val());                
        if (last_row_value == "") {                                      
            $("#" + e + "_err" + cnt_array[i]).text("[required]").addClass('error');
            $("#" + e + "_err" + cnt_array[i]).show();
            $("#" + e + "_firstcolumn_" + cnt_array[i]).addClass('error');
            stat_val = false;
        } else {
            $("#" + e + "_err" + cnt_array[i]).text("").removeClass('error');
            $("#" + e + "_firstcolumn_" + cnt_array[i]).removeClass('error');
            if (e == 'edu') {
                var selected_edu_level = $.trim($( "#" + e + '_firstcolumn_' + cnt_array[i] +" option:selected" ).val());               
                if (selected_edu_level != "")
                    selected_exec_array.push(selected_edu_level);
            }
        }
    }            
    return stat_val;
}

function remove_row(rowid) {
    var rowarray = rowid.split('_');
    var e = rowarray[0];
    var rowId = rowarray[2];

    if ($('#' + e + '_tbl tbody tr').size() == 2) {
        var first_tr_id = $('#' + e + '_tbl').closest('table').find(' tbody tr:first').attr('id');    
        var first_tr_id_number=first_tr_id.split('_');
        var id = 'edu_firstcolumn_'+first_tr_id_number[2];                
        $("#" + id + " option").attr('disabled',false);
        $('#' + e + '_tbl tbody tr td span').hide();
    }
    if ($('#' + e + '_tbl tbody tr').size() < 2) {
         $("#add" + e + "_err").text("[ Atlease one row is required.]").addClass('error');
         return false;
     }else{
         $("#add" + e + "_err").text("").removeClass('error');
     }

    if ($('#' + e + '_tbl tr').size() > 1) {
        if (e == 'edu') {                    
            selected_array_pop(rowId, e);                   
        }
        array_pop(rowId, e);
        $('#' + e + '_row_' + rowId).remove();
        $("#" + e + "_tbl_err").text("").removeClass('error');
    } else {
        $("#" + e + "_tbl_err").text("[ Atlease one row is required.]").addClass('error');
    }
    if (e == 'edu') {
        var last_tr_id = $('#' + e + '_tbl').closest('table').find(' tbody tr:last').attr('id');
        var last_tr_id_number = last_tr_id.split('_');
        removeBasedOnValue('edu_firstcolumn_' + last_tr_id_number[2]);
    }
}

function selected_array_pop(removeItem, e) {            
    var remove_value = $( "#" + e + '_firstcolumn_' + removeItem +" option:selected" ).val();
    selected_exec_array = jQuery.grep(selected_exec_array, function(value) {
            return value != remove_value;
    });            
}

function array_pop(removeItem, e) {
    if (e == 'edu') {                                                 
        edu_cnt_array = jQuery.grep(edu_cnt_array, function(value) {
            return value != removeItem;
        });
    }
    if (e == 'other') {                
        other_cnt_array = jQuery.grep(other_cnt_array, function(value) {
            return value != removeItem;
        });               
    }
    if (e == 'work') {                                 
        work_cnt_array = jQuery.grep(work_cnt_array, function(value) {
            return value != removeItem;
        });
    }            
}                  
$("#country_of_residence").change(function() {

    country_of_residence = $('#country_of_residence').val();
    if (country_of_residence == "") {
        $("#country_of_residence > option").each(function() {
            if (this.value != "")
                $("#" + this.value).hide();
        });
    }

    $("#country_of_residence > option").each(function() {
        if (this.value == country_of_residence) {
            $("#" + country_of_residence).show();
        }
        else {
            if (this.value != "") {                        
                $("#"+this.value).hide();
                $("#"+this.value+"_OTHERS").hide();
                $("#"+this.value+"_ID").hide();
                remove_all_errors();
            }
        }
    });

    if (country_of_residence == "IND") {               
        $("#PAN").show();
        $("#NRIC").hide();
        $("#SSN").hide();
        $("#SGP_ID").hide();
        $("#SGP_OTHERS").hide();
        $('#SSN_err').text('').removeClass('error');
        $('#SSN').removeClass('error');
        remove_nric_errors();
    }
    if (country_of_residence == "SGP") {             
        $("#NRIC").show();
        $('#NRIC option:first-child').attr("selected", "selected");
        $("#PAN").hide();
        $("#SSN").hide();
        remove_ind_usa_errors();
    }
    if (country_of_residence == "USA") {               
        $("#SSN").show();
        $("#PAN").hide();
        $("#NRIC").hide();
        $("#SGP_ID").hide();
        $("#SGP_OTHERS").hide();
        $('#PAN_err').text('').removeClass('error');
        $('#PAN').removeClass('error');
        remove_nric_errors();
    }
});
function remove_nric_errors() {
    $('#NRIC_err').text('').removeClass('error');
    $('#NRIC').removeClass('error');
    $('#NRIC_OTHER_err').text('').removeClass('error');
    $('#NRIC_OTHER').removeClass('error');
    $('#NRIC_ID_err').text('').removeClass('error');
    $('#NRIC_ID').removeClass('error');
}
function remove_ind_usa_errors() {
    $('#PAN_err').text('').removeClass('error');
    $('#PAN').removeClass('error');
    $('#SSN_err').text('').removeClass('error');
    $('#SSN').removeClass('error');
}
function remove_all_errors() {
    $('#NRIC_err').text('').removeClass('error');
    $('#NRIC').removeClass('error');
    $('#NRIC_OTHER_err').text('').removeClass('error');
    $('#NRIC_OTHER').removeClass('error');
    $('#NRIC_ID_err').text('').removeClass('error');
    $('#NRIC_ID').removeClass('error');
    $('#PAN_err').text('').removeClass('error');
    $('#PAN').removeClass('error');
    $('#SSN_err').text('').removeClass('error');
    $('#SSN').removeClass('error');
}
/*
* This method for singapore contry of residence.
*/  
$("#NRIC").change(function() {     
    if(this.value == "") {        
        $("#SGP_ID").hide();
        $("#SGP_OTHERS").hide();
    }else if(this.value == "SNG_3") {
        $("#SGP_OTHERS").show();
        $('#SGP_OTHERS option:first-child').attr("selected", "selected");
        $('#SGP_OTHERS_label').text('');
        $('#SGP_OTHERS_label').text('OTHERS :');
        $('#SGP_ID_label').text('');
        $('#SGP_ID_label').text('OTHERS :'); 
    }else {
        $('#SGP_OTHERS_label').text('');
        $('#SGP_OTHERS_label').text('NRIC :');
        $('#SGP_ID_label').text('');
        $('#SGP_ID_label').text('NRIC Code :'); 
        $("#SGP_OTHERS").hide();
        $("#SGP_ID").show();
    }
});
$("#NRIC_OTHER").change(function() {     
    if(this.value == "") {        
        $("#SGP_ID").hide();
    }else {    
        $("#SGP_ID").show();        
    }
});

$(function() {
        var d = new Date();        
        var currentYear = d.getFullYear(); 
        var currenyMonth=d.getMonth();
        var CurrentDate=d.getDay();
        var startYear = currentYear - 90;
        var endYear = currentYear - 10;
        $(function() {          
          $( "#pers_dob" ).datepicker({ 
              dateFormat: 'dd-mm-yy',
              minDate: new Date(startYear,currenyMonth,CurrentDate),
              maxDate: new Date(endYear,currenyMonth,CurrentDate),
              changeMonth: true,
              changeYear: true,
              yearRange: '-100:+0'              
          });
        });
});

$('#pers_country').change(function() {
    var country_param = $(this).val();    
    if (country_param) {
        $.post( baseurl + 'user/get_states_json', {country_param: country_param}, function(data) {
            json_data = $.parseJSON(data);
            $pers_states_select = $("#pers_states");
            $pers_states_select.html('<option value="">Select</option>');
            $.each(json_data, function(i, item) {
                $pers_states_select.append('<option value="' + item.parameter_id + '">' + item.category_name + '</option>');
            });
        });
    }else {
        $('#pers_states').find('option').remove().end().append('<option value="">Select</option>');
    }    
});        
function validate_alternate_email(e,id) {
    e=$.trim(e);
    if(e =='') {
        $("#"+id+"_err").text("").removeClass('error');
        $("#"+id).removeClass('error');
        return false;
    }else if(valid_email_address(e) == false) {
        $("#"+id+"_err").text("[invalid]").addClass('error');
        $("#"+id).addClass('error');
        return false;
    }else {
        $("#"+id+"_err").text("").removeClass('error');
        $("#"+id).removeClass('error'); 
        return true;
    } 
}
function  validate_pers_contact_number(e,id){
       e=$.trim(e);
    if(e =='') {
      $("#"+id+"_err").text("[invalid]").addClass('error');
        $("#"+id).addClass('error');
        return false;
    }else if(valid_contact_number(e) == false) {
        $("#"+id+"_err").text("[invalid]").addClass('error');
        $("#"+id).addClass('error');
        return false;
    }else {
        $("#"+id+"_err").text("").removeClass('error');
        $("#"+id).removeClass('error'); 
        return true;
    } 
}
function validate_alternate_pers_contact_number(e,id){
     e=$.trim(e);
    if(e =='') {
        $("#"+id+"_err").text("").removeClass('error');
        $("#"+id).removeClass('error');
        return false;
    }else if(valid_contact_number(e) == false) {
        $("#"+id+"_err").text("[invalid]").addClass('error');
        $("#"+id).addClass('error');
        return false;
    }else {
        $("#"+id+"_err").text("").removeClass('error');
        $("#"+id).removeClass('error'); 
        return true;
    } 
}
function isunique_username(e,id) {
    e=$.trim(e);        
    if(e ==''){
        $("#"+id+"_err").text("[required]").addClass('error');
        $("#"+id).addClass('error');
        return false;
    } else if(valid_user_name(e) == false) {
        $("#"+id+"_err").text("[invalid]").addClass('error');
        $("#"+id).addClass('error');
    } else {
        $.ajax({
            url: baseurl + "user/check_username",
            type: "post",
            data: 'username='+e,
            success: function(res){
                if(res == 1) {
                    window.username = 'exists';                        
                    $("#"+id+"_err").text("[Username exists!]").addClass('error');
                    $("#"+id).addClass('error');
                    return false;
                }else{
                    window.username = 'notexists';
                    $("#"+id+"_err").text("").removeClass('error');
                    $("#"+id).removeClass('error');
                    return true;
                }
            },
            error:function(){
                return false;
            }
        });
    }        
}

function isunique_taxcode(e,id) {
    e=$.trim(e);
    if(e ==''){
        $("#"+id+"_err").text("[required]").addClass('error');
        $("#"+id).addClass('error');
        return false;
    }else{
        var $country_of_residence = $.trim($('#country_of_residence').val());
        var $nric = $.trim($('#NRIC').val());  
        $.ajax({
            url: baseurl + "user/check_taxcode",
            type: "post",            
            data: {tax_code: e, country_of_residence:$country_of_residence, nric:$nric},
            success: function(res){
                if(res == 1) {
                    window.username = 'exists';                        
                    $("#"+id+"_err").text("[code exists!]").addClass('error');
                    $("#"+id).addClass('error');
                    return false;
                } else if (res == 2) {                            
                    $("#" + id + "_err").text("[Invalid!]").addClass('error');
                    $("#" + id).addClass('error');
                    return false;
                } else{
                    window.username = 'notexists';
                    $("#"+id+"_err").text("").removeClass('error');
                    $("#"+id).removeClass('error');
                    return true;
                }
            },
            error:function(){
                return false;
            }
        });
    }        
}
function confirm_email(confirm) {
    var email = $.trim($('#user_registered_email').val());
    if(confirm == '' && email !='') {
        $("#pers_conf_email_err").text("[required]").addClass('error');
        $("#pers_conf_email").addClass('error');
    } else if(valid_email_address(confirm) == false && confirm !='') {
        $("#pers_conf_email_err").text("[invalid]").addClass('error');
        $("#pers_conf_email").addClass('error');        
    } else if(email != confirm ) {
        $('#pers_conf_email_err').text('[Email does not match]').addClass('error');
        $('#pers_conf_email').addClass('error');        
    } else {
        $('#pers_conf_email_err').text('').removeClass('error');
        $('#pers_conf_email').removeClass('error');
    }
    return false;
}    

function isunique_email(e,id) {
    e=$.trim(e);
    if(e == '' && $("#bypassemail_1").is(":checked")){
        $("#"+id+"_err").text("").removeClass('error');
        $("#"+id).removeClass('error');
        return false;
    }
    if(e ==''){
        $("#"+id+"_err").text("[required]").addClass('error');
        $("#"+id).addClass('error');
        return false;
    }else if(valid_email_address(e) == false){
        $("#"+id+"_err").text("[invalid]").addClass('error');
        $("#"+id).addClass('error');
        return false;
    }else{
        $.ajax({
            url: baseurl + "user/check_email",
            type: "post",
            data: 'email='+e,
            success: function(res){
                if(res == 1) {
                    window.email_id = 'exists';                        
                    $("#"+id+"_err").text("[Email Id exists!]").addClass('error');
                    $("#"+id).addClass('error');                        
                    return false;
                }else{
                    window.email_id = 'notexists';                    
                    $("#"+id+"_err").text("").removeClass('error');
                    $("#"+id).removeClass('error');                        
                    return true;
                }
            },
            error:function(){                    
                return false;
            }
        });
    }       
}

function valid_contact_number(contactNum) {   
    
      return /^\d+$/.test(contactNum.replace(/[,\s]/g, '')); 
   
}
function valid_email_address(emailAddress) {
    var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);    
    return pattern.test(emailAddress);
}
function valid_user_name(userName) {
    var pattern = new RegExp(/^[a-zA-Z0-9_]+$/);    
    return pattern.test(userName);
}
function valid_discount(discountValue) {
    var pattern = new RegExp(/^(([1-8][0-9]?|9[0-8]?)\.\d+|[1-9][0-9]?)$/);
    return pattern.test(discountValue);
}
function  valid_date_field(dateofjoin) {
      var pattern = /^\d{1,2}-\d{1,2}-\d{4}$/;
      return pattern.test(dateofjoin);
  }
function valid_name($name) {
    var ck_name = /^[A-Za-z ]+$/;
    return ck_name.test($name);
} 
function valid_zip($zip) {
    var ck_name = /^[A-Za-z0-9 ]+$/;
    return ck_name.test($zip);
}
function validate() {                  
    var retVal = true;
    var country_of_residence = $("#country_of_residence").val();
    if (country_of_residence == "") {
        $("#country_of_residence_err").text("[required]").addClass('error');
        $("#country_of_residence").addClass('error');
        retVal = false;
    } 
    else{
        $("#country_of_residence_err").text("").removeClass('error');
        $("#country_of_residence").removeClass('error');
    }

    if (country_of_residence == "IND") {
        var PAN = $.trim($("#PAN").val());
        var pan_error_text = $("#PAN_err").text();
        if (PAN == "") {
            $("#PAN_err").text("[required]").addClass('error');
            $("#PAN").addClass('error');
            retVal = false;
        } 
        else if(pan_error_text != '[code exists!]') {
            $("#PAN_err").text("").removeClass('error');
            $("#PAN").removeClass('error');
        }
    }
    if (country_of_residence == "SGP") {
        var NRIC = $.trim($("#NRIC").val());
        var nric_error_text = $("#NRIC_ID_err").text();
        var NRIC_ID = $("#NRIC_ID").val();
        var NRIC_OTHER = $("#NRIC_OTHER").val();
        if (NRIC == "") {
            $("#NRIC_err").text("[required]").addClass('error');
            $("#NRIC").addClass('error');
            retVal = false;
        } else if(NRIC == "SNG_3") {
            if(NRIC_OTHER == ""){
                $("#NRIC_OTHER_err").text("[required]").addClass('error');
                $("#NRIC_OTHER").addClass('error');
                retVal = false;
            }else{
                $("#NRIC_OTHER_err").text("").removeClass('error');
                $("#NRIC_OTHER").removeClass('error');
            }
            if(NRIC_ID == ""){
                $("#NRIC_err").text("").removeClass('error');
                $("#NRIC").removeClass('error');                    
                $("#NRIC_ID_err").text("[required]").addClass('error');
                $("#NRIC_ID").addClass('error');
                retVal = false;
            }else if(nric_error_text != '[code exists!]'){
                $("#NRIC_ID_err").text("").removeClass('error');
                $("#NRIC_ID").removeClass('error');
            }
        } else if(NRIC_ID == "") {
            $("#NRIC_err").text("").removeClass('error');
            $("#NRIC").removeClass('error');
            $("#NRIC_ID_err").text("[required]").addClass('error');
            $("#NRIC_ID").addClass('error');
            retVal = false;
        } else if(nric_error_text != '[code exists!]' && nric_error_text != '[Invalid!]' ) {
            $("#NRIC_err").text("").removeClass('error');
            $("#NRIC").removeClass('error');
            $("#NRIC_ID_err").text("").removeClass('error');
            $("#NRIC_ID").removeClass('error');                    
        }
    }
    if (country_of_residence == "USA") {
        var SSN = $.trim($("#SSN").val());
        var ssn_error_text = $("#SSN_err").text();
        if (SSN == "") {
            $("#SSN_err").text("[required]").addClass('error');
            $("#SSN").addClass('error');
            retVal = false;
        } else if(ssn_error_text != '[code exists!]') {
            $("#SSN_err").text("").removeClass('error');
            $("#SSN").removeClass('error');
        }
    }
    
    var user_name = $.trim($("#user_name").val());
    if (user_name == "") {
        $("#user_name_err").text("[required]").addClass('error');
        $("#user_name").addClass('error');
        retVal = false;
    } else {
        if (valid_user_name(user_name) == false) {                    
            retVal = false;
            $("#user_name_err").text("[invalid]").addClass('error');
            $("#user_name").addClass('error');
        } 
    }
    
    var pers_first_name = $.trim($("#pers_first_name").val());
    if (pers_first_name == "") {
        $("#pers_first_name_err").text("[required]").addClass('error');
        $("#pers_first_name").addClass('error');
        retVal = false;
    } else if(valid_name(pers_first_name) == false) {
        $("#pers_first_name_err").text("[invalid]").addClass('error');
        $("#pers_first_name").addClass('error');
    } else {
        $("#pers_first_name_err").text("").removeClass('error');
        $("#pers_first_name").removeClass('error');
    }
    
    var pers_second_name = $.trim($("#pers_second_name").val());
    if(pers_second_name != ''){
        if(valid_name(pers_second_name) == false) {
            $("#pers_second_name_err").text("[invalid]").addClass('error');
            $("#pers_second_name").addClass('error');
        }else {
            $("#pers_second_name_err").text("").removeClass('error');
            $("#pers_second_name").removeClass('error');
        }
    }else {
        $("#pers_second_name_err").text("").removeClass('error');
        $("#pers_second_name").removeClass('error');
    }
    
    var nationality = $.trim($("#nationality").val());
    if (nationality == "") {
        $("#nationality_err").text("[required]").addClass('error');
        $("#nationality").addClass('error');
        retVal = false;
    } else {
        $("#nationality_err").text("").removeClass('error');
        $("#nationality").removeClass('error');
    }
            
    var pers_gender = $.trim($("#pers_gender option:selected").val());
    if (pers_gender == "") {
        $("#pers_gender_err").text("[required]").addClass('error');
        $("#pers_gender").addClass('error');
        retVal = false;
    } else {
        $("#pers_gender_err").text("").removeClass('error');
        $("#pers_gender").removeClass('error');
    }

    var pers_dob = $.trim($("#pers_dob").val());
    if(valid_date_field(pers_dob) == false && pers_dob != '') {
       $("#pers_dob_err").text("[dd-mm-yy format]").addClass('error');
       $("#pers_dob").removeClass('error');
        retVal = false;
    }
    else {
        $("#pers_dob_err").text("").removeClass('error');
        $("#pers_dob").removeClass('error');
    }

    var pers_contact_number = $.trim($("#pers_contact_number").val());
    if (pers_contact_number == "") {
        $("#pers_contact_number_err").text("[required]").addClass('error');
        $("#pers_contact_number").addClass('error');
        retVal = false;
    } else {
        if (valid_contact_number(pers_contact_number) == false) {
            $("#pers_contact_number_err").text("[invalid]").addClass('error');
            $("#pers_contact_number").addClass('error');
            retVal = false;
        } else {
            $("#pers_contact_number_err").text("").removeClass('error');
            $("#pers_contact_number").removeClass('error');
        }
    }

    var pers_alternate_contact_number = $.trim($("#pers_alternate_contact_number").val());
    if (pers_alternate_contact_number != "") {
        if (valid_contact_number(pers_alternate_contact_number) == false) {
            $("#pers_alternate_contact_number_err").text("[invalid]").addClass('error');
            $("#pers_alternate_contact_number").addClass('error');
            retVal = false;
        } else {
            $("#pers_alternate_contact_number_err").text("").removeClass('error');
            $("#pers_alternate_contact_number").removeClass('error');
        }
    } else {
            $("#pers_alternate_contact_number_err").text("").removeClass('error');
            $("#pers_alternate_contact_number").removeClass('error');
        }

    var user_registered_email = $.trim($("#user_registered_email").val());                     
    if (user_registered_email == "") {
        $("#user_registered_email_err").text("[required]").addClass('error');
        $("#user_registered_email").addClass('error');
        $("#pers_conf_email_err").text("[required]").addClass('error');
        $("#pers_conf_email").addClass('error');
        retVal = false;
    } else if (valid_email_address(user_registered_email) == false) {
        $("#user_registered_email_err").text("[invalid]").addClass('error');
        $("#user_registered_email").addClass('error');
        retVal = false;
    }else {
        $("#user_registered_email_err").text("").removeClass('error');
        $("#user_registered_email").removeClass('error');
        $("#pers_conf_email_err").text("").removeClass('error');
        $("#pers_conf_email").removeClass('error');
    }             

    var pers_conf_email = $.trim($("#pers_conf_email").val());
    if (pers_conf_email != user_registered_email) {
        if (valid_email_address(user_registered_email) == true) {
            if(pers_conf_email == '') {
                $("#pers_conf_email_err").text("[required]").addClass('error');
                $("#pers_conf_email").addClass('error'); 
            }else if(valid_email_address(pers_conf_email) == false) {
                $("#pers_conf_email_err").text("[invalid]").addClass('error');
                $("#pers_conf_email").addClass('error'); 
            }else {    
                $("#pers_conf_email_err").text("[Email does not match]").addClass('error');
                $("#pers_conf_email").addClass('error');
            }
            retVal = false;
        }
    } else {
        $("#pers_conf_email").removeClass('error');
        $("#pers_conf_email_err").text("").removeClass('error');
    }

    var pers_alt_email = $.trim($("#pers_alt_email").val());
    if (pers_alt_email != "") {
        if (valid_email_address(pers_alt_email) == false) {
            $("#pers_alt_email_err").text("[invalid]").addClass('error');
            $("#pers_alt_email").addClass('error');
            retVal = false;
        }else {
            $("#pers_alt_email").removeClass('error');
            $("#pers_alt_email_err").text("").removeClass('error');
        }
    }else {
        $("#pers_alt_email_err").text("").removeClass('error');
    }
    
    var highest_educ_level = $.trim($("#highest_educ_level").val());
    if (highest_educ_level == "") {
        $("#highest_educ_level_err").text("[required]").addClass('error');
        $("#highest_educ_level").addClass('error');
        retVal = false;
    } else {
        $("#highest_educ_level_err").text("").removeClass('error');
        $("#highest_educ_level").removeClass('error');
    }
    
    var occupation = $.trim($("#occupation").val());
    if (occupation == "") {
        $("#occupation_err").text("[required]").addClass('error');
        $("#occupation").addClass('error');
        retVal = false;
    } else {
        $("#occupation_err").text("").removeClass('error');
        $("#occupation").removeClass('error');
    }
    
    var pers_city = $.trim($("#pers_city").val());
    if(pers_city != ''){
        if(valid_name(pers_city) == false) {
            $("#pers_city_err").text("[invalid]").addClass('error');
            $("#pers_city").addClass('error');
        }else {
            $("#pers_city_err").text("").removeClass('error');
            $("#pers_city").removeClass('error');
        }
    }else {
        $("#pers_city_err").text("").removeClass('error');
        $("#pers_city").removeClass('error');
    }
    
    var pers_zipcode = $.trim($("#pers_zipcode").val());
    if(pers_zipcode != '') {
        if(valid_zip(pers_zipcode) == false) {
            $("#pers_zipcode_err").text("[invalid]").addClass('error');
            $("#pers_zipcode").addClass('error');
        }else {
            $("#pers_zipcode_err").text("").removeClass('error');
            $("#pers_zipcode").removeClass('error');
        }
    }else {
        $("#pers_zipcode_err").text("").removeClass('error');
        $("#pers_zipcode").removeClass('error');
    }      
    if($('#trainee_validation_div span').hasClass('error')) {                
        retVal = false;        
    }               
    return retVal;
}

 /*
* This function for triggering the validate
*/
$(document).ready(function() {
var check = 0;
$('#trainee_form').submit(function() {
     check = 1;
      return validate();
});
$('#trainee_form select,#trainee_form input').change(function() {
    if (check == 1) {
        return validate();
    }
});
}); 
/*
* This method for allowing number value only
*/
$('.number').keydown(function(event) {    
    if ($.inArray(event.keyCode, [46, 44, 8, 9, 27, 13, 32, 188]) !== -1 
        || ($.inArray(event.keyCode, [65, 67, 86]) !== -1 && event.ctrlKey === true) 
        || (event.keyCode >= 35 && event.keyCode <= 39)){
            return;
    }else {
        if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
            event.preventDefault(); 
        }   
    }
});
$(".float_number").keydown(function (e) {        
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 173]) !== -1 ||
        (e.keyCode == 65 && e.ctrlKey === true) || 
        (e.keyCode >= 35 && e.keyCode <= 39)) {
             return;
    }
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
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
$(".alphanumeric").keydown(function (e) {
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 32]) !== -1 ||
        (e.keyCode == 65 && e.ctrlKey === true) || 
        (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
    }
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 90) && (e.keyCode < 96 || e.keyCode > 105 ))) {
        e.preventDefault();
    }
});
$(document).ready(function() {
    $('#reset_form').click(function() {                
        $(".error").text("").removeClass('error');
        $('#trainee_form').each(function(){
            this.reset();
        });
        $('#country_of_residence').trigger('change');           
        $('#pers_states').find('option').remove().end().append('<option value="">Select</option>');
    });
});

function showimagepreview(input) {
    var ext = $('#userfile').val().split('.').pop().toLowerCase();
    if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
        $('#image_err').text('Invalid file!').addClass('error');
        $('#userfile').val('');
        $('#imgprvw').removeAttr('src');
        $('#removeimagebutton').hide();
        return false;
    }
    
    if (input.files && input.files[0]) {

        var size = input.files[0].size;
        if (size > 0) {
            var sizekb = size /1024;
        }else {
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
            $('#imgprvw').attr('width','120px');
            $('#imgprvw').attr('height','100px');
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
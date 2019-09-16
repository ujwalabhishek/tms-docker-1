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
// new addmore starts here
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
        //disabling unselected items from the last drop down           
        $("#" + e + "_firstcolumn_" + last_tr_id_number[2] + " > option:not(:selected)").attr('disabled', true);
        //removing the error.       
        $("#" + e + "_tbl_err").text("").removeClass('error');
        //adding new table row        
        tbl_row = '<tr id="' + e + '_row_' + cnt + '">' + $("#" + first_tr_id).html() + '</tr>';              
        tbl_row = tbl_row.replace(e + '_firstcolumn_' + first_tr_id_number[2], e + '_firstcolumn_' + cnt);
        tbl_row = tbl_row.replace("remove_row('" + e + "_remove_" + first_tr_id_number[2] + "')", "remove_row('" + e + "_remove_" + cnt + "')");
        tbl_row = tbl_row.replace(e + '_err' + first_tr_id_number[2], e + '_err' + cnt);
        tbl_row = tbl_row.replace('year_of_comp_' + first_tr_id_number[2], 'year_of_comp_' + cnt);
        tbl_row = tbl_row.replace('year_of_certi_' + first_tr_id_number[2], 'year_of_certi_' + cnt);
       // tbl_row = tbl_row.replace('yr_comp_err_' + first_tr_id_number[2], 'yr_comp_err_' +cnt);
        // for datepicker
        tbl_row = tbl_row.replace('validity_' + first_tr_id_number[2], 'validity_' + cnt);
        tbl_row = tbl_row.replace('class="hasDatepicker"', '');
        tbl_row = tbl_row.replace('empfrom_datetimepicker_' + first_tr_id_number[2], 'empfrom_datetimepicker_' + cnt);
        tbl_row = tbl_row.replace('class="hasDatepicker"', '');
        tbl_row = tbl_row.replace('empto_datetimepicker_' + first_tr_id_number[2], 'empto_datetimepicker_' + cnt);
        tbl_row = tbl_row.replace('class="hasDatepicker"', '');                
        // for datepicker end
        $('#' + e + '_tbl tr').last().after(tbl_row);
       // $('#' +  e + '_firstcolumn_' + cnt).val($('#'+ e + '_firstcolumn_' + cnt +' option:first').val()); 
 
        $('#' + e + '_row_' + cnt).find('input:text').val('');
        $('#' + e + '_row_' + cnt).find('textarea').val('');
        $('#' + e + '_row_' + cnt).find('select').val('');
        //disabling the selected items from drop down
        //disabling the selected items from drop down
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
    //for showing the remove button for multiple rows
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

   //for removing the remove button for single row
    if ($('#' + e + '_tbl tbody tr').size() == 2) {
        // removing disabled of first combobox starts.
        var first_tr_id = $('#' + e + '_tbl').closest('table').find(' tbody tr:first').attr('id');    
        var first_tr_id_number=first_tr_id.split('_');
        var id = 'edu_firstcolumn_'+first_tr_id_number[2];                
        $("#" + id + " option").attr('disabled',false);
        // removing disabled of first combobox ends.
        $('#' + e + '_tbl tbody tr td span').hide();
        //$('.remove2').hide();                
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
    //alert(selected_exec_array);
    //enabling all the remaining option to last drop down
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
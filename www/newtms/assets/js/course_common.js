/* 
 * This js file included in course page.
 */
$(function() {
    $('#language').multiselect({
        includeSelectAllOption: true
    });
    $('#pre_requisite').multiselect({        
        numberDisplayed:0,
        includeSelectAllOption: true,     
    });
    $('#course_manager').multiselect({
        includeSelectAllOption: true
    });
});
$(document).ready(function() {
    $(".float_number").keydown(function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $('.number').keydown(function(event) {
        if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9
                || event.keyCode == 27 || event.keyCode == 13
                || (event.keyCode == 65 && event.ctrlKey === true)
                || (event.keyCode >= 35 && event.keyCode <= 39)) {
            return;
        } else {
            if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {
                event.preventDefault();
            }
        }
    });

});
function valid_number(duration) {
    return /^\d+$/.test(duration.replace(/[\s]/g, '')); 
}

/* 
 * This js file included in copy course page.
 */
    $("#reason_copy_course").change(function(){
        var reason_for_deactivation = $("#reason_copy_course").val();        
        if(reason_for_deactivation=='OTHERS'){
            $("#other_reason").show();
        }
        else{
            $("#other_reason").hide();
        }
    });
       
    function isunique_course(e,id){         
        if(e ==''){
            $("#"+id+"_err").text("[required]").addClass('error');
            $("#"+id).addClass('error');
            return false;
        }else {
            $.ajax({
                url: "check_course_name",
                type: "post",
                data: 'course_name='+e,
                async: false,
                success: function(res){
                    if(res == 1) {
                        window.email_id = 'exists';                        
                        $("#"+id+"_err").text("[Couse Name exists!]").addClass('error');
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
   
    function valid_copy_reason(userName) {
        var pattern = new RegExp(/^[\sa-zA-Z0-9_,.-]+$/);        
        return pattern.test(userName);
    }
    
    function validate_copy_course(){        
        var retVal=true;
        var couse_name = $("#couse_name_copy").val();        
        var reason_copy_course = $("#reason_copy_course").val();
        var other_reason_copy_course = $("#other_reason_copy_course").val();        
        if(couse_name == "") {
            $("#couse_name_copy_err").text("[required]").addClass('error');
            $("#couse_name_copy").addClass('error');
            retVal = false;
        }
        if(reason_copy_course == "") {
            $("#reason_copy_course_err").text("[required]").addClass('error');
            $("#reason_copy_course").addClass('error');
            retVal = false;
        }else{
           $("#reason_copy_course_err").text("").removeClass('error'); 
           $("#reason_copy_course").removeClass('error');
        }
        
        if(reason_copy_course == "OTHERS") {            
            if(other_reason_copy_course == "") {
                $("#other_reason_copy_course_err").text("[required]").addClass('error');
                $("#other_reason_copy_course").addClass('error');
                retVal = false;
            }else{
                if(valid_copy_reason(other_reason_copy_course)==false)
                {
                    $("#other_reason_copy_course_err").text("[invalid]").addClass('error');
                    $("#other_reason_copy_course").addClass('error');
                    retVal = false;                    
                }else{
                    $("#other_reason_copy_course_err").text("").removeClass('error');
                    $("#other_reason_copy_course").removeClass('error');
                }
            }
        }else{
            $("#other_reason_copy_course_err").text("").removeClass('error');
        }
        isunique_course(couse_name,"couse_name_copy");
        if($('span').hasClass('error')){            
            retVal = false;
        }
        //////////////////////////////////added by shubhranshu to prevent multi click////////////////////////////////////////////////
        if(retVal){
            var self = $('#copy_course'),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
        }/////////////////////////////////////////////////////////////////////////////////
        return retVal;        
    }
    function validate_search() {
        var copy_course_name = $("#copy_course_name").val();        
        if(copy_course_name == "") {
            $("#copy_course_name_err").text("[required]").addClass('error');
            $("#copy_course_name").addClass('error');            
            return false;
        }else if(copy_course_name.indexOf('(') === -1) {
            $("#copy_course_name_err").text("[select course from autofill]").addClass('error');
            $("#copy_course_name").addClass('error');
            return false;
        }else {
            
            return true;
        }        
    }
     //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM////////////////////////////////////
    $('#search_form').on('submit',function() {
        if(validate_search()){
            var self = $(this),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            return true;
        }else{
            return false;
        }
    }); //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM///////////////////////////////
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
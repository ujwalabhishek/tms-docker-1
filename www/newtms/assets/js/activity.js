$(document).ready(function() {

    $module_id = $('#module_id').val();
//    alert($module_id);
    if($module_id == 1 || $module_id == 12 || $module_id == 13){ // internal staff
           
            $('.staff').show()// show internal staff list
            $('.company_list').hide();// hide company list
            $('#trainee_list').hide();// hide trainee list
            $('.course_list').hide(); // hide course list
            $('.class_course_list').hide(); // hide course list in class module
            $('.course_class_list').hide(); // hide class list
            $('.acc_type').hide(); // hide account type
            $('.invoice').hide();// hide account type in payment section
            $('.password').hide(); // hide password section
        }
        else if($module_id == 2){ // company
           
            $('.staff').hide()// show internal staff list
            $('.company_list').show();// show company list
            $('#trainee_list').hide();// hide trainee list
            $('.course_list').hide(); // hide course list
            $('.class_course_list').hide(); // hide course list in class module
            $('.course_class_list').hide(); // hide class list
            $('.acc_type').hide(); // hide account type
            $('.invoice').hide();// hide account type in payment section
            $('.password').hide(); // hide password section
        }
        else if($module_id == 3 || $module_id == 18){ // trainee
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list
            $('#trainee_list').show();// show trainee list
            $('.course_list').hide(); // hide course list
            $('.class_course_list').hide(); // hide course list in class module
            $('.course_class_list').hide(); // hide class list
            $('.acc_type').hide(); // hide account type
            $('.invoice').hide();// hide account type in payment section
            $('.password').hide(); // hide password section
        }else if($module_id == 4){ // course
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list
            $('#trainee_list').hide();// hide trainee list
            $('.course_list').show(); // show course list
            $('.class_course_list').hide(); // hide course list in class module
            $('.course_class_list').hide(); // hide class list
            $('.acc_type').hide(); // hide account type
            $('.invoice').hide();// hide account type in payment section
            $('.password').hide(); // hide password section
        }else if($module_id == 5){ // course
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list
            $('#trainee_list').hide();// hide trainee list
            $('.course_list').hide(); // hide course list
            $('.class_course_list').show(); // show course list in class module
            $('.course_class_list').hide(); // hide class list
            $('.acc_type').hide(); // hide account type
            $('.invoice').hide();// hide account type in payment section
            $('.password').hide(); // hide password section
        }else if($module_id == 6){ // course
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list
            $('#trainee_list').hide();// hide trainee list
            $('.course_list').hide(); // hide course list
            $('.class_course_list').show(); // show course list in class module
            $('.course_class_list').hide(); // hide class list
            $('.acc_type').hide(); // hide account type
            $('.invoice').hide();// hide account type in payment section
            $('.password').hide(); // hide password section
        }
        else if($module_id == 10 || $module_id == 17){ // chnage payment mode or discount
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list
            $('#trainee_list').hide();// hide trainee list
            $('.course_list').hide(); // hide course list
            $('.class_course_list').hide(); // show course list in class module
            $('.course_class_list').hide(); // hide class list
            $('.acc_type').show(); // show account type
            $('.invoice').hide();// hide account type in payment section
            $('.password').hide(); // hide password section
        }else if($module_id == 11){ // trainee list
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list
            $('#trainee_list').show();// hide trainee list
            $('.course_list').hide(); // hide course list
            $('.class_course_list').hide(); // show course list in class module
            $('.course_class_list').hide(); // hide class list
            $('.acc_type').hide(); // show account type
            $('.invoice').hide();// hide account type in payment section
            $('.password').hide(); // hide password section
        }else if($module_id == 14){ // course
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list
            $('#trainee_list').hide();// hide trainee list
            $('.course_list').hide(); // hide course list
            $('.class_course_list').hide(); // show course list in class module
            $('.course_class_list').hide(); // hide class list
            $('.acc_type').hide(); // hide account type
            $('.invoice').show(); // show invoice section
            $('.password').hide(); // hide password section
        }
        else if($module_id == 16){ // Reset password
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list
            $('#trainee_list').hide();// hide trainee list
            $('.course_list').hide(); // hide course list
            $('.class_course_list').hide(); // show course list in class module
            $('.course_class_list').hide(); // hide class list
            $('.acc_type').hide(); // hide account type
            $('.invoice').hide(); // hide password section
            $('.password').show(); // show password section
        }
        else{
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list
            $('#trainee_list').hide();// hide trainee list
            $('.course_list').hide(); // hide course list
            $('.class_course_list').hide(); // hide course list in class module
            $('.course_class_list').hide(); // hide class list   
            $('.acc_type').hide(); // hide account type
            $('.invoice').hide();// hide account type in payment section
            $('.password').hide(); // hide password section
        }
        
    $('#module_id').change(function() { 
        $internal_staff = $('#internal_staff').val('');
        $user_id = $('#user_id').val('');
        
        $account_type = $('#account_type').val('');
        
        $company = $('#company').val('');
        $com_id = $('#com_id').val('');
        
        $course = $('#course').val('');
        $crse_id = $('#crse_id').val('');
        
        $inv_taxcode = $('#inv_taxcode').val('');
        $invid = $('#invid').val('');
        
        $cls_name = $('#cls_name').val('');
        $cls_id = $('#cls_id').val('');

        $course_id = $('#course_id').val('');// when click on class module it blank the course id.
        
        $inv = $('#inv').val('');
        
        $module_id = $('#module_id').val();
        
        $ac_typ = $('#ac_typ').val('');
        
        $pass = $('#pass').val('');
        
        
        
         
        if($module_id == 1 || $module_id == 12 || $module_id == 13){ // internal staff
           
            $('.staff').show();// show internal staff list
            $('.company_list').hide();// hide company list
            $('#trainee_list').hide();// hide trainee list
            $('.course_list').hide(); // hide course list
            $('.class_course_list').hide(); // hide course list in class module
            $('.course_class_list').hide(); // hide class list
            $('.acc_type').hide(); // hide account type
            $('.invoice').hide();// hide account type in payment section
            $('.password').hide(); // hide password section
        }else if($module_id == 2){ // company
            $('.staff').hide();// hide internal staff list
            $('.company_list').show();// show company list
            $('#trainee_list').hide();// hide trainee list
            $('.course_list').hide(); // hide course list
            $('.class_course_list').hide(); // hide course list in class module
            $('.course_class_list').hide(); // hide class list
            $('.acc_type').hide(); // hide account type
            $('.invoice').hide();// hide account type in payment section
            $('.password').hide(); // hide password section
        }else if($module_id == 3 || $module_id == 18){ // trainee
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list
            $('#trainee_list').show();// show trainee list
            $('.course_list').hide(); // hide course list
            $('.class_course_list').hide(); // hide course list in class module
            $('.course_class_list').hide(); // hide class list
            $('.acc_type').hide(); // hide account type
            $('.invoice').hide();// hide account type in payment section
            $('.password').hide(); // hide password section
            
        }else if($module_id == 4){ // course
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list
            $('#trainee_list').hide();// hide trainee list
            $('.course_list').show(); // show course list
            $('.class_course_list').hide(); // hide course list in class module
            $('.course_class_list').hide(); // hide class list
            $('.acc_type').hide(); // hide account type
            $('.invoice').hide();// hide account type in payment section
            $('.password').hide(); // hide password section
        }else if($module_id == 5){ // course
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list
            $('#trainee_list').hide();// hide trainee list
            $('.course_list').hide(); // hide course list
            $('.class_course_list').show(); // show course list in class module
            $('.course_class_list').hide(); // hide class list
            $('.acc_type').hide(); // hide account type
            $('.invoice').hide();// hide account type in payment section
            $('.password').hide(); // hide password section
        }else if($module_id == 6){ // class
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list
            $('#trainee_list').hide();// hide trainee list
            $('.course_list').hide(); // hide course list
            $('.class_course_list').show(); // show course list in class module
            $('.course_class_list').hide(); // hide class list
            $('.acc_type').hide(); // hide account type
            $('.invoice').hide();// hide account type in payment section
            $('.password').hide(); // hide password section
        }else if($module_id == 10 || $module_id == 17){ // change payemnt mode or discount
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list          
            $('.course_list').hide(); // hide course list
            $('.class_course_list').hide(); // show course list in class module
            $('.course_class_list').hide(); // hide class list           
            $('.acc_type').show(); // show account type
            $('#trainee_list').hide();// hide trainee list 
            $('.invoice').hide();// hide account type in payment section
            $('.password').hide(); // hide password section
        }else if($module_id == 11){ // Trainer feedback
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list          
            $('.course_list').hide(); // hide course list
            $('.class_course_list').hide(); // show course list in class module
            $('.course_class_list').hide(); // hide class list           
            $('.acc_type').hide(); // show account type
            $('#trainee_list').show();// hide trainee list
            $('.invoice').hide();// hide account type in payment section
            $('.password').hide(); // hide password section
        }else if($module_id == 14 || $module_id == 15){ // Invoice
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list          
            $('.course_list').hide(); // hide course list
            $('.class_course_list').hide(); // show course list in class module
            $('.course_class_list').hide(); // hide class list           
            $('.acc_type').hide(); // show account type
            $('#trainee_list').hide();// hide trainee list
            $('.invoice').show();// hide account type in payment section
            $('.password').hide(); // hide password section
        }else if($module_id == 16){ // PASSWORD
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list          
            $('.course_list').hide(); // hide course list
            $('.class_course_list').hide(); // show course list in class module
            $('.course_class_list').hide(); // hide class list           
            $('.acc_type').hide(); // show account type
            $('#trainee_list').hide();// hide trainee list
            $('.invoice').hide();// hide account type in payment section
            $('.password').show(); // show password section
        }else{
            $('.staff').hide();// hide internal staff list
            $('.company_list').hide();// hide company list
            $('#trainee_list').hide();// hide trainee list
            $('.course_list').hide(); // hide course list
            $('.class_course_list').hide(); // hide course list in class module
            $('.course_class_list').hide(); // hide class list 
            $('.acc_type').hide(); // show account type
            $('#trainee_list').hide();// hide trainee list
            $('.invoice').hide(); // hide class list  
            $('.password').hide(); // hide password section
           
        }
        
    

    });
    
    $('.class_course_list #course_id').change(function() { 
        $('.course_class_list').show();
    });

/* it execute when we submit the form and url contain crs value start*/
    if( $('#course_id').val()!=''){
       $('.course_class_list').show();
    }
    /* end */
    
    /* change payment mode.. when select account type then its work skm start*/
    $('.acc_type #account_type').change(function(){
        if($('#account_type').val()==2){     
            $('#trainee_list').hide();
            $('.company_list').show();      
         }else if($('#account_type').val()==1){

             $('.company_list').hide();
             $('#trainee_list').show();
         }
//         else{ 
//             $('.company_list').hide();
//             $('#trainee_list').hide();
//         }
    });
   
     // $('.acc_type #account_type').change(function() { 
        if($('#account_type').val()==2){
        $('.company_list').show();  
        $('#trainee_list').hide();
        }else if($('#account_type').val()==1){
           $('#trainee_list').show();
           $('.company_list').hide();  
        }
//        else{
//            $('.company_list').hide();
//            $('#trainee_list').hide();
//        }
   // });
   
    
    
//     if( $('#account_type').val()==1){
//       $('#trainee_list').show();
//    }else {
//        $('.company_list').show();
//         $('#trainee_list').hide();
//    }
    /* skm end */

    

    $('.class_course_list #course_id').change(function() { 
        $('.course_class_list').show();
    });
    
    jQuery("#internal_staff").autocomplete({
            source: function(request, response) {
            $('#staff_id').val('');
            jQuery.get(baseurl + "reports_finance/get_internalstaff_name_autocomplete", {
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
        minLength: 4,
        });
        
        jQuery("#company").autocomplete({
            source: function(request, response) {
            $('#com_id').val('');
            jQuery.get(baseurl + "reports_finance/get_companyname_autocomplete", {
                query: request.term,
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
            });
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#com_id').val(id);
        },
        minLength: 4,
        });
        
        jQuery("#course").autocomplete({
            source: function(request, response) {
            $('#crse_id').val('');
            jQuery.get(baseurl + "reports_finance/get_coursename_autocomplete", {
                query: request.term,
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
            });
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#crse_id').val(id);
        },
        minLength: 4,
        });
        
        jQuery("#inv_taxcode").autocomplete({
            source: function(request, response) {
            $('#invid').val('');
            jQuery.get(baseurl + "reports_finance/get_invtaxcode_autocomplete", {
                query: request.term,
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
            });
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#invid').val(id);
        },
        minLength: 4,
        });
        
        jQuery("#inv").autocomplete({
            source: function(request, response) {
            $('#invoice').val('');
            jQuery.get(baseurl + "reports_finance/get_inv_autocomplete", {
                query: request.term,
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
            });
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#inv').val(id);
        },
        minLength: 4,
        });
        
        jQuery("#pass").autocomplete({
            source: function(request, response) {
//            $('#invoice').val('');
            jQuery.get(baseurl + "reports_finance/get_password_autocomplete", {
                query: request.term,
                dataType: "json"
            }, function(data) {
                var d = jQuery.parseJSON(data);
                response(d);
            });
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#pass').val(id);
        },
        minLength: 4,
        });
        
        
        $('#course_id').change(function() {
        $cls_id = $('#cls_id');
        $.ajax({
            type: 'post',
            url: $baseurl + 'reports_finance/get_course_class_name_json',
            data: {course_id: $('#course_id').val()},
            dataType: "json",
            beforeSend: function() {
                $cls_id.html('<option value="">All</option>');
            },
            success: function(res) {
                if (res != '') {
                    $cls_id.html('<option value="">All</option>');
//                    $cls_name.removeAttr('disabled');
                } else {
                    $cls_id.html('<option value="">All</option>');
//                    $cls_name.attr('disabled', 'disabled');
                }
                $.each(res, function(i, item) {
                    $cls_id.append('<option value="' + item.key + '">' + item.value + '</option>');
                });
            }
        });
    });
        
        
    
//    $("#cls_name").autocomplete({
//        source: function(request, response) 
//        {
//            course_id = $("#course_id").val();
//            $.ajax({
//                type: "GET",
//                url: baseurl + "activity_log/get_course_class_name_autocomplete",
//                dataType: "json",
//                data: {name_startsWith: request.term, course_id: course_id},
//                contentType: "application/json; charset=utf-8",
//                success: function(data) 
//                {                  
//                    response($.map(data, function(item) {
//                        return {
//                            //label: item.label + '(' + item.key + ')',
//                            label: item.label,
//                        }
//                    }));
//                }
//            });
//        },
//        select: function(event, ui) {
//            $('#cls_name').val(ui.item.label);
//            return false;
//        }
//         //minLength: 0
//    });
    

    
});


function disp_err($id, $text) {
    $text = typeof $text !== 'undefined' ? $text : '[required]';
    $($id).addClass('error');
    $($id + '_err').addClass('error').html($text);
}


   

 
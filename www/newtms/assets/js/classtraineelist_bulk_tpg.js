/** 
 * This js file included in class trainee list page
 */
$(document).ready(function() {
    
    $("#input_from_date").datepicker({
        dateFormat: CLIENT_DATE_FORMAT,
        onSelect: function (newDate, obj) {
            $('#input_to_date').datepicker('option', 'minDate', $.datepicker.parseDate(CLIENT_DATE_FORMAT, newDate));
        }
    });

    $("#input_to_date").datepicker({
        dateFormat: CLIENT_DATE_FORMAT
    });
    
    $("#input_enrol_date").datepicker({
        dateFormat: CLIENT_DATE_FORMAT
    });
    
    $('.search_select').change(function() {
        $('#taxcode').val('');
        $('#taxcode_id').val('');
        $('#trainee').val('');
        $('#trainee_id').val('');
        $('#taxcode').attr('disabled', 'disabled');
        $('#trainee').attr('disabled', 'disabled');
        $val = $('.search_select:checked').val();
        if ($val == 1) {
            $div = $('#taxcode');
        } else {
            $div = $('#trainee');
        }
        $div.removeAttr('disabled');
    });
    var form_check = 0;
    //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM////////////////////////////////////
    $('#search_form').on('submit',function() {
        form_check = 1;
        //alert("form click");
        var status=form_validate(true);//alert(status);
        if(status){
        var self = $(this),
        button = self.find('input[type="submit"],button'),
        submitValue = button.data('submit-value');
        button.attr('disabled','disabled').html('Please Wait..');
        return true;
       }else{
           return false;
       }
    }); //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM///////////////////////
//    $('#search_form').submit(function() {
//        form_check = 1;
//        return form_validate(true);
//    });
    $('#search_form input, #search_form select').change(function() {
        if (form_check == 1) {
            return form_validate(false);
        }
    })
    
    //////////////////////////////////////Abdulla fixed to prevent multiple clicks 14/11/2018 AT 3:45PM////////////////////////////////////
    $('#tpg_form').on('submit',function() {                        
        
        var len = $('input[name="chk[]"]:checked').length;
        
        if(len === 0) {
            $('#bulk_upload_err').addClass('error').text('Please, select atleast one trainee to submit to TPG.');
            return false;                                    
        } else {
            var self = $(this),
            button = self.find('input[type="submit"],button'),
            submitValue = button.data('submit-value');
            button.attr('disabled','disabled').html('Please Wait..');
            
            return true;
        }                        
    }); //////////////////////////////////////Abdulla fixed to prevent multiple clicks 14/11/2018 AT 3:45PM///////////////////////
    
    
    $('.subsidy_save').click(function() {
        $retVal = true;
        $tg_number = $('#tg_number').val();
        if ($tg_number.length == 0 || parseFloat($tg_number) == 0) {
            disp_err('#tg_number');
            $retVal = false;
        } else {
            remove_err('#tg_number');
        }
        if ($retVal == true) {
            trigger_ajax();
        }
    });
    $('.eid_save').click(function() {
        $retVal = true;
        $eid_number = $('#eid_number').val();
        if ($eid_number.length == 0 || parseFloat($eid_number) == 0) {
            disp_err('#eid_number');
            $retVal = false;
        } else {
            remove_err('#eid_number');
        }
        if ($retVal == true) {
            eid_trigger_ajax();
        }
    });
    /////////////////////added by shubhranshu for search validate on 26/11/2018///////////////////////////
    function exportValidate(){
        if(form_validates()){
            return true;
        }else{
            return false;
        }
    }

     ///////////added by shubhranshu for validation
    function form_validates() {
            var $retVal = true;
            var crse = $('#course').val();
            var comp = $('#company_name').val();
//            if ($('.search_select:checked').val() == 1)  {         
//                var taxcode_val = $.trim($("#taxcode").val());
//    
//                if (taxcode_val == "") {
//                    $('#taxcode_err').text("[Select NRIC from autofill-help]").addClass('error');
//                    return false;
//                } 
//                $('#taxcode_err').removeClass('error').text('');
//            }
//            if ($('.search_select:checked').val() == 2) { 
//               var trainee_val = $.trim($("#trainee").val());
// 
//                if (trainee_val == "") {
//                    $('#trainee_err').text("[Select trainee from autofill-help]").addClass('error');
//                    return false;
//                }
//                $('#trainee').removeClass('error').text('');
//            }  
            $comp=$('#search_select:checked').val(); //alert($comp); 
            if(crse == '' && comp == '' && $('.search_select:checked').val() != ''){
                
                $('#company_name_err').addClass('error').text('Oops!Please select atleast one filter to perform search operation');
                return false;
            }else{
                $('#company_name_err').removeClass('error').text('');
            }
//            if ($comp==undefined || empty($comp)) { 
//                if($('#company_name').val()==""){
//                     $('#company_name_err').text("[Select Company from autofill-help]").addClass('error');
//                     return false;
//                     
//                }else{
//                     $('#company_name_err').removeClass('error').text('');
//                }
//       
//            }          
            return $retVal;
        }
    /////////////////////added by shubhranshu for search validate on 26/11/2018///////////////////////////
    
    function form_validate($retval) {
        var crse = $('#course').val();
        var cls = $('#class').val();
        
        if(crse == '' && ($('.search_select:checked').val() == undefined)){                
            $('#company_name_err').addClass('error').text('Please, select filter to perform search operation.');
            $retval = false;
        } else if(cls == '') {
            $('#company_name_err').addClass('error').text('Please, select class to display the data.');
            $retval = false;
        } else {
                $('#company_name_err').removeClass('error').text('');
        }
        //check_remove_id();////added by shubhranshu to removed the ids if field remove
        return $retval;
    }
    /////////////added by shubhranshu///////////////////////
    function check_remove_id(){
        $comp = $('#company_name').val();
        $taxcode = $('#taxcode').val();
        $trainee_name = $('#trainee').val();
        if($comp == ''){
           $('#company_id').val(''); 
        }
        if($taxcode == ''){
           $('#taxcode_id').val(''); 
        }
        if($trainee_name == ''){
           $('#trainee_id').val(''); 
        }
    }/////////////////////////////////////////////////////////////////////////////////////
    function trigger_ajax() {
        $subsidy_amount = $('#subsidy_amount').val();
        $subsidy_date = $('#subsidy_date').val();
        $tg_number = $('#tg_number').val();
        $class = $('#h_class').val();
        $user = $('#h_user').val();
        $.ajax({
            url: $baseurl + 'class_trainee/update_tgnumber',
            type: 'post',
            data: {class: $class, user: $user, tg_number: $tg_number},
            success: function(i) {
                if (i != '') {
                    label_alert = false;
                    alert('TG# updated successfully!');
                    $('#subsidy_amount').val('');
                    $('#subsidy_per').val('');
                    $('#tg_number').val();
                    $('#subsidy_date').val();
                    $('#h_class').val();
                    $('#h_user').val();
                    $('.close-modal').trigger('click');
                } else {
                    alert('Unable to Update TG#.');
                    return false;
                }
            }
        });
    }
    
    function eid_trigger_ajax() {
        $eid_number = $('#eid_number').val();
        $class = $('#eid_class').val();
        $user = $('#eid_user').val();
        $.ajax({
            url: $baseurl + 'class_trainee/update_eidnumber',
            type: 'post',
            data: {class: $class, user: $user, eid_number: $eid_number},
            success: function(i) {
                if (i != '') {
                    label_alert = false;
                    alert('EID# updated successfully!');
                    $('#eid_number').val();
                    $('#eid_class').val();
                    $('#eid_user').val();
                    $('.close-modal').trigger('click');
                } else {
                    alert('Unable to Update EID#.');
                    return false;
                }
            }
        });
    }
    $('.get_update').click(function() {
        $this = $(this);
        $class = $this.data('class');
        $user = $this.data('user');
        $.ajax({
            url: $baseurl + 'class_trainee/get_subsidy_tg_data',
            type: 'post',
            data: {class: $class, user: $user},
            async: false,
            dataType: 'json',
            success: function(i) {
                if (i != '') {
                    $('#tg_number').val(i.tg_number);
                } else {
                    $('#tg_number').val('');
                }
            }
        });
        $('#h_user').val($user);
        $('#h_class').val($class);
        $('#ex9').modal();
    });
    $('.get_update_eid').click(function() {
        $this = $(this);
        $class = $this.data('class');
        $user = $this.data('user');
        $.ajax({
            url: $baseurl + 'class_trainee/get_eid_data',
            type: 'post',
            data: {class: $class, user: $user},
            async: false,
            dataType: 'json',
            success: function(i) {
                if (i != '') {
                    $('#eid_number').val(i);
                } else {
                    $('#eid_number').val('');
                }
            }
        });
        $('#eid_user').val($user);
        $('#eid_class').val($class);
        $('#exeid').modal();
    });
    $('#subsidy_amount').change(function() {
        $subsidy = $(this).val();
        $.ajax({
            url: $baseurl + 'class_trainee/calculate_gst_get_class_for_subsidy',
            type: 'post',
            data: {subsidy: $subsidy, class: $('#h_class').val(), user: $('#h_user').val(), },
            dataType: 'json',
            success: function(i) {
                if (i.label != '') {
                    label_alert = false;
                    $('#subsidy_amount').val('');
                    $('#subsidy_per').val('');
                    alert(i.label);
                } else {
                    $('.net_due').html(i.amount);
                    $('#subsidy_per').val(i.subsidy_per);
                    $('.subsidy_amount').val(parseFloat($subsidy).toFixed(2));
                    $amount_check = i.amount;

                }
            }
        });
    })

    $('#subsidy_per').change(function() {
        $subsidy = $(this).val();
        $.ajax({
            url: $baseurl + 'class_trainee/calculate_gst_get_class_for_subsidy_pers',
            type: 'post',
            data: {subsidy_per: $subsidy, class: $('#h_class').val(), user: $('#h_user').val(), },
            dataType: 'json',
            success: function(i) {
                if (i.label != '') {
                    label_alert = false;
                    alert(i.label);
                    $('#subsidy_amount').val('');
                    $('#subsidy_per').val('');
                } else {
                    $('.net_due').html(i.amount);
                    $('#subsidy_amount').val(i.subsidy);
                    $('.subsidy_amount').html(i.subsidy);
                    $amount_check = i.amount;
                }
            }
        });
    });
    $("#subsidy_per,#subsidy_amount").keydown(function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    $("#subsidy_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "+0:+100",
        onClose: function() {
            $(this).trigger("change");
        }
    });
    $('.paid_href').click(function() {
        $class = $(this).data('class');
        $user = $(this).data('user');
        $.ajax({
            type: 'post',
            url: $baseurl + 'class_trainee/get_payment_class_user',
            dataType: 'json',
            data: {class: $class, user: $user},
            success: function(res) {
                $('.r_recd_on').html(res.invoice.recd_on);
                $('.r_mode').html(res.invoice.mode_of_pymnt);
                $('.othr_mode').html(res.invoice.othr_mode_of_payment);// added by shubhranshu for other payment mode
                if(res.data.sfc_claim_id !=''){
                   $('.sfc_claim_id').html(res.data.sfc_claim_id);// added by shubhranshu for sfc claim id
                   $('#sfc_claim_id_tr').show();
                }else{
                    $('.sfc_claim_id').html("");
                    $('#sfc_claim_id_tr').hide();
                }
                
                $('.r_class_fees').html(parseFloat(res.data.class_fees).toFixed(2))
                $('.r_net_due').html(parseFloat(res.invoice.amount_recd).toFixed(2));
                $('.r_dis_label').html(res.data.discount_label);
                $('.r_dis_rate').html(parseFloat(res.data.discount_rate).toFixed(2));
                $('.r_dis_amount').html(parseFloat(res.data.total_inv_discnt).toFixed(2));
                $('.r_subsidy_amount').html(parseFloat(res.data.total_inv_subsdy).toFixed(2));
                $('.r_after_gst').html(parseFloat(res.data.after_gst).toFixed(2));
                $('.r_gst_rate').html(parseFloat(res.data.gst_rate).toFixed(2));
                $('.r_gst_label').html(res.data.gst_label);
                $('.r_total_gst').html(parseFloat(res.data.total_gst).toFixed(2));
                $('.payment_recd_href').attr('href', $baseurl + 'class_trainee/export_payment_received/' + res.data.pymnt_due_id);
                $('#ex8').modal();
            }
        });
    });
    $('#course').change(function() {
        $class = $('#class');
        $.ajax({
            type: 'post',
            url: $baseurl + 'classes/get_course_classes_json',
            data: {course_id: $('#course').val()},
            dataType: "json",
            beforeSend: function() {
                $class.html('<option value="">Select</option>');
            },
            success: function(res) {
                if (res != '') {
                    $class.html('<option value="">All</option>');
                    $class.removeAttr('disabled');
                } else {
                    $class.html('<option value="">Select</option>');
                    $class.attr('disabled', 'disabled');
                }
                $.each(res, function(i, item) {
                    $class.append('<option value="' + item.key + '">' + item.value + '</option>');
                });
            }
        });
    });
    $('#tg_number').keyup(function() {
        $(this).val($(this).val().toUpperCase());
    });
    $("#taxcode").autocomplete({
        source: function(request, response) {
            $('#taxcode_id').val('');
            if (request.term.trim().length > 0) {
                $.ajax({
                    url: $siteurl + "class_trainee/get_alltaxcode_with_courseclass",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term,
                        course: $('#course').val(),
                        class: $('#class').val()
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            } else {
                var d;
                response(d);
            }
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#taxcode_id').val(id);
        },
        minLength: 4
    });
    $("#trainee").autocomplete({source: function(request, response) {
            $('#trainee_id').val('');
            if (request.term.trim().length > 3) {
                $.ajax({
                    url: $siteurl + "class_trainee/get_alltrainee_with_courseclass",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term,
                        course: $('#course').val(),
                        class: $('#class').val()
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            } else {
                var d;
                response(d);
            }
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#trainee_id').val(id);
        },
        minLength: 0
    });
    $("#company_name").autocomplete({
        source: function(request, response) {
            $('#company_id').val('');
            if (request.term.trim().length > 3) {
                $.ajax({
                    url: $siteurl + "class_trainee/get_company_json",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            } else {
                var d;
                response(d);
            }
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#company_id').val(id);
        },
        minLength:0
    });
    $("#eidbox").autocomplete({
        source: function(request, response) {
            $('#eid').val('');
            if (request.term.trim().length > 3) {
                $.ajax({
                    url: $siteurl + "class_trainee/get_eid_json",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            } else {
                var d;
                response(d);
            }
        },
        select: function(event, ui) {
            var id = ui.item.key;
            $('#eid').val(ui.item.value);
        },
        minLength:0
    });
    
    $('#taxcode').attr('disabled', 'disabled');
    $('#trainee').attr('disabled', 'disabled');
    $val = $('.search_select:checked').val();
    if ($val == 1) {
        $div = $('#taxcode');
    } else {
        $div = $('#trainee');
    }
    $div.removeAttr('disabled');
    function reset_form() {
        $('#collected_on').val('');
        $('#new_entrance').val('');
        $('#COMMNTS').text('');  
        $('#trainer_feedback_form').find('input:radio').prop('checked', false);
       // $('#trainer_feedback_form #COMYTCOM_C').attr('checked', 'checked');
     //   $('#trainer_feedback_form #COMYTCOM_ABS').prop('disabled',true);
        $("#satisfaction_rating option:selected").removeAttr("selected");
        $("#traineefeedbackForm option:selected").removeAttr("selected");
        $('#traineefeedbackForm #remarks').text(''); 
    }
    $("#collected_on").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50"        
    });
    $("#new_entrance").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "+0:+100",
        minDate: 0
    });
     $('.training_update').click(function() {
        var $course = $(this).data('course');
        var $class =  $(this).data('class');
        var $user =   $(this).data('user');
        var $payment= $(this).data('payment');
        
        
        var ajax_image = "<img src='assets/images/q.gif' alt='Loading...' />";
        $('#tbl').hide();
        $('#trainee_fdbk').hide();
        $('#skm').show();
        $('#ssp').show();
        $('#skm').html(ajax_image);
        $('#ssp').html(ajax_image);
        reset_form();
        $('#traineefeedbackForm').attr("action", baseurl + "class_trainee/trainee_feedback/" + $user + "/" + $course + "/" + $class);
        $('#trainer_feedback_form').attr("action", baseurl + "class_trainee/trainer_feedback/" + $user + "/" + $course + "/" + $class);
        $.ajax({
            type: 'post',
            url: baseurl + 'trainee/get_trainer_feedback',
            dataType: 'json',
            data: {course: $course, class: $class, user: $user,payment:$payment},
            success: function(res) {     
               
                var trainer = res.trainer;
                var trainee = res.trainee;
                var lock_status= res.class_lock;
                
                $.each(trainer, function(i, item) {                    
                    if (item.feedback_question_id == 'CERTCOLDT')
                        $('#collected_on').val(item.feedback_answer);
                    if (item.feedback_question_id == 'DTCOMMEMP')
                        $('#new_entrance').val(item.feedback_answer);
                    if (item.feedback_question_id == 'FSCORE')
                        $('#feedback_score option[value="' + item.feedback_answer + '"]').attr("selected", "selected");
                    if (item.feedback_question_id == 'FGRADE')
                        $('#feedback_grade option[value="' + item.feedback_answer + '"]').attr("selected", "selected");
                    if (item.feedback_question_id == 'SATSRATE')
                        $('#satisfaction_rating option[value="' + item.feedback_answer + '"]').attr("selected", "selected");
                    if (item.feedback_question_id == 'COMMNTS')
                        $('#COMMNTS').text(item.feedback_answer);                    
                    if (item.feedback_question_id == 'APPKNLSKL') {
                        if (item.feedback_answer == 'Y') {
                            $('#APPKNLSKL_YES').prop('checked', true);
                        } else {
                            $('#APPKNLSKL_NO').prop('checked', true);
                        }
                    }
                    if (item.feedback_question_id == 'CERTCOM1') {
                        if (item.feedback_answer == 'Y') {
                            $('#CERTCOM1_YES').prop('checked', true);
                        } else {
                            $('#CERTCOM1_NO').prop('checked', true);
                        }
                    }
                    if (item.feedback_question_id == 'EXPJOBSCP') {
                        if (item.feedback_answer == 'Y') {
                            $('#EXPJOBSCP_YES').prop('checked', true);
                        } else {
                            $('#EXPJOBSCP_NO').prop('checked', true);
                        }
                    }
                    if (item.feedback_question_id == 'RT3MNTHS') {
                        if (item.feedback_answer == 'Y') {
                            $('#RT3MNTHS_YES').prop('checked', true);
                        } else {
                            $('#RT3MNTHS_NO').prop('checked', true);
                        }
                    }
                   
                    if(item.training_score == 'C')
                    {
                        $('#COMYTCOM_C').prop('disabled', false);
                        $('#COMYTCOM_NYC').prop('disabled', false);     
                        $('#COMYTCOM_EX').prop('disabled', false);
                        $('#COMYTCOM_2NYC').prop('disabled', false);
                        $('#COMYTCOM_ABS').prop('disabled', true);
                         $('#skm').hide();
                         $('#tbl').show();
                          $('#ssp').hide();
                        $('#trainee_fdbk').show();
                      
                    }
                    if(item.training_score == 'ABS')
                    {
                      
                        $('#COMYTCOM_C').prop('disabled', true);
                        $('#COMYTCOM_ABS').prop('disabled', false);     
                        $('#COMYTCOM_EX').prop('disabled', true);
                        $('#COMYTCOM_NYC').prop('disabled', true);
                        $('#COMYTCOM_2NYC').prop('disabled', true);
                        $('#skm').hide();
                        $('#tbl').show();
                         $('#ssp').hide();
                        $('#trainee_fdbk').show();
                    }
                    
                    if(item.training_score == 'ATR')
                    {
                      
                        $('#COMYTCOM_C').prop('disabled', true);
                        $('#COMYTCOM_ABS').prop('disabled', false);     
                        $('#COMYTCOM_EX').prop('disabled', true);
                        $('#COMYTCOM_NYC').prop('disabled', true);
                        $('#COMYTCOM_2NYC').prop('disabled', true);
                         $('#COMYTCOM_ATTRITION').prop('disabled', false);
                        $('#skm').hide();
                        $('#tbl').show();
                        $('#ssp').hide();
                        $('#trainee_fdbk').show();
                    }
                    
                    if (item.training_score == null){
                        
                        $('#COMYTCOM_C').prop('disabled', false);
                        $('#COMYTCOM_ABS').prop('disabled', false);     
                        $('#COMYTCOM_EX').prop('disabled', false);
                        $('#COMYTCOM_NYC').prop('disabled', false);
                        $('#COMYTCOM_2NYC').prop('disabled', false);
                        $('#COMYTCOM_ATTRITION').prop('disabled', false);
                        $('#skm').hide();
                        $('#tbl').show();
                         $('#ssp').hide();
                        $('#trainee_fdbk').show();
                    }
                  
                    if (item.feedback_question_id == 'COMYTCOM') {
                        $('#COMYTCOM_C').prop('disabled', false);
                        $('#COMYTCOM_NYC').prop('disabled', false);     
                        $('#COMYTCOM_EX').prop('disabled', false);
                        $('#COMYTCOM_2NYC').prop('disabled', false);
                        $('#COMYTCOM_ABS').prop('disabled', true);
                        $('#COMYTCOM_ATTRITION').prop('disabled', true);
                        if (item.feedback_answer == 'C') {
                            $('#COMYTCOM_C').prop('checked', true);
                        } else if(item.feedback_answer == 'NYC'){
                            $('#COMYTCOM_NYC').prop('checked', true);                        
                        }else if(item.feedback_answer == 'EX'){
                            $('#COMYTCOM_EX').prop('checked', true);
                            
                        }else if(item.feedback_answer == 'ABS'){
                            $('#COMYTCOM_ABS').prop('checked', true);
                        }else if(item.feedback_answer == '2NYC') {
                            $('#COMYTCOM_2NYC').prop('checked', true);
                        }else if(item.feedback_answer == 'ATR') {
                            $('#COMYTCOM_ATTRITION').prop('checked', true);
                        }     
                         $('#skm').hide();
                         $('#tbl').show();
                          $('#ssp').hide();
                        $('#trainee_fdbk').show();
                    }
                    
                    ///////below code was added by shubhranshu for xp for attrition option start-----
                    if(item.training_score == "NYC" || item.training_score == "C" || item.training_score == "2NYC"){
                        $('#COMYTCOM_ATTRITION').prop('disabled', true);
                    }else{
                        if((res.att_percentage <= 0.50) && (res.att_percentage != null) && (res.att_percentage >= 0)){
                            $('#COMYTCOM_ATTRITION').prop('disabled', false);
                            $('#COMYTCOM_C').prop('disabled', true);
                            $('#COMYTCOM_NYC').prop('disabled', true);     
                            $('#COMYTCOM_EX').prop('disabled', true);
                            $('#COMYTCOM_2NYC').prop('disabled', true);
                            $('#COMYTCOM_ABS').prop('disabled', false);
                        }else if((res.att_percentage < 0.75) && (res.att_percentage != null) && (res.att_percentage >= 0)){
                            $('#COMYTCOM_ATTRITION').prop('disabled', true);
                            $('#COMYTCOM_C').prop('disabled', true);
                            $('#COMYTCOM_NYC').prop('disabled', true);     
                            $('#COMYTCOM_EX').prop('disabled', true);
                            $('#COMYTCOM_2NYC').prop('disabled', true);
                            $('#COMYTCOM_ABS').prop('disabled', false);
                        }else if(res.att_percentage == null){
                            $('#COMYTCOM_ATTRITION').prop('disabled', false);
                            $('#COMYTCOM_C').prop('disabled', true);
                            $('#COMYTCOM_NYC').prop('disabled', true);     
                            $('#COMYTCOM_EX').prop('disabled', true);
                            $('#COMYTCOM_2NYC').prop('disabled', true);
                            $('#COMYTCOM_ABS').prop('disabled', false);
                        }else{
                            $('#COMYTCOM_ATTRITION').prop('disabled', true);
                        }
                    }
                    ////below code was added by shubhranshu for xp for attrition option end-----
                   
                });
                $('#rating').val('');
                $.each(trainee, function(i, item) {
                    //$('#rating option[value="' + item.trainee_feedback_rating + '"]').attr("selected", "selected"); 
                    $('#rating').val(item.trainee_feedback_rating);
                    $('#'+item.feedback_question_id+' option[value="' + item.feedback_answer + '"]').attr("selected", "selected");
                    $('#remarks').text(item.other_remarks_trainee);
                });
                $.each(lock_status, function(i,item){
                        if(item.lock_status==1){
                           //   $('#lock_att').prop('disabled',true);
                            $('#COMYTCOM_C').prop('disabled', true);
                            $('#COMYTCOM_ABS').prop('disabled', true);     
                            $('#COMYTCOM_EX').prop('disabled', true);
                            $('#COMYTCOM_NYC').prop('disabled', true);
                            $('#COMYTCOM_2NYC').prop('disabled', true);
                            $('#new_entrance').prop('disabled', true);
                            $('#satisfaction_rating').prop('disabled', true);
                            $('#COMMNTS').prop('disabled', false); 
                            $('#APPKNLSKL_YES').prop('disabled', true);
                            $('#APPKNLSKL_NO').prop('disabled', true);
                            $('#CERTCOM1_YES').prop('disabled', true);
                            $('#CERTCOM1_NO').prop('disabled', true);
                            $('#EXPJOBSCP_YES').prop('disabled', true);
                            $('#EXPJOBSCP_NO').prop('disabled', true);
                            $('#RT3MNTHS_YES').prop('disabled', true);
                            $('#RT3MNTHS_NO').prop('disabled', true);
                            $('.attendance_lock').show();
                        }
                        else{
                            $('#lock_att').prop('disabled',false);
                            $('.attendance_lock').hide();
                        }
                });
                
            }
        });
    });
});
function disp_err($id, $text) {
    $text = typeof $text !== 'undefined' ? $text : '[required]';
    $($id).addClass('error');
    $($id + '_err').addClass('error').addClass('error_text').html($text);
}
function remove_err($id) {
    $($id).removeClass('error');
    $($id + '_err').removeClass('error').text('');
}

/* 
 * This js file includes in invoice_list form
 */
$(document).ready(function() {
    $("#start_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
        onSelect: function(selectedStartDate) {
            $("#end_date").datepicker("option", {
                minDate: selectedStartDate,
                maxDate: '',
            });
        },
    });
    $("#end_date").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
        minDate: 0,
        maxDate: -1
    });
    if ($('#start_date').val().length > 0) {
        $("#end_date").datepicker("option", {
            minDate: $('#start_date').val(),
            maxDate: '',
        });
    }
    $('.company_pdf').click(function() {
        $inv = $(this).data('invoice');
        $pdi = $(this).data('pdi');
        $('#payment_due_id').val($pdi);
        $('#company_invoice_id').val($inv);
        $('#ex13').modal();
    });
    $('.print_company_invoice').click(function() {
        remove_err('#without_subsidy');
        remove_err('#with_subsidy');
        $val = $('.select_invoice_print:checked').val();
        $pymnt_due_id = $('#payment_due_id').val();
        $company_invoice_id = $('#company_invoice_id').val();
        if ($val == 1) {
            location.replace($baseurl + 'class_trainee/export_company_generate_invoice/' + $pymnt_due_id);
        } else if ($val == 2 || $val == 3) {
            $count = 0;
            $subsidy = ($val == 2) ? 1 : 0;
            $.ajax({
                url: $baseurl + "class_trainee/get_count_company_invoice_no_subsidy",
                type: "post",
                async: false,
                data: {
                    invoice: $company_invoice_id,
                    subsidy: $subsidy
                },
                success: function(res) {
                    $count = res;
                }
            });
            if ($count == 0) {
                if ($val == 3) {
                    disp_err('#without_subsidy', '[No trainees found in this invoice without subsidy.]');
                } else {
                    disp_err('#with_subsidy', '[No trainees found in this invoice with subsidy.]');
                }
            } else {
                location.replace($baseurl + 'class_trainee/gen_inv_pdf_basedon_subsidy/' + $company_invoice_id + '/' + $subsidy);
            }
        }
        return false;
    });
    $('.print_new_invoice').click(function() {
       
        $inv = $(this).data('invoice');
        $pdi = $(this).data('pdi');
        ///added by shubhranshu to fix the current invoice issue
        location.replace($baseurl + 'class_trainee/export_current_invoice_new/' + $pdi +'/' + $inv);
       // alert($pdi);
    /*    $.ajax({
                url: $baseurl + "class_trainee/get_count_invoice",
                type: "post",
                async: false,
                data: {
                    pay_id: $pdi
                },
                success: function(res) {
                    $count = res;
                }
            });*/
//            if($count>0){
//                location.replace($baseurl + 'class_trainee/export_company_generate_invoice/' + $pdi);
//            }else{
//                 location.replace($baseurl + 'class_trainee/export_old_generate_invoice/' + $pdi +'/' + $inv);
//            }
        
       
       
    });
     $('.print_old_invoice').click(function() {
       
        $inv = $(this).data('old_invoice_id');
        $pdi = $(this).data('pdi');
       // alert($pdi);
//        location.replace($baseurl + 'class_trainee/export_old_generate_invoice/' + $pdi +'/' + $inv);
        ///added by shubhranshu to fix the previous invoice issue
       location.replace($baseurl + 'class_trainee/export_previous_generate_invoice/' + $pdi +'/' + $inv);
       
    });
     
    $("#company_name").autocomplete({
        source: function(request, response) {
            $('#company_id').val('');
            if (request.term.trim().length > 0) {
                $.ajax({
                    url: $siteurl + "reports_finance/get_company_json",
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
            validate(false);
        },
        minLength: 4
    });
	
	// skm reg invoice start
	$("#invoice_name").autocomplete({
        source: function(request, response) {
            $('#invoice_id').val('');
            if (request.term.trim().length > 0) {
                $.ajax({
                    url: $siteurl + "reports_finance/get_invoice_json1",
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
           
            $('#invoice_id').val(id);
            validate(false);
        },
        minLength: 5
    });
    
    // skm reg invoice end
    
    // skm prev invoice start
	$("#prev_invoice_name").autocomplete({
        source: function(request, response) {
            $('#prev_invoice_id').val('');
            if (request.term.trim().length > 0) {
                $.ajax({
                    url: $siteurl + "reports_finance/get_prev_invoice_json",
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
           
            $('#prev_invoice_id').val(id);
            validate(false);
        },
        minLength: 5
    });
    
    // skm prev invoice end
    
var search_check = 0;
    $('#invoicelistform').submit(function() {
        search_check = 1;
        ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
        if(validate(true)){
           var self = $(this),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            return true;
        }else{
            return false;
        } ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
    });
    $('#invoicelistform input').change(function() {
        if (search_check == 1) {
            return validate(false);
        }
    });
    function validate(retval) {
        var company_name = $('#company_name').val().trim();
        var company_id = $('#company_id').val();
        var invoice_name = $('#invoice_name').val();
        var prev_invoice_name = $('#prev_invoice_name').val();
        var end_date = $('#end_date').val();
        var start_date = $('#start_date').val();
        if (company_name == "" && invoice_name == "" && prev_invoice_name == "" && (start_date == "" || end_date== "")) {
            disp_err('#valid','Oops! Please Choose atleast one field & Write 4 characters to search');
            retval = false;
        } else {
           remove_err('#valid');
        }
        if (company_name.length > 0 && company_id.length == 0) {
            disp_err('#company_name', 'Select from auto-help');
            retval = false;
        } else {
            remove_err('#company_name');
        }
        check_remove_id();/////added by shubhranshu
        return retval;
    }
    /////////////added by shubhranshu///////////////////////
    function check_remove_id(){
        
        $invoice = $('#invoice_name').val();
        $prev_invoice= $('#prev_invoice_name').val();
        $company= $('#company_name').val();
        
        if($invoice == ''){
           $('#invoice_id').val(''); 
        }
        if($prev_invoice == ''){
           $('#prev_invoice_id').val(''); 
        }
        if($company == ''){
           $('#company_id').val(''); 
        }
    }/////////////////////////////////////////////////////////////////////////////////////
    
    
    $('.search_select').change(function() {
        $('#invoice_name').val('');
        $('#invoice_id').val('');
        $('#prev_invoice_name').val('');
        $('#prev_invoice_id').val('');
        $('#invoice_name').attr('disabled', 'disabled');
        $('#prev_invoice_name').attr('disabled', 'disabled');
        $val = $('.search_select:checked').val();
        if ($val == 1) {
            $div = $('#invoice_name');
        } else {
            $div = $('#prev_invoice_name');
        }
        $div.removeAttr('disabled');
    });
    $('#invoice_name').attr('disabled', 'disabled');
    $('#prev_invoice_name').attr('disabled', 'disabled');
    $val = $('.search_select:checked').val();
    if ($val == 1) {
        $div = $('#invoice_name');
    } else {
        $div = $('#prev_invoice_name');
    }
    $div.removeAttr('disabled');

		


});
function disp_err($id, $text) {
    $text = typeof $text !== 'undefined' ? $text : '[required]';
    $($id).addClass('error');
    $($id + '_err').addClass('error').text($text);
}
function remove_err($id) {
    $($id).removeClass('error');
    $($id + '_err').removeClass('error').text('');
}
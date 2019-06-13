/**
 * This js file includes in settings page
 */
function change_paypal() {    
    var paypal_email_id = $.trim($('#paypal_email_id').val());        
    if(valid_email_address(paypal_email_id)== true && paypal_email_id!=''){            
            $('#paypal_img').css('display','');
            $('#paypal_email_id_err').text('').removeClass('error');
            $.ajax({
                    type: 'post',
                    url: baseurl + '/settings/update_paypal_email_id',
                    data: {paypal_email_id: paypal_email_id},                    
                    success: function(res) {                                                 
                        $('#paypal_img').css('display','none');
                    }
                });
        }else{
            $('#paypal_email_id_err').text('[invalid]').addClass('error');
        }
        
}
function change_invoice(){
    var invoice_name = $.trim($('#invoice_name').val());     
    if(invoice_name != ""){
        $('#invoice_img').css('display','');
        $('#invoice_name_err').text('').removeClass('error');
        $.ajax({
                type: 'post',
                url: baseurl + '/settings/update_invoice_name',
                data: {invoice_name: invoice_name},                    
                success: function(res) {                                                                       
                    $('#invoice_img').css('display','none');
                }
            });
    }else{
        $('#invoice_name_err').text('[required]').addClass('error');
    }     
}
function change_invoice_footer_text(){
    var invoice_footer_text = $.trim($('#invoice_footer_text').val());     
    if(invoice_footer_text != ""){
        $('#invoice_footer_text_img').css('display','');
        $('#invoice_footer_text_err').text('').removeClass('error');
        $.ajax({
                type: 'post',
                url: baseurl + '/settings/update_invoice_footer_text',
                data: {invoice_footer_text: invoice_footer_text},                    
                success: function(res) {                                                                       
                    $('#invoice_footer_text_img').css('display','none');
                }
            });
    }else{
        $('#invoice_footer_text_err').text('[required]').addClass('error');
    }     
}
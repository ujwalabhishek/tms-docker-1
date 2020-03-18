
<style>
    .comp_block{
        text-align: center;
        background: #c6e6f7;
        height: 119px;
        width: 525px;
        font-weight: bold;
        margin: auto;
        margin-top: 5px;
        color: #1b2dad;
        font-size: 20px;
        padding: 34px;
        border-radius: 5px;
        display:none;
    }
</style>
<div class="col-md-10">
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/internal_user.png"/>&nbsp; SEARCH COMPANY NAME BY INVOICE ID</h2>
    <h2 class="sub_panel_heading_style"><img src="<?php echo $baseurl; ?>/assets/images/education.png"> Search Company Name :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id='comp_name'></span></h2> 

    <div class="table-responsive" id='data_hides'>    
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">                               
                        Enter Invoice ID To Search: 
                    </td>
                    <td>

                        <?php
                        $invoice = array(
                            'name' => 'invoice_no',
                            'id' => 'invoice_no',
                            //'value' => $this->input->get('invoice_no'),
                            'style' => 'width:200px;',
                            'class' => 'upper_case',
                            'placeholder' => 'Enter Invoice ID',
                            'autocomplete' => 'off'
                        );

                        echo form_input($invoice);
                        ?>
                        <span class='invoice_no_err'></span>
                        <button type="button" class="comp_search_button1 btn btn-xs btn-primary no-mar">
                            <span class="glyphicon glyphicon-search"></span>
                            Search
                        </button>
                    </td>
                    
                </tr>
            </tbody>
        </table>
    </div> 
     <div class="table-responsive">    
         <div class='text-center comp_head' style='font-size: 20px;
    color: #696666;
    padding-top: 10px;display:none;'>Company Name</div>
         <div class='comp_block'></div>
          <div class='comp_status' style='font-size: 15px;
    color: grey;
    padding-top: 10px;display:none;text-align: center;'></div>
     </div>
</div>
<script>
    // added by shubhranshu
$(document).ready(function() {
       $(".comp_search_button1").click(function () {
            $status = true;
            if ($('#invoice_no').val() == ''){
                $status = false;
                $('.invoice_no_err').html('required');
                $('.invoice_no_err').css('color', 'red');
            } else{
                
                 $('.invoice_no_err').html('');
            }
            
            if ($status){
                $(".comp_block").slideUp("slow"); 
                $(".comp_head").slideUp("slow");
                $('.comp_block').html('');
                $(".comp_status").html();
                $(".comp_status").slideUp("slow");
                $('.comp_search_button1').attr('disabled', 'disabled').html('Please Wait..');
                $.ajax({
                url: 'tms_report_search_company_name',
                    type: "post",
                    dataType: "json",
                    data: {
                    invoice_no: $("#invoice_no").val()
                    },
                    success: function(data) {
                        $('.comp_block').html(data.company_name);
                        $(".comp_block").slideDown("slow");
                        $(".comp_status").slideDown("slow");
                        $(".comp_head").slideDown("slow");
                        $(".comp_status").html(data.status);
                        $('.comp_search_button1').removeAttr('disabled').html('<span class="glyphicon glyphicon-search"></span> Search');
                    }
                });
            } else{
                return false;
            }
        });
        
//        $('#collapse_datas').click(function(){
//            $('#data_hides').toggle(500);
//        });
        
    });
    </script>
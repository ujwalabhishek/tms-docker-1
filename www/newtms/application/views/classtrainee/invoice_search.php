<?php
$this->load->helper('form');
$this->load->helper('metavalues_helper');
$this->load->helper('common_helper');
$this->load->model('meta_values');
?>
<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<div class="col-md-10">
    <?php
    if ($this->session->flashdata('success_message')) {
        echo '<div class="success">' . $this->session->flashdata('success_message') . '!</div>';
    }
    if ($this->session->flashdata('error_message')) {
        echo '<div class="error1">' . $this->session->flashdata('error_message') . '!</div>';
    }
    ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/trainee.png"> Accounting - Search Invoice</h2>
    <div class="table-responsive">
        <?php
        $attr = 'id="search_form" name="search_form" method="GET"';
        echo form_open("accounting/invoice_search", $attr);
        ?>
        <table class="table table-striped">      
            <tbody>    
                <tr>
                    <td class="td_heading" style="width:22%;">Search by Invoice Number:</td>	          
                    <td>
                        <?php
                        $fn = array(
                            'name' => 'invoice_number',
                            'id' => 'invoice_number',
                            'value' => $this->input->get('invoice_number'),
                            'style' => 'width: 90%'
                        );
                        echo form_input($fn);
                        ?>
                        <span id="credit_note_number_err"></span>  
                        <input type="hidden" name="invoice_no" id="invoice_no" value="">
                        <input type="hidden" name="payment_due_id" id="payment_due_id" value="">
                        <input type="hidden" name="inv_type" id="inv_type" value="">
                    </td>
                    <td align="center">              
                        <button title="Search" value="Search" type="submit" class="btn btn-xs btn-primary no-mar">
                            <span class="glyphicon glyphicon-search"></span> Search
                        </button>
                    </td>
                </tr>        
            </tbody>
        </table>
<?php echo form_close(); ?>
    </div>
    <div class="bs-example" style="margin-top:25px">
        <div class="table-responsive">
            <div class="add_button space_style">                            
                <div class="add_button">
                    <?php if (count($tabledata) > 0): ?>
                        <!--<a href="<?php echo site_url('/accounting/export_credit_note' . $export_url) ?>"  class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to XLS</span></a> &nbsp;&nbsp;-->
                        <!--<a href="<?php echo site_url('/accounting/pdf_credit_note') . '?' . $_SERVER['QUERY_STRING']; ?>"  class="small_text1">-->
                    <!--<span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to PDF</span></a>-->                
<?php endif; ?>  
                    <!--<a href="<?php echo site_url('/accounting/add_credit_note') ?>"  class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Add New</span></a>-->
                </div>                
            </div>    
            <div style="clear:both;"></div>
            <table id="testTable" class="table table-striped">
                <thead>
                    <?php
                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                    $pageurl = $controllerurl;
                    ?>
                    <tr>
                        <th width="10%" class="th_header td_center_align"><a style="color:#000000;">NRIC NO #</a></th>
                        <th width="15%" class="th_header td_center_align"><a style="color:#000000;">NAME.</a></th>
                        <th width="10%" class="th_header td_center_align"><a style="color:#000000;">ENROLL TYPE. #</a></th>
                        <th width="10%" class="th_header td_center_align"><a style="color:#000000;" >COMPANY NAME. #</a></th>
                        <th width="10%" class="th_header td_center_align"><a style="color:#000000;" >INVOICE NO.</a></th>    
                        <th width="10%" class="th_header td_center_align"><a style="color:#000000;" >INVOICE AMOUNT.</a></th>  
                        <th width="10%" class="th_header td_center_align"><a style="color:#000000;" >PAYMENT STATUS.</a></th> 
                        <!--<th width="15%" class="th_header td_center_align"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=crn.credit_note_amount&o=" . $ancher; ?>" >Amt(SGD)</a></th>-->                
                        <!--<th width="20%" class="th_header td_center_align"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=crn.credit_note_issued_by&o=" . $ancher; ?>" >Issued By</a></th>-->                
                        <!--<th width="30%" class="th_header td_center_align"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=crn.tg_ref_number&o=" . $ancher; ?>" >TG Ref. #</a></th>-->                
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($tabledata) > 0) {
                        foreach ($tabledata as $data) {
                            ?>
                            <tr>
                                <td class="td_center_align"><?php echo $data->tax_code; ?></td>
                                <td class="td_center_align"><?php echo $data->first_name; ?></td>
                                <td class="td_center_align"><?php  if(($data->inv_type)=='INVINDV'){echo 'INDIVIDUAL';}else{echo 'Company';} ?></td>
                                <td class="td_center_align"> <?php echo $data->company_name; ?></td>
                                <td class="td_center_align"> <?php echo $data->invoice_id; ?></td>
                                <td class="td_center_align"> <?php echo $data->total_inv_amount; ?></td>
                                <td class="td_center_align"> <?php echo $data->payment_status; ?></td>
                                <!--<td class="td_center_align"><?php echo $data->credit_note_issued_by; ?></td>-->
                                <!--<td class="td_center_align"><?php echo $data->tg_ref_number; ?></td>-->
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>Search to see the invoice details.</label></td></tr>";
                    }
                    ?>        
                </tbody>     
            </table>          
        </div>
        <div style="clear:both;"></div><br>
        <ul class="pagination pagination_style">            
            <?php
            echo $pagination;
            ?>
        </ul>
    </div>
</div>
<script>
    $("#invoice_number").autocomplete({
        source: function(request, response) {
            //$('#invoice_number').val('');
            if (request.term.trim().length > 0) {
                $.ajax({
                    url: $siteurl + "accounting/search_invoice",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term,
                        paid: $('#payment_status').val(),
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            }else{
                var d;
                response(d);
            }
        },
        select: function(event, ui) {
            var id1 = ui.item.label;
            var id = ui.item.key;
            var id2 = ui.item.pymtdue;
            var id3 = ui.item.invtype;
            $('#invoice_number').val(id1);
            $('#invoice_no').val(id);
             $('#payment_due_id').val(id2);
             $('#inv_type').val(id3);
            //remove_all_err('#search_form');
        },
        minLength:4
    });
    
    $("#search_form").submit(function(){
        $srcno=$("#invoice_number").val();
       if( $srcno !== ""){
           var self = $(this),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            $("#credit_note_number_err").text("");
            return true;
        }else{
            $("#credit_note_number_err").text("[required]");
            $("#credit_note_number_err").css("color","red");
            return false;
        }
    });
</script>    
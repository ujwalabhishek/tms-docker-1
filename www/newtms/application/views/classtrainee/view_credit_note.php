<div class="col-md-10">           
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/internal_user.png"/>  Credit Notes</h2>
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/personal_details.png"/> Credit Note Details</h2>
    <div class="table-responsive">
        <table class="table table-striped">    
            <tbody>                
                <tr>
                    <td class="td_heading">Credit Note #:<span class="required">*</span></td>
                    <td width="29%">
                        <span><?php echo $tabledata->credit_note_number; ?></span>               
                    </td>
                    <td width="20%" class="td_heading">Credit Note Date:<span class="required">*</span></td>
                    <td width="30%">
                        <span><?php echo date('d-m-Y',strtotime($tabledata->credit_note_date)); ?></span>                             
                    </td>
                </tr>        
                <tr>
                    <td class="td_heading">Original Invoice #:<span class="required">*</span></td>
                    <td>
                        <span><?php echo $tabledata->ori_invoice_number; ?></span>                                                            
                    </td>
                    <td class="td_heading">Original Invoice Date:<span class="required">*</span></td>
                    <td>
                        <span><?php echo date('d-m-Y',strtotime($tabledata->ori_invoice_date)); ?></span>                             
                    </td>
                </tr>          
                <tr>          
                    <td class="td_heading">Credit Note Amount:<span class="required">*</span></td>
                    <td>
                        <span>$ <?php echo number_format($tabledata->credit_note_amount,2); ?></span>                              
                    </td>
                    <td class="td_heading">Credit Note Issued By:</td>
                    <td>
                        <span><?php echo $tabledata->credit_note_issued_by; ?></span>                               
                    </td>
                </tr>                                       
                <tr>        
                    <td class="td_heading">Credit Note Issued Reason:</td>
                    <td colspan="3" class="textarea_inp">
                        <div class="table-responsive payment_scroll" style="height: 87px;min-height: 87px;">
                            <span><?php echo $tabledata->credit_note_issue_reason; ?></span>
                        </div>
                    </td>      
                </tr>
                <tr>        
                    <td class="td_heading">TG Ref #:</td>
                    <td colspan="3" class="textarea_inp">
                        <div class="table-responsive payment_scroll" style="height: 87px;min-height: 87px;">
                            <span><?php echo $tabledata->tg_ref_number; ?></span>
                        </div>
                    </td>      
                </tr>
            </tbody>
        </table>
    </div>            
    <br>
    <div class="button_class99">
        <a class="small_text white_color" href="<?php echo base_url().'accounting/credit_note'?>">
            <button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-saved"></span>&nbsp;Back</button>
        </a> &nbsp; &nbsp;       
    </div>    
</div>

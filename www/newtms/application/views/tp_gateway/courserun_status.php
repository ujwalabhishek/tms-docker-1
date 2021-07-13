 <div class="col-md-10">
     <?php
    if ($this->session->flashdata('success')) {
        echo '<div class="success">' . $this->session->flashdata('success') . '</div>';
    }
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    ?> 
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/course.png"> Course Run Status</h2>   
    
    <h2 class="sub_panel_heading_style">COURSE</h2>
    <table class="table table-striped">
        <tbody>
            <tr>
                <td class="td_heading" width="20%">Reference Number:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $refno; ?></label>
                   
                </td>
            </tr>
            <tr>
                <td class="td_heading">Course Title:<span class="required">*</span></td>
                <td>
                    <label class="label_font"></label><?php echo $course_title; ?>
                </td>
            </tr>
            <tr>
                <td class="td_heading">External Reference Number:<span class="required">*</span></td>
                <td>
                    <label class="label_font"></label><?php echo $exrefno; ?>
                </td>
            </tr>
        </tbody>
    </table> 
    
    <h2 class="sub_panel_heading_style">RUN</h2>
    
     <table class="table table-striped">
        <tbody>
            <tr>
                <td width="20%" class="td_heading">Course Run ID:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $run->id;?></label>
              
                <td colspan="2"> <label class="label_font"></label>
                    <div style='color:grey'>(This ID was given by TPG After Successful Addition of Course Run)</div>
                </td>
            </tr>

            <tr>
                <td class="td_heading">Attendance Taken:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $run->attendanceTaken? 'True': 'False'; ?></label>
                <td colspan="2"> <label class="label_font"></label>
                    <div style='color:grey'>indicate whether the attendance taken is done for the course run</div>
                </td>
            </tr>
            
             <tr>
                <td width="20%" class="td_heading">QR-Code Link:<span class="required">*</span></td>
                <td colspan='3'><label class="label_font"><?php echo $run->qrCodeLink;?></label>
            </tr>
            

        </tbody>
    </table>
    
    <h2 class="sub_panel_heading_style">SUPPORT</h2>
    <?php foreach($support as $sup){?>
    <table class="table table-striped">
        <tbody>
            <tr width="20%">                        
                <td class="td_heading" width="25%">Category:<span class="required">*</span></td>
                <td width='25%'><label class="label_font"><?php echo $sup->category; ?></label></td>
                <td class="td_heading" width='25%'>Details ID:<span class="required">*</span></td>
                <td width='25%'>
                    <?php echo $sup->detailsId; ?>
                    <div class='comment'>The unique ID in MySF for course support</div>
                </td>
            </tr>

            <tr>                        
                <td class="td_heading">Effective Date:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $sup->effectiveDate; ?></label></td>
                <td class="td_heading">Support Code:<span class="required">*</span></td>
                <td><label class="label_font"></label><?php echo $sup->supportCode; ?></td>
            </tr>

            <tr>                        
                <td class="td_heading">Created By:<span class="required">*</span></td>
                <td><label class="svenue_floor"></label><?php echo $sup->meta->createBy;?></td>
                <td class="td_heading">Created date:<span class="required">*</span></td>
                <td><label class="svenue_unit"></label><?php echo $sup->meta->createDate;?></td>
            </tr>

            <tr>                        
                <td class="td_heading">Updated By:<span class="required">*</span></td>
                <td><label class="svenue_postalcode"></label><?php echo $sup->meta->updateBy;?></td>
                <td class="td_heading">Updated Date:<span class="required">*</span></td>
                <td><label class="svenue_room"></label><?php echo $sup->meta->updateDate;?></td>
            </tr>
            
            <tr>                        
                <td class="td_heading">From Period:<span class="required">*</span></td>
                <td><label class="svenue_postalcode"></label><?php echo $sup->period->from;?></td>
                <td class="td_heading">To Period:<span class="required">*</span></td>
                <td><label class="svenue_room"></label><?php echo $sup->period->to;?></td>
            </tr>
            
            <tr>                        
                <td class="td_heading">Tagging code:<span class="required">*</span></td>
                <td><label class="svenue_postalcode"></label><?php echo $sup->period->taggingCode;?></td>
                <td class="td_heading">Tagging Description:<span class="required">*</span></td>
                <td><label class="svenue_room"></label><?php echo $sup->period->taggingDescription;?></td>
            </tr>
        </tbody>
    </table>
    <hr class='hr'>
    <?php }?>
    
</div>
<style>
    
    .comment{
      color:grey;
    }
    .hr{
      background: #a0bdf1;
      height: 5px;
    }
</style>
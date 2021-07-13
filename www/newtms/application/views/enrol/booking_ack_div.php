<div id="ack-div" style="margin:0 auto; width: 815px;">
    <div  style="float: left;min-height:400px; height: auto; padding: 4px;background: #f2f2f2; border: 1px solid #c3cde0; border-radius: 4px;">
        <div style="float:left; width:100%">
            <div style="float:left;width:600px;text-align: center;padding:40px;font-size:17px "><b>Booking Acknowledgement</b></div>
            <div style="float:right;">
                <img src="<?php echo base_url() . 'logos/' . $tenant_det->Logo; ?>" style="float: right;">
                <p align='right' style='clear:both'><?php echo $tenant_det->tenant_name; ?><br>
                    <?php echo $tenant_det->tenant_address; ?><br>
                 <?php echo $tenant_det->tenant_country; ?><br/>
                    Email: <?php echo $tenant_det->tenant_email_id; ?><br>
                    Phone: <?php echo $tenant_det->tenant_contact_num; ?><br>
                </p>
                <br>
            </div>
        </div>
        <div style="float:left; width:100%;">
            <strong>Booking No: </strong><?php echo $book_no; ?><br>
            <strong>Booking Date: </strong><?php echo $book_date; ?><br><br>
        </div>
        <div style="float: left;width: 100%"> 
            <?php echo $message; ?><br>
            <br><strong>Class Start Date:</strong> <?php echo $start_date; ?>  <br>
            <br><strong>Location:</strong>  <?php echo $loc; ?><br>
            <br><strong>Contact Details:</strong> <?php echo $contact; ?>
              <br> <?php echo $message3; ?>
            <span class="pull-right" style="margin-right: 10px;">
                <a href="<?php echo base_url() . 'course_public/booking_acknowledge_pdf/' . $trainee_id . '/' . $class_id ?>">
                    <button type="button" class="btn btn-sm btn-info" style="float: right;"><strong>Print</strong></button>
                </a>
            </span>
            <br>
        </div>
    </div>
</div>
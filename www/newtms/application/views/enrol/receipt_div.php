<div id="book_receipt" style="margin:0 auto; width: 815px;">
    <div  style="float: left;min-height:400px; height: auto; padding: 4px;background: #f2f2f2; border: 1px solid #c3cde0; border-radius: 4px;">
    <div style="float:left; width:100%">
        <div style="float:left;width:600px;text-align: center;padding:40px;font-size:17px "> <b>Payment Receipt</b></div>
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
    <br>
    <table width="100%">      
        <tbody>
            <tr>
                <td><b>Receipt No: </b><?php echo $book_no; ?></td>
            </tr>
            <tr>
                <td><b>Receipt Date:</b><?php echo $book_date; ?></td>
            </tr>
            <tr>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table><br>
    <?php echo $message2; ?><br>
    <table class="table table-bordered" style="width:80%">
        <tbody>
            <tr>
                <td class="td_heading" width="30%">Class Start Date:</td>
                <td width="70%"><?php echo $start_date; ?></td>
            </tr>
            <tr>
                <td class="td_heading">Location:</td>
                <td><?php echo $loc; ?></td>
            </tr>
            <tr>
                <td class="td_heading">Contact Details:</td>
                <td><?php echo $contact; ?></td>
            </tr>
        </tbody>
    </table>
    <br>        <span style="float:right;margin-right:10px;">
        <a href="<?php echo base_url() . 'course_public/payment_receipt_pdf/' . $trainee_id . '/' . $class_id ?>">
            <button type="button"  class="btn btn-sm btn-info" style="float: right;"><strong>Print</strong></button>
        </a>
    </span>
</div>
</div>
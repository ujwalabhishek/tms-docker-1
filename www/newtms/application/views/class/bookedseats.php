<?php
$CI = & get_instance();
$CI->load->model('course_model');
$CI->load->model('class_model');
$CI->load->model('company_model');
$this->load->helper('common_helper');
?>
<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bookedseats.js"></script>
<div class="col-md-10">
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/booked.png"> Booked Seats</h2>
    <?php if (array_key_exists('EXP_XLS', $this->data['left_side_menu']['CLSS'])) { ?>
        <div class="pull-right space_style" style="margin-top: 1px;"><a href="<?php echo base_url() . 'classes/export_booked_seats/' . $class['class_id'] . '?f=' . $sort_field . '&o=' . $sort_order; ?>" class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to XLS</span></a></div>
    <?php } ?>
    <div class="clearfix"></div>
    <div class="table-responsive">
        <strong>Course:</strong> <?php echo $course->crse_name; ?>&nbsp;&nbsp; 
        <strong> Class:</strong> <?php echo $class['class_name']; ?> &nbsp;&nbsp;
        <strong>Start Date:</strong>  <?php echo date('M d Y', strtotime($class['class_start_datetime'])); ?> &nbsp;&nbsp;
        <strong>End Date:</strong> <?php echo date('M d Y', strtotime($class['class_end_datetime'])); ?> &nbsp;&nbsp;
        <strong>Total Seats:</strong><?php echo $class['total_seats']; ?> &nbsp;&nbsp;
        <strong>Total Booked: </strong><?php echo $total_booked; ?> &nbsp;&nbsp;

        <?php if ($sales_total_booked) { ?>
            <span style="color: blue;"><strong>Your Booking: </strong><?php echo $sales_total_booked; ?></span>
        <?php }
        ?>
        <input class='payment_due_id' id='payment_due_id' type='hidden'>
        <input class='company_invoice_id' id='company_invoice_id' type='hidden'>
        <table class="table table-striped">
            <thead>
                <?php
                $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                $pageurl = $controllerurl;
                if (!empty($tabledata)) {
                ?>
                <tr>
                    <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=usrs.country_of_residence&o=" . $ancher; ?>" >Nationality</a></th>
                    <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=usrs.tax_code&o=" . $ancher; ?>" >Tax Code</a></th>
                    <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=prs.first_name&o=" . $ancher; ?>" >Trainee Name</a></th>
                    <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=enrol.enrolment_mode&o=" . $ancher; ?>" >Enrollment Mode</a></th>
                    <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=enrol.enrolled_on&o=" . $ancher; ?>" >Enrollment Date</a></th>
                    <th class="th_header">Contact Details</th>
                    <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=enrol.payment_status&o=" . $ancher; ?>" >Fee Paid</a></th>
                </tr>
                <?php
                }
                ?>
            </thead>
            <tbody>
                <?php
                if (!empty($tabledata)) {                    
                    $role_array = array("TRAINER","COMPACT");
                    foreach ($tabledata as $data) {
                        if ($data->enrolment_mode == 'COMPSPON') {
                            $company = $CI->company_model->get_company_details($tenant_id, $data->company_id);
                            if ($data->company_id[0] == "T") {
                                $tenant_details = fetch_tenant_details($tenant_id);
                                $company[0]->company_name = $tenant_details->tenant_name;
                            }
                            $enrol_mode = 'Company (' . $company[0]->company_name . ')';
                            if ($data->payment_status == 'PAID') {
                                $paid_td = '<td valign="top"><a href="javascript:;" class="company_paid" data-pdi="' . $data->pymnt_due_id . '" data-invoice="' . $data->invoice_id . '">Paid</a></td>';
                            } else if ($data->payment_status == 'PARTPAID' && in_array($this->session->userdata('userDetails')->role_id,$role_array)) {
                                $paid_td = '<td valign="top"><span class="red">Part Paid</span></td>';
                            } else if($data->payment_status == 'PARTPAID') { 
                                $paid_td = '<td valign="top"><a href="' . base_url() . 'accounting/update_payment"><span class="red">Part Paid</span></a></td>';                                
                            }else if ($data->payment_status == 'PYNOTREQD') {
                                $paid_td = '<td valign="top"><span class="red">Payment Not Reqd.</span></td>';
                            } else if(in_array($this->session->userdata('userDetails')->role_id,$role_array)){                                
                                $paid_td = '<td valign="top"><span class="red">Not Paid</span></td>';
                            } else {
                                $paid_td = '<td valign="top"><a href="' . base_url() . 'accounting/update_payment"><span class="red">Not Paid</span></a></td>';
                            }
                        } else {
                            $enrol_mode = 'Individual';
                            if ($data->payment_status == 'PAID') {
                                $paid_td = '<td valign="top"><a href="#ex8" rel="modal:open" class="small_text1 open_paid" data-user="' . $data->user_id . '" data-class="' . $class['class_id'] . '">Paid</a></td>';
                            } else if ($data->payment_status == 'PYNOTREQD') {
                                $paid_td = '<td valign="top"><span class="red">Payment Not Reqd.</span></td>';
                            } else if(in_array($this->session->userdata('userDetails')->role_id,$role_array)){                                
                                $paid_td = '<td valign="top"><span class="red">Not Paid</span></td>';
                            } else {
                                $paid_td = '<td valign="top"><a href="' . base_url() . 'accounting/update_payment"><span class="red">Not Paid</span></a></td>';
                            }
                        }
                        $contact_number = !empty($data->contact_number) ? 'Contact Number: ' . $data->contact_number . ', ' : '';
                        $email = !empty($data->registered_email_id) ? 'Email Id: ' . $data->registered_email_id . ', ' : '';
                        $address = '';
                        $pac = '';
                        if (!empty($data->personal_address_country)) {
                            $pac = get_catname_by_parm($data->personal_address_country);
                        }
                        $pas = '';
                        if (!empty($data->personal_address_state)) {
                            $pas = get_catname_by_parm($data->personal_address_state);
                        }
                        if (!empty($data->personal_address_bldg)) {
                            $address .=$data->personal_address_bldg . ', ';
                        }
                        if (!empty($data->personal_address_city)) {
                            $address .=$data->personal_address_city . ', ';
                        }
                        if (!empty($pas)) {
                            $address .=$pas . ', ';
                        }
                        if (!empty($pac)) {
                            $address .=$pac . ', ';
                        }
                        if (!empty($data->personal_address_zip)) {
                            $address .='Zip: ' . $data->personal_address_zip;
                        }
                        if (!empty($address)) {
                            $address = 'Address: ' . $address;
                        }
                        $contact = rtrim($contact_number . $email . $address, ', ');
                        echo '<tr>
                                <td valign="top">' . rtrim($CI->course_model->get_metadata_on_parameter_id($data->country_of_residence), ', ') . '</td>
                                <td valign="top">' . $data->tax_code . '</td>
                                <td valign="top">' . $data->first_name . ' ' . $data->last_name . '</td>
                                <td valign="top">' . $enrol_mode . '</td>
                                <td valign="top">' . date('M d Y', strtotime($data->enrolled_on)) . '</td>
                                <td valign="top">' . $contact . '</td>
                                    ' . $paid_td . '
                            </tr>';
                    }
                } else {
                    echo "<td colspan='7' class='error' style='text-align:center;font-weight:bold'>There are no booked seats available.</td>";
                }
                ?>
            </tbody>  
        </table>
    </div>
    <div style="clear:both;"></div><br>
    <ul class="pagination pagination_style">
        <?php echo $pagination; ?>
    </ul>
    <div style="clear:both;"></div>
    <div class="button_class"><a href="<?php echo base_url() . 'classes?course_id=' . $class['course_id']; ?>"><button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-step-backward"></span></span>&nbsp;Back</button></a></div> 
    <div style="clear:both;"></div>
</div>
<div class="modal-inv" id="ex13" style="display:none;width:25%">
    <p>
    <h2 class="panel_heading_style">Select Invoice Type</h2>
    <div>
        <?php
        $data = array('name' => 'select_invoice_print', 'class' => 'select_invoice_print');
        echo form_radio($data, 1, TRUE, $extra);
        echo '&nbsp; &nbsp; All'
        ?>
    </div>
    <div>
        <?php
        $data = array('name' => 'select_invoice_print', 'class' => 'select_invoice_print');
        echo form_radio($data, 2, FALSE, $extra);
        echo '&nbsp; &nbsp; With Subsidy';
        ?>
        <span id="with_subsidy_err"></span>
    </div>
    <div>
        <?php
        $data = array('name' => 'select_invoice_print', 'class' => 'select_invoice_print');
        echo form_radio($data, 3, FALSE, $extra);
        echo '&nbsp; &nbsp; Without Subsidy';
        ?>
        <span id="without_subsidy_err"></span>
    </div>
    <div class="popup_cancel popup_cancel001">
        <span href="#" rel="modal:close"><button class="btn btn-primary print_company_invoice" type="button">Print</button></span></div>
</p>
</div>
<div class="modal_333 modal_payment_recd" id="ex3" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Payment Received Details for '<span class="cp_invoice_company_name"></span>'</h2>
    <div class="table-responsive">
        <table class="table">
            <tbody>
                <tr>
                    <td class="td_heading">Course:</td>
                    <td><span class="cp_invoice_course_name"></span></td>
                    <td class="td_heading">Class:</td>
                    <td colspan="3"><span class="cp_invoice_class_name"></span></td>
                </tr>
                <tr>
                    <td class="td_heading">Invoice #:</td>
                    <td><span class="cp_invoice_id"></span></td>
                    <td class="td_heading">Invoice Dt:</td>
                    <td><span class="cp_invoice_dated"></span></td>
                    <td class="td_heading">Invoice Amount:</td>
                    <td>$<span class="cp_invoice_amount"></span> SGD</td>
                </tr>
                <tr>
                    <td class="td_heading"><span class="cp_invoice_discount_label"></span> Discount @<span class="cp_invoice_discount_rate"></span>%:</td>
                    <td>$<span class="cp_invoice_discount_amount"></span> SGD</td>
                    <td class="td_heading">Subsidy:</td>
                    <td>$<span class="cp_invoice_subsidy_amount"></span> SGD</td>
                    <td class="td_heading">GST @ (<span class="cp_invoice_gst_label"></span>)<span class="cp_invoice_gst_rate"></span>%:</td>
                    <td>$<span class="cp_invoice_total_gst"></span> SGD</td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <div class="table-responsive payment_scroll" style="height: 122px;">
        <table style="width:60%; margin:0 auto;" class="table table-striped cpayment_received">
            <thead>
                <tr>
                    <th>Payment Recd. On</th>
                    <th>Trainee Name</th>
                    <th>Amt. Recd.</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <br>
    <div class="popup_cance89">
        <a href="#" class="company_print_pdf"><button type="button" class="btn btn-primary">Print</button></a></div>
</p>
</div>
<div class="modal_333" id="ex8" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Payment Received Details for '<span class="p_invoice_trainee_name"></span>'</h2>
    <table class="table">
        <tbody>
            <tr>
                <td class="td_heading">Course:</td>
                <td><span class="p_invoice_course_name"></span></td>
                <td class="td_heading">Class:</td>
                <td colspan="3"><span class="p_invoice_class_name"></span></td>
            </tr>
            <tr>
                <td class="td_heading">Invoice #:</td>
                <td><span class="p_invoice_id"></span></td>
                <td class="td_heading">Invoice Dt:</td>
                <td><span class="p_invoice_dated"></span></td>
                <td class="td_heading">Invoice Amount:</td>
                <td>$<span class="p_invoice_amount"></span> SGD</td>
            </tr>
            <tr>
                <td class="td_heading"><span class="p_invoice_discount_label"></span> Discount @<span class="p_invoice_discount_rate"></span>%:</td>
                <td>$<span class="p_invoice_discount_amount"></span> SGD</td>
                <td class="td_heading">Subsidy:</td>
                <td>$<span class="p_invoice_subsidy_amount"></span> SGD</td>
                <td class="td_heading">GST @ (<span class="p_invoice_gst_label"></span>)<span class="p_invoice_gst_rate"></span>%:</td>
                <td>$<span class="p_invoice_total_gst"></span> SGD</td>
            </tr>
        </tbody>
    </table><br>
    <table style="width:60%; margin:0 auto;" class="table table-striped payment_received">
        <thead>
            <tr>
                <th>Payment Recd. On</th>
                <th>Mode</th>
                <th>Amt. Recd.</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <div class="popup_cance89">
        <a href="#" class="print_pdf"><button type="button" class="btn btn-primary">Print</button></a></div>
</p>
</div>
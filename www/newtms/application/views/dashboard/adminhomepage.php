<?php
$this->load->helper('common');
$role_check = $this->data['user']->role_id;
$role_array = array("COMPACT", "SLEXEC", "TRAINER", "CRSEMGR");
$style = '';
if (in_array($role_check, $role_array))
    $style = 'style="width:100%"';

?>
<div class="col-md-10">
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-dashboard"></span> Dashboard Details
        <span class="label label-default pull-right white-btn">
            <?php if ($role_check == 'COMPACT') { ?>
            <a href="<?php echo site_url() . 'profile/change_password/'; ?>">
                <span class="glyphicon glyphicon-retweet"></span> Change Password
            </a>
            <?php } else { ?>
                <a href="<?php echo site_url() . 'profile/'; ?>">
                    <span class="glyphicon glyphicon-user"></span> My Profile
                </a>
            <?php } ?>
        </span>
    </h2>          
    <div class="row">
        <div class="col-lg-6" <?php echo $style; ?>>            
            <h4 class="dash-title"><img src="<?php echo base_url(); ?>assets/images/dashboard.png" border="0">&nbsp;Classes Due to Start this week
                <span class="required-d red pull-right">** Overbooking Allowed</span></h4>
            <div class="table-responsive d-table-scroll">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="th_header">Class Date</th>
                            <th class="th_header">Class Name</th>
                            <th class="th_header">Trainer Name</th>
                            <th class="th_header">Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($classes_start_this_week)) {
                            foreach ($classes_start_this_week as $item) :
                                ?>                        
                                <tr>
                                    <td> <?php echo date('d-m-Y H:i:s', strtotime($item->class_start_datetime)); ?> </td>
                                    <td>
                                        <a href="<?php echo base_url(); ?>classes/view_class/<?php echo $item->class_id; ?>" >
                                            <?php echo ($item->class_pymnt_enrol == 'PAENROL') ? '<span class="red">**</span>' . $item->class_name : $item->class_name; ?>
                                        </a> 
                                    </td>
                                    <td> 
                                        <?php
                                            echo $item->trainer_name;
                                        ?> 
                                    </td>
                                    <td> 
                                        <?php echo ($item->classroom_location && $item->classroom_location!='OTH') ? get_catname_by_parm($item->classroom_location) : $item->classroom_venue_oth; ?> 
                                    </td>                        
                                </tr>
                                <?php
                            endforeach;
                        } else {
                            echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>No classes Found</label></td></tr>";
                        }
                        ?>                    
                    </tbody>
                </table>          
            </div>
            <p></p>          
        </div>
        <?php
        if ($role_check == 'ADMN') {
            ?>
            <div class="col-lg-6">
                <h4 class="dash-title"><img src="<?php echo base_url(); ?>assets/images/pending.png" border="0">&nbsp;Pending Account Activations</h4>
                <div class="table-responsive d-table-scroll">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="46%" class="th_header">Account Type</th>
                                <th width="54%" class="th_header">Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (count($pending_account_activation) > 0) {
                                $account_type_arr = array(
                                    'TRAINE'=>'Trainee',
                                    'COMUSR'=>'Company Staff',
                                    'INTUSR'=>'Internal Staff',
                                );
                                foreach ($pending_account_activation as $item) :
                                    if ($item->account_type == 'COMUSR') {
                                        $company_id = company_details_by_userid($item->user_id)->company_id;
                                        $link = base_url() . 'company/view_company/' . $company_id;
                                    } else if ($item->account_type == 'TRAINE') {
                                        $link = base_url() . 'trainee/view_trainee/' . $item->user_id;
                                    } else if ($item->account_type == 'INTUSR') {
                                        $link = base_url() . 'internal_user/view_user/' . $item->user_id;
                                    }
                                    ?>
                                    <tr>
                                        <td> 
                                            <?php echo $account_type_arr[$item->account_type]; ?> 
                                        </td>
                                        <td> 
                                            <?php
                                            echo '<a href="' . $link . '">' . $item->first_name . ' ' . $item->last_name . '</a>';
                                            ?> 
                                        </td>
                                    </tr>
                                    <?php
                                endforeach;
                            } else {
                                echo "<tr class='danger'><td colspan='2' style='text-align:center'><label>No Pending Account Activations Found</label></td></tr>";
                            }
                            ?>         
                        </tbody>
                    </table>
                </div><p></p>
            </div>
            <?php
        }
        ?>
        <div class="col-lg-12">
            <h4 class="dash-title"><a href="<?php echo base_url(); ?>class_trainee"><img src="<?php echo base_url(); ?>assets/images/invoice.png">&nbsp;Pending Class Payments and Invoices</a></h4>
            <div class="table-responsive d-table-scroll">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="th_header" width='11%'>Class Name</th>
                            <th class="th_header" width='12%'>Class Start Date</th>
                            <th class="th_header" width='25%'>Name</th>
                            <th class="th_header">Enrolment Type</th>
                            <th class="th_header">Enrolled On</th>                                                       
                            <th class="th_header"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($pending_class)) {
                            foreach ($pending_class as $item) :
                                if ($item->inv_type == 'INVINDV') {
                                    $name = $item->first_name . ' ' . $item->last_name.' (Individual)';
                                    $suffix_link = 'individual';
                                } else {                                    
                                    if ($item->company_id[0] == 'T') {
                                        $tenant_details = fetch_tenant_details($item->company_id);
                                        $company_name = $tenant_details->tenant_name;
                                    } else {
                                        $company_name = get_companyname($item->company_id);
                                    }
                                    $name = $company_name . ' (Company)';
                                    $suffix_link = 'company&company_id='.$item->company_id;
                                }
                                ?>                                        
                                <tr>
                                    <td> 
                                        <?php
                                        $role_array = array('TRAINER', 'CRSEMGR', 'COMPACT','SLEXEC');
                                        if (in_array($this->session->userdata('userDetails')->role_id, $role_array)) {
                                            ?>
                                            <a href="<?php echo base_url(); ?>classes/view_class/<?php echo $item->class_id; ?>" >
                                                <?php echo $item->class_name; ?>
                                            </a>
                                        <?php } else { ?>                                        
                                            <a href="<?php echo base_url(); ?>classes/edit_class">
                                                <?php echo $item->class_name; ?>
                                            </a>
                                        <?php } ?>    
                                    </td>
                                    <td> <?php echo date('d-m-Y H:i:s', strtotime($item->class_start_datetime)); ?> </td>
                                    <td> <?php echo $name; ?> </td>
                                    <td> <?php echo ($item->enrolment_type) ? get_catname_by_parm($item->enrolment_type) : ''; ?> </td>
                                    <td> <?php echo date('d-m-Y H:i:s', strtotime($item->enrolled_on)); ?> </td>                                
                                    <td>
                                        <?php if (in_array($this->session->userdata('userDetails')->role_id, $role_array)) { ?>
                                            <span class="red">Paymnt. Due</span>
                                        <?php } else { ?>
                                            <a href="<?php echo base_url() . 'accounting/update_payment?invoice_id='.$item->invoice_id.'&enrol_mode='.$suffix_link; ?>" class="red">Pay Now </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                            endforeach;
                        } else {
                            echo "<tr class='danger'><td colspan='6' style='text-align:center'><label>No Pending Class Payments and Invoices Found</label></td></tr>";
                        }
                        ?>  
                    </tbody>
                </table>
            </div>
            <p></p>
        </div>
        <?php
        if ($role_check == 'ADMN') {
            ?>
            <div class="col-lg-6">
                <h4 class="dash-title"><img src="<?php echo base_url(); ?>assets/images/commission.png" border="0">&nbsp;Sales Commission Payment Due</h4>

                <div class="table-responsive d-table-scroll">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="th_header">Sales Person Name</th>
                                <th class="th_header">Due Period</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (count($sales_commission_due) > 0) {
                                foreach ($sales_commission_due as $k => $item) :
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo base_url(); ?>accounting/update_commission">
                                                <span class='name_<?php echo $k; ?>' ><?php echo $item['name']; ?></span>
                                            </a>
                                        </td>
                                        <td> 
                                            <?php
                                            $period = "";
                                            foreach ($item['due_period'] as $year => $due_period) :
                                                foreach ($due_period as $key => $value) {
                                                    $period .= $value . " " . $year . ", ";
                                                }
                                            endforeach;
                                            echo rtrim($period, ", ");
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                endforeach;
                            } else {
                                echo "<tr class='danger'><td colspan='2' style='text-align:center'><label>No Sales Commission Payment Due Found</label></td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <p></p>
            </div>
            <?php
        }
        ?>
        <div class="col-lg-6" <?php echo $style; ?>>
            <h4 class="dash-title"><img src="<?php echo base_url(); ?>assets/images/notification.png" border="0">&nbsp;Notifications/ Announcements</h4>
            <?php
            if (count($notifications) > 0) {
                $cls = 'alert-info';
            } else {
                $cls = 'n-danger';
            }
            ?>
            <div class="alert <?php echo $cls ?>">
                <ul class="list_style1">
                    <?php
                    if (count($notifications) > 0) {
                        foreach ($notifications as $item) :
                            ?>
                            <li><?php echo nl2br($item->noti_msg_txt); ?></li>                    
                            <?php
                        endforeach;
                    } else {
                        echo "<li>No Notifications/ Announcements Found</li>";
                    }
                    ?>
                </ul>
            </div>  
        </div>
    </div> 
</div>
<div class="modal49" id="ex9" style="display:none;">  
    <h5 class="panel_heading_style panel_heading_style1">Commission Paid Details for '<span class="s_name"></span>' as on <?php echo date('d M, Y'); ?></h5>  
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="th_header">Paid On</th>
                <th class="th_header">Amount(SGD)</th>
            </tr>
        </thead>
        <tbody class="sales_popup">
        </tbody>
    </table>  
</div>

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

    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/internal_user.png"/>  TMS REPORT - PAID/NOTPAID</h2>
    <div class="table-responsive">
        <?php
//            print_r($tabledata);
        $atr = 'id="search_form" name="search_form" method="POST"';
//        echo form_open("internal_user/activity_log", $atr);
        echo form_open("reports_finance/tms_report", $atr);
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">                               
                        Select Any Year
                    </td>
                    <td>
                        <select id='gYear' name='yearVal'>
                            <option value=''>--Select Year--</option>
                            <option value='2015'>2016</option>
                            <option value='2017'>2017</option>
                            <option value='2018'>2018</option>
                            <option value='2019'>2019</option>
                            <option value='2020'>2020</option>
                        </select> 
                    </td>
                    <td class="td_heading">                               
                        Select Any Month
                    </td>
                    <td>
                        <select id='gMonth' name='monthVal'>
                            <option value=''>--Select Month--</option>
                            <option value='01'>Janaury</option>
                            <option value='02'>February</option>
                            <option value='03'>March</option>
                            <option value='04'>April</option>
                            <option value='05'>May</option>
                            <option value='06'>June</option>
                            <option value='07'>July</option>
                            <option value='08'>August</option>
                            <option value='09'>September</option>
                            <option value='10'>October</option>
                            <option value='11'>November</option>
                            <option value='12'>December</option>
                        </select> 
                    </td>
                    <td class="td_heading">                               
                        Payment Status
                    </td>
                    <td>
                        <select id='payStatus' name='payStatus'>
                            <option value=''>--Select Payment Status--</option>
                            <option value='1'>Paid</option>
                            <option value='2'>Not - Paid</option>
                        </select> 
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">                               
                        Result Status
                    </td>
                    <td>
                        <select id='tStatus' name='trainingStatus'>
                            <option value=''>--Select Training Score--</option>
                            <option value='1'>Competant</option>
                            <option value='2'>Not Yet Competant</option>
                            <option value='3'>Absentees</option>
                            <option value='4'>Competant / Not Yet Competant</option>
                        </select> 
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="push_right btn_srch">
            <button type="submit" class="search_button btn btn-xs btn-primary no-mar">
                <span class="glyphicon glyphicon-search"></span>
                Search
            </button>

        </div>
        <?php echo form_close(); ?>


        <div class="bs-example">
            <div class="table-responsive">
                <!--            <div class="add_button space_style">
                <?php //if (count($tabledata) > 0 && array_key_exists('EXP_XLS', $this->data['left_side_menu']['INTUSR'])) { ?>
                                            <a href="<?php //echo site_url('/internal_user/export_users_page' . $export_url) ?>"  class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export Page Fields</span></a> &nbsp;&nbsp;
                                            <a href="<?php //echo site_url('/internal_user/export_users_full' . $export_url) ?>"  class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export All Fields</span></a>
                <?php //} ?>
                            </div>-->
                <div style="clear:both;"></div>
                <table id="listview" class="table table-striped">
                    <thead>
                        <tr>
                            <th width="10%">Tax Code</th>
                            <th width="10%">Invoice ID.</th>
                            <th width="10%">Trainee Name</th>
                            <th width="15%">Company Name</th>
                            <th width="20%">Class Fees</th>
                            <th width="10%">Discount</th>
                            <th width="10%">Subsidy</th>
                            <th width="10%">GST</th>
                            <th width="10%">Net Amt.</th>
                            <th width="9%">Mode</th>
                            <th width="9%">TG No.</th>
                            <th width="9%">Class Start Date</th>
                            <th width="9%">Class End Date</th>
                            <th width="9%">Class Name</th>
                            <th width="9%">Training Score</th>
                            <th width="9%">Payment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            echo "Total Count : ".count($result);
                            ?>
                        </tr>
                        <?php
                        $unpaidVal = 0;
                        foreach ($result as $data) {
                            ?>
                            <tr>
                                <td><?php echo $data->tax_code; ?></td>
                                <td><?php echo $data->invoice_id; ?></td>
                                <td><?php echo $data->name; ?></td>
                                <td><?php echo $data->company_name ?? "N/A"; ?></td>
                                <td>$ <?php echo $data->class_fees; ?></td>
                                <td>$ <?php echo $data->discount_rate ?? "N/A"; ?></td>
                                <td>$ <?php echo $data->subsidy_amount ?? "N/A"; ?></td>
                                <td>$ <?php echo $data->gst_amount ?? "N/A"; ?></td>
                                <td>$ <?php 
                                
                                echo $data->total_amount_due; 
                                $unpaidVal = $unpaidVal + $data->total_amount_due;
                                ?></td>
                                <td> <?php echo $data->mode_of_pymnt ?? "N/A"; ?></td>
                                <td><?php echo $data->tg_number ?? "N/A"; ?></td>
                                <td><?php echo date('d/m/Y H:i:s', strtotime($data->class_start_datetime)); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($data->class_end_datetime)); ?></td>
                                <td><?php echo $data->class_name; ?></td>
                                <td><?php echo $data->training_score; ?></td>
                                <td><?php echo $data->class_name; ?></td>
                                 <td><?php echo $data->payment_status; ?></td>
                            </tr>
                        <?php } 
                        
                        echo "Total Amount Due for unpaid invoices :".$unpaidVal;
                        
                        ?>
                    </tbody>      
                </table>
            </div>
            <div style="clear:both;"></div><br>
            <ul class="pagination pagination_style">
                <?php
                echo $pagination;
//            echo $this->input->get('cls_name');
                ?>
            </ul>
        </div>
    </div>
    <script>
        $("#search_form").submit(function () {
            ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
            check_remove_id();
            var self = $(".btn_srch"),
                    button = self.find('input[type="submit"],button');
            button.attr('disabled', 'disabled').html('Please Wait..');
            ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
        });
        function check_remove_id() {
            $staff = $('#internal_staff').val();
            if ($staff == '') {
                $('#user_id').val('');
            }

        }
    </script>
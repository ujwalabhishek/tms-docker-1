<?php
$start = set_value('from_date');
$end = set_value('end_date');
$sales_id = set_value('sales_id');
$sales_id_post = $this->input->get('sales_id');

$non_sales_id = set_value('non_sales_id');
$non_sales_id_post = $this->input->get('non_sales_id');


$ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
$pageurl = 'TMSAdmin/'. $controllerurl;
?>

<script type="text/javascript">
    var CLIENT_DATE_FORMAT = 'dd/mm/yy';
    var pageurl = '/<?php echo $pageurl; ?>';
    var ancher = '<?php echo $ancher; ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/enrollment_report.js"></script>

<div class="col-md-10">
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-repeat"></span> Reports - Enrollment Report (Sales)</h2>
    <?php
    $start_date = $this->input->get('from_date');
    $end_date = $this->input->get('to_date');
    $atr = 'id="search_form" name="search_form" method="get"';
    echo form_open("reports/enrolment_report?b=" . $sort_by . "&o=" . $sort_order, $atr);
    ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <?php
                $style = '';
                if (!empty($salesexec_check)) {
                    $style = 'display:none;';
                }
                ?>
                <tr style="<?php echo $style; ?>">
                   
                    <td class="td_heading">&nbsp;&nbsp;
                        <?php
                        $checked = TRUE;
                        $check = $this->input->get('search_select');
                        if ($check) {
                            $checked = ( $check == 1) ? TRUE : FALSE;
                        }
                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => 1,
                            'checked' => $checked
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp;Sales Executive:
                    </td>
                    <td colspan="2">
                        <?php
                        
                        $attr_js = 'id="select_sales_id">';
                        echo form_dropdown('sales_id', $sales, $this->input->get('sales_id'), $attr_js);
                        ?>
                    </td>
                    <td width="15%" class="td_heading">
                        <?php
                        $checked = ($this->input->get('search_select') == 2) ? TRUE : FALSE;
                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => 2,
                            'checked' => $checked
                            );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp;Non Sales Executive:
                    </td>
                  
                    <td colspan="2">
                        <?php
                        $attr_js = 'id="select_non_sales_id">';
                        echo form_dropdown('non_sales_id', $non_sales, $this->input->get('non_sales_id'), $attr_js);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Period From:</td>
                    <td>
                        <?php
                        $attr_js = 'id="input_from_date" placeholder="dd/mm/yyyy"';
                        echo form_input('from_date', $this->input->get('from_date'), $attr_js);
                        ?>
                    </td>
                    <td class="td_heading">To:</td>
                    <td>
                        <?php
                        $attr_js = 'id="input_to_date" placeholder="dd/mm/yyyy"';
                        echo form_input('to_date', $this->input->get('to_date'), $attr_js);
                        ?>
                    </td>
                    <td colspan="2" align="center">
                        <button type="submit" class="pull-right btn btn-xs btn-primary no-mar" name="enrolmentrep_search_button" title="Generate Report" value="Generate Report"><span class="glyphicon glyphicon-search"></span> Search</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
    echo form_hidden('export', null);
    echo form_close();
    ?>
    <?php if (empty($tabledata) ) { ?>
        <br>
        <table class="table table-striped">
            <tr class="danger">
                <td colspan="7" align="center" class="error">There are no enrollments.</td>
            </tr>
        </table>
        <?php
    }
    if (!empty($tabledata)) {
        ?>
        <div>
            <strong>
                <?php
                $strMsg = 'Enrollment Report ';
                if(!empty($sales_id_post)) {
                        $strMsg .= "for Sales Executive '".$sales_details->first_name . ' ' . 
                        $sales_details->last_name."(".$sales_details->tax_code.")Contact Detail: ".$sales_details->contact_number . ', ' . $sales_details->off_email_id; 
                    }
                else if(!empty($non_sales_id_post)) {
                        $strMsg .= "for  '".$sales_details->first_name . ' ' . 
                        $sales_details->last_name."(".$sales_details->tax_code.")Contact Detail: ".$sales_details->contact_number . ', ' . $sales_details->off_email_id; 
                    }
                ?>
                <?php
                if (empty($start_date) && empty($end_date)) {
                        $period = ' for ' . date('F d Y, l');
                } else {
                         $period = 'for the period';
                    if (!empty($start_date))
                        $period .= ' from ' . date('F d Y', DateTime::createFromFormat('d/m/Y', $start_date)->getTimestamp());
                    if (!empty($end_date))
                        $period .= ' to ' . date('F d Y', DateTime::createFromFormat('d/m/Y', $end_date)->getTimestamp());
                }
                echo $strMsg.$period;
                ?>
            </strong><span id="search_error"></span>
            <?php 
            // added by shubhranshu
            $salesid = $this->input->get("sales_id");
                  $nonsalesid = $this->input->get("non_sales_id");
                
                  $start = $this->input->get("from_date");
                  $end = $this->input->get("to_date");
             if(($salesid!="") || ($nonsalesid!="") || ($start!="" && $end!=''))
             {
             ?>
            <div class="pull-right" style='margin: 10px;'>
                <br />
                <a href="<?php echo site_url('/reports/enrolment_report_export_xls') . '?' . $_SERVER['QUERY_STRING']; ?>"  class="small_text1">

                    <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to XLS</span></a> &nbsp;&nbsp;
                <a href="<?php echo site_url('/reports/enrolment_report_export_pdf') . '?' . $_SERVER['QUERY_STRING']; ?>"  class="small_text1">
                    <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to PDF</span></a>                
                <br /><br />
            </div>
             <?php }else{?>
                <div class="pull-right" style='margin: 10px;'>
                    <br />
                    <a href="javascript:void(0)" class="small_text1" id='displayText'>
                        <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to XLS</span></a> &nbsp;&nbsp;
                    <a href="javascript:void(0)" class="small_text1" id='displayText1'>
                        <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to PDF</span></a>                
                       
                       
                    <br/>
                </div>
             <div id="alertmsg" style="padding:5px;clear:both;display:none" class='alert alert-danger'>Please Select One of the above filter to export PDF/XLS.</div>
            <?php }?>
        </div>
        <div style="clear:both;"></div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <?php
                        $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                        $pageurl = $controllerurl;
                    ?>
                    <tr>
                        <th class="th_header" sort="c.crse_name"><a style="color:#000000;" href="<?php  echo base_url().$pageurl. "?" . $sort_link."&f=c.crse_name&o=" .$ancher; ?>" >Course Name</a></th>
                        <th class="th_header" sort="cc.class_name"><a style="color:#000000;" href="<?php  echo base_url().$pageurl. "?" . $sort_link."&f=cc.class_name&o=" .$ancher; ?>" >Class Name</a></th>
                        <th class="th_header" sort="tu.tax_code"><a style="color:#000000;" href="<?php  echo base_url().$pageurl. "?" . $sort_link."&f=tu.tax_code&o=" .$ancher; ?>" >NRIC/FIN No.</a></th>
                        <th class="th_header" sort="name"><a style="color:#000000;" href="<?php  echo base_url().$pageurl. "?" . $sort_link."&f=tup.first_name&o=" .$ancher; ?>" >Trainee Name</a></th>
                        <th class="th_header" sort="enrolment_date"><a style="color:#000000;" href="<?php  echo base_url().$pageurl. "?" . $sort_link."&f=ce.enrolled_on&o=" .$ancher; ?>" >Enrollment Date</a></th>
                        <th class="th_header" sort="country"><a style="color:#000000;" href="<?php  echo base_url().$pageurl. "?" . $sort_link."&f=tu.country_of_residence&o=" .$ancher; ?>" >Nationality</a></th>
                        <th class="th_header" sort="contact">Trainee Contact Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    foreach ($tabledata as $key => $data) {
                        ?>
                        <tr>
                            <td><?php echo $data['crse_name'] ?></td>
                            <td><?php echo $data['class_name'] ?></td>
                            <td><?php echo $data['tax_code'] ?></td>
                            <td><?php echo $data['name'] ?></td>
                            <td><?php echo $data['enrolment_date'] ?></td>
                            <td><?php echo $meta_map[$data['country']] ?></td>
                            <td><?php echo rtrim($data['contact'], ', '); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div style="clear:both;"></div>
        <br>
        <span class="required required_i">* Required Fields</span><br>
        <ul class="pagination pagination_style">
            <?php echo $pagination; ?>
        </ul>
    <?php } ?>
</div>
<script>
    // added by shubhranshu
$(document).ready(function() {
        $( "#displayText" ).click(function() {
          $( "#alertmsg" ).show();
        });
         $( "#displayText1" ).click(function() {
          $( "#alertmsg" ).show();
        });
        
    });
    </script>
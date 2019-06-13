<div class="col-md-10">
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Reports - Monthly Enrollment Count</h2>
    <div class="table-responsive">
        <?php
        $year = $this->input->get('year');
        $year = empty($year) ? date('Y') : $year;
        $atr = array('id' => 'monthly_enrollment_form', 'method' => 'get');
        echo form_open("reports/monthly_enrollment_count", $atr);
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading" width="15%">Report for the year:</td>
                    <td width="85%">
                        <?php echo form_dropdown("year", $year_arr, $year, 'id="year"') ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="clear:both;"></div><br/>    
    <?php
    if (!empty($tabledata)) {
        ?>
        <div class = "panel-heading panel_headingstyle" style = "width:100%;"><strong>Monthly Enrollment Count Report for the year '<?php echo $year; ?>'</strong></div>
        <br>
        <div>
            <span class="pull-right">
                <a href="<?php echo site_url('/reports/monthly_enrollment_count_xls') . '?' . $_SERVER['QUERY_STRING']; ?>" class="small_text1">
                    <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to XLS</span></a> &nbsp;&nbsp;
                <a href="<?php echo site_url('/reports/monthly_enrollment_count_pdf') . '?' . $_SERVER['QUERY_STRING']; ?>" class="small_text1">
                    <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to PDF</span></a>
            </span>
        </div>
        <br><br>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th width="10%" class="th_header">Month</th>
                    <th width="15%" class="th_header">Total Enrollments</th>
                    <th width="15%" class="th_header">Present`s Attendance Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tabledata as $data) { ?>
                    <tr>
                        <td><?php echo $data->month; ?></td>
                        <td><?php echo $data->count; ?></td>
                        <td><?php echo empty($att_data[$data->month])?0:$att_data[$data->month]; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <br>
        <table class="table table-striped">
            <tr class="danger">
                <td style="color:red;text-align: center;">No data available.</td>
            </tr>
        </table>
    <?php } ?>
</div>
<script>
$(function(){
    var currenturl = '<?php echo current_url(); ?>';
    $('#year').change(function(){
        var year = $(this).val();
        window.location.href= currenturl + '?year=' + year;
    });
});
</script>
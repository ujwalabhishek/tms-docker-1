<div class="col-md-10">
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/comission.png"> Settings - GST Rates</h2>   
    <?php
    if ($this->session->flashdata('success_message')) {
        echo '<div class="success">' . $this->session->flashdata('success_message') . '!</div>';
    }
    if ($this->session->flashdata('error_message')) {
        echo '<div class="error1">' . $this->session->flashdata('error_message') . '!</div>';
    }
    ?>
    <table class="table table-striped">
        <thead>        
            <tr>
                <th class="th_header">Rate</th>
                <th class="th_header">Default</th>
                <th class="th_header">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td valign="top">
                    <?php
                    if ($gst_active_value->gst_rate) {
                        echo number_format($gst_active_value->gst_rate, 2, '.', '') . '%';
                    } else {
                        ?>
                        <label style='color:red'>Active GST rate not set.</label>
                    <?php } ?>    
                </td>
                <td valign="top"><input type="radio" value="" checked disabled></td>
                <td valign="top"><a href="<?php echo site_url(); ?>settings/edit_gst_rate" class="small_text1">Edit</a></td>
            </tr>
        </tbody>
    </table>    
    <br>
    <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-list"></span> GST Rate Change History</h2>
    <table class="table table-striped">
        <thead>
            <?php
            $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
            $pageurl = $controllerurl;
            ?>
            <tr>
                <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?&f=gst.gst_rate&o=" . $ancher; ?>" >Rate</a></th>
                <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?&f=usr.first_name&o=" . $ancher; ?>" >Change Details</a></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($gst_list)) {
                for ($i = 0; $i < count($gst_list); $i++) {
                    $updated_on = explode("-", $gst_list[$i]->updated_on);
                    $monthName = date("F", mktime(null, null, null, $updated_on[1]));
                    if ($gst_active_value->gst_id != $gst_list[$i]->gst_id) {
                        ?>
                        <tr>
                            <td valign="top"><?php echo $gst_list[$i]->gst_rate ?> %</td>
                            <td valign="top">Changed on <?php echo $updated_on[2] . "/" . $updated_on[1] . "/" . $updated_on[0]; ?> by TMS Administrator <?php echo $gst_list[$i]->first_name . " " . $gst_list[$i]->last_name; ?></td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
        </tbody>
    </table>      
</div>
<div style="clear:both;"></div><br>    
<ul class="pagination pagination_style">
    <?php echo $pagination; ?>
</ul>

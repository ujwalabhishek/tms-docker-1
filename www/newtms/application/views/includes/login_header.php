<div class="container container_style">
    <div class="masthead">
        <div class="h3_logo">
            <a href="<?php echo base_url(); ?>user/dashboard" title="Home" >
                <img class="logo" src="<?php echo base_url(); ?>logos/<?php echo $this->session->userdata('public_tenant_details')->Logo; ?>" border="0" width="64" height="64">
            </a>
        </div>
        <div class="training">
             <img src="<?php echo base_url(); ?>assets/images/<?php echo $this->session->userdata('public_tenant_details')->ApplicationName; ?>" border="0" title="Training Management System">
        </div>
        <div class="right_date">
            <table class="pull-right">
                <tbody>
                    <tr>
                        <td><div class="welcome">Welcome <?php echo $this->session->userdata('userDetails')->first_name . ' ' . $this->session->userdata('userDetails')->last_name; ?></div></td>
                        <td ><a href="<?php echo site_url(); ?>user/logout"><img src="<?php echo base_url(); ?>assets/images/logout1.png" border="0" /></a></td>
                        <a href="error.php"></a>
                    </tr>
                    <tr>
                        <td><span class="date_time" id="date_time"></span>
                          <script type="text/javascript">window.onload = date_time('date_time');</script>
                        </td>
                    </tr>
                    <tr><td colspan="2"><a href="<?php echo base_url(); ?>user/dashboard" style="float:right;"> <img src="<?php echo base_url(); ?>assets/images/home.png" title="Home"></a></td></tr>
                </tbody>
            </table>
        </div>
    </div>    
</div>
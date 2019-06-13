<?php 
 $website_url = ($this->data['tenant_details']->website_url) ? $this->data['tenant_details']->website_url : base_url();
?>
<div class="container container_style">
    <div class="masthead">
        <div class="h3_logo">            
            <a href="<?php echo base_url(); ?>">
                <img class="logo" src="<?php echo base_url(); ?>logos/<?php echo $this->data['tenant_details']->logo; ?>" border="0"/>
            </a>
        </div>
        <div class="training" style='margin: 15px 0px'>
            <img src="<?php echo base_url(); ?>assets/images/<?php echo $this->data['tenant_details']->applicationname; ?>" border="0" title="Training Management System" />
        </div>
        <div class="right_date">
<table class="pull-right">
<tbody>
<tr>
<td><div class="welcome">Welcome <?php echo $this->data['user']->first_name . ' ' . $this->data['user']->last_name; ?></div></td>
<td rowspan="2"><a href="<?php echo site_url(); ?>login/logout"><img src="<?php echo base_url(); ?>assets/images/logout1.png" border="0" /></a></td>
<a href="error.php"></a>
</tr>
<tr>
<td><span class="date_time" id="date_time"></span>
  <script type="text/javascript">window.onload = date_time('date_time');</script></td>
</tr>
</tbody>
</table>
        </div>
    </div>
</div>
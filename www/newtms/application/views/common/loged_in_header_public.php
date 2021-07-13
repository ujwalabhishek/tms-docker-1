
<div class="container-fluid bg-3 text-center header-box">
    <div class="containers">
        <div class="row">
            <div class="col-sm-2">
               <a href="<?php echo base_url()?>" class='pull-left'>
                <img class="logo" src="<?php echo base_url()?>logos/t01.png" border="0">
                </a>
            </div>
            <div class="col-sm-7 header-ttl"><p>Training Management as a Service (TMaaS)</p></div>
            <div class="col-sm-3">
                <div class="right_dates">
                    <table class="pull-right">
                        <tbody>
                            <tr>
                                <td>
                                    <div class="welcome">Welcome <?php 
                                        $string = $this->session->userdata('userDetails')->first_name . ' ' . $this->session->userdata('userDetails')->last_name; 
                                        $fullname =(strlen($string) > 22) ? substr($string,0,20).'..' : $string;
                                        echo $fullname;
                                       ?>
                                    </div>
                                </td>
                                <td ><a href="<?php echo site_url(); ?>user/logout"><img src="<?php echo base_url(); ?>assets/images/logout1.png" border="0" /></a></td>
                                <a href="error.php"></a>
                            </tr>
                            <tr>
                                <td><span class="date_time" id="date_time"></span>
                                  <script type="text/javascript">window.onload = date_time('date_time');</script>
                                </td>
                            </tr>
                            <!--<tr><td colspan="2"><a href="<?php echo site_url(); ?>" style="float:right;"> <img src="<?php echo base_url(); ?>assets/images/home.png" title="Home"></a></td></tr>-->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>




<!--<div class="container container_style">
    <div class="masthead">
        <div class="h3_logo">
            <a href="<?php echo site_url(); ?>" title="Home" >
                <?php
                if(base_url() == 'https://xprienz2.biipmi.co/'){?>                
               <img class="logo" src="<?php echo base_url(); ?>logos/arise.png" border="0" style="max-width: 200px;" height="64">
                <?php }else{?>
                <img class="logo" src="<?php echo base_url(); ?>logos/t01.png" border="0" style="max-width: 200px;" height="64">
                <?php }?>
                <img class="logo" src="<?php echo base_url(); ?>logos/<?php echo $this->session->userdata('public_tenant_details')->Logo; ?>" border="0" width="64" height="64">
            </a>
        </div>
        <div class="training1">
            <a href="<?php echo site_url(); ?>" title="Home" >
            <img src="<?php echo base_url(); ?>assets/images/tms1.png" border="0" title="Training Management System">
        </a>
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
                    <tr><td colspan="2"><a href="<?php echo site_url(); ?>" style="float:right;"> <img src="<?php echo base_url(); ?>assets/images/home.png" title="Home"></a></td></tr>
                </tbody>
            </table>
        </div>
    </div>    
</div>-->
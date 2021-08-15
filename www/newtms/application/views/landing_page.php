<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="addrip" value="<?php echo $_SERVER['SERVER_ADDR']; ?>" /> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Biipmi Training Management System" />
        <meta name="timezone" value="<?php date_default_timezone_set('Asia/Singapore');echo $timezone = date_default_timezone_get() . " / " . date('m/d/Y h:i:s a', time()); ?>">
        <title><?php echo $page_title; ?></title> 
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
        <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-78372106-1', 'auto');
        ga('send', 'pageview');

      </script> 
        <style>
            #wrapper{
                margin:0;
                padding:0;
            }

            .header-box{
                background-color: rgba(255, 255, 255, 0.2);
                -webkit-box-shadow: 0 3px 1px rgba(0, 0, 0, 0.2);
                padding: 20px;

            }
            .header-div{margin:20px 0;}

            .header-ttl{color:#446cb3;
                font-size: 40px;
                font-weight: 600;
                text-shadow: 1px 1px 1px #161163;
                font-family: "Gill Sans", "Gill Sans MT", "Myriad Pro", "DejaVu Sans Condensed", Helvetica, Arial, "sans-serif";

            }

            .col-desg{
                padding:10px;
                height:100%;
                height:150px;
            }

            .col-desg-tlt{
                background: #446cb3;
                padding: 10px;
                margin: 10px;
                height:40px;
                color: #fff;
                box-shadow: 0px -2px 6px 1px rgba(0, 0, 255, .2);
            }

            .col-desg-tlt:p{
                text-align: center;
                color: #fff;
                margin-top: 30px;	
            }
		
        </style>

    </head>
    <body>
        <div id="wrapper">
            <div class="container-fluid bg-3 text-center header-box">
                <div class="container">
                    <div class="row">

                        <div class="col-sm-2">
                           <a href="<?php echo base_url()?>" class='pull-left'>
                            <img class="logo" src="<?php echo base_url()?>logos/t01.png" border="0">
                            </a>
                   
                        </div>
                        <div class="col-sm-10 header-ttl"><p>Training Management as a Service (TMaaS)</p></div>


                    </div>
                </div>
            </div>
            <div class="clear"></div>

            <div class="container-fluid bg-3 text-center" style="margin:0 0 82px 0;">    
                <h4>Welcome to BIIPMI's training portal. Please click on the links below to navigate to your training institute.</h4><br>

                <div class="container bg-4 text-center">
                    <div class="row">
                        <?php 
                            foreach ($tenants as $client){ ?>
                                <div class="col-sm-4 col-desg">
                                    <a href="<?php echo 'http://'.$client['tenant_url'];?>">
                                        <p class="col-desg-tlt"><?php echo $client['tenant_name'];?></p>
                                        <?php if($client['tenant_id'] == "T25") {?>
                                            <img src="<?php echo base_url()?>logos/<?php echo $client['tenant_logo'];?>"  width='100px' height="100px" alt="Image">
                                        <?php } else {?>
                                            <img src="<?php echo base_url()?>logos/<?php echo $client['tenant_logo'];?>"  width='132px' alt="Image">
                                        <?php }?>
                                    </a>
                                </div>
                        <?php } ?>
                    </div>
                </div>                
                <br>
            </div>
            <p></p>
            <div id="footer" style="padding: 10px;margin: auto;box-shadow: 0px -2px 4px  rgba(0, 0, 255, .2);">

                <div class="container-fluid bg-4 text-center"> 
                    <div class="container bg-3 text-center">
                        <div class="row">
                            <div class="col-sm-8"><h5  style="text-align: right;color: #999393;">Copyright © BIIPMI 2015-2021, All Rights Reserved.</h5></div>
                            <div class="col-sm-4"><h6 style="text-align: right;color: #999393;">Powered by biipmi Pte Ltd</h6></div>

                        </div>
                    </div>
                </div>
            </div>
       </div>
    </body>
</html>




<!--
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
	<meta name="addrip" value="<?php echo $_SERVER['SERVER_ADDR']; ?>" /> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Biipmi Training Management System and tms" />
	<meta name="timezone" value="<?php date_default_timezone_set('Asia/Singapore'); echo $timezone = date_default_timezone_get()." / ". date('m/d/Y h:i:s a', time()); ?>">
        <title><?php echo $page_title; ?></title> 
		<link href="<?php echo base_url()?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
		<link href="<?php echo base_url()?>assets/css/style.css" rel="stylesheet" type="text/css">
		<link href="<?php echo base_url()?>assets/css/glyphicons.css" rel="stylesheet" type="text/css">
		<link href="<?php echo base_url()?>assets/css/jquery.modal.css" rel="stylesheet"  type="text/css" media="screen" />
		<link href="<?php echo base_url()?>assets/css/student.css"  rel="stylesheet" type="text/css" media="screen" />
		<link href="<?php echo base_url()?>assets/css/autocomplete.css" rel="stylesheet" type="text/css"  />
            <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-78372106-1', 'auto');
  ga('send', 'pageview');

</script> 
<style>
    .training1{
    width: 75%;
    float: left;
    margin: 0px auto;
    padding-top: 35px;
    }
    .table{
        margin:0px auto;
        width:35%;
        
    }
    .table h5{
        text-align:left;
    }
</style>
    </head>
    <body>
        <div class="main_container_new_top" >     
            
            <div class="container container_style">
                <div class="masthead">
                    <h3 class="text-muted h3_logo">
                        <a href="<?php echo base_url()?>">
                            <img class="logo" src="<?php echo base_url()?>logos/t01.png" border="0">
                        </a>
                    </h3>
                    <div class="training1">
                        <img src="<?php echo base_url()?>assets/images/tms1.png">
                    </div>       
                    <div class="right_date">
                    </div>
                </div>	
            </div>
                           
            <div class="container_nav_style">	
                <div class="container container_row">
                    <div class="row row_pushdown">
                       
                       <div class="col-md-12 min-pad">
            <h2 class="panel_heading_style"><img src="<?php echo base_url()?>assets/images/trainee.png"/> Public Landing Page</h2>            
            <br>
            <h4 style="text-align:center">Welcome to BIIPMI's training portal. Please click on the links below to navigate to  your training institute.</h4>
            <br/>            
            <div id ='trainee_validation_div' style="text-align: center">
                                  
                    <div class="table table-responsive">                        
                        <br>                        
                        
                                <?php 
                                    foreach ($tenants as $client){ ?>
                                      <h5>                                                                
                                        <a href="<?php echo 'http://'.$client['tenant_url'];?>">
                                            <img style="width: 100px; height:43px; margin-right: 5px" src="<?php echo base_url()?>logos/<?php echo $client['tenant_logo'];?>">
                                            <?php echo $client['tenant_name'];?>
                                        </a> 
                                      </h5> 
                            <?php    }

                                ?>
                           
                            <br/>
                            <div class="clear"></div>  
                      
                   
                </div>
            </div>
        </div>
                       
                    </div>
                    <div style="clear:both;">
                    </div>
                         <footer>
                             <p>&copy;<font style="font-size:11px; color:#000000; font-weight:bold;">BIIPMI PTE LTD</font> <font style="font-size:9px; color:#ac0000; font-weight:bold; float:right;">powered by biipmi Pte Ltd.</font></p>
      </footer>

                </div>
            </div>	
        </div>       
    </body>
</html>-->
<?php exit;?>
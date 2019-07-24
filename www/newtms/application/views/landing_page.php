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
		<link href="https://www.biipmi.co/tmspublic/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
		<link href="https://www.biipmi.co/tmspublic/assets/css/style.css" rel="stylesheet" type="text/css">
		<link href="https://www.biipmi.co/tmspublic/assets/css/glyphicons.css" rel="stylesheet" type="text/css">
		<link href="https://www.biipmi.co/tmspublic/assets/css/jquery.modal.css" rel="stylesheet"  type="text/css" media="screen" />
		<link href="https://www.biipmi.co/tmspublic/assets/css/student.css"  rel="stylesheet" type="text/css" media="screen" />
		<link href="https://www.biipmi.co/tmspublic/assets/css/autocomplete.css" rel="stylesheet" type="text/css"  />
            <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-78372106-1', 'auto');
  ga('send', 'pageview');

</script>     
    </head>
    <body>
        <div class="main_container_new_top" >     
            
            <div class="container container_style">
                <div class="masthead">
                    <h3 class="text-muted h3_logo">
                        <a href="https://www.biipmi.co/tmspublic/">
                            <img class="logo" src="https://www.biipmi.co/tmspublic/logos/defaultlogo.png" border="0">
                        </a>
                    </h3>
                    <div class="training1">
                        <img src="https://www.biipmi.co/tmspublic/assets/images/TMS1.png">
                    </div>       
                    <div class="right_date">
                    </div>
                </div>	
            </div>
                           
            <div class="container_nav_style">	
                <div class="container container_row">
                    <div class="row row_pushdown">
                       
                       <div class="col-md-12 min-pad">
            <h2 class="panel_heading_style"><img src="https://www.biipmi.co/tmspublic/assets/images/trainee.png"/> Public Landing Page</h2>            
            <br>
            <h4 style="text-align:center">Welcome to BIIPMI's training portal. Please click on the links below to navigate to  your training institute.</h4>
            <br/>            
            <div id ='trainee_validation_div' style="text-align: center">
                <div class="bs-example">                    
                    <div class="table-responsive">                        
                        <br>                        
                        <div class="table-responsive">
                                <?php 
                                    foreach ($tenants as $client){ ?>
                                      <h5>                                                                
                                        <a href="<?php echo 'http://'.$client['tenant_url'];?>">
                                            <img style="width: 100px; height:43px; margin-right: 5px" src="http://focus.biipbyte.co/logos/<?php echo $client['tenant_logo'];?>">
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
</html>
<?php exit;?>
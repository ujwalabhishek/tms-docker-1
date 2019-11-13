<!--      <footer>
        <p>Copyright &copy; <?php echo $this->data['tenant_details']->copyrighttext.', All Rights Reserved.'; ?><font style="font-size:9px; color:#ac0000; font-weight:bold; float:right;">powered by biipmi Pte Ltd.</font></p>
      </footer>-->

<div id="footer" style="padding: 10px;margin: auto;box-shadow: 0px -2px 4px  rgba(0, 0, 255, .2); margin: 10px 0 0;">

    <div class="container-fluid bg-4 text-center"> 
        <div class="container bg-3 text-center">
            <div class="row">
                <div class="col-sm-8"><h5  style="text-align: right;color: #999393;">Copyright © 
                    <?php 
                    if(!empty($this->data['tenant_details']->copyrighttext)){
                       echo $this->data['tenant_details']->copyrighttext;
                    }else{
                    echo "BIIPMI 2015-2019";
                    }?>, All Rights Reserved.</h5></div>
                <div class="col-sm-4"><h6 style="text-align: right;color: #999393;">powered by biipmi Pte Ltd</h6></div>

            </div>
        </div>
    </div>
</div>
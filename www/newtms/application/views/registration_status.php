<?php
echo $this->load->view('common/refer_left_wrapper');
?>
<div class="ref_col ref_col_tax_code" style="min-height: 500px;">	
    <div class="container_row">
        <div class="col-md-12 min-pad">                              
            <div id ='trainee_validation_div'>
                <div class="bs-example">                    
                    <div class="table-responsive">
                        <div class="col-md-12 min-pad warng">

                            <div  style="background-color:#CEFADE;padding:10px;">
                                <?php
                                if (!empty($error_message)) {
                                    echo '<div style="color:red;font-weight: bold;">' . $error_message . '</div>';
                                }
                                if (!empty($success_message)) {
                                    echo '<div style="color:green;font-weight: bold;">' . $success_message . '</div>';
                                }
                                ?>     </div>  
                        </div>                            
                    </div>
                </div>                 
            </div>
        </div>
    </div>
</div>    
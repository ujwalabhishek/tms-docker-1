<div style="clear:both;"></div>
<?php $this->load->helper('form'); ?>
        
    <div class="col-md-12"  style="min-height: 390px;">
        <br>
        <?php
            if ($this->session->flashdata('success')) {        
                echo '<div class="success">' .$this->session->flashdata('success'). '</div>';
            }
            if ($this->session->flashdata('error')) {
                echo '<div class="error">' . $this->session->flashdata('error') . '</div>';
            }
            echo validation_errors('<div class="error">', '</div>');
        ?>
        <h2 class="panel_heading_style">Change Password</h2>
	<?php
            $atr = 'id="change_pwd_form" name="change_pwd_form" onsubmit="return(validate());"';
            echo form_open('user/change_password',$atr); 
        ?> 	  
        <div class="bs-example">
            <div class="table-responsive" id='validation_div'>    
                <table class="table table-striped">      
                  <tbody>
                    <tr>
                        <td class="td_heading">Old Password:<span class="required">*</span></td>
                        <td>
                            <?php 
                                $oldpwd =   array('name' => 'old_password',
                                              'id' => 'old_password',
                                              'maxlength' => '15',
                                              'onblur'  => 'javascript:ispassword_exist(this.value,this.id);'
                                          );
                                echo form_password($oldpwd,  set_value('old_password') ); ?>
                         <span id="old_password_err"></span>
                        </td>
                        
                    </tr>

                    
                    <tr>
                        <td class="td_heading">New Password:<span class="required">*</span></td>
                        <td>
                            <?php
                                $pwd =  array('name' => 'new_password',
                                                      'id' => 'new_password',
                                                      'maxlength' => '15'                                
                                        );
                                echo form_password($pwd,  set_value('new_password'));
                            ?>
                            <span id="new_password_err"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Confirm New Password:<span class="required">*</span></td>
                        <td>    
                            <?php
                            $cnpwd= array('name' => 'confirm_password',
                                                  'id' => 'confirm_password',
                                                  'maxlength' => '15',
                                                  'onblur'  => 'javascript:password_matches(this.value);'
                                    );
                            echo form_password($cnpwd);
                            ?>
                            <span id="confirm_password_err"></span>
                        </td>
                    </tr>
                  </tbody>
                </table>
                <span class="required required_i">* Required Fields</span>
                <span style="text-align:center">
                <?php if($success_msg) echo '<h5><font color="green">'.$success_msg.'</font></h5>'; 
                      if($error_msg) echo '<h5><font color="red">'.$error_msg.'</font></h5>'; ?>
                </span>
            </div>
        </div>
<div class="throw_right">
    <button class="btn btn-primary" type="submit">
        <span class="glyphicon glyphicon-retweet"></span>&nbsp;Change Password
    </button>
</div>
<?php echo form_close(); ?>
</div>
<br><br>
<script src="<?php echo base_url(); ?>assets/public_js/change_password.js?v=1" type="text/javascript"></script>
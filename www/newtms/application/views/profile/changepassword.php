<?php
    $atr = 'id="change_pwd_form" name="change_pwd_form"';
    echo form_open("profile/update_password", $atr);    
?>
<div class="col-md-10 col_10_height_other">
    <?php
        if ($this->session->flashdata('success')) {        
            echo '<div class="success">' .$this->session->flashdata('success'). '</div>';
        }
        if ($this->session->flashdata('error')) {
            echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
        }
        echo validation_errors('<div class="error1">', '</div>');
    ?>
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-retweet"></span> Change Password</h2>
    <div class="bs-example">
        <div class="table-responsive">
            <table class="table table-striped">
              <tbody>
                <tr>
                  <td class="td_heading">Old Password:<span class="required">*</span></td>
                  <td>                      
                    <?php 
                        $attr = array('name'=>'old_password',
                            'id'=>'old_password',
                            'placeholder'=>'Old Password',
                            'value' => set_value('old_pwd'),
                            'maxlength'   => '10');
                        echo form_password($attr, '');                                       
                    ?>
                    <span id="old_pwd_err"></span>
                  </td>
                  
                </tr>
                <tr>
                    <td class="td_heading">New Password:<span class="required">*</span></td>
                  <td>                      
                    <?php 
                      $attr = array('name'=>'new_password', 
                          'id'=>'new_password',
                          'placeholder'=>'New Password',
                          'value' => set_value('new_pwd'),
                          'maxlength'   => '10');
                      echo form_password($attr, '');
                    ?>
                    <span id="new_pwd_err"></span>  
                  </td>
                </tr>
                <tr>
                  <td class="td_heading">Confirm New Password:<span class="required">*</span></td>
                  <td colspan="3">                      
                      <?php 
                      $attr = array('name'=>'new_password_confirm', 
                          'id'=>'new_password_confirm',
                          'placeholder'=>'Confirm New Password',
                          'value' => set_value('new_pwd_confirm'),
                          'maxlength'   => '10');
                      echo form_password($attr, '');
                    ?>
                    <span id="new_pwd_confirm_err"></span>  
                  </td>
                </tr>
              </tbody>
            </table>
            <span class="required required_i">* Required Fields</span>
        </div>
    </div>	  
<div class="throw_right">
    <button class="btn btn-primary submit_btn" type="submit">
        <span class="glyphicon glyphicon-retweet"></span>&nbsp;Change Password
    </button>    
</div>
</div>
<?php echo form_close(); ?>
<script src="<?php echo base_url(); ?>assets/js/change_password.js"></script>    
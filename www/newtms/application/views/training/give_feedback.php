<style>
    .modal1_555{
        overflow:scroll !important;
    }
</style>
<div class="modal1_555" id="ex6">
    <?php  
        $atr = 'id="feedbackForm" name="validate_form"';
        echo form_open('trainings/save_feedback',$atr);
    ?> 
    <p>
    <h2 class="panel_heading_style">Your Feedback Form <span class="required_i white">(1. Strongly disagree, 2. Disagree, 3. Neutral, 4. Agree, 5. Strongly agree)</span></h2>
    <table class="table table-striped">
        <?php 
         $options = array(''=>'Select','1'  => '1','2'  => '2','3'  => '3','4'  => '4','5'  =>'5');   
        ?>
        <tbody>
            <tr>
                <td colspan="2"><strong>Your satisfaction rating of the training program:</strong>
                <?php echo form_dropdown('rating', $options);?>
            </tr>
            <tr>
                  <td colspan="2" class="td_heading"><strong><u> <?php echo $feedback['FDBCK01']['category_name']?></u></strong> </td>
            </tr>
            <tr>     
                <td colspan="2">1.<?php echo $feedback['Q01']['category_name'];?>
                    <?php
                        $atr='id="Q01"';
                        echo form_dropdown('Q01', $options,'',$atr);
                    ?>
                </td>
            </tr>
            <tr>     
                <td colspan="2">2.<?php echo $feedback['Q02']['category_name'];?>
                    <?php
                        $atr='id="Q02"';
                        echo form_dropdown('Q02', $options,'',$atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">3.<?php echo $feedback['Q03']['category_name'];?>
                    <?php
                        $atr='id="Q03"';
                        echo form_dropdown('Q03', $options,'',$atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">4.<?php echo $feedback['Q04']['category_name'];?>
                    <?php
                        $atr='id="Q04"';
                        echo form_dropdown('Q04', $options,'',$atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">5.<?php echo $feedback['Q05']['category_name'];?>
                    <?php
                        $atr='id="Q05"';
                        echo form_dropdown('Q05', $options,'',$atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">6.<?php echo $feedback['Q06']['category_name'];?>
                    <?php
                        $atr='id="Q06"';
                        echo form_dropdown('Q06', $options,'',$atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2" class="td_heading"><strong><u><?php echo $feedback['FDBCK02']['category_name']?></u></strong> </td>
            </tr>
            <tr>
                <td colspan="2">1.<?php echo $feedback['Q07']['category_name'];?>:
                    <?php 
                        $atr='id="Q07"';
                        echo form_dropdown('Q07', $options,'',$atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">2.<?php echo $feedback['Q08']['category_name'];?>:
                    <?php 
                    $atr='id="Q08"';
                    echo form_dropdown('Q08', $options,'',$atr);?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">3.<?php echo $feedback['Q09']['category_name'];?>:
                    <?php
                    $atr='id="Q09"';
                    echo form_dropdown('Q09', $options,'',$atr);?>
                </td>
            </tr>
            <tr>
                <td colspan="2">4.<?php echo $feedback['Q10']['category_name'];?>:
                    <?php 
                     $atr='id="Q10"';
                    echo form_dropdown('Q10', $options,'',$atr);?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">5.<?php echo $feedback['Q11']['category_name'];?>:
                    <?php 
                     $atr='id="Q11"';
                    echo form_dropdown('Q11', $options,'',$atr);?>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="td_heading"><strong><u>C. <?php echo $feedback['FDBCK03']['category_name']?></u></strong> </td>
            </tr>

            <tr>
                <td colspan="2">1. <?php echo $feedback['Q12']['category_name'];?>:
                    <?php 
                    $atr='id="Q12"';
                    echo form_dropdown('Q12', $options,'',$atr);?>
                </td>
            </tr>

            <tr>
                <td colspan="2">2.  <?php echo $feedback['Q13']['category_name'];?>:
                    <?php 
                     $atr='id="Q13"';
                    echo form_dropdown('Q13', $options,'',$atr);?>
                </td>
            </tr>

            <tr>
                <td colspan="2">3.  <?php echo $feedback['Q14']['category_name'];?>:
                    <?php
                        $atr='id="Q14"';
                        echo form_dropdown('Q14', $options,'',$atr);
                    ?> 
                </td>
            </tr>

            <tr>
                <td colspan="2">4.  <?php echo $feedback['Q15']['category_name'];?>:
                    <?php 
                        $atr='id="Q15"';
                        echo form_dropdown('Q15', $options,'',$atr);
                    ?> 
                </td>
            </tr>

            <tr>
                <td class="td_heading">Any other remarks:</td>
                <td>
                    <textarea rows="1" cols="60" name="remarks"></textarea>
                    <input type="hidden" name="class_id" value="<?php echo $class_id;?>"/>
                    <input type="hidden" name="course_id" value="<?php echo $course_id;?>"/>
                    <span style="float:right;">

                        <button class="btn btn-primary" type="submit" id="save" style="display: none;">Save</button>

                    </span>
                </td>
            </tr>
     </tbody>
     </table>
  
<?php form_close();?>
  </p>
</div>

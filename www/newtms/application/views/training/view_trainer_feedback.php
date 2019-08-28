<div class="modal modal1_5551" style="height:450px;width: 45%; max-height: 4500px !important;">
    <h2 class="panel_heading_style"><?php echo $heading; ?> </h2>
    <table class="table table-striped">

        <tbody>

            <tr>
                <td colspan="2">1.  <?php echo $tabledata['CERTCOLDT']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['CERTCOLDT']['feedback_answer']) ? '--' : $tabledata['CERTCOLDT']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">2. <?php echo $tabledata['SATSRATE']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['SATSRATE']['feedback_answer']) ? '--' : $tabledata['SATSRATE']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">3. <?php echo $tabledata['CERTCOM1']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['CERTCOM1']['feedback_answer']) ? '--' : $tabledata['CERTCOM1']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">4. <?php echo $tabledata['APPKNLSKL']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['APPKNLSKL']['feedback_answer']) ? '--' : $tabledata['APPKNLSKL']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">5. <?php echo $tabledata['EXPJOBSCP']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['EXPJOBSCP']['feedback_answer']) ? '--' : $tabledata['EXPJOBSCP']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">6. <?php echo $tabledata['RT3MNTHS']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['RT3MNTHS']['feedback_answer']) ? '--' : $tabledata['RT3MNTHS']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">7. <?php echo $tabledata['DTCOMMEMP']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['DTCOMMEMP']['feedback_answer']) ? '--' : $tabledata['DTCOMMEMP']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">8. <?php echo $tabledata['COMYTCOM']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['COMYTCOM']['feedback_answer']) ? '--' : $tabledata['COMYTCOM']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td width="25%">9. <?php echo $tabledata['COMMNTS']['category_name'] ?>: </td>
                <td>    
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['COMMNTS']['feedback_answer']) ? '--' : $tabledata['COMMNTS']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <span style="float:right;">
                        <a href="<?php echo base_url(); ?>trainings/print_trainer_feedback?course_id=<?php echo $course_id ?>&class_id=<?php echo $class_id ?>&user_id=<?php echo $user_id ?>" class="small_text1">
                            <button class="btn btn-primary" type="button">Print</button></a>
                        <a href="#" rel="modal:close">
                            <button class="btn btn-primary" type="button">Close</button>
                        </a>
                    </span>
                </td>
            </tr>

            <tr>
                <td colspan="2"><span class="required_i red">(1. Strongly disagree, 2. Disagree, 3. Neutral, 4. Agree, 5. Strongly agree)</span>
                </td>
            </tr>

        </tbody>
    </table>
</div>
<div class="modal modal1_5551" style="height:600px; max-height: 600px !important;">
    <h2 class="panel_heading_style"><?php echo $heading; ?> '<?php echo $trainee_name; ?>'</h2>
    <table class="table table-striped">

        <tbody>
            <tr>
                <td colspan="2"><span class="required_i red">(1. Strongly disagree, 2. Disagree, 3. Neutral, 4. Agree, 5. Strongly agree)</span>
                </td>
            </tr>
            <tr>
                <td colspan="2"><strong>Your satisfaction rating of the training program:</strong>
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['FDBCK01']['trainee_feedback_rating']) ? '--' : $tabledata['FDBCK01']['trainee_feedback_rating']; ?></span></td>

            </tr>

            <tr>
                <td colspan="2" class="td_heading">
                    <strong><u>A. <?php echo $tabledata['FDBCK01']['category_name'] ?></u></strong></td>
            </tr>

            <tr>
                <td colspan="2">1.  <?php echo $tabledata['Q01']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['Q01']['feedback_answer']) ? '--' : $tabledata['Q01']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">2. <?php echo $tabledata['Q02']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['Q02']['feedback_answer']) ? '--' : $tabledata['Q02']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">3. <?php echo $tabledata['Q03']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['Q03']['feedback_answer']) ? '--' : $tabledata['Q03']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">4. <?php echo $tabledata['Q04']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['Q04']['feedback_answer']) ? '--' : $tabledata['Q04']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">5. <?php echo $tabledata['Q05']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['Q05']['feedback_answer']) ? '--' : $tabledata['Q05']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">6. <?php echo $tabledata['Q06']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['Q06']['feedback_answer']) ? '--' : $tabledata['Q06']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2" class="td_heading">
                    <strong><u>B. <?php echo $tabledata['FDBCK02']['category_name'] ?></u></strong></td>
            </tr>

            <tr>
                <td colspan="2">1. <?php echo $tabledata['Q07']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['Q07']['feedback_answer']) ? '--' : $tabledata['Q07']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">2. <?php echo $tabledata['Q08']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['Q08']['feedback_answer']) ? '--' : $tabledata['Q08']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">3. <?php echo $tabledata['Q09']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['Q09']['feedback_answer']) ? '--' : $tabledata['Q09']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">4. <?php echo $tabledata['Q10']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['Q10']['feedback_answer']) ? '--' : $tabledata['Q10']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">5. <?php echo $tabledata['Q11']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['Q11']['feedback_answer']) ? '--' : $tabledata['Q11']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2" class="td_heading">
                    <strong><u>C. <?php echo $tabledata['FDBCK03']['category_name'] ?></u></strong></td>
            </tr>

            <tr>
                <td colspan="2">1. <?php echo $tabledata['Q12']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['Q12']['feedback_answer']) ? '--' : $tabledata['Q12']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">2. <?php echo $tabledata['Q13']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['Q13']['feedback_answer']) ? '--' : $tabledata['Q13']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">3. <?php echo $tabledata['Q14']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['Q14']['feedback_answer']) ? '--' : $tabledata['Q14']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">4. <?php echo $tabledata['Q15']['category_name'] ?>:
                    <span style="float:right; margin-right:2%;"><?php echo empty($tabledata['Q15']['feedback_answer']) ? '--' : $tabledata['Q15']['feedback_answer']; ?></span>
                </td>
            </tr>

            <tr>
                <td class="td_heading">Any other remarks:</td>
                <td>
                    <?php echo $tabledata['FDBCK01']['trainee_other_remarks']; ?>
                    <span style="float:right;">
                        <a href="<?php echo base_url(); ?>reports/print_trainee_feedback?course_id=<?php echo $course_id ?>&class_id=<?php echo $class_id ?>&user_id=<?php echo $user_id ?>" class="small_text1">
                            <button class="btn btn-primary" type="button">Print</button></a>
                        <a href="#" rel="modal:close">
                            <button class="btn btn-primary" type="button">Close</button>
                        </a>
                    </span>
                </td>
            </tr>

        </tbody>
    </table>
</div>
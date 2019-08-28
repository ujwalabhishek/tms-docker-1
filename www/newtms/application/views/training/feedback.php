<div class="modal modal1_5551">
    <h2 class="panel_heading_style">Trainee Feedback Form</h2>
    <form action="<?php echo base_url();?>trainings/print_feedback_form" method="post">  
    <table class="table table-striped">
     <tbody>
        <tr>
            <td colspan="2"><strong>Your satisfaction rating of the training program:</strong>
                <span style="float:right; margin-right:2%;font-weight: bold;"><?php echo  $classdata['details'][0]['trainee_feedback_rating']; ?></span></td>
        </tr>

        <tr>
            <td colspan="2" class="td_heading">
                <strong><u>A. <?php echo $tabledata['FDBCK01']['category_name'] ?></u></strong></td>
        </tr>

        <tr>
            <td colspan="2">1.  <?php echo $tabledata['Q01']['category_name'] ?>:
                <span style="float:right; margin-right:2%;"><?php echo $tabledata['Q01']['feedback_answer'] ?></span>
            </td>
        </tr>

        <tr>
            <td colspan="2">2. <?php echo $tabledata['Q02']['category_name'] ?>:
                <span style="float:right; margin-right:2%;"><?php echo $tabledata['Q02']['feedback_answer'] ?></span>
            </td>
        </tr>

        <tr>
            <td colspan="2">3. <?php echo $tabledata['Q03']['category_name'] ?>:
                <span style="float:right; margin-right:2%;"><?php echo $tabledata['Q03']['feedback_answer'] ?></span>
            </td>
        </tr>

        <tr>
            <td colspan="2">4. <?php echo $tabledata['Q04']['category_name'] ?>:
                <span style="float:right; margin-right:2%;"><?php echo $tabledata['Q04']['feedback_answer'] ?></span>
            </td>
        </tr>

        <tr>
            <td colspan="2">5. <?php echo $tabledata['Q05']['category_name'] ?>:
                <span style="float:right; margin-right:2%;"><?php echo $tabledata['Q05']['feedback_answer'] ?></span>
            </td>
        </tr>

        <tr>
            <td colspan="2">6. <?php echo $tabledata['Q06']['category_name'] ?>:
                <span style="float:right; margin-right:2%;"><?php echo $tabledata['Q06']['feedback_answer'] ?></span>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="td_heading">
                <strong><u>B. <?php echo $tabledata['FDBCK02']['category_name'] ?></u></strong></td>
        </tr>

        <tr>
            <td colspan="2">1. <?php echo $tabledata['Q07']['category_name'] ?>:
                <span style="float:right; margin-right:2%;"><?php echo $tabledata['Q07']['feedback_answer'] ?></span>
            </td>
        </tr>

        <tr>
            <td colspan="2">2. <?php echo $tabledata['Q08']['category_name'] ?>:
                <span style="float:right; margin-right:2%;"><?php echo $tabledata['Q08']['feedback_answer'] ?></span>
            </td>
        </tr>

        <tr>
            <td colspan="2">3. <?php echo $tabledata['Q09']['category_name'] ?>:
                <span style="float:right; margin-right:2%;"><?php echo $tabledata['Q09']['feedback_answer'] ?></span>
            </td>
        </tr>

        <tr>
            <td colspan="2">4. <?php echo $tabledata['Q10']['category_name'] ?>:
                <span style="float:right; margin-right:2%;"><?php echo $tabledata['Q10']['feedback_answer'] ?></span>
            </td>
        </tr>

        <tr>
            <td colspan="2">5. <?php echo $tabledata['Q11']['category_name'] ?>:
                <span style="float:right; margin-right:2%;"><?php echo $tabledata['Q11']['feedback_answer'] ?></span>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="td_heading">
                <strong><u>C. <?php echo $tabledata['FDBCK03']['category_name'] ?></u></strong></td>
        </tr>

        <tr>
            <td colspan="2">1. <?php echo $tabledata['Q12']['category_name'] ?>:
                <span style="float:right; margin-right:2%;"><?php echo $tabledata['Q12']['feedback_answer'] ?></span>
            </td>
        </tr>

        <tr>
            <td colspan="2">2. <?php echo $tabledata['Q13']['category_name'] ?>:
                <span style="float:right; margin-right:2%;"><?php echo $tabledata['Q13']['feedback_answer'] ?></span>
            </td>
        </tr>

        <tr>
            <td colspan="2">3. <?php echo $tabledata['Q14']['category_name'] ?>:
                <span style="float:right; margin-right:2%;"><?php echo $tabledata['Q14']['feedback_answer'] ?></span>
            </td>
        </tr>

        <tr>
            <td colspan="2">4. <?php echo $tabledata['Q15']['category_name'] ?>:
                <span style="float:right; margin-right:2%;"><?php echo $tabledata['Q15']['feedback_answer'] ?></span>
            </td>
        </tr>

        <tr>
            <td class="td_heading">Any other remarks:</td>

            <td>
                  <?php echo  $classdata['details'][0]['other_remarks_trainee']; ?>
                    
            <input type="hidden" name="class_id" value="<?php echo $classdata['details'][0]['class_id']; ?>"/>
            <input type="hidden" name="course_id" value=" <?php echo  $classdata['details'][0]['course_id']; ?>"/>
     
                      <span style="float:right;">
                 <button class="btn btn-primary" type="submit">Download</button> </span>
            </td>
        </tr>
 
        <tr>
            <td colspan="2"><span class="required_i red">(1. Strongly disagree, 2. Disagree, 3. Neutral, 4. Agree, 5. Strongly agree)</span>
            </td>
        </tr>

        </tbody>
    </table>
           </form>
</div>

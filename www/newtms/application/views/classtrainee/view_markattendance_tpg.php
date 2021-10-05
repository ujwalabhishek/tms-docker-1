<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>

<div class="col-md-10 right-minheight">
    <?php echo validation_errors('<div class="error1">', '</div>'); ?> 
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> TPG Course Session Attendance</h2>
    <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Course Session Attendance Details</h2>
    <div class="table-responsive">
        <?php
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        ?>  
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td colspan="4">                            
                        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Course Details</h2>
                    </td>                           
                </tr>
                <tr class="change_span" style="">
                    <td>                    
                        <b>Course Title. :</b> 
                    </td>   
                    <td>
                        <?php echo $title; ?>
                    </td> 

                    <td>
                        <b>Coure Reference Number:</b> 
                    </td>
                    <td> 
                        <?php echo $referenceNumber; ?>
                    </td>
                </tr>

                <tr class="change_span" style="">
                    <td>                    
                        <b>Course Run ID:</b> 
                    </td>   
                    <td>
                        <?php echo $courseRunId; ?>
                    </td> 

                    <td>
                        <b>External Reference Number.:</b> 
                    </td>
                    <td> 
                        <?php echo $externalReferenceNumber; ?>
                    </td>
                </tr>

                <tr class="change_span" style="">
                    <td>                    
                        <b>Course Start Date.:</b> 
                    </td>   
                    <td>
                        <?php echo $courseStartDate; ?>
                    </td> 

                    <td>
                        <b>Course End Date:</b> 
                    </td>
                    <td> 
                        <?php echo $courseEndDate; ?>
                    </td>
                </tr>
                <tr class="change_span" style="">
                    <td>                    
                        <b>Mode of Training.:</b> 
                    </td>   
                    <td colspan="3">
                        <?php echo $modeOfTraining; ?>
                    </td>                    
                </tr> 
                <tr>
                    <td colspan="4">                            
                        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Session Details</h2>
                    </td>                           
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Session ID:</td>
                    <td colspan="3">
                        <?php echo $sessionId; ?>
                    </td>
                </tr> 
                <tr class="new_span">
                    <td class="td_heading" width="15%">Session Start Date & Time:</td>
                    <td>
                       <?php echo $sessionStartDate . ' ' . $sessionStartTime; ?>
                    </td>
                    <td class="td_heading" width="15%">Session End Date & Time:</td>
                    <td>
                        <?php echo $sessionEndDate . ' ' . $sessionEndTime; ?>
                    </td>
                </tr>        
                <tr>
                    <td>                    
                        <b>Entry Mode.:</b> 
                    </td>   
                    <td>
                        <?php echo $SessionEntryMode; ?>
                    </td>     
                    <td>                    
                        <b>Attendance ID.:</b> 
                    </td>   
                    <td>
                        <?php echo $SessionAttendanceId; ?>
                    </td>  
                </tr> 
                <tr>
                    <td>                    
                        <b>No Of Hours.:</b> 
                    </td>   
                    <td>
                        <?php echo $SessionnumberOfHours; ?>
                    </td>     
                    <td>                    
                        <b>Session To Traqam.:</b> 
                    </td>   
                    <td>
                        <?php echo $SessionsentToTraqom; ?>
                    </td>  
                </tr> 
                <tr>
                    <td>                    
                        <b>Session Status.:</b> 
                    </td>   
                    <td>
                        <?php echo $Sessionstatus; ?>
                    </td>  
                    <td>                    
                        <b>Edited By TP.:</b> 
                    </td>   
                    <td>
                        <?php echo $SessioneditedByTP; ?>
                    </td>  
                    
                </tr> 
                
                
                
                <tr>
                    <td colspan="4">                            
                        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Venue Details</h2>
                    </td>                           
                </tr>
                <tr class="new_span">
                    <td class="td_heading">Venue:</td>
                    <td colspan="3">                        
                        <?php echo $venueBlock . ' ' . $venueBuilding.' Floor : '.$venueFloor.' '.$venuePostalCode .' '.$venueRoom. ' ' .$venueStreet.' Unit : '.$venueUnit; ?>                                                
                    </td>                                    
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Wheel Chair Access</td>
                    <td colspan="3">
                        <?php
                        if ($venueWheelChairAccess == TRUE) {
                            echo "Yes";
                        } else {
                            echo "No";
                        }
                        ?>
                        <span id="tpcode_err"></span>
                    </td>
                </tr> 
                
                
                 <tr>
                    <td colspan="4">                            
                        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Trainee Details</h2>
                    </td>                           
                </tr>
                <tr>
                    <td>                    
                        <b>Account Type.:</b> 
                    </td>   
                    <td>
                        <?php echo $TraineeaccountType; ?>
                    </td>     
                    <td>                    
                        <b>Contact Number.:</b> 
                    </td>   
                    <td>
                        <?php echo $TraineecontactNumber; ?>
                    </td>  
                </tr> 
                 <tr>
                    <td>                    
                        <b>Email.:</b> 
                    </td>   
                    <td>
                        <?php echo $Traineeemail; ?>
                    </td>     
                    <td>                    
                        <b>ID.:</b> 
                    </td>   
                    <td>
                        <?php echo $Traineeid; ?>
                    </td>  
                </tr> 
                <tr>
                    <td>                    
                        <b>ID Type.:</b> 
                    </td>   
                    <td>
                        <?php echo $TraineeidType; ?>
                    </td>     
                    <td>                    
                        <b>Individual ID.:</b> 
                    </td>   
                    <td>
                        <?php echo $TraineeindividualId; ?>
                    </td>  
                </tr> 
                <tr>
                    <td>                    
                        <b>Name.:</b> 
                    </td>   
                    <td>
                        <?php echo $Traineename; ?>
                    </td>     
                    <td>                    
                        <b>Survey Language.:</b> 
                    </td>   
                    <td>
                        <?php echo $TraineesurveyLanguageCode; ?>
                    </td>  
                </tr> 
                
                
                <tr class="new_span">
                    <td colspan="4" class="no-bg">
                        <div class="push_right">
                            <a href="<?php echo base_url() . 'class_trainee/mark_attendance_tpg'; ?>" class="small_text1"><span class="label label-default black-btn">Back</span></a>
                        </div>
                    </td>
                </tr>                
            </tbody>
        </table>        
    </div>
</div>
<!-- end abdulla -->
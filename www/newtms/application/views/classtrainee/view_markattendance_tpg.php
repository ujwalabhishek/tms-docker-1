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
                        <b>Course Title. :<span class="required">*</span></b> 
                    </td>   
                    <td>
                        <input type="text" name="change_taxcode_autocomplete" id="nric" style="" value='<?php echo $title; ?>' disabled="disabled"/>
                        <span id="nric_err"></span>
                    </td> 

                    <td>
                        <b>Coure Reference Number:<span class="required">*</span></b> 
                    </td>
                    <td> 
                        <input type="text" name="fullname" id="fullname" style="" value='<?php echo $referenceNumber; ?>' disabled="disabled"/>
                        <span id="fullname_err"></span>
                    </td>
                </tr>

                <tr class="change_span" style="">
                    <td>                    
                        <b>Course Run ID:<span class="required">*</span></b> 
                    </td>   
                    <td>
                        <input type="text" name="change_taxcode_autocomplete" id="nric" style="" value='<?php echo $courseRunId; ?>' disabled="disabled"/>
                        <span id="nric_err"></span>
                    </td> 

                    <td>
                        <b>External Reference Number.:<span class="required">*</span></b> 
                    </td>
                    <td> 
                        <input type="text" name="fullname" id="fullname" style="" value='<?php echo $externalReferenceNumber; ?>' disabled="disabled"/>
                        <span id="fullname_err"></span>
                    </td>
                </tr>

                <tr class="change_span" style="">
                    <td>                    
                        <b>Course Start Date.:<span class="required">*</span></b> 
                    </td>   
                    <td>
                        <input type="text" name="change_taxcode_autocomplete" id="dob" style="" value='<?php echo $courseStartDate; ?>' disabled="disabled"/>
                        <span id="dob_err"></span>
                    </td> 

                    <td>
                        <b>Course End Date:<span class="required">*</span></b> 
                    </td>
                    <td> 
                        <input type="text" name="fullname" id="contactno" style="" value='<?php echo $courseEndDate; ?>' disabled="disabled"/>
                        <span id="contactno_err"></span>
                    </td>
                </tr>
                <tr class="change_span" style="">
                    <td>                    
                        <b>Mode of Training.:<span class="required">*</span></b> 
                    </td>   
                    <td>
                        <input type="text" name="change_taxcode_autocomplete" id="dob" style="" value='<?php echo $modeOfTraining; ?>' disabled="disabled"/>
                        <span id="dob_err"></span>
                    </td>                    
                </tr> 
                <tr>
                    <td colspan="4">                            
                        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Session Details</h2>
                    </td>                           
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Session ID:<span class="required">*</span></td>
                    <td>
                        <input type="text" name="tpuen" id="tpuen" style="" value='<?php echo $sessionId; ?>' disabled="disabled"/>
                        <span id="tpuen_err"></span>
                    </td>
                </tr> 
                <tr class="new_span">
                    <td class="td_heading" width="15%">Session Start Date & Time:<span class="required">*</span></td>
                    <td>
                        <input type="text" name="tpcode" id="tpcode" style="" value='<?php echo $sessionStartDate . ' ' . $sessionStartTime; ?>' disabled="disabled"/>
                        <span id="tpcode_err"></span>
                    </td>
                    <td class="td_heading" width="15%">Session End Date & Time:<span class="required">*</span></td>
                    <td>
                        <input type="text" name="tpuen" id="tpuen" style="" value='<?php echo $sessionEndDate . ' ' . $sessionEndTime; ?>' disabled="disabled"/>
                        <span id="tpuen_err"></span>
                    </td>
                </tr>                               
                <tr>
                    <td colspan="4">                            
                        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> Venue Details</h2>
                    </td>                           
                </tr>
                <tr class="new_span">
                    <td class="td_heading">Venue:<span class="required">*</span></td>
                    <td>                       
                        <textarea type="text" name="course" id="crefno" style="" disabled="disabled"/>
                        <?php echo $venueBlock . ' ' . $venueBuilding; ?></br>
                        <?php echo $venueFloor; ?></br>
                        <?php echo $venuePostalCode; ?></br>
                        <?php echo $venueRoom; ?></br>
                        <?php echo $venueStreet; ?></br>
                        <?php echo $venueUnit; ?></br>
                        </textarea>
                        <span id="crefno_err"></span>
                    </td>                                    
                </tr>
                <tr class="new_span">
                    <td class="td_heading" width="15%">Wheel Chair Access<span class="required">*</span></td>
                    <td>
                        <?php
                        if ($venueWheelChairAccess) {
                            echo "Yes";
                        } else {
                            echo "No";
                        }
                        ?>
                        <span id="tpcode_err"></span>
                    </td>
                </tr> 
                <tr class="new_span">
                    <td colspan="4" class="no-bg">
                        <div class="push_right">
                            <a href="<?php echo base_url() . 'class_trainee'; ?>" class="small_text1"><span class="label label-default black-btn">Back</span></a>
                        </div>
                    </td>
                </tr>                
            </tbody>
        </table>        
    </div>
</div>
<!-- end abdulla -->
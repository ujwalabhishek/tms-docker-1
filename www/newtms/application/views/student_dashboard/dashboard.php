
<div style="clear:both;"></div>        
<!--<div class="col-md-2 col_2_style" >
      <br>
      
    <ul class="ad">
        <li><div id="datepicker" class="date_top"></div></li>
        <li><a href="#" class="thumbnail"><img src="<?php echo base_url(); ?>assets/images/ad1.jpg" style="display: block;" data-src="holder.js/100%x180" alt="100%x180"></a></li>
        <li><a href="#" class="thumbnail"><img src="<?php echo base_url(); ?>assets/images/ad2.jpg" style="display: block;" data-src="holder.js/100%x180" alt="100%x180"></a></li>
    </ul>
      Referral list
        
</div>-->
<div class="col-md-12 col_12_height_other pad" style="min-height: 390px;"> 
    <div class="col-md-7 pad">   
        <!--Referral list-->
        <div class="col-md-12">
            <div class="paper-curl">
                <h2 class="panel_heading_style">Enrollments List<span class="badge"><?php echo count($referrals); ?></span></h2>
                <div class="table-scrol">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th></th>
                                <th>Name </th>
                                <th>Class Name</th>
                              
                                <th>Relation</th>
                            </tr>
                            <?php 
                            if (!empty($referrals)) 
                            { ?>
                                <?php 
                                foreach ($referrals as $referral) 
                                { ?>
                                    <tr>
                                        <td width="5%"><img src="<?php echo base_url(); ?>assets/images/friend-list.png" width="16" height="15"></td>
                                        <!--code modification starts here, author:  date: 05/03/2015, reason: referer link changed-->
                                        <td width="25%"><a href="<?php echo base_url(); ?>user/view_trainee/<?php echo $referral['user_id']; ?>">
                                            <?php echo $referral['first_name'] . ' ' . $referral['last_name']; ?></a></td>
                                        
                                        </td>
                                        <td width="60%"><?php if($referral['class_id']==NULL){ echo "----------";}else{?> 
                                             <?php echo $referral['class_name'];?>&nbsp;&nbsp;&nbsp;
                                            <a href="#ex<?php echo $referral['class_id'];?>" rel="modal:open" class="small_text1">
                                               View Details
                                            </a>
                                            <?php }?>
                                         </td>
                                        <td width="10%"><?php echo ucwords(strtolower($referral['user_friend'])); ?></td>
                                        
                                    </tr>
                                <?php
                                }
                            } 
                            else 
                            {
                            ?>
                                <div class='error' style="text-align:center">
                                    <label class="no-result"><img src="<?php echo base_url(); ?>assets/images/no-result.png">Referral List is empty.</label>
                                </div>  
                            <?php 
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--end  referral list -->
    </div>
    <div class="col-md-5 pad">   
        <!--Referral list-->
        <div class="col-md-12">
            <div class="paper-curl">
                <h2 class="panel_heading_style">Registered List<span class="badge"><?php 
               
                echo count($referral1); ?></span></h2>
                <div class="table-scrol">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th></th>
                                <th>Name </th>
                                <th>Relation</th>
                            </tr>
                            <?php 
                            if (!empty($referral1)) 
                            { ?>
                                <?php 
                                foreach ($referral1 as $referral1) 
                                { ?>
                                    <tr>
                                        <td width="5%"><img src="<?php echo base_url(); ?>assets/images/friend-list.png" width="16" height="15"></td>
                                        <!--code modification starts here, author:  date: 05/03/2015, reason: referer link changed-->
                                        <td width="25%"><a href="<?php echo base_url(); ?>user/view_trainee/<?php echo $referral1['user_id']; ?>">
                                            <?php echo $referral1['first_name'] . ' ' . $referral1['last_name']; ?></a></td>
                                        
                                        </td>
                                        <td width="10%"><?php echo ucwords(strtolower($referral1['user_friend'])); ?></td>
                                        
                                    </tr>
                                <?php
                                }
                            } 
                            else 
                            {
                            ?>
                                <div class='error' style="text-align:center">
                                    <label class="no-result"><img src="<?php echo base_url(); ?>assets/images/no-result.png">Referral List is empty.</label>
                                </div>  
                            <?php 
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--end  referral list -->
    </div>
</div>
<!-- pop up div start -->
 <?php 
    foreach ($referrals as $referral) 
    { ?>
        <div class="modalnew modal13" id="ex<?php echo $referral['class_id'];?>" style="display:none;">
            <h2 class="panel_heading_style"> Class Details </h2>
            <div class="class_desc_course">
                <table class="table table-striped">                                                                                 
                    <tr>
                        <td width="40%"><span class="crse_des">Class Code. :</span></td>
                        <td><?php echo $referral['class_id']; ?></td>
                    </tr>
                    <tr>
                        <td><span class="crse_des">Class Name :</span></td>
                        <td><?php echo $referral['class_name']; ?></td>
                    </tr>
                    <tr>
                        <td><span class="crse_des">Class Start Date and Time :</span></td>
                        <td><?php echo date('d/m/Y h:i A', strtotime($referral['class_start_datetime'])); ?></td>
                    </tr>
                    <tr>
                        <td><span class="crse_des">Class End Date and Time :</span></td>
                        <td><?php echo date('d/m/Y h:i A', strtotime($referral['class_end_datetime'])); ?></td>
                    </tr>
                    <tr>
                        <td><span class="crse_des">Class Room Location :</span></td>
                        <td><?php echo $status_lookup_location[$referral['classroom_location']]; ?></td>
                    </tr>
                    <tr>
                        <td><span class="crse_des">Class Language :</span></td>
                        <td><?php echo $status_lookup_language[$referral['class_language']]; ?></td>
                    </tr>

                    <tr>
                        <td><span class="crse_des">Class Status: </span></td>
                        <td style="height:25px;">
                          
                            <?php
                           
                            $start =  strtotime($referral['class_start_datetime']);
                            $end = strtotime($referral['class_end_datetime']);
                            $cur_date = strtotime(date("Y-m-d H:i:s"));
                            if($status_lookup_class_status[$referral['class_status']] == 'COMPLTD'){
                                echo 'Completed';
                            }
                            elseif ($start > $cur_date && $end > $cur_date)
                            {
                                echo 'Yet to Start';
                            }
                            else if ($start <= $cur_date && $end >= $cur_date)
                            {
                                echo 'In-Progress';
                            }
                            elseif ($end < $cur_date && $start < $cur_date)
                            {
                                echo 'Completed';
                            }
                            
//                            echo $status_lookup_class_status[$referral['class_status']];

                            ?>
                        </td>
                    </tr>
                </table>

            </div>
            <div class="popup_cancel11">
                <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Close</button></a>
            </div>
        </div>
 <?php }?>


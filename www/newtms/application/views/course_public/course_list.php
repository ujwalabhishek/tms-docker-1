
        <div class="col-md-2 col_2_style">
              <br>
            <ul class="ad">
                <li><div id="datepicker" class="date_top"></div></li>
                <li><a href="#" class="thumbnail"><img src="<?php echo base_url(); ?>assets/images/ad1.jpg" style="display: block;" data-src="holder.js/100%x180" alt="100%x180"></a></li>
                <li><a href="#" class="thumbnail"><img src="<?php echo base_url(); ?>assets/images/ad2.jpg" style="display: block;" data-src="holder.js/100%x180" alt="100%x180"></a></li>
            </ul>
        </div>                

        <!--<div style="clear:both;"></div>-->
        <div class="col-md-10">
             <div class="table-responsive">
        <?php
        $attr = 'onsubmit = "return validate_search()" method="GET"';
        echo form_open('course_public/course_list', $attr);
        ?>
        <table class="table table-striped" <?php echo $form_style; ?> >
            <tbody>
                <tr>
                    <td width="30%" class="td_heading">Search on Course Name:</td>
                    <td colspan="3" width="35%"><input class="inputsearchbox_course" type="text" name="search_value" id="course_name" value="<?php echo ($this->input->get('search_value')) ? $this->input->get('search_value') : $search_value; ?>" placeholder="Course Name"><span id="crse_id"></span>
                    </td>
                    <td width="35%" align="center">                        
                        <button title="Search" value="Search" type="submit" class="btn btn-sm btn-info"><span class="glyphicon glyphicon-search"></span> <strong>Search</strong></button>
                        <a href="<?php echo site_url(); ?>course_public" style="text-decoration:none !important; color:#000;" title="All" id="srch_all" class="btn btn-sm btn-info">
                            <span class="glyphicon glyphicon-refresh"></span> <strong>All</strong>
                        </a>
                     </td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
            <br>
            <h2 class="panel_heading_style">Available Courses and Classes</h2>
            <div style="width:100%; margin:0 auto;"> 
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <?php
                            $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                            $pageurl = $controllerurl;
                            ?> 
                            <tr>
                                <th width="14%" class=""><a href="<?php echo base_url() . $pageurl . "?f=course_id&o=" . $ancher; ?>" >Course Code</a></th>
                                <th width="16%" class=""><a href="<?php echo base_url() . $pageurl . "?f=crse_name&o=" . $ancher; ?>" >Course Name</a></th>
                                <th width="11%" class=""><a href="<?php echo base_url() . $pageurl . "?f=certi_level&o=" . $ancher; ?>" >Certification Level</a></th>
                                <th width="11%" class=""><a href="<?php echo base_url() . $pageurl . "?f=crse_duration&o=" . $ancher; ?>" >Duration(hrs)</a></th>
                                <th width="13%" class=""><a href="<?php echo base_url() . $pageurl . "?f=language&o=" . $ancher; ?>" >Language</a></th>
                                <th width="23%" class=""><a href="<?php echo base_url() . $pageurl . "?f=pre_requisite&o=" . $ancher; ?>" >Course Type</a></th>
                                <th width="23%" class=""><a href="<?php echo base_url() . $pageurl . "?f=pre_requisite&o=" . $ancher; ?>" >Check</a></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (count($tabledata) > 0) {
                                foreach ($tabledata as $course):
                                    ?>
                                    <tr>
                                        <td>
<!--                                            <a href="<?php echo site_url(); ?>course_public/course_class_schedule/<?php echo $course['course_id']; ?>"><?php echo $course['course_id']; ?></a>-->
                                           <?php echo $course['course_id']; ?>
                                        </td>
                                        <td><?php echo $course['crse_name']; ?></td>
                                        <td><?php if(!empty($course['certi_level'])){echo $cert_level=get_catname_by_parm($course['certi_level']);} 
                                        else{
                                            echo '-';
                                        }
                                        ?></td>
                                        <td><?php echo $course['crse_duration']; ?></td>
                                        <td><?php
                                            $lang_names = explode(",", $course['language']);
                                            $lan_names = '';
                                                foreach ($lang_names as $lang_name):
                                                $lan_names .= $status_lookup_language[trim($lang_name)] . ", ";
                                            endforeach;
                                            if ($lan_names) {
                                                            echo substr($lan_names, 0, -2);
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>

                                                    </td>
                                                    <td>
                                                        <div >
                                                        <?php 
                                                        echo      $status_lookup_course_type[$course['crse_type']];
                                                        ?>
                                                      </div>

                                                    </td>
<!--                                                    <td>
                                                        <a href="<?php echo site_url(); ?>course_public/course_class_schedule/<?php echo $course['course_id']; ?>">
                                                            Check Schedule</a>
                                                    </td>-->
                                                    <td>
                                                        
                                                        <?php
                                                        if(in_array($course['course_id'],$course_id))
                                                        {?>
                                                        <a style="margin-top: 4px;" href="<?php echo site_url(); ?>course_public/course_class_schedule/<?php echo $course['course_id']; ?>" class="small_text1 pull-right btn1">
                                                            <span class="label label-default black-btn"><span class="glyphicon glyphicon-ok"></span> Check Schedule</span>
                                                        </a>
                                                        <?php 
                                                        }else{?>
                                                        <br/>
                                                         <a> <span  class="label label-default disable-btn"><span class="glyphicon glyphicon-ok"></span> Check Schedule</span></a> 
                                                         <br/><br/> <span style="color: red"> No Class Available </span>
                                                        <?php }
                                                        ?>
                                                    </td>
                                                </tr>
    <?php endforeach;
}
?>
                                    </tbody>
                                </table>
                            </div>  
                            <div style="clear:both;"></div>
                            <ul class="pagination pagination_style"><?php echo $pagination; ?></ul>
                        </div>
                    </div>
              
      

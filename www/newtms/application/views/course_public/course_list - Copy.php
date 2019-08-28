                <div class="row row_pushdown">
                    <div style="clear:both;"></div>
                    <div class="col-md-12">
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
                                            <th width="14%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=course_id&o=" . $ancher; ?>" >Course Code</a></th>
                                            <th width="16%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=crse_name&o=" . $ancher; ?>" >Course Name</a></th>
                                            <th width="11%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=certi_level&o=" . $ancher; ?>" >Course Level</a></th>
                                            <th width="11%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=crse_duration&o=" . $ancher; ?>" >Duration(hrs)</a></th>
                                            <th width="13%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=language&o=" . $ancher; ?>" >Language</a></th>
                                            <th width="23%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=pre_requisite&o=" . $ancher; ?>" >Pre-requisites</a></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (count($tabledata) > 0) {
                                            foreach ($tabledata as $course):
                                                ?>
                                                <tr>
                                                    <td><a href="<?php echo site_url(); ?>course/course_class_schedule/<?php echo $course['course_id']; ?>"><?php echo $course['course_id']; ?></a></td>
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
                                                        <?php if($course['pre_requisite']){
                                                        $all_course_name = explode(",", $course['pre_requisite']);
                                                         $course_names = '';
                                                         foreach ($all_course_name as $course_id):
                                                           $course_names .= $status_lookup_course_name[trim($course_id)] . ", ";
                                                        endforeach;
                                                        if ($course_names) {
                                                            echo substr($course_names, 0, -2);
                                                        } else {
                                                            echo '-';
                                                        }
                                                        }else {
                                                            echo '-';
                                                        }
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
                </div>
      

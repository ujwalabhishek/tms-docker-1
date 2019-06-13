<script>
    var export_url = '<?php echo base_url() . "trainee/download_import_xls/"; ?>';
    var files = '<?php echo $files; ?>'
    var filesa = '<?php echo $filesa; ?>'
    var filesb = '<?php echo $filesb; ?>'
    var baseurl = '<?php echo base_url(); ?>';
    var disable_arr = ['#class','#trainers'];
    var form_check;
    $(document).ready(function(){
        disable_ids(disable_arr,'empty_only');
        $('#course').change(function() {
            var cls = $('#class');
            $.ajax({
                type: 'get',
                url: '<?php echo base_url(); ?>' + 'reports/get_classes_for_certificate_course',
                data: {courseId: $('#course').val()},
                dataType: "json",
                beforeSend: function() {
                    disable_ids(disable_arr,'do_empty');
                },
                success: function(res) {
                    if (res.data != '') {
                        $.each(res.data, function(i, item) {
                            cls.append('<option value="' + item.class_id + '">' + item.class_name + '</option>');
                        });
                        disable_ids(['#class'],'enable');
                    } 
                }
            });
        }); 
        $('#class').change(function() {
            var trainers = $('#trainers');
            $.ajax({
                type: 'get',
                url: '<?php echo base_url(); ?>' + 'class_trainee/get_classtrainer',
                data: {class: $('#class').val()},
                dataType: "json",
                beforeSend: function() {
                    disable_ids(['#trainers'],'do_empty');
                },
                success: function(res) {
                    if (res.data != '') {
                        $.each(res.data, function(i, item) {
                            trainers.append('<option value="' + item.user_id + '">' + item.trainer_name + '</option>');
                        });
                        disable_ids(['#trainers'],'enable');
                    } 
                }
            });
        }); 
        $('#upd_tnr_fdbk_frm').submit(function(){
            form_check = 1;
            return form_validate(true);
        });
        $('#upd_tnr_fdbk_frm select, #upd_tnr_fdbk_frm input').change(function(){
            if(form_check){
                return form_validate(false);
            }
        });
        $('#filter_status').change(function() {
            var val = $(this).val();
            if (val == 'Success') {
                $('tr.danger').hide();
                $('tr.nodanger').show();
                $('.export_but').attr('href', export_url + filesa);
            } else if (val == 'Failure') {
                $('tr.danger').show();
                $('tr.nodanger').hide();
                $('.export_but').attr('href', export_url + filesb);
            } else {
                $('tr.danger').show();
                $('tr.nodanger').show();
                $('.export_but').attr('href', export_url + files);
            }
        });
    });
    function form_validate(retval){
        var course = $('#course').val();
        var cls = $('#class').val();
        var trainers = $('#trainers').val();
        var upload = $('#upload').val();
        var ext = upload.split('.').pop().toLowerCase();
        if (course.length == 0) {
            disp_err('#course');
            retval = false;
        } else {
            remove_err('#course');
        }
        if (cls.length == 0) {
            disp_err('#class');
            retval = false;
        } else {
            remove_err('#class');
        }
        if (trainers.length == 0) {
            disp_err('#trainers');
            retval = false;
        } else {
            remove_err('#trainers');
        }
        if (upload.length == 0) {
            disp_err('#upload');
            retval = false;
        } else if ($.inArray(ext, ['xls', 'xlsx']) == -1) {
            disp_err('#upload', '[Please upload only XLS.]');
            $('#upload').val('');
            retval = false;
        } else {
            remove_err('#upload');
        }
        return retval;
    }
    function disp_err($id, $text) {
        $text = typeof $text !== 'undefined' ? $text : '[required]';
        $($id).addClass('error');
        $($id + '_err').addClass('error').addClass('error_text').html($text);
    }
    function remove_err($id) {
        $($id).removeClass('error');
        $($id + '_err').removeClass('error').text('');
    }
    function disable_ids(ids,condition){
        condition = typeof condition !== 'undefined' ? condition : '';
        var id;
        var element;
        for(value in ids){
            id = ids[value];
            element = $(id);
            if(condition == 'empty_only'){
                if(element.children('option').size() <= 1){
                    element.attr('disabled','disabled');
                }
            } else if (condition == 'do_empty'){
                element.html('<option value="">Select</option>');
                element.attr('disabled','disabled');
            } else if (condition == 'enable'){
                element.removeAttr('disabled');
            } else {
                element.attr('disabled','disabled');
            }
        }
    }
</script>
<div class="col-md-10">
    <?php
    $atr = 'id="upd_tnr_fdbk_frm" name="upd_tnr_fdbk_frm" method="post"';
    echo form_open_multipart("class_trainee/update_trainer_feedback", $atr);
    ?> 
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Class Trainer Feedback</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="20%" class="td_heading">Select Course:<span class="required">*</span></td>
                    <td width="30%">
                        <?php
                        $options = array();
                        $options[''] = 'Select';
                        foreach ($courses as $k => $v) {
                            $options[$k] = $v;
                        }
                        echo form_dropdown('course', $options, $this->input->post('course'), 'id="course"')
                        ?>
                        <span id="course_err"></span>
                    </td>
                    <td width="20%" class="td_heading">Select Class:<span class="required">*</span></td>
                    <td width="30%"><?php
                        $options = array();
                        $options[''] = 'Select';
                        foreach ($classes as $k => $v) {
                            $options[$v->class_id] = $v->class_name;
                        }
                        echo form_dropdown('class', $options, $this->input->post('class'), 'id="class"')
                        ?>
                        <span id="class_err"></span>
                    </td>
                </tr>
                <tr>
                    <td width="20%" class="td_heading">Select Trainer:<span class="required">*</span></td>
                    <td colspan="3">
                        <?php
                        $options = array();
                        $options[''] = 'Select';
                        foreach ($trainers as $k => $row) {
                            $options[$row->user_id] = $row->first_name . ' ' . $row->last_name;
                        }
                        echo form_dropdown('trainers', $options, $this->input->post('trainers'), 'id="trainers"')
                        ?>
                        <span id="trainers_err"></span>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Import Trainer Feedback:<span class="required">*</span></td>
                    <td><?php
                        ?>
                        <input name="userfile" type="file" id="upload">
                        <br>
                        <span id="upload_err"></span>
                    </td>
                    <td><span>
                            <button name="upload" type="submit" id="submit" class="btn btn-xs btn-primary no-mar" value="upload"/><span class="glyphicon glyphicon-upload"></span> Upload</button>
                            (xls or xlsx)
                        </span></td>
                    <td><a href="<?php echo base_url() . 'uploads/Trainer_feedback.xls'; ?>"><span class="label label-default black-btn pull-right"><span class="glyphicon glyphicon-download-alt"></span> Download Import XLS</span></a></td>
                </tr>

            </tbody>
        </table>
    </div>
    <?php echo form_close(); ?>
    <div style="clear:both;"></div><br>
    <?php
    if (!empty($details)) {
        ?>
        <div class="panel-heading panel_headingstyle" style="margin-top:0px;"><strong>Filter On Import Status:</strong> <select id="filter_status">
                <option>All</option>
                <option>Success</option>
                <option>Failure</option>
            </select>
        </div>
    <?php } ?>
    <div style="clear:both;"></div>
    <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-import"></span> Import Preview
        <?php
        if (!empty($details)) {
            ?>
            <div class="add_button">
                <span class="label label-default black-btn pull-right">
                    <a href="<?php echo base_url() . "trainee/download_import_xls/" . $files; ?>" class="small_text1 export_but">
                        <span class="glyphicon glyphicon-export"></span> Export to XLS
                    </a>
                </span>
            </div>  
        <?php } ?>
    </h2>
    <div class="excel">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>NRIC/FIN No.</th>
                        <th>Trainee Name</th>
                        <th>Overall Rating</th>
                        <th>Status</th>
                        <th>Failure Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($details as $k => $data) {
                        $class = 'nodanger';
                        $status_text = 'Success';
                        $status_color = 'green';
                        if($data['status'] == 'FAILED'){
                            $class = 'danger';
                            $status_text = 'Fail.';
                            $status_color = 'red';
                        }
                        ?>
                        <tr class="<?php echo $class; ?>">
                            <td><?php echo $data['taxcode']; ?> </td>
                            <td> <?php echo $data['fullname']; ?></td>
                            <td><?php echo $data['rating']; ?> </td>
                            <td> <?php echo "<font color='$status_color'> $status_text </font>"; ?></td>
                            <td> <?php echo $data['failure_reason']; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div style="clear:both;"></div><br>
    <span class="required required_i">* Required Fields</span>
</div>
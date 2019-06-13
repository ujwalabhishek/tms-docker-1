<div class="col-md-10">
    <?php
        if ($this->session->flashdata('success_message')) {
            echo '<div class="success">' . $this->session->flashdata('success_message') . '!</div>';
        }
    ?>
    <h2 class="panel_heading_style"><img src="<?php  echo base_url(); ?>/assets/images/notification1.png"> Settings - Notifications and Announcements</h2>
    <?php
        $atr = array('id' => 'notifications_list_form', 'method' => 'get');
        echo form_open("settings/notifications", $atr);
    ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <script>
                var tms = tms || {};
                tms.notifications = {
                    runSearch: function(){
                        var form = $("#notifications_list_form");
                        form.find('input[name="page_number"]').val(1);
                        button = form.find('input[type="button"],button');
                        button.attr('disabled','disabled').html('Please Wait..');
                        form.submit();
                    },
                    showViewMoreDialog: function(text){                        
                        $('#view_notification_details').modal().find(".modal-body").html(text);
                    },
                    openAddNotificationDialog: function(){
                        var dialog = $('#add_notification_dialog');
                        dialog.find('h2').html('Add New Notification / Announcement');
                        dialog.find('form')[0].reset();
                        dialog.modal();
                        $('select[name="noti_type"]').trigger('change');
                        $('#notification_id').val("");
                        $("#broadcast_to").datepicker("option", "minDate", 0);
                        $("#broadcast_to").datepicker("option", "maxDate", -1);
                    },
                    openEditNotificationDialog: function(notification_id){
                        var data = {notification_id: notification_id};
                        $.getJSON('<?php echo site_url('settings/get_notification');?>', data, function(data){
                            var currentDate = new Date();
                            $("#broadcast_to").datepicker("option", "minDate", currentDate);
                            $("#broadcast_to").datepicker("option", "maxDate", "");
                            var dialog = $('#add_notification_dialog');
                            dialog.find('h2').html('Update Notification / Announcement');
                            dialog.find('input[name="notification_id"]').val(data.notification_id);
                            dialog.find('textarea[name="noti_msg_txt"]').val(data.noti_msg_txt);
                            dialog.find('input[name="broadcast_from"]').val(data.broadcast_from);
                            dialog.find('input[name="broadcast_to"]').val(data.broadcast_to);
                            dialog.find('select[name="noti_type"]').val(data.noti_type);
                            dialog.find('select[name="broadcast_user_type"]').val(data.broadcast_user_type);
                            if (data.broadcast_user_list) {
                                dialog.find('input[name="broadcast_user_list"]').select2('val', data.broadcast_user_list.split(','));
                            }
                            $('select[name="noti_type"]').trigger('change');
                            dialog.modal();
                        });
                    },
                    openCopyNotificationDialog: function(nodification_id, broadcast_from, broadcast_to){                        
                        tms.notifications.selectedNotificationId = nodification_id;
                        if (broadcast_from < tms.utils.formatDateMysql(new Date())) {
                            broadcast_from = tms.utils.formatDateSingapore(new Date());
                        } else {
                            broadcast_from = tms.utils.formatDateSingapore(new Date(broadcast_from));
                        }                         
                        if (broadcast_to && broadcast_to <= tms.utils.formatDateMysql(new Date())) {
                            broadcast_to = tms.utils.formatDateSingapore(new Date());
                        } else if (broadcast_to) {
                            broadcast_to = tms.utils.formatDateSingapore(new Date(broadcast_to));
                        }                        
                        $("#copy_broadcast_to").datepicker("option", "minDate", broadcast_from);
                        $('#copy_broadcast_from').val(broadcast_from);
                        $('#copy_broadcast_to').val(broadcast_to);

                        $('#copy_notification_dialog').modal();
                    },
                    doCopyNotification: function(){
                        var copy_broadcast_to = $('#copy_broadcast_to').val();
                        if(copy_broadcast_to !="" && isDate(copy_broadcast_to)==false ){
                            $("#copy_broadcast_to_err").text("[invalid format (dd/mm/yyyy)]").addClass('error');
                            return false;
                        }
                        var params = {
                            notification_id: tms.notifications.selectedNotificationId,
                            copy_broadcast_from: $('#copy_broadcast_from').val(),
                            copy_broadcast_to: $('#copy_broadcast_to').val()
                        };
                        $.getJSON("<?php echo site_url('settings/copy_notification');?>", params, function(data){
                            document.location.reload();
                        })
                    },
                    sortBy: function(field){
                        var sf = $('#notifications_list_form').find('input[name="sort_field"]');
                        var so = $('#notifications_list_form').find('input[name="sort_order"]');
                        if (sf.val() == field) {
                            so.val() == 'asc' ? so.val('desc') : so.val('asc');
                        } else {
                            sf.val(field);
                            so.val('asc');
                        }
                        $('#notifications_list_form')[0].submit();
                    },
                    setPage: function(pageNumber){
                        $('#notifications_list_form').find('input[name="page_number"]').val(pageNumber);
                        $('#notifications_list_form')[0].submit();
                    }
                }
            </script>
            <script>
                $(function(){
                    $("#start_date").datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        changeYear: true,
                        onClose: function(selectedDate){
                            $("#end_date").datepicker("option", "minDate", selectedDate);
                        }
                    });
                    $("#end_date").datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        changeYear: true,
                        onClose: function(selectedDate){
                            $("#start_date").datepicker("option", "maxDate", selectedDate);
                        }
                    });
                    $("#broadcast_from").datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        minDate : 0,
                        onClose: function(selectedDate){                            
                            if(selectedDate != '') {
                                selectedDate = check_broadcastfrom_or_today();
                                $("#broadcast_to").datepicker("option", "minDate",selectedDate);
                                $("#broadcast_to").datepicker("option", "maxDate","");
                            }
                        }
                    });
                    $("#broadcast_to").datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        minDate :  0,
                        maxDate : -1,
                        onClose: function(selectedDate){
                            $broadcast_from = $('#broadcast_from').val();
                            $("#broadcast_from").datepicker("option", "maxDate", selectedDate);
                            $('#broadcast_from').val($broadcast_from);
                        }
                    });
                    $("#copy_broadcast_from").datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        minDate : 0,
                        onClose: function(selectedDate){
                            $("#copy_broadcast_to").datepicker("option", "minDate", selectedDate);
                        }
                    });
                    $("#copy_broadcast_to").datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        onClose: function(selectedDate){
                            $("#copy_broadcast_from").datepicker("option", "maxDate", selectedDate);
                        }
                    });
                    $("#broadcast_user_list").select2({
                        placeholder: "Search for a user",
                        minimumInputLength: 3,
                        multiple: true,
                        ajax: {
                            url: "<?php echo site_url('settings/search_trainees_by_name');?>",
                            dataType: 'json',
                            data: function(term, page){
                                return {
                                    name: term
                                };
                            },
                            results: function(data, page){
                                return {results: data.results};
                            }
                        },
                        initSelection: function(element, callback){
                            var id = $(element).val();
                            if (id !== "") {
                                $.ajax("<?php echo site_url('settings/search_trainees_by_ids');?>", {
                                    data: {
                                        ids: id
                                    },
                                    dataType: "json"
                                }).done(function(data){
                                    callback(data.results);
                                });
                            }
                        },
                        dropdownCssClass: "bigdrop"
                    });

                    $('#paging_holder').append(tms.paging(<?php echo $page_count?>, $('input[name="page_number"]').val(), 'tms.notifications.setPage'));
                    $("#notification_form").validate({
                        submitHandler: function(form){
                            var broadcast_to = $('#broadcast_to').val();
                            if(broadcast_to !="" && isDate(broadcast_to)==false ){
                                $("#broadcast_to_err").text("[invalid format (dd/mm/yyyy)]").addClass('error');
                                return false;
                            }                                                       
                            var formData = $(form).serialize();
                            var formURL = $(form).attr("action");
                            $.getJSON(formURL, formData, function(data){
                                document.location.reload();
                            })

                        }
                    });
                });
                function isDate(txtDate) {                     
                    var reg=/^(0[1-9]|[12][0-9]|3[01])[\- \/.](?:(0[1-9]|1[012])[\- \/.](19|20)[0-9]{2})$/;                    
                    return reg.test(txtDate);
                }    
                function check_broadcastfrom_or_today() {
                    $start_date = $('#broadcast_from').val();
                    $start_date_timestamp = parseDate($start_date);
                    $current_date_timestamp = new Date();
                    if ($start_date_timestamp > $current_date_timestamp) {
                        return $('#broadcast_from').val();
                    }
                    else {
                        return $current_date_timestamp;
                    }
                }
                function parseDate(s) {
                    s = typeof s !== 'undefined' ? s : '';
                    if (s.length > 0) {
                        var b = s.split('/');
                        return new Date(b[2], --b[1], b[0]);
                    }
                }
            </script>
            <tbody>
                <tr>
                    <td width="28%" class="td_heading">Search by Period Created From:</td>
                    <td width="23%">
                        <input type="text" name="start_date" id="start_date" class="date_picker" readonly="readonly" placeholder="dd/mm/yyyy" value="<?php echo $this->input->get('start_date'); ?>">
                    </td>
                    <td width="9%" class="td_heading">To:</td>
                    <td width="30%">
                        <input type="text" name="end_date" id="end_date" class="date_picker" readonly="readonly" placeholder="dd/mm/yyyy" value="<?php echo $this->input->get('end_date'); ?>">
                    </td>
                    <td width="10%" align="center">
                        
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Search by Keyword:</td>
                    <td colspan="4">
                        <input type="text" name="keyword" value="<?php echo $this->input->get('keyword'); ?>" style="width: 200px;" maxlength="40"/>
                        <button type="button" onclick="tms.notifications.runSearch();" class="btn btn-xs btn-primary no-mar pull-right"><span class="glyphicon glyphicon-search"></span> Search</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="table-responsive">
        <div class="mar-bot">
            <strong>Filter on:</strong>
            <select name="status" onchange="tms.notifications.runSearch();">
                <option value="ALL" <?php echo $this->input->get('status') == 'ALL' ? 'selected' : ''; ?>>All</option>
                <option value="ACTIVE" <?php echo $this->input->get('status') == 'ACTIVE' ? 'selected' : ''; ?>>Active</option>
                <option value="INACTIVE" <?php echo $this->input->get('status') == 'INACTIVE' ? 'selected' : ''; ?>>In-Active</option>
            </select>
            <span class="pull-right">
                <a href="#" onclick="tms.notifications.openAddNotificationDialog();" class="small_text">
                    <span class="label label-default black-btn">
                        <span class="glyphicon glyphicon-plus glyphicon1"></span> Add New
                    </span>
                </a>
            </span>
        </div>
        <?php
            echo form_hidden('page_number', $this->input->get('page_number'));
            echo form_hidden('sort_field', $this->input->get('sort_field'));
            echo form_hidden('sort_order', $this->input->get('sort_order'));
            echo form_close();
        ?>
        <div style="clear:both;"></div>
        <table class="table table-striped" id="listview">
            <thead>
                <tr>
                    <th width="4%" class="th_header"><a href="#" onclick="tms.notifications.sortBy('notification_id');">Id</a> </th>
                    <th width="30%" class="th_header"><a href="#" onclick="tms.notifications.sortBy('noti_msg_txt');">Notifications</a> </th>
                    <th width="33%" class="th_header"><a href="#" onclick="tms.notifications.sortBy('noti_type');">Display Type</a></th>
                    <th width="12%" class="th_header"><a href="#" onclick="tms.notifications.sortBy('broadcast_from');">Display From</a></th>
                    <th width="11%" class="th_header"><a href="#" onclick="tms.notifications.sortBy('broadcast_to');">Display To</a></th>
                    <th width="11%" class="th_header">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $n = 1; ?>
                <?php
                    if ($notifications->num_rows() > 0) {
                        foreach ($notifications->result() as $notification) { ?>
                            <tr>
                                <td><?php echo $notification->notification_id; ?></td>
                                <td>
                                    <?php
                                        $noti_txt = $notification->noti_msg_txt;
                                        if (strlen($noti_txt)>50 ) {
                                            echo substr($notification->noti_msg_txt, 0, 50).'...';
                                            $noti_txt = preg_replace( "/\r|\n/", "", $noti_txt);
                                            echo "<span class=\"more\"><a href=\"#\" onclick=\"tms.notifications.showViewMoreDialog('".  $noti_txt ."');\">View More</a></span>";
                                        } else {
                                            echo $noti_txt;
                                        }
                                    ?>
                                </td>
                                <td><?php echo get_param_values_from_map($meta_map, $notification->noti_type); ?></td>
                                <td><?php echo date_format_singapore($notification->broadcast_from); ?></td>
                                <td><?php echo date_format_singapore($notification->broadcast_to); ?></td>
                                <td>
                                    <a href="#" onclick="tms.notifications.openEditNotificationDialog(<?php echo $notification->notification_id; ?>)">Edit</a> &nbsp;&nbsp;
                                    <a href="#" onclick="tms.notifications.openCopyNotificationDialog(<?php echo $notification->notification_id; ?>, '<?php echo $notification->broadcast_from; ?>', '<?php echo $notification->broadcast_to; ?>')">Copy</a>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <td colspan="6" style="text-align: center;">No Notifications Found</td>
                        <script>
                            $('#listview th').each(function(){$(this).html($(this).text())});
                        </script>
                <?php } ?>
            </tbody>
        </table>
        <div style="clear:both;"></div>
        <br/>
        <div id="paging_holder" class="pagination_style"></div>
    </div>
    <div class="modalnew" id="view_notification_details" style="display:none;">
        <h2 class="panel_heading_style">View Notification</h2>
        <div class="modal-body">&nbsp;</div>
        <div class="pull-right">
            <a href="#" rel="modal:close">
                <button class="btn btn-primary" type="button">Cancel</button>
            </a>
        </div>
    </div>
    <div class="modalassessment1111" id="add_notification_dialog" style="display:none;">
        <h2 class="panel_heading_style">Add New Notification / Announcement</h2>
        <?php
            $atr = 'id="notification_form"';
            echo form_open("/settings/update_notification", $atr);
            echo form_hidden("notification_id", "0");
        ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td class="td_heading">Notification:<span class="required">*</span></td>
                        <td><textarea name="noti_msg_txt" id="noti_msg_txt" required style="width:100%;"></textarea></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Notification Type:<span class="required">*</span></td>
                        <td>
                            <select name="noti_type" id="type" required>
                                <?php foreach ($notification_types as $notification_type) { ?>
                                    <option value="<?php echo $notification_type['parameter_id'] ?>"><?php echo $notification_type['category_name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr id="notification_broadcast_user_type_options" style="display: none;">
                        <td>
                            <strong>Broadcast User Type:</strong>
                        </td>
                        <td>
                            <select id="broadcast_user_type" name="broadcast_user_type" style="width:100%;">
                                <?php foreach ($broadcast_user_types as $broadcast_user_type) { ?>
                                    <option value="<?php echo $broadcast_user_type['parameter_id'] ?>"><?php echo $broadcast_user_type['category_name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr id="notification_broadcast_user_list_options" style="display: none;">
                        <td>
                            <strong>Broadcast User List:</strong>
                        </td>
                        <td>
                            <input id="broadcast_user_list" name="broadcast_user_list" type="hidden" class="bigdrop"  style="width:100%;" value=""/>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Display/ Broadcast From:<span class="required">*</span></td>
                        <td>
                            <input name="broadcast_from" id="broadcast_from" type="text" class="date_picker" readonly="readonly" placeholder="dd/mm/yyyy" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Display/ Broadcast To:</td>
                        <td>
                            <input name="broadcast_to" id="broadcast_to" type="text" class="date_picker" placeholder="dd/mm/yyyy">
                            <span id="broadcast_to_err"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <span class="required required_i">* Required Fields</span>
        <div class="button_class">
            <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-saved"></span>&nbsp;Save</button>
        </div>
        <?php echo form_close(); ?>
    </div>
    <div class="modal1_051233" id="copy_notification_dialog" style="display:none;">
        <h2 class="panel_heading_style">Copy Notifications / Announcements</h2>
        Are you sure you want to copy this notification?<br><br>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">Display/ Broadcast From:<span class="required">*</span></td>
                    <td>
                        <input name="copy_broadcast_from" id="copy_broadcast_from" type="text" class="date_picker" readonly="readonly" placeholder="dd/mm/yyyy" required>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Display/ Broadcast To:</td>
                    <td>
                        <input name="copy_broadcast_to" id="copy_broadcast_to" type="text" class="date_picker"  placeholder="dd/mm/yyyy">
                        <span id="copy_broadcast_to_err"></span>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="popup_cancel9">
            <button class="btn btn-primary" type="button" onclick="tms.notifications.doCopyNotification();">Yes</button>&nbsp;&nbsp;
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">No</button></a>
        </div>
    </div>
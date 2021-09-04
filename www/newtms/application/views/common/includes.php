<script type="text/javascript">
  //var baseurl = "http://"+window.location.hostname+"/";
  var baseurl = "<?php echo base_url(); ?>";
  var tenant_id = "<?php print $this->session->userdata('userDetails')->tenant_id; ?>";
</script>
<link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>assets/css/style.css?version=1.0000" rel="stylesheet">
<link href="<?php echo base_url(); ?>assets/css/glyphicons.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/js/ajax.jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.validate.min.js"></script>
<script>
    $.validator.messages.required = '[required]';
</script>
<script src="<?php echo base_url(); ?>assets/js/jquery.cookie.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery.treeview.css" />
<script src="<?php echo base_url(); ?>assets/js/jquery.treeview.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url(); ?>assets/js/menuactive.js" type="text/javascript" charset="utf-8"></script>

<script src="<?php echo base_url(); ?>assets/js/demo.js" type="text/javascript" charset="utf-8"></script>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery-ui.css">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-ui.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.modal.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery.modal.css" type="text/css" media="screen" />

<script src="<?php echo base_url(); ?>assets/js/date_time.js" type="text/javascript" charset="utf-8"></script>

<script src="<?php echo base_url(); ?>assets/js/jquery-ui_auto.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/autocomplete.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js"></script>

<script src="<?php echo base_url(); ?>assets/js/select2.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/select2.css" />


<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/multiselectdropdown.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/multidropdown.js"></script>

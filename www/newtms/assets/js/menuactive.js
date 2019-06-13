$(document).ready(function() {
   var menuindex = $.cookie('cnameIndex');
   var visible = '';
   for (i = 1; i <= 10; i++) {
       if (i == menuindex) {
           visible+='1';
       }else {
           visible+='0';
       }
   }
    document.cookie = "treeview=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
    document.cookie = "cnameIndex=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
    $.cookie("treeview", visible, {path: "/"});
});

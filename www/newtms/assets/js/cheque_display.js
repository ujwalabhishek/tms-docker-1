$(document).ready(function() {
    

 $(function () {
     $('#row_dim').hide();
     $('#type').change(function () {
         $('#row_dim').hide();
         if (this.options[this.selectedIndex].value == 'parcel') {
             $('#row_dim').show();
			 $('.table_new_style').css('margin-bottom','0px');
         }
     });
 });
 
 
 $(function () {
     $('#row_dim2').show();
     $('#type').change(function () {
         $('#row_dim2').hide();
         if (this.options[this.selectedIndex].value == 'parce2') {
             $('#row_dim2').show();
			 $('.table_new_style').css('margin-bottom','0px');
         }
     });
 });
 
 
 $(function () {
     $('#row_dim3').hide();
     $('#type').change(function () {
         $('#row_dim3').hide();
         if (this.options[this.selectedIndex].value == 'parce3') {
             $('#row_dim3').show();
			 $('.table_new_style').css('margin-bottom','0px');
         }
     });
 });

});
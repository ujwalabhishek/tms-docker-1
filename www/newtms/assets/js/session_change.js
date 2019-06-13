$(document).ready(function() {
    

 $(function () {
     $('.row_dim').hide();
     $('#type').change(function () {
         $('.row_dim').hide();
         if (this.options[this.selectedIndex].value == 'parcel') {
             $('.row_dim').show();
         }
     });
 });
 
 
 $(function () {
     $('.row_dim1').hide();
     $('#type').change(function () {
         $('.row_dim1').hide();
         if (this.options[this.selectedIndex].value == 'parcel1') {
             $('.row_dim1').show();
         }
     });
 });
 
 

});
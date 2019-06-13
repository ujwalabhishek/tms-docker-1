$(document).ready(function() {   


 
  $(function () {
     $('#row_dim8').hide();
     $('.type01').change(function () {
         $('#row_dim8').hide();
         if (this.options[this.selectedIndex].value == 'parcel8') {
             $('#row_dim8').show();
         }
     });
 });


  $(function () {
     $('#row_dim9').hide();
     $('.type01').change(function () {
         $('#row_dim9').hide();
         if (this.options[this.selectedIndex].value == 'parcel9') {
             $('#row_dim9').show();
         }
     });
 });


});
$(document).ready(function() {
    $("#submit").click(function(event){
        //alert("The paragraph was clicked.");
        var px = 'px';
        var w = $("#width").val();
        var h = $("#height").val();
        var c = $("#comp").val();
       
//var s = $("#frame1").attr("width", "31");
       if(w =='' && h == '')
       {
        var s = '<iframe src="http://'+c+'.biipbyte.co/course/com_page" frameborder="0" styel="width:800px; height:500px;"></iframe>';
       }else
       {
           if(w =='')
           {
               w = '800';
           }
           else
           {
               w = w;
           }
           
           if(h =='')
           {
               h = '500';
           }
           else
           {
               h = h;
           }
       var s = '<iframe src="http://'+c+'.biipbyte.co/course/com_page" frameborder="0" style="width:'+w+''+px+';'+' height:'+h+''+px+';'+'"></iframe>';    
       }
       
        //alert(s);
       if(s !== '')
       {
         $("#widgets").html(s);  
         return false;
       }
       
    });event.preventDefault();
});



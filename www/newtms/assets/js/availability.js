$(document).ready(function() {

$('.hovercard').hover(function() {
    $(this).stop(true, false).show();
}, function() {
    $('.hovercard').hide();
});
$('#images li').hover(function() {
    $(this).find('.hovercard').delay(100).fadeIn(); 
}, function() {
    $(this).find('.hovercard').delay(100).fadeOut('fast');
});
});
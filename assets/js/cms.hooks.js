$(function() {
    var inter = setInterval(function() {
        if (!$.cookie('ci_setinterval')) {
            alert('Your session has expired. Please log in again.');
            clearInterval(inter);
            location.reload();
        }
    }, 500);
});

$(document).ready(function() {
    if ($.fn.bootstrapSwitch) {
        $('input[type="checkbox"],[type="radio"]').not('#create-switch').bootstrapSwitch();
    }
});
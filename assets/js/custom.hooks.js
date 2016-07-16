// TOGGLE PANELS
$(document).ready(function() {
    $('.panel-title a').on('click', function(e) {
        e.preventDefault();
        var nav = $('.navbar');
        var theTitle = $(this);
        var thePanel = theTitle.parents('.panel');
        thePanel.find('.panel-collapse').collapse('toggle');
        if ($.scrollTo) {
            $.scrollTo(thePanel, {
                offset: {
                    top: -(nav.outerHeight())
                },
                duration: 'slow'
            });
        }
    });
});
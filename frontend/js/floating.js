// floating
jQuery(document).ready(function () {
    var socialize_floating = jQuery('.socialize-floating');
    var iw = jQuery('body').innerWidth();
    if(iw>400){
        socialize_floating.css({
            'position' : 'absolute'
        }).delay(1000).fadeIn(600);
    }
    jQuery.event.add(window, "resize", socialize_resize);
    jQuery.event.add(window, "scroll", socialize_scroll);
    function socialize_scroll(){
        var topPadding = 30;
        var socialize_floating = jQuery('.socialize-floating')
        var s = socialize_floating.parent().offset().top;
        var p = jQuery(window).scrollTop();
        socialize_floating.css('position',((p+topPadding)>s) ? 'fixed' : 'absolute');
        socialize_floating.css('top',((p+topPadding)>s) ? topPadding+'px' : '');
    }
    function socialize_resize(){
        var iw = jQuery('body').innerWidth();
        if(iw<400){socialize_floating.hide();}else{socialize_floating.show();}
    }
});
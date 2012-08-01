// floating


jQuery(document).ready(function () {
    var socialize_floating = jQuery('.socialize-floating');
    socialize_floating.css({
        'position' : 'absolute'
    }).delay(1500).fadeIn(600);
    
    jQuery.event.add(window, "scroll", socialize_scroll);
    var socialize_floating = jQuery('.socialize-floating');
    var s = socialize_floating.offset().top;

    function socialize_scroll(){
    
        var socialize_floating = jQuery('.socialize-floating')
        var s = socialize_floating.offset().top;
        var p = jQuery(window).scrollTop();
        console.log('s:' + s)
        console.log('p:' + p)
        socialize_floating.css('position',((p+10)>s) ? 'fixed' : 'absolute');
        socialize_floating.css('top',((p+10)>s) ? '10px' : '');
    }

});

var tabs_display = function(){

    var $tab = $(".tab"),
        length = $tab.length,
        hash = window.location.hash,
        current_id = (hash!="") ? hash.split('_')[1].slice(0,1) : 0;

    call_tab_content($("#tab_"+current_id), current_id);

    if(length>0){
        $tab.each(function(){
            var $t = $(this);

            $t.click(function(e){
                var id = $t.attr("id").split('_')[1];
                window.location.hash = "tab_"+id;
                hide_tabs_content();
                call_tab_content($t, id);
            });
        });
    }

}

var call_tab_content = function(el, id){
    el.addClass("active");
    $("#tabcont_"+id).css({'display':'block'});
}
var hide_tabs_content = function(){
    $(".tab").removeClass("active");
    $(".tab-content-container").css({'display':'none'});
}

$(window).on('hashchange', function() {
    var hash = window.location.hash,
        id = hash.split('_')[1].slice(0,1);
    if (hash.indexOf('tab') > -1) {
       hide_tabs_content();
       call_tab_content($("#tab_"+id),id);
    }
});
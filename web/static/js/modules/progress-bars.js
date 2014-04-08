
var progress_bars = function(){

    var $pbar = $(".status-bar-overflowed");
    if($pbar.length>0){
        $pbar.each(function(){
            var $t = $(this),
            status = $t.attr('data-status-percent');
            $t.animate({
                'width': 100-status+'%'
            }, anim_time*3);
        });
    }

}

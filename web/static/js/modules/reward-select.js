
var reward_select = function(){

    var $rew = $(".reward-container");

    if($rew.length>0){
        $rew.each(function(){
            var $t = $(this),
                radio = $($($t.children()[0]).children()[0]),
                $r = $(radio)[0];

            if($t.hasClass("out-of-stock"))
                $r.disabled=true;
        });
    }

}

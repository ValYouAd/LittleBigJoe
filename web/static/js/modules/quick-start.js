
var quick_start = function(){
    var $imc = $("#image-message-container"),
        $qsc = $("#quick-start-container");

    $qsc.opened=false;

    $imc.click(function(){

        if(!$qsc.opened){

            $qsc.opened=true;

            $qsc.stop().animate({
                'height': $qsc[0].scrollHeight+60+'px',
                'padding-top': '30px',
                'padding-bottom': '30px'
            }, anim_time/2);

            var offset = $qsc[0].scrollHeight+150;

            $imc.stop().animate({
                'top': offset+'px'
            }, anim_time/2);
            $imc.find("#imc-arrow").html('&#x25B2;');

            $(".page").animate({
                'margin-top': offset+'px'
            }, anim_time/2);

        } else {

            $qsc.stop().animate({
                'height': '0px',
                'padding': '0px'
            }, anim_time, function(){
                $qsc.opened=false;
            });

            $imc.stop().animate({
                'top': '92px'
            }, anim_time);
            $imc.find("#imc-arrow").html('&#x25BC;');

            $(".page").animate({
                'margin-top': '60px'
            }, anim_time);
        }
    });

}


var faq_display = function(){

    var $faq = $(".faq-container"),
        l = $faq.length,
        faq_array = [];

    if(l>0){
        for(var i=0; i<l; i++){faq_array.push(false);}

        $faq.each(function(){
            var $t=$(this),
                id=$t.attr("id").split('_')[1],
                $q=$($t.children()[0]),
                $a=$($t.children()[1]);

            $q.click(function(){
                if(!faq_array[id]) {
                    $a.stop().animate({
                        'height': $a[0].scrollHeight+'px'
                    }, anim_time);
                    faq_array[id]=true;
                } else {
                    $a.stop().animate({
                        'height': '0px'
                    }, anim_time);
                    faq_array[id]=false;
                }
            });
        });
    }

}

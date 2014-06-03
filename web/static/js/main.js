
var global_width = $(window).innerWidth(),
    anim_time    = 300;

window.onload = function(){

    window.quick_start();
    
    // execute modules if they exist
    ( window.progress_bars || function(){} )();
    ( window.involve_graph || function(){} )();
    ( window.project_carousel || function(){} )();
    ( window.faq_display || function(){} )();
    ( window.reward_select || function(){} )();
    ( window.tabs_display || function(){} )();

}

WebFontConfig = {
  custom: {
    families: ['Proxima-Nova', 'Proxima-Nova-Black', 'Proxima-Nova-SemiBold'],
    urls: ['/static/less/custom-fonts-rules.less']
  }
};

WebFont.load({
    custom: {
      families: ['Proxima-Nova', 'Proxima-Nova-Black', 'Proxima-Nova-SemiBold']
    }
});
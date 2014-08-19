var involve_graph = function(){

    var dark_blue = '#348c93',
        light_so_blue = 'rgba(158, 215, 220, 0.5)',
        orange = '#fa7451',
        $graph = $("#involve-graph"),

        def_sparkline = function(el, values){
            el.sparkline(values, {
                type: 'line',
                width: '100%',
                height: '150px',
                lineColor: dark_blue,
                spotColor: dark_blue,
                minSpotColor: dark_blue,
                maxSpotColor: dark_blue,
                highlightSpotColor: orange,
                highlightLineColor: orange,
                spotRadius: 6,
                fillColor: light_so_blue,
                chartRangeMin: 0,
                chartRangeMax: $graph.attr('data-max-likes'),
                tooltipSuffix: ' likes'
            });
        };

    if($graph.length>0){
        var values = JSON.parse("[" + $graph.attr('data-values-array') + "]");
        def_sparkline($graph, values);
    }

    $(window).resize(function(){
        def_sparkline($graph, values);
    });
}

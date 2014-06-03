
var project_carousel = function(){
    var $car = $(".project-carousel");

    if($car.length>0){
        $car.carousel({
            interval: false
        });
    }
};

var product_carousel = function(){
    var $productCar = $(".product-carousel");

    if($productCar.length>0){
        $productCar.carousel({
            interval: false
        });
    }
};

$(document).ready(function() {
    // Initialize brand slider
    $('.brand-slider').slick({
        dots: true,
        infinite: true,
        speed: 300,
        slidesToShow: 4,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3000,
        pauseOnHover: false,  
        pauseOnFocus: false,  
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    $('.brand-slider').on('click', '.slick-slide', function(e) {
        e.stopPropagation();
        var index = $(this).data("slick-index");
        if ($('.brand-slider').slick('slickCurrentSlide') !== index) {
            $('.brand-slider').slick('slickGoTo', index);
        }
    });

    // Initialize latest products slider
    $('.latest-products-slider').slick({
        dots: true,
        infinite: true,
        speed: 300,
        slidesToShow: 4,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3000,
        pauseOnHover: false,
        pauseOnFocus: false,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    $('.latest-products-slider').on('click', '.slick-slide', function(e) {
        e.stopPropagation();
        var index = $(this).data("slick-index");
        if ($('.latest-products-slider').slick('slickCurrentSlide') !== index) {
            $('.latest-products-slider').slick('slickGoTo', index);
        }
    });


});

/*
-------------------------------------------------------------------------
* Template Name    : Sofash - SaaS & Software Html5 Landing Page        * 
* Author           : ThemesBoss                                         *
* Version          : 1.0.0                                              *
* Created          : January 2019                                      * 
* File Description : Main JS file of the template                       *
*------------------------------------------------------------------------
*/

! function($) {
    "use strict";

    var Sofash = function() {};

    Sofash.prototype.initStickyAddMenu = function() {
        $(window).on('scroll', function() {
            var scroll = $(window).scrollTop();

            if (scroll >= 50) {
                $(".sticky").addClass("stickyadd");
            } else {
                $(".sticky").removeClass("stickyadd");
            }
        });
    },

    Sofash.prototype.initSmoothLink = function() {
        $('.nav-item a').on('click', function(event) {
            var $anchor = $(this);
            $('html, body').stop().animate({
                scrollTop: $($anchor.attr('href')).offset().top - 50
            }, 1500, 'easeInOutExpo');
            event.preventDefault();
        });
    },

    Sofash.prototype.initCollpsehide = function() {
        $(document).on('click', '.navbar-collapse.show', function(e) {
            if ($(e.target).is('a')) {
                $(this).collapse('hide');
            }
        });
    },

    Sofash.prototype.initScrollspy = function() {
        $("#navbarCollapse").scrollspy({
            offset: 70
        });
    },

    Sofash.prototype.initMFPImage = function() {
        $('.img-zoom').magnificPopup({
            type: 'image',
            closeOnContentClick: true,
            mainClass: 'mfp-fade',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [0, 1]
            }
        });
    },

    Sofash.prototype.initClientSlider = function() {
        $("#owl-demo").owlCarousel({
            autoPlay: 10000,
            items: 3,
            itemsDesktop: [1199, 3],
            itemsDesktopSmall: [979, 3]
        });
    },

    Sofash.prototype.initFunFacts = function() {
        var a = 0;
        $(window).on('scroll', function() {
            var oTop = $('#counter').offset().top - window.innerHeight;
            if (a == 0 && $(window).scrollTop() > oTop) {
                $('.sof_count').each(function() {
                    var $this = $(this),
                        countTo = $this.attr('data-count');
                    $({
                        countNum: $this.text()
                    }).animate({
                        countNum: countTo
                    }, {
                        duration: 2000,
                        easing: 'swing',
                        step: function() {
                            $this.text(Math.floor(this.countNum));
                        },
                        complete: function() {
                            $this.text(this.countNum);
                            //alert('finished');
                        }

                    });
                });
                a = 1;
            }
        });
    },

    Sofash.prototype.initMFPVideoPlay = function() {
        $('.video_hit').magnificPopup({
            disableOn: 700,
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: false,
            fixedContentPos: false
        });
    },

    Sofash.prototype.initBTT = function() {
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 100) {
                $('.back_top').fadeIn();
            } else {
                $('.back_top').fadeOut();
            }
        });
        $('.back_top').on('click', function() {
            $("html, body").animate({
                scrollTop: 0
            }, 1000);
            return false;
        });
    },

    Sofash.prototype.initPreLoader = function() {
        var loader = $(".loader");
        var wHeight = $(window).height();
        var wWidth = $(window).width();
        var o = 0;

        loader.css({
            top: wHeight / 2 - 2.5,
            left: wWidth / 2 - 200
        })

        do {
            loader.animate({
                width: o
            }, 10)
            o += 3;
        } while (o <= 400)
        if (o === 402) {
            loader.animate({
                left: 0,
                width: '100%'
            })
            loader.animate({
                top: '0',
                height: '100vh'
            })
        }

        setTimeout(function() {
            $(".loader-wrapper").fadeOut('fast');
            (loader).fadeOut('fast');
        }, 3500);
    },

    Sofash.prototype.init = function() {
        this.initStickyAddMenu();
        this.initSmoothLink();
        this.initCollpsehide();
        this.initScrollspy();
        this.initMFPImage();
        this.initClientSlider();
        this.initFunFacts();
        this.initMFPVideoPlay();
        this.initBTT();
        this.initPreLoader();
    },
    //init
    $.Sofash = new Sofash, $.Sofash.Constructor = Sofash
}(window.jQuery),

//initializing
function($) {
    "use strict";
    $.Sofash.init();
}(window.jQuery);
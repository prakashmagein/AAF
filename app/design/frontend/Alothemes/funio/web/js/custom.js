/*
 * @Author: Aloteam
 * @Date:   2020-11-16
 * @Last Modified by:   Aloteam
 * @Last Modified time: 2021-01-29 11:28:51
 */

require(['jquery'],
    function($) {
        $(document).ready(function() {
            // toggle top menu
            $('.topmenu-close').on('click', function(event) {
                event.preventDefault();
                $('html').toggleClass('toggle-top-menu');
            });

            // close-cookie
            $('.cookie-close').on('click', function(event) {
                event.preventDefault();
                $('.magepow-gdpr-cookie-notice').addClass('disable');
            });
            setInterval(function() {
                var leftTimeNode = $('body').find('#product-addtocart-button');
                leftTimeNode.addClass('_show');
                setTimeout(function() { leftTimeNode.removeClass('_show') }, 1000);

            }, 6000);

            // active vertical
            'use strict';
            $('.page-footer').append('<div class="overlay-bg"></div>');
            $(".block-title-vmagicmenu").on("click", function() {
                if ($(".block-title-vmagicmenu").hasClass("active")) {
                    $(".block-title-vmagicmenu").removeClass("active");
                    $("html").removeClass("open-nav-vertical");
                } else {
                    $(".block-title-vmagicmenu").addClass("active");
                    $("html").addClass("open-nav-vertical");

                }

            });

            // close open content in mobile
            $(window).resize(function() {
                var windowWidth = $(window).width();
                if (windowWidth > 767) {
                    $('.collapsible .toggle-content').slideDown();
                }
                if (windowWidth == 767) {
                    $('.collapsible').removeClass('opened');
                    $('.collapsible .toggle-content').slideUp();
                }
            });
            $(".toggle-tab-mobile").on('click', function(event) {
                if ($(window).width() > 767) {
                    event.stopPropagation();
                } 
                else {
                    $(this).parent().hasClass('opened') ? $(this).parent().removeClass('opened') : $(this).parent().addClass("opened");
                    $(this).next().slideToggle();
                }
            });
        });

        // hover mouse to menu (nav-destop)
        'use strict';
        $('body').append('<div class="menu-overlay"></div>');
        $(".nav-desktop").mouseenter(function() {
            $('body').addClass('menu-open');

            $(".menu-overlay").fadeIn();
        });
        $('.nav-desktop').mouseleave(function() {
            $('body').removeClass('menu-open');
            $(".menu-overlay").hide();
        });
        $(".menu-overlay").mouseenter(function() {
            $(this).hide();
        });


        // detai page tab
        $('.product.info.detailed').on('click', '.tab-scroll-content .tab-items .tab-item', function (event) {
            event.preventDefault();
            $(this).parent().find('.tab-item').removeClass('active');
            $(this).addClass('active');
    
              
        });

        $(window).scroll(function() {
            if($('body').hasClass('catalog-product-view')){
                let item_sticky = $('.product.info.detailed');
                item_sticky_position = item_sticky.offset().top;
                body = $('html, body');
                item_no_sticky = $('.block-product-bottom');
                item_no_sticky_position = $('.block-product-bottom').offset().top;
                mouse_position = body.scrollTop();

                if (mouse_position < item_no_sticky_position) {
                    if (mouse_position >= item_sticky_position) {
                        body.addClass('detail-tab-sticky');
                    } else {
                        body.removeClass('detail-tab-sticky');
                    }
                } else {
                    body.removeClass('detail-tab-sticky');
                }
            }
            
        });

        if($('.magicmenu .level0.home').has('.submenu').length>0){
            $('.magicmenu .level0.home').addClass('hasChild');
        }

        // input focus 
        $('.input-text').on('click', function(){
            $(this).parents('.field').addClass('active');
        });
        if($('body').hasClass('catalog-product-view')){
            var anchor, addReviewBlock;
            var windowHref = window.location.href; 
            anchor = windowHref.replace(/^.*?(#|$)/, '');
            if(anchor){
                addReviewBlock = $('#' + anchor);
                (anchor == 'review-form') ? addReviewBlock.parents("#reviews").show().trigger('click') : addReviewBlock.show().trigger('click');
                $('html, body').animate({
                    scrollTop: addReviewBlock.offset().top - 250
                }, 2000);
            }
            
        }

        // onmap toggle 
        $('.onmap .toggle-tab').on('click', function(){
            $('html').addClass('open-map');
        });

        $('.onmap .btn-close').on('click', function(){
            $('html').removeClass('open-map');
        });

        // if ($(window).width() <= 479){
        //     $(document).ready(function (){
        //         var option = $('.toolbar-sorter').find('option');
        //         option.each(function() {
        //           var data = $(this).attr('value');
        //             $(this).html(data);
        //         });
        //     });

        //     $( document ).ajaxComplete(function( event,request, settings ) {
        //         var option = $('.toolbar-sorter').find('option');
        //         option.each(function() {
        //             var data = $(this).attr('value');
        //             $(this).html(data);
        //         });
        //     });
        // }

        $('.block.filter .block-content').on('scroll', function(){
            var h_scroll = $(this).scrollTop();
            console.log(h_scroll);
            
            if (h_scroll < 50) {
                $('body').removeClass('scroll-filter-content');
            }
            else{
                $('body').addClass('scroll-filter-content');
            }
        });
    });
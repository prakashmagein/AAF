define(
    [
        'jquery'
    ],
    function ($) {
        'use strict';
        return function (param)
        {
            if (param.product == 0) {
                var whatsAppChat = $('<div />').appendTo('body');
                whatsAppChat.attr('id', 'whatsappchat');
                whatsAppChat.attr('class', param.position);

                var links = param.number + '?text=' + param.message;
                links = "https://wa.me/" + links;

                var aLink = $('<a/>').appendTo('#whatsappchat');
                aLink.attr('target', '_blank');
                aLink.attr('id', 'whatsapplink');
                aLink.attr('alt', 'WhatsApp Chat');

                aLink.attr('href', links);

                var wImage = $('<img/>').appendTo('#whatsapplink');
                wImage.attr('id', 'whatsappimage');
                wImage.attr('src', param.image);
            } else {
                if($('.catalog-product-view .product-info-main .price-box').length){
                    var url = window.location.href;
                    var whatsAppChat = $('<div />').appendTo('.catalog-product-view .product-info-main .price-box');
                    whatsAppChat.attr('id', 'whatsappchat');
                    whatsAppChat.attr('class', 'product-page');

                    var links = param.number + '?text=' + url + ' ' + param.message;
                    links = "https://wa.me/" + links;

                    var aLink = $('<a/>').appendTo('#whatsappchat');
                    aLink.attr('target', '_blank');
                    aLink.attr('id', 'whatsapplink');
                    aLink.attr('title', 'WhatsApp Chat');
                    aLink.attr('href', links);

                    var wImage = $('<img/>').appendTo('#whatsapplink');
                    wImage.attr('id', 'whatsappimage');
                    wImage.attr('src', param.image);
                    wImage.attr('alt', 'WhatsApp Chat');
                }

            }

        }
    });

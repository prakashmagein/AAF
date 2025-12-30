define([
    "jquery",
    "mage/loader",
    'mage/translate',
    'mage/url',
    'underscore',
    'Magento_Checkout/js/model/quote'
], function ($, loader, $t, url, _,quote) {
    'use strict';

    $.widget('mage.mdLoginOTP', {
        options: {
            selectors: {
                shippingNextButton : '#shipping-method-buttons-container .button',
                codOtpButton:".send_otp_cod",
                codOtpVerifyButton:'.verif_otp_cod',
                codResendButton:'.resendotp_cod',
                checkoutLoginInputField:'#checkout-step-shipping #login_mobile'

            },
            sendOtpUrl:url.build('smsprofile/otp/send'),
            isResend:0,
            otpVerifyUrlLogin:url.build('smsprofile/otp/verify')
        },

        _create: function () {
            this.init();
        },

        init: function () {
            this.initBindings();
        },

        initBindings: function () {

            var self = this,
                protocol = document.location.protocol;

            /* shipping next button */
            $(document).on("click", self.options.selectors.shippingNextButton, function (event) {
                
                $(self.options.selectors.checkoutLoginInputField).removeClass('validate-length validate-digits');
                $(self.options.selectors.checkoutLoginInputField).addClass('validate-email');
                if ($(self.options.selectors.checkoutLoginInputField).length > 0) {
                    if ($(self.options.selectors.checkoutLoginInputField).valid()) {
                        quote.guestEmail = $(self.options.selectors.checkoutLoginInputField).val();
                    } else {
                        $(self.options.selectors.checkoutLoginInputField).val("magedelight");
                        $(self.options.selectors.checkoutLoginInputField).trigger("keyup");
                        $(self.options.selectors.checkoutLoginInputField).focus();
                        return false;
                    }
                }

                var address = quote.shippingAddress();
                var billingAddress = quote.billingAddress();

                if (billingAddress != null) {
                    if ($(document).find(".resendlink").length > 0 && $(document).find(".field-name-otp").length > 0 && address.telephone != billingAddress.telephone) {
                        $(document).find(".resendlink").remove();
                        $(document).find(".field-name-otp").remove();
                        $(document).find(".send_otp_cod").show();
                        $(document).find('#cashondelivery_codotp').val(0);
                        $(document).find('.otp_text').remove();
                        $(document).find('.cod').addClass('disabled');
                    }
                }

            });

            /* cod payment generate OTP */
            $(document).on("click", self.options.selectors.codOtpButton, function (event) {
                var otpForm=$(this).closest('form');
                self.sendCodOtpVerification(otpForm);
            });

            /*verify cod button*/
            $(document).on("click", self.options.selectors.codOtpVerifyButton, function (event) {
                self.verifyCodOtpCode();
            });

            /* resend otp button click */
            $(document).on("click",self.options.selectors.codResendButton, function () {
                var form = $(this).closest('form');
                $(form).find(".resendotp-block").hide();
                self.options.isResend = 1;
                self.sendCodOtpVerification(form);
            });

            
        },

        sendCodOtpVerification:function (form) {
            var self = this;
            var address = quote.shippingAddress();
            var mobile = address.telephone;
            var countrycode = null;
            if (window.checkoutConfig.customer_country_enabled) {
                countrycode="+";
            }
            
            $.ajax({
                showLoader: true,
                url: self.options.sendOtpUrl,
                method: "POST",
                data: {
                    mobile: mobile,
                    eventType: 'cod_otp',
                    resend: self.options.isResend,
                    formType:'cod_otp',
                    countrycode: countrycode
                },
                dataType: "json"
            }).done(function (response) {

                if (response.Success === 'success') {
                    $(document).find(".send_otp_cod").hide();
                    $(document).find(".otp_cod_generatenote").html(response.otp_message);
                    $(document).find('.otp_cod_generatenote').delay(5000).fadeOut(800);
                        self.showOtpResendButton(form,response.resend_link_count);
                    if ($(".payment-method-content .resendlink").length == 0 && $(".payment-method-content .field-name-otp").length == 0) {
                        $(".otp_cod_resendlink").after('<div class="resendlink" style="display:none;"><button class="resendotp_cod"  type="button">Resend OTP</button></div>');
                        $(".otp_cod_generatenote").after('<div class="field field-name-otp required"><label class="label" for="otp"><span>Please enter verification code here</span></label>  <div class="control"> <input id="otp_cod" name="otp" value="" title="otp" class="input-text required-entry" data-validate="{required:true}" autocomplete="off" aria-required="true" type="text" novalidate="novalidate" style="width: 200px;"> <button style="display: block;margin-top: 10px;" class="verif_otp_cod action primary" type="button">Verify OTP</button> </div></div><span class="otp_text"></span> ');
                    }
                    var resendLimit=$(form).find('.resend-limit').text();
                    var leftAttamp= resendLimit -response.resend_link_count;
                    var otpMessage='Resend OTP attempt '+leftAttamp+' of '+resendLimit+' left. '
                    $(form).find(".resend-link-attempt").html(otpMessage);
                    $(form).find('.resend-link-attempt').show();
                } else {
                    $(document).find(".otp_cod_generatenote").html('Not able to send SMS without respective OTP');
                    $(document).find('.otp_cod_generatenote').delay(5000).fadeOut(800);
                }

                $('.send_otp_cod').unbind("click"); /*stop click event after html added */
            });
        },
        showOtpResendButton:function (form,resend_link_count) {
            $(form).find(".resend-wait").show();
                var resendTime = $(form).find('.resendtime').text();
                var resendLinkDisable = $(form).find('#resend_link_disable').val();
            if (resendLinkDisable==1) {
                $(form).find(".resendlink").hide();
                $(form).find(".resend-wait").hide();
            } else {
                if (resendTime < 5) {
                    resendTime = 5;
                }
                $(form).find(".resendlink").hide();
                var i = resendTime;
                var time = $(form).find(".time");
                var timer = setInterval(function () {
                    time.html(i);
                    if (i === 0) {
                        $(form).find(".resendlink").show();
                        $(form).find(".resend-wait").hide();
                        clearInterval(timer);
                    }
                    i--;
                }, 1000)
            }
        },
        verifyCodOtpCode:function (form) {
             var self = this;
            if ($('#otp_cod').val() != '') {
                $('.otp_text').html('');
                var address = quote.shippingAddress();
                var mobile = "+"+address.telephone;
                $.ajax({
                    showLoader: true,
                    url: self.options.otpVerifyUrlLogin,
                    method: "POST",
                    data: {
                        otp: $("#otp_cod").val(),
                        mobile: mobile
                    },
                    dataType: "json"
                }).done(function (response) {
                    if (response) {
                        $(document).find(".otp_cod_generatenote").html('');
                        $(document).find(".resendlink").html('');
                        $(document).find('.resendlink').fadeIn();
                        $(document).find('.otp_cod_generatenote').fadeIn();
                        $('.otp_text').html(response.message);
                        if (response.message == 'Verified') {
                            $('.otp_text').css('color','green');
                            $('.send_otp_cod').hide();
                            $('.resendlink').hide();
                            $('.field-name-otp').hide();
                            $('#cashondelivery_codotp').val(1);
                            $('.checkout-index-index .actions-toolbar .cod').removeClass('disabled');
                        } else {
                            $('.otp_text').css('color','red');
                            $('#cashondelivery_codotp').val(0);
                        }
                    }
                });
            } else {
                alert('Please enter OTP');
            }
        }

    });

    return $.mage.mdLoginOTP;
});

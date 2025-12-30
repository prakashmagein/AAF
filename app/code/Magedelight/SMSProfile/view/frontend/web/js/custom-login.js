define([
    "jquery",
    "mage/loader",
    'mage/translate',
    'mage/url',
    'underscore',
    'mage/validation'
], function ($, loader, $t, url, _) {
    'use strict';
    url.setBaseUrl(BASE_URL);
    $.widget('mage.mdLoginOTP', {
        mdoptions: {
            fields: {
                loginOtpTab: '[data-md-js="md-login-opt"]',
                loginPassTab: '[data-md-js="md-login-password"]',
                loginTab: '.login-opt',
                formType:'#form_type',
                loginOtpContent:'.smsprofile-login-mobile',
                loginPassContent:'.block-customer-login',
                autoVerify:'#auto_verify_otp',
                actionButton:'[data-md-js="md-submit-button"]',
                loginInputField:'#login_mobile',
                otpinput:'input#otp',
                registerLoginInputField:"#md-register-content #login_mobile",
                /*forgotLoginInputField:"#md-forgot-content #login_mobile",*/
                generateOtpButton:'.send_otp_login',
                resendOtpButton:'.resendotp',
                verifyOtpButton:'.verif_otp_login',
                defaultMobileValidation:'#default_mobile_validation'

            },
            sendOtpUrl:url.build('smsprofile/otp/send'),
            isResend:0,
            otpVerifyUrlLogin:url.build('smsprofile/otp/verify')
        },

        _create: function () {
            this.initObservable();
        },

        initObservable: function () {

            $('#md-login-popup #md-register-content form.form-create-account').validation();

            var self = this;

            if ($(self.mdoptions.fields.defaultMobileValidation).val()!="") {
                var className = "validate-length validate-digits";
                className += " maximum-length-"+$(self.mdoptions.fields.defaultMobileValidation).val();
                className += " minimum-length-"+$(self.mdoptions.fields.defaultMobileValidation).val();
                $(".form-create-account #login_mobile").addClass(className);
                $(".form-edit-account #login_mobile").addClass(className);
            }

            /* login tabs*/
            $(document).on("click", self.mdoptions.fields.loginTab, function (event) {
                self.showResultTab($(this).attr('id'));
            });

            $('.customer-account-create form.form-create-account').on('submit', function(e) {
                if ($(this).valid()) {
                var otp = $('#otp').val();
                if (!otp) {
                    $('.cust-create-ac-error span').text($t('Please verify OTP'));
                    $('.customer-account-create form.form-create-account .action.submit.primary').attr('disabled', true);
                    e.preventDefault();
                    setTimeout(function() {
                        $('.customer-account-create form.form-create-account .action.submit.primary').attr('disabled', false);
                        $('.cust-create-ac-error span').text('');
                    }, 3000);
                    return false;
                    }else if(otp.length > 0){
                        if($('.otp-value-verify').val() != "Verified"){
                            $('.cust-create-ac-error span').text($t('Please verify OTP'));
                            $('.customer-account-create form.form-create-account .action.submit.primary').attr('disabled', true);
                            e.preventDefault();
                            setTimeout(function() {
                                $('.customer-account-create form.form-create-account .action.submit.primary').attr('disabled', false);
                                $('.cust-create-ac-error span').text('');
                            }, 3000);
                            return false;
                        }
                    }
                } else {
                    e.preventDefault();
                    return false;
                }
            });

            /* email/mobile field keyup*/
            $(document).on("keyup", self.mdoptions.fields.loginInputField, function (event) {
                $('.otp_text').html('');
                $('#md-login-popup .md-error span').text('');
                $("input#otp").val("");
                if ($(this).data('name')!="create" && !$('body').hasClass('customer-account-create') && !$('body').hasClass('customer-account-edit')) {
                    if ($.isNumeric($(this).val().slice(0, 1))) {
                        if ($(self.mdoptions.fields.defaultMobileValidation).val()!="") {
                            if ($(this).attr("name")=='login[mobile]' || $(this).attr("name")=='customer_mobile' || $(this).attr("name")=='mobile') {
                                var className = "validate-length validate-digits";
                                className += " maximum-length-"+$(self.mdoptions.fields.defaultMobileValidation).val();
                                className += " minimum-length-"+$(self.mdoptions.fields.defaultMobileValidation).val();
                                $(this).addClass(className);
                                $(this).removeClass('validate-email');
                            }
                        } else {
                            if ($(this).attr("name")=='login[username]') {
                                $(this).addClass('validate-length validate-digits');
                                $(this).removeClass('validate-email');
                                return;
                            }
                            if ($(this).attr("name")=='mobile') {
                                $(this).addClass('validate-length validate-digits');
                                $(this).removeClass('validate-email');
                            }
                        }

                        $(this).prev('.iti__flag-container').show();
                    } else {
                        if ($(this).attr("name")=='login[username]') {
                            $(this).removeClass('validate-length validate-digits');
                            $(this).addClass('validate-email');
                            return;
                        }
                        $(this).prev('.iti__flag-container').hide();
                        $(this).css('padding-left','6px');
                        $(this).removeClass('validate-length validate-digits');
                        $(this).addClass('validate-email');
                    }
                } else {
                    $(this).prev('.iti__flag-container').show();
                }
                $('.resendotp-block').hide();
                if ($(this).valid()) {
                    if ($(self.mdoptions.fields.autoVerify).val()==1 && $.isNumeric($(this).val().slice(0, 1))) {
                        var element = $(this).closest('form');
                        $(this).attr("disabled", true);
                        self.sendVerification(element);
                    } else {
                        $(self.mdoptions.fields.generateOtpButton).show();
                    }
                } else {
                    $(self.mdoptions.fields.generateOtpButton).hide();
                }
            });

            /* otp input field keyup*/
            $(self.mdoptions.fields.otpinput).on('keyup', function (event) {
                $('#md-login-popup .md-error span').text('');
                 $('.otp_text').html('');
            });

            /* register popup email/mobile field keyup*/
            /*$(self.mdoptions.fields.registerLoginInputField).on('keyup', function (event) {
                if(!$('body').hasClass('customer-account-create')){
                    if ($.isNumeric($(this).val().slice(0, 1))) {
                        $(this).prev('.iti__flag-container').show();
                        $(this).addClass('validate-length validate-digits');
                        $(this).removeClass('validate-email');
                    }else{
                        $(this).prev('.iti__flag-container').hide();
                        $(this).css('padding-left','6px');
                        $(this).removeClass('validate-length validate-digits');
                        $(this).addClass('validate-email');
                    }
                }
                $('.resendotp-block').hide();
                if ($(this).validation('isValid')) {
                    if($(self.mdoptions.fields.autoVerify).val()==1 && $.isNumeric($(this).val().slice(0, 1)))
                    {
                        var element = $(this).closest('form');
                        $(this).attr("disabled", true);
                        self.sendVerification(element);
                    }else{
                        $(self.mdoptions.fields.generateOtpButton).show();
                    }
                }else{
                    $(self.mdoptions.fields.generateOtpButton).hide();
                }
            });*/

            /* forgot popup email/mobile field keyup*/
            $(self.mdoptions.fields.forgotLoginInputField).on('keyup', function (event) {
                if (!$('body').hasClass('customer-account-create')) {
                    if ($.isNumeric($(this).val().slice(0, 1))) {
                        $(this).prev('.iti__flag-container').show();
                        $(this).addClass('validate-length validate-digits');
                        $(this).removeClass('validate-email');
                    } else {
                        $(this).prev('.iti__flag-container').hide();
                        $(this).css('padding-left','6px');
                        $(this).removeClass('validate-length validate-digits');
                        $(this).addClass('validate-email');
                    }
                }
                $('.resendotp-block').hide();
                if ($(this).validation('isValid')) {
                    if ($(self.mdoptions.fields.autoVerify).val()==1 && $.isNumeric($(this).val().slice(0, 1))) {
                        var element = $(this).closest('form');
                        $(this).attr("disabled", true);
                        self.sendVerification(element);
                    } else {
                        $(self.mdoptions.fields.generateOtpButton).show();
                    }
                } else {
                    $(self.mdoptions.fields.generateOtpButton).hide();
                }
            });

            /* generate button click event */
            $(document).on("click", self.mdoptions.fields.generateOtpButton, function (event) {
                var form = $(this).closest('form');
                self.sendVerification(form);
            });

            /* resend otp button click */
             $(document).on("click", self.mdoptions.fields.resendOtpButton, function (event) {
                var form = $(this).closest('form');
                $(form).find('.otp_text').html("");
                $(form).find("input#otp").val("");
                $('#md-login-popup .md-error span').text('');
                $(form).find(".resendotp-block").hide();
                self.mdoptions.isResend = 1;
                self.sendVerification(form);
             });

            /*verify button click */
            $(document).on("click", self.mdoptions.fields.verifyOtpButton, function (event) {
                var form = $(this).closest('form');
                self.verifyOtpCode(form);
            });
        },

        showResultTab:function (activeTab) {
            var self = this;
            if (activeTab == 'login-opt-email') {
                $(self.mdoptions.fields.loginOtpContent).hide();
                $(self.mdoptions.fields.loginPassContent).show();
                $(self.mdoptions.fields.loginPassTab).addClass('active');
                $(self.mdoptions.fields.loginOtpTab).removeClass('active');
                $(self.mdoptions.fields.actionButton).show();
                $(self.mdoptions.fields.actionButton).attr("disabled", false);
                if ($(self.mdoptions.fields.formType).val()=='forgot') {
                    $(self.mdoptions.fields.actionButton).text($t("Send Email"));
                }
            } else {
                $(self.mdoptions.fields.loginOtpContent).show();
                $(self.mdoptions.fields.loginPassContent).hide();
                $(self.mdoptions.fields.loginPassTab).removeClass('active');
                $(self.mdoptions.fields.loginOtpTab).addClass('active');
                if (self.mdoptions.fields.autoVerify==1) {
                    $(self.mdoptions.fields.actionButton).hide();
                } else {
                    $(self.mdoptions.fields.actionButton).attr("disabled", true);
                }
                if ($(self.mdoptions.fields.formType).val()=='forgot') {
                    $(self.mdoptions.fields.actionButton).text($t("Reset Password"));
                }
            }
            $('.authentication-dropdown .block-customer-login').show();
        },

        sendVerification:function (form) {
            var self = this;
            var inputFieldValue = $(form).find("#login_mobile").val();

            if(inputFieldValue.length <= 0){
                $('.custom-login-action-button .action.login.primary').trigger( "click" );
                return;
            }
            var mobile = "";
            var email  = "";
            var countrycode = "";
            
            if ($.isNumeric(inputFieldValue.slice(0, 1))) {
                mobile = inputFieldValue;
                countrycode = $(form).find("#countryreg").val();
            } else {
                email = inputFieldValue;
            }

            var resendTime = $(form).find('#resend_limit_time').val();
            var smsError = $(form).find(".smserror");
            var resendLimit  = $(form).find("#resend_limit").val();

            if (smsError.length > 0 ) {
                smsError.html('');
            }

            var actionType = $(form).find("#form_type").val();

            var event = 'customer_login_otp';

            if ($('.md-forget-password #login_mobile').length > 0 && $('.md-forget-password #login_mobile').val()!="" && actionType=='forgot') {
                event = 'forgot_password_otp';
            } else if ($('.customer-account-create #login_mobile').length > 0 && $('.customer-account-create #login_mobile').val()!="") {
                event = 'customer_signup_otp';
            } else if ($('#md-register-content #login_mobile').length > 0 && $('#md-register-content #login_mobile').val()!="" && actionType=='create') {
                event = 'customer_signup_otp';
            } else if ($('.customer-account-edit #login_mobile').length > 0 && $('.customer-account-edit #login_mobile').val()!="") {
                event = 'customer_account_edit_otp';
            }
            
            $.ajax({
                showLoader: true,
                url: self.mdoptions.sendOtpUrl,
                method: "POST",
                data: {
                    countrycode: countrycode,
                    mobile: mobile,
                    eventType: event,
                    resend:  self.mdoptions.isResend,
                    email : email,
                    formType:actionType,
                    captchaResponse:$(form).find("#captcha-response").val()
                },
                dataType: "json"
            }).done(function (response) {

                if (response.attempt_limit_exhausted) {
                    $(self.mdoptions.fields.generateOtpButton).hide();
                }
                if (response.Success === 'success') {
                    $(form).find('.otp_generatenote').html(response.otp_message);
                    $(form).find('.otp_generatenote').delay(5000).fadeOut(800);
                    var leftAttamp= resendLimit-response.resend_link_count;
                    var otpMessage=$t('Resend OTP attempt left')+' '+leftAttamp+'/'+resendLimit;
                    $(form).find(".resend-link-attempt").html(otpMessage);
                    $(form).find('.resend-link-attempt').show();
                    $(form).find('.send_otp_login').hide();
                    $("#otp").val("");
                    self.showResendButton(form,response.resend_link_count);
                } else {
                    if (smsError.length === 0 ) {
                        $(self.mdoptions.fields.generateOtpButton).after('<p class="smserror">'+response.Success+'</p>');
                        $(".custom-customer-create-link").removeClass("show-customer-account");
                    }
                    if (smsError.length > 0 ) {
                        smsError.html(response.Success);
                        $("#otp").val("");
                        $(".custom-customer-create-link").addClass("show-customer-account");
                    }

                    $(".smserror").css("color","red");
                    $(".custom-customer-create-link").addClass("show-customer-account");
                }

                $(form).find('#login_mobile').attr("disabled", false);
            });
        },
        showResendButton:function (form,resend_link_count) {
            if ($("#captcha-response").length) {
                grecaptcha.reset($(form).find("#captcha-widget-id").val());
            }
            $(form).find(".resendotp-block").show();
            var resendLimit  = $(form).find("#resend_limit").val();
            if (resend_link_count < resendLimit) {
                $(form).find(".resend-wait").show();
                var resendTime = $(form).find('#resend_limit_time').val();
                var resendLinkDisable = $(form).find('#resend_link_disable').val();
                if (resendLinkDisable==1) {
                    $(form).find(".resendlink").hide();
                    $(form).find(".resend-wait").hide();
                } else {
                    $(form).find(".resendlink").hide();
                    if (resendTime < 5) {
                        resendTime = 5;
                    }
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
            } else {
                $(form).find(".resendlink").hide();
                $(form).find(".resend-wait").hide();
            }
        },
        verifyOtpCode:function (form) {
            var self = this;
            var otpText = $(form).find('.otp_text');
            var actionType = $(form).find("#form_type").val();
            var inputFieldValue = $(form).find("#login_mobile").val();
            var mobile = "";
            var email  = "";
            var countrycode = "";
            
            if ($.isNumeric(inputFieldValue.slice(0, 1))) {
                mobile = inputFieldValue;
                countrycode = $(form).find("#countryreg").val();
            } else {
                email = inputFieldValue;
            }

            if ($(form).find('#otp').val() !== '') {
                $(form).find('.otp_text').html('');
                var otp = $(form).find("#otp").val();
                $.ajax({
                    showLoader: true,
                    url: self.mdoptions.otpVerifyUrlLogin,
                    method: "POST",
                    data: {
                        otp: otp,
                        countrycode: countrycode,
                        mobile: mobile,
                        email:email
                    },
                    dataType: "json"
                }).done(function (response) {
                    if (response) {
                        $(form).find(".otp_generatenote").html('').fadeIn();
                        $(form).find('.otp_text').html(response.message);
                        if (response.success === 'Verified') {
                            $('.otp_text').css('color','green');
                            $('.otp_text').show();
                            setTimeout(function() {
                            $('#md-login-popup .md-error span').text('');
                            }, 1000);
                            $('.customer-account-create form.form-create-account .otp-value-verify').val("Verified");
                            $('.verifiedotp').val(1);
                            $('.verifiedotpval').val(otp);
                            $('.send_otp_login').hide();
                            $('.resendotp-block').hide();

                            $('.custom-login-action-button .action.login.primary').trigger( "click" );

                            if (self.mdoptions.fields.autoVerify==1 && actionType!='forgot' && actionType!='create') {
                                $(form).submit();
                            } else {
                                $(self.mdoptions.fields.actionButton).show();
                                $(self.mdoptions.fields.actionButton).attr("disabled", false);
                            }
                            $(".md_reset_password").show();
                            $(self.mdoptions.fields.loginInputField).prop("readonly", true);
                            $(self.mdoptions.fields.loginInputField).css('pointer-events','none');
                            $(".sms-profile-register").show();
                            $(".fieldset.mobile-verification").hide();
                            var loginMobileValue = $('#login_mobile').val();
                            $('.customer-account-create #customer_mobile').val(loginMobileValue);
                        } else {
                            otpText.css('color','red');
                            $(form).find('.verifiedotp').val(0);
                            $('.verifiedotpval').val(0);
                            $('.countrycodeval').val(0);
                            $('#otp').attr("disabled", false);
                            $('.otp_text').show();
                        }
                    }
                });
            } else {
                otpText.html($t('Please enter OTP')).css('color','red');
                $(form).find('.verifiedotp').val(0);
                $('.verifiedotpval').val(0);
                $('.countrycodeval').val(0);
                $('#otp').attr("disabled", false);
            }
        }

    });

    return $.mage.mdLoginOTP;
});

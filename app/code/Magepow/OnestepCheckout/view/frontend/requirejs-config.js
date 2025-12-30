

var config = {};
if (window.location.href.indexOf('onestepcheckout') !== -1) {
    config = {
        map: {
            '*':
                {
                'Magento_Checkout/js/model/shipping-rate-service': 'Magepow_OnestepCheckout/js/model/shipping/shipping-rate-service',
                'Magento_Checkout/js/model/shipping-rates-validator': 'Magepow_OnestepCheckout/js/model/shipping/shipping-rates-validator',
                'Magento_CheckoutAgreements/js/model/agreements-assigner': 'Magepow_OnestepCheckout/js/model/agreement/agreements-assigner',
                'Magento_Checkout/js/action/select-payment-method':'Magepow_OnestepCheckout/js/action/select-payment-method'
            },
            'Magepow_OnestepCheckout/js/model/shipping/shipping-rates-validator': {
                'Magento_Checkout/js/model/shipping-rates-validator': 'Magento_Checkout/js/model/shipping-rates-validator'
            },
            'Magento_Checkout/js/model/shipping-save-processor/default': {
                'Magento_Checkout/js/model/full-screen-loader': 'Magepow_OnestepCheckout/js/model/one-step-checkout-loader'
            },
            'Magento_Checkout/js/action/set-billing-address': {
                'Magento_Checkout/js/model/full-screen-loader': 'Magepow_OnestepCheckout/js/model/one-step-checkout-loader'
            },
            'Magento_SalesRule/js/action/set-coupon-code': {
                'Magento_Checkout/js/model/full-screen-loader': 'Magepow_OnestepCheckout/js/model/onestepcheckout-loader/discount'
            },
            'Magento_SalesRule/js/action/cancel-coupon': {
                'Magento_Checkout/js/model/full-screen-loader': 'Magepow_OnestepCheckout/js/model/onestepcheckout-loader/discount'
            },
            'Magepow_OnestepCheckout/js/model/one-step-checkout-loader': {
                'Magento_Checkout/js/model/full-screen-loader': 'Magento_Checkout/js/model/full-screen-loader'
            },

        },
        config: {
            mixins: {
                'Magento_Braintree/js/view/payment/method-renderer/paypal': {
                    'Magepow_OnestepCheckout/js/view/payment/braintree-paypal-mixins': true
                },
                'Magento_Checkout/js/action/place-order': {
                    'Magepow_OnestepCheckout/js/action/place-order-mixins': true
                },
                /*'Magento_Paypal/js/action/set-payment-method': {
                    'Magepow_OnestepCheckout/js/model/set-payment-method-mixin': true
                },
                'Magento_Paypal/js/in-context/express-checkout-wrapper': {
                    'Magepow_OnestepCheckout/js/in-context/express-checkout-wrapper-mixin': true
                },
                'Magento_Paypal/js/view/payment/method-renderer/in-context/checkout-express': {
                    'Magepow_OnestepCheckout/js/view/payment/method-renderer/in-context/checkout-express-mixin': true
                },*/
                'Magento_Paypal/js/action/set-payment-method': {
                    'Magepow_OnestepCheckout/js/action/set-payment-method-mixin': true
                },
                

            }
        }
    };

    if (window.location.href.indexOf('#') !== -1) {
        window.history.pushState("", document.title, window.location.pathname);
    }
}

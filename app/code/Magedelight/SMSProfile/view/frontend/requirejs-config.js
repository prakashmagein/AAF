var config = {
    map: {
        '*': {
            intlTelInput: 'Magedelight_SMSProfile/js/intlTelInput',
            'Magento_OfflinePayments/template/payment/cashondelivery.html':
            'Magedelight_SMSProfile/template/payment/cashondelivery.html',
            'Magento_Checkout/template/authentication.html':
              'Magedelight_SMSProfile/template/authentication.html',
            'Magento_Checkout/template/form/element/email.html':
              'Magedelight_SMSProfile/template/form/element/email.html'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/authentication': {
                'Magedelight_SMSProfile/js/view/authentication-mixin': true
            },
            'Magento_Checkout/js/view/form/element/email': {
                'Magedelight_SMSProfile/js/view/form/element/email-mixin': true
            }
        }
    }
};
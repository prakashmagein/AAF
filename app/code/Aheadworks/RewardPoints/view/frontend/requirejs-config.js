var config = {
    map: {
        '*': {
            awRewardPointsShare: 'Aheadworks_RewardPoints/js/aw-rp-share',
            awRewardPointsAjax: 'Aheadworks_RewardPoints/js/aw-reward-points-ajax'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/model/quote': {
                'Aheadworks_RewardPoints/js/model/quote-mixin': true
            },
            'PayPal_Braintree/js/view/payment/method-renderer/paypal': {
                'Aheadworks_RewardPoints/js/view/payment/method-renderer/paypal-reward-points': true
            }
        }
    }
};

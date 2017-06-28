
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Woopra_Analytics/js/action/place-order': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Woopra_Analytics/js/view/shipping': true
            },
            'Magento_Customer/js/action/check-email-availability': {
                'Woopra_Analytics/js/action/check-email-availability': true
            },
            'Magento_Checkout/js/model/shipping-rate-processor/new-address': {
                'Woopra_Analytics/js/model/shipping-rate-processor/new-address': true
            },
            'Magento_Checkout/js/model/shipping-rate-processor/customer-address' : {
                'Woopra_Analytics/js/model/shipping-rate-processor/customer-address': true
            }
        }
    }
};

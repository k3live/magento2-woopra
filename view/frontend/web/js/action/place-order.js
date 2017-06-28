
define(['mage/utils/wrapper'], function (wrapper) {
    'use strict';

    return function (placeOrderAction) {
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, redirectOnSuccess) {
            woopra.track({
                name: 'magento checkout_order_placed'
            });
            return originalAction(paymentData, redirectOnSuccess);
        });
    };
});

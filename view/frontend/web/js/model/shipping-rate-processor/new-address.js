
define(['mage/utils/wrapper'], function (wrapper) {
    'use strict';

    return function (target) {
        target.getRates = wrapper.wrap(target.getRates, function (originalAction, address) {
            woopra.track({
                name: 'magento checkout_shipping_address'
            });
            return originalAction(address);
        });
        return target;
    };
});


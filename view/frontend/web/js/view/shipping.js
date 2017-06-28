
define(function () {
    'use strict';

    return function (target) {
        return target.extend({
            setShippingInformation: function () {
            woopra.track({
                name: 'magento checkout_shipping_method'
            });
                this._super();
            }
        });
    }
});

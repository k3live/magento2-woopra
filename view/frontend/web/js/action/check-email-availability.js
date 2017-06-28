
define(['mage/utils/wrapper'], function (wrapper) {
    'use strict';

    return function (checkEmailAction) {
        return wrapper.wrap(checkEmailAction, function (originalAction, deferred, email) {
            woopra.track({
                name: 'magento check_email_availability'
            });
            return originalAction(deferred, email);
        });
    };
});

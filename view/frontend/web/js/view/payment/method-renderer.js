define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'momo',
                component: 'MG_VnPay/js/view/payment/method-renderer/momo'
            }
        );
        return Component.extend({});
    }
);
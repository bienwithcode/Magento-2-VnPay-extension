define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'mage/url'
    ],
    function (ko, $, Component, url) {
        'use strict';

        return Component.extend({
            defaults: {
                redirectAfterPlaceOrder: false,
                template: 'MG_VnPay/payment/momo'
            },

            afterPlaceOrder: function () {
                window.location.replace(url.build('vnpay/payment/momo'));
            }
        });
    }
);
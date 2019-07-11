define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/view/payment/default'
    ],
    function (ko, $, Component, setPaymentMethodAction) {
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
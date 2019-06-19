define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'MG_VnPay/payment/momo'
            },

            afterPlaceOrder: function () {
                // setPaymentMethodAction(this.messageContainer);
                // return false;
                alert('hi');
                return false;
            }
        });
    }
);
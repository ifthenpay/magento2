/**
* Ifthenpay_Payment module dependency
*
* @category    Gateway Payment
* @package     Ifthenpay_Payment
* @author      Ifthenpay
* @copyright   Ifthenpay (http://www.ifthenpay.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
define([
    'Magento_Checkout/js/view/payment/default'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Ifthenpay_Payment/payment/payshopForm'
        },
        getLogoUrl: function () {
                return window.checkoutConfig.payment.ifthenpay_payshop.logoUrl;
        },
        getShowPaymentIcon: function () {
            return window.checkoutConfig.payment.ifthenpay_payshop.showPaymentIcon ? window.checkoutConfig.payment.ifthenpay_payshop.showPaymentIcon : false;
        },
        getTitle: function () {
            return window.checkoutConfig.payment.ifthenpay_payshop.title;
        },
    });
});

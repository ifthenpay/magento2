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
    'Magento_Checkout/js/view/payment/default',
    'mage/url'
], function (Component, url) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Ifthenpay_Payment/payment/ifthenpaygatewayForm'
        },
        redirectAfterPlaceOrder: false,
        getLogoUrl: function () {
            return window.checkoutConfig.payment.ifthenpay_ifthenpaygateway.logoUrl;
        },
        getShowPaymentIcon: function () {
            return window.checkoutConfig.payment.ifthenpay_ifthenpaygateway.showPaymentIcon ? window.checkoutConfig.payment.ifthenpay_ifthenpaygateway.showPaymentIcon : false;
        },
        afterPlaceOrder: function () {
            window.location.replace(url.build('ifthenpay/Frontend/IfthenpaygatewayRedirectToProviderCtrl'));
        },
        getTitle: function () {
            return window.checkoutConfig.payment.ifthenpay_ifthenpaygateway.title;
        }
    });
});

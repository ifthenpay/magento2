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
    'jquery',
    'domReady!'
], function (Component, $, documentReady) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Ifthenpay_Payment/payment/mbwayForm',
        },
        getCountryCodes: function () {
            return window.checkoutConfig.payment.ifthenpay_mbway.countryCodeOptions;
        },
        getLogoUrl: function () {
            return window.checkoutConfig.payment.ifthenpay_mbway.logoUrl;
        },
        getMobileIconUrl: function () {
            return window.checkoutConfig.payment.ifthenpay_mbway.mobileIconUrl;
        },
        getShowPaymentIcon: function () {
            return window.checkoutConfig.payment.ifthenpay_mbway.showPaymentIcon ? window.checkoutConfig.payment.ifthenpay_mbway.showPaymentIcon : false;
        },
        getData: function () {
            return {
                'method': this.item.method,
                'additional_data': {
                    'countryCode': $('#ifthenpay_mbway_country_code').val(),
                    'phoneNumber': $('#ifthenpay_mbway_phone_number').val()
                }
            };
        },
        getTitle: function () {
            return window.checkoutConfig.payment.ifthenpay_mbway.title;
        }
    });
});

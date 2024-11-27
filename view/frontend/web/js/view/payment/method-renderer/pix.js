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
    'mage/url'
], function (Component, $, url) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Ifthenpay_Payment/payment/pixForm'
        },
        redirectAfterPlaceOrder: false,
        getLogoUrl: function () {
            return window.checkoutConfig.payment.ifthenpay_pix.logoUrl;
        },
        getShowPaymentIcon: function () {
            return window.checkoutConfig.payment.ifthenpay_pix.showPaymentIcon ? window.checkoutConfig.payment.ifthenpay_pix.showPaymentIcon : false;
        },
        afterPlaceOrder: function () {
            window.location.replace(url.build('ifthenpay/Frontend/PixRedirectToProviderCtrl'));
        },
        getData: function () {
            return {
                'method': this.item.method,
                'additional_data': {
                    'name': $('#ifthenpay_pix_name').val(),
                    'cpf': $('#ifthenpay_pix_cpf').val(),
                    'email': $('#ifthenpay_pix_email').val(),
                    'phone': $('#ifthenpay_pix_phone').val(),
                    'address': $('#ifthenpay_pix_address').val(),
                    'streetNumber': $('#ifthenpay_pix_streetNumber').val(),
                    'city': $('#ifthenpay_pix_city').val(),
                    'zipCode': $('#ifthenpay_pix_zipCode').val(),
                    'state': $('#ifthenpay_pix_state').val(),
                }
            };
        },
        getTitle: function () {
            return window.checkoutConfig.payment.ifthenpay_pix.title;
        }
    });
});

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
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    rendererList.push(
        {
            type: 'ifthenpay_multibanco', // must equals the payment code
            component: 'Ifthenpay_Payment/js/view/payment/method-renderer/multibanco'
        },
        {
            type: 'ifthenpay_mbway', // must equals the payment code
            component: 'Ifthenpay_Payment/js/view/payment/method-renderer/mbway'
        },
        {
            type: 'ifthenpay_payshop', // must equals the payment code
            component: 'Ifthenpay_Payment/js/view/payment/method-renderer/payshop'
        },
        {
            type: 'ifthenpay_ccard', // must equals the payment code
            component: 'Ifthenpay_Payment/js/view/payment/method-renderer/ccard'
        },
        {
            type: 'ifthenpay_cofidis', // must equals the payment code
            component: 'Ifthenpay_Payment/js/view/payment/method-renderer/cofidis'
        },
        {
            type: 'ifthenpay_pix', // must equals the payment code
            component: 'Ifthenpay_Payment/js/view/payment/method-renderer/pix'
        },
        {
            type: 'ifthenpay_ifthenpaygateway', // must equals the payment code
            component: 'Ifthenpay_Payment/js/view/payment/method-renderer/ifthenpaygateway'
        }
    );

    /** Add view logic here if you needed */
    return Component.extend({});
});

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
            type: 'multibanco', // must equals the payment code
            component: 'Ifthenpay_Payment/js/view/payment/method-renderer/multibanco'
        },
        {
            type: 'mbway', // must equals the payment code
            component: 'Ifthenpay_Payment/js/view/payment/method-renderer/mbway'
        },
        {
            type: 'payshop', // must equals the payment code
            component: 'Ifthenpay_Payment/js/view/payment/method-renderer/payshop'
        },
        {
            type: 'ccard', // must equals the payment code
            component: 'Ifthenpay_Payment/js/view/payment/method-renderer/ccard'
        }
    );

    /** Add view logic here if you needed */
    return Component.extend({});
});
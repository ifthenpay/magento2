/**
* Ifthenpay_Payment module dependency
*
* @category    Gateway Payment
* @package     Ifthenpay_Payment
* @author      Ifthenpay
* @copyright   Ifthenpay (http://www.ifthenpay.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Ifthenpay_Payment/js/model/pixValidator'
    ],
    function (Component, additionalValidators, pixValidator) {
        'use strict';
        additionalValidators.registerValidator(pixValidator);
        return Component.extend({});
    }
);

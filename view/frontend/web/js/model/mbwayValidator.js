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
        'mage/translate',
        'Magento_Ui/js/model/messageList',
        'jquery',
        'domReady!'
    ],
    function ($t, messageList, $, documentReady) {
        'use strict';
        return {
            validate: function () {
                var mbwayPhoneRegex = /^((91|96|92|93)[0-9]{7})$/g;
                var isValid = true; //Put your validation logic here
                var paymentMethodSelected = $('input[name="payment[method]"]:checked').attr('id');
                if (paymentMethodSelected === 'mbway') {
                    var mbwayPhoneNumber = $('#mbwayPhoneNumber').val();
                    if (!mbwayPhoneNumber) {
                        isValid = false;
                        messageList.addErrorMessage({ message: $t('mbwayPhoneRequired')});
                    } else if (!mbwayPhoneRegex.test($('#mbwayPhoneNumber').val())) {
                        isValid = false;
                        messageList.addErrorMessage({ message: $t('mbwayPhoneInvalid')});
                    }
                }
                return isValid;
            }
        }
    }
);

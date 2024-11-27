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

                let isValid = true; //Put your validation logic here
                const paymentMethodSelected = $('input[name="payment[method]"]:checked').attr('id');

                if (paymentMethodSelected === 'ifthenpay_pix' && $('#ifthenpay_pix_name').length) {
                    const pixName = $('#ifthenpay_pix_name').val();
                    if (!pixName) {
                        messageList.addErrorMessage({ message: $t('pixNameRequired') });
                        return false
                    }
                }

                if (paymentMethodSelected === 'ifthenpay_pix' && $('#ifthenpay_pix_cpf').length) {
                    const pixCpf = $('#ifthenpay_pix_cpf').val();
                    const cpfRegex = /^(\d{3}\.\d{3}\.\d{3}-\d{2}|\d{11})$/;

                    if (!pixCpf) {
                        messageList.addErrorMessage({ message: $t('pixCpfRequired') });
                        return false
                    } else if (!cpfRegex.test(pixCpf)) {
                        messageList.addErrorMessage({ message: $t('pixCpfInvalid') });
                        return false
                    }
                }

                if (paymentMethodSelected === 'ifthenpay_pix' && $('#ifthenpay_pix_email').length) {
                    const pixEmail = $('#ifthenpay_pix_email').val();
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    if (!pixEmail) {
                        messageList.addErrorMessage({ message: $t('pixEmailRequired') });
                        return false
                    } else if (!emailRegex.test(pixEmail)) {
                        messageList.addErrorMessage({ message: $t('pixEmailInvalid') });
                        return false
                    }
                }
                return isValid;
            }
        }
    }
);

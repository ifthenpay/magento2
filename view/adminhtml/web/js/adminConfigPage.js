/**
* Ifthenpay_Payment module dependency
*
* @category    Gateway Payment
* @package     Ifthenpay_Payment
* @author      Ifthenpay
* @copyright   Ifthenpay (http://www.ifthenpay.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

require([
    'jquery',
    'domReady!',
    'mage/url',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function($, documentReady, url, alert, $t){
    var subEntidadeInput = $('#payment_us_ifthenpay_multibanco_subEntidade');
    var documentFragment = $(document.createDocumentFragment());

    $('#payment_us_ifthenpay_multibanco_entidade').change(function (event) {
        var eventTarget = $(event.target);
        $.ajax({
            url: window.urlChangeEntidade,
            data: {
                form_key: window.FORM_KEY,
                entidade: eventTarget.val()
            },
            showLoader: true,
            type: 'POST',
            dataType: 'json',
            success: function(data, status, xhr) {
                subEntidadeInput.empty();
                Object.keys(data).forEach(key => {
                    data[key][1].SubEntidade.forEach((subEntidade) => {
                        documentFragment.append($(`<option value="${subEntidade}">${subEntidade}</option>`));
                    });
                });
                subEntidadeInput.append(documentFragment);
            },
            error: function (xhr, status, errorThrown) {
                alert({
                    title: 'Error!',
                    content: $t('errorRetreivingSubEntidade'),
                    actions: {
                        always: function(){}
                    }
                });
            }
        });
    });

    $('.chooseNewEntidadeBtn').click(function(event) {
        $.ajax({
            url: window.urlChooseNewEntidadeSubEntidade,
            data: {
                form_key: window.FORM_KEY,
                paymentMethod: $(event.target).parent().attr('data-paymentmethod')
            },
            showLoader: true,
            type: 'GET',
            dataType: 'json',
            success: function(data, status, xhr) {
                location.reload();
            },
            error: function (xhr, status, errorThrown) {
                alert({
                    title: 'Error!',
                    content: $t('errorResetingEntidadeSubEntidade'),
                    actions: {
                        always: function(){}
                    }
                });
            }
        });
    });

    $('.addNewAccountBtn').click(function(event) {
        $.ajax({
            url: window.urlAddNewAccount,
            data: {
                form_key: window.FORM_KEY,
                paymentMethod: $(event.target).parent().attr('data-paymentmethod')
            },
            showLoader: true,
            type: 'GET',
            dataType: 'json',
            success: function(data, status, xhr) {
                alert({
                    title: 'Success!',
                    content: $t('emailRequestNewAccount'),
                    actions: {
                        always: function(){}
                    }
                });
            },
            error: function (xhr, status, errorThrown) {
                alert({
                    title: 'Error!',
                    content: $t('errorRequestNewAccount'),
                    actions: {
                        always: function(){}
                    }
                });
            }
        });
    });

    $('.resetIfthenpayAccounts').click(function(event) {
        $.ajax({
            url: window.urlResetAccounts,
            data: {
                form_key: window.FORM_KEY,
                paymentMethod: $(event.target).parent().attr('data-paymentmethod')
            },
            showLoader: true,
            type: 'GET',
            dataType: 'json',
            success: function(data, status, xhr) {
                if (data.success) {
                    location.reload();
                } else {
                    alert({
                        title: 'Error!',
                        content: $t('errorResetingAccounts'),
                        actions: {
                            always: function(){}
                        }
                    });
                }

            },
            error: function (xhr, status, errorThrown) {
                alert({
                    title: 'Error!',
                    content: $t('errorResetingAccounts'),
                    actions: {
                        always: function(){}
                    }
                });
            }
        });
    });
});

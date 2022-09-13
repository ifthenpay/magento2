/**
* Ifthenpay_Payment module dependency
*
* @category    Gateway Payment
* @package     Ifthenpay_Payment
* @author      Ifthenpay
* @copyright   Ifthenpay (http://www.ifthenpay.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

function extractScopeId(){
    let needle = 'website';
    let scopeId = '0';
    let currUrl = window.location.href

    let scopeStrIndex = currUrl.indexOf(needle);

    if (scopeStrIndex !== -1) {
        scopeId = currUrl.substring(scopeStrIndex + needle.length).replace(/^\/|\/$/g, '');
    }

    return scopeId;
}

require([
    'jquery',
    'domReady!',
    'mage/url',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function($, documentReady, url, alert, $t){
    var subEntidadeInput = $('select[id*="ifthenpay_multibanco_subEntidade"]');
    var documentFragment = $(document.createDocumentFragment());

    $('select[id*="ifthenpay_multibanco_entidade"]').change(function (event) {
        var eventTarget = $(event.target);

        let scopeId = extractScopeId();

        $.ajax({
            url: window.urlChangeEntidade,
            data: {
                scope_id: scopeId,
                form_key: window.FORM_KEY,
                entidade: eventTarget.val()
            },
            showLoader: true,
            type: 'POST',
            dataType: 'json',
            success: function(data, status, xhr) {
                subEntidadeInput.empty();
                if (Array.isArray(data) && data.length && data[0][0]) {
                    
                    Object.keys(data).forEach(key => {
                        data[key][0].SubEntidade.forEach((subEntidade) => {
                            documentFragment.append($(`<option value="${subEntidade}">${subEntidade}</option>`));
                        });
                    });
                    subEntidadeInput.append(documentFragment);
                }

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

    $('.addNewAccountBtn').click(function(event) {
        let scopeId = extractScopeId();

        $.ajax({
            url: window.urlAddNewAccount,
            data: {
                scope_id: scopeId,
                form_key: window.FORM_KEY,
                paymentMethod: $(event.target).parent().attr('data-paymentmethod')
            },
            showLoader: true,
            type: 'GET',
            dataType: 'json',
            success: function(data, status, xhr) {
                if (data.error) {
                    alert({
                        title: 'Error!',
                        content: $t('errorRequestNewAccount'),
                        actions: {
                            always: function(){}
                        }
                    });
                }
                else{
                    alert({
                        title: 'Success!',
                        content: $t('emailRequestNewAccount'),
                        actions: {
                            always: function(){}
                        }
                    });
                }
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

    
    $('.resetBackOfficeKey').click(function(event) {

        let scopeId = extractScopeId();

        $.ajax({
            url: window.urlResetBackofficeKey,
            data: {
                scope_id: scopeId,
                form_key: window.FORM_KEY,
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
                        content: $t('errorResetingBackofficeKey'),
                        actions: {
                            always: function(){}
                        }
                    });
                }

            },
            error: function (xhr, status, errorThrown) {
                alert({
                    title: 'Error!',
                    content: $t('errorResetingBackofficeKey'),
                    actions: {
                        always: function(){}
                    }
                });
            }
        });
    });
    $('.requestDynamicMb').click(function(event) {

        let scopeId = extractScopeId();

        $.ajax({
            url: window.urlAddMultibancoDeadline,
            data: {
                scope_id: scopeId,
                form_key: window.FORM_KEY,
            },
            showLoader: true,
            type: 'GET',
            dataType: 'json',
            success: function(data, status, xhr) {
                alert({
                    title: 'Success!',
                    content: $t('emailRequestDynamicMbAccount'),
                    actions: {
                        always: function(){}
                    }
                });
            },
            error: function (xhr, status, errorThrown) {
                alert({
                    title: 'Error!',
                    content: $t('errorRequestMultibancoDeadline'),
                    actions: {
                        always: function(){}
                    }
                });
            }
        });
    });
});

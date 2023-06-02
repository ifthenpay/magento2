require([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'mage/translate'
], function ($, mConfirm, $t) {

    // variables from the phtml
    // ifthenpay_storeId
    // ifthenpay_urlRequestRefundToken
    // ifthenpay_urlVerifyRefundToken
    // ifthenpay_IsPaymentMethodRefundable
    // ifthenpay_IsPaymentMethodRefundEnabled


    $(document).ready(function () {

        placeProxyButton();

        // Select the div element you want to observe
        const targetElement = document.getElementById('creditmemo_item_container');

        // Create a new instance of MutationObserver
        const observer = new MutationObserver((mutations) => {
            // Callback function to execute when a change occurs
            mutations.forEach((mutation) => {
                // Check if the div element has been added or modified
                if (mutation.addedNodes.length > 0 || mutation.type === 'characterData') {

                    placeProxyButton();
                }
            });
        });

        // Configuration options for the MutationObserver
        const config = { childList: true, subtree: true, characterData: true };

        // Start observing the target element with the specified configuration
        observer.observe(targetElement, config);


    });

    function placeProxyButton() {
        if (ifthenpay_IsPaymentMethodRefundable === 'true') {

            window.dom_if_refundBtn = $('.order-totals-actions button.refund');

            // if payment method has not refundable option in config, then remove the button
            if (ifthenpay_IsPaymentMethodRefundEnabled !== 'true') {
                dom_if_refundBtn.detach();
                return;
            }

            // if the button is already a proxy, then return
            if (dom_if_refundBtn.hasClass('refund_proxy')) {
                return;
            }


            // create and add the proxy button
            window.dom_if_proxyBtn = $('<button>');

            const classes = dom_if_refundBtn.attr('class').split(' ');

            dom_if_proxyBtn.addClass(classes);
            dom_if_proxyBtn.addClass('refund_proxy');
            dom_if_proxyBtn.text(dom_if_refundBtn.text());
            dom_if_proxyBtn.attr('type', dom_if_refundBtn.attr('type'));


            dom_if_refundBtn.after(dom_if_proxyBtn);
            dom_if_refundBtn.detach();



            dom_if_proxyBtn.on('click', function (event) {
                showConfirmationModal();
            });

        }
    }


    function showConfirmationModal() {

        let amountElement = $('#creditmemo_item_container section .admin__page-section-content .creditmemo-totals .order-subtotal-table tfoot tr td span.price');
        let amount = amountElement.text() ? amountElement.text() + '' : '';
        amount = amount.replace(/€/g, "");


        let content = `
        <p>${$t('sureRefundAmount')} ${amount}€?</p>
        <p>${$t('operationIrreversible')}</p>
        `;

        mConfirm({

            title: $t('paymentRefund'),
            content: content,
            actions: {
                confirm: function () {

                    ajaxRequestRefundToken();
                },
                cancel: function () {
                    return false;
                } // the cancel does not require any action
            }
        });
    }


    function showTokenModal(response) {
        let content = `
        <p>${$t('checkEmailToken')}</p>
        <div class="modal_input_div">
        <label>${$t('securityCode')}</label for="refund_token">
        <input id="refund_token" type="text" name="refund_token" placeholder="#####" />
        </div>

        `;

        if (response.isSuccess === true) {
            mConfirm({
                title: $t('paymentRefund'),
                content: content,
                actions: {
                    confirm: function () {
                        let token = $('#refund_token').val() ? $('#refund_token').val() + '' : '';

                        ajaxVerifyRefundToken(token);
                    },
                    cancel: function () { } // the cancel does not require any action
                }
            });
        }
        else if (response.isSuccess === false) {
            showErrorModal(response);
        }
        else {
            showErrorModal({ message: $t('somethingWrong') });
        }
    }

    function showErrorModal(response) {
        mConfirm({
            title: $t('error'),
            content: `<p>${response.errorMessage}</p>`,
            actions: {
                confirm: function () { },
                cancel: function () { }
            }
        });
    }

    function resumeRefundProcess() {
        // trigger click event
        dom_if_proxyBtn.after(dom_if_refundBtn);
        dom_if_proxyBtn.detach();
        dom_if_refundBtn.trigger('click');
    }


    async function ajaxRequestRefundToken() {
        $.ajax({
            method: 'GET',
            dataType: 'json',
            url: ifthenpay_urlRequestRefundToken,
            showLoader: true,
            data: {
                form_key: window.FORM_KEY,
                storeId: ifthenpay_storeId
            }
        })
            .done(function (response) {

                if (response.isSuccess === true) {
                    showTokenModal(response);
                }
                if (response.isSuccess === false) {
                    showErrorModal(response);
                }
            })
            .fail(function () {
                showErrorModal({ message: $t('somethingWrong') });
            });
    }


    async function ajaxVerifyRefundToken(token) {
        $.ajax({
            method: 'GET',
            dataType: 'json',
            url: ifthenpay_urlVerifyRefundToken,
            showLoader: true,
            data: {
                form_key: window.FORM_KEY,
                token: token,
                storeId: ifthenpay_storeId
            }
        })
            .done(function (response) {
                if (response.isSuccess === false) {
                    showErrorModal(response);
                    return;
                }

                resumeRefundProcess();
            })
            .fail(function () {
                showErrorModal({ message: $t('somethingWrong') });
            });
    }
});

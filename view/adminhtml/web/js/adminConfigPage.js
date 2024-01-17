require([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'mage/translate'
], function ($, mConfirm, $t) {
    $(document).ready(function () {

        // variables from the block Main _getExtraJs
        // ifthenpay_scope
        // ifthenpay_scopeCode
        // ifthenpay_storeId
        // ifthenpay_urlResetBackofficeKey
        // ifthenpay_urlGetSubEntities
        // ifthenpay_urlRequestAccount
        // ifthenpay_urlRefreshAccounts
        // ifthenpay_urlGetMinMax
        // ifthenpay_dynamicMultibancoCode



        // add accounts refresh button
        addAccountsRefreshButton();

        // set label on load
        setMultbancoEntityLabel();

        // eventlistener: on selected entity will get the corresponding list of subentities
        $('select[id*="ifthenpay_multibanco_entity"]').on("change", function (event) {
            let eventTarget = $(event.target);
            let entity = eventTarget.val();

            if (!entity) {
                return;
            }
            ajaxGetSubEntities(entity);
        });


        // eventlistener: on selected cofidis key will get the corresponding min max
        $('select[id*="ifthenpay_cofidis_key"]').on("change", function (event) {
            let eventTarget = $(event.target);
            let key = eventTarget.val() ?? '';

            ajaxGetCofidisMinMax(ifthenpay_scope, ifthenpay_scopeCode, key);
        });


        // eventlistener: on click of the reset backoffice key button displays modal, which when confirmed will reset key through ajax call
        $("#reset_backoffice_key_btn").on("click", function () {
            mConfirm({
                title: $t('resetBackofficeKey'),
                content: $t('atentionResetBackofficeKey'),
                actions: {
                    confirm: function () {
                        ajaxResetBackofficeKey(ifthenpay_scope, ifthenpay_scopeCode);
                    },
                    cancel: function () { } // the cancel does not require any action
                }
            });
        });
        $(".request_account_btn").on("click", function (event) {

            let paymentMethod = $(event.target).parent().attr('data-paymentmethod');


            let content = $t('requestAccountFor') + translatePaymentMethod(paymentMethod) + '?';

            mConfirm({
                title: $t('requestAccount'),
                content: content,
                actions: {
                    confirm: function () {
                        ajaxRequestAccount(paymentMethod, ifthenpay_scope, ifthenpay_scopeCode, ifthenpay_storeId);
                    },
                    cancel: function () { } // the cancel does not require any action
                }
            });
        });
    });

    function addAccountsRefreshButton() {

        let targetElements = $('.ifthenpay-payment-logo');

        if (targetElements.length !== 0) {
            let targetElement = targetElements[0];

            targetElement.on("click", function (event) {
                if (event.shiftKey && event.altKey) {

                    mConfirm({
                        title: $t('refreshAccounts'),
                        content: $t('actionWillRefresh'),
                        actions: {
                            confirm: function () {
                                ajaxRefreshAccounts(ifthenpay_scope, ifthenpay_scopeCode);
                            },
                            cancel: function () { } // the cancel does not require any action
                        }
                    });
                }
            });
        }
    }



    /**
     * ajax function to get the available subentities of selected entity will display error message if no subentities are found
     * @param {*} entity
     */
    function ajaxGetSubEntities(entity) {
        $.ajax({
            method: 'GET',
            dataType: 'json',
            url: ifthenpay_urlGetSubEntities,
            showLoader: true,
            data: {
                entity: entity,
                form_key: window.FORM_KEY,
                scope: ifthenpay_scope,
                scopeCode: ifthenpay_scopeCode
            }
        })
            .done(function (response) {
                let subEntitiesSelect = $('select[id*="ifthenpay_multibanco_sub_entity"]');
                subEntitiesSelect.empty();

                ifth_clearErrorMessageInField('select[id*="ifthenpay_multibanco_sub_entity"]');

                if (response.subEntities) {
                    if (entity === ifthenpay_dynamicMultibancoCode) {
                        subEntitiesSelect.append('<option value="">' + $t('selectMultibancoKey') + '</option>');
                    }
                    else {
                        subEntitiesSelect.append('<option value="">' + $t('selectMultibancoSubEntity') + '</option>');
                    }

                    setMultbancoEntityLabel();

                    $.each(response.subEntities, function (key, value) {
                        subEntitiesSelect.append('<option value="' + value + '">' + value + '</option>');
                    });
                } else {
                    let errorMessage = response.errorMessage ? response.errorMessage : ifthenpay_standardErrorMessage;
                    ifth_displayErrorMessageInField('select[id*="ifthenpay_multibanco_sub_entity"]', errorMessage);
                }
            })
            .fail(function () {
                ifth_displayErrorMessageInField('select[id*="ifthenpay_multibanco_sub_entity"]', ifthenpay_standardErrorMessage);
            });
    }

    /**
     * ajax function to reset the backofficeKey, error treatment is dealt with on php side by setting an error message to session
     */
    function ajaxResetBackofficeKey(ifthenpay_scope, ifthenpay_scopeCode) {
        $.ajax({
            method: 'GET',
            dataType: 'json',
            url: ifthenpay_urlResetBackofficeKey,
            showLoader: true,
            data: {
                form_key: window.FORM_KEY,
                scope: ifthenpay_scope,
                scopeCode: ifthenpay_scopeCode
            }
        })
            .done(function () {
                location.reload();
            })
            .fail(function () {
                // do nothing
            });
    }

    function ajaxRequestAccount(paymentMethod, ifthenpay_scope, ifthenpay_scopeCode, ifthenpay_storeId) {
        $.ajax({
            method: 'GET',
            dataType: 'json',
            url: ifthenpay_urlRequestAccount,
            showLoader: true,
            data: {
                form_key: window.FORM_KEY,
                paymentMethod: paymentMethod,
                scope: ifthenpay_scope,
                scopeCode: ifthenpay_scopeCode,
                storeId: ifthenpay_storeId
            }
        })
            .done(function () {
                location.reload();
            })
            .fail(function () {
                // do nothing
            });
    }

    function ajaxRefreshAccounts(ifthenpay_scope, ifthenpay_scopeCode) {
        $.ajax({
            method: 'GET',
            dataType: 'json',
            url: ifthenpay_urlRefreshAccounts,
            showLoader: true,
            data: {
                form_key: window.FORM_KEY,
                scope: ifthenpay_scope,
                scopeCode: ifthenpay_scopeCode
            }
        })
            .done(function () {
                location.reload();
            })
            .fail(function () {
                // do nothing
            });
    }

    function ajaxGetCofidisMinMax(ifthenpay_scope, ifthenpay_scopeCode, key) {

        if (!key) {
            $('input[id*="ifthenpay_cofidis_min_order_total"]').val('');
            $('input[id*="ifthenpay_cofidis_max_order_total"]').val('');
            return;
        }
        $.ajax({
            method: 'GET',
            dataType: 'json',
            url: ifthenpay_urlGetMinMax,
            showLoader: true,
            data: {
                form_key: window.FORM_KEY,
                scope: ifthenpay_scope,
                scopeCode: ifthenpay_scopeCode,
                cofidis_key: key
            }
        })
            .done(function (response) {

                if (response.success) {
                    $('input[id*="ifthenpay_cofidis_min_order_total"]').val(response.min);
                    $('input[id*="ifthenpay_cofidis_max_order_total"]').val(response.max);
                }
            })
            .fail(function () {
                // do nothing
            });
    }

    /**
     * updates subentity/key label in configuration acording to selected Entity
     * example:
     * MB: Key
     * 12345: Sub Entity
     * 10342: Sub Entity
     */
    function setMultbancoEntityLabel() {
        let selectedEntity = $('select[id*="ifthenpay_multibanco_entity"]').val();
        let subEntityLabel = $('tr[id*="ifthenpay_multibanco_sub_entity"] .label label span');

        if (selectedEntity === ifthenpay_dynamicMultibancoCode) {
            subEntityLabel.text($t('multibancoKey'));
        }
        else {
            subEntityLabel.text($t('subEntity'));
        }
    }




    function translatePaymentMethod(paymentMethod) {

        switch (paymentMethod) {
            case "multibanco":
                return $t("multibanco");
            case "mbway":
                return $t("mbway");
            case "payshop":
                return $t("payshop");
            case "ccard":
                return $t("ccard");
            case "MB":
                return $t("mb");
            default:
                return paymentMethod;
        }
    }

});

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
        // ifthenpay_urlGetGatewayMethods
        // ifthenpay_dynamicMultibancoCode

        initGatewayMethodsSelection();

        function initGatewayMethodsSelection() {

            // hide the hidden inputs, its necessary when opening right after instalation of the module
            hideFormInput('payment_methods_select');
            hideFormSelect('default_payment_method_select');


            addEventlistener_onchangePaymentMethods();
            addEventlistener_onClickRequestMethod();

            const gatewayKeyElement = $('select[id$="ifthenpaygateway_key"]');
            const gatewayKeyValue = gatewayKeyElement.val();
            if (!gatewayKeyValue) {
                return;
            }

            ajaxGetGatewayMethodsAndUpdateDom(gatewayKeyValue);


        }


        function addEventlistener_onClickRequestMethod() {
            const inputMethodsContainer = $('tr[id$="payment_methods_select"]');

            const gatewayKeyElement = $('select[id$="ifthenpaygateway_key"]');
            const gatewayKeyValue = gatewayKeyElement.val();

            inputMethodsContainer.on('click', '.request_ifthenpaygateway_method', function () {

                const paymentMethod = $(this).data('method');

                mConfirm({
                    title: $t('resetBackofficeKey'),
                    content: $t('atentionResetBackofficeKey'),
                    actions: {
                        confirm: function () {
                            ajaxRequestGatewayMethod(gatewayKeyValue, paymentMethod, ifthenpay_scope, ifthenpay_scopeCode, ifthenpay_storeId);
                        },
                        cancel: function () { } // the cancel does not require any action
                    }
                });
            });
        }

        function ajaxRequestGatewayMethod(gatewayKey, paymentMethod, ifthenpay_scope, ifthenpay_scopeCode, ifthenpay_storeId) {
            $.ajax({
                method: 'GET',
                dataType: 'json',
                url: ifthenpay_urlRequestGatewayMethod,
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    gatewayKey: gatewayKey,
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



        function hideFormInput(targetId) {
            let targetElement = $('input[id$="' + targetId + '"]');
            if (targetElement) {
                targetElement.addClass('iftp_hidden');
            }
        }

        function hideFormSelect(targetId) {
            let targetElement = $('select[id$="' + targetId + '"]');
            if (targetElement) {
                targetElement.addClass('iftp_hidden');
            }
        }


        function addEventlistener_onchangePaymentMethods() {
            const inputMethodsId = $('input[id$="payment_methods_select"]').attr('id');
            const inputMethodsContainer = $('input[id$="payment_methods_select"]').parent();

            inputMethodsContainer.on('change', 'select, .method_checkbox_input', function (event) {
                const json = getGatewayMethodsJsonString(inputMethodsContainer);
                $('#' + inputMethodsId).val(json);
            });

            inputMethodsContainer.on('change', '.method_checkbox_input', function (event) {
                const isActive = $(event.target).is(":checked");
                const paymentMethod = $(event.target).data('method');

                const selectDefaultMethod = $('select[id$="default_payment_method_select"]');

                const target = selectDefaultMethod.find('option[data-method="' + paymentMethod + '"]');
                target.prop("disabled", !isActive);

                if (target.prop("selected")) {
                    target.prop("selected", false);
                    selectDefaultMethod.find("option").first().prop("selected", true);
                }
            });
        }



        function getGatewayMethodsJsonString(inputMethodsContainer) {
            const selectArray = inputMethodsContainer.find('.method_line');
            let jsonArray = {};
            selectArray.each(function () {

                const methodName = $(this).data('method');
                const checkboxVal = $(this).find('.method_checkbox_input').is(':checked') ? '1' : '0';
                const selectVal = $(this).find('select').val();
                const imageUrl = $(this).find('select').data('img_url');

                jsonArray[methodName] =
                {
                    'is_active': checkboxVal,
                    'account': selectVal,
                    'image_url': imageUrl
                };
            });

            return JSON.stringify(jsonArray);
        }


        addEventlistener_onchangeGatewayKey();

        function addEventlistener_onchangeGatewayKey() {
            $('select[id*="ifthenpay_ifthenpaygateway_key"]').on("change", function (event) {
                let eventTarget = $(event.target);
                let gatewayKey = eventTarget.val();
                if (!gatewayKey) {
                    return;
                }
                const willClearSelection = true;
                ajaxGetGatewayMethodsAndUpdateDom(gatewayKey, willClearSelection);
            });
        }


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
     *
     * @param {*} gatewayKey
     */
    function ajaxGetGatewayMethodsAndUpdateDom(gatewayKey, willClearSelection = false) {

        let inputMethodsElement = $('input[id$="payment_methods_select"]');
        let inputMethodsId = inputMethodsElement.attr('id');
        let inputMethodsName = inputMethodsElement.attr('name');
        let inputMethodsContainer = inputMethodsElement.parent();


        let inputDefaultMethodElement = $('select[id$="default_payment_method_select"]');
        let inputDefaultMethodId = inputDefaultMethodElement.attr('id');
        let inputDefaultMethodName = inputDefaultMethodElement.attr('name');
        let inputDefaultMethodContainer = inputDefaultMethodElement.parent();


        $.ajax({
            method: 'GET',
            dataType: 'json',
            url: ifthenpay_urlGetGatewayMethods,
            showLoader: true,
            data: {
                gatewayKey: gatewayKey,
                inputMethodsId: inputMethodsId,
                inputMethodsName: inputMethodsName,
                inputDefaultMethodId: inputDefaultMethodId,
                inputDefaultMethodName: inputDefaultMethodName,
                willClearSelection: willClearSelection,
                form_key: window.FORM_KEY,
                scope: ifthenpay_scope,
                scopeCode: ifthenpay_scopeCode
            }
        })
            .done(function (response) {

                if (response.error == true) {
                    ifth_displayErrorMessageInField('select[id*="ifthenpay_ifthenpaygateway_key"]', response.errorMessage);
                }

                inputMethodsContainer.html(response.methodsHtml);
                inputDefaultMethodContainer.html(response.defaultMethodHtml);

                dom_disableUncheckedDefaultPaymentMethods(inputMethodsId, inputDefaultMethodId);

            })
            .fail(function () {
                ifth_displayErrorMessageInField('select[id*="ifthenpay_ifthenpaygateway_key"]', ifthenpay_standardErrorMessage);
            });
    }


    function dom_disableUncheckedDefaultPaymentMethods(inputMethodsId, inputDefaultMethodId) {

        const methodsElementContainer = $('#' + inputMethodsId).parent();
        const defaultMethodElement = $('#' + inputDefaultMethodId);

        const methods = methodsElementContainer.find('.method_checkbox_input');

        methods.each(function (i, obj) {
            const method = $(obj).data("method");
            const isSwitchOn = $(obj).prop("checked");

            const target = defaultMethodElement.find('option[data-method="' + method + '"]');

            target.prop("disabled", !isSwitchOn);

            if (target.prop("selected")) {
                target.prop("selected", false);
                defaultMethodElement.find("option").first().prop("selected", true);
            }
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
            case "cofidis":
                return $t("cofidis");
            case "ifthenpaygateway":
                return $t("ifthenpaygateway");
            default:
                return paymentMethod;
        }
    }

});

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
    'Magento_Ui/js/modal/alert',
    'mage/template',
    'uiRegistry',
    'jquery/ui',
    'prototype',
    'mage/translate'
], function ($, documentReady, alert, mageTemplate, rg, ui, prototype, $t) {
    const storeId = $('#ifthenpay_store_id').val();
    const showCountdown = $('#ifthenpay_show_countdown').val();
    const checkStatusUrl = $('#ifthenpay_check_mbway_status_url').val();
    const resendNotificationUrl = $('#ifthenpay_resend_mbway_notification_url').val();
    const orderId = $('#ifthenpay_order_id').val();
    const countryCode = $('#ifthenpay_mbway_country_code').val();
    const phoneNumber = $('#ifthenpay_mbway_phone_number').val();
    const dom_transaction = $('#ifthenpay_transaction_id');
    const dom_countdownPanel = $('div.ifthenpay_countdown_panel');
    const dom_minutesElement = $('#ifthenpay_countdown_minutes');
    const dom_secondsElement = $('#ifthenpay_countdown_seconds');
    const dom_paymentConfirmPanel = $('#ifthenpay_mbway_payment_confirmed_panel');
    const dom_paymentRefusedPanel = $('#ifthenpay_mbway_payment_refused_panel');
    const dom_paymentErrorPanel = $('#ifthenpay_mbway_payment_error_panel');
    const dom_paymentOutOfTimePanel = $('#ifthenpay_mbway_payment_out_of_time_panel');
    const dom_ifthenpayResendMbwayNotificationDiv = $('#ifthenpay_resend_mbway_notification_div');
    const dom_resendMbwayNotificationBtn = $('#ifthenpay_resend_mbway_notification_btn');
    const dom_paymentReturnPanel = $('#ifthenpay_payment_return_panel');
    const checkIntervalMilliseconds = 10000;
    var timeMinutes = '04';
    var timeSeconds = '00';
    var countdownIntervalTimer;
    var checkIntervalTimer;


    // this verification will prevent a crash if template has been manipulated and the necessary elements are not present
    if (
        storeId &&
        showCountdown &&
        checkStatusUrl &&
        resendNotificationUrl &&
        orderId &&
        countryCode &&
        phoneNumber &&
        dom_transaction.length &&
        dom_countdownPanel.length &&
        dom_minutesElement.length &&
        dom_secondsElement.length &&
        dom_paymentConfirmPanel.length &&
        dom_paymentRefusedPanel.length &&
        dom_paymentErrorPanel.length &&
        dom_paymentOutOfTimePanel.length &&
        dom_paymentReturnPanel.length &&
        dom_ifthenpayResendMbwayNotificationDiv.length &&
        dom_resendMbwayNotificationBtn.length
    ) {
        init();
    }


    function init() {
        // if showCountdown is enabled
        if (showCountdown) {
        // start status check
        checkMBwayPaymentStatus();

        // start timer
        countdownTimer();

        setEventResendMbwayNotification();
        }
    }

    function checkMBwayPaymentStatus() {

        checkIntervalTimer = setInterval(() => {

            transactionId = dom_transaction.val();

            $.ajax({
                url: checkStatusUrl,
                data: {
                    form_key: window.FORM_KEY,
                    transaction_id: transactionId,
                    storeId: storeId
                },
                type: 'POST',
                dataType: 'json',
                success: function (response, status, xhr) {

                    if (response.orderStatus === 'pending') {
                        return;
                    }
                    if (response.orderStatus === 'paid') {
                        dom_paymentReturnPanel.hide();
                        dom_paymentConfirmPanel.show();
                        dom_ifthenpayResendMbwayNotificationDiv.hide();
                    }
                    if (response.orderStatus === 'refused') {
                        dom_paymentRefusedPanel.show();
                        dom_ifthenpayResendMbwayNotificationDiv.show();
                    }
                    if (response.orderStatus === 'expired') {
		                dom_paymentOutOfTimePanel.show();
                        dom_ifthenpayResendMbwayNotificationDiv.show();
                    }
                    if (response.orderStatus === 'error') {
                        dom_paymentErrorPanel.show();
                        dom_ifthenpayResendMbwayNotificationDiv.show();
                    }

                    dom_countdownPanel.hide();
                    clearInterval(countdownIntervalTimer);
                    clearInterval(checkIntervalTimer);
                },
                error: function (xhr, status, errorThrown) {
                    dom_countdownPanel.hide();
                    clearInterval(countdownIntervalTimer);
                    clearInterval(checkIntervalTimer);
                }
            });
        }, checkIntervalMilliseconds);
    }


    function countdownTimer() {
        countdownIntervalTimer = setInterval(() => {

            let minutes = parseInt(timeMinutes, 10);
            let seconds = parseInt(timeSeconds, 10);
            --seconds;
            minutes = (seconds < 0) ? --minutes : minutes;
            seconds = (seconds < 0) ? 59 : seconds;
            // 0 leftpad needs to be in type string
            let strSeconds = (seconds < 10) ? "0" + seconds : "" + seconds;

            dom_minutesElement.text(minutes);
            dom_secondsElement.text(strSeconds);

            if (minutes < 0) {
                dom_countdownPanel.hide();
                dom_paymentOutOfTimePanel.show();

                clearInterval(countdownIntervalTimer);
                clearInterval(checkIntervalTimer);
                dom_ifthenpayResendMbwayNotificationDiv.show();
            }
            if ((seconds <= 0) && (minutes <= 0)) {
                dom_countdownPanel.hide();
                dom_paymentOutOfTimePanel.show();

                clearInterval(countdownIntervalTimer);
                clearInterval(checkIntervalTimer);
                dom_ifthenpayResendMbwayNotificationDiv.show();
            }
            timeMinutes = '' + minutes;
            timeSeconds = '' + seconds;
        }, 1000);
    }


    function setEventResendMbwayNotification() {

        dom_resendMbwayNotificationBtn.click(function () {

            clearInterval(countdownIntervalTimer);
            clearInterval(checkIntervalTimer);
            dom_countdownPanel.hide();
            toggleVisibilityOfResponsePanel(false);
            dom_ifthenpayResendMbwayNotificationDiv.hide();

            let fullPhoneNumber = `${countryCode}#${phoneNumber}`;

            $.ajax({
                url: resendNotificationUrl,
                data: {
                    form_key: window.FORM_KEY,
                    orderId: orderId,
                    phoneNumber: fullPhoneNumber,
                    storeId: storeId
                },
                showLoader: true,
                type: 'POST',
                dataType: 'json',
                success: function (response, status, xhr) {
                    if (response.result !== 'success') {
                        alert({
                            title: $t('error'),
                            content: $t('resendMbwayNotificationError'),
                            actions: {
                                always: function () { }
                            }
                        });
                    };
                    alert({
                        title: $t('success'),
                        content: response.message,
                        actions: {
                            always: function () { }
                        }
                    });
                    timeMinutes = '4';
                    timeSeconds = '01';
                    dom_countdownPanel.show();


                    dom_transaction.val(response.transactionId);

                    // start status check
                    checkMBwayPaymentStatus();

                    // start timer
                    countdownTimer();

                },
                error: function (xhr, status, errorThrown) {
                    alert({
                        title: $t('error'),
                        content: $t('resendMbwayNotificationError'),
                        actions: {
                            always: function () { }
                        }
                    });
                }
            });
        });
    }

    function toggleVisibilityOfResponsePanel(isVisible) {
        if (isVisible) {
            dom_paymentConfirmPanel.show();
            dom_paymentRefusedPanel.show();
            dom_paymentErrorPanel.show();
            dom_paymentOutOfTimePanel.show();
        } else {
            dom_paymentConfirmPanel.hide();
            dom_paymentRefusedPanel.hide();
            dom_paymentErrorPanel.hide();
            dom_paymentOutOfTimePanel.hide();
        }
    }

});

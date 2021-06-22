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
], function($, documentReady, alert, mageTemplate, rg, ui, prototype, $t){
        var timer2 = '5:01';
        var minutesElement = $('#countdownMinutes');
        var secondsElement = $('#countdownSeconds');
        var appSpinner = $('#appSpinner');
        var countdownMbway = $('#countdownMbway');
        var resendMbwayNotificationBtn = $('#resendMbwayNotificationBtn');
        var interval;
        var interval2;

        function checkMBwayPaymentStatus() {
            interval2 = setInterval(function() {
                $.ajax({
                    url: $('#checkMbwayPaymentStatusUrl').val(),
                    data: {
                        form_key: window.FORM_KEY,
                        idPedido: $('#mbwayIdPedido').val()
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(response, status, xhr) {
                        if (response.orderStatus === 'paid') {
                            clearInterval(interval);
                            clearInterval(interval2);
                            $('div.mbwayCountdownPanel').hide();
                            $('#confirmMbwayOrder').show();
                        }
                        appSpinner.hide();
                    },
                    error: function (xhr, status, errorThrown) {
                        console.log('error checking mbway payment status');
                    }
                });
            }, 10000);
        }

        function mbwayOrderCancel() {
            countdownMbway.hide();
            appSpinner.show();

            $.ajax({
                url: $('#cancelMbwayOrderUrl').val(),
                data: {
                    form_key: window.FORM_KEY,
                    orderId: $('#ifthenpayOrderId').val()
                },
                type: 'POST',
                dataType: 'json',
                success: function(response, status, xhr) {
                    $('div.mbwayCountdownPanel').hide();
                    if (response.orderStatus === 'pending') {
                        $('#confirmMbwayOrder').hide();
                    } else {
                        $('#confirmMbwayOrder').show();
                        $('#cancelMbwayOrder').hide();
                    }
                    appSpinner.hide();
                },
                error: function (xhr, status, errorThrown) {
                    alert({
                        title: 'Error!',
                        content: $t('erroMbwayOrderVerify'),
                        actions: {
                            always: function(){}
                        }
                    });
                }
            });
        }

        function init() {
                interval = setInterval(() => {
                var timer = timer2.split(':');
                var minutes = parseInt(timer[0], 10);
                var seconds = parseInt(timer[1], 10);
                --seconds;
                minutes = (seconds < 0) ? --minutes : minutes;
                seconds = (seconds < 0) ? 59 : seconds;
                seconds = (seconds < 10) ? 0 + seconds : seconds;
                minutesElement.text(minutes);
                secondsElement.text(seconds)
                if (minutes < 0) {
                    mbwayOrderCancel();
                    clearInterval(interval);
                    clearInterval(interval2);
                }
                if ((seconds <= 0) && (minutes <= 0)) {
                    mbwayOrderCancel();
                    clearInterval(interval);
                    clearInterval(interval2);
                }
                timer2 = minutes + ':' + seconds;
            }, 1000);
        }
        if (countdownMbway.length > 0) {
            init();
            checkMBwayPaymentStatus();
        }
        
    if (resendMbwayNotificationBtn.length > 0) {
        resendMbwayNotificationBtn.click(function() {
            clearInterval(interval);
            clearInterval(interval2);
            $('div.mbwayCountdownPanel').hide();
            $.ajax({
                url: $('#resendMbwayNotificationControllerUrl').val(),
                data: {
                    form_key: window.FORM_KEY,
                    orderId: $('#ifthenpayOrderId').val(),
                    mbwayPhoneNumber: $('#mbwayPhoneNumber').val(),
                    mbwayTotalToPay: $('#mbwayTotalToPay').val(),
                },
                showLoader: true,
                type: 'POST',
                dataType: 'json',
                success: function(response, status, xhr) {
                    alert({
                        title: 'Success!',
                        content: response.success,
                        actions: {
                            always: function(){}
                        }
                    });
                    init();
                    checkMBwayPaymentStatus();
                    $('div.mbwayCountdownPanel').show();
                },
                error: function (xhr, status, errorThrown) {
                    alert({
                        title: 'Error!',
                        content: $t('resendMbwayNotificationError'),
                        actions: {
                            always: function(){}
                        }
                    });
                }
            });
        });
    }
});

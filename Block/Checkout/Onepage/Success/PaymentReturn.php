<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Block\Checkout\Onepage\Success;

use Ifthenpay\Payment\Config\ConfigVars;
use Magento\Checkout\Model\Session;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Ifthenpay\Payment\Model\ScopeConfigResolver;



class PaymentReturn extends Template
{

    public $checkoutSession;
    public $paymentReturnData;
    private $urlBuilder;
    private $scopeConfigResolver;
    public function __construct(
        Context $context,
        Session $checkoutSession,
        UrlInterface $urlBuilder,
        ScopeConfigResolver $scopeConfigResolver,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfigResolver = $scopeConfigResolver;
        parent::__construct($context, $data);


        $order = $this->checkoutSession->getLastRealOrder();
        $payment = $order->getPayment();
        $this->paymentReturnData = $payment->getAdditionalInformation();


        // set the correct template for the payment method
        switch ($this->getMethodCode()) {

            case ConfigVars::MULTIBANCO_CODE:
                $this->paymentReturnData['paymentLogo'] = $this->getViewFileUrl(ConfigVars::ASSET_PATH_CHECKOUT_LOGO_MULTIBANCO);
                // set the formatted reference
                $ref = $this->paymentReturnData['reference'];
                $this->paymentReturnData['formattedReference'] = substr($ref, 0, 3) . " " . substr($ref, 3, 3) . " " . substr($ref, 6);

                $this->setTemplate('Ifthenpay_Payment::checkout/onepage/success/multibancoPaymentReturn.phtml');
                break;
            case ConfigVars::PAYSHOP_CODE:
                $this->paymentReturnData['paymentLogo'] = $this->getViewFileUrl(ConfigVars::ASSET_PATH_CHECKOUT_LOGO_PAYSHOP);
                $this->setTemplate('Ifthenpay_Payment::checkout/onepage/success/payshopPaymentReturn.phtml');
                break;

            case ConfigVars::MBWAY_CODE:

                $store = $this->scopeConfigResolver->storeManager->getStore($this->scopeConfigResolver->storeId);

                $showCountdown = $store->getConfig('payment/ifthenpay_mbway/show_countdown');

                $this->paymentReturnData['storeId'] = $this->scopeConfigResolver->storeId;

                $this->paymentReturnData['paymentLogo'] = $this->getViewFileUrl(ConfigVars::ASSET_PATH_CHECKOUT_LOGO_MBWAY);
                $this->paymentReturnData['spinnerImg'] = $this->getViewFileUrl(ConfigVars::ASSET_PATH_SPINNER);
                $this->paymentReturnData['confirmImg'] = $this->getViewFileUrl(ConfigVars::ASSET_PATH_CHECKOUT_CONFIRM);
                $this->paymentReturnData['failImg'] = $this->getViewFileUrl(ConfigVars::ASSET_PATH_CHECKOUT_FAIL);
                $this->paymentReturnData['warningImg'] = $this->getViewFileUrl(ConfigVars::ASSET_PATH_CHECKOUT_WARNING);
                $this->paymentReturnData['checkMbwayPaymentStatusUrl'] = $this->urlBuilder->getUrl(ConfigVars::AJAX_URL_STR_GET_MBWAY_PAYMENT_STATUS);
                $this->paymentReturnData['resendMbwayNotificationUrl'] = $this->urlBuilder->getUrl(ConfigVars::AJAX_URL_STR_GET_MBWAY_RESEND_NOTIFICATION);
                $this->paymentReturnData['showCountdown'] = $showCountdown;

                $this->setTemplate('Ifthenpay_Payment::checkout/onepage/success/mbwayPaymentReturn.phtml');
                break;

            case ConfigVars::CCARD_CODE:
                $this->paymentReturnData['paymentLogo'] = $this->getViewFileUrl(ConfigVars::ASSET_PATH_CHECKOUT_LOGO_CCARD);
                $this->setTemplate('Ifthenpay_Payment::checkout/onepage/success/ccardPaymentReturn.phtml');
                break;
            case ConfigVars::COFIDIS_CODE:
                $this->paymentReturnData['paymentLogo'] = $this->getViewFileUrl(ConfigVars::ASSET_PATH_CHECKOUT_LOGO_COFIDIS);
                $this->setTemplate('Ifthenpay_Payment::checkout/onepage/success/cofidisPaymentReturn.phtml');
                break;
            case ConfigVars::IFTHENPAYGATEWAY_CODE:
                $this->paymentReturnData['paymentLogo'] = $this->getViewFileUrl(ConfigVars::ASSET_PATH_CHECKOUT_LOGO_IFTHENPAYGATEWAY);
                $this->setTemplate('Ifthenpay_Payment::checkout/onepage/success/ifthenpaygatewayPaymentReturn.phtml');
                break;
            case ConfigVars::PIX_CODE:
                $this->paymentReturnData['paymentLogo'] = $this->getViewFileUrl(ConfigVars::ASSET_PATH_CHECKOUT_LOGO_PIX);
                $this->setTemplate('Ifthenpay_Payment::checkout/onepage/success/pixPaymentReturn.phtml');
                break;
        }
    }


    public function getPaymentMethod(): string
    {
        return $this->getOrder()->getPayment()->getMethod();
    }

    public function getPayment()
    {
        $order = $this->checkoutSession->getLastRealOrder();

        return $order->getPayment()->getMethodInstance();
    }

    /**
     * Method Code.
     *
     * @return string
     */
    public function getMethodCode()
    {
        return $this->getPayment()->getCode();
    }
}

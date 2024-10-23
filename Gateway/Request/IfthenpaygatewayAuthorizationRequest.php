<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Gateway\Request;

use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Lib\Utility\Token;
use Ifthenpay\Payment\Lib\Utility\Currency;
use Ifthenpay\Payment\Lib\Utility\Locale;
use Ifthenpay\Payment\Lib\Utility\Time;

class IfthenpaygatewayAuthorizationRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;
    private $urlBuilder;
    private $currency;
    private $localeResolver;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config,
        UrlInterface $urlBuilder,
        Token $token,
        Currency $currency,
        Locale $localeResolver
    ) {
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
        $this->currency = $currency;
        $this->localeResolver = $localeResolver;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        if (
            !isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }


        $paymentDO = $buildSubject['payment'];
        $order = $paymentDO->getOrder();
        $orderId = $order->getOrderIncrementId();
        $currency = $order->getCurrencyCode();
        $orderTotal = $order->getGrandTotalAmount();
        $convertedOrderTotal = $this->currency->convertAndFormatToEuro($currency, $orderTotal);

        $deadline = $this->config->getValue(ConfigVars::IFTHENPAYGATEWAY_DEADLINE) ?? '';
        $lang = $this->localeResolver->getCurrentLocale() ?? 'en';

        $methodArr = $this->config->getValue(ConfigVars::IFTHENPAYGATEWAY_PAYMENT_METHODS) ?? '';
        $methodArr = $methodArr != '' ? json_decode($methodArr, true) : [];

        $methodsStr = '';
        foreach ($methodArr as $key => $value) {
            if ($value != null && $value['is_active'] === '1') {

                $methodsStr .= str_replace(' ', '', $value['account']) . ';';
            }
        }

        $defaultMethod = $this->config->getValue(ConfigVars::IFTHENPAYGATEWAY_DEFAULT_PAYMENT_METHOD) ?? '';

        $closeButtonLabel = $this->config->getValue(ConfigVars::IFTHENPAYGATEWAY_CLOSE_BUTTON_LABEL) ?? '';

        $key = $this->config->getValue('key');

        $payload = [
            'id' => $orderId,
            "amount" => $convertedOrderTotal,
            "description" => 'Magento order #' . $orderId,
            "lang" => $lang,
            "expiredate" => Time::dateAfterDays($deadline),
            "accounts" => $methodsStr,
            "selected_method" => $defaultMethod,
            "btnCloseLabel" => $closeButtonLabel,
            "btnCloseUrl" => $this->getReturnUrl($orderId),
            "success_url" => $this->getSuccessCallbackUrl($orderId),
            "cancel_url" => $this->getCancelCallbackUrl($orderId),
            "error_url" => $this->getErrorCallbackUrl($orderId),
        ];



        return [
            'url' => ConfigVars::API_URL_IFTHENPAYGATEWAY_SET_REQUEST . $key,
            'payload' => $payload,
        ];
    }


    private function getReturnUrl(string $orderId)
    {
        return $this->urlBuilder->getUrl() . str_replace('[ORDER_ID]', $orderId, ConfigVars::IFTHENPAYGATEWAY_RETURN_URL_STRING);
    }

    private function getSuccessCallbackUrl(string $orderId)
    {
        // same as returnUrl
        return $this->urlBuilder->getUrl() . str_replace('[ORDER_ID]', $orderId, ConfigVars::IFTHENPAYGATEWAY_RETURN_URL_STRING);
    }

    private function getCancelCallbackUrl(string $orderId)
    {
        return $this->urlBuilder->getUrl() . str_replace('[ORDER_ID]', $orderId, ConfigVars::IFTHENPAYGATEWAY_CALLBACK_CANCEL_URL_STRING);
    }

    private function getErrorCallbackUrl(string $orderId)
    {
        return $this->urlBuilder->getUrl() . str_replace('[ORDER_ID]', $orderId, ConfigVars::IFTHENPAYGATEWAY_CALLBACK_ERROR_URL_STRING);
    }
}

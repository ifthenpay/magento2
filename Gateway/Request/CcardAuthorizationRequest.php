<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Gateway\Request;

use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Lib\Utility\Currency;
use Ifthenpay\Payment\Lib\Utility\Locale;
use Ifthenpay\Payment\Lib\Utility\Token;
use Ifthenpay\Payment\Lib\Utility\Version;
use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

class CcardAuthorizationRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;
    private $urlBuilder;
    private $token;
    private $currency;
    private $version;
    private Locale $localeResolver;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config,
        UrlInterface $urlBuilder,
        Token $token,
        Currency $currency,
        Version $version,
        Locale $localeResolver

    ) {
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
        $this->token = $token;
        $this->currency = $currency;
        $this->version = $version;
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
        $lang = $this->localeResolver->getCurrentLocale() ?? 'en';

        $payload = [
            "orderId" => $orderId,
            "amount" => $convertedOrderTotal,
            "successUrl" => $this->getSuccessCallbackUrl($orderId),
            "errorUrl" => $this->getErrorCallbackUrl($orderId),
            "cancelUrl" => $this->getCancelCallbackUrl($orderId),
            "language" => $lang
        ];

        $key = $this->config->getValue('key');

        $url = ConfigVars::API_URL_CCARD_SET_REQUEST . $key . '?ec={ec}&mv={mv}';
        $url = $this->version->replaceVersionVariables($url);


        return [
            'url' => $url,
            'payload' => $payload,
        ];
    }

    private function getSuccessCallbackUrl($orderId)
    {
        $successToken = $this->token->encrypt(ConfigVars::CCARD_SUCCESS_STATUS);
        $str = str_replace('[ORDER_ID]', $orderId, ConfigVars::CCARD_CALLBACK_STRING);
        $str = str_replace('[QN]', $successToken, $str);

        $versionString = $this->version->replaceVersionVariables('&ec={ec}&mv={mv}');

        return $this->urlBuilder->getUrl() . $str . $versionString;
    }

    private function getErrorCallbackUrl($orderId)
    {
        $successToken = $this->token->encrypt(ConfigVars::CCARD_ERROR_STATUS);
        $str = str_replace('[ORDER_ID]', $orderId, ConfigVars::CCARD_CALLBACK_STRING);
        $str = str_replace('[QN]', $successToken, $str);

        return $this->urlBuilder->getUrl() . $str;
    }

    private function getCancelCallbackUrl($orderId)
    {
        $successToken = $this->token->encrypt(ConfigVars::CCARD_CANCEL_STATUS);
        $str = str_replace('[ORDER_ID]', $orderId, ConfigVars::CCARD_CALLBACK_STRING);
        $str = str_replace('[QN]', $successToken, $str);

        return $this->urlBuilder->getUrl() . $str;
    }
}

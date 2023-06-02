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

class CcardAuthorizationRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;
    private $urlBuilder;
    private $token;
    private $currency;
    /**
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config,
        UrlInterface $urlBuilder,
        Token $token,
        Currency $currency
    ) {
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
        $this->token = $token;
        $this->currency = $currency;
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

        $payload = [
            "orderId" => $orderId,
            "amount" => $convertedOrderTotal,
            "successUrl" => $this->getSuccessCallbackUrl($orderId),
            "errorUrl" => $this->getErrorCallbackUrl($orderId),
            "cancelUrl" => $this->getCancelCallbackUrl($orderId),
            "language" => "pt"
        ];

        $key = $this->config->getValue('key');

        return [
            'url' => ConfigVars::API_URL_CCARD_SET_REQUEST . $key,
            'payload' => $payload,
        ];

    }

    private function getSuccessCallbackUrl($orderId)
    {
        $successToken = $this->token->encrypt(ConfigVars::CCARD_SUCCESS_STATUS);
        $str = str_replace('[ORDER_ID]', $orderId, ConfigVars::CCARD_CALLBACK_STRING);
        $str = str_replace('[QN]', $successToken, $str);

        return $this->urlBuilder->getUrl() . $str;
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

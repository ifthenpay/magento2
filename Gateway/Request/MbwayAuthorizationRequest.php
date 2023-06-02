<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ifthenpay\Payment\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Lib\Utility\Currency;


class MbwayAuthorizationRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;
    private $currency;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config,
        Currency $currency,
    ) {
        $this->config = $config;
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
        $key = $this->config->getValue('key');
        $tlm = $paymentDO->getPayment()->getAdditionalInformation('phoneNumber');


        $payload = [
            'MbWayKey' => $key,
            'canal' => '03',
            'referencia' => $orderId,
            'valor' => $convertedOrderTotal,
            'nrtlm' => $tlm,
            'email' => '',
            'descricao' => '',
        ];


        return [
            'url' => ConfigVars::API_URL_MBWAY_SET_REQUEST,
            'payload' => $payload,
        ];

    }

}

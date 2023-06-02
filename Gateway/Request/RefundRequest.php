<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ifthenpay\Payment\Gateway\Request;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;
use Ifthenpay\Payment\Lib\Factory\ServiceFactory;
use Ifthenpay\Payment\Lib\Utility\Currency;
use Ifthenpay\Payment\Config\ConfigVars;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Store\Model\StoreManagerInterface;




class RefundRequest implements BuilderInterface
{
    private $serviceFactory;
    private $configData;
    private $currency;
    private $config;
    private $storeManager;


    public function __construct(
        ServiceFactory $serviceFactory,
        Currency $currency,
        IfthenpayConfig $configData,
        ConfigInterface $config,
        StoreManagerInterface $storeManager
    ) {
        $this->serviceFactory = $serviceFactory;
        $this->currency = $currency;
        $this->configData = $configData;
        $this->config = $config;
        $this->storeManager = $storeManager;
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

        // get transactionID
        $paymentMethod = $paymentDO->getPayment()->getMethod();
        $service = $this->serviceFactory->createService($paymentMethod);
        $storedTransactionId = $service->getPaymentTransactionIdByOrderId($orderId);

        // get ammount
        $currency = $order->getCurrencyCode();
        $amount = $buildSubject['amount'];
        $convertedAmount = $this->currency->convertAndFormatToEuro($currency, $amount);



        $storeId = $order->getStoreId();
        $store = $this->storeManager->getStore($storeId);
        $backofficeKey = $store->getConfig('payment/ifthenpay/backoffice_key');


        return [
            'url' => ConfigVars::API_URL_IFTHENPAY_POST_REFUND,
            'payload' => [
                'backofficeKey' => $backofficeKey,
                'amount' => $convertedAmount,
                'requestId' => $storedTransactionId
            ]
        ];
    }

}

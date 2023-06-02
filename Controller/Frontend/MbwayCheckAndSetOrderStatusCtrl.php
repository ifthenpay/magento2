<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Controller\Frontend;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Gateway\Config\MbwayConfig;
use Ifthenpay\Payment\Lib\Factory\ServiceFactory;
use Ifthenpay\Payment\Lib\HttpClient;
use Ifthenpay\Payment\Logger\Logger;
use Ifthenpay\Payment\Model\ScopeConfigResolver;




class MbwayCheckAndSetOrderStatusCtrl extends Action
{
    private $config;
    private $resultJsonFactory;
    private $logger;
    private $httpClient;
    private $service;
    private $scopeConfigResolver;

    private const SUCCESS = '000';
    private const REFUSED = '020';
    private const PAID = '000';
    private const PENDING = '123';




    public function __construct(
        Context $context,
        MbwayConfig $config,
        Logger $logger,
        JsonFactory $resultJsonFactory,
        HttpClient $httpClient,
        ServiceFactory $serviceFactory,
        ScopeConfigResolver $scopeConfigResolver
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->service = $serviceFactory->createService(ConfigVars::MBWAY_CODE);
        $this->httpClient = $httpClient;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->scopeConfigResolver = $scopeConfigResolver;
    }

    public function execute()
    {
        try {
            $requestData = $this->getRequest()->getParams();

            $store = $this->scopeConfigResolver->storeManager->getStore($requestData['storeId']);

            $mbwayKey = $store->getConfig('payment/ifthenpay_mbway/key');

            $transactionId = $requestData['transaction_id'];
            $url = ConfigVars::API_URL_POST_MBWAY_GET_PAYMENT_STATUS;
            $payload = [
                'MbWayKey' => $mbwayKey,
                'canal' => '03',
                'idspagamento' => $transactionId
            ];

            $this->httpClient->doGet($url, $payload);
            $responseArray = $this->httpClient->getBodyArray();
            $status = $this->httpClient->getStatus();

            if ($status !== 200 || $responseArray['Estado'] !== self::SUCCESS) {
                throw new \Exception('Error: MB WAY request failed.');
            }

            $PaymentStatusCode = $responseArray['EstadoPedidos'][0]['Estado'];

            if ($PaymentStatusCode === self::PAID) {

                return $this->resultJsonFactory->create()->setData(['orderStatus' => 'paid']);
            }
            if ($PaymentStatusCode === self::PENDING) {
                return $this->resultJsonFactory->create()->setData(['orderStatus' => 'pending']);
            }
            if ($PaymentStatusCode === self::REFUSED) {
                return $this->resultJsonFactory->create()->setData(['orderStatus' => 'refused']);
            }

            return $this->resultJsonFactory->create()->setData(['orderStatus' => 'error']);

        } catch (\Throwable $th) {
            return $this->resultJsonFactory->create()->setData(['orderStatus' => $th->getMessage()]);
        }
    }
}

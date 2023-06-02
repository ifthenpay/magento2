<?php
/**
 * Ifthenpay_Payment module dependency
 *
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (http://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Controller\Frontend;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\OrderRepositoryInterface;

use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Gateway\Config\MbwayConfig;
use Ifthenpay\Payment\Lib\Factory\ServiceFactory;
use Ifthenpay\Payment\Lib\HttpClient;
use Ifthenpay\Payment\Logger\Logger;
use Ifthenpay\Payment\Model\ScopeConfigResolver;




class MbwayResendNotificationCtrl extends Action
{

    protected $config;
    private $resultJsonFactory;
    private $logger;
    private $httpClient;
    private $service;
    private $orderFactory;
    private $orderRepository;
    private $scopeConfigResolver;


    private const SUCCESS = '000';


    public function __construct(
        Context $context,
        MbwayConfig $config,
        Logger $logger,
        JsonFactory $resultJsonFactory,
        HttpClient $httpClient,
        ServiceFactory $serviceFactory,
        Order $orderFactory,
        OrderRepositoryInterface $orderRepository,
        ScopeConfigResolver $scopeConfigResolver
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->service = $serviceFactory->createService(ConfigVars::MBWAY_CODE);
        $this->httpClient = $httpClient;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->orderFactory = $orderFactory;
        $this->orderRepository = $orderRepository;
        $this->scopeConfigResolver = $scopeConfigResolver;
    }

    public function execute()
    {
        try {
            $requestData = $this->getRequest()->getParams();

            $store = $this->scopeConfigResolver->storeManager->getStore($requestData['storeId']);
            $mbwayKey = $store->getConfig('payment/ifthenpay_mbway/key');

            $orderId = $requestData['orderId'];
            $phoneNumber = $requestData['phoneNumber'];
            $orderTotal = $this->service->getOrderTotalByOrderId($orderId);


            $url = ConfigVars::API_URL_MBWAY_SET_REQUEST;

            $payload = [
                'MbWayKey' => $mbwayKey,
                'canal' => '03',
                'referencia' => $orderId,
                'valor' => $orderTotal,
                'nrtlm' => $phoneNumber,
                'email' => '',
                'descricao' => '',
            ];

            $this->httpClient->doPost($url, $payload);

            $responseArray = $this->httpClient->getBodyArray();

            $status = $this->httpClient->getStatus();

            if ($status !== 200 || $responseArray['Estado'] !== self::SUCCESS) {
                throw new \Exception('Error: MB WAY request failed.');
            }

            $transactionId = $responseArray['IdPedido'];

            $this->updateOrderTransactionId($orderId, $transactionId);

            $payload = [
                'result' => 'success',
                'message' => __('MB WAY notification sent successfully.'),
                'transactionId' => $transactionId
            ];

            return $this->resultJsonFactory->create()->setData($payload);
        } catch (\Throwable $th) {
            $this->logger->error('frontend/mbway/resend_notification', [
                'error' => $th,
            ]);

            return $this->resultJsonFactory->create()->setData(['error' => __('resendMbwayNotificationError')]);
        }
    }

    private function updateOrderTransactionId($orderId, $transactionId)
    {

        // update ifthenpay_mbway table
        $this->service->setPaymentTransactionIdByOrderId($orderId, $transactionId);
        $this->service->save();

        // upadate magento payment additional info
        $order = $this->orderFactory->loadByIncrementId($orderId);
        $payment = $order->getPayment();
        $payment->setAdditionalInformation('transactionId', $transactionId);
        $this->orderRepository->save($order);
    }
}

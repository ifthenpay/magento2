<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Controller\Frontend;

use Ifthenpay\Payment\Config\ConfigVars;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use Magento\Sales\Model\Order;
use Ifthenpay\Payment\Lib\Services\CofidisService;
use Ifthenpay\Payment\Lib\HttpClient;


use Magento\Sales\Api\OrderRepositoryInterface;
use Ifthenpay\Payment\Lib\Utility\Token;
use Ifthenpay\Payment\Logger\Logger;
use Magento\Framework\Controller\ResultFactory;
use Ifthenpay\Payment\Lib\Services\CreateInvoiceService;
use Ifthenpay\Payment\Gateway\Config\CofidisConfig;
use Magento\Sales\Model\Order\Invoice\NotifierInterface;
use Magento\Store\Model\StoreManagerInterface;



class ReturnCofidisCtrl extends Action
{

    protected $resultPateFactory;
    protected $_orderFactory;
    protected $_moduleDirReader;
    protected $_scopeConfig;
    protected $_order;
    protected $_objPmReq;
    protected $_builderInterface;
    protected $cofidisService;
    protected $_orderRepository;
    protected $token;
    private $logger;
    private $createInvoiceService;
    private $config;
    private $invoiceNotifier;
    private $storeManager;
    private $httpClient;


    /**
     * Ipn constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Reader $reader
     * @param ScopeConfigInterface $scopeConfig
     * @param BuilderInterface $builderInterface
     * @param Order $orderFactory
     */

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Reader $reader,
        ScopeConfigInterface $scopeConfig,
        BuilderInterface $builderInterface,
        Order $orderFactory,
        OrderRepositoryInterface $orderRepository,
        Token $token,
        Logger $logger,
        CreateInvoiceService $createInvoiceService,
        CofidisService $cofidisService,
        CofidisConfig $config,
        NotifierInterface $invoiceNotifier,
        StoreManagerInterface $storeManager,
        HttpClient $httpClient
    ) {
        parent::__construct($context);
        $this->_orderFactory = $orderFactory;
        $this->resultPateFactory = $resultPageFactory;
        $this->_moduleDirReader = $reader;
        $this->_scopeConfig = $scopeConfig;
        $this->_builderInterface = $builderInterface;
        $this->_orderRepository = $orderRepository;
        $this->token = $token;
        $this->logger = $logger;
        $this->createInvoiceService = $createInvoiceService;
        $this->cofidisService = $cofidisService;
        $this->invoiceNotifier = $invoiceNotifier;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->httpClient = $httpClient;
    }



    public function execute()
    {
        $requestData = $this->getRequest()->getParams();
        $storedPaymentData = $this->cofidisService->getPaymentByRequestData($requestData);


        $orderId = $storedPaymentData['order_id'];
        $this->_order = $this->_orderFactory->loadByIncrementId($orderId);

        if ($this->_order->getStatus() == Order::STATE_PROCESSING || $requestData['order_id'] != $storedPaymentData['order_id']) {
            $this->messageManager->addErrorMessage(__('Error: Payment by Cofidis failure.'));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('checkout/cart');
        }

        $success = $requestData['Success'];
        $transactionId = $storedPaymentData['transaction_id'];

        if ($storedPaymentData['status'] === ConfigVars::DB_STATUS_PENDING) {

            $transactionId = $storedPaymentData['transaction_id'];
            $cofidisKey = $this->config->getKey();
            $status = $this->checkCofidisStatus($cofidisKey, $transactionId);


            if ($success === ConfigVars::INIT_STATUS_TRUE && ($status === ConfigVars::COFIDIS_STATUS_INITIATED || $status === ConfigVars::COFIDIS_STATUS_PENDING_INVOICE)) {
                $this->messageManager->addSuccessMessage(__('Payment by Cofidis made with success.'));
            } else if ($success === ConfigVars::INIT_STATUS_FALSE && $status === ConfigVars::COFIDIS_STATUS_CANCELED) {
                $storedPaymentData['status'] = ConfigVars::DB_STATUS_CANCELED;
                $this->cofidisService->setData($storedPaymentData)->save();
                $this->handleCancel($transactionId);
                $this->messageManager->addErrorMessage(__('Payment by Cofidis canceled.'));
                $this->logger->info('Cofidis Callback', [
                    'info' => 'payment canceled by user',
                    'paymentStatus' => $status
                ]);
            } else {
                $storedPaymentData['status'] = ConfigVars::DB_STATUS_CANCELED;
                $this->cofidisService->setData($storedPaymentData)->save();
                $this->handleCancel($transactionId);
                $this->messageManager->addErrorMessage(__('Error, payment by Cofidis canceled.'));
                $this->logger->error('Error Executing Cofidis Callback', [
                    'error' => 'paymentStatus not valid',
                    'paymentStatus' => $status
                ]);
            }

        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('checkout/onepage/success');
    }

    private function handleCancel($transactionId)
    {
        $payment = $this->_order->getPayment();
        $payment->setPreparedMessage(__('Payment canceled'));
        $payment->setParentTransactionId($transactionId);
        $payment->registerVoidNotification();
        $trans = $this->_builderInterface;
        $trans->setPayment($payment)
            ->setOrder($this->_order)
            ->setTransactionId($transactionId)
            ->setFailSafe(true)
            ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_VOID);
        $this->_order->setState(Order::STATE_CANCELED);
        $this->_order->setStatus($this->_order->getConfig()->getStateDefaultStatus(Order::STATE_CANCELED));
        $this->_orderRepository->save($this->_order);
    }

    private function handleCapture($transactionId)
    {
        $this->_order->setState(Order::STATE_PROCESSING);
        $this->_order->setStatus($this->_order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING));

        $amount = $this->_order->getGrandTotal();
        $payment = $this->_order->getPayment();
        $payment->setAdditionalInformation('status', 'success');
        $payment->setTransactionId($transactionId);
        $payment->setParentTransactionId($transactionId);
        $payment->registerCaptureNotification($amount);


        $this->_orderRepository->save($this->_order);


        $orderId = $this->_order->getStoreId();
        $store = $this->storeManager->getStore($orderId);
        $canNotifyInvoice = $store->getConfig('payment/' . ConfigVars::COFIDIS_CODE . '/' . ConfigVars::COFIDIS_SEND_INVOICE_EMAIL);


        if ($canNotifyInvoice) {
            $invoice = $payment->getCreatedInvoice();
            $this->invoiceNotifier->notify($this->_order, $invoice);

            $latestInvoice = $this->_order->getInvoiceCollection()->getLastItem();
            $invoiceId = $latestInvoice->getIncrementId();
            $message = __('Sent invoice email to customer #') . $invoiceId;

            $this->_order->addCommentToStatusHistory($message)->setIsCustomerNotified(true);
            $this->_order->getStatusHistories();

            $this->_orderRepository->save($this->_order);
        }
    }

    private function checkCofidisStatus($cofidisKey, $transactionId)
    {
        $url = ConfigVars::API_URL_COFIDIS_GET_PAYMENT_STATUS;
        $payload = [
            'cofidisKey' => $cofidisKey,
            'requestId' => $transactionId,
        ];

        $this->httpClient->doPost($url, $payload);
        $responseArray = $this->httpClient->getBodyArray();
        $status = $this->httpClient->getStatus();

        if ($status !== 200 || !count($responseArray) > 0) {
            throw new \Exception('Error: Cofidis request failed.');
        }

        if (isset($responseArray[0]) && isset($responseArray[0]['statusCode'])) {
            return $responseArray[0]['statusCode'];
        }
        return 'ERROR';
    }


}

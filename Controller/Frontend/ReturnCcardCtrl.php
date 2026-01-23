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
use Ifthenpay\Payment\Lib\Services\CcardService;


use Magento\Sales\Api\OrderRepositoryInterface;
use Ifthenpay\Payment\Lib\Utility\Token;
use Ifthenpay\Payment\Logger\Logger;
use Magento\Framework\Controller\ResultFactory;
use Ifthenpay\Payment\Lib\Services\CreateInvoiceService;
use Ifthenpay\Payment\Gateway\Config\CcardConfig;
use Magento\Sales\Model\Order\Invoice\NotifierInterface;
use Magento\Store\Model\StoreManagerInterface;



class ReturnCcardCtrl extends Action
{

    protected $resultPateFactory;
    protected $_orderFactory;
    protected $_moduleDirReader;
    protected $_scopeConfig;
    protected $_order;
    protected $_objPmReq;
    protected $_builderInterface;
    protected $ccardService;
    protected $_orderRepository;
    protected $token;
    private $logger;
    private $createInvoiceService;
    private $config;
    private $invoiceNotifier;
    private $storeManager;


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
        CcardService $ccardService,
        CcardConfig $config,
        NotifierInterface $invoiceNotifier,
        StoreManagerInterface $storeManager
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
        $this->ccardService = $ccardService;
        $this->invoiceNotifier = $invoiceNotifier;
        $this->config = $config;
        $this->storeManager = $storeManager;
    }



    public function execute()
    {

        $requestData = $this->getRequest()->getParams();
        $transactionId = $requestData['requestId'];

        $storedPaymentData = $this->ccardService->getByRequestId($transactionId);

        $orderId = $storedPaymentData['order_id'];
        $this->_order = $this->_orderFactory->loadByIncrementId($orderId);

        if ($this->_order->getStatus() == Order::STATE_PROCESSING) {
            return false;
        }


        if ($storedPaymentData['status'] === ConfigVars::DB_STATUS_PENDING) {
            $paymentStatus = $this->token->decrypt($requestData['qn']);

            if ($paymentStatus === ConfigVars::CCARD_SUCCESS_STATUS) {

                $storedPaymentData['status'] = ConfigVars::DB_STATUS_PAID;
                $this->ccardService->setData($storedPaymentData)->save();
                $this->handleCapture($transactionId);
                $this->messageManager->addSuccessMessage(__('Payment by Credit Card made with success.'));
            } else if ($paymentStatus === ConfigVars::CCARD_CANCEL_STATUS) {
                $storedPaymentData['status'] = ConfigVars::DB_STATUS_CANCELED;
                $this->ccardService->setData($storedPaymentData)->save();
                $this->handleCancel($transactionId);
                $this->messageManager->addErrorMessage(__('Payment by Credit Card canceled.'));
                $this->logger->info('Ccard Callback', [
                    'info' => 'payment canceled by user',
                    'paymentStatus' => $paymentStatus
                ]);
            } else if ($paymentStatus === ConfigVars::CCARD_ERROR_STATUS) {
                $storedPaymentData['status'] = ConfigVars::DB_STATUS_CANCELED;
                $this->ccardService->setData($storedPaymentData)->save();
                $this->handleCancel($transactionId);
                $this->messageManager->addErrorMessage(__('Error, payment by Credit Card canceled.'));
                $this->logger->error('Error Executing Ccard Callback', [
                    'error' => 'paymentStatus returned error from provider',
                    'paymentStatus' => $paymentStatus
                ]);
            } else {
                $storedPaymentData['status'] = ConfigVars::DB_STATUS_CANCELED;
                $this->ccardService->setData($storedPaymentData)->save();
                $this->handleCancel($transactionId);
                $this->messageManager->addErrorMessage(__('Error, payment by Credit Card canceled.'));
                $this->logger->error('Error Executing Ccard Callback', [
                    'error' => 'paymentStatus not valid',
                    'paymentStatus' => $paymentStatus
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
        $canNotifyInvoice = $store->getConfig('payment/' . ConfigVars::CCARD_CODE . '/' . ConfigVars::CCARD_SEND_INVOICE_EMAIL);


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
}

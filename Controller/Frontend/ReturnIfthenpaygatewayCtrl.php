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
use Ifthenpay\Payment\Lib\Services\IfthenpaygatewayService;
use Magento\Sales\Api\OrderRepositoryInterface;
use Ifthenpay\Payment\Logger\Logger;
use Magento\Framework\Controller\ResultFactory;


class ReturnIfthenpaygatewayCtrl extends Action
{

    protected $_orderFactory;
    protected $_order;
    protected $_builderInterface;
    protected $ifthenpaygatewayService;
    protected $_orderRepository;
    private $logger;


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
        BuilderInterface $builderInterface,
        Order $orderFactory,
        OrderRepositoryInterface $orderRepository,
        Logger $logger,
        IfthenpaygatewayService $ifthenpaygatewayService,
    ) {
        parent::__construct($context);
        $this->_orderFactory = $orderFactory;
        $this->_builderInterface = $builderInterface;
        $this->_orderRepository = $orderRepository;
        $this->logger = $logger;
        $this->ifthenpaygatewayService = $ifthenpaygatewayService;
    }



    public function execute()
    {
        // the return controler does only set the order as pending, or canceled. payment verification is done through callback
        try {
            $requestData = $this->getRequest()->getParams();
            $storedPaymentData = $this->ifthenpaygatewayService->getPaymentByRequestData($requestData);

            $orderId = $storedPaymentData['order_id'];
            $this->_order = $this->_orderFactory->loadByIncrementId($orderId);

            if ($storedPaymentData['status'] === ConfigVars::DB_STATUS_PENDING) {

                $paymentStatus = $requestData['status'];

                if ($paymentStatus === ConfigVars::IFTHENPAYGATEWAY_SUCCESS_STATUS) {

                    $this->messageManager->addSuccessMessage(__('Payment concluded with success, awaiting verification.'));
                } else if ($paymentStatus === ConfigVars::IFTHENPAYGATEWAY_CANCEL_STATUS) {

                    $storedPaymentData['status'] = ConfigVars::DB_STATUS_CANCELED;
                    $this->ifthenpaygatewayService->setData($storedPaymentData)->save();
                    $this->handleCancel($storedPaymentData['transaction_id']);
                    $this->messageManager->addErrorMessage(__('Payment by Ifthenpay Gateway canceled.'));
                    $this->logger->info('Ifthenpay Gateway Callback', [
                        'info' => 'payment canceled by user',
                        'paymentStatus' => $paymentStatus
                    ]);
                } else if ($paymentStatus === ConfigVars::IFTHENPAYGATEWAY_ERROR_STATUS) {

                    $storedPaymentData['status'] = ConfigVars::DB_STATUS_CANCELED;
                    $this->ifthenpaygatewayService->setData($storedPaymentData)->save();
                    $this->handleCancel($storedPaymentData['transaction_id']);
                    $this->messageManager->addErrorMessage(__('Error, payment by Ifthenpay Gateway failed.'));
                    $this->logger->info('Ifthenpay Gateway Callback', [
                        'info' => 'payment canceled by user',
                        'paymentStatus' => $paymentStatus
                    ]);
                }
            }
        } catch (\Throwable $th) {
            $this->logger->error('Error ifthenpay return ctrl: ', [
                'error' => $th->getMessage(),
            ]);
            $this->messageManager->addErrorMessage(__('An error ocurred while redirecting to store, please contact the store admin.'));
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
}

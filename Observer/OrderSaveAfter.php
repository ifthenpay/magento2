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

namespace Ifthenpay\Payment\Observer;

use Magento\Sales\Model\Order;
use \Magento\Framework\Event\Observer;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderSaveAfter implements ObserverInterface
{

    protected $orderCommentSender;
    protected $gateway;
    private $logger;
    private $orderRepository;

    public function __construct(
        OrderCommentSender $orderCommentSender,
        Gateway $gateway,
        IfthenpayLogger $logger,
        OrderRepositoryInterface $orderRepository
    )
    {
        $this->orderCommentSender = $orderCommentSender;
        $this->gateway = $gateway;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
    }

    public function execute(Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            $paymentMethod = $order->getPayment()->getMethod();
            if ( $this->gateway->checkIfthenpayPaymentMethod($paymentMethod)) {
                if ($order->getState() == 'canceled') {
                    $this->orderCommentSender->send($order, true);
                    $this->logger->debug('Email order canceled sent with success', [
                        'paymentMethod' => $paymentMethod,
                        'order' => $order->getData()
                    ]);
                }
                if ($paymentMethod === Gateway::CCARD && $order->getState() == Order::STATE_PROCESSING) {
                    $order->setState(Order::STATE_NEW)->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_NEW));
                    $this->orderRepository->save($order);
                    $this->logger->debug('Order status with credit card payment changed with success', [
                        'paymentMethod' => $paymentMethod,
                        'order' => $order->getData()
                    ]);
                }
            }

        } catch (\Throwable $th) {
            $this->logger->debug('Error sending email order canceled', [
                'error' => $th,
                'errorMessage' => $th->getMessage(),
                'paymentMethod' => $paymentMethod,
                'order' => $order->getData()

            ]);
            throw $th;
        }
    }
}

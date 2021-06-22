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

use \Magento\Framework\Event\Observer;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;

class OrderSaveAfter implements ObserverInterface
{

    protected $orderCommentSender;
    protected $gateway;
    private $logger;

    public function __construct(
        OrderCommentSender $orderCommentSender,
        Gateway $gateway,
        IfthenpayLogger $logger
    )
    {
        $this->orderCommentSender = $orderCommentSender;
        $this->gateway = $gateway;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            if ($order->getState() == 'canceled' && $this->gateway->checkIfthenpayPaymentMethod($order->getPayment()->getMethod())) {
                $this->orderCommentSender->send($order, true);
                $this->logger->debug('Ifthenpay Order Canceled: Email order canceled sent with success');
            }
        } catch (\Throwable $th) {
            $this->logger->debug('Ifthenpay Order Canceled: Error sending email order canceled - ' . $th->getMessage());
            throw $th;
        }
        
    }
}

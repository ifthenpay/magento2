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

namespace Ifthenpay\Payment\Lib\Payments\Cancel;

use Magento\Sales\Model\Order;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Magento\Sales\Api\OrderRepositoryInterface;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Lib\Factory\Model\RepositoryFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Ifthenpay\Payment\Lib\Factory\Payment\PaymentStatusFactory;
use Ifthenpay\Payment\Lib\Traits\Payments\GatewayDataBuilderBackofficeKey;

abstract class CancelOrder {

    use GatewayDataBuilderBackofficeKey;

    protected $orderCollectionFactory;
    protected $configData;
    protected $paymentStatus;
    protected $paymentRepository;
    protected $gatewayDataBuilder;
    protected $logger;
    protected $pendingOrders;
    protected $payment;
    protected $orderRepository;

    public function __construct(
        CollectionFactory $orderCollectionFactory,
        DataFactory $dataFactory,
        PaymentStatusFactory $paymentStatusFactory,
        RepositoryFactory $repositoryFactory,
        GatewayDataBuilder $gatewayDataBuilder,
        IfthenpayLogger $logger,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->configData = $dataFactory->setType($this->paymentMethod)->build()->getConfig();
        $this->paymentStatus = $paymentStatusFactory->setType($this->paymentMethod)->build();
        $this->paymentRepository = $repositoryFactory->setType($this->paymentMethod)->build();
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
    }

    protected function setPendingOrders(): void
    {
        $colection = $this->orderCollectionFactory->create()->addFieldToFilter('status',  'pending');
        $colection->getSelect()->join(["sop" => "sales_order_payment"],
                'main_table.entity_id = sop.parent_id',
                array('method')
        )->where('sop.method = ?', $this->paymentMethod);
        $this->pendingOrders = $colection;
    }

    protected function changeIfthenpayPaymentStatus(string $orderId): void
    {
        $paymentModel = $this->paymentRepository->getByOrderId($orderId);
        $paymentModel->setStatus('cancel');
        $this->paymentRepository->save($paymentModel);
    }

    protected function checkTimeChangeStatus(Order $order, string $days = null, string $paymentDeadline = null, string $dateFormat = null)
    {
        date_default_timezone_set('Europe/Lisbon');
        $today = new \DateTime(date("Y-m-d G:i"));
        $time = new \DateTime($order->getCreatedAt());
        if (!is_null($days) && is_null($paymentDeadline) && is_null($dateFormat)) {
            $time->add(new \DateInterval('P' . $days . 'D'));
            $time->settime(0,0);
            $today->settime(0,0);
        } else if (!is_null($paymentDeadline) && !is_null($dateFormat) && is_null($days)) {
            $time = \DateTime::createFromFormat($dateFormat, $paymentDeadline);
            $time->settime(0,0);
            $today->settime(0,0);
        } else {
            $time->add(new \DateInterval('PT' . 30 . 'M'));
        }
        if ($time < $today) {
            $order->setState(Order::STATE_CANCELED)
            ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_CANCELED));
            $this->orderRepository->save($order);
            $this->changeIfthenpayPaymentStatus($order->getIncrementId());
        }
    }

    protected function logCancelOrder(string $paymentMethod, string $idPedido, array $orderData): void
    {
        $this->logger->debug('Cancel ' . $paymentMethod .  ' order executed with success', [
            'gatewayDataBuilder' => $this->gatewayDataBuilder,
            'idPedido' => $idPedido,
            'order' => $orderData
        ]);
    }

    protected function logErrorCancelOrder(string $paymentMethod, \Throwable $th): void
    {
        $this->logger->debug('Error cancel ' . $paymentMethod . ' order', ['error' => $th, 'message' => $th->getMessage()]);
    }

    abstract function cancelOrder(): void;
}

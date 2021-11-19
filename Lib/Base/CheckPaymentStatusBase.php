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

declare(strict_types=1);

namespace Ifthenpay\Payment\Lib\Base;

use Magento\Sales\Model\Order;
use Ifthenpay\Payment\Lib\Request\WebService;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Magento\Sales\Api\OrderRepositoryInterface;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Model\Service\CreateInvoiceService;
use Ifthenpay\Payment\Lib\Factory\Model\RepositoryFactory;
use Ifthenpay\Payment\Lib\Traits\Logs\LogGatewayBuilderData;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Ifthenpay\Payment\Lib\Contracts\Payments\PaymentStatusInterface;
use Ifthenpay\Payment\Lib\Traits\Payments\GatewayDataBuilderBackofficeKey;

abstract class CheckPaymentStatusBase
{
    use LogGatewayBuilderData, GatewayDataBuilderBackofficeKey;

    protected $paymentMethod;
    protected $gatewayDataBuilder;
    protected $paymentStatus;
    protected $webService;
    protected $paymentRepository;
    protected $logger;
    protected $orderCollectionFactory;
    protected $pendingOrders;
    protected $orderRepository;
    protected $createInvoiceService;
    protected $configData;

    public function __construct(
        GatewayDataBuilder $gatewayDataBuilder,
        PaymentStatusInterface $paymentStatus,
        WebService $webService,
        DataFactory $dataFactory,
        RepositoryFactory $repositoryFactory,
        CollectionFactory $orderCollectionFactory,
        OrderRepositoryInterface $orderRepository,
        CreateInvoiceService $createInvoiceService,
        IfthenpayLogger $logger
    ) {
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->paymentStatus = $paymentStatus;
        $this->webService = $webService;
        $this->configData = $dataFactory->setType($this->paymentMethod)->build()->getConfig();
        $this->paymentRepository = $repositoryFactory->setType($this->paymentMethod)->build();
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->createInvoiceService = $createInvoiceService;
        $this->logger = $logger;
    }

    protected function updatePaymentStatus(string $id): void
    {
        $paymentModel = $this->paymentRepository->getById($id);
        $paymentModel->setStatus('paid');
        $this->paymentRepository->save($paymentModel);
        $this->logger->debug('payment status updated with success in database', [
                'paymentMethod' => $this->paymentMethod,
                'status' => 'paid',
                'id' => $id
            ]
        );
    }

    protected function updateOrderStatus(Order $order, string $invoiceCapture): void
    {
        $order->setState(Order::STATE_PROCESSING)->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING));
        $this->orderRepository->save($order);
        $this->createInvoiceService->createInvoice($order, $invoiceCapture);
    }

    protected function logGetPendingOrders(array $data): void
    {
        $this->logger->debug('pending orders retrieved with success from database', [
                'paymentMethod' => $this->paymentMethod,
                'pendingOrders' => $data
            ]
        );
    }

    protected function logChangePaymentStatus(array $paymentData): void
    {
        $this->logger->debug('payment status changed with success', [
                'paymentMethod' => $this->paymentMethod,
                'gatewayDataBuilder' => $this->gatewayDataBuilder,
                'payment' => $paymentData
            ]
        );
    }

    protected function getPendingOrders(): void
    {
        $colection = $this->orderCollectionFactory->create()->addFieldToFilter('status',  'pending');
        $colection->getSelect()->join(["sop" => "sales_order_payment"],
                'main_table.entity_id = sop.parent_id',
                array('method')
        )->where('sop.method = ?', $this->paymentMethod);
        $this->pendingOrders = $colection;
        $this->logGetPendingOrders($this->pendingOrders->getData());
    }

    abstract protected function setGatewayDataBuilder(): void;
    abstract public function changePaymentStatus(): void;
}

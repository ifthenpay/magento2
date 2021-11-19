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

namespace Ifthenpay\Payment\Lib\Payments\Data;

use Magento\Sales\Model\Order\Invoice;
use Ifthenpay\Payment\Lib\Request\WebService;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Lib\Utility\ConvertEuros;
use Magento\Sales\Api\OrderRepositoryInterface;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Base\CheckPaymentStatusBase;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Model\Service\CreateInvoiceService;
use Ifthenpay\Payment\Lib\Factory\Model\RepositoryFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Ifthenpay\Payment\Lib\Contracts\Payments\PaymentStatusInterface;
use Ifthenpay\Payment\Lib\Payments\Gateway;

class CCardChangePaymentStatus extends CheckPaymentStatusBase
{
	private $convertEuros;
    protected $paymentMethod = Gateway::CCARD;

    public function __construct(
        GatewayDataBuilder $gatewayDataBuilder,
        PaymentStatusInterface $paymentStatus,
        WebService $webService,
        DataFactory $dataFactory,
        RepositoryFactory $repositoryFactory,
        CollectionFactory $orderCollectionFactory,
        OrderRepositoryInterface $orderRepository,
        CreateInvoiceService $createInvoiceService,
        ConvertEuros $convertEuros,
        IfthenpayLogger $logger
    )
	{
        parent::__construct(
            $gatewayDataBuilder,
            $paymentStatus,
            $webService,
            $dataFactory,
            $repositoryFactory,
            $orderCollectionFactory,
            $orderRepository,
            $createInvoiceService,
            $logger
        );
        $this->convertEuros = $convertEuros;
	}

    protected function setGatewayDataBuilder(): void
    {
        $this->setGatewayDataBuilderBackofficeKey();
        $this->gatewayDataBuilder->setCCardKey($this->configData['ccardKey']);
        $this->logGatewayBuilderData();
    }

    public function changePaymentStatus(): void
    {
        $this->setGatewayDataBuilder();
        $this->getPendingOrders();
        if ($this->pendingOrders->getSize()) {
            foreach ($this->pendingOrders as $order) {
                $ccardPayment = $this->paymentRepository->getByOrderId((string) $order->getIncrementId());
                $this->gatewayDataBuilder->setReferencia($order->getIncrementId());
                $this->gatewayDataBuilder->setTotalToPay($this->convertEuros->execute(
                        $order->getOrderCurrencyCode(),
                        $order->getGrandTotal()
                    )
                );
                if ($this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                    $this->updatePaymentStatus((string) $ccardPayment['id']);
                    $this->updateOrderStatus($order, Invoice::CAPTURE_ONLINE);
                    $this->logChangePaymentStatus($ccardPayment);
                }
            }
        }
    }
}

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

use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Lib\Payments\Payment;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Lib\Utility\ConvertEuros;
use Magento\Sales\Api\OrderRepositoryInterface;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Lib\Payments\Cancel\CancelOrder;
use Ifthenpay\Payment\Lib\Factory\Model\RepositoryFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Ifthenpay\Payment\Lib\Factory\Payment\PaymentStatusFactory;

class CancelCCardOrder extends CancelOrder {

    protected $paymentMethod = Gateway::CCARD;
    private $convertEuros;

    public function __construct(
        CollectionFactory $orderCollectionFactory,
        DataFactory $dataFactory,
        PaymentStatusFactory $paymentStatusFactory,
        RepositoryFactory $repositoryFactory,
        GatewayDataBuilder $gatewayDataBuilder,
        IfthenpayLogger $logger,
        ConvertEuros $convertEuros,
        OrderRepositoryInterface $orderRepository
    )
	{
        parent::__construct(
            $orderCollectionFactory,
            $dataFactory,
            $paymentStatusFactory,
            $repositoryFactory,
            $gatewayDataBuilder,
            $logger,
            $orderRepository
        );
        $this->convertEuros = $convertEuros;
	}



    public function cancelOrder(): void
    {
        try {
            if ($this->configData['cancelOrder']) {
                $this->setPendingOrders();
                if ($this->pendingOrders->getSize()) {
                    foreach ($this->pendingOrders as $order) {
                        $payment = $order->getPayment();
                        $idPedido = $payment->getAdditionalInformation('idPedido');
                        if ($idPedido) {
                            $this->setGatewayDataBuilderBackofficeKey();
                            $this->gatewayDataBuilder->setCCardKey($this->configData['ccardKey']);
                            $this->gatewayDataBuilder->setReferencia((string) $payment->getAdditionalInformation('idPedido'));
                            $this->gatewayDataBuilder->setTotalToPay($this->convertEuros->execute(
                                    $order->getOrderCurrencyCode(),
                                    $order->getGrandTotal()
                                )
                            );
                            if (!$this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                                $this->checkTimeChangeStatus($order);
                            }
                        }
                        $this->logCancelOrder(Gateway::CCARD, $idPedido, $order->getData());
                    };
                }

            }
        } catch (\Throwable $th) {
            $this->logErrorCancelOrder(Gateway::CCARD, $th);
            throw $th;
        }
    }
}

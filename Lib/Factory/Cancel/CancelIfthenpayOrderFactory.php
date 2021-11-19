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

namespace Ifthenpay\Payment\Lib\Factory\Cancel;

use Ifthenpay\Payment\Lib\Factory\Factory;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Lib\Utility\ConvertEuros;
use Magento\Sales\Api\OrderRepositoryInterface;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Lib\Payments\Cancel\CancelOrder;
use Ifthenpay\Payment\Lib\Factory\Model\RepositoryFactory;
use Ifthenpay\Payment\Lib\Payments\Cancel\CancelCCardOrder;
use Ifthenpay\Payment\Lib\Payments\Cancel\CancelMbwayOrder;
use Ifthenpay\Payment\Lib\Payments\Cancel\CancelPayshopOrder;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Ifthenpay\Payment\Lib\Factory\Payment\PaymentStatusFactory;

class CancelIfthenpayOrderFactory extends Factory
{
    private $orderCollectionFactory;
    private $dataFactory;
    private $paymentStatusFactory;
    private $repositoryFactory;
    private $gatewayDataBuilder;
    private $logger;
    private $convertEuros;
    private $orderRepository;

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
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->dataFactory = $dataFactory;
        $this->paymentStatusFactory = $paymentStatusFactory;
        $this->repositoryFactory = $repositoryFactory;
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->logger = $logger;
        $this->convertEuros = $convertEuros;
        $this->orderRepository = $orderRepository;
	}

    public function build(
        ): CancelOrder {
            switch ($this->type) {
                case Gateway::MBWAY:
                    return new CancelMbwayOrder(
                        $this->orderCollectionFactory,
                        $this->dataFactory,
                        $this->paymentStatusFactory,
                        $this->repositoryFactory,
                        $this->gatewayDataBuilder,
                        $this->logger,
                        $this->orderRepository
                    );
                case Gateway::PAYSHOP:
                    return new CancelPayshopOrder(
                        $this->orderCollectionFactory,
                        $this->dataFactory,
                        $this->paymentStatusFactory,
                        $this->repositoryFactory,
                        $this->gatewayDataBuilder,
                        $this->logger,
                        $this->orderRepository
                    );
                case Gateway::CCARD:
                    return new CancelCCardOrder(
                        $this->orderCollectionFactory,
                        $this->dataFactory,
                        $this->paymentStatusFactory,
                        $this->repositoryFactory,
                        $this->gatewayDataBuilder,
                        $this->logger,
                        $this->convertEuros,
                        $this->orderRepository
                    );
                default:
                    throw new \Exception('Unknown Cancel Order Class');
            }
        }

}

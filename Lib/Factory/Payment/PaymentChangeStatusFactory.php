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

namespace Ifthenpay\Payment\Lib\Factory\Payment;

use Ifthenpay\Payment\Lib\Factory\Factory;
use Ifthenpay\Payment\Lib\Payments\Gateway;
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
use Ifthenpay\Payment\Lib\Factory\Payment\PaymentStatusFactory;
use Ifthenpay\Payment\Lib\Payments\Data\CCardChangePaymentStatus;
use Ifthenpay\Payment\Lib\Payments\Data\MbwayChangePaymentStatus;
use Ifthenpay\Payment\Lib\Payments\Data\PayshopChangePaymentStatus;
use Ifthenpay\Payment\Lib\Payments\Data\MultibancoChangePaymentStatus;


class PaymentChangeStatusFactory extends Factory
{
    private $gatewayDataBuilder;
    private $logger;
    private $paymentStatusFactory;
    private $webService;
    private $dataFactory;
    private $repositoryFactory;
    private $orderCollectionFactory;
    private $orderRepository;
    private $createInvoiceService;
    private $convertEuros;


    public function __construct(
        GatewayDataBuilder $gatewayDataBuilder,
        PaymentStatusFactory $paymentStatusFactory,
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
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->paymentStatusFactory = $paymentStatusFactory;
        $this->webService = $webService;
        $this->dataFactory = $dataFactory;
        $this->repositoryFactory = $repositoryFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->createInvoiceService = $createInvoiceService;
        $this->convertEuros = $convertEuros;
        $this->logger = $logger;
    }

    public function build(): CheckPaymentStatusBase {
        switch ($this->type) {
            case Gateway::MULTIBANCO:
                return new MultibancoChangePaymentStatus(
                    $this->gatewayDataBuilder,
                    $this->paymentStatusFactory->setType(Gateway::MULTIBANCO)->build(),
                    $this->webService,
                    $this->dataFactory,
                    $this->repositoryFactory,
                    $this->orderCollectionFactory,
                    $this->orderRepository,
                    $this->createInvoiceService,
                    $this->logger
                );
            case Gateway::MBWAY:
                return new MbwayChangePaymentStatus(
                    $this->gatewayDataBuilder,
                    $this->paymentStatusFactory->setType(Gateway::MBWAY)->build(),
                    $this->webService,
                    $this->dataFactory,
                    $this->repositoryFactory,
                    $this->orderCollectionFactory,
                    $this->orderRepository,
                    $this->createInvoiceService,
                    $this->logger
                );
            case Gateway::PAYSHOP:
                return new PayshopChangePaymentStatus(
                    $this->gatewayDataBuilder,
                    $this->paymentStatusFactory->setType(Gateway::PAYSHOP)->build(),
                    $this->webService,
                    $this->dataFactory,
                    $this->repositoryFactory,
                    $this->orderCollectionFactory,
                    $this->orderRepository,
                    $this->createInvoiceService,
                    $this->logger
                );
            case Gateway::CCARD:
                return new CCardChangePaymentStatus(
                    $this->gatewayDataBuilder,
                    $this->paymentStatusFactory->setType(Gateway::CCARD)->build(),
                    $this->webService,
                    $this->dataFactory,
                    $this->repositoryFactory,
                    $this->orderCollectionFactory,
                    $this->orderRepository,
                    $this->createInvoiceService,
                    $this->convertEuros,
                    $this->logger
                );
            default:
                throw new \Exception('Unknown Payment Change Status Class');
        }
    }
}

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

use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Lib\Builders\DataBuilder;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Factory\Model\ModelFactory;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Lib\Factory\Model\RepositoryFactory;
use Ifthenpay\Payment\Lib\Traits\Logs\LogGatewayBuilderData;

abstract class PaymentBase
{
    use LogGatewayBuilderData;

    protected $gatewayDataBuilder;
    protected $paymentDefaultData;
    protected $paymentGatewayResultData;
    protected $ifthenpayGateway;
    protected $paymentDataFromDb;
    protected $paymentTable;
    protected $paymentMethod;
    protected $paymentMethodAlias;
    protected $redirectUrl;
    protected $dataConfig;
    protected $paymentModel;
    protected $paymentRepository;

    public function __construct(
        DataFactory $dataFactory,
        ModelFactory $modelFactory,
        DataBuilder $paymentDefaultData,
        GatewayDataBuilder $gatewayDataBuilder,
        Gateway $ifthenpayGateway,
        RepositoryFactory $repositoryFactory,
        IfthenpayLogger $logger
    ) {
        $this->dataConfig = $dataFactory->setType($this->paymentMethod)->build()->getConfig();
        $this->paymentModel = $modelFactory->setType($this->paymentMethod)->build();
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->paymentDefaultData = $paymentDefaultData->getData();
        $this->ifthenpayGateway = $ifthenpayGateway;
        $this->paymentRepository = $repositoryFactory->setType($this->paymentMethod)->build();
        $this->logger = $logger;
    }

    public function getRedirectUrl(): array
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl(bool $redirect = false, string $url = '')
    {
        $this->redirectUrl = [
            'redirect' => $redirect,
            'url' => $url
        ];
        return $this;
    }

    abstract protected function setGatewayBuilderData(): void;
    abstract protected function saveToDatabase(): void;

    public function getPaymentDataFromDb()
    {
        return $this->paymentDataFromDb;
    }

    public function getPaymentGatewayResultData()
    {
        return $this->paymentGatewayResultData;
    }
}

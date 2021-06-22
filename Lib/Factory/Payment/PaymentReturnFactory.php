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

use Magento\Framework\UrlInterface;
use Magento\Framework\App\Request\Http;
use Ifthenpay\Payment\Lib\Utility\Token;
use Ifthenpay\Payment\Lib\Utility\Status;
use Ifthenpay\Payment\Lib\Factory\Factory;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Factory\Model\ModelFactory;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Lib\Factory\Model\RepositoryFactory;
use Ifthenpay\Payment\Lib\Payments\Data\CCardPaymentReturn;
use Ifthenpay\Payment\Lib\Payments\Data\MbwayPaymentReturn;
use Ifthenpay\Payment\Lib\Payments\Data\PayshopPaymentReturn;
use Ifthenpay\Payment\Lib\Payments\Data\MultibancoPaymentReturn;
use Ifthenpay\Payment\Lib\Contracts\Payments\PaymentReturnInterface;
use Ifthenpay\Payment\Lib\Contracts\Factory\PaymentReturnFactoryInterface;


class PaymentReturnFactory extends Factory implements PaymentReturnFactoryInterface
{
    protected $paymentDefaultData;
    protected $twigDefaultData;
    protected $gatewayBuilder;
    protected $ifthenpayGateway;
    protected $configData;
    protected $utility;
    protected $ifthenpayController;
    protected $dataFactory;
    protected $modelFactory;
    protected $urlBuilder;
    protected $repositoryFactory;

    public function __construct(
        DataFactory $dataFactory,
        ModelFactory $modelFactory,
        GatewayDataBuilder $gatewayBuilder,
        Gateway $ifthenpayGateway,
        UrlInterface $urlBuilder,
        Token $token,
        Status $status,
        RepositoryFactory $repositoryFactory,
        Http $request
    )
	{
        $this->dataFactory = $dataFactory;
        $this->modelFactory = $modelFactory;
        $this->gatewayBuilder = $gatewayBuilder;
        $this->ifthenpayGateway = $ifthenpayGateway;
        $this->urlBuilder = $urlBuilder;
        $this->token = $token;
        $this->status = $status;
        $this->repositoryFactory = $repositoryFactory;
        $this->request = $request;
    }

    public function setPaymentDefaultData($paymentDefaultData)
    {
        $this->paymentDefaultData = $paymentDefaultData;

        return $this;
    }

    public function build(): PaymentReturnInterface {
        switch ($this->type) {
            case 'multibanco':
                return new MultibancoPaymentReturn(
                    $this->dataFactory,
                    $this->modelFactory,
                    $this->paymentDefaultData,
                    $this->gatewayBuilder,
                    $this->ifthenpayGateway,
                    $this->repositoryFactory
                );
            case 'mbway':
                return new MbwayPaymentReturn(
                    $this->dataFactory,
                    $this->modelFactory,
                    $this->paymentDefaultData,
                    $this->gatewayBuilder,
                    $this->ifthenpayGateway,
                    $this->request,
                    $this->repositoryFactory
                );
            case 'payshop':
                return new PayshopPaymentReturn(
                    $this->dataFactory,
                    $this->modelFactory,
                    $this->paymentDefaultData,
                    $this->gatewayBuilder,
                    $this->ifthenpayGateway,
                    $this->repositoryFactory
                );
            case 'ccard':
                return new CCardPaymentReturn(
                    $this->dataFactory,
                    $this->modelFactory,
                    $this->paymentDefaultData,
                    $this->gatewayBuilder,
                    $this->ifthenpayGateway,
                    $this->repositoryFactory,
                    $this->urlBuilder,
                    $this->token,
                    $this->status
                );
            default:
                throw new \Exception('Unknown Payment Return Class');
        }
    }
}

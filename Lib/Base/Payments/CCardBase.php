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

namespace Ifthenpay\Payment\Lib\Base\Payments;


use Magento\Framework\UrlInterface;
use Ifthenpay\Payment\Lib\Utility\Token;
use Ifthenpay\Payment\Lib\Utility\Status;
use Ifthenpay\Payment\Lib\Base\PaymentBase;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Lib\Builders\DataBuilder;
use Ifthenpay\Payment\Lib\Utility\ConvertEuros;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Factory\Model\ModelFactory;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Lib\Factory\Model\RepositoryFactory;
use Ifthenpay\Payment\Logger\IfthenpayLogger;

class CCardBase extends PaymentBase
{
    protected $paymentMethod = Gateway::CCARD;
    private $token;
    private $urlBuilder;
    protected $convertEuros;

    public function __construct(
        DataFactory $dataFactory,
        ModelFactory $modelFactory,
        DataBuilder $paymentDefaultData,
        GatewayDataBuilder $gatewayDataBuilder,
        Gateway $ifthenpayGateway,
        RepositoryFactory $repositoryFactory,
        IfthenpayLogger $logger,
        ConvertEuros $convertEuros,
        UrlInterface $urlBuilder,
        Token $token,
        Status $status
    ) {
        parent::__construct($dataFactory, $modelFactory, $paymentDefaultData, $gatewayDataBuilder, $ifthenpayGateway, $repositoryFactory, $logger);
        $this->token = $token;
        $this->status = $status;
        $this->urlBuilder = $urlBuilder;
        $this->convertEuros = $convertEuros;
    }



    private function getUrlCallback(): string
    {
        return $this->urlBuilder->getUrl('ifthenpay/Frontend/Callback');
    }

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayDataBuilder->setCCardKey($this->dataConfig['ccardKey']);
        $this->gatewayDataBuilder->setSuccessUrl($this->getUrlCallback() . '?type=online&payment=ccard&orderId=' . $this->paymentDefaultData->order->getOrderIncrementId() . '&qn=' .
            $this->token->encrypt($this->status->getStatusSucess())
        );
        $this->gatewayDataBuilder->setErrorUrl($this->getUrlCallback() . '?type=online&payment=ccard&orderId=' . $this->paymentDefaultData->order->getOrderIncrementId() . '&qn=' .
            $this->token->encrypt($this->status->getStatusError())
        );
        $this->gatewayDataBuilder->setCancelUrl($this->getUrlCallback() . '?type=online&payment=ccard&orderId=' . $this->paymentDefaultData->order->getOrderIncrementId() . '&qn=' .
            $this->token->encrypt($this->status->getStatusCancel())
        );
        $this->logGatewayBuilderData();
    }

    protected function saveToDatabase(): void
    {
        $this->paymentModel->setData([
            'requestId' => $this->paymentGatewayResultData['idPedido'],
            'paymentUrl' => $this->paymentGatewayResultData['paymentUrl'],
            'order_id' => !is_null($this->paymentDefaultData->order->getOrderIncrementId()) ? $this->paymentDefaultData->order->getOrderIncrementId() : $this->paymentDefaultData->order->getIncrementId(),
            'status' => 'pending'
        ]);
        $this->paymentRepository->save($this->paymentModel);
        $this->logger->debug('ccard payment saved wtih success in database', [
                'requestId' => $this->paymentGatewayResultData['idPedido'],
                'paymentUrl' => $this->paymentGatewayResultData['paymentUrl'],
                'order_id' => !is_null($this->paymentDefaultData->order->getOrderIncrementId()) ? $this->paymentDefaultData->order->getOrderIncrementId() : $this->paymentDefaultData->order->getIncrementId(),
                'status' => 'pending'
            ]
        );
    }
}

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
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Factory\Model\ModelFactory;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Lib\Factory\Model\RepositoryFactory;

class CCardBase extends PaymentBase
{
    protected $paymentMethod = 'ccard';
    private $token;
    private $urlBuilder;

    public function __construct(
        DataFactory $dataFactory,
        ModelFactory $modelFactory,
        DataBuilder $paymentDefaultData,
        GatewayDataBuilder $gatewayBuilder,
        Gateway $ifthenpayGateway,
        RepositoryFactory $repositoryFactory,
        UrlInterface $urlBuilder = null,
        Token $token = null,
        Status $status = null
    ) {
        parent::__construct($dataFactory, $modelFactory, $paymentDefaultData, $gatewayBuilder, $ifthenpayGateway, $repositoryFactory);
        $this->token = $token;
        $this->status = $status;
        $this->urlBuilder = $urlBuilder;
    }



    private function getUrlCallback(): string
    {
        return $this->urlBuilder->getUrl('ifthenpay/Frontend/Callback');
    }

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setCCardKey($this->dataConfig['ccardKey']);
        $this->gatewayBuilder->setSuccessUrl($this->getUrlCallback() . '?type=online&payment=ccard&orderId=' . $this->paymentDefaultData->order->getOrderIncrementId() . '&qn=' .
            $this->token->encrypt($this->status->getStatusSucess())
        );
        $this->gatewayBuilder->setErrorUrl($this->getUrlCallback() . '?type=online&payment=ccard&orderId=' . $this->paymentDefaultData->order->getOrderIncrementId() . '&qn=' .
            $this->token->encrypt($this->status->getStatusError())
        );
        $this->gatewayBuilder->setCancelUrl($this->getUrlCallback() . '?type=online&payment=ccard&orderId=' . $this->paymentDefaultData->order->getOrderIncrementId() . '&qn=' .
            $this->token->encrypt($this->status->getStatusCancel())
        );
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
    }
}

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


use Magento\Framework\App\Request\Http;
use Ifthenpay\Payment\Lib\Base\PaymentBase;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Lib\Builders\DataBuilder;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Factory\Model\ModelFactory;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Lib\Factory\Model\RepositoryFactory;



class MbwayBase extends PaymentBase
{
    protected $paymentMethod = 'mbway';
    protected $paymentMethodAlias = 'MB WAY';

    public function __construct(
        DataFactory $dataFactory,
        ModelFactory $modelFactory,
        DataBuilder $paymentDefaultData,
        GatewayDataBuilder $gatewayBuilder,
        Gateway $ifthenpayGateway,
        Http $request,
        RepositoryFactory $repositoryFactory
    ) {
        parent::__construct($dataFactory, $modelFactory, $paymentDefaultData, $gatewayBuilder, $ifthenpayGateway, $repositoryFactory);
        $this->request = $request;
    }

    protected function setGatewayBuilderData(): void
    {
        $mbwayPhoneNumber = $this->paymentDefaultData->order->getPayment()->getAdditionalInformation('mbwayPhoneNumber');
        $this->gatewayBuilder->setMbwayKey($this->dataConfig['mbwayKey']);
        $this->gatewayBuilder->setTelemovel(
            !is_null($mbwayPhoneNumber) ? $mbwayPhoneNumber : $this->request->getParams()['mbwayPhoneNumber']
        );
    }

    protected function saveToDatabase(): void
    {
        $this->paymentModel->setData([
            'id_transacao' => $this->paymentGatewayResultData->idPedido,
            'telemovel' => $this->paymentGatewayResultData->telemovel,
            'order_id' => !is_null($this->paymentDefaultData->order->getOrderIncrementId()) ? $this->paymentDefaultData->order->getOrderIncrementId() : $this->paymentDefaultData->order->getIncrementId(),
            'status' => 'pending'
        ]);
        $this->paymentRepository->save($this->paymentModel);
    }
}

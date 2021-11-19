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
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Lib\Builders\DataBuilder;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Factory\Model\ModelFactory;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Lib\Factory\Model\RepositoryFactory;

class MbwayBase extends PaymentBase
{
    protected $paymentMethod = Gateway::MBWAY;
    protected $paymentMethodAlias = 'MB WAY';



    public function __construct(
        DataFactory $dataFactory,
        ModelFactory $modelFactory,
        DataBuilder $paymentDefaultData,
        GatewayDataBuilder $gatewayDataBuilder,
        Gateway $ifthenpayGateway,
        Http $request,
        RepositoryFactory $repositoryFactory,
        IfthenpayLogger $logger
    ) {
        parent::__construct($dataFactory, $modelFactory, $paymentDefaultData, $gatewayDataBuilder, $ifthenpayGateway, $repositoryFactory, $logger);
        $this->request = $request;
    }

    protected function setGatewayBuilderData(): void
    {
        $mbwayPhoneNumber = $this->paymentDefaultData->order->getPayment()->getAdditionalInformation('mbwayPhoneNumber');
        $this->gatewayDataBuilder->setMbwayKey($this->dataConfig['mbwayKey']);
        $this->gatewayDataBuilder->setTelemovel(
            !is_null($mbwayPhoneNumber) ? $mbwayPhoneNumber : $this->request->getParams()['mbwayPhoneNumber']
        );
        $this->logGatewayBuilderData();
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
        $this->logger->debug('mbway payment saved in database with success', [
                'id_transacao' => $this->paymentGatewayResultData->idPedido,
                'telemovel' => $this->paymentGatewayResultData->telemovel,
                'order_id' => !is_null($this->paymentDefaultData->order->getOrderIncrementId()) ? $this->paymentDefaultData->order->getOrderIncrementId() : $this->paymentDefaultData->order->getIncrementId(),
                'status' => 'pending'
            ]
        );
    }
}

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

use Ifthenpay\Payment\Lib\Base\PaymentBase;
use Ifthenpay\Payment\Lib\Payments\Gateway;

class PayshopBase extends PaymentBase
{
    protected $paymentMethod = Gateway::PAYSHOP;
    protected $paymentMethodAlias = 'Payshop';


    protected function setGatewayBuilderData(): void
    {
        $this->gatewayDataBuilder->setPayshopKey($this->dataConfig['payshopKey']);
        $this->gatewayDataBuilder->setValidade(!is_null($this->dataConfig['validade']) ? $this->dataConfig['validade'] : '');
        $this->logGatewayBuilderData();
    }

    protected function saveToDatabase(): void
    {
        $this->paymentModel->setData([
            'id_transacao' => $this->paymentGatewayResultData->idPedido,
            'referencia' => $this->paymentGatewayResultData->referencia,
            'validade' => $this->paymentGatewayResultData->validade,
            'order_id' => !is_null($this->paymentDefaultData->order->getOrderIncrementId()) ? $this->paymentDefaultData->order->getOrderIncrementId() : $this->paymentDefaultData->order->getIncrementId(),
            'status' => 'pending'
        ]);
        $this->paymentRepository->save($this->paymentModel);
        $this->logger->debug('payshop payment saved in database with success', [
                'id_transacao' => $this->paymentGatewayResultData->idPedido,
                'referencia' => $this->paymentGatewayResultData->referencia,
                'validade' => $this->paymentGatewayResultData->validade,
                'order_id' => !is_null($this->paymentDefaultData->order->getOrderIncrementId()) ? $this->paymentDefaultData->order->getOrderIncrementId() : $this->paymentDefaultData->order->getIncrementId(),
                'status' => 'pending'
            ]
        );
    }
}

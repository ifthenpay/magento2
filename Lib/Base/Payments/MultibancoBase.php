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

class MultibancoBase extends PaymentBase
{
    protected $paymentMethod = Gateway::MULTIBANCO;
    protected $paymentMethodAlias = 'Multibanco';

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayDataBuilder->setEntidade($this->dataConfig['entidade']);
        $this->gatewayDataBuilder->setSubEntidade($this->dataConfig['subEntidade']);
        $this->gatewayDataBuilder->setValidade($this->dataConfig['validade'] !== 'Choose Deadline' ? $this->dataConfig['validade'] : '999999');
        $this->logGatewayBuilderData();
    }

    protected function saveToDatabase(): void
    {
        $this->paymentModel->setData([
            'entidade' => $this->paymentGatewayResultData->entidade,
            'referencia' => $this->paymentGatewayResultData->referencia,
            'order_id' => !is_null($this->paymentDefaultData->order->getOrderIncrementId()) ? $this->paymentDefaultData->order->getOrderIncrementId() : $this->paymentDefaultData->order->getIncrementId(),
            'requestId' => isset($this->paymentGatewayResultData->idPedido) ? $this->paymentGatewayResultData->idPedido : null,
            'validade' => isset($this->paymentGatewayResultData->validade) ? $this->paymentGatewayResultData->validade : null,
            'status' => 'pending'
        ]);
        $this->paymentRepository->save($this->paymentModel);
        $this->logger->debug('multibanco payment saved in database with success', [
                'entidade' => $this->paymentGatewayResultData->entidade,
                'referencia' => $this->paymentGatewayResultData->referencia,
                'order_id' => !is_null($this->paymentDefaultData->order->getOrderIncrementId()) ? $this->paymentDefaultData->order->getOrderIncrementId() : $this->paymentDefaultData->order->getIncrementId(),
                'status' => 'pending'
            ]
        );
    }
}

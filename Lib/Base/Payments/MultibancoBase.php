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

class MultibancoBase extends PaymentBase
{
    protected $paymentMethod = 'multibanco';
    protected $paymentMethodAlias = 'Multibanco';

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setEntidade($this->dataConfig['entidade']);
        $this->gatewayBuilder->setSubEntidade($this->dataConfig['subEntidade']);
    }

    protected function saveToDatabase(): void
    {
        $this->paymentModel->setData([
            'entidade' => $this->paymentGatewayResultData->entidade,
            'referencia' => $this->paymentGatewayResultData->referencia,
            'order_id' => !is_null($this->paymentDefaultData->order->getOrderIncrementId()) ? $this->paymentDefaultData->order->getOrderIncrementId() : $this->paymentDefaultData->order->getIncrementId(),
            'status' => 'pending'
        ]);
        $this->paymentRepository->save($this->paymentModel);
    }
}

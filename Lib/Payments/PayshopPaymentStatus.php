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

namespace Ifthenpay\Payment\Lib\Payments;

use Ifthenpay\Payment\Lib\Payments\PaymentStatus;
use Ifthenpay\Payment\Lib\Contracts\Payments\PaymentStatusInterface;

class PayshopPaymentStatus extends PaymentStatus implements PaymentStatusInterface
{
    private $payshopPedido;

    private function checkEstado(): bool
    {
        if (isset($this->ccardPedido['CodigoErro']) && $this->ccardPedido['CodigoErro'] === '0') {
            return true;
        }
        return false;
    }

    private function getPayshopEstado(): void
    {
        $this->payshopPedido = $this->webService->postRequest(
            'https://www.ifthenpay.com/IfmbWS/WsIfmb.asmx/GetPaymentsJson',
                [
                    'Chavebackoffice' => $this->data->getData()->backofficeKey,
                    'Entidade' => strtoupper(Gateway::PAYSHOP),
                    'Subentidade' => $this->data->getData()->payshopKey,
                    'dtHrInicio' => '',
                    'dtHrFim' => '',
                    'Referencia' => $this->data->getData()->referencia,
                    'Valor' => $this->data->getData()->totalToPay,
                    'Sandbox' => 0
                ]
        )->getXmlConvertedResponseToArray();
    }

    public function getPaymentStatus(): bool
    {
        $this->getPayshopEstado();
        $this->logger->info('payshop payment status request executed with success', [
                'data' => $this->data,
                'payshopPedido' => $this->payshopPedido,
                'className' => get_class($this)
            ]
        );
        return $this->checkEstado();
    }
}

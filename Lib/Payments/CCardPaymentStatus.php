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

use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Lib\Payments\PaymentStatus;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Lib\Contracts\Payments\PaymentStatusInterface;


class CCardPaymentStatus extends PaymentStatus implements PaymentStatusInterface
{
    private $ccardPedido;

    private function checkEstado(): bool
    {
        if (isset($this->ccardPedido['CodigoErro']) && $this->ccardPedido['CodigoErro'] === '0') {
            return true;
        }
        return false;
    }

    private function getCCardEstado(): void
    {
        $this->ccardPedido = $this->webService->postRequest(
            'https://www.ifthenpay.com/IfmbWS/WsIfmb.asmx/GetPaymentsJson',
                [
                    'Chavebackoffice' => $this->data->getData()->backofficeKey,
                    'Entidade' => strtoupper(Gateway::CCARD),
                    'Subentidade' => $this->data->getData()->ccardKey,
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
        $this->getCCardEstado();
        return $this->checkEstado();
    }

    /**
     * Set the value of data
     *
     * @return  self
     */
    public function setData(GatewayDataBuilder $data): PaymentStatus
    {
        $this->data = $data;

        return $this;
    }
}

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

class MultibancoPaymentStatus extends PaymentStatus implements PaymentStatusInterface
{
    private $multibancoPedido;

    private function checkEstado(): bool
    {
        if (isset($this->multibancoPedido['CodigoErro']) && $this->multibancoPedido['CodigoErro'] === '0') {
            return true;
        }
        return false;
    }

    private function getMultibancoEstado(): void
    {
        $this->multibancoPedido = $this->webService->getRequest(
            'https://www.ifthenpay.com/IfmbWS/WsIfmb.asmx/GetPaymentsJson',
                [
                    'Chavebackoffice' => /*$this->data->getData()->backofficeKey*/'9802-8539-7462-2952',
                    'Entidade' => $this->data->getData()->entidade,
                    'Subentidade' => $this->data->getData()->subEntidade,
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
        $this->getMultibancoEstado();
        $this->logger->info('multibanco payment status request executed with success', [
                'data' => $this->data,
                'multibancoPedido' => $this->multibancoPedido,
                'className' => get_class($this)
            ]
        );
        return $this->checkEstado();
    }
}

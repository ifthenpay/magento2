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

use Ifthenpay\Payment\Lib\Request\Webservice;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Lib\Contracts\Payments\PaymentStatusInterface;


class MbWayPaymentStatus implements PaymentStatusInterface
{
    private $data;
    private $mbwayPedido;
    private $webservice;

    public function __construct(Webservice $webservice)
    {
        $this->webservice = $webservice;
    }

    private function checkEstado(): bool
    {
        if ($this->mbwayPedido['EstadoPedidos'][0]['Estado'] === '000') {
            return true;
        }
        return false;
    }

    private function getMbwayEstado(): void
    {
        $this->mbwayPedido = $this->webservice->postRequest(
            'https://mbway.ifthenpay.com/IfthenPayMBW.asmx/EstadoPedidosJSON',
            [
                    'MbWayKey' => $this->data->getData()->mbwayKey,
                    'canal' => '03',
                    'idspagamento' => $this->data->getData()->idPedido
                ]
        )->getResponseJson();
    }

    public function getPaymentStatus(): bool
    {
        $this->getMbwayEstado();
        return $this->checkEstado();
    }

    /**
     * Set the value of data
     *
     * @return  self
     */
    public function setData(GatewayDataBuilder $data)
    {
        $this->data = $data;

        return $this;
    }
}
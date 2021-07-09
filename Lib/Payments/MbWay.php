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

use Ifthenpay\Payment\Lib\Payments\Payment;
use Ifthenpay\Payment\Lib\Builders\DataBuilder;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Lib\Contracts\Payments\PaymentMethodInterface;
use Ifthenpay\Payment\Lib\Request\WebService;


class MbWay extends Payment implements PaymentMethodInterface
{
    private $mbwayKey;
    private $telemovel;
    private $mbwayPedido;

    public function __construct(GatewayDataBuilder $data, string $orderId, string $valor, WebService $webservice = null)
    {
        parent::__construct($orderId, $valor, $data, $webservice);
        $this->mbwayKey = $data->getData()->mbwayKey;
        $this->telemovel = $data->getData()->telemovel;
    }

    public function checkValue(): void
    {
        if ($this->valor < 0.10) {
            throw new \Exception('Mbway does not allow payments under 0.10€');
        }
    }

    private function checkEstado(): void
    {
        if ($this->mbwayPedido['Estado'] !== '000') {
            throw new \Exception($this->mbwayPedido['MsgDescricao']);
        }
    }

    private function setReferencia(): void
    {
        $this->mbwayPedido = $this->webservice->postRequest(
            'https://mbway.ifthenpay.com/IfthenPayMBW.asmx/SetPedidoJSON',
            [
                    'MbWayKey' => $this->mbwayKey,
                    'canal' => '03',
                    'referencia' => $this->orderId,
                    'valor' => $this->valor,
                    'nrtlm' => $this->telemovel,
                    'email' => '',
                    'descricao' => '',
                ]
        )->getResponseJson();
    }

    private function getReferencia(): DataBuilder
    {
        $this->setReferencia();
        $this->checkEstado();
        $this->dataBuilder->setIdPedido($this->mbwayPedido['IdPedido']);
        $this->dataBuilder->setTelemovel($this->telemovel);
        $this->dataBuilder->setTotalToPay((string)$this->valor);
        return $this->dataBuilder;
    }

    public function buy(): DataBuilder
    {
        $this->checkValue();
        return $this->getReferencia();
    }
}

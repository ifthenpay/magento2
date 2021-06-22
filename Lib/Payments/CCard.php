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
use Ifthenpay\Payment\Lib\Builders\DataBuilder;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Lib\Contracts\Payments\PaymentMethodInterface;

class CCard extends Payment implements PaymentMethodInterface
{
    private $ccardKey;
    private $ccardPedido;
    private $successUrl;
    private $errorUrl;
    private $cancelUrl;

    public function __construct(GatewayDataBuilder $data, string $orderId, string $valor, Webservice $webservice = null)
    {
        parent::__construct($orderId, $valor, $data, $webservice);
        $this->ccardKey = $data->getData()->ccardKey;
        $this->successUrl = $data->getData()->successUrl;
        $this->errorUrl = $data->getData()->errorUrl;
        $this->cancelUrl = $data->getData()->cancelUrl;
    }

    public function checkValue(): void
    {
        //void
    }

    private function checkEstado(): void
    {
        if ($this->ccardPedido['Status'] !== '0') {
            throw new \Exception($this->ccardPedido['Message']);
        }
    }

    private function setReferencia(): void
    {
        $this->ccardPedido = $this->webservice->postRequest(
            'https://ifthenpay.com/api/creditcard/sandbox/init/' . $this->ccardKey,
            [
                "orderId" => $this->orderId,
                "amount" => $this->valor,
                "successUrl" => $this->successUrl,
                "errorUrl" => $this->errorUrl,
                "cancelUrl" => $this->cancelUrl,
                "language" => "pt"
            ],
            true
        )->getResponseJson();
    }

    private function getReferencia(): DataBuilder
    {
        $this->setReferencia();
        $this->checkEstado();

        $this->dataBuilder->setPaymentMessage($this->ccardPedido['Message']);
        $this->dataBuilder->setPaymentUrl($this->ccardPedido['PaymentUrl']);
        $this->dataBuilder->setIdPedido($this->ccardPedido['RequestId']);
        $this->dataBuilder->setPaymentStatus($this->ccardPedido['Status']);
        $this->dataBuilder->setTotalToPay((string)$this->valor);

        return $this->dataBuilder;
    }

    public function buy(): DataBuilder
    {
        return $this->getReferencia();
    }
}
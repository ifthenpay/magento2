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
use Ifthenpay\Payment\Lib\Request\WebService;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
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

    public function __construct(GatewayDataBuilder $data, string $orderId, string $valor, WebService $webService, IfthenpayLogger $ifthenpayLogger)
    {
        parent::__construct($orderId, $valor, $data, $webService, $ifthenpayLogger);
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
        try {
            $this->ccardPedido = $this->webService->postRequest(
                'https://ifthenpay.com/api/creditcard/init/' . $this->ccardKey,
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
        } catch (\Throwable $th) {
            $this->logWebserviceRequestError(Gateway::CCARD, $th, [
                "orderId" => $this->orderId,
                "amount" => $this->valor,
                "successUrl" => $this->successUrl,
                "errorUrl" => $this->errorUrl,
                "cancelUrl" => $this->cancelUrl,
                "language" => "pt"
            ]);
            throw $th;
        }
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

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

namespace Ifthenpay\Payment\Lib\Builders;

use Ifthenpay\Payment\Lib\Contracts\Builders\DataBuilderInterface;

class DataBuilder implements DataBuilderInterface
{
    protected $data;

    public function __construct()
    {
        $this->data = new \stdClass;
    }

    public function setOrder($value): DataBuilderInterface
    {
        $this->data->order = $value;
        return $this;
    }

    public function setTotalToPay(string $value): DataBuilderInterface
    {
        $this->data->totalToPay = $value;
        return $this;
    }

    public function setPaymentMethod(string $value): DataBuilderInterface
    {
        $this->data->paymentMethod = $value;
        return $this;
    }

    public function setEntidade(string $value): DataBuilderInterface
    {
        $this->data->entidade = $value;
        return $this;
    }

    public function setReferencia(string $value): DataBuilderInterface
    {
        $this->data->referencia = $value;
        return $this;
    }

    public function setTelemovel(string $value = null): DataBuilderInterface
    {
        $this->data->telemovel = $value;
        return $this;
    }

    public function setValidade(string $value): DataBuilderInterface
    {
        $this->data->validade = $value;
        return $this;
    }

    public function setIdPedido(string $value = null): DataBuilderInterface
    {
        $this->data->idPedido = $value;
        return $this;
    }

    public function setBackofficeKey(string $value): DataBuilderInterface
    {
        $this->data->backofficeKey = $value;
        return $this;
    }

    public function setSuccessUrl(string $value): DataBuilderInterface
    {
        $this->data->successUrl = $value;
        return $this;
    }

    public function setErrorUrl(string $value): DataBuilderInterface
    {
        $this->data->errorUrl = $value;
        return $this;
    }

    public function setCancelUrl(string $value): DataBuilderInterface
    {
        $this->data->cancelUrl = $value;
        return $this;
    }

    public function setPaymentMessage(string $value): DataBuilderInterface
    {
        $this->data->message = $value;
        return $this;
    }

    public function setPaymentUrl(string $value): DataBuilderInterface
    {
        $this->data->paymentUrl = $value;
        return $this;
    }

    public function setPaymentStatus(string $value): DataBuilderInterface
    {
        $this->data->status = $value;
        return $this;
    }


    public function toArray(): array
    {
        return json_decode(json_encode($this->data), true);
    }

    public function getData(): \stdClass
    {
        return $this->data;
    }
}

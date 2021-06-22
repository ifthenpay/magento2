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

namespace Ifthenpay\Payment\Lib\Contracts\Builders;

interface DataBuilderInterface
{
    public function setTotalToPay(string $value): DataBuilderInterface;
    public function setPaymentMethod(string $value): DataBuilderInterface;
    public function setEntidade(string $value): DataBuilderInterface;
    public function setReferencia(string $value): DataBuilderInterface;
    public function setTelemovel(string $value = null): DataBuilderInterface;
    public function setValidade(string $value): DataBuilderInterface;
    public function setIdPedido(string $value = null): DataBuilderInterface;
    public function setBackofficeKey(string $value): DataBuilderInterface;
    public function setSuccessUrl(string $value): DataBuilderInterface;
    public function setErrorUrl(string $value): DataBuilderInterface;
    public function setCancelUrl(string $value): DataBuilderInterface;
    public function setPaymentMessage(string $value): DataBuilderInterface;
    public function setPaymentUrl(string $value): DataBuilderInterface;
    public function setPaymentStatus(string $value): DataBuilderInterface;
    public function setOrder($value): DataBuilderInterface;
}
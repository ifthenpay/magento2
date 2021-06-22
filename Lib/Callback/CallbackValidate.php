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

namespace Ifthenpay\Payment\Lib\Callback;


class CallbackValidate
{
    private $httpRequest;
    private $order;
    private $configurationChaveAntiPhishing;
    private $paymentDataFromDb;

    private function validateOrder(): void
    {
        if (!$this->order) {
            throw new \Exception('Ordem não encontrada.');
        }
    }

    private function validateOrderValue(): void
    {
        $orderTotal = floatval($this->order['base_grand_total']);
        $requestValor = floatval($this->httpRequest['valor']);
        if (round($orderTotal, 2) !== round($requestValor, 2)) {
            throw new \Exception('Valor não corresponde ao valor da encomenda.');
        }
    }

    private function validateOrderStatus(): void
    {
        if ($this->paymentDataFromDb['status'] === 'paid') {
                throw new \Exception('Encomenda já foi paga.');
        }
    }

    private function validateChaveAntiPhishing()
    {
        if (!$this->httpRequest['chave']) {
            throw new \Exception('Chave Anti-Phishing não foi enviada.');
        }

        if ($this->httpRequest['chave'] !== $this->configurationChaveAntiPhishing) {
            throw new \Exception('Chave Anti-Phishing não é válida.');
        }
    }

    public function validate(): bool
    {
        $this->validateChaveAntiPhishing();
        $this->validateOrder();
        $this->validateOrderValue();
        $this->validateOrderStatus();
        return true;
    }

    /**
     * Set the value of httpRequest
     *
     * @return  self
     */
    public function setHttpRequest(array $httpRequest)
    {
        $this->httpRequest = $httpRequest;

        return $this;
    }

    /**
     * Set the value of order
     *
     * @return  self
     */
    public function setOrder(array $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Set the value of configurationChaveAntiPhishing
     *
     * @return  self
     */
    public function setConfigurationChaveAntiPhishing(string $configurationChaveAntiPhishing)
    {
        $this->configurationChaveAntiPhishing = $configurationChaveAntiPhishing;

        return $this;
    }

    /**
     * Set the value of paymentDataFromDb
     *
     * @return  self
     */
    public function setPaymentDataFromDb(array $paymentDataFromDb)
    {
        $this->paymentDataFromDb = $paymentDataFromDb;

        return $this;
    }
}

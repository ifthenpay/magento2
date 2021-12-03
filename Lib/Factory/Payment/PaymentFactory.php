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

namespace Ifthenpay\Payment\Lib\Factory\Payment;


use Ifthenpay\Payment\Lib\Payments\CCard;
use Ifthenpay\Payment\Lib\Payments\MbWay;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Lib\Payments\Payshop;
use Ifthenpay\Payment\Lib\Request\WebService;
use Ifthenpay\Payment\Lib\Payments\Multibanco;
use Ifthenpay\Payment\Lib\Builders\DataBuilder;
use Ifthenpay\Payment\Lib\Factory\Factory as BaseFactory;
use Ifthenpay\Payment\Lib\Contracts\Payments\PaymentMethodInterface;
use Ifthenpay\Payment\Logger\IfthenpayLogger;

class PaymentFactory extends BaseFactory
{
    private $data;
    private $orderId;
    private $valor;
    private $dataBuilder;
    private $webService;
    private $logger;

    public function __construct(DataBuilder $dataBuilder, WebService $webService, IfthenpayLogger $ifthenpayLogger)
	{
        $this->dataBuilder = $dataBuilder;
        $this->webService = $webService;
        $this->logger = $ifthenpayLogger;
    }

    public function build(): PaymentMethodInterface
    {
        switch ($this->type) {
            case Gateway::MULTIBANCO:
                return new Multibanco($this->data, $this->orderId, $this->valor, $this->webService, $this->logger, $this->dataBuilder);
            case Gateway::MBWAY:
                return new MbWay($this->data, $this->orderId, $this->valor, $this->webService, $this->logger, $this->dataBuilder);
            case Gateway::PAYSHOP:
                return new Payshop($this->data, $this->orderId, $this->valor, $this->webService, $this->logger, $this->dataBuilder);
            case Gateway::CCARD:
                return new CCard($this->data, $this->orderId, $this->valor, $this->webService, $this->logger, $this->dataBuilder);
            default:
                throw new \Exception("Unknown Payment Class");
        }
    }

    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}

<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Ifthenpay\Payment\Lib\Factory;


use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Gateway\Config\PixConfig;
use Ifthenpay\Payment\Model\MultibancoFactory;
use Ifthenpay\Payment\Model\PayshopFactory;
use Ifthenpay\Payment\Model\MbwayFactory;
use Ifthenpay\Payment\Model\CcardFactory;
use Ifthenpay\Payment\Model\CofidisFactory;
use Ifthenpay\Payment\Model\PixFactory;
use Ifthenpay\Payment\Model\IfthenpaygatewayFactory;

class ModelFactory
{
    private $payshopFactory;
    private $multibancoFactory;
    private $mbwayFactory;
    private $ccardFactory;
    private $cofidisFactory;
    private $pixFactory;
    private $ifthenpaygatewayFactory;

    public function __construct(
        MultibancoFactory $multibancoFactory,
        PayshopFactory $payshopFactory,
        MbwayFactory $mbwayFactory,
        CcardFactory $ccardFactory,
        CofidisFactory $cofidisFactory,
        PixFactory $pixFactory,
        IfthenpaygatewayFactory $ifthenpaygatewayFactory
    ) {
        $this->multibancoFactory = $multibancoFactory;
        $this->payshopFactory = $payshopFactory;
        $this->mbwayFactory = $mbwayFactory;
        $this->ccardFactory = $ccardFactory;
        $this->cofidisFactory = $cofidisFactory;
        $this->pixFactory = $pixFactory;
        $this->ifthenpaygatewayFactory = $ifthenpaygatewayFactory;
    }

    public function createModel(string $paymentMethod)
    {
        switch ($paymentMethod) {
            case ConfigVars::MULTIBANCO:
                return $this->multibancoFactory->create();
            case ConfigVars::PAYSHOP:
                return $this->payshopFactory->create();
            case ConfigVars::MBWAY:
                return $this->mbwayFactory->create();
            case ConfigVars::CCARD:
                return $this->ccardFactory->create();
            case ConfigVars::COFIDIS:
                return $this->cofidisFactory->create();
            case ConfigVars::PIX:
                return $this->pixFactory->create();
            case ConfigVars::IFTHENPAYGATEWAY:
                return $this->ifthenpaygatewayFactory->create();
            default:
                throw new \Exception("Unknown Model Class");
        }
    }
}

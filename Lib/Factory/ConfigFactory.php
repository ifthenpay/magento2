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
use Ifthenpay\Payment\Gateway\Config\MultibancoConfigFactory;
use Ifthenpay\Payment\Gateway\Config\PayshopConfigFactory;
use Ifthenpay\Payment\Gateway\Config\MbwayConfigFactory;
use Ifthenpay\Payment\Gateway\Config\CcardConfigFactory;
use Ifthenpay\Payment\Gateway\Config\CofidisConfigFactory;
use Ifthenpay\Payment\Gateway\Config\PixConfigFactory;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfigFactory;
use Ifthenpay\Payment\Gateway\Config\IfthenpaygatewayConfigFactory;

class ConfigFactory
{
    private $ifthenpayFactory;
    private $multibancoFactory;
    private $payshopFactory;
    private $mbwayFactory;
    private $ccardFactory;
    private $cofidisFactory;
    private $pixFactory;
    private $ifthenpaygatewayFactory;

    public function __construct(
        IfthenpayConfigFactory $ifthenpayFactory,
        MultibancoConfigFactory $multibancoFactory,
        PayshopConfigFactory $payshopFactory,
        MbwayConfigFactory $mbwayFactory,
        CcardConfigFactory $ccardFactory,
        CofidisConfigFactory $cofidisFactory,
        PixConfigFactory $pixFactory,
        IfthenpaygatewayConfigFactory $ifthenpaygatewayFactory
    ) {
        $this->ifthenpayFactory = $ifthenpayFactory;
        $this->multibancoFactory = $multibancoFactory;
        $this->payshopFactory = $payshopFactory;
        $this->mbwayFactory = $mbwayFactory;
        $this->ccardFactory = $ccardFactory;
        $this->cofidisFactory = $cofidisFactory;
        $this->pixFactory = $pixFactory;
        $this->ifthenpaygatewayFactory = $ifthenpaygatewayFactory;
    }

    public function createConfig(string $paymentMethod)
    {
        switch ($paymentMethod) {
            case ConfigVars::IFTHENPAY_CODE:
                return $this->ifthenpayFactory->create();
            case ConfigVars::MULTIBANCO_CODE:
                return $this->multibancoFactory->create();
            case ConfigVars::PAYSHOP_CODE:
                return $this->payshopFactory->create();
            case ConfigVars::MBWAY_CODE:
                return $this->mbwayFactory->create();
            case ConfigVars::CCARD_CODE:
                return $this->ccardFactory->create();
            case ConfigVars::COFIDIS_CODE:
                return $this->cofidisFactory->create();
            case ConfigVars::PIX_CODE:
                return $this->pixFactory->create();
            case ConfigVars::IFTHENPAYGATEWAY_CODE:
                return $this->ifthenpaygatewayFactory->create();
            default:
                throw new \Exception("Unknown Config Class");
        }
    }
}

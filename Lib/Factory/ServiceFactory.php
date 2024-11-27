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
use Ifthenpay\Payment\Lib\Services\MultibancoService;
use Ifthenpay\Payment\Lib\Services\PayshopService;
use Ifthenpay\Payment\Lib\Services\MbwayService;
use Ifthenpay\Payment\Lib\Services\CcardService;
use Ifthenpay\Payment\Lib\Services\CofidisService;
use Ifthenpay\Payment\Lib\Services\PixService;
use Ifthenpay\Payment\Lib\Services\IfthenpaygatewayService;


class ServiceFactory
{
    private $multibancoService;
    private $payshopService;
    private $mbwayService;
    private $ccardService;
    private $cofidisService;
    private $pixService;
    private $ifthenpaygatewayService;

    public function __construct(
        MultibancoService $multibancoService,
        PayshopService $payshopService,
        MbwayService $mbwayService,
        CcardService $ccardService,
        CofidisService $cofidisService,
        PixService $pixService,
        IfthenpaygatewayService $ifthenpaygatewayService
    ) {
        $this->multibancoService = $multibancoService;
        $this->payshopService = $payshopService;
        $this->mbwayService = $mbwayService;
        $this->ccardService = $ccardService;
        $this->cofidisService = $cofidisService;
        $this->pixService = $pixService;
        $this->ifthenpaygatewayService = $ifthenpaygatewayService;
    }

    public function createService(string $paymentMethod)
    {
        switch ($paymentMethod) {
            case ConfigVars::MULTIBANCO_CODE:
                return $this->multibancoService;
            case ConfigVars::PAYSHOP_CODE:
                return $this->payshopService;
            case ConfigVars::MBWAY_CODE:
                return $this->mbwayService;
            case ConfigVars::CCARD_CODE:
                return $this->ccardService;
            case ConfigVars::COFIDIS_CODE:
                return $this->cofidisService;
            case ConfigVars::PIX_CODE:
                return $this->pixService;
            case ConfigVars::IFTHENPAYGATEWAY_CODE:
                return $this->ifthenpaygatewayService;

            default:
                throw new \Exception("Unknown Service Class");
        }
    }
}

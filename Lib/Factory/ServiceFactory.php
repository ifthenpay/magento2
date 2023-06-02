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


class ServiceFactory
{
    private $multibancoService;
    private $payshopService;
    private $mbwayService;
    private $ccardService;

    public function __construct(
        \Ifthenpay\Payment\Lib\Services\MultibancoService $multibancoService,
        \Ifthenpay\Payment\Lib\Services\PayshopService $payshopService,
        \Ifthenpay\Payment\Lib\Services\MbwayService $mbwayService,
        \Ifthenpay\Payment\Lib\Services\CcardService $ccardService
    ) {
        $this->multibancoService = $multibancoService;
        $this->payshopService = $payshopService;
        $this->mbwayService = $mbwayService;
        $this->ccardService = $ccardService;
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
            default:
                throw new \Exception("Unknown Service Class");
        }
    }
}

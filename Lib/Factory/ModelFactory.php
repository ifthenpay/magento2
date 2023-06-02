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
use Ifthenpay\Payment\Model\MultibancoFactory;
use Ifthenpay\Payment\Model\PayshopFactory;
use Ifthenpay\Payment\Model\MbwayFactory;
use Ifthenpay\Payment\Model\CcardFactory;

class ModelFactory
{
    private $payshopFactory;
    private $multibancoFactory;
    private $mbwayFactory;
    private $ccardFactory;

    public function __construct(
        MultibancoFactory $multibancoFactory,
        PayshopFactory $payshopFactory,
        MbwayFactory $mbwayFactory,
        CcardFactory $ccardFactory
    ) {
        $this->multibancoFactory = $multibancoFactory;
        $this->payshopFactory = $payshopFactory;
        $this->mbwayFactory = $mbwayFactory;
        $this->ccardFactory = $ccardFactory;
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
            default:
                throw new \Exception("Unknown Model Class");

        }
    }
}

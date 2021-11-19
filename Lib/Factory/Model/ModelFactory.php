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

namespace Ifthenpay\Payment\Lib\Factory\Model;

use Ifthenpay\Payment\Model\CCardFactory;
use Ifthenpay\Payment\Model\MbwayFactory;
use Ifthenpay\Payment\Lib\Factory\Factory;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Model\PayshopFactory;
use Ifthenpay\Payment\Model\MultibancoFactory;

class ModelFactory extends Factory
{
    private $multibancoFactory;
    private $mbwayFactory;
    private $payshopFactory;
    private $ccardFactory;

    public function __construct(
        MultibancoFactory $multibancoFactory,
        MbwayFactory $mbwayFactory,
        PayshopFactory $payshopFactory,
        CCardFactory $ccardFactory
    )
	{
        $this->multibancoFactory = $multibancoFactory;
        $this->mbwayFactory = $mbwayFactory;
        $this->payshopFactory = $payshopFactory;
        $this->ccardFactory = $ccardFactory;
    }

    public function build()
    {
        switch ($this->type) {
            case Gateway::MULTIBANCO:
                return $this->multibancoFactory->create();
            case Gateway::MBWAY:
                return $this->mbwayFactory->create();
            case Gateway::PAYSHOP:
                return $this->payshopFactory->create();
            case Gateway::CCARD:
                return $this->ccardFactory->create();
            default:
                throw new \Exception("Unknown Model Class");

        }
    }
}

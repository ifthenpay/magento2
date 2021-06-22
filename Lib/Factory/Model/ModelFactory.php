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

use Ifthenpay\Payment\Lib\Factory\Factory;
use Ifthenpay\Payment\Model\MultibancoFactory;
use Ifthenpay\Payment\Model\MbwayFactory;
use Ifthenpay\Payment\Model\PayshopFactory;
use Ifthenpay\Payment\Model\CCardFactory;

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
            case 'multibanco':
                return $this->multibancoFactory->create();
            case 'mbway':
                return $this->mbwayFactory->create();
            case 'payshop':
                return $this->payshopFactory->create();
            case 'ccard':
                return $this->ccardFactory->create();
            default:
                throw new \Exception("Unknown Model Class");

        }
    }
}
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
use Ifthenpay\Payment\Api\CCardRepositoryInterface;
use Ifthenpay\Payment\Api\MbwayRepositoryInterface;
use Ifthenpay\Payment\Api\PayshopRepositoryInterface;
use Ifthenpay\Payment\Api\MultibancoRepositoryInterface;

class RepositoryFactory extends Factory
{
    private $multibancoRepository;
    private $mbwayRepository;
    private $payshopRepository;
    private $ccardRepository;

    public function __construct(
        MultibancoRepositoryInterface $multibancoRepository,
        MbwayRepositoryInterface $mbwayRepository,
        PayshopRepositoryInterface $payshopRepository,
        CCardRepositoryInterface $ccardRepository
    )
	{
        $this->multibancoRepository = $multibancoRepository;
        $this->mbwayRepository = $mbwayRepository;
        $this->payshopRepository = $payshopRepository;
        $this->ccardRepository = $ccardRepository;
    }

    public function build()
    {
        switch ($this->type) {
            case 'multibanco':
                return $this->multibancoRepository;
            case 'mbway':
                return $this->mbwayRepository;
            case 'payshop':
                return $this->payshopRepository;
            case 'ccard':
                return $this->ccardRepository;
            default:
                throw new \Exception("Unknown Repository Class");
        }
    }
}

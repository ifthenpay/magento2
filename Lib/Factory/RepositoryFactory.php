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
use Ifthenpay\Payment\Model\Repository\MultibancoRepository;
use Ifthenpay\Payment\Model\Repository\PayshopRepository;
use Ifthenpay\Payment\Model\Repository\MbwayRepository;
use Ifthenpay\Payment\Model\Repository\CcardRepository;
use Ifthenpay\Payment\Model\Repository\CofidisRepository;

class RepositoryFactory
{
	private $multibancoRepository;
	private $payshopRepository;
	private $mbwayRepository;
	private $ccardRepository;
	private $cofidisRepository;

	public function __construct(
		MultibancoRepository $multibancoRepository,
		PayshopRepository $payshopRepository,
		MbwayRepository $mbwayRepository,
		CcardRepository $ccardRepository,
		CofidisRepository $cofidisRepository
	) {
		$this->multibancoRepository = $multibancoRepository;
		$this->payshopRepository = $payshopRepository;
		$this->mbwayRepository = $mbwayRepository;
		$this->ccardRepository = $ccardRepository;
		$this->cofidisRepository = $cofidisRepository;
	}

	public function createRepository(string $paymentMethod)
	{
		switch ($paymentMethod) {
			case ConfigVars::MULTIBANCO:
				return $this->multibancoRepository;
			case ConfigVars::PAYSHOP:
				return $this->payshopRepository;
			case ConfigVars::MBWAY:
				return $this->mbwayRepository;
			case ConfigVars::CCARD:
				return $this->ccardRepository;
			case ConfigVars::COFIDIS:
				return $this->cofidisRepository;
			default:
				throw new \Exception("Unknown Repository Class");
		}
	}
}

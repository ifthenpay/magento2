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

namespace Ifthenpay\Payment\Lib\Utility;

use Magento\Directory\Helper\Data;

class ConvertEuros {

    private $directoryHelper;

    public function __construct(Data $directoryHelper)
	{
        $this->directoryHelper = $directoryHelper;
	}

    public function execute(string $currencyCode, $totalToPay)
    {
        if ($currencyCode !== 'EUR') {
            return $this->directoryHelper->currencyConvert(
                $totalToPay, $currencyCode, 'EUR');
        } else {
            return $totalToPay;
        }
    }
}

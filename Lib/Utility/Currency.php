<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Ifthenpay\Payment\Lib\Utility;

use Magento\Directory\Helper\Data;


class Currency
{
    private $directoryHelper;

    public function __construct(Data $directoryHelper)
    {
        $this->directoryHelper = $directoryHelper;
    }


    /**
     * convert $totalToPay to euro and format to 2 decimal places and dot as decimal separator, and returns a string
     * @param string $currentCurrencyCode
     * @param $totalToPay
     */
    public function convertAndFormatToEuro(string $currentCurrencyCode, $totalToPay)
    {
        return $this->format($this->convertToEuro($currentCurrencyCode, $totalToPay));
    }

    /**
     * convert value to euro using magento directory helper, but only if current currency is not euro
     * @param string $currentCurrencyCode
     * @param $totalToPay
     * @return float
     */
    public function convertToEuro(string $currentCurrencyCode, $totalToPay): float
    {
        if ($currentCurrencyCode !== 'EUR') {
            return $this->directoryHelper->currencyConvert(
                $totalToPay,
                $currentCurrencyCode,
                'EUR'
            );
        } else {
            return (float) $totalToPay;
        }
    }

    /**
     * format value to 2 decimal places and dot as decimal separator, and returns a string
     * @param float $value
     * @return string
     */
    public function format(float $value): string
    {
        return number_format($value, 2, '.', '');
    }

}

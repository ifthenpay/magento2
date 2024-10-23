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

class Time
{
    public static function dateAfterDays(string $numberOfDays): string
    {
        if ($numberOfDays === '') {
            return '';
        }

        $timezone = new \DateTimeZone('Europe/Lisbon');
        $dateTime = new \DateTime('now', $timezone);
        $dateTime->modify("+$numberOfDays days");

        return $dateTime->format('Ymd');
    }
}

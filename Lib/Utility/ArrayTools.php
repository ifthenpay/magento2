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



class ArrayTools
{

    public static function jsonToArray(string $jsonStr): array
    {
        if ($jsonStr === '' || $jsonStr === '{}') {
            return [];
        }

        $result = json_decode($jsonStr, true);

        // Check if the decoding was successful and return the result or null if it failed
        if (json_last_error() === JSON_ERROR_NONE) {
            return $result;
        } else {
            return [];
        }
    }
}

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

class Token {

    public function encrypt(string $input): string 
    {
        return urlencode(base64_encode( $input));
    }

    public function decrypt(string $input): string 
    {
        return base64_decode(urldecode($input));
    }
}

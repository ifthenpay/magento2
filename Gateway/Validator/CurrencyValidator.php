<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ifthenpay\Payment\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Ifthenpay\Payment\Config\ConfigVars;


class CurrencyValidator extends AbstractValidator
{

    public function validate(array $validationSubject)
    {
        $isValid = false;
        $currency = $validationSubject['currency'];


        if ($currency == ConfigVars::ALLOWED_CURRENCY_CODE) {
            $isValid = true;
        }

        return $this->createResult($isValid);
    }
}

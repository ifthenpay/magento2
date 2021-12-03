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

namespace Ifthenpay\Payment\Block\Adminhtml\System\Config\Form;

use Ifthenpay\Payment\Block\Adminhtml\System\Config\Form\IfthenpayField;

class PaymentSpecificCountriesVisibleField extends IfthenpayField
{

    protected $paymentMethodFinder = '_specificcountry';

}

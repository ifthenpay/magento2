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

namespace Ifthenpay\Payment\Model;

use Magento\Payment\Block\Form;
use Ifthenpay\Payment\Block\Info;
use Magento\Payment\Model\Method\AbstractMethod;

class PaymentModelBase extends AbstractMethod
{
    protected $_code = '';
    protected $_formBlockType = Form::class;
    protected $_infoBlockType = Info::class;
    protected $_isOffline = true;
}

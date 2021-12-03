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

use Magento\Framework\Data\Form\Element\AbstractElement;
use Ifthenpay\Payment\Lib\Traits\Admin\CheckIfnotRenderElement;
use Ifthenpay\Payment\Block\Adminhtml\System\Config\Form\IfthenpayField;

class AddNewAccountBtn extends IfthenpayField
{
    use CheckIfnotRenderElement;

    protected $_template = 'Ifthenpay_Payment::system/config/AddNewAccountBtn.phtml';
    protected $paymentMethodFinder = '_addNewAccount';

    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    public function getCustomUrl()
    {
        return $this->getUrl('ifthenpay/Config/AddNewAccount');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData([
                'class' => 'addNewAccountBtn',
                'label' => __('Request New Account'),
                'data_attribute' => [
                    'paymentMethod' => $this->paymentMethod,
                ],
        ]);
        return $button->toHtml();
    }
}

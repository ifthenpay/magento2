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
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Ifthenpay\Payment\Helper\Data;

class ResetAccountBtn extends Field
{
    protected $_template = 'Ifthenpay_Payment::system/config/ResetAccountsBtn.phtml';
    protected $helperData;

    public function __construct(
        Context $context,
        Data $helperData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperData = $helperData;
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    public function getCustomUrl()
    {
        return $this->getUrl('ifthenpay/Config/ResetAccounts');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData([
                'class' => 'resetIfthenpayAccounts',
                'label' => __('Reset Accounts')
        ]);
        return $button->toHtml();
    }

    public function render(AbstractElement $element)
    {
       $html = '';
       $backofficeKey = $this->helperData->getBackofficeKey();

        if (is_null($backofficeKey)) {
            return $this->_decorateRowHtml($element, '');
        }
        return parent::render($element);
    }
}

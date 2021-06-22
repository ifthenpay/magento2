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


use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Ifthenpay\Payment\Helper\Data;
use \Magento\Framework\Data\Form\Element\AbstractElement;

class IsVisible extends Field
{
    private $helperData;

    public function __construct(
        Context $context,
        Data $helperData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperData = $helperData;
    }

    public function render(AbstractElement $element)
    {
       $html = '';
       $backofficeKey = $this->helperData->getBackofficeKey();

        if (!is_null($backofficeKey)) {
            return $this->_decorateRowHtml($element, $html);
        }

        return parent::render($element);
    }
}

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

use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form\Fieldset as BaseField;
use Magento\Config\Model\Config;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\Js;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Ifthenpay\Payment\Helper\Data;


class FieldSet extends BaseField
{
    
    private $config;
    private $secureRenderer;
    private $helperData;

    public function __construct(
        Data $helperData,
        Context $context,
        Session $authSession,
        Js $jsHelper,
        Config $config,
        SecureHtmlRenderer $secureRenderer,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $authSession,
            $jsHelper,
            $data,
            $secureRenderer
        );
        $this->config         = $config;
        $this->secureRenderer = $secureRenderer;
        $this->helperData = $helperData;
    }

    public function render(AbstractElement $element)
    {
        $html = '';
        $backofficeKey = $this->helperData->getBackofficeKey();

        if (is_null($backofficeKey)) {
            return $this->_decorateRowHtml($element, $html);
        }
        return parent::render($element);
    }
}

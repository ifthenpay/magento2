<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form\Fieldset as BaseField;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\Js;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;



class IfBackofficeKeyToggleVisibility extends BaseField
{
    private $configData;

    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        SecureHtmlRenderer $secureRenderer,
        IfthenpayConfig $configData,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $authSession,
            $jsHelper,
            $data,
            $secureRenderer
        );
        $this->configData = $configData;
    }


    /**
     * will render the fieldset only if backoffice key is set, this is used to hide the payment methods if backoffice key is not set
     * @param AbstractElement $element
     * @return mixed
     */
    public function render(AbstractElement $element)
    {
        $html = '';
        $backofficeKey = $this->configData->getBackofficeKey();

        if ($backofficeKey === '') {
            return $html;
        }
        return parent::render($element);
    }
}

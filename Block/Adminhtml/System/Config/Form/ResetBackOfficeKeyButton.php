<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;




class ResetBackOfficeKeyButton extends Field
{
    protected $_template = ConfigVars::PATH_TEMPLATE_ADMIN_SYSTEM_CONFIG_FORM . 'Btn.phtml';
    private $configData;
    private $urlBuilder;


    public function __construct(
        IfthenpayConfig $ifthenpayConfig,
        Context $context,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configData = $ifthenpayConfig;
        $this->urlBuilder = $urlBuilder;
    }



    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    public function getButtonHtml()
    {
        $html = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
                [
                    'id' => 'reset_backoffice_key_btn',
                    'label' => __('Reset Backoffice Key')
                ]
            )->toHtml();

        return $html;
    }


    /**
     * render element html according to backoffice key, if it is empty, return empty string
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $html = parent::render($element);
        $backofficeKey = $this->configData->getBackofficeKey();

        if ($backofficeKey === '') {
            return '';
        }

        return $html;
    }
}

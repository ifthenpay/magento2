<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Block\Adminhtml\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;




class RefreshAccountsButton extends Field
{
    protected $_template = ConfigVars::PATH_TEMPLATE_ADMIN_SYSTEM_CONFIG_FORM . 'Btn.phtml';
    private $configData;

    public function __construct(
        Context $context,
        IfthenpayConfig $configData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configData = $configData;
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
                    'id' => 'refresh_accounts_btn',
                    'label' => __('Refresh')
                ]
            )->toHtml();

        return $html;
    }


    /**
     * render element html only if a backoffice key is set, there is nothing to refresh without one
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

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
use Ifthenpay\Payment\Model\ScopeConfigResolver;



class RequestNewAccountButton extends Field
{
    protected $_template = ConfigVars::PATH_TEMPLATE_ADMIN_SYSTEM_CONFIG_FORM . 'Btn.phtml';
    private $urlBuilder;
    private $paymentMethod;
    private $scopeConfigResolver;

    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        ScopeConfigResolver $scopeConfigResolver,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfigResolver = $scopeConfigResolver;
    }



    protected function _getElementHtml(AbstractElement $element)
    {
        $this->paymentMethod = str_replace('_add_new_account', '', explode("_ifthenpay_", $element->getHtmlId())[1]);

        return $this->_toHtml();
    }

    public function getButtonHtml()
    {

        $html = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
                [
                    'class' => 'request_account_btn',
                    'label' => __('Request New Account'),
                    'data_attribute' => [
                        'paymentMethod' => $this->paymentMethod
                    ],
                ]
            )->toHtml();

        return $html;
    }
}

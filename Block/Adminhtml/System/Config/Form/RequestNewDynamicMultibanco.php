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
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;


class RequestNewDynamicMultibanco extends Field
{
    protected $_template = ConfigVars::PATH_TEMPLATE_ADMIN_SYSTEM_CONFIG_FORM . 'Btn.phtml';
    private $urlBuilder;
    private $paymentMethod;
    private $scopeConfigResolver;
    private $config;

    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        ScopeConfigResolver $scopeConfigResolver,
        IfthenpayConfig $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfigResolver = $scopeConfigResolver;
        $this->config = $config;
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
                    'class' => 'request_account_btn',
                    'label' => __('Request New Multibanco Dynamic Reference Account'),
                    'data_attribute' => [
                        'paymentMethod' => ConfigVars::MULTIBANCO_DYNAMIC
                    ],
                ]
            )->toHtml();

        return $html;
    }
    public function render(AbstractElement $element)
    {
        try {
            if (!$this->config->hasDynamicReferencesAccount()) {
                return parent::render($element);
            } else {
                $this->_decorateRowHtml($element, '');
            }

        } catch (\Throwable $th) {
            $this->logger->debug('error ' . lcfirst(get_class($this)), ['error' => $th, 'errorMessage' => $th->getMessage()]);
            throw $th;
        }
    }

}

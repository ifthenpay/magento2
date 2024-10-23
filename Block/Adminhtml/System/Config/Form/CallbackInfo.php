<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Ifthenpay\Payment\Lib\Factory\ConfigFactory;
use Ifthenpay\Payment\Logger\Logger;


class CallbackInfo extends field
{
    /**
     * Template path
     *
     * @var string
     */
    public $_template = 'Ifthenpay_Payment::system/config/form/callbackInfo.phtml';
    protected $configFactory;
    protected $logger;
    public $data;
    protected $paymentMethod;
    public function __construct(
        Context $context,
        ConfigFactory $configFactory,
        Logger $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configFactory = $configFactory;
        $this->logger = $logger;
    }

    /**
     * Render fieldset html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        try {
            $this->paymentMethod = str_replace('_callbackInfo', '', explode("_ifthenpay_", $element->getHtmlId())[1]);

            $config = $this->configFactory->createConfig('ifthenpay_' . $this->paymentMethod);

            $this->data['callbackUrl'] = $config->getCallbackUrl();
            $this->data['chaveAntiPhishing'] = $config->getAntiPhishingKey();
            $this->data['callbackActivated'] = $config->getIsCallbackActivated();


            if ($this->data['callbackUrl'] && $this->data['chaveAntiPhishing']) {
                return $this->_decorateRowHtml($element, "<td colspan='5'>" . $this->toHtml() . '</td>');
            }
        } catch (\Throwable $th) {
            $this->logger->error('config/callback/info', [
                'error' => $th,
            ]);
        }
        return $this->_decorateRowHtml($element, '');
    }
}

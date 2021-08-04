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

use \Magento\Backend\Block\Template\Context;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Lib\Factory\Config\IfthenpayConfigFormFactory;


class CallbackInfo extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Template path
     *
     * @var string
     */
    public $_template = 'Ifthenpay_Payment::system/config/callbackInfo.phtml';

    private $ifthenpayConfigFormFactory;

    public $configData;

    private $logger;

    public function __construct(
        Context $context,
        IfthenpayConfigFormFactory $ifthenpayConfigFormFactory,
        IfthenpayLogger $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->ifthenpayConfigFormFactory = $ifthenpayConfigFormFactory;
        $this->logger = $logger;
    }

    /**
     * Render fieldset html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        try {
            $paymentMethod = str_replace('_callbackInfo', '', explode("_ifthenpay_", $element->getHtmlId())[1]);
            $ifthenpayConfigForm = $this->ifthenpayConfigFormFactory->setType($paymentMethod)->build();

            if (!$ifthenpayConfigForm->displayCallbackInfo()) {
                $html = '';
            } else {
                $this->configData = $ifthenpayConfigForm->createCallback();
                $this->logger->debug('callback form: Callback Created with success.');
                $html =  $this->toHtml();
            }

            return $this->_decorateRowHtml($element, "<td colspan='5'>" . $html . '</td>');    
        } catch (\Throwable $th) {
            $this->logger->debug('callback form: Error creating callback info. - ' . $th->getMessage());
            throw $th;
        }
        
    }
}

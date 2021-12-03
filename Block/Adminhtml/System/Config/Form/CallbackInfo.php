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

use Ifthenpay\Payment\Lib\Payments\Gateway;
use Magento\Backend\Block\Template\Context;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Factory\Config\IfthenpayConfigFormFactory;
use Ifthenpay\Payment\Block\Adminhtml\System\Config\Form\IfthenpayField;

class CallbackInfo extends IfthenpayField
{
    /**
     * Template path
     *
     * @var string
     */
    public $_template = 'Ifthenpay_Payment::system/config/callbackInfo.phtml';

    private $ifthenpayConfigFormFactory;

    public $configData;

    protected $paymentMethodFinder = '_callbackInfo';

    public function __construct(
        Context $context,
        IfthenpayConfigFormFactory $ifthenpayConfigFormFactory,
        DataFactory $dataFactory,
        Gateway $gateway,
        IfthenpayLogger $logger,
        array $data = []
    ) {
        parent::__construct($context, $dataFactory, $gateway, $logger, $data);
        $this->ifthenpayConfigFormFactory = $ifthenpayConfigFormFactory;
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
            $paymentMethod = $this->findPaymentMethod($element);
            $ifthenpayConfigForm = $this->ifthenpayConfigFormFactory->setType($paymentMethod)->build();

            if (!$ifthenpayConfigForm->displayCallbackInfo()) {
                $html = '';
            } else {
                $this->configData = $ifthenpayConfigForm->createCallback();
                $this->logger->debug('callback form: Callback Created with success.', ['configData' => $this->configData]);
                $html =  $this->toHtml();
            }

            return $this->_decorateRowHtml($element, "<td colspan='5'>" . $html . '</td>');
        } catch (\Throwable $th) {
            $this->logger->debug('callback form: Error creating callback info', ['error' => $th, 'errorMessage' => $th->getMessage()]);
            throw $th;
        }

    }
}

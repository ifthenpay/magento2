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
use Magento\Framework\Data\Form\Element\AbstractElement;
use Ifthenpay\Payment\Lib\Traits\Admin\CheckIfnotRenderElement;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Logger\IfthenpayLogger;

class RequestDynamicMbAccountBtn extends Field
{
    use CheckIfnotRenderElement;

    protected $_template = 'Ifthenpay_Payment::system/config/RequestDynamicMbAccountBtn.phtml';
    protected $paymentMethodFinder = '_requestDynamicMb';
    protected $dataFactory;
    protected $paymentMethod;
    protected $gateway;
    protected $logger;

    public function __construct(
        Context $context,
        DataFactory $dataFactory,
        Gateway $gateway,
        IfthenpayLogger $logger,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->dataFactory = $dataFactory;
        $this->gateway = $gateway;
        $this->logger = $logger;
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    public function getCustomUrl()
    {
        return $this->getUrl('ifthenpay/Config/requestDynamicMb');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData([
                'class' => 'requestDynamicMb',
                'label' => __('Request Multibanco Deadline')
        ]);
        return $button->toHtml();
    }

    public function render(AbstractElement $element)
    {
        try {
            $configData = $this->dataFactory->setType(Gateway::MULTIBANCO)->build();
            $userPaymentMethods = $configData->getUserPaymentMethods();
            $ifthenpayPaymentMethods = $this->gateway->getPaymentMethodsType();
            $this->logger->debug(lcfirst(get_class($this)) . ': user payment methods retrieved with success', [
                'ifthenpayPaymentMethods' => $ifthenpayPaymentMethods,
                'userPaymentMethods' => $userPaymentMethods
            ]);
            if (!$this->checkIfRenderElement($userPaymentMethods)) {
                $this->_decorateRowHtml($element, '');
            }
            $userAccount = $configData->getUserAccount();

            if ($this->gateway->checkDynamicMb($userAccount)) {
                return $this->_decorateRowHtml($element, '');
            }
            if ($this->checkIfRenderElement($userPaymentMethods)) {
                $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
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

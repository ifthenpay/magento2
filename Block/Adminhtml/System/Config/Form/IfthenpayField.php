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

use Magento\Backend\Block\Template\Context;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Ifthenpay\Payment\Logger\IfthenpayLogger;

class IfthenpayField extends Field
{
    protected $dataFactory;
    protected $paymentMethod;
    protected $gateway;
    protected $logger;
    protected $paymentMethodFinder;

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

    protected function findPaymentMethod(AbstractElement $element): string
    {
        return str_replace($this->paymentMethodFinder, '', explode("_ifthenpay_", $element->getHtmlId())[1]);
    }

    protected function checkIfRenderElement(array $userPaymentMethods): bool
    {
        if (in_array($this->paymentMethod, $userPaymentMethods)) {
            return true;
        } else {
            return false;
        }
    }

    public function render(AbstractElement $element)
    {
        try {
            $this->paymentMethod = $this->findPaymentMethod($element);
            $configData = $this->dataFactory->setType($this->paymentMethod)->build();
            $userPaymentMethods = $configData->getUserPaymentMethods();
            $ifthenpayPaymentMethods = $this->gateway->getPaymentMethodsType();
            $this->logger->debug(lcfirst(get_class($this)) . ': user payment methods retrieved with success', [
                'ifthenpayPaymentMethods' => $ifthenpayPaymentMethods,
                'userPaymentMethods' => $userPaymentMethods
            ]);
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

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
use Ifthenpay\Payment\Block\Adminhtml\System\Config\Form\IfthenpayField;
use Ifthenpay\Payment\Lib\Payments\Multibanco;

class MultibancoValidadeVisibleField extends IfthenpayField
{
    protected $paymentMethodFinder = '_validade';

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

            if (!$this->gateway->checkDynamicMb($userAccount) || $configData->getConfig()['entidade'] !== Multibanco::DYNAMIC_MB_ENTIDADE) {
                return $this->_decorateRowHtml($element, '');
            }
            return parent::render($element);

        } catch (\Throwable $th) {
            $this->logger->debug('error ' . lcfirst(get_class($this)), ['error' => $th, 'errorMessage' => $th->getMessage()]);
            throw $th;
        }
    }
}

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

use Ifthenpay\Payment\Lib\Payments\Multibanco;
use Magento\Framework\Data\Form\Element\AbstractElement;

class CancelMultibancoOrderVisibleField extends CancelOrderVisibleField
{

    public function render(AbstractElement $element)
    {
        try {
            $this->paymentMethod = $this->findPaymentMethod($element);
            $configData = $this->dataFactory->setType($this->paymentMethod)->build();
            if ($configData->getConfig()['entidade'] !== Multibanco::DYNAMIC_MB_ENTIDADE) {
                $this->_decorateRowHtml($element, '');
            } else {
                parent::render($element);
            }
        } catch (\Throwable $th) {
            $this->logger->debug('error ' . lcfirst(get_class($this)), ['error' => $th, 'errorMessage' => $th->getMessage()]);
            throw $th;
        }
    }
}

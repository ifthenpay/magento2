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
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;


class IfBackofficeKeyHideBackofficeKey extends Field
{
    private $configData;

    public function __construct(
        IfthenpayConfig $configData,

        Context $context,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->configData = $configData;
    }


    /**
     * will render the backoffice field only if backoffice key is not set, this is used to hide the backoffice key after it has been saved
     * @param AbstractElement $element
     * @return mixed
     */
    public function render(AbstractElement $element)
    {
        $backofficeKey = $this->configData->getBackofficeKey();

        if ($backofficeKey !== '') {
            return $this->_decorateRowHtml($element, '');
        }
        return parent::render($element);
    }
}

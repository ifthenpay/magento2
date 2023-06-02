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
use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Logger\Logger;

/**
 * block class for hiding fields in store scope
 *
 */
class NotAvailableInStoreScope extends field
{
    /**
     * Template path
     *
     * @var string
     */
    protected $_template = ConfigVars::PATH_TEMPLATE_ADMIN_SYSTEM_CONFIG_FORM . 'notAvailableInStoreScope.phtml';

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
        return $this->_decorateRowHtml($element, $this->toHtml());
    }
}

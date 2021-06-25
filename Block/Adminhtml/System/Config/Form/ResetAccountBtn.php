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
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ResetAccountBtn extends Field
{
    protected $_template = 'Ifthenpay_Payment::system/config/ResetAccountsBtn.phtml';
    protected $dataFactory;
    private $paymentMethod;
    private $gateway;
    private $logger;

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

    public function render(AbstractElement $element)
    {
        try {
            $this->paymentMethod = str_replace('_resetAccounts', '', str_replace('payment_us_ifthenpay_', '', $element->getHtmlId()));
            $configData = $this->dataFactory->setType($this->paymentMethod)->build();
            $userPaymentMethods = $configData->getUserPaymentMethods();
            $ifthenpayPaymentMethods = $this->gateway->getPaymentMethodsType();

            if (!empty(array_diff($userPaymentMethods, $ifthenpayPaymentMethods))) {
                $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
                $this->logger->debug('ResetAccountBtn: ResetAccountBtn render with success');
                return parent::render($element);
            } else {
                $this->_decorateRowHtml($element, '');
            }
        } catch (\Throwable $th) {
            $this->logger->debug('ResetAccountBtn: Error - ' . $th->getMessage());
            throw $th;
        }
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    public function getCustomUrl()
    {
        return $this->getUrl('ifthenpay/Config/ResetAccounts');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData([
                'class' => 'resetIfthenpayAccounts',
                'label' => __('Reset Accounts'),
                'data_attribute' => [
                    'paymentMethod' => $this->paymentMethod,
                ],
        ]);
        return $button->toHtml();
    }
}

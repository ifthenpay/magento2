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

class AddNewAccountBtn extends Field
{
    protected $_template = 'Ifthenpay_Payment::system/config/AddNewAccountBtn.phtml';
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
            $this->paymentMethod = str_replace('_addNewAccount', '', str_replace('payment_us_ifthenpay_', '', $element->getHtmlId()));
            $configData = $this->dataFactory->setType($this->paymentMethod)->build();
            $userPaymentMethods = $configData->getUserPaymentMethods();
            $ifthenpayPaymentMethods = $this->gateway->getPaymentMethodsType();
            $this->logger->debug('addNewAccountBtn: user payment methods retrieved with success');
            if (!empty($configData) && empty(array_diff($userPaymentMethods, $ifthenpayPaymentMethods))) {
                $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
                return parent::render($element);
            } else {
                $this->_decorateRowHtml($element, '');
            }
        } catch (\Throwable $th) {
            $this->logger->debug('addNewAccountBtn: ' . $th->getMessage());
            throw $th;
        }
        

    }

    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    public function getCustomUrl()
    {
        return $this->getUrl('ifthenpay/Config/AddNewAccount');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData([
                'class' => 'addNewAccountBtn',
                'label' => __('Request New Account'),
                'data_attribute' => [
                    'paymentMethod' => $this->paymentMethod,
                ],
        ]);
        return $button->toHtml();
    }
}

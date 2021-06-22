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
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ChooseNewEntidadeSubEntidadeBtn extends Field
{
    protected $_template = 'Ifthenpay_Payment::system/config/chooseNewEntidadeSubEntidadeBtn.phtml';
    private $dataFactory;
    private $paymentMethod;
    private $logger;

    public function __construct(Context $context, DataFactory $dataFactory, IfthenpayLogger $logger,array $data = [])
    {
        parent::__construct($context, $data);
        $this->dataFactory = $dataFactory;
        $this->logger = $logger;
    }

    public function render(AbstractElement $element)
    {
        try {
            $this->paymentMethod = str_replace('_chooseNewEntidadeSubEntidade', '', str_replace('payment_us_ifthenpay_', '', $element->getHtmlId()));
            $configData = $this->dataFactory->setType($this->paymentMethod)->build()->getConfig();
            if (!empty($configData)) {
                $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
                $this->logger->debug('chooseNewEntidadeSubEntidadeBtn: Button Choose New Entidade/SubEntidade rendered with success');
                return parent::render($element);
            } else {
                $this->_decorateRowHtml($element, '');
            }
        } catch (\Throwable $th) {
            $this->logger->debug('chooseNewEntidadeSubEntidadeBtn: Error - ' . $th->getMessage());
            throw $th;
        }
        

    }

    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    public function getCustomUrl()
    {
        return $this->getUrl('ifthenpay/Config/ChooseNewEntidadeSubEntidade');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData([
                'class' => 'chooseNewEntidadeBtn', 
                'label' => __('Choose New Entidade/SubEntidade'),
                'data_attribute' => [
                    'paymentMethod' => $this->paymentMethod,
                ],
        ]);
        return $button->toHtml();
    }
}

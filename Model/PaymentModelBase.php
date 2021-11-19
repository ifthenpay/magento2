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

namespace Ifthenpay\Payment\Model;

use Magento\Framework\Registry;
use Magento\Payment\Block\Form;
use Magento\Payment\Helper\Data;
use Ifthenpay\Payment\Block\Info;
use Magento\Framework\Model\Context;
use Magento\Payment\Model\Method\Logger;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Payment\Model\Method\AbstractMethod;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;

class PaymentModelBase extends AbstractMethod
{
    protected $_code = '';
    protected $_formBlockType = Form::class;
    protected $_infoBlockType = Info::class;
    protected $_isOffline = true;

    protected $ifthenpayLogger;

    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        DataFactory $dataFactory,
        IfthenpayLogger $ifthenpayLogger,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        DirectoryHelper $directory = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data,
            $directory
        );
        $this->ifthenpayLogger = $ifthenpayLogger;
        $this->configData = $dataFactory->setType($this->_code)->build()->getConfig();
    }

    public function canUseForCurrency($currencyCode)
    {
        return $currencyCode === 'EUR';
    }

    public function isAvailable( $quote = null)
    {
        return $this->checkPaymentConfig() && parent::isAvailable($quote);
    }
}

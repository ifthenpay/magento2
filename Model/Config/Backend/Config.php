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

namespace Ifthenpay\Payment\Model\Config\Backend;

use Magento\Framework\Registry;
use Ifthenpay\Payment\Helper\Data;
use Magento\Framework\Model\Context;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;

class Config extends \Magento\Framework\App\Config\Value
{
    const USER_PAYMENT_METHODS = 'payment/ifthenpay/userPaymentMethods';
    const USER_ACCOUNT = 'payment/ifthenpay/userAccount';

    private $gateway;
    private $logger;

    public function __construct(
        Data $helperData,
        Gateway $gateway,
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        IfthenpayLogger $logger,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->gateway = $gateway;
        $this->dataHelper = $helperData;
        $this->logger = $logger;
    }

    public function beforeSave()
    {
        try {
            $label = $this->getData('field_config/label');

            if ($this->getValue() == '') {
                throw new \Magento\Framework\Exception\ValidatorException(__($label . ' is required.'));
            }
            $this->gateway->authenticate($this->getValue());
            $userPaymentMethods = $this->gateway->getPaymentMethods();
            $userAccount = $this->gateway->getAccount();

            $this->dataHelper->saveUserPaymentMethods($userPaymentMethods);
            $this->dataHelper->saveUserAccount($userAccount);

            $this->logger->debug('User payment methods and user account saved with success', [
                'userPaymentMethods' => $userPaymentMethods,
                'userAccount' => $userAccount
            ]);
        } catch (\Throwable $th) {
            $this->logger->debug('Error saving user payment methods and account', [
                'error' => $th,
                'errorMessage' => $th->getMessage()
            ]);
            throw new \Magento\Framework\Exception\ValidatorException(__('errorIfthenpayDatabaseTables'));
        }

        $this->setValue($this->getValue());

        parent::beforeSave();
    }
}

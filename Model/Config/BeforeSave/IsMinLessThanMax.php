<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);


namespace Ifthenpay\Payment\Model\Config\BeforeSave;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;



/**
 * Class IsMinLessThanMax
 * Validate if min value is less than max value
 * @package Ifthenpay\Payment\Model\Config\Validation
 */
class IsMinLessThanMax extends Value
{
    protected $gateway;
    protected $configData;
    protected $validateIf;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        IfthenpayConfig $configData,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->configData = $configData;
    }

    /**
     * Validate if min value is less than max value
     * @throws LocalizedException
     * @return Value
     */
    public function beforeSave()
    {
        if ($this->configData->getBackofficeKey() !== '') {

            try {
                // get values to compare
                $min = $this->getValue();
                $max = $this->getData('fieldset_data/max_order_total');
                $paymentMethod = $this->getData('group_id');
                $MessagePrefix = 'ifthenpay ' . ucfirst($paymentMethod) . ': ';

                if ($min !== '' && $min != null && $max !== '' && $max != null && $min >= $max) {
                    throw new \Exception('Minimum Order Value Value must be lesser than Maximum Order Value.');
                }

            } catch (\Throwable $th) {
                throw new LocalizedException(__($MessagePrefix . $th->getMessage()));
            }
        }

        return parent::beforeSave();
    }
}

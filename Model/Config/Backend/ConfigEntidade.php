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
use Ifthenpay\Payment\Helper\DataPaymentMethodTable;
use Ifthenpay\Payment\Lib\Payments\Multibanco;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\App\Config\Value;

class ConfigEntidade extends Value
{

    private $dataPaymentMethodTable;
    private $logger;

    public function __construct(
        DataPaymentMethodTable $dataPaymentMethodTable,
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
        $this->dataPaymentMethodTable = $dataPaymentMethodTable;
        $this->logger = $logger;
    }

    public function beforeSave()
    {
        try {
            $label = $this->getData('field_config/label');

            if ($this->getValue() == '') {
                throw new \Magento\Framework\Exception\ValidatorException(__($label . ' is required.'));
            }

            $this->logger->debug('Multibanco Database table updated with success');
        } catch (\Throwable $th) {
            $this->logger->debug('Error Updating Multibanco table', [
                'error' => $th,
                'errorMessage' => $th->getMessage()
            ]);
            throw new \Magento\Framework\Exception\ValidatorException(__('errorUpdateMultibancoDatabaseTable'));
        }

        $this->setValue($this->getValue());

        parent::beforeSave();
    }
}

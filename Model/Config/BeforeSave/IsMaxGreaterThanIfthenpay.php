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

use Ifthenpay\Payment\Config\ConfigVars;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;
use Ifthenpay\Payment\Lib\HttpClient;



/**
 * Class IsMaxGreaterThanIfthenpay
 * Validate if min value is less than max value
 * @package Ifthenpay\Payment\Model\Config\Validation
 */
class IsMaxGreaterThanIfthenpay extends Value
{
    protected $gateway;
    protected $configData;
    protected $validateIf;
    private $httpClient;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        IfthenpayConfig $configData,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        HttpClient $httpClient,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->configData = $configData;
        $this->httpClient = $httpClient;
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
                $max = $this->getData('fieldset_data/max_order_total');
                $paymentMethod = $this->getData('group_id');
                $MessagePrefix = 'ifthenpay ' . ucfirst($paymentMethod) . ': ';

                $cofidisKey = $this->getData('fieldset_data/key');

                $url = ConfigVars::API_URL_COFIDIS_GET_MAX_MIN_AMOUNT . '/' . $cofidisKey;

                $this->httpClient->doGet($url, []);
                $responseArray = $this->httpClient->getBodyArray();
                $status = $this->httpClient->getStatus();

                if ($status !== 200 || !(isset($responseArray['message']) && $responseArray['message'] == 'success')) {
                    throw new \Exception('Error: Min Max request failed.');
                }

                $maxIfthenpay = $responseArray['limits']['maxAmount'];

                if ($max > $maxIfthenpay) {
                    throw new \Exception('Minimum Order Value must be greater or equal to value defined in ifthenpay backoffice than Maximum Order Value.');
                }




            } catch (\Throwable $th) {
                throw new LocalizedException(__($MessagePrefix . $th->getMessage()));
            }
        }

        return parent::beforeSave();
    }
}

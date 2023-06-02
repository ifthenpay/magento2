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
use Ifthenpay\Payment\Lib\Services\GatewayService;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Ifthenpay\Payment\Logger\Logger;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;



/**
 * Class BackofficeKey
 * Validate the backoffice key before saving it
 * @package Ifthenpay\Payment\Model\Config\Validation
 */
class BackofficeKey extends Value
{
    protected $formKeyValidator;
    protected $gatewayService;
    protected $configData;
    protected $logger;
    protected $validateIf;
    protected $request;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        Validator $formKeyValidator,
        GatewayService $gatewayService,
        IfthenpayConfig $configData,
        Logger $logger,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->formKeyValidator = $formKeyValidator;
        $this->gatewayService = $gatewayService;
        $this->logger = $logger;
        $this->configData = $configData;
        $this->request = $request;
    }

    /**
     * Validate the backoffice key before saving it
     * save accounts attached to this backoffice key in DB config (saves a string  )
     * save payment methods available for this backoffice key in DB config
     * @throws LocalizedException
     * @return Value
     */
    public function beforeSave()
    {
        if ($this->configData->getBackofficeKey() === '') {

            try {
                $backofficeKey = $this->getValue();

                // validate backoffice key format
                $this->isBackofficeKeyValidFormat($backofficeKey);

                // validate backoffice key with ifthenpay api, will throw exception if invalid
                $this->gatewayService->setAccountsWithRequest($backofficeKey);

                // save accounts attached to this backoffice key in DB config
                $this->configData->saveUserAccounts($this->gatewayService->getAccounts());
                // save payment method names attached to this backoffice key in DB config
                $this->configData->saveUserPaymentMethods($this->gatewayService->getUserPaymentMethods());

            } catch (\Throwable $th) {
                $this->logger->error('Error saving user payment methods and account', [
                    'error' => $th,
                ]);

                throw new LocalizedException(__($th->getMessage()));
            }
        }
        return parent::beforeSave();
    }


    private function isBackofficeKeyValidFormat($backofficeKey)
    {
        $ruleRegex = '/^[0-9]{4}(-[0-9]{4}){3}$/';

        if (!preg_match($ruleRegex, $backofficeKey)) {
            throw new \Exception('Backoffice Key format is invalid. ex: 1234-1234-1234-1234');
        }
    }
}

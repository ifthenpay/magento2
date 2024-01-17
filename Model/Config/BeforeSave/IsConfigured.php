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
use Ifthenpay\Payment\Config\ConfigVars;



/**
 * Class IsMinLessThanMax
 * Validate if payment method is configured before saving active state
 */
class IsConfigured extends Value
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
		array $data = []
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
				$active = $this->getValue();
				$paymentMethod = $this->getData('group_id');
				$message = 'Please fill all required fields before activating the payment method.';


				$entity = $this->getData('fieldset_data/' . ConfigVars::MULTIBANCO_ENTITY) ?? '';
				$subEntity = $this->getData('fieldset_data/' . ConfigVars::MULTIBANCO_SUB_ENTITY) ?? '';
				// 'key' is the same for all other payment methods (mbway, payshop, ccard)
				$key = $this->getData('fieldset_data/key') ?? '';

				if ($active === '1') {
					$messagePrefix = 'ifthenpay ' . ucfirst($paymentMethod) . ': ';

					if ($paymentMethod === ConfigVars::MULTIBANCO) {

						// for dynamic multibanco
						if ($entity === ConfigVars::MULTIBANCO_DYNAMIC && $subEntity === '') {
							$message = $messagePrefix . 'Multibanco Key is a required field. ' . $message;
							throw new \Exception($message);
						}
						// for regular multibanco
						if ($entity === '' || $subEntity === '') {
							$message = $messagePrefix . 'Entity and Sub Entity are required fields. ' . $message;
							throw new \Exception($message);
						}
					}
					if ($paymentMethod === ConfigVars::MBWAY) {
						if ($key === '') {
							$message = $messagePrefix . 'MB WAY Key is a required field. ' . $message;
							throw new \Exception($message);
						}
					}
					if ($paymentMethod === ConfigVars::PAYSHOP) {
						if ($key === '') {
							$message = $messagePrefix . 'Payshop Key is a required field. ' . $message;
							throw new \Exception($message);
						}
					}
					if ($paymentMethod === ConfigVars::CCARD) {
						if ($key === '') {
							$message = $messagePrefix . 'Ccard Key is a required field. ' . $message;
							throw new \Exception($message);
						}
					}
					if ($paymentMethod === ConfigVars::COFIDIS) {
						if ($key === '') {
							$message = $messagePrefix . 'Cofidis Pay Key is a required field. ' . $message;
							throw new \Exception($message);
						}
					}
				}

			} catch (\Throwable $th) {
				throw new LocalizedException(__($th->getMessage()));
			}
		}

		return parent::beforeSave();
	}
}

<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Ifthenpay\Payment\Model\Config\Source;

use Ifthenpay\Payment\Config\ConfigVars;
use Magento\Framework\Data\OptionSourceInterface;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;
use Ifthenpay\Payment\Gateway\Config\CofidisConfig;



class CofidisKeyOptions implements OptionSourceInterface
{
	private $configData;
	private $cofidisConfig;

	public function __construct(
		IfthenpayConfig $configData,
		CofidisConfig $cofidisConfig
	) {
		$this->configData = $configData;
		$this->cofidisConfig = $cofidisConfig;
	}


	/**
	 * Return array of order canceling options as value-label pairs
	 * @return array
	 */
	public function toOptionArray()
	{
		$cofidisKeys = $this->configData->getUserPaymentMethodAccounts(ConfigVars::COFIDIS);

		// remove keys in use by other stores (in case of multistore)
		$thisKey = $this->cofidisConfig->getKey();
		$otherKeysInUse = $this->cofidisConfig->getOtherKeysInUse($thisKey);

		// remove thiskey from list of excludes
		foreach ($otherKeysInUse as $key => $inUse) {
			if ($thisKey === $inUse) {
				unset($otherKeysInUse[$key]);
			}
		}

		$optionArray = [];
		foreach ($cofidisKeys as $key => $value) {

			if (is_array($value) && isset($value['Entidade']) && isset($value['SubEntidade'])) {

				foreach ($value['SubEntidade'] as $cofidisKey) {

					// only add option for keys not in the otherKeysInUse
					if (!in_array($cofidisKey, $otherKeysInUse)) {
						$optionArray[] = [
							'value' => $cofidisKey,
							'label' => $cofidisKey
						];
					}
				}
			}
		}

		if (!empty($optionArray)) {
			array_unshift($optionArray, ['value' => '', 'label' => __('Select a Cofidis Pay key')]);
		} else {
			// fallback for when there are no cofidis keys and the the request cofidis account button is not showing
			array_unshift($optionArray, ['value' => 'nokey', 'label' => __('No Cofidis Pay keys found')]);
		}

		return $optionArray;
	}
}
